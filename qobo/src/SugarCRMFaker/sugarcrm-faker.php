<?php
require (dirname(__FILE__) . '/sugarcrm-api.php');

class SugarCRM_Faker extends SugarCRM_API {
  
  protected $faker;
  
  const FIELD_TYPE_VARCHAR = 'varchar';
  const FIELD_TYPE_TEXT = 'text';
  const FIELD_TYPE_ENUM = 'enum';
  const FIELD_TYPE_CURRENCY = 'currency';
  const FIELD_TYPE_BOOL = 'bool';
  const FIELD_TYPE_PHONE = 'phone';
  const FIELD_TYPE_DATETIME = 'datetime';
  const FIELD_TYPE_DATE = 'date';
  const FIELD_TYPE_URL = 'url';
  const FIELD_TYPE_NAME = 'name';
  const FIELD_TYPE_RELATE = 'relate';
  const FIELD_TYPE_ID = 'id';
  const FIELD_TYPE_LINK = 'link';
  const FIELD_SOURCE_NONDB = 'non-db';
  
  const MAX_VARCHAR_LEN = 10;
  const MAX_TEXT_LEN = 100;
  const MAX_RECORDS_PER_MODULE = 2;
  const MIN_RECORDS_PER_MODULE = 1;
  const MAX_PER_RELATIONSHIP = 5;
  const MIN_PER_RELATIONSHIP = 1;
  
  const DOCUMENTS_PATH_RELATIVE = '/etc/documents';
  const DOCUMENTS_TEMP_PATH = '/tmp';
  const MODULE_NAME_DOCUMENTS = 'Documents';
  const MAX_DOCUMENT_REVISIONS = 2;
  const MIN_DOCUMENT_REVISIONS = 1;
  
  const MODULE_NAME_USERS = 'Users';

  public function __construct() {
    parent::__construct();
    $this->set_faker(self::create_faker());
  }
  
  public function get_faker() {
    return $this->faker;
  }
  
  public function set_faker($faker) {
    $this->faker = $faker;
  }
  
  public static function create_faker() {
    $faker = Faker\Factory::create();
    $faker->addProvider(new Faker\Provider\en_GB\PhoneNumber($faker));

    return $faker;
  }
  
  public function populate_modules(array $module_def_list=null) {
    $result = array();
    
    if(!isset($module_def_list))
      $module_def_list = array_fill_keys($this->sugarcrm_config->get_available_modules_populate(), null);
    
    //Create Records
    $result['Records'] = $this->set_entries_fake_modules($module_def_list);
    //Create Document Revisions
    if(array_key_exists(self::MODULE_NAME_DOCUMENTS, $module_def_list)) {
      $result['Document Revisions'] = $this->set_document_revisions_fake_modules($result['Records'], 
          $module_def_list[self::MODULE_NAME_DOCUMENTS]['max_revisions'], 
          $module_def_list[self::MODULE_NAME_DOCUMENTS]['min_revisions']);
    }
    //Create Relations
    $result['Relationships'] = $this->set_relationships_fake_modules($result['Records']);
  
    return $result;
  }

  public function set_entry_fake($module_name) {
    $name_value_list = $this->prepare_entry_fields($module_name);
    return SugarCRM_API::set_entry($module_name, $name_value_list);
  }
  
  public function set_entries_fake($module_name, $max_records=null, $min_records=null) {
    $name_value_lists = $this->prepare_entries_fields($module_name, $max_records, $min_records);
    return SugarCRM_API::set_entries($module_name, $name_value_lists);
  }
  
  public function set_entries_fake_modules(array $module_def_list) {
    $result = array();
    
    foreach($module_def_list as $module_name=>$module_def_value) {
      $result[$module_name] = $this->set_entries_fake($module_name, 
          $module_def_value['max_records'], 
          $module_def_value['min_records']);
    }
    
    return $result;
  }
  
  public function prepare_entry_fields($module_name) {
    $name_value_list = array();
    $dictionary_module = $this->sugarcrm_config->get_dictionary($module_name);
    //$random_seed = $this->faker->randomNumber; //Initially added so that fields will be related with each other, for e.g. country and city. However faker formatters are not related with eatch other
  
    if(isset($dictionary_module) && isset($dictionary_module['fields'])) {
      foreach ($dictionary_module['fields'] as $dictFieldKey=>$dictFieldValue) {
        //$this->faker->seed($random_seed);
        $name = $dictFieldValue['name'];
        $type = $dictFieldValue['type'];
        $source = isset($dictFieldValue['source'])? $dictFieldValue['source']:null;
        $required = empty($dictFieldValue['required'])? 0:1;
        $len = isset($dictFieldValue['len'])? $dictFieldValue['len']:null;
        $vname = isset($dictFieldValue['vname'])? $dictFieldValue['vname']:null;
        $dbType = isset($dictFieldValue['dbType'])? $dictFieldValue['dbType']:null;
        
        if( $name!=='deleted' && !(isset($source) && $source===self::FIELD_SOURCE_NONDB) && $dbType!=='id' ) {
          switch($type) {
            case self::FIELD_TYPE_VARCHAR:
              $name_value_list[$name] = $this->handle_type_varchar($name);
              break;
            case self::FIELD_TYPE_ENUM && isset($dictFieldValue['options']):
              $name_value_list[$name] = $this->handle_type_enum($dictFieldValue['options']);
              break;
            case self::FIELD_TYPE_CURRENCY:
              $name_value_list[$name] = $this->faker->randomFloat($nbMaxDecimals=2, $min=100, $max=30000);
              break;
            case self::FIELD_TYPE_TEXT:
              $name_value_list[$name] = $this->faker->text(self::MAX_TEXT_LEN);
              break;
            case self::FIELD_TYPE_BOOL:
              $name_value_list[$name] = (int) $this->faker->boolean(50);
              break;
            case self::FIELD_TYPE_PHONE:
              $name_value_list[$name] = $this->faker->phoneNumber;
              break;
            case self::FIELD_TYPE_DATETIME:
              $name_value_list[$name] = self::format_datetime($this->faker->dateTime($max='now'));
              break;
            case self::FIELD_TYPE_DATE:
              $name_value_list[$name] = $this->faker->date($format='Y-m-d', $max='now');
              break;
            case self::FIELD_TYPE_URL:
              $name_value_list[$name] = $this->faker->url;
              break;
            case self::FIELD_TYPE_NAME:
              $name_value_list[$name] = $this->handle_type_varchar(
                strtolower($this->sugarcrm_config->get_mod_strings()[$module_name][$vname]));
              break;
            default:
              break;
          }
        }
        
      }
    }
    
    return $name_value_list;
  }
  
  public function prepare_entries_fields($module_name, $max_records=null, $min_records=null) {
    $name_value_lists = array();
    
    $min_records = self::get_min($min_records, self::MIN_RECORDS_PER_MODULE, self::MAX_RECORDS_PER_MODULE);
    $max_records = self::get_max($max_records, self::MIN_RECORDS_PER_MODULE, self::MAX_RECORDS_PER_MODULE);
    $number_records = $this->faker->numberBetween($min_records, $max_records);
    for($i=0; $i<$number_records; $i++)
      $name_value_lists[] = $this->prepare_entry_fields($module_name);
  
    return $name_value_lists;
  }
  
  public function set_relationships_fake_modules(array $module_record_list) {
    $result = array();
  
    foreach($module_record_list as $module_name=>$record_list) {
      $result[$module_name] = array();
      foreach($record_list->ids as $record_id) {
        $name_value_list = $this->prepare_entry_fields_related($module_name, $record_id, $module_record_list);
        $result[$module_name][] = SugarCRM_API::set_entry($module_name, $name_value_list);
      }
    }
  
    return $result;
  }
  
  public function prepare_entry_fields_related($module_name, $id, $module_record_list) {
    $name_value_list = array();
    $dictionary_module = $this->sugarcrm_config->get_dictionary($module_name);
    $name_value_list['id'] = $id;
  
    if(isset($dictionary_module) && isset($dictionary_module['fields'])) {
      foreach ($dictionary_module['fields'] as $dictFieldKey=>$dictFieldValue) {
        $name = $dictFieldValue['name'];
        $type = $dictFieldValue['type'];
        $related_module = isset($dictFieldValue['module'])? $dictFieldValue['module']:null;
        $field = isset($dictFieldValue['id_name'])? $dictFieldValue['id_name']:null;
  
        if($type===self::FIELD_TYPE_RELATE && isset($field)) {
          //NOTE Temporarily relationships with users set with administrator
          if($related_module===self::MODULE_NAME_USERS)
            $name_value_list[$field] = $this->get_user_id();
          
          else if(isset($module_record_list[$related_module])) {
            $record_list_c = clone $module_record_list[$related_module];
            if($module_name===$related_module) {
              unset($record_list_c->ids[array_search($id, $record_list_c->ids)]);
            }
            if(!empty($record_list_c->ids))
              $name_value_list[$field] = $record_list_c->ids[array_rand($record_list_c->ids)];
          }
        }
  
      }
    }
  
    return $name_value_list;
  }
  
  public function set_document_revisions_fake_modules(array $module_record_list, $max_revisions=null, $min_revisions=null) {
    $result = array();
  
    if(isset($module_record_list[self::MODULE_NAME_DOCUMENTS])) {
      $record_list = $module_record_list[self::MODULE_NAME_DOCUMENTS];
            
      foreach($record_list->ids as $record_id) {
        if(!isset($result[$record_id]))
          $result[$record_id] = array();
        $result[$record_id] = $this->set_document_revisions_fake($record_id, $max_revisions, $min_revisions);
      }
    }
  
    return $result;
  }
  
  public function set_document_revisions_fake($document_id, $max_revisions=null, $min_revisions=null) {
    $result = array();
  
    $attachment_detail_revision_list = $this->prepare_document_revisions($document_id, $max_revisions, $min_revisions);
    foreach($attachment_detail_revision_list as $attachment_detail_key=>$attachment_detail)
      $result[] = $this->set_document_revision($attachment_detail['document_id'], $attachment_detail['file'], 
          $attachment_detail['filename'], $attachment_detail['revision']);
  
    return $result;
  }
  
  public function prepare_document_revisions($document_id, $max_revisions=null, $min_revisions=null) {
    $attachment_detail_list = array();
  
    $documents_path = dirname(__FILE__).self::DOCUMENTS_PATH_RELATIVE;
    $documents_temp_path = self::DOCUMENTS_TEMP_PATH;
    
    $min_revisions = self::get_min($min_revisions, self::MIN_DOCUMENT_REVISIONS, self::MAX_DOCUMENT_REVISIONS);
    $max_revisions = self::get_max($max_revisions, self::MIN_DOCUMENT_REVISIONS, self::MAX_DOCUMENT_REVISIONS);
    $number_revisions = $this->faker->numberBetween($min_revisions, $max_revisions);
    for($i=0; $i<$number_revisions; $i++) {
      $filepath = $this->faker->file($sourceDir=$documents_path, $targetDir=$documents_temp_path);
      $filename = basename($filepath);
      $file_contents = file_get_contents($filepath);
      
      $attachment_detail_list[$i] = array();
      $attachment_detail_list[$i]['document_id'] = $document_id;
      $attachment_detail_list[$i]['file'] = $file_contents;
      $attachment_detail_list[$i]['filename'] = $filename;
      $attachment_detail_list[$i]['revision'] = $i+1;
    }
  
    return $attachment_detail_list;
  }
  
  protected function handle_type_varchar($name) {
    switch(true) {
      case strstr($name,'surname'):
      case strstr($name,'last_name'):
        return $this->faker->lastName;
        break;
      case strstr($name,'name'):
        return $this->faker->firstName;
        break;
      case strstr($name,'email'):
        return $this->faker->email;
        break;
      case strstr($name,'address_country'):
        return $this->faker->country;
        break;
      case strstr($name,'address_postalcode'):
        return $this->faker->postcode;
        break;
      case strstr($name,'address_state'):
        return $this->faker->state;
        break;
      case strstr($name,'address_city'):
        return $this->faker->city;
        break;
      case strstr($name,'address_street_2'):
        return $this->faker->secondaryAddress;
        break;
      case strstr($name,'address_street'):
      case strstr($name,'address_'):
        return $this->faker->streetAddress;
        break;
      case strstr($name,'code'):
      case strstr($name,'reference'):
      case strstr($name,'refn'):
      case strstr($name,'ref_n'):
        //return $this->faker->regexify('[A-Z0-9]{8,10}'); //Not yet part of faker latest release (1.4.0)
        return $this->faker->numerify('##########');
        break;
      default:
        return $this->faker->text(self::MAX_VARCHAR_LEN);
        break;
    }
  }
  
  protected function handle_type_enum($options_name) {
    return array_rand($this->sugarcrm_config->get_app_list_strings()[$options_name]);
  }
  
  private static function format_datetime(DateTime $subject, $format='Y-m-d H:i:s') {
    return $subject->format($format);
  }
  
  public static function get_min($min_param, $min_limit, $max_limit) {
    if( !(isset($min_param) && $min_param>=$min_limit && $min_param<=$max_limit) )
      return $min_limit;
    return $min_param;
  }
  
  public static function get_max($max_param, $min_limit, $max_limit) {
    if( !(isset($max_param) && $max_param<=$max_limit && $max_param>=$min_limit) )
      return $max_limit;
    return $max_param;
  }
  
  //NOTE Temporarily assigning everything to administrator
  protected function get_user_id() {
    return "1";
  }
  
}

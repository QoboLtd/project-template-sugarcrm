<?php
require (dirname(__FILE__) . '/sugarcrm-config.php');

class SugarCRM_API {
  
  protected static $session_id;
  protected $sugarcrm_config;
  
  public function __construct() {
    $this->set_sugarcrm_config(new SugarCRM_Config());
    $this->set_session_id(self::login($this->sugarcrm_config->get_options_api_username(), 
        md5($this->sugarcrm_config->get_options_api_password()), 
        $this->sugarcrm_config->get_options_api_url()));
    $this->sugarcrm_config->set_dictionary(json_decode($this->get_dictionary_c(), true));
    $this->sugarcrm_config->set_app_list_strings(json_decode($this->get_app_list_strings_c(), true));
    $this->sugarcrm_config->set_mod_strings(json_decode($this->get_mod_strings_c(), true));
    $this->sugarcrm_config->set_available_modules_populate(json_decode($this->get_available_modules_populate_c(), true));
  }
  
  public function get_session_id() {
    return $this->session_id;
  }
  
  public function set_session_id($session_id) {
    $this->session_id = $session_id;
  }
  
  public function get_sugarcrm_config() {
    return $this->sugarcrm_config;
  }
  
  public function set_sugarcrm_config(SugarCRM_Config $sugarcrm_config) {
    $this->sugarcrm_config = $sugarcrm_config;
  }
  
  /**
   * Makes a cURL request and returns parsed SugarCRM response in JSON format 
   *
   * @param method method name
   * @param parameters array of parameters
   * @param url rest url
   * @return result JSON format result
   */
  public static function call($method, $parameters, $url){
    $result = null;
    
    ob_start();
    $curl_request = curl_init();
    curl_setopt($curl_request, CURLOPT_URL, $url);
    curl_setopt($curl_request, CURLOPT_POST, 1);
    curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl_request, CURLOPT_HEADER, 1);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
    
    $jsonEncodedData = json_encode($parameters);
    $post = array(
      'method' => $method,
      'input_type' => 'JSON',
      'response_type' => 'JSON',
      'rest_data' => $jsonEncodedData
    );
    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
    
    $result = curl_exec($curl_request);
    curl_close($curl_request);
    $result = explode("\r\n\r\n", $result, 2);
    $result = json_decode($result[1]);
    ob_end_flush();
    
    return $result;
  }
  
  /**
   * Logins to SugarCRM and returns session id 
   *
   * @return session_id
   */
  public static function login($username, $password, $url){
    $parameters = array(
      'user_auth' => array(
        'user_name' => $username,
        'password' => $password,
        'version' => '1'
      ),
      'application_name' => 'RestTest',
      'name_value_list' => array(),
    );
  
    $ws_return = self::call('login', $parameters, $url);
  
    if(isset($ws_return->id)){
      return $ws_return->id;
    }
    //TODO handle response failure
  }
  
  /**
   * Handles call to SugarCRM
   *
   * @param method method name
   * @param parameters array of parameters
   * @return result JSON format result
   */
  public function handleCallToSugarCRM($method, $parameters){
    $result = null;
    
    //add session id at the start parameters array
    $parameters = array_merge(array('session' => $this->session_id), $parameters);
  
    //call API method
    $result = self::call($method, $parameters, $this->sugarcrm_config->get_options_api_url());
  
    //if session has expired, login again and call method again
    if(isset($result->name) && isset($result->number) &&
      $result->number==11){
      $this->set_session_id(self::login($this->sugarcrm_config->get_options_api_username(), 
          md5($this->sugarcrm_config->get_options_api_password()), 
          $this->sugarcrm_config->get_options_api_url()));
      $parameters['session'] = $this->session_id;
      $result = self::call($method, $parameters, $this->sugarcrm_config->get_options_api_url());
      //TODO handle response failure
    }
    if(isset($result->name) && isset($result->number) &&
      $result->number==-1){
      $result = null;
      //TODO handle response failure
    }
    
    return $result;
  }

  /**
   * Run Quick Repair and Rebuild
   * @return result
   */
  public function run_quick_repair_rebuild_c($autoexecute=true){
      $method = 'run_quick_repair_rebuild_c';
      $parameters = array(
        'autoexecute' => $autoexecute,
      );
  
      return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Returns list of avaialble modules
   * @return result JSON format result
   */
  public function get_available_modules($filter='all'){
    $method = 'get_available_modules';
    $parameters = array(
      'filter' => $filter,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves the list of field vardefs for a specific module
   * @return result JSON format result
   */
  public function get_module_fields($module_name, array $fields=null){
    $method = 'get_module_fields';
    $parameters = array(
      'module_name' => $module_name,
      'fields' => $fields,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves the MD5 hash of the vardefs for the specified modules
   * @return result JSON format result
   */
  public function get_module_fields_md5(array $module_names){
    $method = 'get_module_fields_md5';
    $parameters = array(
      'module_names' => $module_names,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves the language label strings for the specified modules
   * @return result JSON format result
   */
  public function get_language_definition(array $module_names, $md5=false){
    $method = 'get_language_definition';
    $parameters = array(
      'modules' => $module_names,
      'md5' => $md5,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves dictionary array
   * @return result JSON format result
   */
  public function get_dictionary_c(){
    $method = 'get_dictionary_c';
    $parameters = array();
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves mod_strings array
   * @return result JSON format result
   */
  public function get_mod_strings_c(){
    $method = 'get_mod_strings_c';
    $parameters = array();
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Retrieves app_list_strings array
   * @return result JSON format result
   */
  public function get_app_list_strings_c(){
    $method = 'get_app_list_strings_c';
    $parameters = array();
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Returns list of avaialble modules to populate
   * @return result JSON format result
   */
  public function get_available_modules_populate_c($filter='all'){
    $method = 'get_available_modules_populate_c';
    $parameters = array();
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Creates or updates a specific record
   * @return result JSON format result
   */
  public function set_entry($module_name, array $name_value_list=null){
    $method = 'set_entry';
    $parameters = array(
      'module_name' => $module_name,
      'name_value_list' => $name_value_list,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }


  /**
   * Creates or updates a list of records
   * @return result JSON format result
   */
  public function set_entries($module_name, array $name_value_lists=null){
    $method = 'set_entries';
    $parameters = array(
      'module_name' => $module_name,
      'name_value_lists' => $name_value_lists,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
  /**
   * Creates a new document revision for a specific document record
   * @return result JSON format result
   */
  public function set_document_revision($id, $file, $filename, $revision){
    $method = 'set_document_revision';
    $parameters = array(
      'note' => array(
        'id' => $id,
        'file' => base64_encode($file),
        'filename' => $filename,
        'revision' => $revision,
      ),
    );
    
    return $this->handleCallToSugarCRM($method, $parameters);
  }

  /**
   * Sets relationships between two records. You can relate multiple records to a single record using this
   * @return result JSON format result
   */
  public function set_relationship($module_name, $module_id, $link_field_name, array $related_ids, array $name_value_list=null, 
      $delete=0){
    $method = 'set_relationship';
    $parameters = array(
      'module_name' => $module_name,
      'module_id' => $module_id,
      'link_field_name' => $link_field_name,
      'related_ids' => $related_ids,
      'name_value_list' => $name_value_list,
      'delete' => $delete,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }

  /**
   * Sets multiple relationships between multiple record sets
   * @return result JSON format result
   */
  public function set_relationships(array $module_names, array $module_ids, array $link_field_names, array $related_ids, 
      array $name_value_lists=null, array $delete_array=null){
    $method = 'set_relationships';
    $parameters = array(
      'module_names' => $module_names,
      'module_ids' => $module_ids,
      'link_field_names' => $link_field_names,
      'related_ids' => $related_ids,
      'name_value_lists' => $name_value_lists,
      'delete_array' => $delete_array,
    );
  
    return $this->handleCallToSugarCRM($method, $parameters);
  }
  
}
<?php

class SugarCRM_Config {
  
  private $options;
  private $dictionary;
  private $app_list_strings;
  private $mod_strings;
  private $available_modules_populate;
  
  const OPTION_NAME_API = 'sugarcrm-api';
  const OPTION_NAME_API_USERNAME = 'ws_username';
  const OPTION_NAME_API_PASSWORD = 'ws_password';
  const OPTION_NAME_API_URL = 'ws_url';
  
  public function __construct() {
    require (dirname(__FILE__) . '/config/options.php');
    $this->set_options($options);
  }
  
  public function get_options() {
    return $this->options;
  }
  
  public function set_options($options) {
    $this->options = $options;
  }
  
  public function get_dictionary($module_name=null) {
    if(isset($module_name))
      return $this->dictionary[$this->get_dictionary_module_key($module_name)];
        
    return $this->dictionary;
  }
  
  public function set_dictionary($dictionary) {
    $this->dictionary = $dictionary;
  }
  
  public function get_app_list_strings() {
    return $this->app_list_strings;
  }
  
  public function set_app_list_strings($app_list_strings) {
    $this->app_list_strings = $app_list_strings;
  }
  
  public function get_mod_strings() {
    return $this->mod_strings;
  }
  
  public function set_mod_strings($mod_strings) {
    $this->mod_strings = $mod_strings;
  }
  
  public function get_available_modules_populate() {
    return $this->available_modules_populate;
  }
  
  public function set_available_modules_populate($available_modules_populate) {
    $this->available_modules_populate = $available_modules_populate;
  }
  
  public function get_options_api_username() {
    return $this->options[self::OPTION_NAME_API][self::OPTION_NAME_API_USERNAME];
  }
  
  public function get_options_api_password() {
    return $this->options[self::OPTION_NAME_API][self::OPTION_NAME_API_PASSWORD];
  }
  
  public function get_options_api_url() {
    return $this->options[self::OPTION_NAME_API][self::OPTION_NAME_API_URL];
  }

  public function get_dictionary_module_key($module_name) {
    if(isset($this->app_list_strings['moduleListSingular'][$module_name]))
      return $this->app_list_strings['moduleListSingular'][$module_name];

    return $module_name;
  }

}
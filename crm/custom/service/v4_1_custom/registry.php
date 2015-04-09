<?php
require_once('service/v4_1/registry.php');

class registry_v4_1_custom extends registry_v4_1
{
    protected function registerFunction()
    {
        parent::registerFunction();
        
        $this->serviceClass->registerFunction('get_dictionary_c', array('session'=>'xsd:string'), array('return'=>'xsd:string'));
        $this->serviceClass->registerFunction('get_mod_strings_c', array('session'=>'xsd:string'), array('return'=>'xsd:string'));
        $this->serviceClass->registerFunction('get_app_list_strings_c', array('session'=>'xsd:string'), array('return'=>'xsd:string'));
        $this->serviceClass->registerFunction('get_available_modules_populate_c', array('session'=>'xsd:string'), array('return'=>'xsd:string'));
        $this->serviceClass->registerFunction('run_quick_repair_rebuild_c', array('session'=>'xsd:string', 'autoexecute'=>'xsd:boolean'), array('return'=>'xsd:string'));
        
    }
}
?>

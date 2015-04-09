<?php
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('service/v4_1/SugarWebServiceImplv4_1.php');

class SugarWebServiceImplv4_1_custom extends SugarWebServiceImplv4_1
{
    /**
     * Validate session
     *
     * @param string $session
     * @return bool $return valid
     */
    protected function validate_session_c($session, $function_name=null, $module_name=null, $login_error_key='invalid_session', 
            $access_level='read', $module_access_level_error_key='no_access', $errorObject=null) {
        if (!isset($errorObject))
            $errorObject = new SoapError();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, $login_error_key, $module_name, $access_level, 
                $module_access_level_error_key, $errorObject)) {
            $GLOBALS['log']->info('End: SugarWebServiceImpl->'.$function_name);
            return 0;
        }
        
        return 1;
    }
    
    /**
     * Returns dictionary variable
     *
     * @param string $session
     * @return string $return (JSON format) dictionary array
     */
    function get_dictionary_c($session) {
        global $current_user;
        if(!$this->validate_session_c($session, __FUNCTION__) || !$current_user->is_admin)
            return;
        
        require_once('modules/ModuleBuilder/views/view.modulefields.php');
        $module_list = array_intersect($GLOBALS['moduleList'],array_keys($GLOBALS['beanList']));
        $dictionary_full = array();
        
        foreach($module_list as $module_name) {
            $objectName = BeanFactory::getObjectName($module_name);
            VardefManager::loadVardef($module_name, $objectName, true);
            global $dictionary;
            $dictionary_full[$objectName] = $dictionary[$objectName];
        }
        
        return json_encode($dictionary_full);
    }
    
    /**
     * Returns mod_strings variable
     *
     * @param string $session
     * @return string $return (JSON format) mod_strings array
     */
    function get_mod_strings_c($session) {
        global $current_user;
        if(!$this->validate_session_c($session, __FUNCTION__) || !$current_user->is_admin)
            return;
    
        require_once('modules/ModuleBuilder/views/view.modulefields.php');
        $module_list = array_intersect($GLOBALS['moduleList'],array_keys($GLOBALS['beanList']));
        $mod_strings = array();
    
        foreach($module_list as $module_name) {
            $objectName = BeanFactory::getObjectName($module_name);
            VardefManager::loadVardef($module_name, $objectName, true);
            global $dictionary;
            $mod_strings[$module_name] = return_module_language('en_US', $module_name);
        }
    
        return json_encode($mod_strings);
    }
    
    /**
     * Returns app_list_strings variable
     *
     * @param string $session
     * @return string $return (JSON format) app_list_strings array
     */
    function get_app_list_strings_c($session) {
        global $current_user;
        if(!$this->validate_session_c($session, __FUNCTION__) || !$current_user->is_admin)
            return;
        
        global $app_list_strings;
    
        return json_encode($app_list_strings);
    }
    
    /**
     * Returns available modules to populate
     *
     * @param string $session
     * @return string $return (JSON format) list of available modules to populate
     */
    function get_available_modules_populate_c($session) {
        global $current_user;
        if(!$this->validate_session_c($session, __FUNCTION__) || !$current_user->is_admin)
            return;
        
        require_once('modules/ModuleBuilder/views/view.modulefields.php');
        $module_list = array_intersect($GLOBALS['moduleList'],array_keys($GLOBALS['beanList']));
        $module_list = array_diff($module_list, $GLOBALS['modInvisList'], $GLOBALS['adminOnlyList']);
        $module_list = array_values($module_list);
        
        return json_encode($module_list);
    }
    
    /**
     * Run Quick Repair and Rebuild
     *
     * @param string $session
     * @param bool $autoexecute
     * @param bool $show_output
     * @return string $return output
     */
    function run_quick_repair_rebuild_c($session, $autoexecute=true) {
        global $current_user;
        if(!$this->validate_session_c($session, __FUNCTION__) || !$current_user->is_admin)
            return;
        
        require_once('modules/Administration/QuickRepairAndRebuild.php');
        $repair = new RepairAndClear();
        $repair->repairAndClearAll(array('clearAll'), array(translate('LBL_ALL_MODULES')), $autoexecute, false);
        return 1;
    }
    
    
}
?>

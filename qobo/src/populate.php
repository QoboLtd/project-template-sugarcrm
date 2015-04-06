#!/usr/bin/php
<?php

require_once (dirname(__FILE__) . '/../../vendor/autoload.php');
try {
        Dotenv::load(__DIR__ . '/../../');
        Dotenv::required(array('DB_NAME'));
}
catch (\Exception $e) {
        echo $e->getMessage();
        exit(1);
}

require_once (dirname(__FILE__) . '/SugarCRMFaker/sugarcrm-faker.php');
require_once (dirname(__FILE__) . '/populate_config.php');

$sugarcrm_faker = new SugarCRM_Faker();
$sugarcrm_faker->populate_modules($module_def_list);

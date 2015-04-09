<?php
/**
 * Define which fields to populate - if null default modules and configurations will be used
 * 
 * e.g.

$module_def_list = array(
    'Accounts' => array (
        'max_records' => 60,
        'min_records' => 50,
    ),
    'Documents' => array (
        'max_records' => 60,
        'min_records' => 50,
        'max_revisions' => 3,
        'min_revisions' => 1,
    ),
);
 */

$module_def_list = null;

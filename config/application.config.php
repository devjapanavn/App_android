<?php
include_once "helper.php";
$module = array("Api");
ini_set('max_execution_time', 123456);
return array(
    'modules' => $module,
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ),
);
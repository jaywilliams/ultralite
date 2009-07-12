<?php

error_reporting(E_ALL|E_STRICT);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/app'));

// Ensure classes/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/classes'),
    get_include_path(),
)));


//require_once APPLICATION_PATH . '/Bootstrap.php';
require_once APPLICATION_PATH . '/Login_test.php';

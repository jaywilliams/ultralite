<?php

require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Pixelpost_');
$autoloader->registerNamespace('Horde_');


$config =  Pixelpost_Config::current();


// $config =  Pixelpost_Config::set('mytest','never!');

// Pixelpost_Config::set("theme", "stardust");
$config->set("my-bool",false,true);

var_dump($config);

?>
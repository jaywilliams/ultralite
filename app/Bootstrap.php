<?php

header('Content-Type: text/html; charset=utf-8');


require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Pixelpost_');
$autoloader->registerNamespace('Horde_');



Pixelpost_DB::init('mysql'); 
// Pixelpost_DB::init('pdo'); 

Pixelpost_DB::connect('root', '', 'ultralite', 'localhost'); 
// Pixelpost_DB::connect('sqlite:'.APPLICATION_PATH.'/pixelpost.sqlite3'); 



/*
	Insert DB Test
*/
// $fields = array('id'=>rand(),'title'=>'MyTitle1','description'=>'bla','filename'=>'null.jpg','published'=>Pixelpost_DB::sysdate());
// $results = Pixelpost_DB::quick_insert('pixelpost', $fields);
// var_dump($results);


/*
	Select DB test
*/
$fields = array('*');
$results = Pixelpost_DB::quick_select('pixelpost_options', '*');
// var_dump($results);


$fields = array('option_value'=>'greyspace'.rand());
$results = Pixelpost_DB::quick_update('pixelpost_options', $fields, "option_name='theme'");
Pixelpost_DB::vardump();
var_dump($results);


# Display all DB Errors:
// var_dump(Pixelpost_DB::get_all_errors());


/*
	Escape Test
*/
// var_dump(Pixelpost_DB::escape('new/lives'));





/*
	Read Config Option Test
*/

#1
// $config =  Pixelpost_Config::current();
// var_dump($config);
// var_dump($config->theme);

#2
// var_dump(Pixelpost_Config::current()->theme);


/*
	Store Config Option Test
*/
#1
// Pixelpost_Config::set("theme", "greyspace");

#2
// $config =  Pixelpost_Config::current();
// var_dump($config->set("my-bool",false));

/*
	Delete Config Option Test
*/
// var_dump($config->remove("my-bool"));



?>
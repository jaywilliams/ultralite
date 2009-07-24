<?php

header('Content-Type: text/html; charset=utf-8');

// we need this old function file to make it work.....
require_once 'functions.php';

// Remove register globals, if applicable: 
unregister_globals();

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$namespaces[] = 'Pixelpost_';
$namespaces[] = 'Horde_';
$autoloader->registerNamespace($namespaces);

/**
 * First we have to try to get the config variable
 */
$config =  Pixelpost_Config::current();
//var_dump(Pixelpost_Config::current()->database['host']);

// Detects if mod_rewrite mode should be enabled
$config->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;

// Default Page Settings
$config->pagination = 0;
$config->total_pages = 0;

// Default (fallback) Template
$config->template   = "greyspace";

/** 
 * With the config in place we can get the db connection
 */
 
switch($config->database['adapter'])
{
	case 'sqlite':
	
		// Initialize Pixelpost_DB for SQLsite PDO
		Pixelpost_DB::init('pdo');
		
		// Make sure the file is writable, otherwise php will error out,
		// and won't be able to add anyting to the database.
		Pixelpost_DB::connect('sqlite:'.Pixelpost_Config::current()->database['sqlite']); 
		break;
		
	case 'mysql':
	default:
	
		// Initialize Pixelpost_DB for mySQL
		Pixelpost_DB::init('mysql');
		Pixelpost_DB::connect(
				$config->database['username'], 
				$config->database['password'], 
				$config->database['database'], 
				$config->database['host']);
		break;
}
/** 
 * Everything is in place now.
 */

 

//var_dump($config->database);
//var_dump(Pixelpost_DB::get_all_errors());		

	

// var_dump(Pixelpost_DB::get_all_errors());


 


// var_dump(APPLICATION_PATH);
// $test = New_Tet::getInstance();
// var_dump($test);
// var_dump('hi');




/*
	Insert DB Test
*/
// $fields = array('id'=>rand(),'title'=>'MyTitle1','description'=>'bla','filename'=>'null.jpg','published'=>Pixelpost_DB::sysdate());
// $results = Pixelpost_DB::quick_insert('pixelpost', $fields);
// var_dump($results);


/*
	Select DB test
*/
// $fields = array('*');
// $results = Pixelpost_DB::quick_select('pixelpost_options', '*');
// var_dump($results);


// $fields = array('option_value'=>'greyspace'.rand());
// $results = Pixelpost_DB::quick_update('pixelpost_options', $fields, "option_name='theme'");
// Pixelpost_DB::vardump();
// var_dump($results);


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
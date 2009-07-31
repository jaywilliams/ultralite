<?php

header('Content-Type: text/html; charset=utf-8');

// we need this old function file to make it work.....
require_once __LIB_PATH.'/functions.php';

// Remove register globals, if applicable: 
unregister_globals();

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$namespaces[] = 'Pixelpost_';
$namespaces[] = 'Horde_';
$namespaces[] = 'Web2BB_';

$autoloader->registerNamespace($namespaces);

/**
 * First we have to try to get the config variable
 */
$config =  Pixelpost_Config::current();
var_dump(Pixelpost_Config::current()->database['host']);

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
?>
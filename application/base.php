<?php defined('APPPATH') or die('No direct script access.');

// the application directory path
define('THMPATH', DOCROOT . '/content/themes');
define('PLGPATH', DOCROOT . '/content/plugins');
define('CSHPATH', DOCROOT . '/content/cache');
define('IMGPATH', DOCROOT . '/content/images');

// add the application to the include path
set_include_path(APPPATH);
set_include_path(DOCROOT);
set_include_path(APPPATH.'classes/');

// we need this old function file to make it work.....
require_once APPPATH.'libraries/functions.php';

// Remove register globals, if applicable:
unregister_globals();

// Remove magic_quotes, if applicable:
remove_magic_quotes();

/**
 * Initialize Autoloaders
 */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$namespaces[] = 'Pixelpost_';
$namespaces[] = 'Horde_';
$namespaces[] = 'Web2BB_';

$autoloader->registerNamespace($namespaces);

/**
 * Initialize Plugin Architecture
 */
Pixelpost_Plugin::getInstance();
Pixelpost_Plugin::executeAction('hook_init');

/**
 * Load the models and controllers
 */
spl_autoload_register(null, false);
spl_autoload_extensions('.php, .class.php, .lang.php');

spl_autoload_register('modelLoader');
spl_autoload_register('controllerLoader');

/**
 * Initialize Uri Class
 */
Web2BB_Uri::getInstance();

/**
 * Initialize Config Class
 */
$config = Pixelpost_Config::getInstance();

/**
 * Set the proper timezone
 */
date_default_timezone_set($config->timezone);
$config->current_time = date("Y-m-d H:i:s",time());

/**
 * Get the language file 
 */
if (!file_exists($language = APPPATH . '/languages/' . $config->locale . '.lang.php'))
	throw new Web2BB_Exception("Unable to open language file",E_ERROR);

	include $language;

/**
 * Initialize DB Class
 */
switch ($config->database['adapter'])
{
	case 'sqlite':

		// Initialize Pixelpost_DB for SQLsite PDO
		Pixelpost_DB::init('pdo');

		// Make sure the file is writable, otherwise php will error out,
		// and won't be able to add anyting to the database.
		Pixelpost_DB::connect('sqlite:' . $config->database['sqlite']);
		break;

	case 'mysql':
	default:

		// Initialize Pixelpost_DB for mySQL
		Pixelpost_DB::init('mysql');
		Pixelpost_DB::connect($config->database['username'], $config->database['password'], $config->database['database'], $config->database['host']);
		break;
}

Pixelpost_DB::set_table_prefix($config->database['prefix']);

if (!Pixelpost_DB::$connected)
	throw new Web2BB_Exception("Unable to connect to database", E_ERROR);
	

/*** set error handler level to E_WARNING ***/
// set_error_handler('web2bbErrorHandler', E_WARNING); 

/**
 * Setup Default Filters & Hooks
 */
Pixelpost_Plugin::registerFilter('filter_escape','entities');

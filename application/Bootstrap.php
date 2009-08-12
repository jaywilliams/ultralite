<?php

/**
 * Bootstrap.php is the file that takes care of the bootstrapping.
 * Bootstrapping means that every server request are funneled through 
 * a single (or a few) PHP file(s). This file will be the “bootstrapper” 
 * of our application. It will help instantiate objects that are needed 
 * by every page in general such as starting a session, connecting to a 
 * database, defining constants and default variables, etc.
 * 
 **/

try
{	
	// the application directory path
	define('__THEME_PATH', __SITE_PATH . '/content/themes');
	define('__PLUGIN_PATH', __SITE_PATH . '/content/plugins');

	// the classes directory path
	define('__CLASS_PATH', __APP_PATH . '/classes');
	define('__LIB_PATH', __APP_PATH . '/libraries');
	
	
	define('__DOC_ROOT', rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/'));

	// set the public web root path
	$path = '/' . trim(str_replace(__DOC_ROOT, '', __SITE_PATH), '/');
	define('__PUBLIC_PATH', $path);
	
	// add the application to the include path
	set_include_path(__APP_PATH);
	set_include_path(__SITE_PATH);
	set_include_path(__CLASS_PATH);
	
	// we need this old function file to make it work.....
	require_once __LIB_PATH . '/functions.php';

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
	 * Get the language file (we really need to find another approach)
	 */
	$file = __APP_PATH . '/languages/' . $config->locale . '.lang.php';
	if (file_exists($file)) {
		include $file;
	}else {
		throw new Exception("Unable to open language file");
	}
	
	// time settings
	date_default_timezone_set($config->timezone);
	$config->current_time = date("Y-m-d H:i:s",time());

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
	
	/*** set error handler level to E_WARNING ***/
	// set_error_handler('web2bbErrorHandler', $config->config_values['application']['error_reporting']);

	/**
	 * Setup Default Filters & Hooks
	 */
	Pixelpost_Plugin::registerFilter('filter_escape','entities');
	

}
catch (Web2BB_Exception $e)
{
	//show a 404 page here
	echo 'FATAL:<br />';
	echo $e->getMessage();
	echo $e->getLine();
}
catch (exception $e)
{
	echo $e->getMessage();
}

<?php
header('Content-Type: text/html; charset=utf-8');

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
	// we need this old function file to make it work.....
	require_once __LIB_PATH . '/functions.php';

	// Remove register globals, if applicable:
	unregister_globals();
	
	// Remove magic_quotes, if applicable:
	remove_magic_quotes();
	

	require_once 'Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();

	$namespaces[] = 'Pixelpost_';
	$namespaces[] = 'Horde_';
	$namespaces[] = 'Web2BB_';

	$autoloader->registerNamespace($namespaces);

	/**
	 * Load the models and controllers
	 */
	spl_autoload_register(null, false);
	spl_autoload_extensions('.php, .class.php, .lang.php');
	// model loader
	function modelLoader($class)
	{
		$class = strtolower($class);
		$models = array('icontroller.php', 'frontcontroller.php', 'view.php');
		$class = strtolower($class);
		$filename = $class . '.php';
		if (in_array($filename, $models))
		{
			$file = __APP_PATH . "/models/$filename";
		}
		else
		{
			$file = __APP_PATH . "/$class/models/$filename";
		}
		if (file_exists($file) == false)
		{
			return false;
		}

		include_once $file;
	}


	// autoload controllers
	function controllerLoader($class)
	{
		$class = str_replace('web2bb\\', '', $class);
		$module = str_replace('Controller', '', $class);
		$filename = $class . '.php';
		$file = strtolower(__APP_PATH . "/modules/$module/controllers/$filename");
		if (file_exists($file) == false)
		{
			return false;
		}
		include_once $file;
	}

	spl_autoload_register('modelLoader');
	spl_autoload_register('controllerLoader');

	/**
	 * First we have to try to get the config variable
	 */
	$config = Pixelpost_Config::getInstance();
	//var_dump(Pixelpost_Config::getInstance()->database['host']);

	/**
	 * Initialise some default settings
	 */
	// Default Page Settings
	$config->pagination = 0;
	$config->total_pages = 0;

	// Default (fallback) Template
	$config->template = "greyspace_neue";
	
	/**
	 * Get the language file (we really need to find another approach)
	 */
	$file = __APP_PATH . '/languages/' . Pixelpost_Config::getInstance()->locale . '.lang.php';
	include $file;
	// alias the lang class (e.g. make the en_US class available as lang)
	//class_alias( $lang, '\lang');
	
	// time settings
	date_default_timezone_set($config->timezone);
	$config->current_time = date("Y-m-d H:i:s",time());

	// Detects if mod_rewrite mode should be enabled
	$config->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;

	/**
	 * With the config in place we can get the db connection
	 */

	switch ($config->database['adapter'])
	{
		case 'sqlite':

			// Initialize Pixelpost_DB for SQLsite PDO
			Pixelpost_DB::init('pdo');

			// Make sure the file is writable, otherwise php will error out,
			// and won't be able to add anyting to the database.
			Pixelpost_DB::connect('sqlite:' . Pixelpost_Config::getInstance()->database['sqlite']);
			break;

		case 'mysql':
		default:

			// Initialize Pixelpost_DB for mySQL
			Pixelpost_DB::init('mysql');
			Pixelpost_DB::connect($config->database['username'], $config->database['password'], $config->database['database'], $config->database['host']);
			break;
	}
	
	/**
	 * Get the plugins in gear
	 */
	
	$plugins = new Pixelpost_Plugin();
	// Load list of plugins from config:
	$plugins->plugins = & $config->plugins;
	$plugins->get();
	$plugins->do_action('global_pre');
	
	/**
	 * Everything is in place now.
	 */

}
catch (Web2BB_Exception$e)
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

?>
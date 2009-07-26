<?php


try
{
	/*** define the site path ***/
	$site_path = realpath(dirname(__FILE__));
	define ('__SITE_PATH', $site_path);

	/*** the application directory path ***/
	define ('__APP_PATH', __SITE_PATH.'/application');

	/*** set the web root path ***/
	$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __SITE_PATH);
	define('__DOC_ROOT', $path);

	spl_autoload_register(null, false);

	spl_autoload_extensions('.php, .inc, .class.php');

	/*** model loader ***/
	function modelLoader($class)
	{
		$filename = strtolower($class) . '.php';
		$file = __APP_PATH . '/models/' . $filename;
		if (file_exists($file) == false)
		{
			return false;
		}
		include $file;
	}


	/*** autoload controllers ***/
	function controllerLoader($class)
	{
		$filename = strtolower($class) . '.php';
		$file = __APP_PATH . '/controllers/' . $filename;
		if (file_exists($file) == false)
		{
			return false;
		}
		include $file;
	}

	/*** autoload libs ***/
	function libLoader($class)
	{
		$filename = strtolower($class) . '.class.php';
		$file = __APP_PATH . '/lib/' . $filename;
		if (file_exists($file) == false)
		{
			return false;
		}
		include $file;
	}

	spl_autoload_register('modelLoader');
	spl_autoload_register('controllerLoader');
	spl_autoload_register('libLoader');

	/*** set config ***/
	$config = config::getInstance();

	/*** set the timezone ***/
	date_default_timezone_set($config->config_values['application']['timezone']);

	/**
	 *
	 * @custom error function to throw exception
	 *
	 * @param int $errno The error number
	 *
	 * @param string $errmsg The error message
	 *
	 */
	function web2bbErrorHandler($errno, $errmsg)
	{
		throw new web2bbException($errmsg, $errno);
	}
	/*** set error handler level to E_WARNING ***/
	set_error_handler('web2bbErrorHandler', $config->config_values['application']['error_reporting']);

	//Initialize the FrontController
	$front = FrontController::getInstance();
	$front->route();

	echo $front->getBody();
}
catch(web2bbException $e)
{
	echo 'FATAL:<br />';
}

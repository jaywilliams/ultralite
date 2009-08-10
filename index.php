<?php

/**
 * File containing the index for system.
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 * @filesource
 *
 */

header('Content-Type: text/html; charset=utf-8');
define('ULTRALITE',true);

/**
 * When debugging, or developing, be sure this is enabled:
 */
error_reporting(E_ALL|E_STRICT);

try
{
	// define the site path
	$site_path = rtrim(str_replace('\\', '/', realpath(dirname(__file__))), '/');
	define('__SITE_PATH', $site_path);

	// the application directory path
	define('__APP_PATH', __SITE_PATH . '/application');
	define('__THEME_PATH', __SITE_PATH . '/content/themes');
	define('__PLUGINS_PATH', __SITE_PATH . '/content/plugins');

	// the classes directory path
	define('__CLASS_PATH', __APP_PATH . '/classes');
	define('__LIB_PATH', __APP_PATH . '/libraries');
	
	// add the application to the include path
	set_include_path(__APP_PATH);
	set_include_path(__SITE_PATH);
	set_include_path(__CLASS_PATH);

	define('__DOC_ROOT', rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/'));

	// set the public web root path
	$path = '/' . trim(str_replace(__DOC_ROOT, '', __SITE_PATH), '/');
	define('__PUBLIC_PATH', $path);

	/**
	 * First attempt of replacing the autoloader with our previous autoloader from
	 * Rebase
	 */
	// Get the bootstrap file
	require_once __APP_PATH.'/Bootstrap.php';
	

	//
	//
	//	// set the timezone
	//	date_default_timezone_set($config->config_values['application']['timezone']);
	//
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
			throw new Web2BB_Exception($errmsg, $errno);
		}
		/*** set error handler level to E_WARNING ***/
		// set_error_handler('web2bbErrorHandler', $config->config_values['application']['error_reporting']);
	
		// Initialize the FrontController
		$front = FrontController::getInstance();
		$front->route();
	
		echo $front->getBody();
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

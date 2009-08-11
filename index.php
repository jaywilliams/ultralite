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
	/**
	 * Define Paths
	 * 
	 * Note: All paths do not have a trailing slash
	 */
	$site_path = rtrim(str_replace('\\', '/', realpath(dirname(__file__))), '/');
	define('__SITE_PATH', $site_path);
	
	define('__APP_PATH', __SITE_PATH . '/application');
	
	/**
	 * Initialize Bootstrap Routine
	 */
	require_once __APP_PATH.'/Bootstrap.php';
	
		// Initialize the FrontController
		$front = FrontController::getInstance();
		
		Pixelpost_Plugin::executeAction('hook_init');
		
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

Pixelpost_Plugin::executeAction('hook_exit');

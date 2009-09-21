<?php
/**
 * Pixelpost Ultralite Index
 *
 * @version 2.0
 * @package pixelpost
 **/

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
	define('__SITE_PATH', rtrim(str_replace('\\', '/', realpath(dirname(__file__))), '/'));
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
	/**
	 * If an error occurred, attempt to load a "fancy" error view, 
	 * otherwise, simply echo the error.
	 */
	if (file_exists($path = __APP_PATH.'/modules/error/views/'.$e->getCode().'.phtml')) {
		include($path);
	}elseif (file_exists($path = __APP_PATH.'/modules/error/views/index.phtml')) {
		include($path);
	}else{
		echo "<h1>Error ".$e->getCode()."</h1><p>".$e->getMessage()."</p><p>".$e->getLine() . " (" .basename($e->getFile()).")</p>";
	}
}

Pixelpost_Plugin::executeAction('hook_exit');

/**
 * To prevent possible issues, do not add a closing "?>" tag.
 */
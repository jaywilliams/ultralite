<?php defined('APPPATH') or die('No direct script access.');

/**
 * Disable any potentially bad PHP features:
 */
unregister_globals();
remove_magic_quotes();


/**
 * Initialize the Class Autoloader
 */
set_include_path(APPPATH.'classes/');
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true); // Load any namespace


/**
 * Initialize Language and Model Autoloader
 */
$resourceLoader = new Zend_Loader_Autoloader_Module(array(
	'basePath'	=> APPPATH,
	'namespace' => '',
));



$resourceLoader->addResourceType('models', 'models', 'Model');
$resourceLoader->addResourceType('modules', 'modules', 'Module');


/**
 * Initialize Uri Class
 */
Pixelpost_Uri::getInstance();


/**
 * Initialize Config Class
 */
$config = Pixelpost_Config::getInstance();


/**
 * Initialize DB Class
 */
switch ($config->database['adapter'])
{
	case 'sqlite':

		Pixelpost_DB::init( 'pdo' );
		Pixelpost_DB::connect( 'sqlite:'.$config->database['sqlite'] );
		break;

	case 'mysql':
	default:

		Pixelpost_DB::init( 'mysql' );
		Pixelpost_DB::connect(	$config->database['username'], 
								$config->database['password'], 
								$config->database['database'], 
								$config->database['host'] );
		break;
}

Pixelpost_DB::set_table_prefix( $config->database['prefix'] );

if (!Pixelpost_DB::$connected)
	throw new Pixelpost_Exception("Unable to connect to database", E_ERROR);


/**
 * Initialize Page
 */
$front = Model_Front::getInstance();

/**
 * Initialize Timezone
 */
if(!empty($config->timezone))
	date_default_timezone_set($config->timezone);
$config->current_time = date("Y-m-d H:i:s",time());

/**
 * Initialize Plugin Hooks
 */
Pixelpost_Plugin::executeAction('hook_init');


/**
 * Helper Functions
 */


/**
 * Turn register globals off.
 *
 * @return null Will return null if register_globals PHP directive was disabled
 */
function unregister_globals() {
	if ( !ini_get('register_globals') )
		return;

	if ( isset($_REQUEST['GLOBALS']) )
		die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}


/**
 * Remove magic quotes for incoming GET/POST/Cookie data.
 *
 * @return null Will return null if magic_quotes_gpc PHP directive was disabled
 */
function remove_magic_quotes() {
	if (!get_magic_quotes_gpc())
		return;
		
	$_GET     = array_map('stripslashes', $_GET);
	$_POST    = array_map('stripslashes', $_POST);
	$_COOKIE  = array_map('stripslashes', $_COOKIE);
}

/**
 * To prevent possible issues, do not add a closing "?>" tag.
 */
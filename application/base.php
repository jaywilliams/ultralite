<?php defined('APPPATH') or die('No direct script access.');

// Remove register globals, if applicable:
unregister_globals();

// Remove magic_quotes, if applicable:
remove_magic_quotes();


/**
 * Class Autoloader
 */
set_include_path(APPPATH.'classes/');
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true); // Load any namespace

/**
 * Language and Model Autoloader
 */
$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	'basePath'	=> APPPATH,
	'namespace' => '',
));

$resourceLoader->addResourceType('models', 'models', 'Model');
			   // ->addResourceType('language', 'languages', 'Language')

/**
 * Initialize Uri Class
 */
Pixelpost_Uri::getInstance();

/**
 * Initialize Config Class
 */
$config = Pixelpost_Config::getInstance();

/**
 * Working Timezone
 */
if(!empty($config->timezone))
	date_default_timezone_set($config->timezone);
$config->current_time = date("Y-m-d H:i:s",time());


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
	throw new Web2BB_Exception("Unable to connect to database", E_ERROR);





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


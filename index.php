<?php
error_reporting(E_ALL);
/*

!! ATTENTION !!

Lets try and keep variable names easy to understand and less cryptic!
I want to be able to know what I'm dealing with just be reading the variable name...

I.E. :	Pixelpost 1.71 Uses the following variable, $cdate.
		
		While all us devs know what it is by repeated use, it may seem less obvious to others,
		so... take the extra second to spell it out: $time->current
		
		Much Better!


Next Step:
	
	Clean up the code:
	Move the functions to their own file, and go through the code to make sure it all makes sense.
	Also be sure to add *helpful* comments were needed to better clarify what is going on.

Future Tasks:

	The plugin system is probably going to be the next "big" thing, because we're going to need to 
	add comments, categories and/or tags next, and those would best be served as plugins.

	For a plugin system, perhaps we could list the plugins in the config file in some sort of array.
	So the plugin could specify which controller it should be loaded for, that way it won't even be
	included if it doesn't need to be in that controller, or if necessary, a plugin could be loaded 
	for every controller.  Anything for speed right?

*/

// Defined for use within included files.
define('ULTRALITE',true);


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
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}

unregister_globals();


// Initialize the config object and set a few default settings:
$config->site->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;
// Total Number of pages for this view, set by the controller:
$config->site->total_pages = 0;

// Split up the config file by refrence for easy access
$site     = & $config->site;
$language = & $config->language;
$time     = & $config->time;
$database = & $config->database;

// Load the settings (can override default settings):
require_once 'settings.php';

// Determine the current datetime
$time->current = gmdate("Y-m-d H:i:s",time()+(3600 * (int) $time->offset));

require_once 'libraries/db.php';

/**
 * Load the correct database
 */
switch($database->type)
{
	case 'sqlite':
	
		require_once 'libraries/db.pdo.php';
		
		// Initialize ezSQL for SQLsite PDO
		$db = new ezSQL_pdo();
		
		// Make sure the file is writable, otherwise php will error out,
		// and won't be able to add anyting to the database.
		$db->connect("sqlite:{$database->sqlite->database}");
		break;
		
	case 'mysql':
	default:
	
		require_once 'libraries/db.mysql.php';
		
		// Initialize ezSQL for mySQL
		$db = new ezSQL_mysql();
		$db->quick_connect($database->mysql->username, 
						   $database->mysql->password, 
						   $database->mysql->database, 
						   $database->mysql->hostname);
		break;
}


/**
 * Renders the URL in the proper format.
 * 
 * Examples:
 * url("view=rss");
 * url("view=post&id=3&extra=my-title");
 * url("view=post&id=3&extra=my-title&custom=true");
 *
 * @param string $url Query (do not include http:// or /)
 * @param bool $echo Disabled by default, this optional option will echo the result rather than return it silently.
 * @return string $output Properly formated URL
 */
function url($url='',$echo=false)
{
	global $config,$view;
	
	/**
	 * Prepare the variables we'll be using in this function
	 */
	$output    = "";
	$url_param = array();
	$url       = ltrim($url,"/");
		         parse_str($url,$url);

	if (array_key_exists('view',$url))
		$url_param['view'] = $url['view'];
	elseif(array_key_exists('id',$url))
		$url_param['view'] = $url['view'] = $view;
	
	if (array_key_exists('id',$url) && array_key_exists('view',$url))
		$url_param['id'] = $url['id'];
	
	if (array_key_exists('extra',$url) && array_key_exists('id',$url) && array_key_exists('view',$url))
		$url_param['extra'] = $url['extra'];
		
	if (array_key_exists('page',$url) && ($url['page'] > 1 && $url['page'] <= $config->site->total_pages))
		$url_param['page'] = $url['page'];
	
	// We can remove them from the $url array,
	// as they now exist in the $url_param array.
	unset($url['view'],$url['id'],$url['extra'],$url['page']);
	
	if (count($url) > 0)
		$url_param['unknown'] = http_build_query($url);
	
	/**
	 * Check to see if we are in mod_rewrite mode,
	 * If so, convert the url to the clean URL format.
	 * 
	 * Otherwise, remove $url_param['unknown'] and merge
	 * the remaining unknown parameters to create the url.
	 */
	if ($config->site->mod_rewrite)
	{
		foreach ($url_param as $key => $value) {
			switch ($key) {
				case 'view':
					$output .= "$value";
					break;
				case 'id':
				case 'extra':
					$output .= "/$value";
					break;
				case 'page':
					$output .= "/page/$value";
					break;
				case 'unknown':
					$output .= "?$value";
					break;
			}
		}
	}
	else
	{
		unset($url_param['unknown']);
		$url_param = array_merge($url_param,$url);
		
		$output    = '?'.http_build_query($url_param);

	}
	
	/**
	 * Output the code
	 * Or, if specified 
	 */
	if ($echo)
		echo $output;
	else
		return $output;
}

/**
 * Template Tag
 * 
 * Includes the specified template tag function.
 * 
 * For example, if you included this in a template:
 * 		<?php tt('thumbnails'); ?>
 * 
 * It would cause the script to run the function:
 * 		tt_thumbnails(); 
 * 
 * If that function didn't exist, it would return false.
 *
 * @param string $template_tag The name of the template tag function, minus the "tt_" prefix. 
 * @param mixed $options Optional settings used to control the template tag
 * @return mixed false if function doesn't exist
 */
function tt($template_tag='',$options='')
{
	if (substr($template_tag,0,3) != 'tt_') {
		$template_tag = "tt_$template_tag";
	}
	
	if (function_exists("$template_tag")) {
		return $template_tag($options);
	}
	
	return false;
}


/**
 * echo with html entities conversion.
 * Useful for templates, and anywhere where you want to echo 
 * a string and make sure it creates correct HTML.
 *
 * @param string the value to echo
 */
function eprint($value='')
{
	echo escape($value);
}

/**
 * echo with html entities conversion.
 * Useful for templates, and anywhere where you want to echo 
 * a string and make sure it creates correct HTML.
 *
 * @param string the value to echo
 */
function escape($value='')
{
	return htmlentities($value,ENT_QUOTES);
}


/**
 * Grab the current View, if the view isn't set, default to "post".
 * Note: Views must be lower case and contain only letters (a-z).
 */
$view = (isset($_GET['view']) && !empty($_GET['view']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['view'])) : 'post';
$site->view = $view;

$id = (isset($_GET['id']) && !empty($_GET['id']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['id'])) : '';
$site->id = $id;

$extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra']) : array();
// $extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? explode('/',preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra'])) : array();
$site->extra = $extra;

$page = (isset($_GET['page']) && !empty($_GET['page']) ) ? (int) $_GET['page'] : 1;
$site->page = $page;

// Check to see if the view controller exists, and if so, include it:
if (file_exists("controllers/$view.php"))
{
	/**
	 * @todo Include sub-views as well, possibly via a custom function.
	 */
	require_once "controllers/$view.php";
}

// If a template page exists for this view, include that as well:
if (file_exists("themes/{$site->template}/$view.php"))
{
	require_once "themes/{$site->template}/$view.php";
}

// If no view or controller exist, display an error:
if ( (! file_exists("controllers/$view.php")) && (! file_exists("themes/{$site->template}/$view.php")) )
{
	// Error? Splash Screen?
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
}


?>
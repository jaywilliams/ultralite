<?php
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

/**
 * Enable this when developing/debugging 
 */
error_reporting(E_ALL);

/**
 * Some included files may require that this be set to prevent them being run outside of Ultralite.
 */
define('ULTRALITE',true);


require_once 'libraries/functions.php';

// Remove register globals, if applicable: 
unregister_globals();

/**
 * Initialize Configuration Class
 * 
 * And define the settings file defaults.
 */
$config = new stdClass();

// Detects if mod_rewrite mode should be enabled
$config->site->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;

// Default Page Settings
$config->site->pagination = 0;
$config->site->total_pages = 0;

// Default (fallback) Template
$config->site->template   = "greyspace";

// Default timezone
$config->time->timezone = date_default_timezone_get();

// Split up the configuration object by reference for easy access
$site     = & $config->site;
$language = & $config->language;
$time     = & $config->time;
$database = & $config->database;

// Load the settings (can override default settings):
if (file_exists('settings.php')) {
	require_once 'settings.php';
}else {
	die("Sorry, but we can't run Ultralite if <em>settings.php</em> hasn't been configured.");
}

/**
 * Initialize Plugins Class
 */
require_once 'libraries/plugins.php';
$plugins = new plugins;
// Plugins array
$config->plugins = $plugins->get();

/**
 * This option is used in the SQL queries to filter out future posts, 
 * so it's important that the time offset is set correctly. After setting 
 * this every date/time function will use the correct timezone.
 */
date_default_timezone_set($time->timezone);
$time->current = date("Y-m-d H:i:s",time());

require_once 'libraries/db.php';

/**
 * Load the correct database
 * 
 * Currently only two types of databases are supported, SQLite and mySQL.
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
 * View
 * $site->view
 * 
 * This is variable stores the current view mode, such as "post", "archive" or "rss".
 * If no view is defined, it will fall back to "post".
 * 
 * Examples: 
 * 		?view=post
 * 		?view=rss
 * 
 * Views may contain lower case letters, numbers, hyphens, and underscores, although only letters are recommended.
 */
$view = (isset($_GET['view']) && !empty($_GET['view']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['view'])) : 'post';
$site->view = & $view;

/**
 * ID
 * $site->id
 * 
 * This is an optional parameter which can be used to define a specific post or category out of a view.
 * Some views may use this to specify a specific mode, for the $extra parameter, such as "tagged".
 * 
 * Examples: 
 * 		?view=post&id=5
 * 		?view=archive&id=2009
 * 
 * IDs may contain lower case letters, numbers, hyphens, and underscores.
 */
$id = (isset($_GET['id']) && !empty($_GET['id']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['id'])) : '';
$site->id = & $id;

/**
 * Extra
 * $site->extra
 * 
 * This is an optional parameter which can be used to define a extra information for a specific View or ID.
 * It can not be used if an ID has not been previous specified. Extras can also contains slashes which can
 * be used to include even more information if necessary.
 * 
 * Examples: 
 * 		?view=post&id=5&extra=my-image-title
 * 		?view=archive&id=2009&extra=02
 * 		?view=archive&id=tagged&extra=blue
 * 		?view=universe&id=milky-way&extra=solar-system/earth
 * 
 * Extras may contain lower case letters, numbers, hyphens, underscores, and slashes.
 * 
 * 
 * @todo Decide if $extra should be an array by default, or simply a string, like the other options.
 */
$extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra']) : array(); // String Version
// $extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? explode('/',preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra'])) : array(); // Array Version 
$site->extra = & $extra;


/**
 * Page
 * $site->page
 * 
 * Some views will need to break content up into pages.  $page will always be an integer, and will default to 1.
 * 
 * Examples: 
 * 		?view=archive&page=2
 * 		?view=archive&id=2009&extra=02&page=4
 * 		?view=universe&id=milky-way&extra=solar-system/earth&page=3
 * 
 * Pages may contain numbers only.
 */
$page = (isset($_GET['page']) && !empty($_GET['page']) ) ? (int) $_GET['page'] : 1;
$site->page = & $page;



/**
 * Controller
 * 
 * The controller is where all of the "logic" code is stored for a specific view.
 */
if (file_exists("controllers/$view.php"))
{
	/**
	 * @todo Possibly include sub-views?
	 */
	require_once "controllers/$view.php";
}


/**
 * Template
 * 
 * The template page can use the variables and template tags created by the controller.
 */
if (file_exists("themes/{$site->template}/$view.php"))
{
	require_once "themes/{$site->template}/$view.php";
}


/**
 * ERROR 404
 * 
 * If both the controller and the template page are missing, we need to display a legit, but helpful error 404 page.
 * 
 * @todo Possibly add some fancy error or splash page, so it doesn't look too unfriendly.
 */
if ( (! file_exists("controllers/$view.php")) && (! file_exists("themes/{$site->template}/$view.php")) )
{
	header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"{$config->site->url}\">home page</a>.");
}


?>
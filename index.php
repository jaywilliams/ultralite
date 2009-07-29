<?php
/*

!! ATTENTION !!

Lets try and keep variable names easy to understand and less cryptic!
I want to be able to know what I'm dealing with just be reading the variable name...

I.E. :	Pixelpost 1.71 Uses the following variable, $cdate.
		
		While all us devs know what it is by repeated use, it may seem less obvious to others,
		so... take the extra second to spell it out: $config->current
		
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
error_reporting(E_ALL|E_STRICT);

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
$config->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;

// Default Page Settings
$config->pagination = 0;
$config->total_pages = 0;

// Default (fallback) Template
$config->template   = "greyspace";

// Default timezone
$config->timezone = @date_default_timezone_get();

// Split up the configuration object by reference for easy access
// $config     = & $config->site;
// $language = & $config->language;
// $config     = & $config->time;
// $database = & $config->database;

// Load the settings (can override default settings):
if (file_exists('settings.php')) {
	require_once 'settings.php';
}else {
	die("Sorry, but we can't run Ultralite if <em>settings.php</em> hasn't been configured.");
}

/**
 * Plugins Class
 */
require_once 'libraries/plugins.php';

$plugins = new plugins();
// Load list of plugins from config:
$plugins->plugins = & $config->plugins;
$plugins->get();

$plugins->do_action('global_pre');

/**
 * This option is used in the SQL queries to filter out future posts, 
 * so it's important that the time offset is set correctly. After setting 
 * this every date/time function will use the correct timezone.
 */
date_default_timezone_set($config->timezone);
$config->current_time = date("Y-m-d H:i:s",time());

require_once 'libraries/db.php';

/**
 * Load the correct database
 * 
 * Currently only two types of databases are supported, SQLite and mySQL.
 */
switch($config->database_type)
{
	case 'sqlite':
	
		require_once 'libraries/db.pdo.php';
		
		// Initialize ezSQL for SQLsite PDO
		$db = new ezSQL_pdo();
		
		// Make sure the file is writable, otherwise php will error out,
		// and won't be able to add anyting to the database.
		$db->connect("sqlite:{$config->sqlite_database}");
		break;
		
	case 'mysql':
	default:
	
		require_once 'libraries/db.mysql.php';
		
		// Initialize ezSQL for mySQL
		$db = new ezSQL_mysql();
		$db->quick_connect($config->mysql_username, 
						   $config->mysql_password, 
						   $config->mysql_database, 
						   $config->mysql_hostname);
		break;
}



/**
 * View
 * $config->view
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
$config->view = (isset($_GET['view']) && !empty($_GET['view']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['view'])) : 'post';
// $plugins->apply_filters('view',$config->view);

/**
 * ID
 * $config->id
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
$config->id = (isset($_GET['id']) && !empty($_GET['id']) ) ? preg_replace('/[^a-z0-9+_\-]/','', strtolower($_GET['id'])) : '';
// $plugins->apply_filters('id',$config->id);

/**
 * Extra
 * $config->extra
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
$config->extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra']) : ''; // String Version
// $extra = (isset($_GET['extra']) && !empty($_GET['extra']) ) ? explode('/',preg_replace('/[^a-z0-9+_\-\/]/','',$_GET['extra'])) : array(); // Array Version 
// $plugins->apply_filters('extra',$config->extra);

/**
 * Page
 * $config->page
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
$config->page = (isset($_GET['page']) && !empty($_GET['page']) ) ? (int) $_GET['page'] : 1;
// $plugins->apply_filters('page',$config->page);

/**
 * Apply Filters (by reference) to the entire $config object!
 * 
 * This is mostly just a tech-demo, we probably won't keep this actual code.
 * You can apply filters from a plugin, like so:
 * 
 * $this->add_filter('config_title', 'my_plugin_filer',10);
 * (This will modify the $config->title option)
 * 
 */

foreach ($config as $key => &$value) {
	$plugins->apply_filters("config_$key",$value);
}

/**
 * Controller
 * 
 * The controller is where all of the "logic" code is stored for a specific view.
 */
$plugins->do_action('controller_pre');
$plugins->do_action($config->view.'_pre');
if (file_exists("controllers/controller_$config->view.php"))
{
	/**
	 * @todo Possibly include sub-views?
	 */
	require_once "controllers/controller_$config->view.php";
}


/**
 * Apply Filters (by reference) to the entire controller object!
 * 
 * $this->add_filter('post_title', 'my_plugin_filer',10);
 * (This will modify the $post->title variable)
 * 
 */

eval("if(isset(\$$config->view)){\$controller = & \$$config->view;}else{\$controller = false;}");

if ($controller) {
	foreach ($controller as $key => &$value) {
		$plugins->apply_filters("{$config->view}_$key",$value);
		// var_dump("{$config->view}_$key",$value);
	}
}


$plugins->do_action($config->view.'_post');
$plugins->do_action('controller_post');

/**
 * Template
 * 
 * The template page can use the variables and template tags created by the controller.
 */
if (file_exists("themes/{$config->template}/theme_$config->view.php"))
{
	require_once "themes/{$config->template}/theme_$config->view.php";
}


/**
 * ERROR 404
 * 
 * If both the controller and the template page are missing, we need to display a legit, but helpful error 404 page.
 * 
 * @todo Possibly add some fancy error or splash page, so it doesn't look too unfriendly.
 */
if ( (! file_exists("controllers/controller_$config->view.php")) && (! file_exists("themes/{$config->template}/theme_$config->view.php")) )
{
	header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"{$config->url}\">home page</a>.");
}

$plugins->do_action('global_post');

?>
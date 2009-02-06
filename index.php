<?php
error_reporting(E_ALL);
/*

!! ATTENTION !!

This is just a bare-bones test of how a template file can be included.

None of the variables passed from the DB to template are cleaned...

ETC.

We may aslo want to specify the image & theme folder path in the settings file instead of hard coding it.

!! ALSO !!

Lets try and keep variable names easy to understand and less cryptic!
I want to be able to know what I'm dealing with just be reading the variable name...

I.E. :	Pixelpost 1.71 Uses the following variable, $cdate.
		
		While all us devs know what it is by repeated use, it may seem less obvious to others,
		so... take the extra second to spell it out: $time->current
		
		Much Better!


!! MOVE !!

Yes, you heard right, we need to move controler specific logic out from the index.php file, 
and move it into the correct controler. That way if you'r creating an RSS feed, 
you won't have to execute other non-essential code.

Of course the best way to handle this is always up for debate. :)

*/

// Defined for use within included files.
define('ULTRALITE',true);


// Initialize the config object and set a few default settings:
$config->site->mod_rewrite = (isset($_GET['mod_rewrite']) && $_GET['mod_rewrite'] == "true") ? true : false;

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
 * Grab the current View, if the view isn't set, default to "post".
 * Note: Views must be lower case and contain only letters (a-z).
 */
$view = (isset($_GET['view']) && !empty($_GET['view']) ) ? preg_replace('/[^a-z]/','', strtolower($_GET['view'])) : 'post';

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
if ( ! (file_exists("controllers/$view.php") && file_exists("themes/{$site->template}/$view.php")) )
{
	// Error? Splash Screen?
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
}


?>
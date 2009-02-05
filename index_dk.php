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

require_once 'settings.php';
require_once 'libraries/db.php';

// Split up the config file by refrence for easy access
$site     = & $config->site;
$language = & $config->language;
$time     = & $config->time;
$database = & $config->database;


// Determine the current datetime
$time->current = gmdate("Y-m-d H:i:s",time()+(3600 * (int) $time->offset));


/**
 * Load the correct database
 */
switch ($database->type)
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


// Include the post template!
require_once "controllers/post.php";
require_once "themes/{$site->template}/post.php";


?>
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

require_once 'settings.php';
require_once 'libraries/db.php';

// Split up the config file by refrence for easy access
$site     = & $config->site;
$language = & $config->language;
$time     = & $config->time;
$database = & $config->database;

// Determine the current datetime
$time->current = gmdate("Y-m-d H:i:s",time()+(3600 * $time->offset));


/**
 * Load the correct database
 */
switch ($database->type) {
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
		$db->quick_connect( $database->mysql->username, 
							$database->mysql->password, 
							$database->mysql->database, 
							$database->mysql->hostname);
		break;
}



// Clean the image id number. Set to int 0 if invalid OR empty.
$image->id = (isset($_GET['post']) && (int) $_GET['post'] > 0 )? (int) $_GET['post'] : 0;


if($image->id > 0)
{
	$sql = "SELECT * FROM pixelpost WHERE id = '$image->id' AND published <= '{$time->current}' LIMIT 1";
}
else
{
	$sql = "SELECT * FROM pixelpost WHERE published <= '{$time->current}' LIMIT 1";
}


// Grab the data object from the DB. Returns null on failure.
$image = $db->get_row($sql);

// Only load the template if the query was successful.
// We can display a nice error or splash screen otherwise...
if (!is_object($image)) {
	// Error? Splash Screen?
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
}


// Set the variables
$image_info			=	getimagesize('images/'.$image->filename);

$image->width		=	$image_info[0];
$image->height		=	$image_info[1];
$image->dimensions	=	$image_info[3];


/*
	Get the Next Image Information:
*/
$sql = "SELECT * FROM pixelpost WHERE (published > '$image->published') and (published<='{$time->current}') ORDER BY published ASC LIMIT 0,1";
$next_image = $db->get_row($sql);

$sql = "SELECT * FROM pixelpost WHERE (published < '$image->published') and (published<='{$time->current}') ORDER BY published DESC LIMIT 0,1";
$previous_image = $db->get_row($sql);


// Include the post template!
include_once "themes/{$site->template}/post.php";


?>
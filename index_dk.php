<?php
// error_reporting(E_ALL);
/*

!! ATTENTION !!

This is just a bare-bones test of how a template file can be included.

None of the variables passed from the DB to template are cleaned...

No fancy pagination (yet)...

No way to connect to a MySQL DB if that is whats desired...

ETC.

We may aslo want to specify the image & theme folder path in the settings file instead of hard coding it.

!! ALSO !!

Lets try and keep variable names easy to understand and less cryptic!
I want to be able to know what I'm dealing with just be reading the variable name...

I.E. :	Pixelpost 1.71 Uses the following variable, $cdate.
		
		While all us devs know what it is by repeated use, it may seem less obvious to others,
		so... take the extra second to spell it out: $current_datetime
		
		Much Better!
*/

require_once 'settings.php';
require_once 'ezsql/db.php';
require_once 'ezsql/db.pdo.php';
// require_once 'ezsql/db.mysql.php';


// User timezone, will return 0 if not an integer.
$user_timezone = (int) $config['user']['timezone'];


// Determine the current datetime
$current_datetime = gmdate("Y-m-d H:i:s",time()+(3600 * $user_timezone));


// This is how to initialse ezsql for sqlite PDO
$db = new ezSQL_pdo();
// $db = new ezSQL_mysql();


// Make sure the file is writable, otherwise php will error out,
// and won't be able to add anyting to the database.
$db->connect('sqlite:'.$config['database']['sqlite']['database']);
// $db->quick_connect($config['database']['mysql']['username'],$config['database']['mysql']['password'],$config['database']['mysql']['database'],$config['database']['mysql']['hostname']);


// Clean the post id number. Set to int 0 if invalid OR empty.
$post_id = (isset($_GET['post']) && (int) $_GET['post'] > 0 )? (int) $_GET['post'] : 0;


if($post_id > 0)
{
	$sql = "SELECT * FROM pixelpost WHERE id = '$post_id' AND published <= '$current_datetime' LIMIT 1";
}
else
{
	$sql = "SELECT * FROM pixelpost WHERE published <= '$current_datetime' LIMIT 1";
}


// Grab the data object from the DB. Returns null on failure.
$image = $db->get_row($sql);


// Only load the template if the query was successful.
// We can display a nice error or splash screen otherwise...
if($image !== null)
{
	// Set the variables
	$site->title		=	$config['site']['title'];
	$site->slogan		=	$config['site']['slogan'];
	$site->language		=	$config['site']['language'];

	$image_info			=	getimagesize('images/'.$image->filename);

	$image->width		=	$image_info[0];
	$image->height		=	$image_info[1];
	$image->dimensions	=	$image_info[3];


	/*
		Get the Next Image Information:
	*/
	$sql = "SELECT * FROM pixelpost WHERE (published > '$image->published') and (published<='$current_datetime') ORDER BY published ASC LIMIT 0,1";
	$next_image = $db->get_row($sql);
	
	$sql = "SELECT * FROM pixelpost WHERE (published < '$image->published') and (published<='$current_datetime') ORDER BY published DESC LIMIT 0,1";
	$previous_image = $db->get_row($sql);


	// Include the image template!
	include_once "themes/{$config['site']['template']}/image.php";
}
else
{
	// Error? Splash Screen?
}

?>
<?php

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


// User timezone, will return 0 if not an integer.
$user_timezone = (int) $config['user']['timezone'];


// Determine the current datetime
$current_datetime = gmdate("Y-m-d H:i:s",time()+(3600 * $user_timezone));


// This is how to initialse ezsql for sqlite PDO
$db = new ezSQL_pdo();


// Make sure the file is writable, otherwise php will error out,
// and won't be able to add anyting to the database.
$db->connect('sqlite:'.$config['database']['sqlite']['database']);


// As an extra precaution, lets unset the database array as it *should* no longer be needed.
// This may have to be moved to a different spot in the future if we decide to keep it.
unset($config['database']);


// Clean the post id number. Set to int 0 if invalid OR empty.
$post_id = (isset($_GET['post']) && is_numeric($_GET['post']) && $_GET['post'] > 0) ? (int) $_GET['post'] : 0;


if($post_id > 0)
{
	$image_sql = "SELECT * FROM pixelpost WHERE id = '$post_id' AND published <= '$current_datetime' LIMIT 1";
}
else
{
	$image_sql = "SELECT * FROM pixelpost WHERE published <= '$current_datetime' LIMIT 1";
}


// Grab the data object from the DB. Returns null on failure.
$image_data = $db->get_results($image_sql);
$image_data	= $image_data[0];


// Only load the template if the query was successful.
// We can display a nice error or splash screen otherwise...
if($image_data !== null)
{
	// Set the variables
	$site_title			=	$config['site']['title'];
	$site_slogan		=	$config['site']['slogan'];
	$site_language		=	$config['site']['language'];

	$image_id			=	$image_data->id;
	$image_title		=	$image_data->title;
	$image_description	=	$image_data->description;
	$image_published	=	$image_data->published;
	$image_filename		=	$image_data->filename;

	$image_info			=	getimagesize('images/'.$image_filename);

	$image_width		=	$image_info[0];
	$image_height		=	$image_info[1];
	$image_dimensions	=	$image_info[3];


	// Include the image template!
	include_once "themes/{$config['site']['template']}/image.php";
}
else
{
	// Error? Splash Screen?
}

?>
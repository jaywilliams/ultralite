<?php

/*

!! ATTENTION !!

This is just a bare-bones test of how a template file can be included.

None of the variables passed from the DB to template are cleaned...

No fancy pagination (yet)...

No way to connect to a MySQL DB if that is whats desired...

ETC.

We may aslo want to specify the image & theme folder path in the settings file instead of hard coding it.

*/

require_once 'settings.php';
require_once 'ezsql/db.php';
require_once 'ezsql/db.pdo.php';


// This is how to initialse ezsql for sqlite PDO
$db = new ezSQL_pdo();


// Make sure the file is writable, otherwise php will error out,
// and won't be able to add anyting to the database.
$db->connect('sqlite:'.$config['database']['sqlite']['database']);


// Grab the data from the DB
$image_data = $db->get_results("SELECT * FROM pixelpost WHERE id = '1'");
$image_data	= $image_data[0];


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
include_once 'themes/'.$config['site']['template'].'/image.php';

?>
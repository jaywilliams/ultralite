<?php

/**
 * Any code used to create the variables and functions necessary 
 * for the post (image) template should go in here.
 * 
 * For example, the code which figures out the current, next, and previous images.
 * 
 * Proposed URL Structure:
 * 
 * # Show Image #3
 * ?view=post&id=3
 * 
 * # Show Image #3 (mod_rewrite)
 * /post/3
 * 
 * # Show Image #3 with optional slug (mod_rewrite)
 * /post/3/my-photo-title
 * 
 * Thought... the post view is special, so it should be the default view.  
 * If nothing else is specified, the script should fall back to that view.
 * So URLs like this should work in theory:
 *
 * # Show Image #3
 * ?id=3
 * 
 * @package ultralite
 **/


// Prevent direct file access.
if(!defined('ULTRALITE')) { die(); }


// Clean the image id number. Set to int 0 if invalid OR empty.
$image->id = (isset($_GET['id']) && (int) $_GET['id'] > 0 ) ? (int) $_GET['id'] : 0;


if($image->id > 0)
{
	$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$image->id' AND `published` <= '{$time->current}' LIMIT 0,1";
}
else
{
	$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` ASC LIMIT 0,1";
}


// Grab the data object from the DB. Returns null on failure.
$image = $db->get_row($sql);

// Only load the template if the query was successful.
// We can display a nice error or splash screen otherwise...
if(!is_object($image))
{
	// Error? Splash Screen?
	die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
}

// Set the variables
$image_info			=	getimagesize('images/'.$image->filename);

$image->width		=	$image_info[0];
$image->height		=	$image_info[1];
$image->dimensions	=	$image_info[3];


// Retrieve the Next image information:
$sql	 = "SELECT * FROM `pixelpost` WHERE (`published` > '$image->published') and (`published` <= '{$time->current}') ORDER BY `published` ASC LIMIT 0,1";

$next_image = $db->get_row($sql);
if(!is_object($next_image))
{
	// Lets wrap around to the first image.
	
	// Retrieve the First image information:
	$sql			= "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` ASC LIMIT 0,1";
	$next_image		= $db->get_row($sql);
}


// Retrieve the Prev image information:
$sql	 = "SELECT * FROM `pixelpost` WHERE (`published` < '$image->published') and (`published` <= '{$time->current}') ORDER BY `published` DESC LIMIT 0,1";

$previous_image = $db->get_row($sql);
if(!is_object($previous_image))
{
	// Lets wrap around to the last image.
		
	// Retrieve the Last image information:
	$sql			= "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` DESC LIMIT 0,1";
	$previous_image = $db->get_row($sql);
}

?>
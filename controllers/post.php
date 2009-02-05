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
$image->id = (isset($_GET['post']) && (int) $_GET['post'] > 0 ) ? (int) $_GET['post'] : 0;


if($image->id > 0)
{
	$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$image->id' AND `published` <= '{$time->current}' LIMIT 0,1";
}
else
{
	$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' LIMIT 0,1";
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
$next_sql = "SELECT * FROM `pixelpost` WHERE (`published` > '$image->published') and (`published` <= '{$time->current}') ORDER BY `published` ASC LIMIT 0,1";

$next_image = $db->get_row($next_sql);
if($next_image == null)
{
	// Lets wrap around to the first image.
	
	// Retrieve the id of the first image.
	$first_image_id = "SELECT MIN(`id`) AS `minid` FROM `pixelpost` WHERE `published` <= '{$time->current}' LIMIT 0,1";
	$first_image_id = $db->get_row($first_image_id);	
	
	// Retrieve the First image information:
	$first_image	= "SELECT * FROM `pixelpost` WHERE `id` <= '{$first_image_id->minid}' LIMIT 1";
	$next_image		= $db->get_row($first_image);
}


// Retrieve the Prev image information:
$prev_sql = "SELECT * FROM `pixelpost` WHERE (`published` < '$image->published') and (`published` <= '{$time->current}') ORDER BY `published` DESC LIMIT 0,1";

$previous_image = $db->get_row($prev_sql);
if($previous_image == null)
{
	// Lets wrap around to the last image.
	
	// Retrieve the id of the last image.
	$last_image_id	= "SELECT MAX(`id`) AS `maxid` FROM `pixelpost` WHERE `published` <= '{$time->current}' LIMIT 0,1";
	$last_image_id	= $db->get_row($last_image_id);
		
	// Retrieve the Last image information:
	$last_image		= "SELECT * FROM `pixelpost` WHERE `id` <= '{$last_image_id->maxid}' LIMIT 1";
	$previous_image = $db->get_row($last_image);
}

?>
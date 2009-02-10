<?php

/**
 * Any code used to create the variables and functions necessary 
 * for the archive template should go in here.
 * 
 * Proposed URL Structure:
 *
 * # Show Archive
 * ?view=archive
 *
 * # Show archived images tagged as 'sports'
 * ?view=archive&tag=sports
 * 
 * # Show archived images tagged as 'sports' (mod_rewrite)
 * /archive/sports
 *
 * 
 * @package ultralite
 **/


// Prevent direct file access.
if(!defined('ULTRALITE')) { die(); }


$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` ASC";


// Store the thumbnail data array within a variable
$image->thumbnails = $db->get_results($sql);


// Tack on thumbnail data to the thumbnails array
foreach($image->thumbnails as $key => $thumbnail)
{
	// Set the variables
	$image_info = getimagesize('images/'.$thumbnail->filename);
		
	// Image width and height are divided by 4 to make the image smaller.
	// This is only a temp fix until actual thumbnailed images become a reality.
	$image->thumbnails[$key]->width			=	$image_info[0]/4;
	$image->thumbnails[$key]->height		=	$image_info[1]/4;
	$image->thumbnails[$key]->dimensions	=	$image_info[3];
}

?>
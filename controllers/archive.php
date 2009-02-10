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

$thumbnails = '';
foreach($db->get_results($sql) as $image)
{
	// Set the variables
	$image_info			=	getimagesize('images/'.$image->filename);
		
	// Image width and height are divided by 4 to make the image smaller.
	// This is only a temp fix until actual thumbnailed images become a reality.
	$image->width		=	$image_info[0]/4;
	$image->height		=	$image_info[1]/4;
	$image->dimensions	=	$image_info[3];
	
	$thumbnails .= "<a href=\"".url("view=post&id={$image->id}")."\"> <img src=\"images/{$image->filename}\" alt=\"{$image->title}\" width=\"{$image->width}\" height=\"{$image->height}\" /> </a>\n";
}

?>
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


// If another controller has already created a query, 
// run with that, rather than create our own:
if (!isset($image->thumbnails)) {

	
	if ($site->pagination > 0)
	{
		
		$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$time->current}'";
		// Get total images publically available
		$image->total = (int) $db->get_var($sql);
		// Determine the total number of available pages
		$image->total_pages = (int) ceil($image->total/$site->pagination);
		
		// The page doesn't exist!
		if ($image->total_pages < $site->page) {
			die("Sorry, we don't have anymore to show!");
		}

		// The database needs to know which row we need to start with:
		$range  = (int) ($site->page-1) * $site->pagination;
		
		$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` ASC LIMIT $range, $site->pagination";
	}
	else
	{
		$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` ASC";
	}
	

	
	// Store the thumbnails array
	$image->thumbnails = $db->get_results($sql);
	
	// var_dump($db);
}

// Tack on thumbnail data to the thumbnails array
foreach($image->thumbnails as $key => $thumbnail)
{
	$image_info = getimagesize('images/'.$thumbnail->filename);
		
	// Image width and height are divided by 4 to make the image smaller.
	// This is only a temp fix until actual thumbnailed images become a reality.
	$image->thumbnails[$key]->width			=	$image_info[0]/4;
	$image->thumbnails[$key]->height		=	$image_info[1]/4;
	$image->thumbnails[$key]->dimensions	=	$image_info[3];
}

?>
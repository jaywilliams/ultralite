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
 * ?view=archive&id=tagged&view=sports
 * 
 * # Show archived images tagged as 'sports' (mod_rewrite)
 * /archive/tagged/sports
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
		$site->total_pages = (int) ceil($image->total/$site->pagination);
		
		// The page doesn't exist!
		if ($site->total_pages < $site->page) {
			die("Sorry, we don't have anymore pages to show!");
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

}

// Tack on thumbnail data to the thumbnails array
foreach($image->thumbnails as $key => $thumbnail)
{
	$image_info = getimagesize('thumbnails/thumb_'.$thumbnail->filename);
		
	$image->thumbnails[$key]->width			=	$image_info[0];
	$image->thumbnails[$key]->height		=	$image_info[1];
	$image->thumbnails[$key]->dimensions	=	$image_info[3];
}

/**
 * TEMPLATE TAGS
 */


function tt_thumbnails($options='')
{
	global $image;
	
	/*
		Default Options for this Template Tag
	*/
	$mode = 'forward';
	// $echo = 'true';
	
	// Get the user-set options:
	parse_str($options);
	
	if ($mode == 'reverse') {
		$thumbnails = array_reverse($image->thumbnails);
	}else {
		$thumbnails = & $image->thumbnails;
	}
	
	foreach ($thumbnails as $thumbnail) {
		echo(
			"<a href=\"".url("view=post&id={$thumbnail->id}")."\">".
				"<img src=\"thumbnails/thumb_{$thumbnail->filename}\" alt=\"".escape($thumbnail->title)."\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />".
			"</a>\n"
		);
	}
}

?>
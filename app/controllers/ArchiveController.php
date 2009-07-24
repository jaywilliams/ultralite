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


$archive->title = 'The Past';

// If another controller has already created a query, 
// run with that, rather than create our own:
if (!isset($archive->thumbnails)) {
	
	
	if ($config->pagination > 0)
	{
		
		$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$config->current_time}'";
		// Get total images publically available
		$image->total = (int) $db->get_var($sql);
		// Determine the total number of available pages
		$config->total_pages = (int) ceil($image->total/$config->pagination);
		
		// The page doesn't exist!
		if ($config->total_pages < $config->page) {
			die("Sorry, we don't have anymore pages to show!");
		}

		// The database needs to know which row we need to start with:
		$range  = (int) ($config->page-1) * $config->pagination;
		
		$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC LIMIT $range, $config->pagination";
	}
	else
	{
		$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC";
	}
	
	// Store the thumbnails array
	$archive->thumbnails = Pixelpost_DB::get_results($sql);

}

// Tack on thumbnail data to the thumbnails array
foreach($archive->thumbnails as $key => $thumbnail)
{
	$image_info = getimagesize('content/images/thumb_'.$thumbnail->filename);
		
	$archive->thumbnails[$key]->width		=	$image_info[0];
	$archive->thumbnails[$key]->height		=	$image_info[1];
	$archive->thumbnails[$key]->dimensions	=	$image_info[3];
}

/**
 * TEMPLATE TAGS
 */


function tt_thumbnails($options='')
{
	global $archive;
	
	/*
		Default Options for this Template Tag
	*/
	$mode = 'forward';
	// $echo = 'true';
	
	// Get the user-set options:
	parse_str($options);
	
	if ($mode == 'reverse') {
		$thumbnails = array_reverse($archive->thumbnails);
	}else {
		$thumbnails = & $archive->thumbnails;
	}
	
	foreach ($thumbnails as $thumbnail) {
		echo(
			"<a href=\"".url("view=post&id={$thumbnail->id}")."\">".
				"<img src=\"content/images/thumb_{$thumbnail->filename}\" alt=\"".escape($thumbnail->title)."\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />".
			"</a>"
		);
	}
}

?>
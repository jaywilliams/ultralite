<?php

/**
 * Any code used to create the variables and functions necessary 
 * for the archive (tagging portion) should go in here.
 * 
 * Proposed URL Structure:
 *
 * # Show All Tags
 * ?view=tagged
 *
 * # Show archived images tagged as 'sports'
 * ?view=tagged&id=sports
 * 
 * # Show archived images tagged as 'sports' (mod_rewrite)
 * /tagged/sports
 *
 * 
 * @package ultralite
 **/


// Prevent direct file access.
if(!defined('ULTRALITE')) { die(); }


if (!isset($image->thumbnails)) {
	$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY random() ASC";
	
	// Store the thumbnails array
	$image->thumbnails = $db->get_results($sql);
}

// Bring our custom sql code into Archive:
$view = 'archive';
include_once('controllers/archive.php');




?>
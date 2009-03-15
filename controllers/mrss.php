<?php

/**
 * Media RSS feed
 * 
 * # Show Media RSS Feed
 * ?view=mrss
 * 
 * # Show Media RSS Feed (mod_rewrite)
 * /mrss
 * 
 * @package ultralite
 **/

// Prevent direct file access.
if(!defined('ULTRALITE')) { die(); }



// Require the feed class
require_once 'libraries/rss.feed.php';


// Initiate a new RSS feed
$feed = new RSS();


// Set the feed variables
$feed->title		= & $site->title;

$feed->link			= & $site->url;

$feed->description	= & $site->tagline;

$feed->language		= & $language->locale;

// Add Media RSS:
$feed->namespace	.= ' xmlns:media="http://search.yahoo.com/mrss/"';


// Query the database, retrieve the 10 most recent photos
$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` DESC LIMIT 0,10";


// Create a feed item for each image
foreach($db->get_results($sql) as $image)
{	
	// Initiate a new RSS feed item
	$item = new RSSItem();
	
	// Determine the images dimensions and mime type
	$image_info			= getimagesize('images/'.$image->filename);
	
	$item->title		= $image->title;
	
	$item->link			= "{$feed->link}".url("view=post&id={$image->id}");
	
	$item->description	= "<img src=\"{$feed->link}images/{$image->filename}\" alt=\"{$image->title}\" {$image_info[3]} /><br />{$image->description}";
	
	$item->setPubDate($image->published);
	
	$image_filesize		= filesize('images/'.$image->filename);
	
	// Get Thumbnail info, for Media RSS:
	$thumb_info			= getimagesize('thumbnails/thumb_'.$image->filename);
	
	// Add Media RSS Tags:
	$item->addTag('media:title',escape($image->title));
	$item->addTag('media:description',escape($image->description),'type="html"');
	$item->addTag('media:content','',"url=\"{$feed->link}images/{$image->filename}\" fileSize=\"$image_filesize\" type=\"$image_info[mime]\" $image_info[3]");
	$item->addTag('media:thumbnail','',"url=\"{$feed->link}thumbnails/thumb_{$image->filename}\" $thumb_info[3]");
	
	// Add the item to the feed
	$feed->addItem($item);
}


// Echo out the feed and XML header(s)
$feed->serve();

?>
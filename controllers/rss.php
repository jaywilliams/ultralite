<?php

/**
 * Rather than using a separate file for the view (template), this controller will be 
 * both a controller and a view by outputting the code at the end and exiting.
 * 
 * # Show RSS Feed
 * ?view=rss
 * 
 * # Show RSS Feed (mod_rewrite)
 * /rss
 * 
 * @package ultralite
 **/

// Prevent direct file access.
if(!defined('ULTRALITE')) { die(); }


// We can move this later on. true/1 OR false/0
$config->feed->enclosure = 1;


// Require the feed class
require_once 'libraries/rss.feed.php';


// Initiate a new RSS feed
$feed = new RSS();


// Set the feed variables
$feed->title		= $site->title;

$feed->link			= $site->siteurl;

$feed->description	= $site->slogan;

$feed->language		= $language->locale;


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
	
	$item->link			= "{$feed->link}?view=post&id={$image->id}";
	
	$item->description	= "<img src=\"{$feed->link}images/{$image->filename}\" alt=\"{$image->title}\" {$image_info[3]} /><br />{$image->description}";
	
	$item->setPubDate($image->published);
	
	if($config->feed->enclosure)
	{
		// Determine the images filesize for use in enclosures
		$image_filesize		= filesize('images/'.$image->filename);
		
		$item->enclosure("{$feed->link}images/{$image->filename}", $image_info['mime'], $image_filesize);
	}
	
	// Add the item to the feed
	$feed->addItem($item);
}


// Echo out the feed and XML header(s)
$feed->serve();

?>
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


// Require the feed class
require_once 'libraries/rss.feed.php';


// Initiate a new RSS feed
$feed = new RSS();

$feed->title       = $site->title;
$feed->link        = 'http://example.com';
$feed->description = $site->slogan;


// Query the database
$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$time->current}' ORDER BY `published` DESC LIMIT 0,10";


// Create the RSS feed tiems
foreach($db->get_results($sql) as $image)
{
	$image_info			=	getimagesize('images/'.$image->filename);
	$image->dimensions	=	$image_info[3];
	
	$description = "<img src=\"images/$image->filename\" alt=\"$image->title\" $image->dimensions /><br />$image->description";
	
	$item = new RSSItem();
	
	$item->title = $image->title;
	$item->link  = "$feed->link/$image->id";
	$item->setPubDate($image->published);
	$item->description = $description;
	$feed->addItem($item);
}


// Echo out the feed and XML header(s)
$feed->serve();

?>
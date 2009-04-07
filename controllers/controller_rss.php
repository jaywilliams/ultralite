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
$rss_class = 'RSS';
$plugins->apply_filters("rss_class",$rss_class);

$rss = new $rss_class();


// We can move this later on. true/1 OR false/0
$rss->enclosure = 1;
$plugins->apply_filters("rss_enclosure",$rss->enclosure);

// Set the feed variables
$rss->title		= & $config->title;

$rss->link			= & $config->url;

$rss->description	= & $config->tagline;

$rss->language		= & $language->locale;

// Query the database, retrieve the 10 most recent photos
$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` DESC LIMIT 0,10";
$plugins->apply_filters("rss_sql",$sql);

$plugins->do_action('rss_before_query',$item);


$rss_item_class = 'RSSItem';
$plugins->apply_filters("rss_item_class",$rss_item_class);

// Create a feed item for each image
foreach($db->get_results($sql) as $image)
{	
	// Initiate a new RSS feed item
	$item = new $rss_item_class();
	
	// Determine the images dimensions and mime type
	$image_info			= getimagesize('images/'.$image->filename);
	
	$item->title		= $image->title;
	
	$item->link			= "{$rss->link}".url("view=post&id={$image->id}");
	
	$item->description	= "<img src=\"{$rss->link}images/{$image->filename}\" alt=\"{$image->title}\" {$image_info[3]} /><br />{$image->description}";
	
	$item->setPubDate($image->published);
	
	if($rss->enclosure)
	{
		// Determine the images filesize for use in enclosures
		$image_filesize		= filesize('images/'.$image->filename);
		
		$item->enclosure("{$rss->link}images/{$image->filename}", $image_info['mime'], $image_filesize);
	}
	
	$plugins->do_action('rss_item',$item);
	
	// Add the item to the feed
	$rss->addItem($item);
}





// Echo out the feed and XML header(s), after any plugins 
// have had a chance to finish their jobs
$plugins->add_action('rss_post','rss_serve');

function rss_serve(){
	global $rss;
	$rss->serve();
}
?>
<?php

/**
 * For a plugin to be included, you MUST define at the very least, a plugin name.
 */
/*
	Plugin Name: Media RSS
*/


/**
 * Change the site title, and the title for the archive and post pages.
 */
$this->add_filter('rss_namespace', 'mrss_namespace');

function mrss_namespace(&$namespace)
{
	
	$namespace	.= ' xmlns:media="http://search.yahoo.com/mrss/"';
}

$this->add_filter('rss_enclosure', 'mrss_enclosure');

function mrss_enclosure(&$enclosure)
{
	
	$enclosure	= 0;
}


$this->add_action('rss_item', 'mrss_item');

function mrss_item()
{
	global $rss, $item, $image, $image_info;
	
	
		$image_filesize		= filesize('images/'.$image->filename);

		// Get Thumbnail info, for Media RSS:
		$thumb_info			= getimagesize('thumbnails/thumb_'.$image->filename);

		// Add Media RSS Tags:
		$item->addTag('media:title',escape($image->title));
		$item->addTag('media:description',escape($image->description),'type="html"');
		$item->addTag('media:content','',"url=\"{$rss->link}images/{$image->filename}\" fileSize=\"$image_filesize\" type=\"$image_info[mime]\" $image_info[3]");
		$item->addTag('media:thumbnail','',"url=\"{$rss->link}thumbnails/thumb_{$image->filename}\" $thumb_info[3]");

}

?>
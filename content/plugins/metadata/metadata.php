<?php

/**
 * For a plugin to be included, you MUST define at the very least, a plugin name.
 */
/*
	Plugin Name: Example Plugin
*/

/**
 * Additionally, you can include other information if you wish:
 */
/*
	Plugin URI: http://pixelpost.org/extend/addons/example/
	Description: This plugin gets the metadata from the array.
	Version: 1.0
	Author: Team Pixelpost
	Author URI: http://pixelpost.org/
*/

Pixelpost_Plugin::registerAction('hook_posts', 'getMetadata',10,1);

/**
 * Example hook
 * 
 * This function adds a new element: $posts->slug
 * And it appends the slug to the end of the permalink
 */
function getMetadata(&$posts)
{
	foreach ($posts as $key => $post) 
	{
		// Read the metadata from each file and add it to the posts array:
		$posts[$key]->metadata = Pixelpost_Metadata::readMeta($posts[$key]->filename); 
	}
}


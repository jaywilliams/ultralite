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

Pixelpost_Plugin::registerAction('hook_posts', 'getCategory',10,1);

/**
 * Example hook
 * 
 * This function adds a new element: $posts->slug
 * And it appends the slug to the end of the permalink
 */
function getCategory(&$posts)
{
	$cats = new Pixelpost_Hierarchy('categories');
	foreach ($posts as $key => $post) 
	{
		/**
		 * Get the category associated with the id
		 */
		$sql = "SELECT categories.name FROM categories, img2cat WHERE img2cat.image_id=" . $posts[$key]->id . " AND categories.category_id = img2cat.category_id";
		$category = Pixelpost_DB::get_var($sql);
		// Read the metadata from each file and add it to the posts array:
		$posts[$key]->category = $cats->singlePath($category); 
	}
}


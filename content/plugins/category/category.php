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

Pixelpost_Plugin::registerAction('hook_method_call', 'plugin_category_method_call',10,3);
Pixelpost_Plugin::registerAction('hook_posts', 'plugin_category_get_category',10,1);

/**
 * Category Method Hook
 * 
 * This hook is only called if the method doesn't exist in the class.
 *
 * @param object & $self the current class instance
 * @param string $controller Active controller name
 * @param string $action Method the user is trying to execute
 * @return null
 */
function plugin_category_method_call(&$self,$controller,$action)
{
	// Only run this method under the /archive/category/ page...
	if ($controller != 'archive' || $action != 'category')
		return;

	// $cats          = new Pixelpost_Hierarchy('categories');
	$last_fragment = Web2BB_Uri::numberOfFragments() - 1;
	$category      = ucfirst(Web2BB_Uri::fragment($last_fragment));
	
	if ($category == 'Category')
	{
		/**
		 * @todo Call categoryController or a plugin function...
		 */
		throw new Exception("Here comes the album view");
		return;
	}	
	
	/**
	 * @todo Call categoryController or a plugin function...
	 */
	
	// show the images from the category
	// in case it isn't a leaf we need to select the subcategories as well
	$sql = "SELECT left_node, right_node FROM categories WHERE name='" . Pixelpost_DB::escape($category) . "'";
	$node = Pixelpost_DB::get_row($sql);
	
	if (empty($node)) throw new Exception("Sorry, that category doesn't exists!");
	
	/**
	 * the config option, posts_per_page, isn't set, so display ALL the posts for this category
	 */
	
	$posts_sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
				  WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node
				  AND pixelpost.published <= '{$self->config->current_time}'
				  AND categories.category_id = img2cat.category_id AND img2cat.image_id = pixelpost.id
				  ORDER BY pixelpost.published DESC";
			
	if ($self->config->posts_per_page > 0)
	{
		/**
		 * If the config option, posts_per_page is set, we will spit up the categories into pages.
		 */
	
		// Get total number of publically available posts
		$sql = "SELECT count(pixelpost.id) FROM img2cat, categories, pixelpost 
				WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node
				AND pixelpost.published <= '{$self->config->current_time}'
				AND categories.category_id = img2cat.category_id AND img2cat.image_id = pixelpost.id";
		$self->total_posts = (int) Pixelpost_DB::get_var($sql);
	
		// Determine the total number of pages
		WEB2BB_Uri::$total_pages = (int) ceil($self->total_posts / $self->config->posts_per_page);

		// Verify that we're on a legitimate page to start with
		if (WEB2BB_Uri::$total_pages < WEB2BB_Uri::$page)
		{
			throw new Exception("Sorry, we don't have anymore pages to show!");
		}

		// The database needs to know which row we need to start with:
		$range = (int) (WEB2BB_Uri::$page - 1) * $self->config->posts_per_page;
		
		// Add the limit to the SQL query...
		$posts_sql .= " LIMIT {$range}, {$self->config->posts_per_page}";
	}

	$self->view->title = $category;

	$self->posts = (array) Pixelpost_DB::get_results($posts_sql);
}


/**
 * Example hook
 * 
 * This function adds a new element: $posts->slug
 * And it appends the slug to the end of the permalink
 */
function plugin_category_get_category(&$posts)
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


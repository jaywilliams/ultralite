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

Pixelpost_Plugin::registerAction('hook_base_construct', 'plugin_category_construct',10,3);
Pixelpost_Plugin::registerAction('hook_posts', 'plugin_category_get_category',10,1);
Pixelpost_Plugin::registerAction('hook_posts', 'plugin_category_change_permalink',10,3);
Pixelpost_Plugin::registerAction('hook_base_construct', 'plugin_category_create_post_array',10,3);

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
function plugin_category_construct(&$self,$controller,$action)
{
	
	if($controller == 'archive' && $action !='category'){
		// Get the main categories if we are on the archive page...
		$cats = new Pixelpost_Hierarchy('categories');
		$self->view->categories = $cats->getNodeDepth();
		return;
	}
	
	
	// Only run this method under the /archive/category/ page...
	if ($controller != 'archive' ||  $action != 'category')
		return;
		
	$category_permalink      = rawurlencode(Web2BB_Uri::fragment(-1));
	
	if ($category_permalink == 'category')
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
	$sql = "SELECT left_node, right_node FROM categories WHERE permalink='" . Pixelpost_DB::escape($category_permalink) . "'";
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

	$self->view->title = $category_permalink;

	$self->posts = (array) Pixelpost_DB::get_results($posts_sql);
	
	// Get the sub-categories
	$cats = new Pixelpost_Hierarchy('categories');
	$self->view->categories = $cats->getLocalSubNodes($category_permalink);
	
	
}


/**
 * Change the permalink to stay into the category
 * 
 * This function gets the associated category for the images
 * in the $posts array.
 */
function plugin_category_change_permalink(&$posts,$controller,$action)
{
	
	/**
	 * If the action == 'category' (archive page browsing)
	 * or the last fragment contains the term == 'category-'
	 * we are browsing by category, so we need to modify the 
	 * permalinks accordingly.
	 */
	if ( $action != 'category'  && strpos(Web2BB_Uri::fragment(-1), 'category-') === false ) 
		return;
	
	$category_permalink = str_replace('category-', '', Web2BB_Uri::fragment(-1));
	// no need to encode the url as it is already encoded
	foreach ($posts as $key => $post) 
	{
		$posts[$key]->permalink .= '/in/category-'.$category_permalink;
	}
}


/**
 * Get the categories
 * 
 * This function gets the associated category for the images
 * in the $posts array.
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


/**
 * Create the $posts array including the prev and next links
 * to stay in the category
 */
function plugin_category_create_post_array(&$self,$controller,$action)
{
	// Only run this method under the 'post' controller with the uri: /in/category-{name}
	if ($controller != 'post' || strpos(Web2BB_Uri::fragment(-1), 'category-') === false)
		return;
		
	$category_permalink = str_replace('category-', '', Web2BB_Uri::fragment(-1));

	
	// show the images from the category
	// in case it isn't a leaf we need to select the subcategories as well
	$sql = "SELECT left_node, right_node FROM categories WHERE permalink='" . Pixelpost_DB::escape($category_permalink) . "'";
	$node = Pixelpost_DB::get_row($sql);
	
	if (empty($node)) throw new Exception("Sorry, that category doesn't exists!");
	 
	/**
	 * Determine the image ID we need to lookup, and verify that it is a positive integer:
	 */
	$self->id = (int) Web2BB_Uri::fragment(1);
	$self->id = ($self->id > 0) ? $self->id : 0;
		
	/**
	 * Current Image
	 */
	$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$self->id' AND `published` <= '{$self->config->current_time}' LIMIT 0,1";

	// Assign the current image to the $posts array
	$self->posts['current'] = Pixelpost_DB::get_row($sql);
		
		
	/**
	 * Verify that the image exists:
	 */
	if (!is_object($self->posts['current']))
	{
		// Error? Splash Screen?
		throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
	}

	/**
	 * Next Image
	 */
	$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
  	WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node 
  	AND categories.category_id = img2cat.category_id 
  	AND img2cat.image_id = pixelpost.id 
  	AND (pixelpost.published < '{$self->posts['current']->published}') 
  		AND (pixelpost.published <= '{$self->config->current_time}') 
  	ORDER BY pixelpost.published DESC LIMIT 0,1";

	$self->posts['next'] = Pixelpost_DB::get_row($sql);
		
	/**
	 * If we are on the last image, there isn't a next image, 
	 * so we can wrap around to the first image:
	 */
	if (!is_object($self->posts['next']))
	{
		$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
 		 	WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node 
  		AND categories.category_id = img2cat.category_id 
  		AND img2cat.image_id = pixelpost.id 
  		AND pixelpost.published <= '{$self->config->current_time}' 
  		ORDER BY pixelpost.published DESC LIMIT 0,1";
			
		$self->posts['next'] = Pixelpost_DB::get_row($sql);
	}

	/**
	 * Previous Image
	 */
	$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
  	WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node 
  	AND categories.category_id = img2cat.category_id 
  	AND img2cat.image_id = pixelpost.id 
  	AND (pixelpost.published > '{$self->posts['current']->published}') 
  		AND (pixelpost.published <= '{$self->config->current_time}') 
  	ORDER BY pixelpost.published ASC LIMIT 0,1";

	$self->posts['previous'] = Pixelpost_DB::get_row($sql);
		
	/**
	 * If the first image, we can't go back any further, 
	 * so we can wrap around to the last image:
	 */
	if (!is_object($self->posts['previous']))
	{
		$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
  		WHERE categories.left_node BETWEEN $node->left_node AND $node->right_node 
  		AND categories.category_id = img2cat.category_id 
  		AND img2cat.image_id = pixelpost.id 
  		AND pixelpost.published <= '{$self->config->current_time}' 
  		ORDER BY pixelpost.published ASC LIMIT 0,1";
	
		$self->posts['previous'] = Pixelpost_DB::get_row($sql);
	}
	
	
}
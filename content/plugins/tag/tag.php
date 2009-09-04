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

Pixelpost_Plugin::registerAction('hook_base_construct', 'plugin_tag_construct',10,3);
Pixelpost_Plugin::registerAction('hook_posts', 'plugin_tag_get_tags',10,1);
Pixelpost_Plugin::registerAction('hook_posts', 'plugin_tag_change_permalink',10,3);
Pixelpost_Plugin::registerAction('hook_base_construct', 'plugin_tag_create_post_array',10,3);

/**
 * Tag Method Hook
 * 
 * This hook is only called if the method doesn't exist in the class.
 *
 * @param object & $self the current class instance
 * @param string $controller Active controller name
 * @param string $action Method the user is trying to execute
 * @return null
 */
function plugin_tag_construct(&$self,$controller,$action)
{
	if($controller == 'archive' && $action !='tag')
	{
		// Get the main tags if we are on the archive page...
		$sql = "SELECT tags.name FROM tags";
		$tags = Pixelpost_DB::get_results($sql);
		$self->view->tags = $tags;
		return;
	}
	
	
	// Only run this method under the /archive/tag/ page...
	if ($controller != 'archive' ||  $action != 'tag')
		return;
		
	$tag      = rawurldecode(Web2BB_Uri::fragment(-1));
		
	/**
	 * @todo Call tagController or a plugin function...
	 */
	
	/**
	 * show the images from the tag
	 * the config option, posts_per_page, isn't set, so display ALL the posts for this/these tag(s)
	 */
	
	$posts_sql = "SELECT pixelpost.* FROM img2tag, tags, pixelpost 
				  WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "' 
				  AND pixelpost.published <= '{$self->config->current_time}'
				  AND tags.tag_id = img2tag.tag_id AND img2tag.image_id = pixelpost.id
				  ORDER BY pixelpost.published DESC";
			
	if ($self->config->posts_per_page > 0)
	{
		/**
		 * If the config option, posts_per_page is set, we will spit up the categories into pages.
		 */
	
		// Get total number of publically available posts
		$sql = "SELECT count(pixelpost.id) FROM img2tag, tags, pixelpost 
				WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "'
				AND pixelpost.published <= '{$self->config->current_time}'
				AND tags.tag_id = img2tag.tag_id AND img2tag.image_id = pixelpost.id";
				
		
				
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

	$self->view->title = $tag;

	$self->posts = (array) Pixelpost_DB::get_results($posts_sql);

	/**
	 * We should grab all the other tags associated with images with a certain tag
	 */
	 
	$sql = "SELECT DISTINCT t2.name FROM tags t1
		JOIN img2tag it1 ON it1.tag_id = t1.tag_id
		JOIN img2tag it2 ON it1.image_id = it2.image_id
		JOIN tags t2 ON it2.tag_id = t2.tag_id
		WHERE t1.name = '" . Pixelpost_DB::escape($tag) . "' 
		AND t2.name <> '" . Pixelpost_DB::escape($tag) . "'";
	$tags = Pixelpost_DB::get_results($sql);
	$self->view->tags = $tags;
}


/**
 * Change the permalink to stay into the tag
 * 
 * This function gets the associated tag for the images
 * in the $posts array.
 */
function plugin_tag_change_permalink(&$posts,$controller,$action)
{
	
	/**
	 * If the action == 'tag' (archive page browsing)
	 * or the last fragment contains the term == 'tag-'
	 * we are browsing by tag, so we need to modify the 
	 * permalinks accordingly.
	 */
	if ( $action != 'tag'  && strpos(Web2BB_Uri::fragment(-1), 'tag-') === false ) 
		return;
	
	$tag      = str_replace('tag-', '', rawurlencode(Web2BB_Uri::fragment(-1)));
	
	foreach ($posts as $key => $post) 
	{
		$posts[$key]->permalink .= '/in/tag-'.$tag;
	}
}


/**
 * Get the tag
 * 
 * This function gets the associated tag(s) for the images
 * in the $posts array.
 */
function plugin_tag_get_tag(&$posts)
{
	foreach ($posts as $key => $post) 
	{
		/**
		 * Get the tag(s) associated with the id
		 */
		$sql = "SELECT tags.name FROM tags, img2tag WHERE img2tag.image_id=" . $posts[$key]->id . " AND tags.tag_id = img2tag.tag_id";
		
		$tags = Pixelpost_DB::get_result($sql);
		$posts[$key]->tag = $tags; 
	}
}


/**
 * Create the $posts array including the prev and next links
 * to stay in the tag
 */
function plugin_tag_create_post_array(&$self,$controller,$action)
{
	// Only run this method under the 'post' controller with the uri: /in/tag-{name}
	if ($controller != 'post' || strpos(Web2BB_Uri::fragment(-1), 'tag-') === false)
		return;
		
	$tag = str_replace('tag-', '', rawurldecode(Web2BB_Uri::fragment(-1)));
	
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
	$sql = "SELECT pixelpost.* FROM img2tag, tags, pixelpost 
  	WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "'
 	AND tags.tag_id = img2tag.tag_id 
  	AND img2tag.image_id = pixelpost.id 
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
		$sql = "SELECT pixelpost.* FROM img2tag, tags, pixelpost 
 		 	WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "'
			AND tags.tag_id = img2tag.tag_id 
  			AND img2tag.image_id = pixelpost.id 
  			AND pixelpost.published <= '{$self->config->current_time}' 
  			ORDER BY pixelpost.published DESC LIMIT 0,1";
			
		$self->posts['next'] = Pixelpost_DB::get_row($sql);
	}

	/**
	 * Previous Image
	 */
	$sql = "SELECT pixelpost.* FROM img2tag, tags, pixelpost 
  		WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "' 
 		AND tags.tag_id = img2tag.tag_id 
  		AND img2tag.image_id = pixelpost.id 
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
		$sql = "SELECT pixelpost.* FROM img2tag, tags, pixelpost 
  			WHERE tags.name = '" . Pixelpost_DB::escape($tag) . "'  
  			AND tags.tag_id = img2tag.tag_id 
  			AND img2tag.image_id = pixelpost.id 
  			AND pixelpost.published <= '{$self->config->current_time}' 
  			ORDER BY pixelpost.published ASC LIMIT 0,1";
	
		$self->posts['previous'] = Pixelpost_DB::get_row($sql);
	}
}
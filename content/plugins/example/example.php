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
	Description: This plugin simply shows how easily it is to tie into filters & hooks.
	Version: 1.0
	Author: Team Pixelpost
	Author URI: http://pixelpost.org/
*/

Pixelpost_Plugin::registerAction('hook_page_head', 'example_echo_head');
Pixelpost_Plugin::registerAction('hook_page_body', 'example_echo_body');
Pixelpost_Plugin::registerFilter('filter_title', 'example_filter_test');
Pixelpost_Plugin::registerFilter('filter_description', 'example_filter_test',5);
Pixelpost_Plugin::registerFilter('filter_site_description', 'example_filter_test');
Pixelpost_Plugin::registerFilter('filter_published', 'example_filter_published');
Pixelpost_Plugin::registerAction('hook_posts', 'example_posts_test',10,1);

/**
 * Example hook
 * 
 * This function adds a new element: $posts->slug
 * And it appends the slug to the end of the permalink
 */
function example_posts_test(&$posts)
{
	foreach ($posts as $key => $post) {
		
		// Generate a "clean url" slug:
		$posts[$key]->slug = str_replace(' ','-',preg_replace('/[^a-z0-9-_ ]/','',strtolower($post->title)));
		
		// Append the slug to the permalink:
		$posts[$key]->permalink   .= "/{$posts[$key]->slug}";
	}
}

/**
 * Example Filter
 * 
 * This function modifies the published date format
 */
function example_filter_published(&$published)
{
	$published = date("l jS \of F Y",strtotime($published));
}

/**
 * This function adds some text to the description
 */
function example_filter_test(&$value)
{
	$value = $value. ' -- Example';
}

function example_echo_head()
{	
	echo "\n<!-- Header Code! -->\n";
}


function example_echo_body()
{	
	echo "\n<!-- Body Code! -->\n";
}



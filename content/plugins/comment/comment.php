<?php
/**
 * Comment Plugin
 * 
 *
 * @author Jay Williams
 * @version 1.0
 **/



// Pixelpost_Plugin::registerAction('hook_page_head', 'comment_echo_head');
// Pixelpost_Plugin::registerAction('hook_page_body', 'comment_echo_body');
// Pixelpost_Plugin::registerFilter('filter_description', 'comment_filter_test');
// Pixelpost_Plugin::registerFilter('filter_site_description', 'comment_filter_test');
// Pixelpost_Plugin::registerFilter('filter_published', 'comment_filter_published');
// Pixelpost_Plugin::registerAction('hook_posts', 'comment_posts_test',10,1);

/**
 * Example hook
 * 
 * This function adds a new element: $posts->slug
 * And it appends the slug to the end of the permalink
 */
function comment_posts_test(&$posts)
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
function comment_filter_published(&$published)
{
	$published = date("l jS \of F Y",strtotime($published));
}

/**
 * This function adds some text to the description
 */
function comment_filter_test(&$value)
{
	$value = $value. ' + Example';
}

function comment_echo_head()
{	
	echo "\n<!-- Header Code! -->\n";
}


function comment_echo_body()
{	
	echo "\n<!-- Body Code! -->\n";
}



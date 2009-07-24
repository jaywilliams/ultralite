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



/**
 * Change the site title, and the title for the archive and post pages.
 */
$this->add_filter('config_title', 'my_plugin_filer',10);
$this->add_filter('post_title', 'my_plugin_filer',10);
$this->add_filter('archive_title', 'my_plugin_filer',10);

function my_plugin_filer(&$input)
{
	

	// Simple Adjustment:
	$input = $input . ' Mod';
	
	// No need to return anyting, since the first 
	// varialbe is passed via reference:
	// return $string;
}




/**
 * This function takes the raw date/time and changes the format, 
 * so it looks better for the template.
 */
$this->add_filter('post_published', 'date_formatter');

function date_formatter(&$date)
{
	$unixtime = strtotime($date);
	$date = date("M n Y",$unixtime);
}




$this->add_action('controller_post', 'adjust_title');
$this->add_action('theme_head', 'echo_head');
$this->add_action('theme_body', 'echo_body');

function adjust_title()
{
	// global $site;
	
	// $site->title = "$site->title - Modified";
}

function echo_head()
{	
	echo "\n<!-- Header Code! -->\n";
}


function echo_body()
{	
	echo "\n<h2 style=\"text-align:center\">Theme body!</h2>\n";
}



?>
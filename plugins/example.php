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
// 
// $plugins = new plugins;
// 
$this->add_filter('title', 'my_plugin_filer');
$this->add_filter('tagline', 'my_plugin_filer');

function my_plugin_filer($string)
{
	// Simple:
	// $string = "$string (= Plugin Fun)";
	
	// Multi-Lingual:
	$string = $string . ' ' . '+ Plugin Fun';
	
	return $string;
}

$this->add_action('home', 'my_plugin_action');

function my_plugin_action($string)
{
	echo "\n<h2>I love plugins, and so should you!</h2>\n";
}



?>
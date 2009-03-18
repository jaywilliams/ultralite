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
$this->add_filter('title', 'my_plugin_filer',10);
$this->add_filter('tagline', 'my_plugin_filer',10);

function my_plugin_filer($input)
{

	// Simple Adjustment:
	$input = "$input + Plugin Fun";
	
	// No need to return anyting, since the first 
	// varialbe is passed via reference:
	// return $string;
}

$this->add_action('home', 'my_plugin_action',10,2);

function my_plugin_action($test,$false)
{
	$test = 'ha ha';
	echo "\n<h2>I love plugins, and so should you!</h2>\n";
}



?>
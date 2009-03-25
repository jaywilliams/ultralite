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
$this->add_filter('site-title', 'my_plugin_filer',10);
$this->add_filter('site-tagline', 'my_plugin_filer',10);

function my_plugin_filer(&$input)
{
	

	// Simple Adjustment:
	$input = "$input + Plugin Fun";
	
	// No need to return anyting, since the first 
	// varialbe is passed via reference:
	// return $string;
}

$this->add_action('body', 'my_plugin_action',10,1);

function my_plugin_action(&$mode)
{
	global $config;
	// var_dump($config);
	
	$config->site->title = 'New Title';
	
	$mode = "my $mode";
	
	echo "\n<h2 style=\"text-align:center\">We are currently in $mode!</h2>\n";
}



?>
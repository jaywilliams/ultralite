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
Pixelpost_Plugin::registerFilter('filter_escape', 'example_filter_test');

function example_filter_test(&$value)
{
	$value = $value. '+Example';
}

function example_echo_head()
{	
	echo "\n<!-- Header Code! -->\n";
}


function example_echo_body()
{	
	echo "\n<h2 style=\"text-align:center\">Theme body! (I got added by a plugin))</h2>\n";
}



?>
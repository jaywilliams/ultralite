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

//$plugins = Pixelpost_Plugin::getInstance(); // this should really be in a base plugin!!!
//$plugins->registerAction('theme_head', 'echo_head');
//$plugins->registerAction('theme_body', 'echo_body');

Pixelpost_Plugin::registerAction('theme_head', 'echo_head');
Pixelpost_Plugin::registerAction('theme_body', 'echo_body');


function echo_head()
{	
	echo "\n<!-- Header Code! -->\n";
}


function echo_body()
{	
	echo "\n<h2 style=\"text-align:center\">Theme body! (I got added by a plugin))</h2>\n";
}



?>
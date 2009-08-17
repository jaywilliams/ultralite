<?php
/**
 * Markdown Plugin
 **/

include_once(__PLUGIN_PATH.'/markdown/libraries/markdown.php');

Pixelpost_Plugin::registerFilter('filter_description', 'plugin_markdown_filter',6);

function plugin_markdown_filter(&$string)
{
	$string = Markdown($string);
}
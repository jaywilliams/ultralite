<?php
/**
 * SmartyPants Plugin
 **/

include_once(__PLUGIN_PATH.'/smartypants/libraries/smartypants.php');

Pixelpost_Plugin::registerFilter('filter_title', 'plugin_smartypants_filter');
Pixelpost_Plugin::registerFilter('filter_description', 'plugin_smartypants_filter');

function plugin_smartypants_filter(&$string)
{
	$string = html_entity_decode(SmartyPants($string),ENT_QUOTES,'UTF-8');
}
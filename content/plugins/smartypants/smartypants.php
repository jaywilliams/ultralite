<?php
/**
 * SmartyPants Plugin
 **/

include_once(__PLUGIN_PATH.'/smartypants/libraries/smartypants.php');

Pixelpost_Plugin::registerFilter('filter_site_name', 'plugin_smartypants_encode');
Pixelpost_Plugin::registerFilter('filter_site_description', 'plugin_smartypants_encode');

Pixelpost_Plugin::registerFilter('filter_title', 'plugin_smartypants_encode');
Pixelpost_Plugin::registerFilter('filter_description', 'plugin_smartypants_encode');

// Disable SmartyPants smart entities, on the feed:
// Pixelpost_Plugin::registerFilter('filter_title_feed', 'plugin_smartypants_decode');
// Pixelpost_Plugin::registerFilter('filter_description_feed', 'plugin_smartypants_decode');

function plugin_smartypants_encode(&$string)
{
	// if(Web2BB_Uri::fragment(0) != 'feed') // Disable for Feeds
	$string = plugin_smartypants_decode_entities(SmartyPants($string));
}

function plugin_smartypants_decode(&$string)
{
	$string = plugin_smartypants_stupefy($string);
}

/**
 * Decodes the SmartyPants entities into their actual UTF-8 equivalents.
 * 
 *     Example input:  &#8220;Hello &#8212; world.&#8221;
 *     Example output: “Hello – world.”
 *
 * @param string $_ 
 * @return string
 */
function plugin_smartypants_decode_entities($_) {

						#  en-dash    em-dash
	$_ = str_replace(array('&#8211;', '&#8212;'),
					 array('–',       '—'), $_);

	# single quote         open       close
	$_ = str_replace(array('&#8216;', '&#8217;'), 
					 array('‘' ,      '’' ), $_);

	# double quote         open       close
	$_ = str_replace(array('&#8220;', '&#8221;'), 
					 array('“' ,      '”' ), $_);

	$_ = str_replace('&#8230;', '…', $_); # ellipsis

	return $_;
}

/**
 * 	The string, with each SmartyPants HTML entity translated 
 * to its ASCII counterpart.
 * 
 *     Example input:  “Hello – world.”
 *     Example output: "Hello -- world."
 *
 * @param string $_ 
 * @return string
 */
function plugin_smartypants_stupefy($_) {

						#  en-dash    em-dash
	$_ = str_replace(array('–',       '—'),
					 array('-',       '--'), $_);

	# single quote         open       close
	$_ = str_replace(array('‘',       '’'), "'", $_);

	# double quote         open       close
	$_ = str_replace(array('“',       '”'), '"', $_);

	$_ = str_replace('…', '...', $_); # ellipsis


	return $_;
}
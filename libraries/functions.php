<?php

/**
 * Turn register globals off.
 *
 * @return null Will return null if register_globals PHP directive was disabled
 */
function unregister_globals() {
	if ( !ini_get('register_globals') )
		return;

	if ( isset($_REQUEST['GLOBALS']) )
		die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}


/**
 * Renders the URL in the proper format.
 * 
 * Examples:
 * url("view=rss");
 * url("view=post&id=3&extra=my-title");
 * url("view=post&id=3&extra=my-title&custom=true");
 *
 * @param string $url Query (do not include http:// or /)
 * @param bool $echo Disabled by default, this optional option will echo the result rather than return it silently.
 * @return string $output Properly formated URL
 */
function url($url='',$echo=false)
{
	global $config,$view;
	
	/**
	 * Prepare the variables we'll be using in this function
	 */
	$output    = "";
	$url_param = array();
	$url       = ltrim($url,"/");
		         parse_str($url,$url);

	if (array_key_exists('view',$url))
		$url_param['view'] = $url['view'];
	elseif(array_key_exists('id',$url))
		$url_param['view'] = $url['view'] = $view;
	
	if (array_key_exists('id',$url) && array_key_exists('view',$url))
		$url_param['id'] = $url['id'];
	
	if (array_key_exists('extra',$url) && array_key_exists('id',$url) && array_key_exists('view',$url))
		$url_param['extra'] = $url['extra'];
		
	if (array_key_exists('page',$url) && ($url['page'] > 1 && $url['page'] <= $config->site->total_pages))
		$url_param['page'] = $url['page'];
	
	// We can remove them from the $url array,
	// as they now exist in the $url_param array.
	unset($url['view'],$url['id'],$url['extra'],$url['page']);
	
	if (count($url) > 0)
		$url_param['unknown'] = http_build_query($url);
	
	/**
	 * Check to see if we are in mod_rewrite mode,
	 * If so, convert the url to the clean URL format.
	 * 
	 * Otherwise, remove $url_param['unknown'] and merge
	 * the remaining unknown parameters to create the url.
	 */
	if ($config->site->mod_rewrite)
	{
		foreach ($url_param as $key => $value) {
			switch ($key) {
				case 'view':
					$output .= "$value";
					break;
				case 'id':
				case 'extra':
					$output .= "/$value";
					break;
				case 'page':
					$output .= "/page/$value";
					break;
				case 'unknown':
					$output .= "?$value";
					break;
			}
		}
	}
	else
	{
		unset($url_param['unknown']);
		$url_param = array_merge($url_param,$url);
		
		$output    = '?'.http_build_query($url_param);

	}
	
	/**
	 * Output the code
	 * Or, if specified 
	 */
	if ($echo)
		echo $output;
	else
		return $output;
}

/**
 * Template Tag
 * 
 * Includes the specified template tag function.
 * 
 * For example, if you included this in a template:
 * 		<?php tt('thumbnails'); ?>
 * 
 * It would cause the script to run the function:
 * 		tt_thumbnails(); 
 * 
 * If that function didn't exist, it would return false.
 *
 * @param string $template_tag The name of the template tag function, minus the "tt_" prefix. 
 * @param mixed $options Optional settings used to control the template tag
 * @return mixed false if function doesn't exist
 */
function tt($template_tag='',$options='')
{
	if (substr($template_tag,0,3) != 'tt_') {
		$template_tag = "tt_$template_tag";
	}
	
	if (function_exists("$template_tag")) {
		return $template_tag($options);
	}
	
	return false;
}


/**
 * Escape Print
 * 
 * Echos and escapes a variable. Useful for templates, 
 * and any place where you want to apply htmlentities() filtering.
 *
 * @param string $value the string to escape and echo
 */
function eprint($value='')
{
	echo escape($value);
}

/**
 * Escape
 * 
 * Escape the specified string with htmlentities().
 *
 * @param string $value the string to escape
 * 
 * @todo Possibly allow plugins to add/remove the the escape methods
 */
function escape($value='')
{
	return htmlentities($value,ENT_QUOTES);
}


/*
// Don't close the file, since it needs to be included.
?>
*/
<?php

/**
 * The directory in which the core Pixelpost resources are located.
 * The application directory must contain the config.php file.
 */
$application = 'application';

/**
 * The directory in which your caches are located.
 */
$caches = 'content/caches';

/**
 * The directory in which your images are located.
 */
$images = 'content/images';

/**
 * The directory in which your plugins are located.
 */
$plugins = 'content/plugins';

/**
 * The directory in which your themes are located.
 */
$themes = 'content/themes';

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 */
error_reporting(E_ALL | E_STRICT);


/**
 * Default Content Type
 */
header('Content-Type: text/html; charset=utf-8');

/**
 * Default Time Zone
 * If you don't set this, you'll get more notice errors than you'd ever care to see.
 */
date_default_timezone_set('America/Chicago');


/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Pixelpost internals.
 *
 * @see  http://docs.pixelpost.org/
 */

// Set the full path to the docroot
define('DOCROOT', path(dirname(__FILE__)));

// Make the application relative to the docroot
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot
if ( ! is_dir($caches) AND is_dir(DOCROOT.$caches))
	$caches = DOCROOT.$caches;

// Make the modules relative to the docroot
if ( ! is_dir($images) AND is_dir(DOCROOT.$images))
	$images = DOCROOT.$images;

// Make the modules relative to the docroot
if ( ! is_dir($plugins) AND is_dir(DOCROOT.$plugins))
	$plugins = DOCROOT.$plugins;

// Make the modules relative to the docroot
if ( ! is_dir($themes) AND is_dir(DOCROOT.$themes))
	$themes = DOCROOT.$themes;

// Define the absolute paths for configured directories
define('APPPATH', path($application));
define('CSHPATH', path($caches));
define('IMGPATH', path($images));
define('PLGPATH', path($plugins));
define('THMPATH', path($themes));

define('PUBPATH',  str_replace(path($_SERVER['DOCUMENT_ROOT']), '', DOCROOT) );

// Clean up the configuration vars
unset($application,$caches,$images,$plugins,$themes);

// Path Helper Function
function path($path='')
{
	return str_replace('\\', '/', realpath($path)) . '/';
}

if (file_exists('install.php'))
{
	// Load the installation check
	return include 'install.php';
}

// Define the start time of the application
define('PIXELPOST_START_TIME', microtime(TRUE));

// Define the memory usage at the start of the application
define('PIXELPOST_START_MEMORY', memory_get_usage());

// Load the base, low-level functions
require_once APPPATH.'base.php';

// Run Translation Test:
require_once APPPATH.'test-translation.php';

// Load the core Pixelpost class
//require APPPATH.'classes/pixelpost/core.php';

// Bootstrap the application
// require APPPATH.'bootstrap.php';

/**
 * To prevent possible issues, do not add a closing "?>" tag.
 */
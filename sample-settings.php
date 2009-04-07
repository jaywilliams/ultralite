<?php

/**
 * Welcome to the Ultralite configuration file.
 * Here you can customize your photoblog with ease!
 * 
 * Just scroll down to see what you can change, 
 * and save the changes once you're done.
 * 
 * @package ultralite
 **/


/**
 * Global Site Configuration
 */
$config->title      = "My Ultralite Photoblog";
$config->tagline    = "Guess what, it's open source, and it's ultralite!";
$config->url        = "http://example.com/ultralite/"; // Dont forget the last backslash!

$config->template   = "greyspace";

// The language used on your photoblog:
$config->locale     = "en-us";

// Time Zone:
// For a list of supported timezones please see: http://www.php.net/manual/en/timezones.php
$config->timezone   = "UCT";

// How may image thumbnails per page:
$config->pagination = 24; // Enter 0 to disable


// Ultralite will auto-detect whether or not clean URLs should be used.
// But if you want to overide the auto-detect, you can do so here:
// $config->mod_rewrite     = true;

/**
 * Plugin Configuration
 * 
 * Specify the plugins you wish to load here.
 * This isn't a perfect solution, but it's the best
 * we have for now.
 * 
 * Filename: plugin_myname.php
 * Plugin Name: myname
 */

// $config->plugins[] = 'myname';
$config->plugins[] = 'example';
$config->plugins[] = 'media_rss';


/**
 * Database Configuration
 * 
 * What type of database are you using?
 * Un-comment the appropriate section for your server.
 */

// Choose either "sqlite" or "mysql"
$config->database_type = "mysql";


/**
 * If you're using a SQLite database, you will need to fill out 
 * the correct information below.
 *
 * NOTE: It is VERY important that this file be hidden from the public.
 * If possible put it in a location outside of public_html or block 
 * access to the file via .htaccess (as shown in sample.htaccess)
 */


// Path to the sqlite database:
$config->sqlite_database = "./pixelpost.sqlite3";


/**
 * If you're using a mySQL database, you will need to fill out 
 * the correct information below.
 */
$config->mysql_hostname = "localhost";
$config->mysql_database = "ultralite";
$config->mysql_username = "root";
$config->mysql_password = "";


/**
 * That's it, you're done, congrats!
 */
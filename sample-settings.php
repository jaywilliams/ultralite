<?php

/**
 * Welcome to the Ultralite configuration file.
 * Here you can customize your photoblog with ease!
 * 
 * Just scroll down to see what you can change, 
 * and save the changes once you're done.
 * 
 **/


/**
 * Global Site Configuration
 */
$config->site->title      = "My Ultralite Photoblog";
$config->site->tagline    = "Guess what, it's open source, and it's ultralite!";
$config->site->url	      = "http://example.com/ultralite/"; // Dont forget the last backslash!

$config->site->template   = "greyspace";

// The language used on your photoblog:
$config->language->locale = "en-us";

// Time Zome Offset (from GMT/UTC):
$config->time->offset     = "-5";

// How may image thumbnails per page:
$config->site->pagination = 25; // Enter 0 to disable

// Ultralite will auto-detect whether or not clean URLs should be used.
// But if you want to overide the auto-detect, you can do so here:
// $config->site->mod_rewrite     = true;


/**
 * Database Configuration
 * 
 * What type of database are you using?
 * Un-comment the appropriate section for your server.
 */

// Choose either "sqlite" or "mysql"
$config->database->type = "sqlite";


/**
 * If you're using a SQLite database, you will need to fill out 
 * the correct information below.
 * 
 * NOTE: It is VERY important that this file be hidden from the public.
 * If possible put it in a location outside of public_html or block 
 * access to the file via .htaccess (as shown in sample.htaccess)
 */

// Path to the sqlite database:
$config->database->sqlite->database = "./pixelpost.sqlite3";


/**
 * If you're using a mySQL database, you will need to fill out 
 * the correct information below.
 */
$config->database->mysql->hostname = "localhost";
$config->database->mysql->database = "ultralite";
$config->database->mysql->username = "root";
$config->database->mysql->password = "";


/**
 * That's it, you're done, congrats!
 */
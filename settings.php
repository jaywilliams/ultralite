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
$config->site->title      = "My Ultralite Photoblog";
$config->site->tagline    = "Guess what, it's open source, and it's ultralite!";
$config->site->url	      = "http://example.com/ultralite/"; // Dont forget the last backslash!

$config->site->template   = "greyspace";

// Not implemented yet:
$config->language->locale = "en-us";

// Time Zome Offset (from GMT/UTC):
$config->time->offset     = "-5";

// The script will auto-choose whether or not clean URLs should be used.
// But if you want to overide this option, you can do so here:
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
 */

// Path to the sqlite database:
$config->database->sqlite->database = "./pixelpost.sqlite3.php";


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
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
$config->site->slogan     = "Guess what, it's open source, and it's ultralite!";

$config->site->template   = "ultralite";

// Not implemented yet:
$config->language->locale = "en-us";

// Time Zome Offset (from GMT/UTC):
$config->time->offset     = (int) "-5";


/**
 * Database Configuration
 * 
 * What type of database are you using?
 * Un-comment the appropriate section for your server.
 */

// Choose either "sqlite" or "mysql"
$config->database->type = "sqlite";


/**
 * If you're using a SQLite database, uncomment the configuration below,
 * and fill out the correct information.
 */

// Path to the sqlite database:
$config->database->sqlite->database = "./pixelpost.sqlite3.php";


/**
 * If you're using a mySQL database, uncomment the configuration below,
 * and fill out the correct information.
 */
// $config->database->mysql->hostname = "localhost";
// $config->database->mysql->database = "ultralite";
// $config->database->mysql->username = "root";
// $config->database->mysql->password = "";


/**
 * That's it, you're done, congrats!
 */
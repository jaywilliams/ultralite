<?php

/**
 * Welcome to the Ultralite configuration file.
 * Here you can customize your photoblog with ease!
 * 
 * Just scroll down to see what you can change.
 * 
 * @package ultralite
 **/

$config['site']['title']       = "My Ultralite Photoblog";
$config['site']['slogan']      = "Guess what, it's open source, and it's ultralite!";

$config['site']['template']    = "ultralite";

// Not implemented yet:
$config['site']['language']    = "en-us";


// User Settings
$config['user']['timezone']	   = '-5';


/**
 * Database Type
 *
 * What type of database are you using? 
 * sqlite [OR] mysql
 */



/**
 * If you're using a SQLite database, uncomment the configuration below,
 * and fill out the correct information.
 */
$config['database']['sqlite']['database'] = './pixelpost.sqlite3.php';


/**
 * If you're using a mySQL database, uncomment the configuration below,
 * and fill out the correct information.
 */
# $config['database']['mysql']['hostname'] = 'localhost';
# $config['database']['mysql']['username'] = 'root';
# $config['database']['mysql']['password'] = '';




// No ending forces that it be included.
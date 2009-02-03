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

$config['site']['template']    = "lite";

// Not implemented yet:
$config['site']['language']    = "en-us";


/**
 * Database Type
 *
 * What type of database are you using? 
 * sqlite [OR] mysql
 */
$config['database']['type']     = 'sqlite';
$config['database']['hostname'] = './pixelpost.sqlite3"';
$config['database']['username'] = '';
$config['database']['password'] = '';




// No ending forces that it be included.
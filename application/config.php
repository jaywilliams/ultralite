<?php defined('APPPATH') or die('No direct script access.');

/**
 * Welcome to the Ultralite configuration file.
 * Here you can customize your photoblog with ease!
 * 
 * Just scroll down to see what you can change, 
 * and save the changes once you're done.
 * 
 * One thing to keep in mind, this file will be 
 * overwritten by Ultralite if you change your 
 * settings via the web admin.
 **/

return array (
  'database' => 
  array (
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'ultralite',
    'prefix' => '',
    'sqlite' => './application/pixelpost.sqlite3',
    'adapter' => 'sqlite',
  ),
  'site_name' => 'My Ultralite Photoblog',
  'site_description' => 'Guess what, it\'s open source, and it\'s ultralite!',
  'copyright' => '(c) 2009 Pixelpost',
  'url' => 'http://localhost/ultralite/',
  'email' => 'user@domain.com',
  'locale' => 'EN',
  'theme' => 'greyspace_neue',
  'posts_per_page' => 5,
  'feed_items' => 5,
  'feed_pagination' => true,
  'clean_urls' => true,
  'post_url' => '(year)/(month)/(day)/(url)/',
  'timezone' => 'America/Chicago',
  'can_register' => true,
  'cache_lifetime' => 3600,
  'enabled_plugins' => 
  array (
  ),
  'routes' => 
  array (
    'tag/(name)/' => 'tag',
  ),
  'secure_hashkey' => '090cf52270f04c28b7bb0fab1e93d425',
  'default_comment_status' => 'denied',
  'allowed_comment_html' => 
  array (
    0 => 'strong',
    1 => 'em',
    2 => 'blockquote',
    3 => 'code',
    4 => 'pre',
    5 => 'a',
  ),
  'logging' => 
  array (
    'log_handler' => 'file',
    'log_file' => 'application/pixelpost.log',
    'log_level' => 999,
  ),
  'comments_per_page' => 25,
  'defensio_api_key' => '090cf52270f04c28b7bb0fab1e93d425',
  'my-bool' => false,
  'default_controller' => 'Post',
  'default_action' => 'indexAction',
  'error_controller' => 'Error',
  'static_controller' => 'Static',
  'test' => true,
)

?>
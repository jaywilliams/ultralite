<?php if(!defined('ULTRALITE')) { @header("Status: 403"); exit("Access denied."); } // Prevent direct file access. 

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
    'username' => NULL,
    'password' => NULL,
    'database' => './mydb.sqlite3',
    'prefix' => NULL,
    'adapter' => 'sqlite',
  ),
  'name' => 'My Ultralite Photoblog2',
  'description' => 'ƒ2.8 isn\'t fast enough™',
  'url' => 'http://192.168.1.200/ultralite',
  'email' => 'user@domain.com',
  'locale' => 'en_US',
  'theme' => 'greyspace1322354182',
  'posts_per_page' => 5,
  'feed_items' => 20,
  'clean_urls' => true,
  'post_url' => '(year)/(month)/(day)/(url)/',
  'timezone' => 'America/Chicago',
  'can_register' => true,
  'uploads_path' => '/images/',
  'enabled_plugins' => 
  array (
    0 => 'markdown',
    2 => 'smartypants',
    3 => 'tags',
    5 => 'swfupload',
    6 => 'comments',
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
  'comments_per_page' => 25,
  'defensio_api_key' => '090cf52270f04c28b7bb0fab1e93d425',
  'my-bool' => false,
  'mytest' => 'Ovver',
)

?>
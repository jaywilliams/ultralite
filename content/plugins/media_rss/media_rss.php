<?php
/**
 * Media RSS
 * 
 * Adds Media RSS support to the Pixelpost RSS Feed
 * 
 *
 * @author Jay Williams
 * @version 1.0
 **/


Pixelpost_Plugin::registerAction('hook_rss_feed', 'plugin_media_rss_namespace',10,1);
Pixelpost_Plugin::registerAction('hook_rss_item', 'plugin_media_rss_item',10,2);

/**
 * Add Media RSS specific Namespace
 *
 * @param array $feed, passed by reference
 */
function plugin_media_rss_namespace(&$feed)
{

	$feed['rss_attr']['xmlns:media'] = 'http://search.yahoo.com/mrss/';

}

/**
 * Add Media RSS specific tags to each of the <item> tags.
 */
function plugin_media_rss_item(&$item,&$post)
{
	$item['media:title']                    = $item['title'];
	$item['media:description']              = $item['description'];
	$item['media:description_attr']['type'] = 'html';
	$item['media:content']                  = array();
	$item['media:content_attr']             = 
		array(
			'url'      => $post->uri,
			'fileSize' => filesize("content/images/$post->filename"),
			'type'     => $post->type,
			'width'    => $post->width,
			'height'   => $post->height,
		);
	$item['media:thumbnail']      = array();
	$item['media:thumbnail_attr'] = 
		array(
			'url'    => $post->thumb_uri,
			'width'  => $post->thumb_width,
			'height' => $post->thumb_height,
		);
}

<?php
/**
 * RSS Feed Controller
 *
 * @package Pixelpost
 * @author Jay Williams 
 */

class feedController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
		
		// Remove the layout.phtml wrapper for feeds
		$this->layout = null;
		$this->feed_type = (string) Web2BB_Uri::fragment(1);
	}

	public function index()
	{
		
		

		if ($this->config->feed_pagination)
		{
			/**
			 * If the config option, posts_per_page is set, we will spit up the feed into pages.
			 */
			
			// Get total number of publically available posts
			$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}'";
			$this->total_posts = (int) Pixelpost_DB::get_var($sql);
			
			// Determine the total number of pages
			WEB2BB_Uri::$total_pages = (int) ceil($this->total_posts / $this->config->posts_per_page);

			// Verify that we're on a legitimate page to start with
			if (WEB2BB_Uri::$total_pages < WEB2BB_Uri::$page)
			{
				throw new Exception("Sorry, we don't have anymore pages to show!");
			}

			// The database needs to know which row we need to start with:
			$range = (int) (WEB2BB_Uri::$page - 1) * $this->config->posts_per_page;
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT {$range}, {$this->config->feed_items}";
		}
		else
		{
			/**
			 * the config option, posts_per_page, isn't set, so display ALL the posts
			 */
			
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0, {$this->config->feed_items}";
		}


		// Grab the data object from the DB. Returns null on failure.
		$this->posts = (array) Pixelpost_DB::get_results($sql);


		// Tack on image data to the posts array
		foreach ($this->posts as $key => $post)
		{
			$image_info = getimagesize('content/images/' . $post->filename);
			
			$this->posts[$key]->id        = (int) $this->posts[$key]->id;
			$this->posts[$key]->permalink = $this->config->url.'post/'.$post->id;
			$this->posts[$key]->width     = $image_info[0];
			$this->posts[$key]->height    = $image_info[1];
			$this->posts[$key]->type      = $image_info['mime'];
			$this->posts[$key]->uri       = $this->config->url.'content/images/' . $post->filename;
			
			$image_info = getimagesize('content/images/thumb_' . $post->filename);
			
			$this->posts[$key]->thumb_width  = $image_info[0];
			$this->posts[$key]->thumb_height = $image_info[1];
			$this->posts[$key]->thumb_type   = $image_info['mime'];
			$this->posts[$key]->thumb_uri    = $this->config->url.'content/images/thumb_' . $post->filename;
		}
		
		/**
		 * If index is called, without specifying a feed type,
		 * auto-run the default rss method.
		 */
		if (empty($this->feed_type) || !method_exists($this,$this->feed_type)) {
			$this->rss();
		}

		
	}
	
	public function rss()
	{
		/**
		 * If this method is being auto-run by index(), 
		 * we don't need to run it here.
		 */
		if ($this->feed_type == 'rss') {
			$this->index();
		}
		
		/**
		 * Transmit the RSS feed using the XML content type
		 */
		@header('Content-Type: text/xml; charset=utf-8');
		
		/**
		 * Initialize the feed array
		 */
		$this->feed = array();
		
		/**
		 * Feed Header Information
		 */
		$this->feed['rss']['channel']['title']          = $this->config->name;
		$this->feed['rss']['channel']['link']           = $this->config->url;
		$this->feed['rss']['channel']['description']    = $this->config->description;
		$this->feed['rss']['channel']['language']       = str_replace('_','-',strtolower($this->config->locale));
		if(isset($this->config->copyright))
		$this->feed['rss']['channel']['copyright']      = $this->config->copyright;
		$this->feed['rss']['channel']['pubDate']        = date(DATE_RSS,time());
		$this->feed['rss']['channel']['generator']      = "Ultralite";
		$this->feed['rss']['channel']['atom:link'][0]    = null;
		$this->feed['rss']['channel']['atom:link']['0_attr'] = 
			array(  
				'rel'  => 'self',
				'type' => 'application/rss+xml',
				'href' => $this->config->url.'feed',
			);
		
		
		if ($this->config->feed_pagination && (WEB2BB_Uri::$page) > 1)
		{
			$this->feed['rss']['channel']['atom:link'][1]    = null;
			$this->feed['rss']['channel']['atom:link']['1_attr']['rel']  = 'previous';
			$this->feed['rss']['channel']['atom:link']['1_attr']['href']  = $this->config->url.'feed';
			
			if ((WEB2BB_Uri::$page-1) != 1)
				$this->feed['rss']['channel']['atom:link']['1_attr']['href'] .= '/page/'. (WEB2BB_Uri::$page-1);
		}

		if ($this->config->feed_pagination && WEB2BB_Uri::$page < WEB2BB_Uri::$total_pages)
		{
			$this->feed['rss']['channel']['atom:link'][2]    = null;
			$this->feed['rss']['channel']['atom:link']['2_attr']['rel']  = 'next';
			$this->feed['rss']['channel']['atom:link']['2_attr']['href']  = $this->config->url.'feed/page/'. (WEB2BB_Uri::$page+1);
		}
		
		/**
		 * Include the feed icon, if it exists:
		 */
		if (file_exists(__THEME_PATH."/{$this->config->theme}/images/feed_icon.png"))
		{
			$image = getimagesize(__THEME_PATH."/{$this->config->theme}/images/feed_icon.png");
			
			$this->feed['rss']['channel']['image']['title']  = $this->config->name;
			$this->feed['rss']['channel']['image']['link']   = $this->config->url;
			$this->feed['rss']['channel']['image']['url']    = "{$this->config->url}content/themes/{$this->config->theme}/images/feed_icon.png";
			$this->feed['rss']['channel']['image']['width']  = $image[0];
			$this->feed['rss']['channel']['image']['height'] = $image[1];
			$this->feed['rss']['channel']['atom:icon']       = "{$this->config->url}content/themes/{$this->config->theme}/images/feed_icon.png";
		}
		
		/**
		 * Feed Items
		 */
		$this->feed['rss']['channel']['item'] = array();
		
		foreach ($this->posts as $id => $post) {
			
			$this->feed['rss']['channel']['item'][$id] = 
				array(
					'title'       => $post->title,
					'link'        => $post->permalink,
					'description' => "<img src=\"{$post->uri}\" alt=\"$post->title\" width=\"$post->width\" height=\"$post->height\" /><br />$post->description",
					'pubDate'     => date(DATE_RSS,strtotime($post->published)),
					'guid'        => $post->permalink,
				);
				
			/**
			 * Begin Media RSS Specific Tags
			 * @todo Add Media RSS tags via a plugin
			 */
			if ($this->config->feed_media_rss) {
			$this->feed['rss']['channel']['item'][$id]['media:title']                    = $this->feed['rss']['channel']['item'][$id]['title'];
			$this->feed['rss']['channel']['item'][$id]['media:description']              = $this->feed['rss']['channel']['item'][$id]['description'];
			$this->feed['rss']['channel']['item'][$id]['media:description_attr']['type'] = 'html';
			$this->feed['rss']['channel']['item'][$id]['media:content']                  = array();
			$this->feed['rss']['channel']['item'][$id]['media:content_attr']             = 
				array(
					'url'      => $post->uri,
					'fileSize' => filesize("content/images/$post->filename"),
					'type'     => $post->type,
					'width'    => $post->width,
					'height'   => $post->height,
				);
			$this->feed['rss']['channel']['item'][$id]['media:thumbnail']      = array();
			$this->feed['rss']['channel']['item'][$id]['media:thumbnail_attr'] = 
				array(
					'url'    => $post->thumb_uri,
					'width'  => $post->thumb_width,
					'height' => $post->thumb_height,
				);
			}
			/**
			 * End Media RSS Specific Tags
			 */
				
		}
		
		/**
		 * Feed Attributes and Namespaces
		 */
		$this->feed['rss_attr'] = 
			array(
				'version'    => '2.0',
				'xmlns:dc'   => 'http://purl.org/dc/elements/1.1/',
				'xmlns:atom' => 'http://www.w3.org/2005/Atom',
			  );
		
		/**
		 * Media RSS Specific Namespace
		 * 
		 * @todo Add via plugin
		 */
		if ($this->config->feed_media_rss) {
			$this->feed['rss_attr']['xmlns:media'] = 'http://search.yahoo.com/mrss/';
		}
		
		/**
		 * Sent the Values out to the view
		 */
		$this->view->feed  = $this->feed;
		$this->view->posts = $this->posts;
	}
	
	public function atom()
	{
		$this->index();
		
		/**
		 * @todo Add ATOM Specific Code
		 */
		
		$this->feed = array();
		
		
		/**
		 * Sent the Values out to the view
		 */
		$this->view->feed  = $this->feed;
		$this->view->posts = $this->posts;
		
	}
}

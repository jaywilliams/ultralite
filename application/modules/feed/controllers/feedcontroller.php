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
		$this->_layout = null;
		$this->feed_type = (string) $this->_uri->fragment(1);
	}

	public function index()
	{
		
		$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->_config->current_time}' ORDER BY `published` ASC LIMIT 0, {$this->_config->feed_items}";

		// Grab the data object from the DB. Returns null on failure.
		$this->posts = Pixelpost_DB::get_results($sql);

		// Only load the template if the query was successful.
		// We can display a nice error or splash screen otherwise...
		if (empty($this->posts))
		{
			// Error? Splash Screen?
			throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
		}

		// Tack on image data to the posts array
		foreach ($this->posts as $key => $post)
		{
			$image_info = getimagesize('content/images/' . $post->filename);
			
			$this->posts[$key]->width  = $image_info[0];
			$this->posts[$key]->height = $image_info[1];
			$this->posts[$key]->type   = $image_info['mime'];
			
			$image_info = getimagesize('content/images/thumb_' . $post->filename);
			
			$this->posts[$key]->thumb_width  = $image_info[0];
			$this->posts[$key]->thumb_height = $image_info[1];
			$this->posts[$key]->thumb_type   = $image_info['mime'];
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
		$this->feed['rss']['channel']['title']          = $this->_config->name;
		$this->feed['rss']['channel']['link']           = $this->_config->url;
		$this->feed['rss']['channel']['description']    = $this->_config->description;
		$this->feed['rss']['channel']['language']       = str_replace('_','-',strtolower($this->_config->locale));
		$this->feed['rss']['channel']['pubDate']        = date(DATE_RSS,time());
		$this->feed['rss']['channel']['atom:link']      = array();
		$this->feed['rss']['channel']['atom:link_attr'] = 
			array(  
				'href' => $this->_config->url.'feed',
		        'rel'  => 'self',
		        'type' => 'application/rss+xml',
			);
		
		/**
		 * Feed Items
		 */
		$this->feed['rss']['channel']['item'] = array();
		
		foreach ($this->posts as $id => $post) {
			
			$this->feed['rss']['channel']['item'][$id] = 
				array(
					'title'       => $post->title,
					'link'        => $this->_config->url.'post/'.$post->id,
					'description' => "<img src=\"{$this->_config->url}content/images/$post->filename\" alt=\"$post->title\" width=\"$post->width\" height=\"$post->height\" /><br />$post->description",
					'pubDate'     => date(DATE_RSS,strtotime($post->published)),
					'guid'        => $this->_config->url.'post/'.$post->id,
				);
				
			/**
			 * Begin Media RSS Specific Tags
			 * @todo Add Media RSS tags via a plugin
			 */
			$this->feed['rss']['channel']['item'][$id]['media:title']                    = $this->feed['rss']['channel']['item'][$id]['title'];
			$this->feed['rss']['channel']['item'][$id]['media:description']              = $this->feed['rss']['channel']['item'][$id]['description'];
			$this->feed['rss']['channel']['item'][$id]['media:description_attr']['type'] = 'html';
			$this->feed['rss']['channel']['item'][$id]['media:content']                  = array();
			$this->feed['rss']['channel']['item'][$id]['media:content_attr']             = 
				array(
					'url'      => "{$this->_config->url}content/images/$post->filename",
					'fileSize' => filesize("content/images/$post->filename"),
					'type'     => $post->type,
					'width'    => $post->width,
					'height'   => $post->height,
				);
			$this->feed['rss']['channel']['item'][$id]['media:thumbnail']      = array();
			$this->feed['rss']['channel']['item'][$id]['media:thumbnail_attr'] = 
				array(
					'url'    => "{$this->_config->url}content/images/thumb_$post->filename",
					'width'  => $post->width,
					'height' => $post->height,
				);
			/**
			 * End Media RSS Specific Tags
			 */
				
		}
		
		/**
		 * Feed Attributes and Namespaces
		 */
		$this->feed['rss_attr'] = 
			array(
				'xmlns:dc'   => 'http://purl.org/dc/elements/1.1/',
				'xmlns:atom' => 'http://www.w3.org/2005/Atom',
				'version'    => '2.0',
			  );
		
		/**
		 * Media RSS Specific Namespace
		 * 
		 * @todo Add via plugin
		 */
		$this->feed['rss_attr']['xmlns:media'] = 'http://search.yahoo.com/mrss/';
		
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

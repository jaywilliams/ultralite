<?php
/**
 * RSS Feed Controller
 *
 * @package Pixelpost
 * @author Jay Williams 
 */

class Module_Feed_indexController extends Module_Base_baseController implements Model_Interface
{

	public function __construct()
	{
		parent::__construct();
		
		// Remove the layout.phtml wrapper for feeds
		$this->layout = null;
		$this->feed_type = (string) Pixelpost_Uri::fragment(1);
	}

	public function indexAction()
	{
		
		
		// Start with row 0 at the beginning...
		$range = 0;
		
		/**
		 * Feed Pagination
		 */
		if ($this->config->feed_pagination)
		{
			/**
			 * If the config option, feed_pagination is set, we will spit up the feed into pages.
			 */
			
			// Get total number of publically available posts
			$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}'";
			$this->total_posts = (int) Pixelpost_DB::get_var($sql);
			
			// Determine the total number of pages
			Pixelpost_Uri::$total_pages = (int) ceil($this->total_posts / $this->config->posts_per_page);

			// Verify that we're on a legitimate page to start with
			if (Pixelpost_Uri::$total_pages < Pixelpost_Uri::$page)
			{
				// @todo this error displays if the database call doesn't work.
				throw new Exception("Sorry, we don't have anymore pages to show!");
			}
			
			// The database needs to know which row we need to start with:
			$range = (int) (Pixelpost_Uri::$page - 1) * $this->config->posts_per_page;
		}
		
		$posts_sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT {$range}, {$this->config->feed_items}";
		
		// Grab the data object from the DB.
		$this->posts = (array) Pixelpost_DB::get_results($posts_sql);
		
		
		/**
		 * The RSS feed can't have altered published dates,
		 * so we need to completely nuke "filter_published" 
		 * before we can run processPosts().
		 */
		Pixelpost_Plugin::removeFilterHook('filter_published');
		
		/**
		 * Run the posts through the Plugin system, and apply any 
		 * necessary data before sending the array to the view.
		 */
		$this->processPosts();
		
		/**
		 * If index is called, without specifying a feed type,
		 * auto-run the default rss method.
		 */
		if (empty($this->feed_type) || !method_exists($this,$this->feed_type.'Action')) {
			$this->rssAction();
		}

		
	}
	
	public function rssAction()
	{
		/**
		 * If this method is being auto-run by index(), 
		 * we don't need to run it here.
		 */
		if ($this->feed_type == 'rss') {
			$this->indexAction();
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
		$this->feed['rss']['channel']['title']          = $this->config->site_name;
		$this->feed['rss']['channel']['link']           = $this->config->url;
		$this->feed['rss']['channel']['description']    = $this->config->site_description;
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
		
		
		/**
		 * Feed Pagination - Next:
		 */
		if ($this->config->feed_pagination && (Pixelpost_Uri::$page) > 1)
		{
			// Add the current page to the rel=self link:
			$this->feed['rss']['channel']['atom:link']['0_attr']['href'] .= '/page/'.(Pixelpost_Uri::$page);
			
			$this->feed['rss']['channel']['atom:link'][1]    = null;
			$this->feed['rss']['channel']['atom:link']['1_attr'] = 
				array(  
					'rel'  => 'previous',
					// 'type' => 'application/rss+xml',
					'href' => $this->config->url.'feed',
				);
			
			if ((Pixelpost_Uri::$page-1) != 1)
				$this->feed['rss']['channel']['atom:link']['1_attr']['href'] .= '/page/'. (Pixelpost_Uri::$page-1);
		}

		/**
		 * Feed Pagination - Previous:
		 */
		if ($this->config->feed_pagination && Pixelpost_Uri::$page < Pixelpost_Uri::$total_pages)
		{
			$this->feed['rss']['channel']['atom:link'][2]    = null;
			$this->feed['rss']['channel']['atom:link']['2_attr'] = 
				array(  
					'rel'  => 'next',
					// 'type' => 'application/rss+xml',
					'href' => $this->config->url.'feed/page/'. (Pixelpost_Uri::$page+1),
				);
			
		}
		
		/**
		 * Include the feed icon, if it exists:
		 */
		if (file_exists(THMPATH."/{$this->config->theme}/images/feed_icon.png"))
		{
			// $image = getimagesize(THMPATH."/{$this->config->theme}/images/feed_icon.png");
			
			$this->feed['rss']['channel']['image']['title']  = $this->config->site_name;
			$this->feed['rss']['channel']['image']['link']   = $this->config->url;
			$this->feed['rss']['channel']['image']['url']    = "{$this->config->url}content/themes/{$this->config->theme}/images/feed_icon.png";
			// $this->feed['rss']['channel']['image']['width']  = $image[0];
			// $this->feed['rss']['channel']['image']['height'] = $image[1];
			$this->feed['rss']['channel']['atom:icon']       = "{$this->config->url}content/themes/{$this->config->theme}/images/feed_icon.png";
		}
		
		
		/**
		 * Allow any plugins to add/remove tags from the <channel> section of the RSS feed:
		 */
		Pixelpost_Plugin::executeAction('hook_rss_channel', $this->feed['rss']['channel']);
		
		/**
		 * Feed Items
		 */
		$this->feed['rss']['channel']['item'] = array();
		
		foreach ($this->posts as $id => $post) {
			
			$this->feed['rss']['channel']['item'][$id] = 
				array(
					'title'       => $post->title,
					'link'        => $post->permalink,
					'description' => "<p><a href=\"$post->permalink\"><img src=\"{$post->uri}\" alt=\"$post->title\" width=\"$post->width\" height=\"$post->height\" /></a></p>$post->description",
					'pubDate'     => date(DATE_RSS,strtotime($post->published)),
					// 'author'      => $post->author, // @todo add Author tag
					// 'dc:creator'      => $post->author, // @todo add Author tag
					'guid'        => $post->permalink,
				);
				
			/**
			 * Allow any plugins to add/remove tags from each of the <item> tags in the RSS feed:
			 */
			Pixelpost_Plugin::executeAction('hook_rss_item', $this->feed['rss']['channel']['item'][$id], $post);
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
		 * Allow any plugins to add/remove tags from the entire this->feed array:
		 */
		Pixelpost_Plugin::executeAction('hook_rss_feed', $this->feed);
		
		/**
		 * Sent the Values out to the view
		 */
		$this->view->feed  = $this->feed;
		$this->view->posts = $this->posts;
	}
	
	public function atomAction()
	{
		$this->indexAction();
		
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

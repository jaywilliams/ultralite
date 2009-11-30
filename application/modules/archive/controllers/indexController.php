<?php
/**
 * Controller for the archive page
 *
 * @package Pixelpost
 * @author Dennis Mooibroek
 * @author Jay Williams
 *
 *
 */

class Module_Archive_indexController extends Module_Base_baseController implements Model_Interface
{
	
	public $posts;

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction()
	{
		
		if (!is_array($this->posts))
		{
			// Page Title
			$this->view->title = 'The Past';
			
			if ($this->config->posts_per_page > 0)
			{
				/**
				 * If the config option, posts_per_page is set, we will spit up the archive into pages.
				 */
			
				// Get total number of publically available posts
				$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}'";
				$this->total_posts = (int) Pixelpost_DB::get_var($sql);
			
				// Determine the total number of pages
				Pixelpost_Uri::$total_pages = (int) ceil($this->total_posts / $this->config->posts_per_page);

				// Verify that we're on a legitimate page to start with
				if (Pixelpost_Uri::$total_pages < Pixelpost_Uri::$page)
				{
					throw new Exception("Sorry, we don't have anymore pages to show!");
				}

				// The database needs to know which row we need to start with:
				$range = (int) (Pixelpost_Uri::$page - 1) * $this->config->posts_per_page;
				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT {$range}, {$this->config->posts_per_page}";
			}
			else
			{
				/**
				 * the config option, posts_per_page, isn't set, so display ALL the posts
				 */
			
				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC";
			}

			/**
			 * The posts to list:
			 */
			$this->posts = (array) Pixelpost_DB::get_results($sql);
			
		} // !is_array($this->posts)
		
		/**
		 * Run the posts through the Plugin system, and apply any 
		 * necessary data before sending the array to the view.
		 */
		$this->processPosts();
		
		/**
		 * Assign the variables to be used in the view
		 * $this->view->myVar can be accessed in the template as $myVar
		 */
		
		$this->view->thumbnails = $this->_thumbnails();
		$this->view->posts = $this->posts;
	}
	
	protected function _thumbnails()
	{
		// create thumbnails list
		$thumbnails = '';
		foreach ($this->posts as $post)
		{
			$thumbnails .= "<a href=\"$post->permalink\">" . "<img src=\"{$post->thumb_uri}\" alt=\"" . $post->title . "\" width=\"{$post->thumb_width}\" height=\"{$post->thumb_height}\" />" . "</a>";
		}
		return $thumbnails;
	}
	
}

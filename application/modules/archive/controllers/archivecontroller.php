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

class archiveController extends baseController implements IController
{
	
	public $posts, $otal_pages;

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
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
			WEB2BB_Uri::$total_pages = (int) ceil($this->total_posts / $this->config->posts_per_page);

			// Verify that we're on a legitimate page to start with
			if (WEB2BB_Uri::$total_pages < WEB2BB_Uri::$page)
			{
				throw new Exception("Sorry, we don't have anymore pages to show!");
			}

			// The database needs to know which row we need to start with:
			$start = (int) (WEB2BB_Uri::$page - 1) * $this->config->posts_per_page;
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT {$start}, {$this->config->posts_per_page}";
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
		$this->posts = Pixelpost_DB::get_results($sql);

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
			$thumbnails .= "<a href=\"$post->permalink\">" . "<img src=\"{$post->thumb_uri}\" alt=\"" . escape($post->title) . "\" width=\"{$post->thumb_width}\" height=\"{$post->thumb_height}\" />" . "</a>";
		}
		return $thumbnails;
	}
	
}

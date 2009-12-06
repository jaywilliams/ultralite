<?php
/**
 *
 * @Model_Archive Class
 *
 * @package Ultralite
 * @Author Dennis Mooibroek
 *
 */

class Model_Archive
{
    /**
     *
     * The constructor, duh
     *
     */
    public function __construct()
    {
        $this->config = $config;
        $this->posts = $posts;
    }

    /**
     * Gets the information of an image specified by the image_id
     * as well as getting the previous and next image.
     *
     * Adds a new array post to the variable $this. This new array 
     * will contain the current, previous and next post information
     *
     * @param $id int id of the image to get. if is empty we grab the current image
     *
     * @access public
     *
     * @return $this->posts
     */
    public static function getDetails()
    {
        /**
         * Since the variables $config and $post are public we don't have to pass them
         * to the function
         */
        
  		if (!is_array($this->posts))
		{
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
        return $this->posts;
    }
}
 /*** end of class ***/

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
		// Page Title
		$this->view->title = 'The Past';
        
        /**
		 * We have to create an $this->posts to allow the Plugin system
         * to work on the code 
		 */
        $this->posts = Model_Archive::getDetails();
	    
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

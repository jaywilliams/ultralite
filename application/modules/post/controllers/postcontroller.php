<?php
/**
 * Controller for the post page (shows image)
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 * @author Jay Williams
 */


// namespace web2bb;

class postController extends baseController implements IController
{

	public $posts = array('previous'=>null,'current'=>null,'next'=>null);

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		/**
		 * Determine the image ID we need to lookup, and verify that it is a positive integer:
		 */
		$this->id = (int) Web2BB_Uri::fragment(1);
		$this->id = ($this->id > 0) ? $this->id : 0;
		
		/**
		 * Current Image
		 */
		if (empty($this->id))
		{
			// If no ID is specified, grab the latest image:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
		}
		else
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$this->id' AND `published` <= '{$this->config->current_time}' LIMIT 0,1";
		}
		
		// Assign the current image to the $posts array
		$this->posts['current'] = Pixelpost_DB::get_row($sql);
		
		
		/**
		 * Verify that the image exists:
		 */
		if (!is_object($this->posts['current']))
		{
			// Error? Splash Screen?
			throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
		}

		/**
		 * Next Image
		 */
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` < '{$this->posts['current']->published}') and (`published` <= '{$this->config->current_time}') ORDER BY `published` DESC LIMIT 0,1";

		$this->posts['next'] = Pixelpost_DB::get_row($sql);
		
		/**
		 * If we are on the last image, there isn't a next image, 
		 * so we can wrap around to the first image:
		 */
		if (!is_object($this->posts['next']))
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
			
			$this->posts['next'] = Pixelpost_DB::get_row($sql);
		}

		/**
		 * Previous Image
		 */
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` > '{$this->posts['current']->published}') and (`published` <= '{$this->config->current_time}') ORDER BY `published` ASC LIMIT 0,1";

		$this->posts['previous'] = Pixelpost_DB::get_row($sql);
		
		/**
		 * If the first image, we can't go back any further, 
		 * so we can wrap around to the last image:
		 */
		if (!is_object($this->posts['previous']))
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` ASC LIMIT 0,1";
			
			$this->posts['previous'] = Pixelpost_DB::get_row($sql);
		}
		
		
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
		
		$this->view->title = $this->posts['current']->title;
		$this->view->posts = $this->posts;
		
		/**
		 * Inclusion of the actual template needed is handled in the destruct
		 * function of the base controller.
		 */
	}
}

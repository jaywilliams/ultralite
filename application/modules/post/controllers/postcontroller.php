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
		/**
	 * Path to image directory
	 *
	 * @var string
	 */
	private $path = IMGPATH;

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
		 * Check if there is a Current Image, else get Current image
		 */
		if (!is_object($this->posts['current']))
		{
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
		}
		
		/**
		 * Verify that the image exists, either from a plugin or from the code above:
		 */
		if (!is_object($this->posts['current']))
		{
			// Error? Splash Screen?
			throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
		}

		/**
		 * Check if Next Image exists, else get Next image
		 */
		if (!is_object($this->posts['next']))
		{
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
		}

		/**
		 * Check if Previous Image exists, else get Previous image
		 */
		if (!is_object($this->posts['previous']))
		{
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
		}
		
		
		/**
		 * Run the posts through the Plugin system, and apply any 
		 * necessary data before sending the array to the view.
		 */
		$this->processPosts();
		
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

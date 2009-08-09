<?php
/**
 * Controller for the post page (shows image)
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 *
 *
 */


// namespace web2bb;

class postController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		/**
		 * Get all the variables from the database
		 */
		/*** the cache id is based on the file name ***/
		// $cache_id = md5('admin/index.phtml');

		$post = new stdClass;

		// Clean the image id number. Set to int 0 if invalid OR empty.
		$id = Web2BB_Uri::fragment(1);
		$post->id = (isset($id) && (int)$id > 0) ? (int)$id : 0;

		if ($post->id > 0)
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$post->id' AND `published` <= '{$this->config->current_time}' LIMIT 0,1";
		}
		else
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
		}

		// Grab the data object from the DB. Returns null on failure.
		$post = Pixelpost_DB::get_row($sql);

		// Only load the template if the query was successful.
		// We can display a nice error or splash screen otherwise...
		if (!is_object($post))
		{
			// Error? Splash Screen?
			throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
		}

		// Set the variables
		$image_info = getimagesize('content/images/' . $post->filename);
		
		$post->width = $image_info[0];
		$post->height = $image_info[1];
		$post->dimensions = $image_info[3];
		$post->type = $image_info['mime'];
		$post->uri = $this->config->url.'content/images/' . $post->filename;

		// Retrieve the Next image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` < '$post->published') and (`published` <= '{$this->config->current_time}') ORDER BY `published` DESC LIMIT 0,1";
		
		$next_image = Pixelpost_DB::get_row($sql);
		if (!is_object($next_image))
		{
			// Lets wrap around to the first image.

			// Retrieve the First image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
			$next_image = Pixelpost_DB::get_row($sql);
		}


		// Retrieve the Prev image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` > '$post->published') and (`published` <= '{$this->config->current_time}') ORDER BY `published` ASC LIMIT 0,1";

		$previous_image = Pixelpost_DB::get_row($sql);
		if (!is_object($previous_image))
		{
			// Lets wrap around to the last image.

			// Retrieve the Last image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` ASC LIMIT 0,1";
			$previous_image = Pixelpost_DB::get_row($sql);
		}
		
		/**
		 * Assign the variables to be used in the view
		 * $this->view-myVar can be accessed in the template as $myVar
		 */
		
		$this->view->title = $post->title;
		$this->view->post = $post;
		$this->view->previous_image = $previous_image;
		$this->view->next_image = $next_image;		
		
		/**
		 * Inclusion of the actual template needed is handled in the destruct
		 * function of the base controller.
		 */
	}
}

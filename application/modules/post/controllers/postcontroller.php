<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
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
	 	$config = Pixelpost_Config::current();

		/*** the cache id is based on the file name ***/
		$cache_id = md5('admin/index.phtml');

		$post = new stdClass;
		// Clean the image id number. Set to int 0 if invalid OR empty.
		$this->_uri = Web2BB_Uri::getInstance();
		
		$id = $this->_uri->fragment(1);
		
		$post->id = (isset($id) && (int)$id > 0) ? (int)$id : 0;


		if ($post->id > 0)
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$post->id' AND `published` <= '{$config->current_time}' LIMIT 0,1";
		}
		else
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC LIMIT 0,1";
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

		// Retrieve the Next image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` > '$post->published') and (`published` <= '{$config->current_time}') ORDER BY `published` ASC LIMIT 0,1";
		
		$next_image = Pixelpost_DB::get_row($sql);
		if (!is_object($next_image))
		{
			// Lets wrap around to the first image.

			// Retrieve the First image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC LIMIT 0,1";
			$next_image = Pixelpost_DB::get_row($sql);
		}


		// Retrieve the Prev image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` < '$post->published') and (`published` <= '{$config->current_time}') ORDER BY `published` DESC LIMIT 0,1";

		$previous_image = Pixelpost_DB::get_row($sql);
		if (!is_object($previous_image))
		{
			// Lets wrap around to the last image.

			// Retrieve the Last image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
			$previous_image = Pixelpost_DB::get_row($sql);
		}
		
		/**
		 * Assign the variables to be used in the view
		 * $this->view-myVar can be accessed in the template as $myVar
		 */
		
		$this->view->post = $post;
		$this->view->previous_image = $previous_image;
		$this->view->next_image = $next_image;		
		$this->view->config = $config;
		
		/**
		 * Do the template stuff here
		 */
		$template = Pixelpost_Config::current()->template;

		/**
		 * Make sure we do have a controller, so either select the one from
		 * the uri or select the default. This is in case this controller is
		 * selected as default in the config. Then the uri method won't work.
		 */
		 
		if($this->_uri->fragment(0))
		{
			$this->_controller = $this->_uri->fragment(0);
		}
		else
		{
			// get the default controller
			$this->_controller = Pixelpost_Config::current()->default_controller;
		}

		if (file_exists(__THEME_PATH.'/'.$template.'/views/'.$this->_controller.'.phtml'))
		{
			$this->content = $this->view->fetch(__THEME_PATH.'/'.$template.'/views/'.$this->_controller.'.phtml', $cache_id);
		}
		else
		{
			$this->content = $tpl->fetch(__APP_PATH . '/modules/post/views/index.phtml', $cache_id);
		}
	}
}

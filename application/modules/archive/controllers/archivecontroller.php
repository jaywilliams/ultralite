<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class archiveController extends baseController implements IController
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
	    
		$archive = new stdClass;
		$archive->title = 'The Past';

		// If another controller has already created a query,
		// run with that, rather than create our own:
		if (!isset($archive->thumbnails))
		{
			if (Pixelpost_Config::current()->pagination > 0)
			{

				$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$config->current_time}'";
				// Get total images publically available
				$image->total = (int)$db->get_var($sql);
				// Determine the total number of available pages
				$config->total_pages = (int)ceil($image->total / $config->pagination);

				// The page doesn't exist!
				if ($config->total_pages < $config->page)
				{
					throw new Exception("Sorry, we don't have anymore pages to show!");
				}

				// The database needs to know which row we need to start with:
				$range = (int)($config->page - 1) * $config->pagination;

				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC LIMIT $range, $config->pagination";
			}
			else
			{
				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$config->current_time}' ORDER BY `published` ASC";
			}

			// Store the thumbnails array
			$archive->thumbnails = Pixelpost_DB::get_results($sql);

		}

		// Tack on thumbnail data to the thumbnails array
		foreach ($archive->thumbnails as $key => $thumbnail)
		{
			$image_info = getimagesize('content/images/thumb_' . $thumbnail->filename);

			$archive->thumbnails[$key]->width = $image_info[0];
			$archive->thumbnails[$key]->height = $image_info[1];
			$archive->thumbnails[$key]->dimensions = $image_info[3];
		}
		// create thumbnails list
		foreach ($archive->thumbnails as $thumbnail)
		{
			$archive->thumbnails_output .= ("<a href=\"post/" . $thumbnail->id . "\">" . "<img src=\"content/images/thumb_{$thumbnail->filename}\" alt=\"" . escape($thumbnail->title) . "\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />" . "</a>");
		}
		
		/**
		 * Assign the variables to be used in the view
		 * $this->view-myVar can be accessed in the template as $myVar
		 */
		
		$this->view->archive = $archive;
		$this->view->config = $config;

		/**
		 * Do the template stuff here
		 */
		$template = Pixelpost_Config::current()->template;
		$this->_uri = Web2BB_uri::getInstance();
		$this->view->config = Pixelpost_Config::current();

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

		/*** a new view instance ***/
		if (file_exists(__THEME_PATH . '/' . $template . '/views/' . $this->_controller . '.phtml'))
		{
			$this->content = $this->view->fetch(__THEME_PATH . '/' . $template . '/views/' . $this->_controller . '.phtml', $cache_id);
		}
		else
		{
			$this->content = $tpl->fetch(__APP_PATH . '/modules/post/views/index.phtml', $cache_id);
		}
	}
}

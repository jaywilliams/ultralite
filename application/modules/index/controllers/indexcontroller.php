<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class indexController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		$current_time = Pixelpost_Config::current()->current_time;
		$template = Pixelpost_Config::current()->template;
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir( __THEME_PATH.'/'.$template);

		/*** the include template ***/
		$tpl->include_tpl =  __THEME_PATH.'/'.$template.'/views/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'WEB2BB - Development Made Easy';
		$this->view->heading = 'WEB2BB';

		// a new config
		//$config = config::getInstance();
		//$this->view->version = 'test';

		/*** the cache id is based on the file name ***/
		$cache_id = md5('admin/index.phtml');

		$post = new stdClass;
		// Clean the image id number. Set to int 0 if invalid OR empty.
		$post->id = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : 0;


		if ($post->id > 0)
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `id` = '$post->id' AND `published` <= '{$current_time}' LIMIT 0,1";
		}
		else
		{
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$current_time}' ORDER BY `published` ASC LIMIT 0,1";
		}

		// Grab the data object from the DB. Returns null on failure.
		$post = Pixelpost_DB::get_row($sql);

		// Only load the template if the query was successful.
		// We can display a nice error or splash screen otherwise...
		if (!is_object($post))
		{
			// Error? Splash Screen?
			die("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
		}

		// Set the variables
		$image_info = getimagesize('content/images/' . $post->filename);

		$post->width = $image_info[0];
		$post->height = $image_info[1];
		$post->dimensions = $image_info[3];

		$this->view->post = $post;


		// Retrieve the Next image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` > '$post->published') and (`published` <= '{$current_time}') ORDER BY `published` ASC LIMIT 0,1";

		$next_image = Pixelpost_DB::get_row($sql);
		if (!is_object($next_image))
		{
			// Lets wrap around to the first image.

			// Retrieve the First image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$current_time}' ORDER BY `published` ASC LIMIT 0,1";
			$next_image = Pixelpost_DB::get_row($sql);
			$this->view->post->next_image = $next_image;
		}


		// Retrieve the Prev image information:
		$sql = "SELECT * FROM `pixelpost` WHERE (`published` < '$post->published') and (`published` <= '{$current_time}') ORDER BY `published` DESC LIMIT 0,1";

		$previous_image = Pixelpost_DB::get_row($sql);
		if (!is_object($previous_image))
		{
			// Lets wrap around to the last image.

			// Retrieve the Last image information:
			$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$current_time}' ORDER BY `published` DESC LIMIT 0,1";
			$previous_image = Pixelpost_DB::get_row($sql);
			$this->view->post->previous_image = $previous_image;
		}

		$this->view->name = Pixelpost_Config::current()->name;
		$this->view->description = Pixelpost_Config::current()->description;
		$this->view->url = Pixelpost_Config::current()->url;
		$this->view->locale = Pixelpost_Config::current()->locale;
		


		/*** fetch the template ***/
		$this->content = $tpl->fetch('views/index.phtml', $cache_id);
	}

	public function test()
	{
		$view = new view;
		$view->text = 'this is a test';
		$result = $view->fetch(__APP_PATH . '/views/index.php');
		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}
}

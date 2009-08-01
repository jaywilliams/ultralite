<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class aboutController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		/**
		 * Do the template stuff here
		 */
		$template = Pixelpost_Config::current()->template;
		$this->_uri = Web2BB_Uri::getInstance();
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

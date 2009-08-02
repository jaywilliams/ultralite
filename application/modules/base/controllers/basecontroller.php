<?php

// namespace web2bb;

class baseController
{
	protected $breadcrumbs, $view, $content=null;

	public function __construct()
	{
		$this->view = new view;
		
		/**
		 * initiate some standard variables for the view
 		 * $this->view-myVar can be accessed in the template as $myVar
		 */
	 	$config = Pixelpost_Config::getInstance();
	 	$plugins = new Pixelpost_Plugin();
	 	$this->_config = $config; // for use in the controllers
		$this->_uri = Web2BB_Uri::getInstance();
		$this->view->config = $config;  // for use in the templates
		$this->view->plugins = $plugins;
		$this->_template = $config->template;

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
			$this->_controller = Pixelpost_Config::getInstance()->default_controller;
		}
		
		// I'm not sure what we should do with this code. I left it for now
		/*** create the bread crumbs ***/
		$bc = new Web2BB_Breadcrumbs;
		// $bc->setPointer('->');
		$bc->crumbs();
		$this->view->Web2BB_Breadcrumbs = $bc->Web2BB_Breadcrumbs;


	}

	public function __destruct()
	{
		/**
		 * Do the template stuff here
		 */
		
		if (file_exists(__THEME_PATH.'/'.$this->_template.'/views/'.$this->_controller.'.phtml'))
		{
			$this->content = $this->view->fetch(__THEME_PATH.'/'.$this->_template.'/views/'.$this->_controller.'.phtml', $cache_id);
		}
		else
		{
			$this->content = $tpl->fetch(__APP_PATH . '/modules/'.$this->_controller.'/views/index.phtml', $cache_id);
		}

		if( !is_null( $this->content ) )
		{
			$this->view->content = $this->content;
			$template = Pixelpost_Config::getInstance()->template;
			$result = $this->view->fetch( __THEME_PATH.'/'.$template.'/layout.phtml' );
			$fc = FrontController::getInstance();
			$fc->setBody($result);
		}
	}
}

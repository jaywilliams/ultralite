<?php

// namespace web2bb;

class baseController
{
	protected $breadcrumbs, $view, $content = null;

	public function __construct()
	{
		$this->view = new view;

		/**
		 * initiate some standard variables for the view
		 * $this->view-myVar can be accessed in the template as $myVar
		 */
		// $config = Pixelpost_Config::getInstance();
		$this->view->plugins = new Pixelpost_Plugin();
		$this->_config = $this->view->config = Pixelpost_Config::getInstance(); // for use in the controllers
		$this->_uri = Web2BB_Uri::getInstance();
		$this->_template = &$this->_config->template;

		/**
		 * Default Layout
		 * If empty, no layout will be used.
		 */
		$this->_layout = 'layout.phtml';

		/**
		 * Make sure we do have a controller, so either select the one from
		 * the uri or select the default. This is in case this controller is
		 * selected as default in the config. Then the uri method won't work.
		 */
		if ($this->_uri->fragment(0))
		{
			/**
			 * We want to have a view based upon the URI
			 */
			$this->_view = $this->_uri->fragment(0);
			/**
			 * If the URI fragment points to an non existent controller it will
			 * try to switch to the page controller.
			 */

			if (file_exists(__APP_PATH . '/modules/' . $this->_uri->fragment(0) . '/controllers/' . $this->_uri->fragment(0) . 'controller.php'))
			{
				$this->_controller = $this->_uri->fragment(0);
			}
			else
			{
				// we couldn't locate a controller so we switched to the page (if it exists)
				if (file_exists(__APP_PATH . '/modules/' . Pixelpost_Config::getInstance()->page_controller . '/controllers/' . Pixelpost_Config::getInstance()->page_controller .'controller.php'))
				{
					$this->_controller = Pixelpost_Config::getInstance()->page_controller;
				}
				else
				{
					$this->_controller = Pixelpost_Config::getInstance()->error_controller;
				}
			}

		}
		else
		{
			// get the default controller
			$this->_controller = &$this->_config->default_controller;
			$this->_view = $this->_controller;
		}

		// I'm not sure what we should do with this code. I left it for now
		/*** create the bread crumbs ***/
		// $bc = new Web2BB_Breadcrumbs;
		// $bc->setPointer('->');
		// $bc->crumbs();
		// $this->view->Web2BB_Breadcrumbs = $bc->breadcrumbs;


	}

	public function __destruct()
	{
		/**
		 * Load the view, either from the controller view, or from the template's view file
		 * 
		 * @todo Possibly setup a $cache_id for fetch()
		 */

		if (file_exists(__THEME_PATH . '/' . $this->_template . '/views/' . $this->_view . '.phtml'))
		{
			$this->content = $this->view->fetch(__THEME_PATH . '/' . $this->_template . '/views/' . $this->_view . '.phtml');
		}
		else
		{
			$this->content = $this->view->fetch(__APP_PATH . '/modules/' . $this->_view . '/views/index.phtml');
		}

		if (!is_null($this->content))
		{
			$this->view->content = $this->content;

			/**
			 * If no layout is specified, only show the view file
			 * Otherwise load the view inside the layout.
			 */
			if (empty($this->_layout))
			{
				$result = &$this->content;
			}
			else
			{
				$result = $this->view->fetch(__THEME_PATH . '/' . $this->_config->template . '/' . $this->_layout);
			}

			$fc = FrontController::getInstance();
			$fc->setBody($result);
		}
	}
}

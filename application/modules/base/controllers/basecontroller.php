<?php

// namespace web2bb;

class baseController
{
	protected $breadcrumbs, $view, $content = null;

	public function __construct()
	{
		$this->view = new view;
		$this->front = FrontController::getInstance();
		$this->config = $this->view->config = Pixelpost_Config::getInstance(); // for use in the controllers
		
		/**
		 * @todo Pixelpost_Plugin must be switched to a singleton, otherwise we're going to have no end of troubles.
		 */
		$this->view->plugins = new Pixelpost_Plugin();

		/**
		 * Default Layout
		 * If empty, no layout will be used.
		 */
		$this->layout = 'layout.phtml';

	}

	public function __destruct()
	{
		/**
		 * Load the view, either from the controller view, or from the template's view file
		 * 
		 * @todo this complex if statement should be moved to a method, or some place out of the way.
		 */
		
		if ($this->front->getAction() != 'index' && file_exists(__THEME_PATH . "/{$this->config->theme}/views/" . $this->front->getView() . '_' . $this->front->getAction() . ".phtml")) 
		{	
			/**
			 * Check for a matching template specific View & Action phtml file 
			 * /[theme]/views/[view]_[action].phtml
			 */
			$this->content = $this->view->fetch(__THEME_PATH . "/{$this->config->theme}/views/" . $this->front->getView() . '_' . $this->front->getAction() . '.phtml');
		
		}
		else if ($this->front->getAction() != 'index' && file_exists(__APP_PATH . '/modules/' . $this->front->getController() . '/views/'. $this->front->getAction() .'.phtml')) 
		{
			/**
			 * Check for a matching controller View & Action phtml file 
			 * /[controller]/views/[action].phtml
			 */
			$this->content = $this->view->fetch(__APP_PATH . '/modules/' . $this->front->getController() . '/views/'. $this->front->getAction() .'.phtml');
		
		}
		else if (file_exists(__THEME_PATH . "/{$this->config->theme}/views/" . $this->front->getView() . ".phtml"))
		{
			/**
			 * Check for a matching template specific View phtml file 
			 * /[theme]/views/[view].phtml
			 */
			$this->content = $this->view->fetch(__THEME_PATH . "/{$this->config->theme}/views/" . $this->front->getView() . ".phtml");
		}
		else if (file_exists(__APP_PATH . '/modules/' . $this->front->getController() . '/views/index.phtml'))
		{
			/**
			 * Check for a matching template specific View phtml file 
			 * /[controller]/views/index.phtml
			 */
			$this->content = $this->view->fetch(__APP_PATH . '/modules/' . $this->front->getController() . '/views/index.phtml');
		}
		else
		{
			/**
			 * No matching View exists, display a 404 error.
			 * @todo Use a Ultralite specific Exception class
			 */
			@header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			throw new Exception('<h1>Error 404</h1><p>File Not Found</p>',404);
		}

		if (!is_null($this->content))
		{
			$this->view->content = $this->content;

			/**
			 * If no layout is specified, only show the view file
			 * Otherwise load the view inside the layout.
			 */
			if (empty($this->layout))
			{
				$result = &$this->content;
			}
			else
			{
				$result = $this->view->fetch(__THEME_PATH . '/' . $this->config->theme . '/' . $this->layout);
			}

			
			$this->front->setBody($result);
		}
	}
	
	
	
}

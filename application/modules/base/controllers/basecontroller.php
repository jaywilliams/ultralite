<?php

// namespace web2bb;

class baseController
{
	var $view, $content = null;

	public function __construct()
	{
		$this->view = new view;
		$this->front = FrontController::getInstance();
		$this->config = $this->view->config = Pixelpost_Config::getInstance(); // for use in the controllers
		
		/**
		 * @todo Pixelpost_Plugin must be switched to a singleton, otherwise we're going to have no end of troubles.
		 */
		$this->view->plugins = Pixelpost_Plugin::getInstance();

		/**
		 * Default Layout
		 * If empty, no layout will be used.
		 */
		$this->layout = 'layout.phtml';
		
		
		// if ( (int)$this->front->getAction() === 0 && $this->front->getAction() != 'index' && !method_exists($this,$this->front->getAction())) {
			
			Pixelpost_Plugin::executeAction('hook_base_construct', $this , $this->front->getController() , $this->front->getAction() );
		// }

	}
	
	/**
	 * Process Posts
	 * 
	 * Run the posts through the Plugin system, and apply any 
	 * necessary data before sending the array to the view.
	 *
	 * @param array & $posts (optional) if not specified, the function will process $this->posts.
	 * @return void
	 */
	public function processPosts(&$posts = null)
	{
		if ($posts === null)
			$posts = & $this->posts;
		
		// Tack on image data to the posts array
		foreach ($posts as $key => $post)
		{
			$posts[$key]->id          = (int) $posts[$key]->id;
			$posts[$key]->permalink   = $this->config->url.'post/'.$post->id;
			
			$image_info = getimagesize('content/images/' . $post->filename);
			
			$posts[$key]->width       = $image_info[0];
			$posts[$key]->height      = $image_info[1];
			$posts[$key]->type        = $image_info['mime'];
			$posts[$key]->uri         = $this->config->url.'content/images/' . $post->filename;
			
			$image_info = getimagesize('content/images/thumb_' . $post->filename);
			
			$posts[$key]->thumb_width  = $image_info[0];
			$posts[$key]->thumb_height = $image_info[1];
			$posts[$key]->thumb_type   = $image_info['mime'];
			$posts[$key]->thumb_uri    = $this->config->url.'content/images/thumb_' . $post->filename;
			
		}
		
		/**
		 * Allow any plugins to modify to adjust the posts before we apply the filters:
		 */
		Pixelpost_Plugin::executeAction('hook_posts', $posts, $this->front->getController(), $this->front->getAction() );
		
		foreach ($posts as $key => $post) {
			Pixelpost_Plugin::executeFilter('filter_permalink',$posts[$key]->permalink);
			Pixelpost_Plugin::executeFilter('filter_title',$posts[$key]->title);
			Pixelpost_Plugin::executeFilter('filter_description',$posts[$key]->description);
			Pixelpost_Plugin::executeFilter('filter_filename',$posts[$key]->filename);
			Pixelpost_Plugin::executeFilter('filter_published',$posts[$key]->published);
		}
	}

	public function __destruct()
	{
		
		/**
		 * Run the default site-wide filters
		 * 
		 * Make a copy of the options we are going to filter, 
		 * to make sure that the filters only change the view, not the actual config.
		 */
		$site_name        = $this->config->site_name;
		$site_description = $this->config->site_description;
		
		Pixelpost_Plugin::executeFilter('filter_site_name',$site_name);
		Pixelpost_Plugin::executeFilter('filter_site_description',$site_description);
		
		$this->view->site_name        = $site_name;
		$this->view->site_description = $site_description;

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
		else if ($this->front->getAction() != 'index' && file_exists(__PLUGIN_PATH . '/' . $this->front->getController() . '/views/'. $this->front->getAction() .'.phtml')) 
		{
			/**
			 * Check for a matching plugin controller View & Action phtml file 
			 * /plugins/[controller]/views/[action].phtml
			 */
			$this->content = $this->view->fetch(__PLUGIN_PATH . '/' . $this->front->getController() . '/views/'. $this->front->getAction() .'.phtml');
		
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
		else if (file_exists(__PLUGIN_PATH . '/' . $this->front->getController() . '/views/index.phtml'))
		{
			/**
			 * Check for a matching plugin template specific View phtml file 
			 * /plugins/[controller]/views/index.phtml
			 */
			$this->content = $this->view->fetch(__PLUGIN_PATH . '/' . $this->front->getController() . '/views/index.phtml');
		}
		else
		{
			/**
			 * No matching View exists, display a 404 error.
			 * @todo Use a Ultralite specific Exception class
			 */
			throw new Web2BB_Exception('File Not Found',404);
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

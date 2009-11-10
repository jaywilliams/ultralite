<?php

/**
 *
 * @Front Controller class
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Core
 * @Author Kevin Waterson
 * @author Dennis Mooibroek
 * 
 * Modified the class to work with the Ultralite structure
 *
 */

class Model_Front
{

	protected $_controller, $_action, $_view ,$_params, $_body, $_url;

	public static $_instance;

	public static function getInstance()
	{
		if( ! (self::$_instance instanceof self) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function __construct()
	{
		/**
		 * Set the controller
		 */
		$this->_controller = $this->_view = ( Web2BB_Uri::fragment(0) )? Web2BB_Uri::fragment(0) : Pixelpost_Config::getInstance()->default_controller;
		
		/**
		 * If the URI fragment points to an non existent controller,
		 * we'll try the page controller.
		 */
		if (!$this->controllerExists())
		{
			$this->_controller = Pixelpost_Config::getInstance()->static_controller;
		}
		
		/**
		 * Set the Action
		 */
		$this->_action = ( Web2BB_Uri::fragment(1) )? Web2BB_Uri::fragment(1) : Pixelpost_Config::getInstance()->default_action;
		
	}

	/**
	 *
 	 * The route
	 *
	 * Checks if controller and action exists
 	 */
	public function route()
	{
		// check if the controller exists
		$con = $this->loadController();
		$rc = new ReflectionClass($con );
		// if the controller exists and implements Model_Interface
		if( $rc->implementsInterface( 'Model_Interface' ) )
		{
			$controller = $rc->newInstance();
			// check if method exists 
			if( $rc->hasMethod( $this->getAction() ) )
			{
				// if all is well, load the action
				$method = $rc->getMethod( $this->getAction() );
			}
			else
			{
				// load the default action method
				//$config = config::getInstance();
				$default = Pixelpost_Config::getInstance()->default_action;
				// $this->setAction($default);
				$method = $rc->getMethod( $default );
			}
			$method->invoke( $controller );
		}
		else
		{
			throw new Exception("Interface iController must be implemented");
		}
	}

	/*
	public function getParams()
	{
		return $this->_params;
	}
	*/

	/**
	*
	* Loads the controller, sets to default if not available
	*
	* @access	public
	* @return	string	The name of the controller
	*
	*/
	public function loadController()
	{
		if( class_exists("{$this->_controller}Controller") )
		{
			return "{$this->_controller}Controller";
		}
		else
		{
			return Pixelpost_Config::getInstance()->error_controller.'Controller';
		}
	}
	
	
	public function controllerExists($controller=null)
	{
		$controller = ($controller)? $controller : $this->_controller;
		
		if (file_exists(APPPATH . "/modules/{$controller}/controllers/{$controller}controller.php")) {
			return true;
		}else {
			
			// Grab the list of enabled plugins...
			$plugins = Pixelpost_Config::getInstance()->enabled_plugins;
			
			if (in_array($controller, $plugins) && file_exists(PLGPATH . "/{$controller}/controllers/{$controller}controller.php"))
				return true;
			else
				return false;
		}
		
	}

	/**
	*
	* Get the action
	*
	* @access	public
	* @return	string	the Name of the action
	*
	*/
	public function getAction()
	{
		return $this->_action;
	}
	
	public function setAction($action)
	{
		return $this->_action = $action;
	}
	
	public function setController($controller)
	{
		return $this->_controller = $controller;
	}
	
	public function getController()
	{
		return $this->_controller;
	}
	
	public function setView($view)
	{
		return $this->_view = $view;
	}
	
	public function getView()
	{
		return $this->_view;
	}

	public function getBody()
	{
		return $this->_body;
	}

	public function setBody($body)
	{
		$this->_body = $body;
	}

} // end of class

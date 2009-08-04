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

// namespace web2bb;

class FrontController
{

	protected $_controller, $_action, $_params, $_body, $_url;

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
		// set the controller
		$this->_uri = Web2BB_Uri::getInstance();
		
		
		if ($this->_uri->fragment(0))
		{
			/**
			 * If the URI fragment points to an non existent controller it will
			 * try to switch to the page controller.
			 */

			if (file_exists(__APP_PATH . '/modules/' . $this->_uri->fragment(0) . '/controllers/' . $this->_uri->fragment(0) . 'controller.php'))
			{
				$this->_controller = $this->_uri->fragment(0).'Controller';
			}
			else
			{
				// we couldn't locate a controller so we switched to the page (if it exists)
				if (file_exists(__APP_PATH . '/modules/' . Pixelpost_Config::getInstance()->page_controller . '/controllers/' . Pixelpost_Config::getInstance()->page_controller .'controller.php'))
				{
					$this->_controller = Pixelpost_Config::getInstance()->page_controller . 'Controller';
				}
				else
				{
					$this->_controller = Pixelpost_Config::getInstance()->error_controller.'Controller';
				}
				
			}
		}
		else
		{
			// get the default controller
			$this->_controller = Pixelpost_Config::getInstance()->default_controller.'Controller';
		}
		
		// the action
		if($this->_uri->fragment(1))
		{
			$this->_action = $this->_uri->fragment(1);
		}
		else
		{
			$this->_action = 'index';
		}
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
		$con = $this->getController();
		$rc = new ReflectionClass($con );
		// if the controller exists and implements IController
		if( $rc->implementsInterface( 'IController' ) )
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
	* Gets the controller, sets to default if not available
	*
	* @access	public
	* @return	string	The name of the controller
	*
	*/
	public function getController()
	{
		if( class_exists($this->_controller ) )
		{
			return $this->_controller;
		}
		else
		{
			//$config = config::getInstance();
			$default = Pixelpost_Config::getInstance()->error_controller.'Controller';
			return $default;
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

	public function getBody()
	{
		return $this->_body;
	}

	public function setBody($body)
	{
		$this->_body = $body;
	}

} // end of class

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
		$this->_uri = uri::getInstance();
		if($this->_uri->fragment(0))
		{
			$this->_controller = $this->_uri->fragment(0).'Controller';
		}
		else
		{
			// get the default controller
			$config = config::getInstance();
			$default = $config->config_values['application']['default_controller'].'Controller';
			$this->_controller = $default;
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
				$config = config::getInstance();
				$default = $config->config_values['application']['default_action'];
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
			$config = config::getInstance();
			$default = $config->config_values['application']['error_controller'].'Controller';
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

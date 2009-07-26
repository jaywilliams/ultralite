<?php

class FrontController
{

	protected $_controller, $_action, $_params, $_body, $_url;

	static $_instance;

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
		/*** set the controller ***/
		$this->_uri = uri::getInstance();
		if($this->_uri->fragment(0))
		{
			$this->_controller = $this->_uri->fragment(0).'Controller';
		}
		else
		{
			/*** get the default controller ***/
			$config = config::getInstance();
			$default = $config->config_values['application']['default_controller'].'Controller';
			$this->_controller = $default;
		}


		/*** the action ***/
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
		/*** check fi the controller exists ***/
		if(class_exists($this->getController()))
		{
			/*** check if controller exists ***/
			$rc = new ReflectionClass($this->getController());

			/*** if the controller exists and implements IController ***/
			if($rc->implementsInterface('IController'))
			{
				$controller = $rc->newInstance();
				/*** check if method exists ***/
				if( $rc->hasMethod( $this->getAction() ) )
				{
					/*** if all is well, load the action ***/
					$method = $rc->getMethod( $this->getAction() );
				}
				else
				{
					/*** load the default action metho ***/
					$config = config::getInstance();
					$default = $config->config_values['application']['default_action'];
					$method = $rc->getMethod( $default );
				}
				$method->invoke( $controller );
			}
			else
			{
				throw new Exception("Interface");
			}
		} 
		else
		{
			throw new Exception("Controller");
		}
	}

	/*
	public function getParams()
	{
		return $this->_params;
	}
	*/

	public function getController()
	{
		if( class_exists( $this->_controller ) )
		{
			return $this->_controller;
		}
		else
		{
			$config = config::getInstance();
			$default = $config->config_values['application']['default_controller'].'Controller';
			return $default;
		}

	}

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

} /*** end of class ***/

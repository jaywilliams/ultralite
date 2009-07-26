<?php

class db{

	/*** Declare instance ***/
	private static $instance = NULL;

	/**
	*
	* the constructor is set to private so
	* so nobody can create a new instance using new
	*
	*/
	private function __construct() {
	  /*** maybe set the db name here later ***/
	}

	/**
	*
	* Return DB instance or create intitial connection
	*
	* @return object (PDO)
	*
	* @access public
	*
	*/
	public static function getInstance()
	{
		if (!self::$instance)
		{
			$config = configuration::getInstance();
			$hostname = $config->config_values['database']['db_hostname'];
			$dbname = $config->config_values['database']['db_name'];
			$db_password = $config->config_values['database']['db_password'];
			$db_username = $config->config_values['database']['db_username'];
			$db_port = $config->config_values['database']['db_port'];

			self::$instance = new PDO("mysql:host=$hostname;port=$db_port;dbname=$dbname", $db_username, $db_password);
			self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$instance;
	}


	/**
	*
	* Like the constructor, we make __clone private
	* so nobody can clone the instance
	*
	*/
	private function __clone()
	{
	}

} /*** end of class ***/

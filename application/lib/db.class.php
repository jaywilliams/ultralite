<?php
/**
 *
 * @Singleton to create database connection
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @version //autogentag//
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @filesource
 * @package Database
 * @Author Kevin Waterson
 *
 */

// namespace web2bb;

class db{

	/**
	 * Holds an insance of self
	 * @var $instance
	 */
	private static $instance = NULL;

	/**
	*
	* the constructor is set to private so
	* so nobody can create a new instance using new
	*
	*/
	private function __construct()
	{
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
			$config = config::getInstance();
			$db_type = $config->config_values['database']['db_type'];
			$hostname = $config->config_values['database']['db_hostname'];
			$dbname = $config->config_values['database']['db_name'];
			$db_password = $config->config_values['database']['db_password'];
			$db_username = $config->config_values['database']['db_username'];
			$db_port = $config->config_values['database']['db_port'];

			self::$instance = new PDO("$db_type:host=$hostname;port=$db_port;dbname=$dbname", $db_username, $db_password);
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

} // end of class

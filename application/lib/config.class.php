<?php

// namespace web2bb;

class config
{
	/*
	* @var string $config_file
	*/
	private static $config_file = '/config/config.ini.php';

	/*
	 * @var array $config_values; 
	 */
	public $config_values = array();

	/*
	* @var object $instance
	*/
	private static $instance = null;

	/**
	 *
	 * Return Config instance or create intitial instance
	 *
	 * @access public
	 *
	 * @return object
	 *
	 */
	public static function getInstance()
	{
 		if(is_null(self::$instance))
 		{
 			self::$instance = new config;
 		}
		return self::$instance;
	}


	/**
	 *
	 * @the constructor is set to private so
	 * @so nobody can create a new instance using new
	 *
	 */
	private function __construct()
	{
		$this->config_values = parse_ini_file(__APP_PATH . self::$config_file, true);

		// check if there is a module config file
		$uri = uri::getInstance();
		$module_conf =  __APP_PATH . '/modules/' . $uri->fragment(0) . '/config/config.ini.php';
		if( file_exists( $module_conf ) )
		{
			$module_array = parse_ini_file( $module_conf, true );
			$this->config_values = array_merge( $this->config_values, $module_array );
		}
	}

	/**
	 * @get a config option by key
	 *
	 * @access public
	 *
	 * @param string $key:The configuration setting key
	 *
	 * @return string
	 *
	 */
	public function getValue($key)
	{
		return self::$config_values[$key];
	}


	/**
	 *
	 * @__clone
	 *
	 * @access private
	 *
	 */
	private function __clone()
	{
	}
}

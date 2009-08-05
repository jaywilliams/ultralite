<?php
/**
 *
 * @Singleton to create uri fragments
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Files
 * @Author Kevin Waterson
 *
 */

// namespace web2bb;

class Web2BB_Uri 
{
	/*
	 * @var array $fragments
	 */
	public static $fragments = array();
	
	/**
	 * @var int $page Current Page, defaults to 1
	 */
	public static $page = 1;

	/*
	* @var object $instance
	*/
	private static $instance = null;

	/**
	 *
	 * Return URI instance
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
 			self::$instance = new Web2BB_Uri;
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
		
		/**
		 * Replace forward-slashes (\) with back-slashes (/),
		 * Remove any double-slashes (//), and any beginning or ending slashes
		 * Remove any potentially harmfull characters from the equasion
		 */
		$uri = clean_filename($_SERVER['QUERY_STRING']);
		
		/**
		 * Check if a page is specified in the URL.
		 * If it is, we can trim that portion from the $uri,
		 * and attach it to the self::$page variable.
		 */
		if( preg_match('/\/page\/(\d+)$/i', $uri, $matches, PREG_OFFSET_CAPTURE) )
		{
			$uri = substr( $uri, 0, $matches[0][1]);
			
			self::$page = ( !empty($matches[1][0]) ) ? (int) $matches[1][0] : self::$page;
		}
		
		/*** put the string into array ***/
		self::$fragments =  explode('/', $uri );
	}

	/**
	 * @get uri fragment 
	 *
	 * @access public
	 *
	 * @param string $key:The uri key
	 *
	 * @return string on success
	 *
	 * @return bool false if key is not found
	 *
	 */
	public static function fragment($key)
	{
		if(array_key_exists($key, self::$fragments))
		{
			return self::$fragments[$key];
		}
		return false;
	}


	/**
	 * Get the current page number
	 * 
	 * Possible ways to get the current page:
	 * 
	 *   Web2BB_Uri::$page;
	 *   $this->_uri->page();
	 *
	 * @param int $page (optional) override the current page number
	 * @return int self::$page current page
	 */
	public static function page(int $page = NULL)
	{
		if(!empty($page))
		{
			self::$page = (int) $page;
		}
		
		return self::$page;
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

} /*** end of class ***/

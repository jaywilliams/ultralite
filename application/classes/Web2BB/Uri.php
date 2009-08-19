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

	/**
	 * @var int $page Total Number of Pages, defaults to 1
	 */
	public static $total_pages = 1;

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
	 * @get number of uri fragments 
	 *
	 * @access public
	 *
	 * @return int on success
	 *
	 */
	public static function numberOfFragments()
	{
		return count(self::$fragments);
	}

	/**
	 * Get the current page number
	 * 
	 * Possible ways to get the current page:
	 * 
	 *   Web2BB_Uri::$page;
	 *   Web2BB_Uri::page();
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
	 * Get or set the total number of pages
	 * 
	 * Possible ways to get the total pages:
	 * 
	 *   Web2BB_Uri::$total_pages;
	 *   Web2BB_Uri::totalPages();
	 *
	 * @param int $total_pages (optional) override the total pages
	 * @return int self::$total_pages total pages
	 */
	public static function totalPages(int $total_pages = NULL)
	{
		if(!empty($total_pages))
		{
			self::$total_pages = (int) $total_pages;
		}
		
		return self::$total_pages;
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

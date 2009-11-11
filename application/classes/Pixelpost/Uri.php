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


class Pixelpost_Uri 
{
	/*
	 * @var array $fragments
	 */
	public static $fragments = array();
	
	/*
	 * @var string $uri
	 */
	public static $uri = '';
	
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
 			self::$instance = new Pixelpost_Uri;
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
		 * Remove any potentially harmful characters from the equation
		 */
		self::$uri = self::clean($_SERVER['QUERY_STRING']);
		
		/**
		 * Check if a page is specified in the URL.
		 * If it is, we can trim that portion from the $uri,
		 * and attach it to the self::$page variable.
		 */
		if( preg_match('/\/page\/(\d+)$/i', self::$uri, $matches, PREG_OFFSET_CAPTURE) )
		{
			self::$uri = substr( self::$uri, 0, $matches[0][1]);
			
			self::$page = ( !empty($matches[1][0]) ) ? (int) $matches[1][0] : self::$page;
		}
		
		/*** put the string into array ***/
		self::$fragments =  explode('/', self::$uri );
	}

	/**
	 * Retrieve URI Fragment
	 * 
	 * Example:
	 *     Pixelpost_Uri::fragment(0); # Returns the first fragment
	 *     Pixelpost_Uri::fragment(-1); # Returns the the last fragment
	 * 
	 * @access public
	 * @param int $key The uri key to retrieve, can be positive or negative.
	 * @return bool|string FALSE if key is not found, the string result if the key is found.
	 */
	public static function fragment($key)
	{
		if($key < 0)
		{
			$key = count(self::$fragments) + (int)$key;
		}
		
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
	 *   Pixelpost_Uri::$page;
	 *   Pixelpost_Uri::page();
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
	 *   Pixelpost_Uri::$total_pages;
	 *   Pixelpost_Uri::totalPages();
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
	 * Get the current uri string
	 * 
	 * Possible ways to get the current page:
	 * 
	 *   Pixelpost_Uri::$uri;
	 *   Pixelpost_Uri::uri();
	 *
	 * @param string $uri (optional) override the current uri string
	 * @return string self::$uri current uri
	 */
	public static function uri(string $uri = NULL)
	{
		if(!empty($uri))
		{
			self::$uri = $uri;
		}
		
		return self::$uri;
	}
	
	
	/**
	 * Clean URI
	 * 
	 * Remove any unsafe characters and return a limited ascii result.
	 * Backslashes are converted to forward slashes, just in case a user mistyped the URI.
	 * Slashes are stripped form the beginning and end of the string as well.
	 * 
	 *   Pixelpost_Uri::clean('/my/uri/string/');
	 *
	 * @param string $uri Unsafe Raw URI
	 * @return string $uri Cleaned URI
	 */
	public static function clean($uri = NULL)
	{
		if(empty($uri))
			return $uri;
		
		$uri = strtolower($uri);
		$uri = str_replace( '\\', '/', $uri);
		$uri = preg_replace('/[^a-z0-9\/\-,_\+!~*\'()]/', '', $uri);
		$uri = trim($uri,'/\\');
		
		return $uri;
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

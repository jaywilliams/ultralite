<?php
/**
 *
 * @Create Breadcrumbs Trail.
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @version //autogentag//
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @filesource
 * @package Breadcrumbs
 * @Author Kevin Waterson
 *
 * @Link http://www.phpro.org/classes/Breadcrumbs.html
 *
 */

// namespace web2bb;

class breadcrumbs{

	/*
	 * @string $breadcrumbs
	 */
	public $breadcrumbs;
	
	/*
	 * @string $pointer
	 */
	private $pointer = '&raquo;';

	/*
	 * @string $url
	 */
	private $url;

	/*
	 * @array $parts
	 */
	private $parts;


	/*
	 * @constructor - duh
	 *
	 * @access public
	 *
	 */
	public function __construct()
	{
		$this->setParts();
		$this->setURL();
		$this->breadcrumbs = '<a href="'.$this->url.'">Home</a>';
	}


	/*
	 *
	 * @set the base url
	 *
	 * @access private
	 *
	 */
	private function setURL()
	{
		$protocol = $_SERVER["SERVER_PROTOCOL"]=='HTTP/1.1' ? 'http' : 'https';
		$this->url = $protocol.'://'.$_SERVER['HTTP_HOST'].'/'.__DOC_ROOT;
	}


	/*
	 * @set the pointer 
	 *
	 * @access public
	 *
	 * @param string $pointer
	 * 
	 */
	public function setPointer($pointer)
	{
		$this->pointer = $pointer;
	}


	/**
	 *
	 * @set the path array
	 *
	 * @access private
	 *
	 * @return array
	 *
	 */
	private function setParts()
	{
		$parts = explode('/', str_replace(__DOC_ROOT, '', $_SERVER['REQUEST_URI']));
		// array_pop($parts);
		array_shift($parts);
		$this->parts = $parts;
	}


	/**
	 *
	 * @create the breadcrumbs
	 *
	 * @access public
	 *
	 */
	public function crumbs()
	{
		foreach($this->parts as $part)
		{
			if( $part != '' )
			{
				$this->url .= "/$part";
				$this->breadcrumbs .= " $this->pointer ".'<a href="'.$this->url.'">'.$part.'</a>';
			}
		}
	}

} /*** end of class ***/

?>

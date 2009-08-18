<?php
/**
 * It works, but it sure isn't pretty.
 * 
 * @todo Clean the this code up!
 */

/**
* @package SPLIB
* @version $Id: FormFilter.php,v 1.1 2003/12/12 08:06:05 kevin Exp $
*/
/**
* FormFilter<br />
* Class for examining HTML tags.<br />
* @access public
* @package SPLIB
*/
class Pixelpost_Filter_Form extends Pixelpost_Filter {
	/**
	* String of allowed tags
	* @access public
	* @var string
	*/
	public $allowedTags = '<a><strong><b><em><i><s><del>';
	
	/**
	 * Force all links to contain the rel="nofollow" attribute.
	 *
	 * @var string
	 */
	// public $nofollow = true;
	
	/**
	* Instance of native XML parser
	* @access private
	* @var resource
	*/
	var $parser;
	/**
	* Used to store the input string
	* @access private
	* @var string
	*/
	var $post = '';
	/**
	* Used to store any XML error string
	* @access private
	* @var string
	*/
	var $error = '';
	/**
	* Constructs FormFilter
	* @access public
	*/
	function __construct() {
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'open', 'close');
		xml_set_character_data_handler($this->parser, 'data');
	}
	/**
	* Constructs FormFilter
	* @param string data to filter
	* @return string filter data
	* @access public
	*/
	public function filter($post) {
		$this->post = '';
		$post = strip_tags($post,$this->allowedTags);
		$post = $this->clean_xss($post);
		var_dump($post);
		$post = '<?xml version="1.0"?><post>'.$post.'</post>';
		if ( !xml_parse($this->parser,$post,true) ) {
			$this->error='Post data is not well formed: '.
				xml_error_string(xml_get_error_code($this->parser)).
					' on line '.xml_get_current_line_number($this->parser);
			return false;
		}
		$this->post = $this->removeEmptyTags($this->post);
		
		return $this->post;
	}
	/**
	* Returns any XML errors
	* @return string XML error
	* @access public
	*/
	public function getError() {
		return $this->error;
	}
	/**
	* Sax Open TagHandler
	* @param XML_HTMLSax
	* @param string tag name
	* @param array attributes
	* @return void
	* @access protected
	*/
	protected function open(& $parser,$tag,$attrs) {
		
		// If we aren't allowing any tags, 
		// don't run through the filter code...
		if (empty($this->allowedTags)) return;
			
		switch ( $tag ) {
			case 'A':
				if (isset($attrs['HREF']) && $this->isValidURL($attrs['HREF'])) {
					$this->post .= "<a href=\"{$attrs['HREF']}\" rel=\"nofollow\">";
				} else {
					$this->post .= '<a>';
				}
			break;
			case 'B':
			case 'STRONG':
				$this->post .= '<strong>';
			break;
			case 'I':
			case 'EM':
				$this->post .= '<em>';
			break;
			case 'S':
			case 'DEL':
				$this->post .= '<del>';
			break;
		}
	}
	/**
	* Sax Close TagHandler
	* @param XML_HTMLSax
	* @param string tag name
	* @param array attributes
	* @return void
	* @access protected
	*/
	protected function close(& $parser,$tag) {
		
		// If we aren't allowing any tags, 
		// don't run through the filter code...
		if (empty($this->allowedTags)) return;
		
		switch ( $tag ) {
			case 'A':
				$this->post .= '</a>';
			break;
			case 'B':
			case 'STRONG':
				$this->post .= '</strong>';
			break;
			case 'I':
			case 'EM':
				$this->post .= '</em>';
			break;
			case 'S':
			case 'DEL':
				$this->post .= '<del>';
			break;
		}
	}
	/**
	* Sax Data Handler
	* @param XML_HTMLSax
	* @param string data inside tag
	* @return void
	* @access protected
	*/
	protected function data(& $parser,$data) {
		$this->post .= $data;
	}
	
	/**
	 * This method was based off of the popoon_classes_externalinput class
	 * Copyright (c) 2001-2008 Liip AG
	 * Licensed under the Apache License, Version 2.0 (the "License");  
	 * Author: Christian Stocker <christian.stocker@liip.ch>     
	 */
	protected function clean_xss($string) {

		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;"),$string);
		// fix &entitiy\n;
		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>#iUu', "$1>", $string);

		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $string);

		//remove any style attributes, IE allows too much stupid things in them, eg.
		//<span style="width: expression(alert('Ping!'));"></span> 
		// and in general you really don't want style declarations in your UGC

		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $string);

		//remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		//remove really unwanted tags

		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$string);
		} while ($oldstring != $string);

		return $string;
	}
   
	
	protected function removeEmptyTags($post)
	{
		// Remove empty tags...
		$post = preg_replace('/<[^\/>]*>([\s]?)*<\/[^>]*>/', '', $post);
		// Remove links that don't point anywhere...
		$post = preg_replace('/<a>([^<]+)<\/a>/', '$1', $post);
		
		return $post;
	}
	
	protected function isValidURL($url) 
	{ 
	 return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url); 
	}
	
}
?>
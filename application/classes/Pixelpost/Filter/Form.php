<?php
/**
* @package SPLIB
* @version $Id: FormFilter.php,v 1.1 2003/12/12 08:06:05 kevin Exp $
*/
/**
* FormFilter<br />
* Class for examining HTML tags.<br />
* Note: requires PEAR::Validate
* @access public
* @package SPLIB
*/
class Pixelpost_Filter_Form extends Pixelpost_Filter {
	/**
	* String of allowed tags
	* @access private
	* @var string
	*/
	var $allowedTags = '<a><b><strong><i><em><u>';
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
		$post = '<?xml version="1.0"?><post>'.$post.'</post>';
		if ( !xml_parse($this->parser,$post,true) ) {
			$this->error='Post data is not well formed: '.
				xml_error_string(xml_get_error_code($this->parser)).
					' on line '.xml_get_current_line_number($this->parser);
			return false;
		}
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
		switch ( $tag ) {
			case 'A':
				if (isset($attrs['HREF']) && Validate::url($attrs['HREF'])) {
					$this->post .= '<a href="'.$attrs['HREF'].
						'" target="_blank">';
				} else {
					$this->post .= '<a href="javascript:;" target="_blank">';
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
}
?>
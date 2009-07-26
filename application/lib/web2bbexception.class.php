<?php
/**
 *
 * @Simple exception class to log exceptions
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Error Handling
 * @Author Kevin Waterson
 *
 */

// namespace web2bb;

class web2bbException extends Exception
{
	/**
	*
	* This function sends the exception data to the logger class
	*
	* @access public
	*
	*/
	public function __construct($message, $code)
	{
		parent::__construct($message, $code);
		logger::exceptionLog( $this->getMessage(), $this->getCode(), $this->getFile(), $this->getLine() );
	}
}

?>

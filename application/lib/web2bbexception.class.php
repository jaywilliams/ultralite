<?php

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

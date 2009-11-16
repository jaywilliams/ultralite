<?php

// namespace web2bb;

class errorController extends Module_Base_Controller implements Model_Interface
{
	/**
	*
	* Constructor, duh
	*
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	*
	* The index function
	*
	* @access	public
	*
	*/
	public function index()
	{
		/*** a view variable ***/
		$this->view->title = '404 File Not Found';
		$this->view->heading = '404 File Not Found';
		@header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	}

}

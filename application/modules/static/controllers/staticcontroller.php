<?php
/**
 * Generic controller for the pages
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 *
 *
 */

// namespace web2bb;

class staticController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		/**
		 * We don't need to do anything here, since we only have to include
		 * a template file. Everything is taken care of by the basecontroller.
		 */
		$this->view->title = 'About';
		
		/**
		 * @todo If we can't find the specified view, return a 404.
		 */
	}
	public function test()
	{
		/**
		 * Sample Sub-Page Action Test
		 */
	}
}

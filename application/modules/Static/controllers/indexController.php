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

class Module_Static_indexController extends Module_Base_baseController implements Model_Interface
{

	public function __construct()
	{
		parent::__construct();
		
		/**
		 * If no title is set, create a generic title based off of the view's name.
		 */
		if (empty($this->view->title))
		{
			$title = ucwords($this->front->getView());
			
			Pixelpost_Plugin::executeFilter('filter_title',$title);
			
			$this->view->title = $title;
		}
		
		
		
	}

	public function indexAction()
	{
		/**
		 * We don't need to do anything here, since we only have to include
		 * a template file. Everything is taken care of by the basecontroller.
		 */
	}
	public function testAction()
	{
		/**
		 * Sample Sub-Page Action Test
		 */
	}
}

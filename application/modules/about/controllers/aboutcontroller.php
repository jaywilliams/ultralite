<?php
/**
 * Controller for the about page
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 *
 *
 */

// namespace web2bb;

class aboutController extends baseController implements IController
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
	}
}

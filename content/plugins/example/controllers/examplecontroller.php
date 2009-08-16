<?php
/**
 * Example Controller
 *
 * @package Pixelpost
 * @author Jay Williams 
 */

class exampleController extends baseController implements IController
{
	public function index()
	{
		$this->view->title = 'Example (Plugin) Controller';
	}
}


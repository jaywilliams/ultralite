<?php
/**
 * Example Controller
 *
 * @package Pixelpost
 * @author Jay Williams 
 */

class commentController extends baseController implements IController
{
	public function index()
	{
		$this->view->title = 'Comment (Plugin) Controller';
	}
}


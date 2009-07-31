<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class postController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		$current_time = Pixelpost_Config::current()->current_time;
		$template = Pixelpost_Config::current()->template;
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir( __THEME_PATH.'/'.$template);

		/*** the include template ***/
		$tpl->include_tpl = __APP_PATH . '/views/post/index.phtml';

		/*** fetch the template ***/
		//$this->content = $tpl->fetch('views/index.phtml', $cache_id);
		$this->content = $tpl->fetch('views/post.phtml', $cache_id);
	}

	public function test()
	{
		$view = new view;
		$view->text = 'this is a test';
		$result = $view->fetch(__APP_PATH . '/views/index.php');
		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}
}

<?php

class blogController implements IController {

	public function index()
	{
		/*** a new view instance ***/
		$view = new view;

		/*** turn caching on for this page ***/
		$view->setCaching(true);

		/*** set the template dir ***/
		$view->setTemplateDir(__SITE_PATH . '/templates/blog');

		/*** a view variable ***/
		$view->title = 'WEB2BB FRAMEWORK';
		$view->heading = 'WEB2BB Example Blob';
		$view->menu = 'This is the blog menu';
		$view->content = 'lots of blog related text here';
		$view->footer = 'This is the footer';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 2 );

		/*** fetch the template ***/
		$result = $view->fetch( 'index.html', $cache_id);

		$fc = FrontController::getInstance();
		$fc->setBody($result);


	}

}

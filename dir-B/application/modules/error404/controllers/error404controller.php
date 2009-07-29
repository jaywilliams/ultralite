<?php

// namespace web2bb;

class error404Controller extends baseController implements IController
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
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(__APP_PATH.'/modules/error404/views');

		/*** a view variable ***/
		$this->view->title = '404 File Not Found';
		$this->view->heading = '404 File Not Found';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( '404/index.php' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

}

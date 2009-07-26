<?php


class blogController extends baseController implements IController
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
	* The index function displays the login form
	*
	*/
	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(__APP_PATH.'/modules/blog/views');

		/*** a view variable ***/
		$this->view->title = 'WEB2BB Blog';
		$this->view->heading = 'WEB2BB Blog';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'blog/index.php' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

}

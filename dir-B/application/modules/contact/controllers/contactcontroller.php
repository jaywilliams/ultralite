<?php

// namespace web2bb;

class contactController extends baseController implements IController
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
		$tpl->setTemplateDir(__APP_PATH.'/modules/contact/views');

		// a new config
		$config = config::getInstance();
		$this->view->version = $config->config_values['application']['version'];

		/*** a view variable ***/
		$this->view->title = 'WEB2BB Blog';
		$this->view->heading = 'WEB2BB Blog';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'contact/index.php' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

}

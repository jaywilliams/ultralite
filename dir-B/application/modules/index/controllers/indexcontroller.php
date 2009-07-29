<?php
/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class indexController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(__APP_PATH . '/modules/index/views');

		/*** the include template ***/
		$tpl->include_tpl = __APP_PATH . '/views/index/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'WEB2BB - Development Made Easy';
		$this->view->heading = 'WEB2BB';

		// a new config
		$config = config::getInstance();
		$this->view->version = $config->config_values['application']['version'];

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'admin/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

	public function test()
	{
		$view = new view;
		$view->text = 'this is a test';
		$result = $view->fetch( __APP_PATH.'/views/index.php' );
		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}
}

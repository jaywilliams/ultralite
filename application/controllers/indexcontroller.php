<?php

class indexController implements IController {

	public function index()
	{
		/*** a new view instance ***/
		$view = new view;

		/*** a debug log entry ***/
		logger::debugLog('this a debug message', 300, __FILE__, __LINE__ );

		/*** this is an error log entry ***/
		logger::errorLog('An error message', 200, __FILE__, __LINE__ );

		/*** this is an audit log entry ***/
		logger::auditLog('An audit log message', 100, __FILE__, __LINE__ ); 

		/*** turn caching on for this page ***/
		$view->setCaching(true);

		/*** set the template dir ***/
		$view->setTemplateDir(__APP_PATH . '/views/index');

		/*** a view variable ***/
		$view->title = 'WEB2BB FRAMEWORK';
		$view->heading = 'WEB2BB Example Site';
		$view->menu = 'This is the sidebar';
		$view->content = 'lots of text here';
		$view->footer = 'This is the footer';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 2 );

		/*** fetch the template ***/
		$result = $view->fetch( 'index.html', $cache_id);

		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}

	public function blah()
	{
		$view = new view;
		$view->name = 'from blah';
		$result = $view->fetch( __APP_PATH.'/views/index.php' );
		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}

}

<?php

class blogController implements IController {

	public function index()
	{
		$view = new View();
		$view->name = "this is the blog";
		$result = $view->render(__APP_PATH.'/views/blog/blog.php');

		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}

}

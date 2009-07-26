<?php

class indexController implements IController {

	public function index()
	{
		$viewRenderer = viewRenderer::getInstance();
		$viewRenderer->name = "Kevin";
		$result = $viewRenderer->render(__APP_PATH.'/views/index.php');

		$fc = FrontController::getInstance();
		$fc->setBody($result);
	}

}

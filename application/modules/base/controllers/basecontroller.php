<?php

namespace web2bb;

class baseController
{
	protected $breadcrumbs, $view, $content=null;

	public function __construct()
	{
		$this->view = new \web2bb\view;

		/*** create the bread crumbs ***/
		$bc = new \web2bb\breadcrumbs;
		// $bc->setPointer('->');
		$bc->crumbs();
		$this->view->breadcrumbs = $bc->breadcrumbs;

	}

	public function __destruct()
	{
		if( !is_null( $this->content ) )
		{
			$this->view->content = $this->content;
			$result = $this->view->fetch( __APP_PATH.'/layouts/index.phtml' );
			$fc = FrontController::getInstance();
			$fc->setBody($result);
		}
	}
}

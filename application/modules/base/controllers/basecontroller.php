<?php

// namespace web2bb;

class baseController
{
	protected $breadcrumbs, $view, $content=null;

	public function __construct()
	{
		$this->view = new view;

		/*** create the bread crumbs ***/
		$bc = new Web2BB_Breadcrumbs;
		// $bc->setPointer('->');
		$bc->crumbs();
		$this->view->Web2BB_Breadcrumbs = $bc->Web2BB_Breadcrumbs;

	}

	public function __destruct()
	{
		if( !is_null( $this->content ) )
		{
			$this->view->content = $this->content;
			$template = Pixelpost_Config::current()->template;
			$result = $this->view->fetch( __THEME_PATH.'/'.$template.'/index.phtml' );
			$fc = FrontController::getInstance();
			$fc->setBody($result);
		}
	}
}

<?php

/**
 * File containing the index controller
 *
 * @package WEB2BB
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

// namespace web2bb;

class postController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$template = Pixelpost_Config::current()->template;
		/*** a new view instance ***/
		$tpl = new view;
		if (file_exists(__THEME_PATH.'/'.$template.'/views/post.phtml'))
		{
			$tpl->setTemplateDir( __THEME_PATH.'/'.$template);
			$this->content = $tpl->fetch('views/post.phtml', $cache_id);
		}
		else
		{
			$tpl->setTemplateDir(__APP_PATH . '/modules/index/views');
			$this->content = $tpl->fetch('index.phtml', $cache_id);
		}
	}
}

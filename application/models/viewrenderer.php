<?php
 
class viewRenderer
{
	public static $viewRendererInstance;
 
	public static function getInstance()
	{
		if( ! (self::$viewRendererInstance instanceof view) )
		{
			$config = config::getInstance();
			self::$viewRendererInstance = new view;
			self::$viewRendererInstance->setTemplateDir(__SITE_PATH.DIRECTORY_SEPARATOR.$config->config_values['template']['template_dir']);
			self::$viewRendererInstance->setCacheDir(__APP_PATH.DIRECTORY_SEPARATOR.$config->config_values['template']['cache_dir']);
			self::$viewRendererInstance->setCacheLifetime($config->config_values['template']['cache_lifetime']);
			self::$viewRendererInstance->setCaching($config->config_values['template']['caching']);
		}
		return self::$viewRendererInstance;
	}
 
} /*** end of class ***/

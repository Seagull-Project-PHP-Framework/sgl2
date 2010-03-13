<?php

class SGL2_Bootstrap extends SGL2_Bootstrap_Abstract
{
	public function __construct()
	{
		$config = new Zend_Config_Ini(PROJECT_PATH.'/var/config.ini', 'staging');
		$registry = SGL2_Registry::createInstance($config);	
		$this->registry = $registry;	
	}				

	public function initEnv()
	{
		define('SGL2_THEME_DIR', PROJECT_PATH.'/www/themes');
		define('SGL2_CACHE_DIR', PROJECT_PATH.'/var/cache');		
		define('SGL2_MOD_DIR', PROJECT_PATH.'/modules');		
	}
	
	public function initRouter()
	{
		$this->registry->set('request', 	new SGL2_Request());
		$this->registry->set('response', 	new SGL2_Response());
		$this->registry->set('router', 		new SGL2_Router());
	}
	
	public function initView()
	{
		
	}	
		
	public function initDb()
	{
		
	}	
	
	public function initConfig()
	{
		
	}
	
	public function initCache()
	{
		
	}
	
	public function initLocale()
	{
		
	}	
}
?>
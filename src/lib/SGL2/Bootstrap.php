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
		
		$config = $this->registry->getConfig();		
        date_default_timezone_set($config->app->timezone);
        error_reporting(E_ALL);			
	}
	
	public function initRouter()
	{
		$this->registry->set('request', 	new SGL2_Request());
		$this->registry->set('response', 	new SGL2_Response());
		$config = $this->registry->getConfig();
		$this->registry->set('router', 		new SGL2_Router($config->routing->adapter, 
			$config->routing->toArray()));
	}	
	
	public function initLogger()
	{
		$config = $this->registry->getConfig();
		$logfile = PROJECT_PATH .'/'.$config->logging->path;
		$logDir = dirname($logfile);
        if (!is_dir($logDir)) {
            $ok = System::mkDir(array('-p', $logDir));
            @chmod($logDir, 0777);
        }	
        if (!is_file($logfile)) {
            $ok = touch($logfile);
        }	
		if (!is_readable($logfile)) {
			throw new Exception(sprintf('Log file not readable at %s', $logfile));
		}
		$writer = new Zend_Log_Writer_Stream($logfile);
		$logger = new Zend_Log($writer);		
		$this->registry->set('logger', 	$logger);
		$this->registry->getEventDispatcher()->addGlobalListener(new SGL2_Plugin_LogMessage());
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
<?php

abstract class SGL2_Bootstrap_Abstract
{		
	protected $registry;		
	
	abstract public function initEnv();	
	
	abstract public function initRouter();

	abstract public function initLogger();
	
	abstract public function initView();

	abstract public function initDb();	

	abstract public function initConfig();
	
	abstract public function initCache();		

	abstract public function initLocale();

}
?>
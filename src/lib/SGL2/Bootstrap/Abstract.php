<?php

abstract class SGL2_Bootstrap_Abstract
{		
	protected $registry;
			
	abstract public function initConfig();
	
	abstract public function initCache();
	
	abstract public function initDb();
	
	abstract public function initEnv();	
	
	abstract public function initLocale();
	
	abstract public function initView();
	
	abstract public function initRouter();
}
?>
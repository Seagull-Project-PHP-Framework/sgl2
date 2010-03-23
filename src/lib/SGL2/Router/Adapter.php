<?php

abstract class SGL2_Router_Adapter
{
	protected $_options           = array();
	protected $_controllers       = array();	
	protected $_routesFile        = null;
	protected $_transOptions      = array();
	protected $_transControllers  = array();
	protected $_transFile         = null;
	protected $_cache             = null;

    public function __construct($options = array())
    {
    	$this->_options = array(
    		'protocol'     => 'http',
    		'domain'       => '',
    		'subDomain'    => '',
    		'scriptName'   => '',
    		'domainPrefix' => '',
    		'routePrefix'  => '',
        );
        
        $this->_options = array_merge($this->_options, $options);
        $this->_options['defaults'] = array_merge(!empty($options['defaults']) ? $options['defaults'] : array(), 
			array('module' => 'default', 'action' => 'index'));
        $this->_options['requirements'] = !empty($options['requirements']) ? $options['requirements'] : array();
        
        if (empty($this->_options['domain'])) {
        	throw new SGL2_Router_Exception('Tried to create a router adapter without specifying a domain');	
        }
        
        if (!empty($this->_options['domainPrefix']) && substr($this->_options['domainPrefix'], -1) != '.') {
        	$this->_options['domainPrefix'] .= '.';
        }
        
        if (!empty($this->_options['routePrefix']) && substr($this->_options['routePrefix'], -1) != '/') {
        	$this->_options['routePrefix'] .= '/';
        }
    }
    
    public function setModuleControllers(array $controllers)
    {
        if (empty($controllers)) {
        	throw new SGL2_Router_Exception('Tried to create a router adapter without specifying valid modules/controllers');	
        }
        
        $this->_controllers = $controllers;
    }

    public function setRoutes($routesFile = null)
    {
        if (!empty($routesFile) && !file_exists($routesFile)) {
        	throw new SGL2_Router_Exception('Tried to create a router adapter with an invalid routes file path');	
        }
        
        $this->_routesFile = $routesFile;
    }    
    
    public function setTranslation($transFile = null, array $transOptions = array())
    {
        if (!empty($transFile) && !file_exists($transFile)) {
        	throw new SGL2_Router_Exception('Tried to create a router adapter with an invalid translation file path');	
        }
        
        if (!empty($transOptions['localePropagation'])) {
			if (empty($transOptions['installedLocales']) || empty($transOptions['defaultLocale'])) {
				throw new SGL2_Router_Exception('Tried to create a router adapter with translation '.
					'without specifying installed locales and/or default locale');	
			}   
		}     
        
        $this->_transFile = $transFile;
        $this->_transOptions = $transOptions;
    }

    public function setCache(SGL2_Cache $cache = null)
    {
    	$this->_cache = $cache;
    }
            	
	abstract public function route($url, $noRedirect = false);
    abstract public function generate($routeName, array $params = array());

    protected function _routePreProcess($url, $protocol, $locale)
    {
		// Routing is always done without protocol, so it's possible
		// to get the route and then redirect, if protocol doesn't match
		$protocol = substr($url, 0, strpos($url, '://'));    
		$url = substr($url, strpos($url, '://') + 3);    	
		    	
    	// Remove script name from url if present
   		$url = str_replace($this->_options['domain'] . '/' . $this->_options['scriptName'], $this->_options['domain'], $url);

    	// When language should be part of the url, the router expects the
    	// url to have the language always present. Even though it's not there,
    	// we add default language and remove it later again    	    	    	    	
    	if (!empty($this->_transOptions['localePropagation']) && in_array($this->_transOptions['localePropagation'], 
				array('prefix', 'subDomain'))) {
    		$localeClass = !empty($this->_transOptions['localeClass']) 
				? new $this->_transOptions['localeClass']() 
				: 'SGL2_Locale';
    		$installedLocales = explode(',', $this->_transOptions['installedLocales']);          	
        	
        	if ($this->_transOptions['localePropagation'] == 'subDomain') {
        		if (preg_match('/(.*\.)?(.*)\.' . preg_quote($this->_options['domain']) . '$/', $aUrl[0], $matches)) {
        			$locale = $this->_routeProcessLocale(
        				!empty($matches[2]) ? $matches[2] : null,
        				$this->_transOptions['defaultLocale'],
        				$installedLocales,
        				$localeClass
        			);
        			
        			if (!empty($matches[2]) && $matches[2] != (string) $locale) {
			        	$url = str_replace($this->_options['domain'], $locale . '.' . $this->_options['domain'], $url);
			        }
        		} else {
        			if (empty($this->_transOptions['localePropagateDefault'])) {
	        			$locale = new $localeClass($this->_transOptions['defaultLocale']);
        			} else {
		    			$locale = $this->_routeProcessLocale(
	        				null,
	        				$this->_transOptions['defaultLocale'],
	        				$installedLocales,
	        				$localeClass
	        			);
	        		}
	        			
        			$url = str_replace($this->_options['domain'], $locale . '.' . $this->_options['domain'], $url);
        		}
        	} else if ($this->_transOptions['localePropagation'] == 'prefix') {
        		if (preg_match('/^(.*\.)?' . preg_quote($this->_options['domain']) . '\/(.*)(\/.*)?$/', $url, $matches)) {
					$locale = $this->_routeProcessLocale(
        				!empty($matches[2]) ? $matches[2] : null,
        				$this->_transOptions['defaultLocale'],
        				$installedLocales,
        				$localeClass
        			);
        			
        			if (!empty($matches[2]) && $matches[2] != (string) $locale) {
			        	$url = str_replace($this->_options['domain'], $this->_options['domain'] . '/' . $locale, $url);
			        }
        		} else {
        			if (empty($this->_transOptions['localePropagateDefault'])) {
	        			$locale = new $localeClass($this->_transOptions['defaultLocale']);
        			} else {
		    			$locale = $this->_routeProcessLocale(
	        				null,
	        				$this->_transOptions['defaultLocale'],
	        				$installedLocales,
	        				$localeClass
	        			);
	        		}
	        			
        			$url = str_replace($this->_options['domain'], $this->_options['domain'] . '/' . $locale, $url);
        		}        			
	        }
        }

        return $url;
    }

    protected function _routeProcessLocale($locale, $defaultLocale, $installedLocales, $localeClass = 'SGL2_Locale')
    {
    	if (empty($locale) || !in_array($locale, $installedLocales)) {
			$locale = new $localeClass();
			if (!in_array((string) $locale, $installedLocales)) {
	    		$language = $locale->getLanguage();
	    		if (in_array($language, $installedLocales)) {
	        		$locale->setLocale($language);
	        	} else {
	        		$locale->setLocale($defaultLocale);
	        	}
	    	}
	    }    
	    
	    return $locale;
    }
    
    protected function _generatePostProcess($url, $locale)
    {
        if (!empty($this->_transOptions['localePropagation']) && empty($this->_transOptions['localePropagateDefault']) 
				&& $locale == $this->_transOptions['defaultLocale']) {
        	// The router always adds language to url, even though the default
        	// language should be hidden. So post process this here and remove
        	// default language again
            if ($this->_transOptions['localePropagation'] == 'subDomain') {	
            	$url = str_replace($locale . '.' . $this->_options['domain'], $this->_options['domain'], $url);
            } else if ($this->_transOptions['localePropagation'] == 'prefix') {	
        		$url = str_replace($this->_options['domain'] . '/' . $locale, $this->_options['domain'], $url);
	        }
    	}

    	if (!empty($this->_options['scriptName']) && substr_count($url, '/') != 0) {
    		// Only add script name, when there is one or more parameters 
   			$url = str_replace($this->_options['domain'], $this->_options['domain'] . '/' . $this->_options['scriptName'], $url);
    	}
    	
    	return $url;	   	
    }
    
	protected function _getRoutePath(array $route, $prependProtocol = false)
	{
		$routePath  = $prependProtocol ? (!empty($route['protocol']) ? $route['protocol'] : $this->_options['protocol']) . '://' : '';
		$routePath .= !empty($route['subDomain']) ? $route['subDomain'] . '.' :  '';		
		$routePath .= $this->_options['domainPrefix'] . $this->_options['domain'];
		$routePath .= '/' . $this->_options['routePrefix'] . $route['route'];
		
		return $routePath;
	}
	
	protected function _getRouteDefaults(array $route, $mergeModuleController = false)
	{
		$aReturn = !empty($route['defaults']) ? $route['defaults'] : array();
		$aReturn = array_merge($this->_options['defaults'], $aReturn);
				
		if ($mergeModuleController) {
			if (!empty($aReturn['controller']) && $aReturn['module'] != $aReturn['controller']) {
				$aReturn['controller'] = $aReturn['module'] . '/' . $aReturn['controller'];
			} else {
				$aReturn['controller'] = $aReturn['module'];
			}
			unset($aReturn['module']);				
		}
		
		return $aReturn;
	}    
	
	protected function _getRouteRequirements(array $route)
	{	
		$aReturn = !empty($route['requirements']) ? $route['requirements'] : array();
		$aReturn = array_merge($this->_options['requirements'], $aReturn);
		
		return $aReturn;
	}
}

?>
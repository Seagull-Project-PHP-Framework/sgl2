<?php

/**
 * A router based on the Horde_Routes library.
 *
 * @see SGL2_Router
 */
class SGL2_Router_Adapter_Horde extends SGL2_Router_Adapter
{
	private $_mapper = null;
	private $_defRoute = null;

    /**
     * @param $url
     * @param bool $noRedirect
     * @return bool
     * @throws SGL2_Router_Exception
     */
    public function route($url, $noRedirect = false)
    {
// use http://www.php.net/manual/en/function.parse-url.php	
		if ($url === false) {
			return false;
		}
    	if (strpos($url, '://') === false) {
    		// The router requires to get the absolute urls for routing
    		throw new SGL2_Router_Exception('Tried to route a url which seems not to be a absolute url with protocol');	
    	}
    	
    	if (substr($url, -1) == '/') {
    		// No final slash for routing
    		$url = substr($url, 0, -1);
    	}
    	
		$orgUrl   = $url;
		$protocol = 'http';
		$locale   = null;
			
		$url = $this->_routePreProcess($url, $protocol, $locale);

    	if (!$this->_mapper) {
    		// Setup routes on first request. This makes
    		// it possible to load routes from cache and/or 
    		// only for the current locale.
    		$this->_setupRoutes($locale);
    	}
    	
        // This hack is necessary to prevent Horde_Routes
        // from killing our slashes in *params
        $url = str_replace("%2F", "%!$", $url);

    	$aHordeMatch = $this->_mapper->routematch('/' . $url);

    	if (!empty($aHordeMatch)) {
    		$aController = explode('/', $aHordeMatch[0]['controller']);
    		$aHordeMatch[0]['module'] = $aController[0];
    		$aHordeMatch[0]['controller'] = !empty($aController[1])
    			? $aController[1]
    			: null;

    		if (empty($aHordeMatch[0]['controller']) && in_array($aHordeMatch[0]['module'], 
					$this->_controllers[$aHordeMatch[0]['module']])) {
    			// If there is a controller with the same name as the module, take it
    			$aHordeMatch[0]['controller'] = $aHordeMatch[0]['module'];
    		}

	        // Resolve *params
	        if (!empty($aHordeMatch[0]['params'])) {
	            $params = $this->_urlParamStringToArray($aHordeMatch[0]['params']);
	            if (isset($params['module'])) unset($params['module']);
	            if (isset($params['controller'])) unset($params['controller']);
	            unset($aHordeMatch[0]['params']);
	            
	            $aHordeMatch[0] = $aHordeMatch[0] + $params;
			}
			
			if ($noRedirect && $protocol != $aHordeMatch[1]->protocol) {
				// If no redirect and protocol doesn't match, route is invalid
				return false;
			} elseif (!$noRedirect) {	
    			// If redirect is enabled, we generate a url based
    			// on the matched route to redirect on demand		
				$verifyUrl = $this->generate($aHordeMatch[1]->sglRouteName, $aHordeMatch[0]);
					
	    		if ($orgUrl != $verifyUrl) {
    				// Redirect with "301 moved permanently"
    				header("Location: " . $verifyUrl, true, 301);
    				exit();
	    		}
	    	}

			return $aHordeMatch[0];    			
    	}

    	return false;
    }
    
    public function generate($routeName, array $params = array())
    {
		if (is_array($routeName)) {
    		$params = $routeName;
    		$routeName = null;
    	}

        if (empty($routeName) && empty($params['module'])) {
			throw new SGL2_Router_Exception('Tried to get a route without specifying route name or module/controller');	
        }

    	if (!empty($this->_transOptions['localePropagation']) && in_array($this->_transOptions['localePropagation'], 
				array('prefix', 'subDomain')) && empty($params['locale'])) {
    		$params['locale'] = $this->_transOptions['defaultLocale'];
    	}

    	if (!$this->_mapper) {
    		// We setup routes on first request. This makes
    		// it possible to load routes from cache and/or 
    		// only for the current language.    		
    		$this->_setupRoutes(!empty($params['locale']) ? $params['locale'] : false);
    	}
    	
    	if (!empty($params['module']) && !empty($params['controller']) && $params['module'] == $params['controller']) {
    	    unset($params['controller']);
		}
        
        if (!empty($params['module'])) {
        	if (!empty($params['controller'])) {
        		$params['controller'] = $params['module'] . '/' . $params['controller'];
        	} else {
        		$params['controller'] = $params['module'];
        	}
        	unset($params['module']);
        }

        $url = $this->_mapper->utils->urlFor($routeName, $params);

		$hasParams = false;
		$protocol = $this->_options['protocol'];
		if (!empty($routeName) && !empty($this->_aMapper->routeNames[$routeName])) {
			$protocol  = $this->_aMapper->routeNames[$routeName]->sglProtocol;
			$hasParams = $this->_aMapper->routeNames[$routeName]->sglParams;
		}
        
        if (!empty($url)) {
        	if (strpos($url, '?') !== false && !$hasParams) {
        		$aUrl = explode('?', str_replace('&amp;', '&', $url));
				parse_str($aUrl[1], $params);

				$url = $aUrl[0] . '/';
				foreach ($params as $k => $v) {
				 	$url .= $k . '/' . $v . '/';
				}
        	}
       
        	$url = substr($url, 1);
        	$url = $this->_generatePostProcess($url, $params['locale']);
        	
        	return $protocol . '://' . $url;      	
        }

        return false;
    }
    
    protected function _setupRoutes($lang = false)
    {
        $this->_mapper = new Horde_Routes_Mapper(array(
            'explicit' => true, // Do not connect to Horde default routes
        ));

		if (!empty($this->_transOptions['localePropagation']) && in_array($this->_transOptions['localePropagation'], 
				array('prefix', 'subDomain'))) {
			if ($this->_transOptions['localePropagation'] == 'subDomain') {
				$this->_options['domainPrefix'] .= ':(locale).';			
			} elseif ($this->_transOptions['localePropagation'] == 'prefix') {
				$this->_options['routePrefix'] .= ':locale/';			
			}
			
			$this->_options['requirements']['locale'] = '(' . str_replace(',', '|', $this->_transOptions['installedLocales']) . ')';
		}
        
    	// To validate controller name per module, we use
    	// controller in format moduleName/controllerName
    	$aControllers = array();
    	foreach ($this->_controllers as $moduleName => $aModuleControllers) {
    		if (is_array($aModuleControllers)) {
    			foreach ($aModuleControllers as $controllerName) {
    				$aControllers[] = $moduleName . '/' . $controllerName;
    			}
    		} else {
    			$aControllers[] = $moduleName . '/' . $aModuleControllers;
    		}
    		$aControllers[] = $moduleName;    		    		
    	}
    	
    	$aRoutes = array();
    	
    	// @todo Add custom routes here...
    	
    	// First translated routes get extracted and get
    	// added as very first routes. Last come untranslated
    	// routes. Within each block, static routes come first.
    	// Dynamic routes get ordered descending by their 
    	// static url part. So: "/testpath/go/:param" comes 
    	// before "/testpath/:param".
    	    	    	    	
		if (empty($this->_options['noDefaultRoute'])) {
			// Default system route comes last
	    	$aRoutes['default'] = array(
	    		'route'      => ':controller/:action/*',
				'protocol'   => $this->_options['protocol'],
				'domain'     => $this->_options['domain'],
				'subDomain'  => $this->_options['subDomain'],
			);
		}
		
		foreach ($aRoutes as $routeName => $aRoute) {
			$hasParams = false;
			if (substr($aRoute['route'], -1) == '*') {
				$aRoute['route'] .= 'params';
				$aRoute['defaults']['params'] = null;
				$hasParams = true;
			}
						
	        $this->_mapper->connect(
	        	$routeName,
	        	$this->_getRoutePath($aRoute),
	        	$this->_getRouteDefaults($aRoute, true),
	        	$this->_getRouteRequirements($aRoute)
	        );	

			$this->_mapper->routeNames[$routeName]->sglParams    = $hasParams;
	        $this->_mapper->routeNames[$routeName]->sglProtocol  = !empty($aRoute['protocol']) 
				? $aRoute['protocol'] : $this->_options['protocol'];
	        $this->_mapper->routeNames[$routeName]->sglRouteName = $routeName;
		}

        $this->_mapper->encoding = false;
        $this->_mapper->appendSlash = false;
        
        $this->_mapper->createRegs($aControllers);    	
    }

    /**
     * Extracts k/v pairs from string.
     *
     * @access  protected
     * @param   string  $params
     * @return  array
     */
    protected function _urlParamStringToArray($params)
    {
        $aRet = array();
        $params = explode('/', $params);
        for ($i = 0, $cnt = count($params); $i < $cnt; $i += 2) {
            // Only for variables with values
            if (isset($params[$i + 1])) {
                // Get back our transformed slashes before decoding the url
                $aRet[urldecode($params[$i])] = urldecode(str_replace("%!$", "%2F", $params[$i + 1]));
            }
        }
        return $aRet;
    }   
}

?>
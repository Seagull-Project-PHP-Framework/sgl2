<?php

/**
 * The router base class to resolve and generate urls.
 *
 * The minmal route consists of a unique name, a route
 * and a default parameter for module, controller and action:
 * 
 * array(
 *	'uniqueRouteName' => array(
 *		'route' => '/route/to/go/',
 *		'defaults' => array(
 *			'module' => 'moduleName',
 *			'controller' => 'controllerName',
 *			'action' => 'actionName',
 *		),
 *	),
 * );
 *	
 * Depending on the adapter, there is support for parameters
 * and requirements, which are defined by regular expressions:
 *
 * array(
 *	'uniqueRouteName' => array(
 *		'route' => '/route/to/go/:year/:month/',
 *		'defaults' => array(
 *			'module' => 'moduleName',
 *			'controller' => 'controllerName',
 *			'action' => 'actionName',
 *			'year' => false, // Make year optional
 *			'month' => false, // Make month optional
 *		),
 *		'requirements' => array(
 *			'year => '\d{2,4}',
 *			'month => '\d{1,2}',
 *		),
 *	),
 * );
 *
 * If it's neccessary to resolve parameters from subdomain,
 * you can specify a subdomain entry per route:
 *
 * array(
 *	'uniqueRouteName' => array(
 *		'route' => '/route/to/go/',
 *  	'subDomain' => :username',
 *		'defaults' => array(
 *			'module' => 'moduleName',
 *			'controller' => 'controllerName',
 *			'action' => 'actionName',
 *		),
 *  	'requirements' => array(
 *			'username' => '^(www)',
 *		),
 *	),
 * );
 *
 * A route can have an unlimited number of parameters
 * at the end of the url. The format is then
 * "/paramName1/paramValue1/...". Just add an *
 * to the end of the route.
 *
 * array(
 *	'uniqueRouteName' => array(
 *		'route' => '/route/to/go/*',
 *		'defaults' => array(
 *			'module' => 'moduleName',
 *			'controller' => 'controllerName',
 *			'action' => 'actionName',
 *		),
 *	),
 * );
 */
class SGL2_Router
{
	const ADAPTER_HORDE = 'Horde';
	
	protected $_adapter = null;

    public function __construct($adapter = self::ADAPTER_HORDE, array $options = array())
    {
    	if (!empty($adapter)) {
        	$this->setAdapter($adapter, $options);
        }
    }

    public function setAdapter($adapter = self::ADAPTER_HORDE, array $options = array())
    {
    	if (is_object($adapter)) {
    		$this->_adapter = $adapter;
    		
	        if (!$this->_adapter instanceof SGL2_Router_Adapter) {
	        	throw new SGL2_Router_Exception('Failed to set router adapter "%adapter" which does '.
					'not extend SGL2_Router_Adapter', array('adapter' => $adapter->toString()));
	        }	    		
    	} else {
	        try {
	        	$adapterClassName = 'SGL2_Router_Adapter_' . ucfirst($adapter);
	        	$this->_adapter = new $adapterClassName($options);
	        } catch (Exception $e) {
	        	if (!$e instanceof SGL2_Router_Exception) {
		            throw new SGL2_Router_Exception('Failed to create router adapter "%adapter"', 
						array('adapter' => $adapter));
		        } else {
		        	throw $e;	
		        }
	        }
	        if (!$this->_adapter instanceof SGL2_Router_Adapter) {
	        	throw new SGL2_Router_Exception('Failed to set router adapter "%adapter" which '.
					'does not extend SGL2_Router_Adapter', array('adapter' => $adapter));
	        }	        
	    }
    }
    
    public function getAdapter()
    {
        return $this->_adapter;
    }
            
    public function __call($method, array $options)
    {	
    	if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }                
        throw new SGL2_Router_Exception('Failed to call unknown router adapter method "%method%"', 
			array('method' => $method));
    }
}
?>

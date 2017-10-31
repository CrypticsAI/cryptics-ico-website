<?php
namespace WpPepVN\Mvc;

use WpPepVN\Text
	,WpPepVN\DependencyInjection
;

class Router
{
	public $di = false;
	
	protected $_fileExtention 	= '.php';
	
	protected $_defaultController			= 'Index';
	protected $_errorController			= 'Error';
	protected $_suffixController			= 'Controller';
	
	protected $_defaultAction			= 'index';
	protected $_suffixAction			= 'Action';
	
	protected $_uri 				= '';
	
	protected $_requestMethod 			= '';
    
    protected $_get_uri_key 		= '_wp_pepvn_uri';
    
    protected $_controllerDir = '';
    
    protected $_activeControllerInstance = null;
	
	protected $_activeControllerName = null;
	protected $_activeActionName = null;
	protected $_activeParams = array();
	
	protected $_namespace = '';	//WPOptimizeByxTraffic\Application\Module\Backend\Controller
	
	protected $_returnedValue = null;
	
	/**
	 * Sets DI
	 */
	public function setDI(DependencyInjection $di)
	{
		$this->di = $di;
	}
	/**
	 * Sets the default controller suffix
	 */
	public function setSuffixController($suffixController)
	{
		$this->_suffixController = $suffixController;
	}
	
	/**
	 * Sets the default action suffix
	 */
	public function setSuffixAction($suffixAction)
	{
		$this->_suffixAction = $suffixAction;
	}

	/**
	 * Sets the default controller name
	 */
	public function setDefaultController($controllerName)
	{
		$this->_defaultController = $controllerName;
	}

	/**
	 * Sets the controller name to be dispatched
	 */
	public function setControllerName($controllerName)
	{
		$this->_activeControllerName = $controllerName;
	}

	/**
	 * Gets last controller name
	 */
	public function getControllerName()
	{
		return $this->_activeControllerName;
	}
	
	/**
	 * Gets last action name
	 */
	public function getActionName()
	{
		return $this->_activeActionName;
	}
	
	/**
	 * Gets last params
	 */
	public function getParams()
	{
		return $this->_activeParams;
	}
	
	/**
	 * Returns the active controller instance
	 */
	public function getActiveControllerInstance()
	{
		return $this->_activeControllerInstance;
	}
	
	/**
	 * Sets the controller dir
	 */
	public function setControllerDir($controllerDir)
	{
		$this->_controllerDir = $controllerDir;
	}
	
	
	/**
	 * Sets the controller dir
	 */
	public function setNamespace($namespace)
	{
		$this->_namespace = $namespace;
	}
	
	public function handle($uri = null) 
    {
        if(null === $uri) {
            $uri 			= isset($_GET[$this->_get_uri_key]) ? $_GET[$this->_get_uri_key] : null;
        }
		
		if(null !== $uri) {
			$uri 			= rtrim($uri, '/');
			$uri 			= filter_var($uri, FILTER_SANITIZE_URL);
			$this->_uri 		= explode('/', $uri);
			$this->_requestMethod 	= isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
			$this->_requestMethod = strtoupper($this->_requestMethod);
			
			if(isset($this->_uri[0]) && !empty($this->_uri[0])) {
				$this->_activeControllerName = $this->_uri[0];
			} else {
				$this->_activeControllerName = $this->_defaultController;
			}
			
			$this->_activeControllerName = Text::camelize($this->_activeControllerName);
			
			if(isset($this->_uri[1]) && !empty($this->_uri[1])) {
				$this->_activeActionName = $this->_uri[1];
			} else {
				$this->_activeActionName = $this->_defaultAction;
			}
			
			$this->_activeActionName = Text::camelize($this->_activeActionName);
			
			if(isset($this->_uri[2])) {
				$this->_activeParams = $this->_uri;
				unset($this->_activeParams[0]);
				unset($this->_activeParams[1]);
				$this->_activeParams = array_values($this->_activeParams);
			}
			
			$this->run();
		}
	}

	public function run() 
    {
		
		$controllerName = $this->_activeControllerName . $this->_suffixController;
		
		$actionName = $this->_activeActionName . $this->_suffixAction;
		
		$controllerFilePath = rtrim($this->_controllerDir,DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $controllerName . $this->_fileExtention;
		
		if (file_exists($controllerFilePath) && is_file($controllerFilePath)) {
			include_once($controllerFilePath);
			$activeControllerInstanceName = rtrim($this->_namespace,'\\').'\\'.$controllerName;
			
			$this->_activeControllerInstance = new $activeControllerInstanceName();
			$this->_activeControllerInstance->setDI($this->di);
			$this->_activeControllerInstance->init($this->di);
			
			if(is_object($this->_activeControllerInstance)) {
				if (method_exists($this->_activeControllerInstance, $actionName)) {
					$this->_returnedValue = call_user_func_array(array($this->_activeControllerInstance, $actionName), $this->_activeParams);
				}
			}
		}
		
		//exit();
	}
	
	
	/**
	 * Sets the latest returned value by an action manually
	 *
	 * @param mixed value
	 */
	public function setReturnedValue($value)
	{
		$this->_returnedValue = $value;
	}

	/**
	 * Returns value returned by the lastest dispatched action
	 *
	 * @return mixed
	 */
	public function getReturnedValue()
	{
		return $this->_returnedValue;
	}

    
	public function error() 
    {
        //$path = $this->_controllerDir.'/'.ucfirst($controller).'Controller'.$this->_fileExtention;
        
		//require CONTROLLERS.'/'.self::$error.self::$fileExtention;
		//self::$CONTROLLER = new \Error;
		//self::$CONTROLLER->index();
		exit;
	}

}
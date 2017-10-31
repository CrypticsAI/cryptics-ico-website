<?php 
namespace WpPepVN\Mvc\View;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Mvc\ViewBaseInterface
	,WpPepVN\Exception
	,WpPepVN\Cache\BackendInterface
	,WpPepVN\Mvc\View\Engine\Php as PhpEngine
;

/**
 * WpPepVN\Mvc\View\Simple
 *
 * This component allows to render views without hierarchical levels
 *
 *<code>
 * $view = new \WpPepVN\Mvc\View\Simple();
 * echo $view->render('templates/my-view', array('content' => $html));
 * //or with filename with extension
 * echo $view->render('templates/my-view.volt', array('content' => $html));
 *</code>
 */
class Simple extends Injectable implements ViewBaseInterface
{

	protected $_options;

	protected $_viewsDir;

	protected $_partialsDir;

	protected $_viewParams;

	protected $_engines = false;

	protected $_registeredEngines;

	protected $_activeRenderPath;

	protected $_content;

	protected $_cache = false;

	protected $_cacheOptions;

	/**
	 * WpPepVN\Mvc\View\Simple constructor
	 *
	 * @param array options
	 */
	public function __construct($options = null)
	{
		if(is_array($options)) {
			$this->_options = $options;
		}
	}

	/**
	 * Sets views directory. Depending of your platform, always add a trailing slash or backslash
	 */
	public function setViewsDir($viewsDir)
	{
		$this->_viewsDir = $viewsDir;
	}

	/**
	 * Gets views directory
	 */
	public function getViewsDir()
	{
		return $this->_viewsDir;
	}

	/**
	 * Register templating engines
	 *
	 *<code>
	 *$this->view->registerEngines(array(
	 *  ".phtml" => "WpPepVN\Mvc\View\Engine\Php",
	 *  ".volt" => "WpPepVN\Mvc\View\Engine\Volt",
	 *  ".mhtml" => "MyCustomEngine"
	 *));
	 *</code>
	 */
	public function registerEngines($engines)
	{
		$this->_registeredEngines = $engines;
	}

	/**
	 * Loads registered template engines, if none is registered it will use WpPepVN\Mvc\View\Engine\Php
	 *
	 * @return array
	 */
	protected function _loadTemplateEngines()
	{
		
		/**
		 * If the engines aren't initialized 'engines' is false
		 */
		$engines = $this->_engines;
		if ($engines === false) {

			$dependencyInjector = $this->_dependencyInjector;

			$engines = array();

			$registeredEngines = $this->_registeredEngines;
			if(!is_array($registeredEngines)) {

				/**
				 * We use WpPepVN\Mvc\View\Engine\Php as default
				 * Use .phtml as extension for the PHP engine
				 */
				$engines['.phtml'] = new PhpEngine($this, $dependencyInjector);

			} else {

				if(!is_object($dependencyInjector)) {
					throw new Exception("A dependency injector container is required to obtain the application services");
				}

				/**
				 * Arguments for instantiated engines
				 */
				$arguments = array($this, $dependencyInjector);

				foreach($registeredEngines as $extension => $engineService) {

					if(is_object($engineService)) {
						/**
						 * Engine can be a closure
						 */
						if ($engineService instanceof \Closure) {
							$engineObject = call_user_func_array($engineService, $arguments);
						} else {
							$engineObject = $engineService;
						}
					} else {
						/**
						 * Engine can be a string representing a service in the DI
						 */
						if(is_string($engineService)) {
							$engineObject = $dependencyInjector->getShared($engineService, $arguments);
						} else {
							throw new Exception("Invalid template engine registration for extension: " . $extension);
						}
					}

					$engines[$extension] = $engineObject;
				}
			}

			$this->_engines = $engines;
		} else {
			$engines = $this->_engines;
		}

		return $engines;
	}

	/**
	 * Tries to render the view with every engine registered in the component
	 *
	 * @param string path
	 * @param array  params
	 */
	protected final function _internalRender($path, $params)
	{
		$eventsManager = $this->_eventsManager;

		if(is_object($eventsManager)) {
			$this->_activeRenderPath = $path;
		}

		/**
		 * Call beforeRender if there is an events manager
		 */
		if(is_object($eventsManager)) {
			if ($eventsManager->fire("view:beforeRender", $this) === false) {
				return null;
			}
		}

		$notExists = true; $mustClean = true;

		$viewsDirPath =  $this->_viewsDir . $path;

		/**
		 * Load the template engines
		 */
		$engines = $this->_loadTemplateEngines();

		/**
		 * Views are rendered in each engine
		 */
		foreach($engines as $extension => $engine) {

			if (file_exists($viewsDirPath . $extension)) {
				$viewEnginePath = $viewsDirPath . $extension;
			} else {

				/**
				 * if passed filename with engine extension
				 */
				if ($extension && (substr($viewsDirPath, -strlen($extension)) == $extension) && file_exists(viewsDirPath)) {
					$viewEnginePath = $viewsDirPath;
				} else {
					$viewEnginePath = "";
				}
			}

			if ($viewEnginePath) {

				/**
				 * Call beforeRenderView if there is a events manager available
				 */
				if(is_object($eventsManager)) {
					if ($eventsManager->fire("view:beforeRenderView", $this, $viewEnginePath) === false) {
						continue;
					}
				}

				$engine->render($viewEnginePath, $params, $mustClean);

				/**
				 * Call afterRenderView if there is a events manager available
				 */
				$notExists = false;
				if(is_object($eventsManager)) {
					$eventsManager->fire("view:afterRenderView", $this);
				}
				break;
			}
		}

		/**
		 * Always throw an exception if the view does not exist
		 */
		if ($notExists === true) {
			throw new Exception("View '" . $viewsDirPath . "' was not found in the views directory");
		}

		/**
		 * Call afterRender event
		 */
		if(is_object($eventsManager)) {
			$eventsManager->fire("view:afterRender", $this);
		}

	}

	/**
	 * Renders a view
	 *
	 * @param  string path
	 * @param  array  params
	 * @return string
	 */
	public function render($path, $params = null)
	{
		
		/**
		 * Create/Get a cache
		 */
		$cache = $this->getCache();
		
		if(is_object($cache)) {

			/**
			 * Check if the cache is started, the first time a cache is started we start the cache
			 */
			if ($cache->isStarted() === false) {

				$key = null; $lifetime = null;

				/**
				 * Check if the user has defined a different options to the default
				 */
				$cacheOptions = $this->_cacheOptions;
				if(is_array($cacheOptions)) {
					$key = $cacheOptions["key"];
					$lifetime = $cacheOptions["lifetime"];
				}

				/**
				 * If a cache key is not set we create one using a md5
				 */
				if ($key === null) {
					$key = md5($path);
				}

				/**
				 * We start the cache using the key set
				 */
				$content = $cache->start($key, $lifetime);
				if ($content !== null) {
					$this->_content = $content;
					return $content;
				}
			}

		}

		ob_start();

		$viewParams = $this->_viewParams;

		/**
		 * Merge parameters
		 */
		if(is_array($params)) {
			if(is_array($viewParams)) {
				$mergedParams = array_merge($viewParams, $params);
			} else {
				$mergedParams = $params;
			}
		} else {
			$mergedParams = $viewParams;
		}

		/**
		 * internalRender is also reused by partials
		 */
		$this->_internalRender($path, $mergedParams);

		/**
		 * Store the data in output into the cache
		 */
		if(is_object($cache)) {
			if ($cache->isStarted() === true) {
				if ($cache->isFresh() === true) {
					$cache->save();
				} else {
					$cache->stop();
				}
			} else {
				$cache->stop();
			}
		}

		ob_end_clean();

		return $this->_content;
	}

	/**
	 * Renders a partial view
	 *
	 * <code>
	 * 	//Show a partial inside another view
	 * 	$this->partial('shared/footer');
	 * </code>
	 *
	 * <code>
	 * 	//Show a partial inside another view with parameters
	 * 	$this->partial('shared/footer', array('content' => $html));
	 * </code>
	 *
	 * @param string partialPath
	 * @param array  params
	 */
	public function partial($partialPath, $params = null)
	{
		
		/**
		 * Start output buffering
		 */
		ob_start();

		/**
		 * If the developer pass an array of variables we create a new virtual symbol table
		 */
		if(is_array($params)) {

			$viewParams = $this->_viewParams;

			/**
			 * Merge or assign the new params as parameters
			 */
			if(is_array($viewParams)) {
				$mergedParams = array_merge($viewParams, $params);
			} else {
				$mergedParams = $params;
			}

		} else {
			$mergedParams = $params;
		}

		/**
		 * Call engine render,$this checks in every registered engine for the partial
		 */
		$this->_internalRender($partialPath, $mergedParams);

		/**
		 * Now we need to restore the original view parameters
		 */
		if(is_array($params)) {
			/**
			 * Restore the original view params
			 */
			$this->_viewParams = $viewParams;
		}

		ob_end_clean();

		/**
		 * Content is output to the parent view
		 */
		echo $this->_content;
	}

	/**
	 * Sets the cache options
	 *
	 * @param  array options
	 * @return WpPepVN\Mvc\View\Simple
	 */
	public function setCacheOptions($options)
	{
		$this->_cacheOptions = $options;
		return $this;
	}

	/**
	 * Returns the cache options
	 *
	 * @return array
	 */
	public function getCacheOptions()
	{
		return $this->_cacheOptions;
	}

	/**
	 * Create a WpPepVN\Cache based on the internal cache options
	 */
	protected function _createCache()
	{
		$dependencyInjector = $this->_dependencyInjector;
		if(is_object($dependencyInjector)) {
			throw new Exception("A dependency injector container is required to obtain the view cache services");
		}

		$cacheService = "viewCache";

		$cacheOptions =$this->_cacheOptions;
		if(is_array($cacheOptions)) {
			if (isset ($cacheOptions["service"])) {
				$cacheService = $cacheOptions["service"];
			}
		}

		/**
		 * The injected service must be an object
		 */
		$viewCache = $dependencyInjector->getShared($cacheService);
		if(!is_object($viewCache)) {
			throw new Exception("The injected caching service is invalid");
		}

		return $viewCache;
	}

	/**
	 * Returns the cache instance used to cache
	 */
	public function getCache()
	{
		$cache = $this->_cache;
		if ($cache) {
			if(!is_object($cache)) {
				$cache = $this->_createCache(); $this->_cache = $cache;
			}
		}
		return $cache;
	}

	/**
	 * Cache the actual view render to certain level
	 *
	 *<code>
	 *  $this->view->cache(array('key' => 'my-key', 'lifetime' => 86400));
	 *</code>
	 */
	public function cache($options = true)
	{
		if(is_array($options)) {
			$this->_cache = true; $this->_cacheOptions = $options;
		} else {
			if ($options) {
				$this->_cache = true;
			} else {
				$this->_cache = false;
			}
		}
		return $this;
	}

	/**
	 * Adds parameters to views (alias of setVar)
	 *
	 *<code>
	 *	$this->view->setParamToView('products', $products);
	 *</code>
	 */
	public function setParamToView($key, $value)
	{
		$this->_viewParams[$key] = $value;
		return $this;
	}

	/**
	 * Set all the render params
	 *
	 *<code>
	 *	$this->view->setVars(array('products' => $products));
	 *</code>
	 */
	public function setVars($params, $merge = true)
	{
		
		if ($merge) {
			$viewParams = $this->_viewParams;
			if(is_array($viewParams)) {
				$mergedParams = array_merge($viewParams, $params);
			} else {
				$mergedParams = $params;
			}
			$this->_viewParams = $mergedParams;
		} else {
			$this->_viewParams = $params;
		}

		return $this;
	}

	/**
	 * Set a single view parameter
	 *
	 *<code>
	 *	$this->view->setVar('products', $products);
	 *</code>
	 */
	public function setVar($key, $value) 
	{
		$this->_viewParams[$key] = $value;
		return $this;
	}

	/**
	 * Returns a parameter previously set in the view
	 *
	 * @param string key
	 * @return mixed
	 */
	public function getVar($key)
	{
		if(isset($this->_viewParams[$key])) {
			return $this->_viewParams[$key];
		}
		
		return null;
	}

	/**
	 * Returns parameters to views
	 *
	 * @return array
	 */
	public function getParamsToView()
	{
		return $this->_viewParams;
	}

	/**
	 * Externally sets the view content
	 *
	 *<code>
	 *	$this->view->setContent("<h1>hello</h1>");
	 *</code>
	 */
	public function setContent($content)
	{
		$this->_content = $content;
		return $this;
	}

	/**
	 * Returns cached output from another view stage
	 */
	public function getContent()
	{
		return $this->_content;
	}

	/**
	 * Returns the path of the view that is currently rendered
	 *
	 * @return string
	 */
	public function getActiveRenderPath()
	{
		return $this->_activeRenderPath;
	}

	/**
	 * Magic method to pass variables to the views
	 *
	 *<code>
	 *	$this->view->products = $products;
	 *</code>
	 */
	public function __set($key, $value)
	{
		$this->_viewParams[$key] = $value;
	}

	/**
	 * Magic method to retrieve a variable passed to the view
	 *
	 *<code>
	 *	echo $this->view->products;
	 *</code>
	 *
	 * @param string key
	 * @return mixed
	 */
	public function __get($key)
	{
		if(isset($this->_viewParams[$key])) {
			return $this->_viewParams[$key];
		}
		return null;
	}
}

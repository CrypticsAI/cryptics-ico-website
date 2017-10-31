<?php 
namespace WpPepVN\Mvc;

use WpPepVN\DependencyInjection;
use WpPepVN\DependencyInjection\Injectable;
use WpPepVN\Mvc\ViewInterface;
use WpPepVN\Cache\BackendInterface;
use WpPepVN\Mvc\View\Engine\Php as PhpEngine;

/**
 * WpPepVN\Mvc\View
 *
 * WpPepVN\Mvc\View is a class for working with the "view" portion of the model-view-controller pattern.
 * That is, it exists to help keep the view script separate from the model and controller scripts.
 * It provides a system of helpers, output filters, and variable escaping.
 *
 * <code>
 * //Setting views directory
 * $view = new \WpPepVN\Mvc\View();
 * $view->setViewsDir('app/views/');
 *
 * $view->start();
 * //Shows recent posts view (app/views/posts/recent.phtml)
 * $view->render('posts', 'recent');
 * $view->finish();
 *
 * //Printing views output
 * echo $view->getContent();
 * </code>
 */
class View extends Injectable implements ViewInterface
{

	/**
	 * Render Level: To the main layout
	 *
	 */
	const LEVEL_MAIN_LAYOUT = 5;

	/**
	 * Render Level: Render to the templates "after"
	 *
	 */
	const LEVEL_AFTER_TEMPLATE = 4;

	/**
	 * Render Level: Hasta el layout del controlador
	 *
	 */
	const LEVEL_LAYOUT = 3;

	/**
	 * Render Level: To the templates "before"
	 *
	 */
	const LEVEL_BEFORE_TEMPLATE = 2;

	/**
	 * Render Level: To the action view
	 */
	const LEVEL_ACTION_VIEW = 1;

	/**
	 * Render Level: No render any view
	 *
	 */
	const LEVEL_NO_RENDER = 0;
	
	/**
	 * Cache Mode
	 *
	 */
	const CACHE_MODE_NONE = 0;
	const CACHE_MODE_INVERSE = 1;

	protected $_options;

	protected $_basePath = "";
	
	protected $_basePaths = array();

	protected $_content = "";

	protected $_renderLevel = 5;

	protected $_currentRenderLevel = 0;

	protected $_disabledLevels;

	protected $_viewParams;

	protected $_layout;

	protected $_layoutsDir = 'layouts/';

	protected $_partialsDir = 'partials/';

	protected $_viewsDir = 'views/';

	protected $_templatesBefore;

	protected $_templatesAfter;

	protected $_engines = false;

	protected $_registeredEngines;

	protected $_mainView = 'index';

	protected $_controllerName;

	protected $_actionName;

	protected $_params;

	protected $_pickView;
	
	protected $_cache;

	protected $_cacheLevel = 0;

	protected $_activeRenderPath;

	protected $_disabled = false;

	/**
	 * WpPepVN\Mvc\View constructor
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
	 * Sets the views directory. Depending of your platform, always add a trailing slash or backslash
	 */
	public function setViewsDir($viewsDir)
	{
		if (substr($viewsDir, -1) !== DIRECTORY_SEPARATOR) {
			$viewsDir = $viewsDir . DIRECTORY_SEPARATOR;
		}
        
		$this->_viewsDir = $viewsDir;
        
		return $this;
	}

	/**
	 * Gets views directory
	 */
	public function getViewsDir()
	{
		return $this->_viewsDir;
	}

	/**
	 * Sets the layouts sub-directory. Must be a directory under the views directory. Depending of your platform, always add a trailing slash or backslash
	 *
	 *<code>
	 * $view->setLayoutsDir('../common/layouts/');
	 *</code>
	 */
	public function setLayoutsDir($layoutsDir)
	{
		$this->_layoutsDir = $layoutsDir;
		return $this;
	}

	/**
	 * Gets the current layouts sub-directory
	 */
	public function getLayoutsDir()
	{
		return $this->_layoutsDir;
	}

	/**
	 * Sets a partials sub-directory. Must be a directory under the views directory. Depending of your platform, always add a trailing slash or backslash
	 *
	 *<code>
	 * $view->setPartialsDir('../common/partials/');
	 *</code>
	 */
	public function setPartialsDir($partialsDir)
	{
		$this->_partialsDir = $partialsDir;
		return $this;
	}

	/**
	 * Gets the current partials sub-directory
	 */
	public function getPartialsDir()
	{
		return $this->_partialsDir;
	}

	/**
	 * Sets base path. Depending of your platform, always add a trailing slash or backslash
	 *
	 * <code>
	 * 	$view->setBasePath(__DIR__ . '/');
	 * </code>
	 */
	public function setBasePath($basePath)
	{
		$this->_basePath = $basePath;
		$this->_basePaths[] = $basePath;
		$this->_basePaths = array_unique($this->_basePaths);
		return $this;
	}

	/**
	 * Sets the render level for the view
	 *
	 * <code>
	 * 	//Render the view related to the controller only
	 * 	$this->view->setRenderLevel(View::LEVEL_VIEW);
	 * </code>
	 */
	public function setRenderLevel($level)
	{
		$this->_renderLevel = $level;
		return $this;
	}

	/**
	 * Disables a specific level of rendering
	 *
	 *<code>
	 * //Render all levels except ACTION level
	 * $this->view->disableLevel(View::LEVEL_ACTION_VIEW);
	 *</code>
	 *
	 * @param int|array level
	 * @return Phalcon\Mvc\View
	 */
	public function disableLevel($level)
	{
        if(is_array($level)) {
			$this->_disabledLevels = $level;
		} else {
			$this->_disabledLevels[$level] = true;
		}
		return $this;
	}

	/**
	 * Sets default view name. Must be a file without extension in the views directory
	 *
	 * <code>
	 * 	//Renders as main view views-dir/base.phtml
	 * 	$this->view->setMainView('base');
	 * </code>
	 */
	public function setMainView($viewPath)
	{
		$this->_mainView = $viewPath;
		return $this;
	}

	/**
	 * Returns the name of the main view
	 */
	public function getMainView()
	{
		return $this->_mainView;
	}

	/**
	 * Change the layout to be used instead of using the name of the latest controller name
	 *
	 * <code>
	 * 	$this->view->setLayout('main');
	 * </code>
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		return $this;
	}

	/**
	 * Returns the name of the main view
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Sets a template before the controller layout
	 *
	 * @param string|array templateBefore
	 * @return Phalcon\Mvc\View
	 */
	public function setTemplateBefore($templateBefore)
	{
        if(!is_array($templateBefore)) {
			$this->_templatesBefore = array($templateBefore);
		} else {
			$this->_templatesBefore = $templateBefore;
		}
		return $this;
	}

	/**
	 * Resets any "template before" layouts
	 */
	public function cleanTemplateBefore()
	{
		$this->_templatesBefore = null;
		return $this;
	}

	/**
	 * Sets a "template after" controller layout
	 *
	 * @param string|array templateAfter
	 * @return Phalcon\Mvc\View
	 */
	public function setTemplateAfter($templateAfter)
	{
		
        if(!is_array($templateAfter)) {
            $this->_templatesAfter = array($templateAfter);
		} else {
			$this->_templatesAfter = $templateAfter;
		}
		return $this;
	}

	/**
	 * Resets any template before layouts
	 */
	public function cleanTemplateAfter()
	{
		$this->_templatesAfter = null;
		return $this;
	}

	/**
	 * Adds parameters to views (alias of setVar)
	 *
	 *<code>
	 *	$this->view->setParamToView('products', $products);
	 *</code>
	 *
	 * @param string key
	 * @param mixed value
	 * @return Phalcon\Mvc\View
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
	 *
	 * @param array params
	 * @param boolean merge
	 * @return Phalcon\Mvc\View
	 */
	public function setVars($params, $merge = true)
	{
		if ($merge) {
            if(is_array($this->_viewParams)) {
				$this->_viewParams = array_merge($this->_viewParams, $params);
			} else {
				$this->_viewParams = $params;
			}
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
	 *
	 * @param string key
	 * @param mixed value
	 * @return Phalcon\Mvc\View
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
	 * Gets the name of the controller rendered
	 *
	 * @return string
	 */
	public function getControllerName()
	{
		return $this->_controllerName;
	}

	/**
	 * Gets the name of the action rendered
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return $this->_actionName;
	}

	/**
	 * Gets extra parameters of the action rendered
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->_params;
	}

	/**
	 * Starts rendering process enabling the output buffering
	 */
	public function start()
	{
		ob_start();
		$this->_content = null;
		return $this;
	}

	/**
	 * Loads registered template engines, if none is registered it will use Phalcon\Mvc\View\Engine\Php
	 */
	protected function _loadTemplateEngines()
	{
		/**
		 * If the engines aren't initialized 'engines' is false
		 */
		
		if ($this->_engines === false) {
			
			$engines = array();
			
            if(!is_array($this->_registeredEngines)) {

				/**
				 * We use WpPepVN\Mvc\View\Engine\Php as default
				 */
				$engines['.php'] = new PhpEngine($this, $this->_dependencyInjector);
			} else {
                if(!is_object($this->_dependencyInjector)) {
					throw new \Exception("A dependency injector container is required to obtain the application services");
				}
                
				$arguments = array($this, $this->_dependencyInjector);
                
                foreach($this->_registeredEngines as $extension => $engineService) {
                    if(is_object($engineService)) {

						/**
						 * Engine can be a closure
						 */
						if ($engineService instanceof \Closure) {
							$engines[$extension] = call_user_func_array($engineService, $arguments);
						} else {
							$engines[$extension] = $engineService;
						}

					} else {

						/**
						 * Engine can be a string representing a service in the DI
						 */
                        if(!is_string($engineService)) {
							throw new \Exception("Invalid template engine registration for extension: " . $extension);
						}
						
						$engines[$extension] = $this->_dependencyInjector->getShared($engineService, $arguments);
					}
				}
			}

			$this->_engines = $engines;
		}

		return $this->_engines;
	}
    
	/**
	 * Checks whether view exists on registered extensions and render it
	 *
	 * @param array engines
	 * @param string viewPath
	 * @param boolean silence
	 * @param boolean mustClean
	 * @param Phalcon\Cache\BackendInterface $cache
	 */
	protected function _engineRender($engines, $viewPath, $silence, $mustClean, BackendInterface $cache = null)
	{
		$notExists = true;
        $viewsDir = $this->_viewsDir;
        $basePath = $this->_basePath;
        
		$viewPath = explode('/',$viewPath);
		foreach($viewPath as $key => $value) {
			$viewPath[$key] = lcfirst($value);
		}
		$viewPath = implode('/',$viewPath);
		
		$viewsDirPath = $basePath . $viewsDir . $viewPath;
        
		if(is_object($cache)) {
			$renderLevel = (int) $this->_renderLevel;
			$cacheLevel = (int) $this->_cacheLevel;

			if($renderLevel >= $cacheLevel) {

				/**
				 * Check if the cache is started, the first time a cache is started we start the
				 * cache
				 */
				if ($cache->isStarted() == false) {

					$key = null; $lifetime = null;

					$viewOptions = $this->_options;
					
					/**
					 * Check if the user has defined a different options to the default
					 */
					if(is_array($viewOptions)) {
						//if fetch cacheOptions, viewOptions["cache"]
						if(isset($viewOptions['cache']) && is_array($viewOptions['cache'])) {
							$key = $viewOptions['cache']['key'];
							$lifetime = $viewOptions['cache']['lifetime'];
						}
					}

					/**
					 * If a cache key is not set we create one using a md5
					 */
					if ($key === null) {
						$key = md5($viewPath);
					}

					/**
					 * We start the cache using the key set
					 */
					$cachedView = $cache->start($key, $lifetime);
					if ($cachedView !== null) {
						$this->_content = $cachedView;
						return null;
					}
				}

				/**
				 * This method only returns true if the cache has not expired
				 */
				if (!$cache->isFresh()) {
					return null;
				}
			}
		}

		$viewParams = $this->_viewParams;
		$eventsManager = $this->_eventsManager;

		/**
		 * Views are rendered in each engine
		 */
        foreach($engines as $extension => $engine) {

			$viewEnginePath = $viewsDirPath . $extension;
			
			if($this->_currentRenderLevel === self::LEVEL_AFTER_TEMPLATE) {
				if (!file_exists($viewEnginePath) || !is_file($viewEnginePath)) {
					
					foreach($this->_basePaths as $valueTwo) {
						if($valueTwo) {
							$valueTemp = $valueTwo . $viewsDir . $viewPath . $extension;
							
							if (file_exists($valueTemp) && is_file($valueTemp)) {
								$viewEnginePath = $valueTemp;
								break;
							}
						}
					}
					
				}
			}
			
			if (file_exists($viewEnginePath) && is_file($viewEnginePath)) {
                
				/**
				 * Call beforeRenderView if there is a events manager available
				 */
				
				if(is_object($eventsManager)) {
					$this->_activeRenderPath = $viewEnginePath;
					if($eventsManager->fire('view:beforeRenderView', $this, $viewEnginePath) === false) {
						continue;
					}
				}

				$engine->render($viewEnginePath, $viewParams, $mustClean);

				/**
				 * Call afterRenderView if there is a events manager available
				 */
				$notExists = false;
				
				if(is_object($eventsManager)) {
					$eventsManager->fire('view:afterRenderView', $this);
				}
				break;
			}
		}
		
		if ($notExists === true) {

			/**
			 * Notify about not found views
			 */
			
			if(is_object($eventsManager)) {
				$this->_activeRenderPath = $viewEnginePath;
				$eventsManager->fire('view:notFoundView', $this, $viewEnginePath);
			}

			if (!$silence) {
				throw new \Exception("View '" . $viewsDirPath . "' was not found in the views directory");
			}
		}
	}

	/**
	 * Register templating engines
	 *
	 *<code>
	 *$this->view->registerEngines(array(
	 *  ".phtml" => "Phalcon\Mvc\View\Engine\Php",
	 *  ".volt"  => "Phalcon\Mvc\View\Engine\Volt",
	 *  ".mhtml" => "MyCustomEngine"
	 *));
	 *</code>
	 */
	public function registerEngines($engines)
	{
		$this->_registeredEngines = $engines;
		return $this;
	}

	/**
	 * Checks whether view exists
	 */
	public function exists($view)
	{
		
		$basePath = $this->_basePath;
		$viewsDir = $this->_viewsDir;
        $engines = $this->_registeredEngines;
        
		if(!is_array($engines)) {
			$engines = array();
			$engines['.phtml'] = '\\WpPepVN\\Mvc\\View\\Engine\\Php';
			$engines['.php'] = '\\WpPepVN\\Mvc\\View\\Engine\\Php';
			$this->_registeredEngines = $engines;
		}

		$exists = false;
		foreach($engines as $extension => $tmp) {
			$exists = (boolean) file_exists($basePath . $viewsDir . $view . $extension);
			if($exists) {
				break;
			}
		}
		
		return $exists;
	}

	/**
	 * Executes render process from dispatching data
	 *
	 *<code>
	 * //Shows recent posts view (app/views/posts/recent.phtml)
	 * $view->start()->render('posts', 'recent')->finish();
	 *</code>
	 *
	 * @param string controllerName
	 * @param string actionName
	 * @param array params
	 */
	public function render($controllerName, $actionName, $params = null)
	{
		
		$this->_currentRenderLevel = 0;

		/**
		 * If the view is disabled we simply update the buffer from any output produced in the controller
		 */
		if ($this->_disabled != false) {
			$this->_content = ob_get_contents();
			return false;
		}

		$this->_controllerName = $controllerName;
		$this->_actionName = $actionName;
		$this->_params = $params;

		/**
		 * Check if there is a layouts directory set
		 */
		$layoutsDir = $this->_layoutsDir;
		if (!$layoutsDir) {
			$layoutsDir = 'layouts/';
		}

		/**
		 * Check if the user has defined a custom layout
		 */
		$layout = $this->_layout;
		if ($layout) {
			$layoutName = $layout;
		} else {
			$layoutName = $controllerName;
		}

		/**
		 * Load the template engines
		 */
		$engines = $this->_loadTemplateEngines();
		

		/**
		 * Check if the user has picked a view diferent than the automatic
		 */
		$pickView = $this->_pickView;

		if ($pickView === null) {
			$renderView = lcfirst($controllerName) . '/' . lcfirst($actionName);
		} else {

			/**
			 * The 'picked' view is an array, where the first element is controller and the second the action
			 */
			$renderView = lcfirst($pickView[0]);
			if(isset($pickView[1])) {
				$layoutName = lcfirst($pickView[1]);
			}
		}
		
		/**
		 * Start the cache if there is a cache level enabled
		 */
		if ($this->_cacheLevel) {
			$cache = $this->getCache();
		} else {
			$cache = null;
		}

		$eventsManager = $this->_eventsManager;
		
		
		/**
		 * Call beforeRender if there is an events manager
		 */
		if(is_object($eventsManager)) {
			if ($eventsManager->fire('view:beforeRender', $this) === false) {
				return false;
			}
		}

		
		/**
		 * Get the current content in the buffer maybe some output from the controller?
		 */
		$this->_content = ob_get_contents();

		$mustClean = true;
		$silence = true;

		/**
		 * Disabled levels allow to avoid an specific level of rendering
		 */
		$disabledLevels = $this->_disabledLevels;

		/**
		 * Render level will tell use when to stop
		 */
		$renderLevel = (int) $this->_renderLevel;
		
		if ($renderLevel) {

			/**
			 * Inserts view related to action
			 */
			if ($renderLevel >= self::LEVEL_ACTION_VIEW) {
				if (!isset ($disabledLevels[self::LEVEL_ACTION_VIEW])) {
					$this->_currentRenderLevel = self::LEVEL_ACTION_VIEW;
					
					$this->_engineRender($engines, $renderView, $silence, $mustClean, $cache);
					
				}
			}

			/**
			 * Inserts templates before layout
			 */
			if ($renderLevel >= self::LEVEL_BEFORE_TEMPLATE) {
				if (!isset ($disabledLevels[self::LEVEL_BEFORE_TEMPLATE])) {
					$this->_currentRenderLevel = self::LEVEL_BEFORE_TEMPLATE;
					$templatesBefore = $this->_templatesBefore;

					/**
					 * Templates before must be an array
					 */
					
					if(is_array($templatesBefore)) {
						$silence = false;
						foreach($templatesBefore as $templateBefore) {
							$this->_engineRender($engines, $layoutsDir . $templateBefore, $silence, $mustClean, $cache);
						}
						$silence = true;
					}
				}
			}

			/**
			 * Inserts controller layout
			 */
			if ($renderLevel >= self::LEVEL_LAYOUT) {
				if (!isset($disabledLevels[self::LEVEL_LAYOUT])) {
					$this->_currentRenderLevel = self::LEVEL_LAYOUT;
					$this->_engineRender($engines, $layoutsDir . $layoutName, $silence, $mustClean, $cache);
				}
			}

			/**
			 * Inserts templates after layout
			 */
			if ($renderLevel >= self::LEVEL_AFTER_TEMPLATE) {
				if (!isset ($disabledLevels[self::LEVEL_AFTER_TEMPLATE])) {
					$this->_currentRenderLevel = self::LEVEL_AFTER_TEMPLATE;

					/**
					 * Templates after must be an array
					 */
					$templatesAfter = $this->_templatesAfter;
					if(is_array($templatesAfter)) {
						$silence = false;
						foreach($templatesAfter as $templateAfter) {
							$this->_engineRender($engines, $layoutsDir . $templateAfter, $silence, $mustClean, $cache);
						}
						$silence = true;
					}
				}
			}

			/**
			 * Inserts main view
			 */
			if ($renderLevel >= self::LEVEL_MAIN_LAYOUT) {
				if (!isset ($disabledLevels[self::LEVEL_MAIN_LAYOUT])) {
					$this->_currentRenderLevel = self::LEVEL_MAIN_LAYOUT;
					$this->_engineRender($engines, $this->_mainView, $silence, $mustClean, $cache);
				}
			}

			$this->_currentRenderLevel = 0;
			
			/**
			 * Store the data in the cache
			 */
			
			if(is_object($cache)) {
				if ($cache->isStarted() == true) {
					if ($cache->isFresh() == true) {
						$cache->save();
					} else {
						$cache->stop();
					}
				} else {
					$cache->stop();
				}
			}
		}
		
		/**
		 * Call afterRender event
		 */
		if(is_object($eventsManager)) {
			$eventsManager->fire('view:afterRender', $this);
		}
		
		return $this;
	}
	
	/**
	 * Choose a different view to render instead of last-controller/last-action
	 *
	 * <code>
	 * class ProductsController extends \Phalcon\Mvc\Controller
	 * {
	 *
	 *    public function saveAction()
	 *    {
	 *
	 *         //Do some save stuff...
	 *
	 *         //Then show the list view
	 *         $this->view->pick("products/list");
	 *    }
	 * }
	 * </code>
	 *
	 * @param string|array renderView
	 * @return Phalcon\Mvc\View
	 */
	public function pick($renderView)
	{
		if(is_array($renderView)) {
			$pickView = $renderView;
		} else {
			$layout = null;
			if (false !== strpos($renderView, '/')) {
				$parts = explode('/', $renderView); $layout = $parts[0];
			}

			$pickView = array($renderView);
			if ($layout !== null) {
				$pickView[] = $layout;
			}
		}

		$this->_pickView = $pickView;
		return $this;
	}

	/**
	 * Renders a partial view
	 *
	 * <code>
	 * 	//Retrieve the contents of a partial
	 * 	echo $this->getPartial('shared/footer');
	 * </code>
	 *
	 * <code>
	 * 	//Retrieve the contents of a partial with arguments
	 * 	echo $this->getPartial('shared/footer', array('content' => $html));
	 * </code>
	 *
	 * @param string partialPath
	 * @param array params
	 * @return string
	 */
	public function getPartial($partialPath, $params = null)
	{
		// not liking the ob_* functions here, but it will greatly reduce the
		// amount of double code.
		ob_start();
		$this->partial($partialPath, $params);
		return ob_get_clean();
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
	 * @param array params
	 */
	public function partial($partialPath, $params = null)
	{
		/**
		 * If the developer pass an array of variables we create a new virtual symbol table
		 */
		if(is_array($params)) {

			/**
			 * Merge or assign the new params as parameters
			 */
			if(is_array($this->_viewParams)) {
				$this->_viewParams = array_merge($this->_viewParams, $params);
			} else {
				$this->_viewParams = $params;
			}
		}

		/**
		 * Partials are looked up under the partials directory
		 * We need to check if the engines are loaded first, this method could be called outside of 'render'
		 * Call engine render, this checks in every registered engine for the partial
		 */
		$this->_engineRender($this->_loadTemplateEngines(), $this->_partialsDir . $partialPath, false, false);

		/**
		 * Now we need to restore the original view parameters
		 */
		if(is_array($params)) {
			/**
			 * Restore the original view params
			 */
			$this->_viewParams = $viewParams;
		}
	}

	/**
	 * Perform the automatic rendering returning the output as a string
	 *
	 * <code>
	 * 	$template = $this->view->getRender('products', 'show', array('products' => $products));
	 * </code>
	 *
	 * @param string controllerName
	 * @param string actionName
	 * @param array params
	 * @param mixed configCallback
	 * @return string
	 */
	public function getRender($controllerName, $actionName, $params = null, $configCallback = null)
	{
		/**
		 * We must to clone the current view to keep the old state
		 */
		$view = clone $this;

		/**
		 * The component must be reset to its defaults
		 */
		$view->reset();

		/**
		 * Set the render variables
		 */
		if(is_array($params)) {
			$view->setVars($params);
		}

		/**
		 * Perform extra configurations over the cloned object
		 */
		if(is_object($configCallback)) {
			call_user_func_array($configCallback, array($view));
		}

		/**
		 * Start the output buffering
		 */
		$view->start();

		/**
		 * Perform the render passing only the controller and action
		 */
		$view->render($controllerName, $actionName);

		/**
		 * Stop the output buffering
		 */
		ob_end_clean();

		/**
		 * Get the processed content
		 */
		return $view->getContent();
	}

	/**
	 * Finishes the render process by stopping the output buffering
	 */
	public function finish()
	{
		ob_end_clean();
		return $this;
	}

	/**
	 * Create a Phalcon\Cache based on the internal cache options
	 */
	protected function _createCache()
	{
		if(!is_object($this->_dependencyInjector)) {
			throw new \Exception("A dependency injector container is required to obtain the view cache services");
		}

		$cacheService = 'viewCache';

		$viewOptions = $this->_options;
		if(is_array($viewOptions)) {
			if(isset($viewOptions['cache']['service'])) {
				$cacheService = $viewOptions['cache']['service'];
			}
		}

		/**
		 * The injected service must be an object
		 */
		$viewCache = $this->_dependencyInjector->getShared($cacheService);
		
		if(!is_object($viewCache)) {
			throw new \Exception("The injected caching service is invalid");
		}

		return $viewCache;
	}

	/**
	 * Check if the component is currently caching the output content
	 */
	public function isCaching()
	{
		return $this->_cacheLevel;
	}

	/**
	 * Returns the cache instance used to cache
	 */
	public function getCache()
	{
		if ($this->_cache) {
			if(!is_object($this->_cache)) {
				$this->_cache = $this->_createCache();
			}
		} else {
			$this->_cache = $this->_createCache();
		}
		
		return $this->_cache;
	}

	/**
	 * Cache the actual view render to certain level
	 *
	 *<code>
	 *  $this->view->cache(array('key' => 'my-key', 'lifetime' => 86400));
	 *</code>
	 *
	 * @param boolean|array options
	 * @return Phalcon\Mvc\View
	 */
	public function cache($options = true)
	{
		
		if(is_array($options)) {
		
			if(!is_array($this->_options)) {
				$this->_options = array();
			}

			/**
			 * Get the default cache options
			 */
			
			if(!isset($this->_options['cache'])) {
				$this->_options['cache'] = array();
			}
			
			foreach($options as $key => $value) {
				$this->_options['cache'][$key] = $value;
			}

			/**
			 * Check if the user has defined a default cache level or use self::LEVEL_MAIN_LAYOUT as default
			 */
			if(isset($this->_options['cache']['level'])) {
				$this->_cacheLevel = $this->_options['cache']['level'];
			} else {
				$this->_cacheLevel = self::LEVEL_MAIN_LAYOUT;
			}
			
		} else {

			/**
			 * If 'options' isn't an array we enable the cache with the default options
			 */
			if ($options) {
				$this->_cacheLevel = self::LEVEL_MAIN_LAYOUT;
			} else {
				$this->_cacheLevel = self::LEVEL_NO_RENDER;
			}
		}

		return $this;
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
	 */
	public function getActiveRenderPath()
	{
		return $this->_activeRenderPath;
	}

	/**
	 * Disables the auto-rendering process
	 */
	public function disable()
	{
		$this->_disabled = true;
		return $this;
	}

	/**
	 * Enables the auto-rendering process
	 */
	public function enable()
	{
		$this->_disabled = false;
		return $this;
	}

	/**
	 * Resets the view component to its factory default values
	 */
	public function reset()
	{
		$this->_disabled = false;
		$this->_engines = false;
		$this->_cache = null;
		$this->_renderLevel = self::LEVEL_MAIN_LAYOUT;
		$this->_cacheLevel = self::LEVEL_NO_RENDER;
		$this->_content = null;
		$this->_templatesBefore = null;
		$this->_templatesAfter = null;
		return $this;
	}

	/**
	 * Magic method to pass variables to the views
	 *
	 *<code>
	 *	$this->view->products = $products;
	 *</code>
	 *
	 * @param string key
	 * @param mixed value
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

	/**
	 * Whether automatic rendering is enabled
	 */
	public function isDisabled()
	{
		return $this->_disabled;
	}

	/**
	 * Magic method to retrieve if a variable is set in the view
	 *
	 *<code>
	 *  echo isset($this->view->products);
	 *</code>
	 *
	 * @param string key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->_viewParams[$key]);
	}
}
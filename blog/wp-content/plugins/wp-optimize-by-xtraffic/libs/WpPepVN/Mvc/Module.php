<?php
namespace WpPepVN\Mvc;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Mvc\ViewInterface
	,WpPepVN\Mvc\Application\Exception
	,WpPepVN\Mvc\ModuleDefinitionInterface
	,WpPepVN\Mvc\RouterInterface
	,WpPepVN\DiInterface
	,WpPepVN\Http\ResponseInterface
	,WpPepVN\Events\ManagerInterface
	,WpPepVN\Mvc\DispatcherInterface
;

class Module extends Injectable
{
	
	public $di = null;
	
	protected $_eventsManager = null;
	
	protected $_implicitView = true; 
	
	public function __construct() 
    {
        
    }
    
    public function init(DependencyInjection $di) 
    {
		$this->di = $di;
    }
	
	public function router_handle($uri = null, $input_configs = false)
	{
		
		if(!is_object($this->_eventsManager)) {
			$this->_eventsManager = $this->di->getShared('eventsManager');
		}
		
		/**
		 * Call boot event, this allow the developer to perform initialization actions
		 */
		if(is_object($this->_eventsManager)) {
			if ($this->_eventsManager->fire("module:before_handle", $this) === false) {
				return false;
			}
		}
		
		if(!$input_configs) {
			$input_configs = array();
		}
		
		/**
		 * Check whether use implicit views or not
		 */
		$implicitView = $this->_implicitView;

		if ($implicitView === true) {
			
			$view = $this->di->getShared('view');
			
			if($view->isDisabled()) {
				$implicitView === false;
			} else {
				
				if(isset($input_configs['view_configs']) && $input_configs['view_configs']) {
					$tmp = array(
						'setTemplateAfter'
						,'setBasePath'
						,'setLayoutsDir'
						,'setPartialsDir'
						,'setViewsDir'
					);
					
					foreach($tmp as $value) {
						if(isset($input_configs['view_configs'][$value])) {
							$view->$value($input_configs['view_configs'][$value]);
						}
					}
				}
			}
			
		}
		
		/**
		 * We get the parameters from the router and assign them to the dispatcher
		 * Assign the values passed from the router
		 */
		/*
		let dispatcher = <DispatcherInterface> dependencyInjector->getShared("dispatcher");
		dispatcher->setModuleName(router->getModuleName());
		dispatcher->setNamespaceName(router->getNamespaceName());
		dispatcher->setControllerName(router->getControllerName());
		dispatcher->setActionName(router->getActionName());
		dispatcher->setParams(router->getParams());
		*/

		/**
		 * Start the view component (start output buffering)
		 */
		if ($implicitView === true) {
			$view->start();
		}
		
		$router = $this->di->getShared('router');
		
		$router->setDI($this->di);
		
		if(isset($input_configs['router_configs']) && $input_configs['router_configs']) {
			
			$tmp = array(
				'setControllerDir'
				,'setNamespace'
			);
			
			foreach($tmp as $value) {
				if(isset($input_configs['router_configs'][$value])) {
					$router->$value($input_configs['router_configs'][$value]);
				}
			}
			
		}
		
		/**
		 * Handle the URI pattern (if any)
		 */
		$router->handle($uri);
		
		$controller = $router->getActiveControllerInstance();
		
		/**
		 * Get the latest value returned by an action
		 */
		$possibleResponse = $router->getReturnedValue();
		

		if (is_bool($possibleResponse) && ($possibleResponse === false)) {
			$response = $this->di->get('response');
		} else {
			
			if(is_object($possibleResponse)) {

				/**
				 * Check if the returned object is already a response
				 */
				$returnedResponse = $possibleResponse instanceof ResponseInterface;
			} else {
				$returnedResponse = false;
			}

			/**
			 * If the dispatcher returns an object we try to render the view in auto-rendering mode
			 */
			if ($returnedResponse === false) {
				if ($implicitView === true) {
					if (is_object($controller)) {

						$renderStatus = true;

						/**
						 * This allows to make a custom view render
						 */
						if(is_object($this->_eventsManager)) {
							//$renderStatus = (boolean) $this->_eventsManager->fire("application:viewRender", $this, $view);
						}

						/**
						 * Check if the view process has been treated by the developer
						 */
						if ($renderStatus !== false) {
							
							$view->render(
								$router->getControllerName(),
								$router->getActionName(),
								$router->getParams()
							);
						}
					}
				}
			}

			/**
			 * Finish the view component (stop output buffering)
			 */
			if ($implicitView === true) {
				$view->finish();
			}
			
			if ($returnedResponse === false) {

				$response = $this->di->get('response');
				if ($implicitView === true) {

					/**
					 * The content returned by the view is passed to the response service
					 */
					$response->setContent($view->getContent());
				}

			} else {

				/**
				 * We don't need to create a response because there is one already created
				 */
				$response = $possibleResponse;
			}
		}

		/**
		 * Calling beforeSendResponse
		 */
		if(is_object($this->_eventsManager)) {
			$this->_eventsManager->fire("application:beforeSendResponse", $this, $response);
		}

		/**
		 * Headers and Cookies are automatically send
		 */
		$response->sendHeaders();
		$response->sendCookies();
		
		/**
		 * Return the response
		 */
		return $response;
	}
}

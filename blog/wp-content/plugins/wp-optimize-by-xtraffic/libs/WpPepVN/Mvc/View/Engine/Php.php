<?php 
namespace WpPepVN\Mvc\View\Engine;

use WpPepVN\Mvc\View\Engine
	, WpPepVN\Mvc\View\EngineInterface
;

/**
 * WpPepVN\Mvc\View\Engine\Php
 *
 * Adapter to use PHP itself as templating engine
 */
class Php extends Engine implements EngineInterface
{

	/**
	 * Renders a view using the template engine
	 */
	public function render($path, $params, $mustClean = false)
	{
		
		if ($mustClean === true) {
			ob_clean();
		}

		/**
		 * Create the variables in local symbol table
		 */
		if(is_array($params)) {
			/*
			foreach($params as $key => $value) {
				{$key} = $value;
			}
			*/
			extract($params);
		}

		/**
		 * Require the file
		 */
		require $path;

		if ($mustClean === true) {
			$this->_view->setContent(ob_get_contents());
		}
	}
}
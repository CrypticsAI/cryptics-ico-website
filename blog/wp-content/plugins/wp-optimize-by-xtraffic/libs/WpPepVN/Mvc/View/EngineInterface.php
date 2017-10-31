<?php 
namespace WpPepVN\Mvc\View;

use WpPepVN\DependencyInjectionInterface
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Mvc\ViewBaseInterface
;

/**
 * WpPepVN\Mvc\View\EngineInterface
 *
 * Interface for WpPepVN\Mvc\View engine adapters
 */
interface EngineInterface
{
	/**
	 * Returns cached output on another view stage
	 */
	public function getContent();

	/**
	 * Renders a partial inside another view
	 */
	public function partial($partialPath, $params = null);

	/**
	 * Renders a view using the template engine
	 */
	public function render($path, $params, $mustClean = false);
}

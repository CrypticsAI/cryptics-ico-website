<?php 
namespace WpPepVN\Mvc\View;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Mvc\ViewBaseInterface
;

/**
 * WpPepVN\Mvc\View\Engine
 *
 * All the template engine adapters must inherit this class. This provides
 * basic interfacing between the engine and the WpPepVN\Mvc\View component.
 */
abstract class Engine extends Injectable
{

	protected $_view;

	/**
	 * WpPepVN\Mvc\View\Engine constructor
	 */
	public function __construct(ViewBaseInterface $view, DependencyInjection $dependencyInjector = null)
	{
		$this->_view = $view;
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns cached output on another view stage
	 */
	public function getContent()
	{
		return $this->_view->getContent();
	}

	/**
	 * Renders a partial inside another view
	 *
	 * @param string partialPath
	 * @param array params
	 * @return string
	 */
	public function partial($partialPath, $params = null)
	{
		return $this->_view->partial($partialPath, $params);
	}

	/**
	 * Returns the view component related to the adapter
	 */
	public function getView()
	{
		return $this->_view;
	}
}
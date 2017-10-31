<?php 
namespace WpPepVN\Mvc;

/**
 * WpPepVN\Mvc\ViewInterface
 *
 * Interface for WpPepVN\Mvc\View and WpPepVN\Mvc\View\Simple
 */
interface ViewBaseInterface
{

	/**
	 * Sets views directory. Depending of your platform, always add a trailing slash or backslash
	 */
	public function setViewsDir($viewsDir);

	/**
	 * Gets views directory
	 */
	public function getViewsDir();

	/**
	 * Adds parameters to views (alias of setVar)
	 *
	 * @param string key
	 * @param mixed value
	 */
	public function setParamToView($key, $value);

	/**
	 * Adds parameters to views
	 *
	 * @param string key
	 * @param mixed value
	 */
	public function setVar($key, $value);

	/**
	 * Returns parameters to views
	 */
	public function getParamsToView();

	/**
	 * Externally sets the view content
	 */
	public function setContent($content);

	/**
	 * Returns cached output from another view stage
	 */
	public function getContent();

	/**
	 * Renders a partial view
	 */
	public function partial($partialPath, $params = null);
}


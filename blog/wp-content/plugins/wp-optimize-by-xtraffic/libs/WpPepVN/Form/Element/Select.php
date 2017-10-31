<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element;
use WpPepVN\Form\ElementInterface;

/**
 * WpPepVN\Forms\Element\Select
 *
 * Component SELECT (choice) for forms
 */
class Select extends Element implements ElementInterface 
{

	protected $_optionsValues;

	/**
	 * WpPepVN\Forms\Element constructor
	 *
	 * @param string name
	 * @param object|array options
	 * @param array attributes
	 */
	public function __construct($name, $options = null, $attributes = null)
	{
		$this->_optionsValues = $options;
		parent::__construct($name, $attributes);
	}

	/**
	 * Set the choice's options
	 *
	 * @param array|object options
	 * @return WpPepVN\Forms\Element
	 */
	public function setOptions($options)
	{
		$this->_optionsValues = $options;
		return $this;
	}

	/**
	 * Returns the choices' options
	 *
	 * @return array|object
	 */
	public function getOptions()
	{
		return $this->_optionsValues;
	}

	/**
	 * Adds an option to the current options
	 *
	 * @param array option
	 * @return this
	 */
	public function addOption($option)
	{
		$this->_optionsValues[] = $option;
		return $this;
	}

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		/**
		 * Merged passed attributes with previously defined ones
		 */
		return \WpPepVN\Tag\Select::selectField($this->prepareAttributes($attributes), $this->_optionsValues);
	}
}

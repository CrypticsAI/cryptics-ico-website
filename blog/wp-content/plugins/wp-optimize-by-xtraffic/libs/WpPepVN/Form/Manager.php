<?php 

namespace WpPepVN\Form;

use WpPepVN\Form\Form
;
/**
 * WpPepVN\Forms\Manager
 */
class Manager
{

	protected $_forms;

	/**
	 * Creates a form registering it in the forms manager
	 *
	 * @param string name
	 * @param object entity
	 * @return WpPepVN\Forms\Form
	 */
	public function create($name = null, $entity = null) 
	{
		
		if(!is_string($name)) {
			throw new Exception("The form name must be string");
		}

		$this->_forms[$name] = new Form($entity);

		return $this->_forms[$name];
	}

	/**
	 * Returns a form by its name
	 */
	public function get($name)
	{
		if(!isset($this->_forms[$name])) {
			throw new Exception("There is no form with name='" . $name . "'");
		}
		return $this->_forms[$name];
	}

	/**
	 * Checks if a form is registered in the forms manager
	 */
	public function has($name)
	{
		return isset ($this->_forms[$name]);
	}

	/**
	 * Registers a form in the Forms Manager
	 */
	public function set($name, $form)
	{
		$this->_forms[$name] = $form;
		return $this;
	}

}

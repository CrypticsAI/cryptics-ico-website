<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element;
use WpPepVN\Form\ElementInterface;

/**
 * WpPepVN\Forms\Element\Submit
 *
 * Component INPUT[type=submit] for forms
 */
class Submit extends Element implements ElementInterface
{

	/**
	 * Renders the element widget
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null) 
	{
		/**
		 * Merged passed attributes with previously defined ones
		 */
		return \WpPepVN\Tag::submitButton($this->prepareAttributes($attributes));
	}
}

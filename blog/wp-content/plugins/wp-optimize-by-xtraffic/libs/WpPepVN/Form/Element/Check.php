<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\Check
 *
 * Component INPUT[type=check] for forms
 */
class Check extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null) 
	{
		return \WpPepVN\Tag::checkField($this->prepareAttributes($attributes, true));
	}
}

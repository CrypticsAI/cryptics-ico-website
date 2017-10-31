<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\Date
 *
 * Component INPUT[type=date] for forms
 */
class Date extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return \WpPepVN\Tag::dateField($this->prepareAttributes($attributes));
	}
}
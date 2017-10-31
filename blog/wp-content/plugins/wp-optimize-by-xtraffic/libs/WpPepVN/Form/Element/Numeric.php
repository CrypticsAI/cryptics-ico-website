<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\Numeric
 *
 * Component INPUT[type=number] for forms
 */
class Numeric extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return \WpPepVN\Tag::numericField($this->prepareAttributes($attributes));
	}
}
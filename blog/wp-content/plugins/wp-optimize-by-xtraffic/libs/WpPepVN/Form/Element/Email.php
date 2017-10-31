<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\Email
 *
 * Component INPUT[type=email] for forms
 */
class Email extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return \WpPepVN\Tag::emailField($this->prepareAttributes($attributes));
	}
}

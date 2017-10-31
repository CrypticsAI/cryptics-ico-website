<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element;
use WpPepVN\Form\ElementInterface;

/**
 * WpPepVN\Forms\Element\Text
 *
 * Component INPUT[type=text] for forms
 */
class Text extends Element implements ElementInterface
{

	/**
	 * Renders the element widget
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return \WpPepVN\Tag::textField($this->prepareAttributes($attributes));
	}
}

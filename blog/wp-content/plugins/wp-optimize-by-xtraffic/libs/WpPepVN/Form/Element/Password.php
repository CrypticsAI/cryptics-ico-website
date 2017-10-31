<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element;
use WpPepVN\Form\ElementInterface;

/**
 * WpPepVN\Forms\Element\Password
 *
 * Component INPUT[type=password] for forms
 */
class Password extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function render($attributes = null) 
	{
		return \WpPepVN\Tag::passwordField($this->prepareAttributes($attributes));
	}
}

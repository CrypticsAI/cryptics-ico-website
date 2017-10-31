<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element;
use WpPepVN\Form\ElementInterface;

/**
 * WpPepVN\Forms\Element\Radio
 *
 * Component INPUT[type=radio] for forms
 */
class Radio extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null) 
	{
		return \WpPepVN\Tag::radioField($this->prepareAttributes($attributes, true));
	}
}

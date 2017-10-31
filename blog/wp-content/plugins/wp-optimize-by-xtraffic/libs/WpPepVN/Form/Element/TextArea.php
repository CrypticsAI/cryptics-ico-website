<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
	,WpPepVN\Tag
;

/**
 * WpPepVN\Forms\Element\TextArea
 *
 * Component TEXTAREA for forms
 */
class TextArea extends Element implements ElementInterface
{

	/**
	 * Renders the element widget
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return Tag::textArea($this->prepareAttributes($attributes));
	}
}
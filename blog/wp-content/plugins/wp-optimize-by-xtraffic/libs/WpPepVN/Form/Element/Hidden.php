<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\Hidden
 *
 * Component INPUT[type=hidden] for forms
 */
class Hidden extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null)
	{
		return \WpPepVN\Tag::hiddenField($this->prepareAttributes($attributes));
	}
}
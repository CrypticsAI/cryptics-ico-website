<?php 

namespace WpPepVN\Form\Element;

use WpPepVN\Form\Element
	,WpPepVN\Form\ElementInterface
;

/**
 * WpPepVN\Forms\Element\File
 *
 * Component INPUT[type=file] for forms
 */
class File extends Element implements ElementInterface
{

	/**
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 * @return string
	 */
	public function render($attributes = null) 
	{
		return \WpPepVN\Tag::fileField($this->prepareAttributes($attributes));
	}

}
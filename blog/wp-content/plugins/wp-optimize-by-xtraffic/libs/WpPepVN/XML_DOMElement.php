<?php
namespace WpPepVN;

use WpPepVN\Text
	, WpPepVN\Hash
	, WpPepVN\System
;

class XML_DOMElement 
{
	public static $defaultParams = false;
	
	private static $_tempData = array();
	
    public function __construct() 
    {
        
    }
    
	public static function setDefaultParams()
	{
		if(false === self::$defaultParams) {
			self::$defaultParams['status'] = true;
		}
	}

	public static function renameTag( \DOMElement $oldTag, $newTagName ) 
	{
		$document = $oldTag->ownerDocument;

		$newTag = $document->createElement($newTagName);
		$oldTag->parentNode->replaceChild($newTag, $oldTag);
		
		foreach ($oldTag->attributes as $attribute) {
			$newTag->setAttribute($attribute->name, $attribute->value);
		}
		
		foreach (iterator_to_array($oldTag->childNodes) as $child) {
			$newTag->appendChild($oldTag->removeChild($child));
		}
		
		return $newTag;
	}
	
}

XML_DOMElement::setDefaultParams();

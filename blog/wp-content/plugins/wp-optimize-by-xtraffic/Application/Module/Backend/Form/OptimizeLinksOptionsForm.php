<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Form;

use WpPepVN\Validation
    ,WpPepVN\Validation\Validator\Email as ValidatorEmail
    ,WpPepVN\Validation\Validator\PresenceOf as ValidatorPresenceOf
    ,WpPepVN\Validation\Validator\StringLength as ValidatorStringLength
    ,WpPepVN\Validation\Validator\Regex as ValidatorRegex
    
    ,WpPepVN\Form\Element\Text as FormElementText
	,WpPepVN\Form\Element\Check as FormElementCheck
	,WpPepVN\Form\Element\TextArea as FormElementTextArea
    ,WpPepVN\Form\Element\Select as FormElementSelect
    ,WpPepVN\Form\Element\Password as  FormElementPassword
	
	,WpPepVN\Form\Form as FormForm
;


class OptimizeLinksOptionsForm extends FormForm
{

	/**
     * Forms initializer
     *
     * @param Property $property
     */
	 
	public $formElements = array();
	
    public function initialize($optionEntity = null, $options)
    {

		
		//Set the same form as entity
        if(!isset($options['fields'])) {
            $options['fields'] = array();
        }
        
		$arrayFieldsNames = array();
		if(!empty($options['fields'])) {
			$arrayFieldsNames = array_keys($options['fields']);
			$arrayFieldsNames = array_flip($arrayFieldsNames);
		}
		
		//Create check elements
		$arrFields = array(
			'optimize_links_enable' => ''
			,'process_in_post' => ''
			,'link_to_postself' => ''
			,'process_in_page' => ''
			,'link_to_pageself' => ''
			,'process_in_comment' => ''
			,'process_in_feed' => ''
			,'exclude_heading' => ''
			,'autolinks_case_sensitive' => ''
			,'autolinks_new_window' => ''
			,'process_only_in_single' => ''
			,'use_cats_as_keywords' => ''
			,'use_tags_as_keywords' => ''
			,'external_nofollow' => ''
			,'external_new_window' => ''
		);
		
		foreach($arrFields as $key => $value) {
			unset($arrFields[$key]);
			//begin input
			$nameElement = $key;
			if(empty($options['fields']) || isset($arrayFieldsNames)) {
			
				$this->formElements[$nameElement] = $nameElement;
				$titleElement = '';
				$inputElement = new FormElementCheck($nameElement);

				$inputElement->setAttribute('value','on');
				
				if('optimize_links_enable' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#optimize_links_enable_container');
				} else if('process_in_post' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#process_in_post_container');
				} else if('process_in_page' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#process_in_page_container');
				}
				
				$arrayFilters = array('striptags','trim','string');
				foreach($arrayFilters as $filerName) {
					$inputElement->addFilter($filerName);
				}
				
				$this->add($inputElement);
			}
			//end input
		}
		
		
		//Create text (number) elements
		$arrFields = array(
			'maxlinks' => 3
		);
		
		foreach($arrFields as $key => $value) {
			unset($arrFields[$key]);
			//begin input
			$nameElement = $key;
			if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
			
				$this->formElements[$nameElement] = $nameElement;
				$titleElement = '';
				$inputElement = new FormElementText($nameElement);
				
				$arrayFilters = array('striptags','trim','int');
				foreach($arrayFilters as $filerName) {
					$inputElement->addFilter($filerName);
				}
				$inputElement->setAttribute('style','width:80px;');
				
				if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
					$inputElement->setDefault($options['fields'][$nameElement]);
				} else {
					$inputElement->setDefault($value);
				}
				
				$this->add($inputElement);
			}
			//end input
		}
        
		//Create text (string) elements
		$arrFields = array(
			'autolinks_exclude_url' => ''
			,'data_custom_url' => ''
			,'external_exclude_url' => ''
			,'nofollow_url' => ''
		);
		
		foreach($arrFields as $key => $value) {
			unset($arrFields[$key]);
			//begin input
			$nameElement = $key;
			if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
			
				$this->formElements[$nameElement] = $nameElement;
				$titleElement = '';
				$inputElement = new FormElementText($nameElement);
				
				$arrayFilters = array('striptags','trim','string');
				foreach($arrayFilters as $filerName) {
					$inputElement->addFilter($filerName);
				}
				
				$inputElement->setAttribute('style','width:100%;');
				
				$this->add($inputElement);
			}
			//end input
		}
        
		
		//Create textarea (string) elements
		$arrFields = array(
			'data_custom' => ''
		);
		
		foreach($arrFields as $key => $value) {
			unset($arrFields[$key]);
			//begin input
			$nameElement = $key;
			if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
			
				$this->formElements[$nameElement] = $nameElement;
				$titleElement = '';
				$inputElement = new FormElementTextArea($nameElement);
				
				$arrayFilters = array('striptags','trim','string');
				foreach($arrayFilters as $filerName) {
					$inputElement->addFilter($filerName);
				}
				
				$inputElement->setAttribute('style','width:100%;min-height:100px;');
				$inputElement->setAttribute('class','wppepvn_expand_on_focus');
				
				$this->add($inputElement);
			}
			//end input
		}
        
		
    }
	
	
}
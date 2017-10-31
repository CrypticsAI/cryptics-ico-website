<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Form;

use WpPepVN\Validation
    ,WpPepVN\Validation\Validator\Email as ValidatorEmail
    ,WpPepVN\Validation\Validator\PresenceOf as ValidatorPresenceOf
    ,WpPepVN\Validation\Validator\StringLength as ValidatorStringLength
    ,WpPepVN\Validation\Validator\Regex as ValidatorRegex
    
    ,WpPepVN\Form\Element\Text as FormElementText
	,WpPepVN\Form\Element\TextArea as FormElementTextArea
    ,WpPepVN\Form\Element\Select as FormElementSelect
    ,WpPepVN\Form\Element\Password as  FormElementPassword
	,WpPepVN\Form\Form as FormForm
;


class HeaderFooterOptionsForm extends FormForm
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
        
        //begin input
        $nameElement = 'code_add_head_home';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			/*
            $arrayFilters = array('striptags','trim','string');
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $inputElement->addValidator(new ValidatorStringLength(array(
                'min' => 6,
                'max' => 100,
                'messageMinimum' => sprintf(_('"%s" too short, a minimum of 6 characters!'), $titleElement),
                'messageMaximum' => sprintf(_('"%s" too long, a maximum of 100 characters!'), $titleElement),
            )));
            */
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input

		
        //begin input
        $nameElement = 'code_add_head_all';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input
        
		
        //begin input
        $nameElement = 'code_add_footer_all';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input
		
        //begin input
        $nameElement = 'code_add_footer_home';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input
		
        //begin input
        $nameElement = 'code_add_before_articles_all';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input
		
        //begin input
        $nameElement = 'code_add_after_articles_all';
        if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
		
            $this->formElements[$nameElement] = $nameElement;
            $titleElement = '';
            $inputElement = new FormElementTextArea($nameElement);

            $inputElement->setAttribute('class','form-control wppepvn_expand_on_focus');
			
			$arrayFilters = array('trim');
			
            foreach($arrayFilters as $filerName) {
                $inputElement->addFilter($filerName);
            }
            
            if(isset($options['fields'][$nameElement]) && (false !== $options['fields'][$nameElement])) {
                $inputElement->setDefault($options['fields'][$nameElement]);
            }
            
            $this->add($inputElement);

        }
		//end input
		

		
    }
	
	
}
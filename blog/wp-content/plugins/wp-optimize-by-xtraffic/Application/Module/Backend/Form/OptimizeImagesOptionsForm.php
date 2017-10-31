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


class OptimizeImagesOptionsForm extends FormForm
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
			
			'optimize_images_override_alt' => ''
			,'optimize_images_override_title' => ''
			
			,'optimize_images_optimize_image_file_enable' => ''
			,'optimize_images_only_handle_file_when_uploading_enable' => ''
			,'optimize_images_auto_resize_images_enable' => ''
			,'optimize_images_watermarks_enable' => ''
			,'optimize_images_watermarks_watermark_text_background_enable' => ''
			
			,'optimize_images_handle_again_files_different_configuration_enable' => ''
			,'optimize_images_remove_files_available_different_configuration_enable' => ''
			
			,'optimize_images_file_exclude_external_url_enable' => ''
			
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
				
				if('optimize_images_optimize_image_file_enable' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#optimize_images_optimize_image_file_enable_container');
				} else if('optimize_images_watermarks_enable' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#optimize_images_watermarks_enable_container');
				} else if('optimize_images_watermarks_watermark_text_background_enable' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#optimize_images_watermarks_watermark_text_background_enable_container');
				} else if('optimize_images_handle_again_files_different_configuration_enable' === $nameElement) {
					$inputElement->setAttribute('class','wppepvn_toggle_show_hide_trigger');
					$inputElement->setAttribute('data-target','#optimize_images_handle_again_files_different_configuration_enable_container');
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
			'optimize_images_file_minimum_width_height' => ''
			,'optimize_images_file_maximum_width_height' => ''
			,'optimize_images_watermarks_watermark_text_margin_x' => ''
			,'optimize_images_watermarks_watermark_text_margin_y' => ''
			,'optimize_images_watermarks_watermark_text_opacity_value' => ''
			,'optimize_images_watermarks_watermark_image_margin_x' => ''
			,'optimize_images_watermarks_watermark_image_margin_y' => ''
			,'optimize_images_image_quality_value' => ''
			,'optimize_images_maximum_files_handled_each_request' => ''
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
				
				if(
					('optimize_images_watermarks_watermark_text_opacity_value' === $nameElement)
					|| ('optimize_images_image_quality_value' === $nameElement)
				) {
					$inputElement->setAttribute('style','width:100%;');
					
					$inputElement->setAttribute('data-slider','true');
					$inputElement->setAttribute('data-slider-step','1');
					$inputElement->setAttribute('data-slider-range','10,100');
				} else {
					$inputElement->setAttribute('style','width:80px;');
				}
				
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
			'optimize_images_alttext' => ''
			,'optimize_images_titletext' => ''
			,'optimize_images_watermarks_watermark_text_value' => ''
			,'optimize_images_watermarks_watermark_text_size' => '20%'
			,'optimize_images_watermarks_watermark_text_color' => 'ffffff'
			,'optimize_images_watermarks_watermark_text_background_color' => '222222'
			,'optimize_images_watermarks_watermark_image_url' => ''
			,'optimize_images_watermarks_watermark_image_width' => '20%'
			,'optimize_images_rename_img_filename_value' => ''
			,'optimize_images_file_exclude_external_url' => ''
		);
		
		foreach($arrFields as $key => $value) {
			unset($arrFields[$key]);
			//begin input
			$nameElement = $key;
			if(empty($options['fields']) || isset($options['fields'][$nameElement])) {
			
				$this->formElements[$nameElement] = $nameElement;
				$titleElement = '';
				$inputElement = new FormElementText($nameElement);
				
				if(
					('optimize_images_watermarks_watermark_text_size' === $nameElement)
					|| ('optimize_images_watermarks_watermark_text_color' === $nameElement)
					|| ('optimize_images_watermarks_watermark_text_background_color' === $nameElement)
					|| ('optimize_images_watermarks_watermark_image_width' === $nameElement)
				) {
					$inputElement->setAttribute('style','width:100px;');
					if(
						('optimize_images_watermarks_watermark_text_color' === $nameElement)
						|| ('optimize_images_watermarks_watermark_text_background_color' === $nameElement)
					) {
						$inputElement->setAttribute('class','wppepvn_color_picker');
					}
				} else {
					$inputElement->setAttribute('style','width:100%;');
				}
				
				$arrayFilters = array('striptags','trim','string');
				foreach($arrayFilters as $filerName) {
					$inputElement->addFilter($filerName);
				}
				
				$this->add($inputElement);
			}
			//end input
		}
        
		
		//begin input
		$nameElement = 'optimize_images_watermarks_watermark_text_font_name';
		if(empty($options['fields']) || isset($arrayFieldsNames)) {
		
			$this->formElements[$nameElement] = $nameElement;
			$titleElement = '';
			$inputElement = new FormElementSelect($nameElement, array(
				'arial' => 'Arial'
				,'arial_black' => 'Arial Black'
				,'verdana' => 'Verdana'
				,'times_new_roman' => 'Times New Roman'
				,'trebuchet_ms' => 'Trebuchet MS'
				,'tahoma' => 'Tahoma'
				,'impact' => 'Impact'
				,'georgia' => 'Georgia'
				,'courier_new' => 'Courier New'
				,'comic_sans_ms' => 'Comic Sans MS'
			));
			
			$inputElement->setAttribute('class','form-control');
			
			$arrayFilters = array('striptags','trim','string');
			foreach($arrayFilters as $filerName) {
				$inputElement->addFilter($filerName);
			}
			
			$this->add($inputElement);
		}
		//end input
		
    }
	
	
}
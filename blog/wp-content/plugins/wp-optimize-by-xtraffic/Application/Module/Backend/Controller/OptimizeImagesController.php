<?php
namespace WPOptimizeByxTraffic\Application\Module\Backend\Controller;

use WPOptimizeByxTraffic\Application\Module\Backend\Form\OptimizeImagesOptionsForm
	,WPOptimizeByxTraffic\Application\Service\OptimizeImages
	,WpPepVN\Utils
;

class OptimizeImagesController extends ControllerBase
{
    
	public function __construct() 
    {
		parent::__construct();
	}
	
    
	public function indexAction() 
    {
		
		$bindPostData = OptimizeImages::getOption();
		
		if(true === $this->request->isPost()) {
			$bindPostData = $this->request->getAllPostData();
		}
		
		$this->view->form = new OptimizeImagesOptionsForm((object)$bindPostData, array(
            'fields' => OptimizeImages::getDefaultOption()
        ));
		$this->view->form->setDI($this->di);
        
    	// Check if request has made with POST
        if(true === $this->request->isPost()) {
			
            // Access POST data
            $submitButton = $this->request->getPost('submitButton');
			
            if($submitButton) {
				$formElementsName = array_keys($this->view->form->formElements);
				$formElementsName = array_unique($formElementsName);
				
				$this->view->form->bind($bindPostData,null,$formElementsName);
				
				if (!$this->view->form->isValid()) {
					$messages = $this->view->form->getMessages();
					foreach ($messages as $message) {
						$this->view->adminNotice->add_notice((string)$message, 'error');
					}
					unset($messages);
				} else {
					$optionsData = array();
					foreach($formElementsName as $name) {
						$optionsData[$name] = trim($this->view->form->getValueFiltered($name));
					}
					
					$tmp = array(
						'optimize_images_watermarks_watermark_position'
						,'optimize_images_watermarks_watermark_type'
					);
					foreach($tmp as $name) {
						if(
							isset($bindPostData[$name])
							&& !empty($bindPostData[$name])
						) {
							$optionsData[$name] = $bindPostData[$name];
						} else {
							$optionsData[$name] = array();
						}
					}
					
					OptimizeImages::updateOption($optionsData);
					
					$this->_addNoticeSavedSuccess();
					
					$this->_doAfterUpdateOptions();
					
				}
				
			}
		}
		
		$watermark_positions = array(
			'x' => array('left', 'center', 'right'),
			'y' => array('top', 'middle', 'bottom')
		);
		
		
		$watermark_positions_table = '
		<table id="optimize_images_watermarks_watermark_position" border="0" align="" cellpadding="0" cellspacing="0" style="margin-left:5%;margin-bottom: 20px;" >';
		
		foreach($watermark_positions['y'] as $y) {
			$watermark_positions_table .= '
			<tr>';
			foreach($watermark_positions['x'] as $x) {
				$watermark_position = $y . '_' . $x;
				$watermark_positions_table .= '
				<td title="'.strtoupper($y . ' - ' . $x).'">
					<input name="optimize_images_watermarks_watermark_position['.$watermark_position.']" type="checkbox" value="'.$watermark_position.'"'.(isset($bindPostData['optimize_images_watermarks_watermark_position'][$watermark_position]) ? ' checked ' : '').' />
				</td>';
			}
			$watermark_positions_table .= '
			</tr>';
		}
		
		$watermark_positions_table .= '
		</table>';
		
		$this->view->watermark_positions_table = $watermark_positions_table;
		
		$this->view->bindPostData = $bindPostData;
		
		$this->view->wp_nonce = wp_create_nonce( WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG );
	}
	
}
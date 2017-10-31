<?php
namespace WPOptimizeByxTraffic\Application\Module\Backend\Controller;

use WPOptimizeByxTraffic\Application\Module\Backend\Form\HeaderFooterOptionsForm
	,WPOptimizeByxTraffic\Application\Service\HeaderFooter
	,WpPepVN\Utils
;

class HeaderFooterController extends ControllerBase
{
    
	public function __construct() 
    {
		parent::__construct();
	}
	
    
	public function indexAction() 
    {
		
		$bindPostData = HeaderFooter::getOption();
		
		if(true === $this->request->isPost()) {
			$bindPostData = $this->request->getAllPostData();
		}
		
		$this->view->form = new HeaderFooterOptionsForm((object)$bindPostData, array(
            'fields' => HeaderFooter::getDefaultOption()
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
					
					HeaderFooter::updateOption($optionsData);
					
					$this->_addNoticeSavedSuccess();
					
					$this->_doAfterUpdateOptions();
					
				}
				
			}
		}
	}
	
}
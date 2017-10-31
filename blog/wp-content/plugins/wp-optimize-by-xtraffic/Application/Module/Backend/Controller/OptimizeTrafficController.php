<?php
namespace WPOptimizeByxTraffic\Application\Module\Backend\Controller;

use WPOptimizeByxTraffic\Application\Service\OptimizeTraffic
	,WPOptimizeByxTraffic\Application\Service\PepVN_Data
	,WpPepVN\Utils
;

class OptimizeTrafficController extends ControllerBase
{
    
	public function __construct() 
    {
		parent::__construct();
	}
	
    
	public function indexAction() 
    {
		
		$options = OptimizeTraffic::getOption();
		
		$optimizeTraffic = $this->di->getShared('optimizeTraffic');
		
		if(true === $this->request->isPost()) {
			$options = $this->request->getAllPostData();
		}
		
    	// Check if request has made with POST
        if(true === $this->request->isPost()) {
			
            // Access POST data
            $submitButton = $this->request->getPost('submitButton');
			
            if($submitButton) {
				
				$optionsData = array(
					'optimize_traffic_modules' => array()
				);
				if(isset($options['optimize_traffic_modules']) && !empty($options['optimize_traffic_modules'])) {
					$optionsData['optimize_traffic_modules'] = $options['optimize_traffic_modules'];
				}
				
				OptimizeTraffic::updateOption($optionsData);
				
				$this->_addNoticeSavedSuccess();
				
				$this->_doAfterUpdateOptions();
			}
		}
		
		$this->view->trafficModuleSample = $optimizeTraffic->create_traffic_module_options(array(
			'module_id' => 'traffic_module_sample_id'
		));
		
		
		$modulesOptionsText = '';
		
		if(isset($options['optimize_traffic_modules']) && !PepVN_Data::isEmptyArray($options['optimize_traffic_modules'])) {
			foreach($options['optimize_traffic_modules'] as $keyOne => $valueOne) {
				$rsOne = $optimizeTraffic->create_traffic_module_options(array(
					'module_id' => $valueOne['module_id']
					,'moduleOptionsData' => $valueOne
				));
				if(isset($rsOne['module']) && $rsOne['module']) {
					$modulesOptionsText .= ' '.$rsOne['module'];
				}
			}
			
		}
		
		$this->view->modulesOptionsText = $modulesOptionsText;
	}
	
}
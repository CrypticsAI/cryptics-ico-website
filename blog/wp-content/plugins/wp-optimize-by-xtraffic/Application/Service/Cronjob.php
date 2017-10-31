<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Service\StaticVar as ServiceStaticVar
	, WpPepVN\DependencyInjection
	, WpPepVN\Utils
	, WpPepVN\Text
	, WpPepVN\System
;

class Cronjob
{
	private $_staticVarObject = false;
	
	private $di = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		$tmp = array();
		$tmp['last_time_process_cronjob'] = 0;
		$tmp['is_processing_cronjob_status'] = false;
		$this->_staticVarObject = new ServiceStaticVar(md5('WPOptimizeByxTraffic/Application/Service/Cronjob/construct'), $tmp);
	}
    
	public function run()
	{
		
		$resultData = array();
		
		$resultData['cronjob_status'] = 0;
		
		$staticVarData = $this->_staticVarObject->get();
		
		$doCronjobsStatus = true;
		
		if($doCronjobsStatus) {
			if(isset($staticVarData['last_time_process_cronjob']) && $staticVarData['last_time_process_cronjob']) {
				$doCronjobsStatus = false;
				if(($staticVarData['last_time_process_cronjob'] + (1 * 30)) < PepVN_Data::$defaultParams['requestTime']) {	//is timeout 
					$doCronjobsStatus = true;
				}
			}
		}
		
		if($doCronjobsStatus) {
			if(isset($staticVarData['is_processing_cronjob_status']) && $staticVarData['is_processing_cronjob_status']) {
				
				$doCronjobsStatus = false;
				
				if(isset($staticVarData['last_time_process_cronjob']) && $staticVarData['last_time_process_cronjob']) {
					if(($staticVarData['last_time_process_cronjob'] + (1 * 3600)) < PepVN_Data::$defaultParams['requestTime']) {	//is timeout
						$doCronjobsStatus = true;
					}
				}
				
			}
		}
		
		if(WP_PEPVN_DEBUG) {
			$doCronjobsStatus = true;
		}
		
		if($doCronjobsStatus) {
			
			System::setMaxHeavyExecution();
			
			$staticVarData['last_time_process_cronjob'] = PepVN_Data::$defaultParams['requestTime'];
			
			$staticVarData['is_processing_cronjob_status'] = 1;
			
			$this->_staticVarObject->save($staticVarData,'m');
			
			$hook = $this->di->getShared('hook');
			
			$resultData['cronjobs_status'] = 1;
			
			
			/*
			* Begin process cronjobs actions
			*/
			
			if(!isset($staticVarData['last_time_clean_cache_all'])) {
				$staticVarData['last_time_clean_cache_all'] = 0;
			}
			
			if($staticVarData['last_time_clean_cache_all'] <= ( PepVN_Data::$defaultParams['requestTime'] - (86400 * 3))) {	//is timeout
				$staticVarData['last_time_clean_cache_all'] = PepVN_Data::$defaultParams['requestTime'];
				
				$this->_staticVarObject->save($staticVarData,'m');
				
				$cacheManager = $this->di->getShared('cacheManager');
				$cacheManager->clean_cache(',all,');
				unset($cacheManager);
			}
			
			if($hook->has_action('cronjob')) {
				$hook->do_action('cronjob');
			}
			
			/*
			* End process cronjobs actions
			*/
			
			
			$staticVarData['last_time_process_cronjob'] = PepVN_Data::$defaultParams['requestTime'];
			$staticVarData['is_processing_cronjob_status'] = 0;
			
			$this->_staticVarObject->save($staticVarData,'m');
			
			//never use backgroundQueueJobsManager->request(); here because infinite loop
			
		}
		
		return $resultData;
	}
	
}
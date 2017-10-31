<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	,WPOptimizeByxTraffic\Application\Service\PepVN_Cache
	,WpPepVN\System
	,WpPepVN\Utils
;

class Queue
{
	private $_key = 0;
	
	private $_cacheObject = false;
	
	private $_defaultData = array();
	
	private $_configs = array();
	
	private $di = false;
	
	public function __construct($di, $key, $configs = array()) 
    {
		$this->di = $di;
		
		$this->_key = md5($key);
		
		$this->_defaultData = array();
		
		$this->_configs['folder'] = WP_CONTENT_PEPVN_DIR . 'static-vars' . DIRECTORY_SEPARATOR . 'queue' . DIRECTORY_SEPARATOR;
		
		if(isset($configs['folder'])) {
			if($configs['folder']) {
				if(!is_dir($configs['folder'])) {
					System::mkdir($configs['folder']);
				}
				
				if(is_dir($configs['folder']) && is_readable($configs['folder']) && is_writable($configs['folder'])) {
					$configs['folder'] = rtrim($configs['folder'],'/');
					$configs['folder'] = rtrim($configs['folder'],DIRECTORY_SEPARATOR);
					
					$this->_configs['folder'] = $configs['folder'] . DIRECTORY_SEPARATOR;
				}
			}
		}
		
		$this->_initCacheObject();
	}
    
	private function _initCacheObject() 
	{
		
		$pepvnDirCachePathTemp = $this->_configs['folder'];

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}
		
		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = PepVN_Data::$defaultParams['fullDomainName'] . $this->_key;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			$this->_cacheObject = new PepVN_CacheSimpleFile(array(
				'cache_timeout' => (86400 * 30)				//seconds
				,'hash_key_method' => 'crc32b'		//best is crc32b
				,'hash_key_salt' => hash('crc32b',md5($pepvnCacheHashKeySaltTemp))
				,'gzcompress_level' => 1 	//should be 0 to achieve the best performance (CPU speed)
				,'key_prefix' => 'dtstvr_'
				,'cache_dir' => $pepvnDirCachePathTemp
			));
		} else {
			$this->_cacheObject = new PepVN_CacheSimpleFile(array()); 
		}
	}
	
	private function _get($keyCachePlus = '')
	{
		
		$resultData = $this->_cacheObject->get_cache($this->_key . $keyCachePlus);
		
		if(null === $resultData) {
			$resultData = $this->_defaultData;
		}
		
		return $resultData;
		
	}
	
	private function _save($data, $method='r', $keyCachePlus = '')
	{
		
		$this->_cacheObject->set_cache($this->_key . $keyCachePlus, $data);
		
		return true;
	}
	
	private function _remove($keyCachePlus = '')
	{
		$this->_cacheObject->delete_cache($this->_key . $keyCachePlus);
		
		return true;
	}
	
	public function add($job_name, $job_data, $job_options = array())
	{
		$allData = $this->_get();
		
		if(isset($job_options['job_id'])) {
			$job_id = $job_options['job_id'];
		} else {
			$job_id = 'z'.Utils::hashKey(array($job_name, $job_data, $job_options));
		}
		
		$allData['jobs'][$job_id] = array(
			'job_name' => $job_name
			,'job_data' =>  $job_data
			,'job_options' =>  $job_options
			,'_job_id' => $job_id
		);
		
		$this->_save($allData);
		
		$backgroundQueueJobsManager = $this->di->getShared('backgroundQueueJobsManager');
		$backgroundQueueJobsManager->registerRequest();
		
	}
	
	public function reserve($input_configs = array())
	{
		$allData = $this->_get();
		
		$updateAllDataStatus = false;
		
		$reserveJobId = false;
		$reserveJobData = false;
		
		$isStillHasJobs = false;
		
		if(
			isset($allData['jobs']) 
			&& $allData['jobs']
			&& is_array($allData['jobs'])
			&& !empty($allData['jobs'])
		) {
			foreach($allData['jobs'] as $jobId => $jobData) {
				
				if(!isset($jobData['_reserved'])) {
					
					if(!$reserveJobId && !$reserveJobData) {
						
						$jobData['_reserved'] = time();
						
						$allData['jobs'][$jobId] = $jobData;
						
						$reserveJobId = $jobId;
						$reserveJobData = $jobData;
						
						$updateAllDataStatus = true;
					} else {
						$isStillHasJobs = true;
					}
					
				}
				
				if($isStillHasJobs) {
					break;
				}
			}
		}
		
		if(isset($input_configs['remove_after_reserve'])) {
			if($reserveJobId) {
				if(isset($allData['jobs'][$reserveJobId])) {
					unset($allData['jobs'][$reserveJobId]);
					$updateAllDataStatus = true;
				}
			}
		}
		
		if($updateAllDataStatus) {
			$this->_save($allData);
		}
		
		unset($allData);
		
		if($reserveJobId && $reserveJobData) {
			return array(
				'job' => $reserveJobData
				, 'still_has_jobs' => $isStillHasJobs
			);
		} else {
			return false;
		}
		
	}
	
	public function delete($job)
	{
		$allData = $this->_get();
		
		$updateAllDataStatus = false;
		
		
		if($updateAllDataStatus) {
			$this->_save($allData);
		}
		
		$jobId = false;
		
		if(is_array($job)) {
			if(isset($job['_job_id'])) {
				$jobId = $job['_job_id'];
			}
		} elseif(is_string($job)) {
			$jobId = $job;
		}
		
		if(isset($allData['jobs'][$jobId])) {
			unset($allData['jobs'][$jobId]);
			$updateAllDataStatus = true;
		}
		
		if($updateAllDataStatus) {
			$this->_save($allData);
		}
		
		unset($allData);
		
		return true;
		
	}
}
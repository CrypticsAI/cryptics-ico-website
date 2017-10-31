<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Service\StaticVar as ServiceStaticVar
	, WpPepVN\DependencyInjection
	, WpPepVN\Utils
	, WpPepVN\Text
	, WpPepVN\System
	, WpPepVN\Hash
;

class BackgroundQueueJobsManager
{
	private $_staticVarObject = false;
	
	private $di = false;
	
	private $_configs = array();
	
	private $_crypt = false;
	
	private static $_registerRequestData = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$tmp = array();
		$tmp['last_time_process_queue_jobs'] = 0;
		$tmp['is_processing_queue_jobs_status'] = false;
		
		$this->_staticVarObject = new ServiceStaticVar(md5('WPOptimizeByxTraffic/Application/Service/BackgroundQueueJobsManager/construct'), $tmp);
		
		$this->_configs['url'] = $wpExtend->admin_url('admin-ajax.php').'?action=wppepvn_background_cronjob&__ts='.PepVN_Data::$defaultParams['requestTime'];
		
		$this->_configs['key_data_send'] = 'wppepvn_bgqumn_dtecs';
		
		$this->_configs['secret_key_data_send'] = Hash::crc32b(md5(
			md5(WP_PEPVN_SITE_ID)
			. '_BackgroundQueueJobsManager_key_data_send_'
			. ceil(PepVN_Data::$defaultParams['requestTime']/86400)
		));
		
		$this->_configs['secret_key_encrypt'] = Hash::crc32b(
			md5(sha1(
				md5(WP_PEPVN_SITE_ID)
				. md5(WP_PEPVN_SITE_SALT)
				. '_BackgroundQueueJobsManager_key_encrypt_'
				. ceil(PepVN_Data::$defaultParams['requestTime']/86400)
			))
		);
		
		$this->_configs['secret_key_encrypt'] .= Hash::crc32b(md5($this->_configs['secret_key_encrypt']));
		
		$this->_crypt = new \WpPepVN\Crypt($this->di);
		
		$this->_crypt->setKey($this->_configs['secret_key_encrypt']);
		$this->_crypt->setCipher('rijndael-256');
		$this->_crypt->setMode('cbc');
		$this->_crypt->setPadding(0);
		
		$hook = $this->di->getShared('hook');
		
		$hook->add_action('wp_shutdown', array($this, 'action_wp_shutdown'));
		
	}
	
	public function action_wp_shutdown()
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$sessionKey = 'z'.crc32(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-BackgroundQueueJobsManager-RegisterRequest');
		
		$session = $this->di->getShared('session');
		
		if(
			$session->has($sessionKey)
			|| (false !== self::$_registerRequestData)
		) {
			$data = array();
			
			if(
				$session->has($sessionKey)
			) {
				$data = $session->get($sessionKey);
			}
			
			$session->set($sessionKey, '');
			$session->remove($sessionKey);
			
			if(false !== self::$_registerRequestData) {
				$data = array_merge($data, self::$_registerRequestData);
			}
			
			$this->request($data);
			
		}
		
	}
	
	public function registerRequest($data = array())
	{
		if(!$data) {
			$data = array();
		}
		
		$data = (array)$data;
		
		$sessionKey = 'z'.crc32(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-BackgroundQueueJobsManager-RegisterRequest');
		
		$session = $this->di->getShared('session');
		
		if($session->has($sessionKey)) {
			$data = array_merge($session->get($sessionKey),$data);
		}
		
		$session->set($sessionKey, $data);
		
		if(false === self::$_registerRequestData) {
			self::$_registerRequestData = array();
		}
		
		self::$_registerRequestData = array_merge(self::$_registerRequestData,$data);
		
	}
	
    
	public function _run()
	{
		
		$resultData = array();
		
		$resultData['run_queue_jobs_status'] = 0;
		
		$staticVarData = $this->_staticVarObject->get();
		
		$doQueueJobsStatus = true;
		
		if($doQueueJobsStatus) {
			if(isset($staticVarData['is_processing_queue_jobs_status']) && $staticVarData['is_processing_queue_jobs_status']) {
				
				$doQueueJobsStatus = false;
				
				if(isset($staticVarData['last_time_process_queue_jobs']) && $staticVarData['last_time_process_queue_jobs']) {
					if(($staticVarData['last_time_process_queue_jobs'] + (1 * 3600)) < PepVN_Data::$defaultParams['requestTime']) {	//is timeout
						$doQueueJobsStatus = true;
					}
				}
				
			}
		}
		
		if($doQueueJobsStatus) {
			
			System::setMaxHeavyExecution();
			
			$staticVarData['last_time_process_queue_jobs'] = PepVN_Data::$defaultParams['requestTime'];
			$staticVarData['is_processing_queue_jobs_status'] = true;
			
			$this->_staticVarObject->save($staticVarData,'m');
			
			$resultData['run_queue_jobs_status'] = 1;
			
			$hook = $this->di->getShared('hook');
			
			/*
			* Begin process queue_jobs actions
			*/
			
			$isStillHasJobs = false;
			
			if($hook->has_action('queue_jobs')) {
				
				$queue = $this->di->getShared('queue');
				
				$jobReserve = $queue->reserve(array(
					'remove_after_reserve' => true
				));
				
				if($jobReserve) {
					if(isset($jobReserve['job']) && $jobReserve['job']) {
						/*
						$jobReserve['job'] : array(
							'job_name' => $job_name
							,'job_data' =>  $job_data
							,'job_options' =>  $job_options
							,'_job_id' => $job_id
						);
						*/
						
						$hook->do_action('queue_jobs', $jobReserve['job']);
						
						if(isset($jobReserve['still_has_jobs']) && $jobReserve['still_has_jobs']) {
							$isStillHasJobs = true;
						}
					}
				}
				
				unset($jobReserve);
				
			}
			
			/*
			* End process queue_jobs actions
			*/
			
			
			$staticVarData['last_time_process_queue_jobs'] = PepVN_Data::$defaultParams['requestTime'];
			$staticVarData['is_processing_queue_jobs_status'] = false;
			
			$this->_staticVarObject->save($staticVarData,'m');
			
			if($isStillHasJobs) {
				$this->request();
			}
			
		}
		
		return $resultData;
	}
	
	
	public function request($data = array())
	{
		
		$secret_key_data_send = $this->_configs['secret_key_data_send'];
		
		$data = (array)$data;
		$data['_requestTime'] = PepVN_Data::$defaultParams['requestTime'];
		$data['_requestID'] = Hash::crc32b(Utils::randomHash());
		
		$data = $this->_crypt->encryptBase64(
			serialize(array(
				$secret_key_data_send => $data
			))
			, null
			, true
		);
		
		$key_data_send = $this->_configs['key_data_send'];
		
		$remote = $this->di->getShared('remote');
		
		$remote->request(
			$this->_configs['url']
			, array(
				'method' => 'POST',
				'timeout' => 1,
				'redirection' => 1,
				//'httpversion' => '1.0',
				//'blocking' => true,
				'headers' => array(),
				'body' => array( 
					$key_data_send => $data
				),
				'cookies' => array()
			)
		);
		
		return true;
	}
	
	public function receive()
	{
		$key_data_send = $this->_configs['key_data_send'];
		
		if(isset($_POST[$key_data_send])) {
			
			if($_POST[$key_data_send]) {
				
				$dataSent = $_POST[$key_data_send];
				
				unset($_POST[$key_data_send]);
				
				$dataSent = $this->_crypt->decryptBase64(
					$dataSent
					, null
					, true
				);
				
				if($dataSent) {
					
					$dataSent = unserialize($dataSent);
					
					if($dataSent) {
						$secret_key_data_send = $this->_configs['secret_key_data_send'];
						if(isset($dataSent[$secret_key_data_send]) && $dataSent[$secret_key_data_send]) {
							$dataSent = $dataSent[$secret_key_data_send];
							if(isset($dataSent['_requestTime']) && $dataSent['_requestTime']) {
								
								if($dataSent['_requestTime'] >= ( PepVN_Data::$defaultParams['requestTime'] - 86400)) {	//is not timeout
									$this->_run();
								}
								
							}
						}
						
					}
				}
				
			}
			
		}
	}
}
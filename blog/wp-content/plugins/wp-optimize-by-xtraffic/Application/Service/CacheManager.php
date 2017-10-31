<?php
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Service\PepVN_Cache
	, WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile
	, WPOptimizeByxTraffic\Application\Model\WpOptions
	, WpPepVN\DependencyInjection
	, WpPepVN\System
;

class CacheManager 
{
	public $di = false;
	
	private $_cleanDataType = array();
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		$hook = $this->di->getShared('hook');
		
		$hook->add_action('wp_shutdown', array($this, 'action_wp_shutdown'));
		
	}
	
	public function action_wp_shutdown()
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$didCleanCacheAll = false;
		
		if($wpExtend->is_admin()) {
			if($wpExtend->isCurrentUserCanManagePlugin()) {
				
				if(isset($_GET[WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY]) && $_GET[WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY]) {
					$this->clean_cache(',all,');
					$didCleanCacheAll = true;
				}
			}
		}
		
		$session = $this->di->getShared('session');
		
		$sessionKeyDataType = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-CacheManager-data_type';
		
		if($session->has($sessionKeyDataType)) {
			
			$data_type = $session->get($sessionKeyDataType);
			
			$data = array();
			
			$sessionKeyData = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-CacheManager-data';
			
			if($session->has($sessionKeyData)) {
				$data = $session->get($sessionKeyData);
			}
			
			$session->set($sessionKeyDataType, '');
			$session->remove($sessionKeyDataType);
			
			$session->set($sessionKeyData, '');
			$session->remove($sessionKeyData);
			
			if(!$didCleanCacheAll) {
				$this->clean_cache($data_type,$data);
			}
			
		}
		
	}
	
	public function registerCleanCache($data_type = ',common,', $data = array())
	{
		if(!$data_type) {
			$data_type = ',common,';
		}
		
		if(!$data) {
			$data = array();
		}
		
		$session = $this->di->getShared('session');
		
		$sessionDataType = '';
		
		$sessionKeyDataType = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-CacheManager-data_type';
		
		if($session->has($sessionKeyDataType)) {
			$sessionDataType .= $session->get($sessionKeyDataType);
		}
		
		$sessionDataType .= $data_type;
		
		$session->set($sessionKeyDataType, $sessionDataType);
		
		unset($sessionDataType,$data_type,$sessionKeyDataType);
		
		$data = (array)$data;
		
		$sessionData = array();
		
		$sessionKeyData = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-CacheManager-data';
		
		if($session->has($sessionKeyData)) {
			$sessionData = $session->get($sessionKeyData);
		}
		
		$avaiableFields = array(
			'cache_tags'
			,'urls'
		);
		
		foreach($avaiableFields as $field) {
			
			if(isset($data[$field])) {
				if(!isset($sessionData[$field])) {
					$sessionData[$field] = array();
				}
				
				$sessionData[$field] = array_merge($sessionData[$field], $data[$field]);
				$sessionData[$field] = array_unique($sessionData[$field]);
			}
			
		}
		
		$session->set($sessionKeyData, $sessionData);
		
		unset($sessionKeyData,$data,$sessionData);
		
	}
	
	public function clean_cache($data_type = ',common,', $data = array())
	{
		$data = (array)$data;
		
		$hook = $this->di->getShared('hook');
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$data_type = (array)$data_type;
		$data_type = implode(',',$data_type);
		$data_type = preg_replace('#[\,\;]+#is',';',$data_type);
		$data_type = explode(';',$data_type);
		foreach($data_type as $key1 => $value1) {
			$value1 = trim($value1);
			if(!$value1) {
				unset($data_type[$key1]);
			}
		}
		
		$data_type = array_unique($data_type);
		
		$data_type = array_values($data_type);
		
		$data_type = array_flip($data_type);
		
		$staticVarObject = $this->di->getShared('staticVar');
		
		$staticVarData = $staticVarObject->get();
		
		$updateStaticVarDataStatus = false;
		
		if(!isset($staticVarData['last_time_clean_cache_all'])) {
			$staticVarData['last_time_clean_cache_all'] = 0;
		}
		if($staticVarData['last_time_clean_cache_all'] <= ( PepVN_Data::$defaultParams['requestTime'] - (WP_PEPVN_CACHE_TIMEOUT_NORMAL * 30))) {	//is timeout
			$data_type['all'] = 1;
		}
		if(isset($data_type['all'])) {
			$staticVarData['last_time_clean_cache_all'] = PepVN_Data::$defaultParams['requestTime'];
			$updateStaticVarDataStatus = true;
		}
		
		//static-files
		if(!isset($staticVarData['last_time_clean_static_files'])) {
			$staticVarData['last_time_clean_static_files'] = 0;
		}
		if($staticVarData['last_time_clean_static_files'] <= ( PepVN_Data::$defaultParams['requestTime'] - (86400 * 1))) {	//is timeout
			$data_type['static-files'] = 1;
		}
		if(isset($data_type['static-files'])) {
			$staticVarData['last_time_clean_static_files'] = PepVN_Data::$defaultParams['requestTime'];
			$updateStaticVarDataStatus = true;
		}
		
		if(!isset($staticVarData['last_time_clean_cache_permanent'])) {
			$staticVarData['last_time_clean_cache_permanent'] = 0;
		}
		if($staticVarData['last_time_clean_cache_permanent'] <= ( PepVN_Data::$defaultParams['requestTime'] - (86400 * 2))) {	//is timeout
			$data_type['cache_permanent'] = 1;
		}
		if(isset($data_type['cache_permanent'])) {
			$staticVarData['last_time_clean_cache_permanent'] = PepVN_Data::$defaultParams['requestTime'];
			$updateStaticVarDataStatus = true;
		}
		
		if($updateStaticVarDataStatus) {
			$staticVarObject->save($staticVarData);
		}
		
		unset($staticVarData,$staticVarObject);
		
		if($hook->has_action('before_clean_cache')) {
			$hook->do_action('before_clean_cache');
		}
		
		$timestampNow = PepVN_Data::$defaultParams['requestTime'];
		$timestampNow = (int)$timestampNow;
		
		if(
			!isset($data_type['all'])
		) {
			if(!isset($data['cache_tags'])) {
				$data['cache_tags'] = array();
			}
			
			$data['cache_tags'] = array_merge($data['cache_tags'],wppepvn_get_cachetags_current_request(true));
		}
		
		if(isset($data['cache_tags']) && $data['cache_tags'] && !empty($data['cache_tags'])) {
			$data['cache_tags'] = array_unique($data['cache_tags']);
		}
		
		if(
			isset($data_type['all'])
			|| isset($data_type['static-files'])
		) {
			
			$arrayPaths = array();
			
			$keyTemp = WP_CONTENT_PEPVN_DIR . 'cache' . DIRECTORY_SEPARATOR . 'static-files' . DIRECTORY_SEPARATOR;
			$arrayPaths[$keyTemp] = 24;	//hours
			
			$keyTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'images' . DIRECTORY_SEPARATOR;
			$arrayPaths[$keyTemp] = 24;	//hours
			
			$keyTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'general' . DIRECTORY_SEPARATOR;
			$arrayPaths[$keyTemp] = 24;	//hours
			
			foreach($arrayPaths as $path1 => $timeout) {
				unset($arrayPaths[$path1]);
				
				$timeout = (int)$timeout;
				$timeoutSeconds = $timeout * 3600;
				
				if($path1) {
					
					$objects = System::scandir($path1);
					
					foreach($objects as $objIndex => $objPath) {
						unset($objects[$objIndex]);
						
						if($objPath) {
							if(is_file($objPath)) {
								if(is_readable($objPath) && is_writable($objPath)) {
									if(filesize($objPath) > 0) {
										$fileatime = fileatime($objPath);
										if($fileatime && ($fileatime > 0)) {
											if(($fileatime + $timeoutSeconds) <= $timestampNow) {	//is timeout
												System::unlink($objPath);
											}
										} else {
											$filemtime = filemtime($objPath);
											if($filemtime && ($filemtime > 0)) {
												if(($filemtime + $timeoutSeconds) <= $timestampNow) {	//is timeout
													System::unlink($objPath);
												}
											}
										}
									} else {
										System::unlink($objPath);
									}
								}
							}
						}
					}
					
				}
			}
		}
		
		WpOptions::cleanCache();
		
		$wpExtend->cleanCache();
		
		if(PepVN_Data::$cacheObject) {
			PepVN_Data::$cacheObject->clean(array(
				'clean_mode' => PepVN_CacheSimpleFile::CLEANING_MODE_ALL
			));
		}
		
		wp_cache_flush();
		
		global $wp_object_cache;
		if(isset($wp_object_cache) && $wp_object_cache) {
			if(is_object($wp_object_cache)) {
				$wp_object_cache->flush();
			}
		}
		
		$cache = $this->di->getShared('cache');
		$cache->flush();
		
		/*
		* Clean all data in these folders
		*/
		/*
		$arrayPaths = array();
		
		$keyTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'db' . DIRECTORY_SEPARATOR;
		$arrayPaths[$keyTemp] = 1;
		
		foreach($arrayPaths as $path1 => $value1) {
			unset($arrayPaths[$path1]);
			if($path1) {
				
				$objects = System::scandir($path1);
				
				foreach($objects as $objIndex => $objPath) {
					unset($objects[$objIndex]);
					if($objPath) {
						if(is_file($objPath)) {
							System::unlink($objPath);
						}
					}
				}
				
			}
		}
		*/
		
		if(
			isset($data_type['cache_permanent'])
			|| isset($data_type['all'])
		) {
			if(PepVN_Data::$cachePermanentObject) {
				PepVN_Data::$cachePermanentObject->clean(array(
					'clean_mode' => PepVN_CacheSimpleFile::CLEANING_MODE_ALL
				));
			}
			
			$cache = $this->di->getShared('cachePermanent');
			$cache->flush();
		}
		
		if(PepVN_Data::$cacheMultiObject) {
			if(
				isset($data_type['all'])
			) {
				
				PepVN_Data::$cacheMultiObject->clean_all_methods();
				
				PepVN_Data::$cacheMultiObject->clean(array(
					'clean_mode' => PepVN_Cache::CLEANING_MODE_ALL
				));
				
			} else {
				
				if(isset($data['cache_tags']) && $data['cache_tags'] && !empty($data['cache_tags'])) {
					
					PepVN_Data::$cacheMultiObject->clean(array(
						'clean_mode' => PepVN_Cache::CLEANING_MODE_MATCHING_ANY_TAG
						,'tags' => $data['cache_tags']
					));
					
				}
				
			}
		}
		
		if(PepVN_Data::$cacheByTagObject) {
			if(
				isset($data_type['all'])
			) {
				
				PepVN_Data::$cacheByTagObject->clean_all_methods();
				
				PepVN_Data::$cacheByTagObject->clean(array(
					'clean_mode' => PepVN_Cache::CLEANING_MODE_ALL
				));
				
			} else {
				
				if(isset($data['cache_tags']) && $data['cache_tags'] && !empty($data['cache_tags'])) {
					
					PepVN_Data::$cacheByTagObject->clean(array(
						'clean_mode' => PepVN_Cache::CLEANING_MODE_MATCHING_ANY_TAG
						,'tags' => $data['cache_tags']
					));
					
				}
				
			}
		}
		
		if(PepVN_Data::$cacheFileByTagObject) {
			if(
				isset($data_type['all'])
			) {
				
				PepVN_Data::$cacheFileByTagObject->clean_all_methods();
				
				PepVN_Data::$cacheFileByTagObject->clean(array(
					'clean_mode' => PepVN_Cache::CLEANING_MODE_ALL
				));
				
			} else {
				
				if(isset($data['cache_tags']) && $data['cache_tags'] && !empty($data['cache_tags'])) {
					
					PepVN_Data::$cacheFileByTagObject->clean(array(
						'clean_mode' => PepVN_Cache::CLEANING_MODE_MATCHING_ANY_TAG
						,'tags' => $data['cache_tags']
					));
					
				}
				
			}
		}
		
		if($hook->has_action('clean_cache')) {
			$hook->do_action('clean_cache', array(
				'type' => $data_type
				,'data' => $data
			));
		}
		
		if(
			isset($data_type['all'])
		) {
			if($hook->has_action('clean_cache_all')) {
				$hook->do_action('clean_cache_all');
			}
		}
		
		if($hook->has_action('after_clean_cache')) {
			$hook->do_action('after_clean_cache');
		}
		
		if($wpExtend->is_admin()) {
			
			$adminNotice = $this->di->getShared('adminNotice');
		
			$adminNotice->add_notice('<b>' . WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME . '</b> : ' . 'All caches have been removed.', 'success');
		}
	}
	
}
<?php
namespace WPOptimizeByxTraffic\Application\Service;

class PepVN_Cache 
{
	
	const CLEANING_MODE_ALL = 1;				//CLEAN ALL CACHE
	
	const CLEANING_MODE_EXPIRED = 2;			//CLEAN CACHE IS EXPIRED TIME
	
	const CLEANING_MODE_MATCHING_ALL_TAG = 3;	//CLEAN CACHE MATCH TAG 1 AND TAG 2 ...
	
	const CLEANING_MODE_MATCHING_ANY_TAG = 4;	//CLEAN CACHE MATCH TAG 1 OR TAG 2 ...
	
	const CLEANING_MODE_NOT_MATCHING_TAG = 5;	//CLEAN CACHE NOT HAS TAG 1 OR TAG 2...
	
	const CLEANING_MODE_CONTAIN_IN_TAG = 6;		//CLEAN CACHE CONTAIN CHARS IN TAG
	
	const GMETA_TYPE_CACHES = 'caches';
	
	const GMETA_TYPE_TAGS = 'tags';
	
	private $_options = false;
	
	private $_requestTime = 0;
	
	private $_memcache = false;
	
	private $_memcacheType = false;
	
	private $_mongo = false;
	
	private $_metadatasArrayCache = array();
	
	private $_shortKeysMethodsCache = array(
		'apc' => 'ac'
		,'memcache' => 'mc'
		,'file' => 'fi'
		,'xcache' => 'xc'
		,'mongo' => 'mg'
	);
	
	private $_has_igbinary = false;
	
	private $_key_salt = '';
	
	private static $_tempData = array();
	
	public function __construct($options = array()) 
	{
		$options = array_merge(
			array(
				'cache_timeout' => 3600	//int : seconds
				,'hash_key_method' => 'crc32b'	//crc32b is best
				,'hash_key_salt' => ''
				,'gzcompress_level' => 2
				,'key_prefix' => ''
				/*
				,'cache_methods' => array(
					
					'apc' => array(
						'cache_timeout' => 3600
					),
					
					'memcache' => array(
						'cache_timeout' => 3600
						,'object' => false	//connection to memcache
						,'servers' => array(
							array(
								'host' => '127.0.0.1'
								,'port' => '11211'
								,'persistent' => true
								,'weight' => 1
							)
						)
						
					),
					
					'file' => array(
						'cache_timeout' => 3600
						, 'cache_dir' => ''
					),
					
					'xcache' => array(
						'cache_timeout' => 3600
					),
					
					'mongo' => array(
						'cache_timeout' => 3600
						,'object' => false //connection to mongo
						,'servers' => array(
							'host' => 'mongo://localhost:27017'
							,'db' => 'cache'
							,'collection' => 'data'
						)
						
					),
				)
				*/
			)
			, (array)$options
		);
		
		$checkStatus1 = false;
		
		if(
			isset($options['cache_methods'])
			&& ($options['cache_methods'])
			&& !empty($options['cache_methods'])
		) {
			
			$shortKeysMethodsCache = $this->_shortKeysMethodsCache;
			
			foreach($options['cache_methods'] as $key1 => $val1) {
				if(!isset($shortKeysMethodsCache[$key1])) {
					unset($options['cache_methods'][$key1]);
				}
			}
			
			if(
				($options['cache_methods'])
				&& !empty($options['cache_methods'])
			) {
				$checkStatus1 = true;
			}
		}
		
		if(
			$checkStatus1
		) {
			
			$options['gzcompress_level'] = (int)$options['gzcompress_level'];
			if($options['gzcompress_level']>9) {
				$options['gzcompress_level'] = 9;
			} elseif($options['gzcompress_level'] < 0) {
				$options['gzcompress_level'] = 0;
			}
			
			if(isset($_SERVER['REQUEST_TIME']) && $_SERVER['REQUEST_TIME']) {
				$this->_requestTime = $_SERVER['REQUEST_TIME'];
			} else {
				$this->_requestTime = time();
			}
			$this->_requestTime = (int)$this->_requestTime;
			
			
			/*
			* APC
			*/
			if(isset($options['cache_methods']['apc'])) {
				if(!function_exists('apc_exists')) {
					unset($options['cache_methods']['apc']);
				}
			}
			
			/*
			* Memcache
			*/
			if(isset($options['cache_methods']['memcache'])) {
				if(isset($options['cache_methods']['memcache']['object']) && ($options['cache_methods']['memcache']['object'])) {
					$this->_memcache = $options['cache_methods']['memcache']['object'];
					unset($options['cache_methods']['memcache']['object']);
				} else {
					if(
						isset($options['cache_methods']['memcache']['servers'])
						&& $options['cache_methods']['memcache']['servers']
						&& !empty($options['cache_methods']['memcache']['servers'])
					) {
						$memcacheType = false;
						
						if(class_exists('\Memcached')) {
							$memcacheType = 'memcached';
						} else if(class_exists('\Memcache')) {
							$memcacheType = 'memcache';
						}
						
						if(false !== $memcacheType) {
							
							if('memcached' === $memcacheType) {
								$this->_memcache = new \Memcached;
							} else if('memcache' === $memcacheType) {
								$this->_memcache = new \Memcache;
							}
							
							if($this->_memcache) {
								foreach($options['cache_methods']['memcache']['servers'] as $val1) {
									if($val1) {
										$opt1 = array_merge(
											array(
												'host' => '127.0.0.1'
												,'port' => '11211'
												,'persistent' => true
												,'weight' => 1
											)
											, $val1
										);
										
										$addServerStatus = false;
										
										if('memcached' === $memcacheType) {
											$addServerStatus = $this->_memcache->addServer($opt1['host'], $opt1['port'], $opt1['weight']);
										} else {//memcache
											$addServerStatus = $this->_memcache->addServer($opt1['host'], $opt1['port'], $opt1['persistent'], $opt1['weight']);
										}
										
									}
								}
							}
							
							$memcacheVersion = false;
							
							if($this->_memcache) {
								$memcacheVersion = $this->_memcache->getVersion();
							}
							
							if(false === $memcacheVersion) {
								$this->_memcache = false;
							} else {
								$this->_memcacheType = $memcacheType;
							}
							
							unset($options['cache_methods']['memcache']['servers']);
						}
						
					}
					
				}
				
			}
			
			/*
			* Mongo
			*/
			if(isset($options['cache_methods']['mongo'])) {
				if(isset($options['cache_methods']['mongo']['object']) && ($options['cache_methods']['mongo']['object'])) {
					$this->_mongo = $options['cache_methods']['mongo']['object'];
					unset($options['cache_methods']['mongo']['object']);
				} else {
					if(
						isset($options['cache_methods']['mongo']['servers'])
						&& $options['cache_methods']['mongo']['servers']
						&& !empty($options['cache_methods']['mongo']['servers'])
					) {
						
						$mongoClientType = false;
						
						if(class_exists('\MongoClient')) {
							$mongoClientType = 'MongoClient';
						}
						
						if(false !== $mongoClientType) {
							
							if(
								isset($options['cache_methods']['mongo']['servers']['host'])
								&& ($options['cache_methods']['mongo']['servers']['host'])
								
								&& isset($options['cache_methods']['mongo']['servers']['db'])
								&& ($options['cache_methods']['mongo']['servers']['db'])
								
								&& isset($options['cache_methods']['mongo']['servers']['collection'])
								&& ($options['cache_methods']['mongo']['servers']['collection'])
							) {
								$mongoClient = new \MongoClient($options['cache_methods']['mongo']['servers']['host']);
								if($mongoClient) {
									$this->_mongo = $mongoClient->selectDb($options['cache_methods']['mongo']['servers']['db'])->selectCollection($options['cache_methods']['mongo']['servers']['collection']);
								}
								
							}
						}
						
						unset($options['cache_methods']['mongo']['servers']);
					}
					
				}
				
			}
			
			
			/*
			* File
			*/
			if(
				isset($options['cache_methods']['file'])
			) {
				
				$isCacheMethodFileValid = false;
				
				if(
					isset($options['cache_methods']['file']['cache_dir'])
				) {
					if($options['cache_methods']['file']['cache_dir']) {
						if(
							!file_exists($options['cache_methods']['file']['cache_dir']) 
							|| !is_dir($options['cache_methods']['file']['cache_dir'])
						) {
							@mkdir($options['cache_methods']['file']['cache_dir'], 0755, true);
						}
						
						if(
							file_exists($options['cache_methods']['file']['cache_dir']) 
							&& is_dir($options['cache_methods']['file']['cache_dir'])
						) {
							if(
								is_readable($options['cache_methods']['file']['cache_dir'])
								&& is_writable($options['cache_methods']['file']['cache_dir'])
							) {
								$isCacheMethodFileValid = true;
							}
						}
					}
				}
				
				if(!$isCacheMethodFileValid) {
					unset($options['cache_methods']['file']);
				}
			}
			
			if(
				($options['cache_methods'])
				&& !empty($options['cache_methods'])
			) {
				$this->_options = $options;
			}
		}
		
		$options = null;unset($options);
		
		if(function_exists('igbinary_serialize')) {
			$this->_has_igbinary = true;
		}
		
		$this->_key_salt = '_'.substr(hash('crc32b', $this->_serialize(array(
			$this->_options['key_prefix']
			,$this->_options['hash_key_salt']
			,$this->_options['hash_key_method']
			,$this->_options['cache_timeout']
		))),0,2).'_';
	}
	
	private function _is_has_hash_algos($algorithm_name)
	{
		if(!isset(self::$_tempData['hash_algos'])) {
			self::$_tempData['hash_algos'] = array();
			if(function_exists('hash_algos')) {
				self::$_tempData['hash_algos'] = hash_algos();
				self::$_tempData['hash_algos'] = array_flip(self::$_tempData['hash_algos']);
			}
		}
		
		return isset(self::$_tempData['hash_algos'][$algorithm_name]);
	}
	
	private function _serialize($data)
	{
		if(true === $this->_has_igbinary) {
			return igbinary_serialize($data);
		} else {
			return serialize($data);
		}
	}
	
	private function _unserialize($data)
	{
		if(true === $this->_has_igbinary) {
			return igbinary_unserialize($data);
		} else {
			return unserialize($data);
		}
	}
	
	private function _hash_key($keyData)
	{
		
		$hash_key_method = $this->_options['hash_key_method'];
		
		if($this->_is_has_hash_algos($hash_key_method)) {
			$keyData = hash($hash_key_method, $keyData, false);
		} else {
			$keyData = hash('crc32b', $keyData, false);
		}
		
		return $keyData;
	}
	
	private function _get_key($keyData,$type)
	{
		
		$keyData = $this->_hash_key(
			$this->_options['hash_key_salt']
			. $keyData
		);
		
		$keyData = $this->_options['key_prefix'] . $this->_key_salt . $keyData;
		
		if('cache' === $type) {
			$keyData .= '.cache';
		} else if('tags' === $type) {
			$keyData .= '.tags';
		} else if('gmeta' === $type) {	//Only 1 file store all keyCache
			$keyData .= '.gmeta';
		}
		
		return $keyData;
	}
	
	private function _get_filepath($keyData)
	{
		$filepath = false;
		
		if(isset($this->_options['cache_methods']['file']['cache_dir'])) {
			$filepath = $this->_options['cache_methods']['file']['cache_dir'] . $keyData;
		}
		
		return $filepath;
	}
	
	private function _get_template_data()
	{
		return array(
			'd' => ''			//data
			,'t' => array()		//tags
			,'e' => 0			//expire time, data will expire at this timestamp
			,'c' => false		//compress status
			,'s' => false		//serialize status
		);
	}
	
	public function set_cache($keyCache, $data, $tags = array(), $timeout = false, $configs = array())
	{
		if(false !== $this->_options) {
			
			$keyCache = $this->_get_key($keyCache,'cache');
			
			$tags = (array)$tags;
			
			if(isset($configs['merge_tags'])) {
				$dataOld = $this->_get_data($keyCache);
				if($dataOld && isset($dataOld['t'])) {
					$tags = array_merge($dataOld['t'],$tags);
				}
				unset($dataOld);
			}
			
			if(!empty($tags)) {
				$tags = array_values($tags);
				$tags = array_unique($tags);
			}
			
			if(is_object($data)) {
				$data = clone $data;
			}
			
			$data = array_merge(
				$this->_get_template_data()
				, array(
					'd' => $data	//data
					,'t' => $tags	//tags
					,'e' => 0		//expire time
					,'c' => false	//compress status
					,'s' => false	//serialize status
				)
			);
			
			if($this->_options['gzcompress_level']>0) {
				$data['d'] = $this->_serialize($data['d']);
				$data['s'] = true;
				
				$data['d'] = gzcompress($data['d'], $this->_options['gzcompress_level']);
				$data['c'] = true;
			}
			
			foreach($this->_options['cache_methods'] as $method => $val1) {
				if(isset($data['e'])) {
					unset($data['e']);
				}
				$this->_set_data($keyCache,$data,$method,$timeout);
			}
			
			$this->_process_metatags($keyCache, $tags, 'add');
			
			$this->_process_gmeta(array(
				'gmeta_type' => self::GMETA_TYPE_CACHES
				,'key_cache' => $keyCache
				,'cache_timeout' => $timeout
				,'action_type' => 'add'
			));
		}
	}
	
	public function get_cache($keyCache)
	{
		
		if(false !== $this->_options) {
			
			$keyCache = $this->_get_key($keyCache,'cache');
			$data = $this->_get_data($keyCache);
			if($data && isset($data['d'])) {
				
				if(true === $data['c']) {
					if($data['d']) {
						$data['d'] = gzuncompress($data['d']);
					}
				}
				
				if(true === $data['s']) {
					if($data['d']) {
						$data['d'] = $this->_unserialize($data['d']);
					}
				}
				
				return $data['d'];
			}
		}
		
		return null;
	}
	
	public function get_filemtime_filecache($keyCache)
	{
		$resultData = 0;
		
		if(false !== $this->_options) {
			$keyCache = $this->_get_key($keyCache,'cache');
			$filepath = $this->_get_filepath($keyCache);
			if($filepath) {
				if(is_file($filepath)) {
					$resultData = filemtime($filepath);
				}
			}
		}
		
		$resultData = (int)$resultData;
		
		return $resultData;
	}
	
	private function _get_data($keyData)
	{
		$cacheMethodsMiss = array();
		
		$data = null;
		
		foreach($this->_options['cache_methods'] as $method => $val1) {
			$data = $this->_get_data_by_method($keyData,$method);
			if($data && isset($data['d'])) {
				break;
			} else {
				$cacheMethodsMiss[$method] = $method;
			}
		}
		
		if($data && isset($data['d'])) {
			if(!empty($cacheMethodsMiss)) {
				
				foreach($cacheMethodsMiss as $method => $val1) {
					if(isset($data['e'])) {
						unset($data['e']);
					}
					$this->_set_data($keyData,$data,$method);
				}
			}
			
			return $data;
		}
	
		return null;
		
	}
	
	private function _is_key_cache($keyData)
	{
		if(1 === preg_match('#\.cache$#',$keyData)) {
			return true;
		}
		
		return false;
	}
	
	private function _get_data_by_method($keyData,$method)
	{
		$deleteCacheStatus = false;
		$data = false;
		
		if('file' === $method) {
			$filepath = $this->_get_filepath($keyData);
			if($filepath) {
				if(is_file($filepath)) {
					$data = file_get_contents($filepath);
				}
			}
		} else {
			
			if('apc' === $method) {
				if(apc_exists($keyData)) {
					$data = apc_fetch($keyData, $apcFetchStatus);
					if(!$data || !$apcFetchStatus) {
						$data = false;
						$deleteCacheStatus = true;
					}
				}
			} else if('memcache' === $method) {
				if($this->_memcache) {
					$data = $this->_memcache->get($keyData);
				}
			} else if('xcache' === $method) {
				if(xcache_isset($keyData)) {
					$data = xcache_get($keyData);
				}
			} else if('mongo' === $method) {
				if($this->_mongo) {
					$conditions = array(
						'key' => $keyData
					);
					$document = $this->_mongo->findOne($conditions);
					if($document) {
						if(isset($document['data'])) {
							$data = $document['data'];
						}
					}
					unset($document,$conditions);
				}
				
			}
		}
		
		if($data) {
			
			$data = $this->_unserialize($data);
			
			if($data) {
				
				if(
					isset($data['d'])
					&& isset($data['t'])
					&& isset($data['e'])
				) {
					
					if($data['e']>0) {
						if($this->_requestTime < $data['e']) {
							return $data;
						} else {
							$deleteCacheStatus = true;
						}
					} else {
						return $data;
					}
				}
			}
		}
		
		if($deleteCacheStatus) {
			$this->_delete_data_by_method($keyData,$method);
		}
		
		return null;
	}
	
	
	private function _set_data($keyData,&$data,$method,$timeout = false)
	{
		if(false === $timeout) {
			$timeout = $this->_get_cache_timeout_by_method($method);
		}
		
		if(!isset($data['e'])) {
			$data['e'] = $this->_get_cache_expire_by_method($method,$timeout);
		}
		
		$data['e'] = (int)$data['e'];
		
		if('apc' === $method) {
			apc_store($keyData,$this->_serialize($data),$timeout);
		} else if('memcache' === $method) {
			if($this->_memcache) {
				if('memcached' === $this->_memcacheType) {
					$this->_memcache->set($keyData, $this->_serialize($data), $timeout);
				} else {
					$this->_memcache->set($keyData, $this->_serialize($data), MEMCACHE_COMPRESSED, $timeout);
				}
			}
		} else if('file' === $method) {
			$filepath = $this->_get_filepath($keyData);
			if($filepath) {
				file_put_contents($filepath, $this->_serialize($data));
			}
		} else if('xcache' === $method) {
			xcache_set($keyData,$this->_serialize($data),$timeout);
		} else if('mongo' === $method) {
			if($this->_mongo) {
				$conditions = array(
					'key' => $keyData
				);
				$document = $this->_mongo->findOne($conditions);
				if($document && isset($document['data'])) {
					$document['data'] = $this->_serialize($data);
					$this->_mongo->update(array("_id" => $document["_id"]), $document);
				} else {
					$this->_mongo->insert(array(
						'key' => $keyData
						, 'data' => $this->_serialize($data)
					));
				}
				unset($document,$conditions);
			}
			
		}
	}
	
	public function delete_cache($keyCache)
	{
		if(false !== $this->_options) {
			
			$keyCache = $this->_get_key($keyCache,'cache');
			
			$this->_delete_cache($keyCache);
			
		}
	}
	
	private function _delete_cache($keyCache)
	{
		$data = $this->_get_data($keyCache);
		
		//Remove This KeyCache From All Tags
		if($data && isset($data['t']) && !empty($data['t'])) {
			$this->_process_metatags($keyCache, $data['t'], 'delete');
		}
		
		$data = null;
		
		$this->_process_gmeta(array(
			'gmeta_type' => self::GMETA_TYPE_CACHES
			,'key_cache' => $keyCache
			,'action_type' => 'delete'
		));
		
		foreach($this->_options['cache_methods'] as $method => $val1) {
			$this->_delete_data_by_method($keyCache,$method);
		}
	}
	
	private function _delete_data_by_method($keyData,$method)
	{
		if('file' === $method) {
			$filepath = $this->_get_filepath($keyData);
			if($filepath && is_file($filepath) && is_writable($filepath)) {
				unlink($filepath);
				clearstatcache(true, $filepath);
			}
		} else if('apc' === $method) {
			if(apc_exists($keyData)) {
				apc_delete($keyData);
			}
		} else if('memcache' === $method) {
			if($this->_memcache) {
				$this->_memcache->delete($keyData);
			}
		} else if('xcache' === $method) {
			if(xcache_isset($keyData)) {
				xcache_unset($keyData);
			}
		} else if('mongo' === $method) {
			if($this->_mongo) {
				$this->_mongo->remove(array('key' => $keyData));
			}
		}
	}
	
	/*
	*	Remove Cache
	*/
	public function clean($input_parameters)
	{
		if(false !== $this->_options) {
			
			if(
				isset($input_parameters['clean_mode'])
				&& ($input_parameters['clean_mode'])
			) {
				
				$keyCachesNeedDeleteAll = array();
				
				if(
					(self::CLEANING_MODE_EXPIRED === $input_parameters['clean_mode'])
					|| (self::CLEANING_MODE_ALL === $input_parameters['clean_mode'])
				) {
					
					$keyCachesMethodsIsExpired = array();
					
					$shortKeysMethodsCache = $this->_shortKeysMethodsCache;
					$shortKeysMethodsCache = array_flip($shortKeysMethodsCache);
					
					$keysGmeta = $this->_process_keys_gmeta(array(
						'gmeta_type' => self::GMETA_TYPE_CACHES
						,'action_type' => 'get'
					));
					
					if(isset($keysGmeta['d']['keys_gmeta']) && $keysGmeta['d']['keys_gmeta'] && !empty($keysGmeta['d']['keys_gmeta'])) {
						foreach($keysGmeta['d']['keys_gmeta'] as $keyGmeta => $keyGmetaTmp1) {
							
							$updateRsGmetaStatus = false;
							
							$rsGmeta = $this->_get_gmeta($keyGmeta);
							
							if($rsGmeta && isset($rsGmeta['d']['cache_keys']) && !empty($rsGmeta['d']['cache_keys'])) {
								
								foreach($rsGmeta['d']['cache_keys'] as $key1 => $val1) {
									
									if($val1 && isset($val1['e']) && !empty($val1['e'])) {
										foreach($val1['e'] as $key2 => $val2) {
											if(
												(self::CLEANING_MODE_EXPIRED === $input_parameters['clean_mode'])
											) {
												if($this->_is_expired($val2)) {
													if(isset($shortKeysMethodsCache[$key2])) {
														$keyCachesMethodsIsExpired[$key1][$shortKeysMethodsCache[$key2]] = 1;
													}
													unset($val1['e'][$key2]);
												}
											} else if(
												(self::CLEANING_MODE_ALL === $input_parameters['clean_mode'])
											) {
												if(isset($shortKeysMethodsCache[$key2])) {
													$keyCachesMethodsIsExpired[$key1][$shortKeysMethodsCache[$key2]] = 1;
												}
												unset($val1['e'][$key2]);
											}
										}
										
										if(empty($val1['e'])) {
											$val1 = null;
										}
									}
									
									if(null === $val1) {
										unset($rsGmeta['d']['cache_keys'][$key1]);
										$keyCachesNeedDeleteAll[$key1] = 1;
										if(isset($keyCachesMethodsIsExpired[$key1])) {
											unset($keyCachesMethodsIsExpired[$key1]);
										}
										$updateRsGmetaStatus = true;
									} else {
										if($rsGmeta['d']['cache_keys'][$key1] != $val1) {
											$rsGmeta['d']['cache_keys'][$key1] = $val1;
											$updateRsGmetaStatus = true;
										}
									}
									
								}
							}
							
							if($updateRsGmetaStatus) {
								$this->_set_gmeta($keyGmeta, $rsGmeta);
							}
							
							unset($rsGmeta);
							
						}
					}
					
					
					
					if(!empty($keyCachesMethodsIsExpired)) {
						
						foreach($keyCachesMethodsIsExpired as $key1 => $val1) {
							unset($keyCachesMethodsIsExpired[$key1]);
							
							if($val1 && !empty($val1)) {
								foreach($val1 as $key2 => $val2) {
									$this->_delete_data_by_method($key1,$key2);
									unset($key2,$val2);
								}
							}
							
							unset($key1,$val1);
						}
					}
					
					/*
					if(self::CLEANING_MODE_ALL === $input_parameters['clean_mode']) {
						foreach($this->_options['cache_methods'] as $method => $val1) {
							$this->_clean_all_by_method($method);
						}
					}
					*/
					
				} else if(
					(self::CLEANING_MODE_MATCHING_ALL_TAG === $input_parameters['clean_mode'])
					|| (self::CLEANING_MODE_MATCHING_ANY_TAG === $input_parameters['clean_mode'])
					|| (self::CLEANING_MODE_NOT_MATCHING_TAG === $input_parameters['clean_mode'])
					|| (self::CLEANING_MODE_CONTAIN_IN_TAG === $input_parameters['clean_mode'])
				) {
					
					if(
						isset($input_parameters['tags'])
						&& ($input_parameters['tags'])
						&& !empty($input_parameters['tags'])
					) {
						
						$input_parameters['tags'] = (array)$input_parameters['tags'];
						$input_parameters['tags'] = array_values($input_parameters['tags']);
						$input_parameters['tags'] = array_unique($input_parameters['tags']);
						
						if(
							(self::CLEANING_MODE_MATCHING_ALL_TAG === $input_parameters['clean_mode'])
							|| (self::CLEANING_MODE_MATCHING_ANY_TAG === $input_parameters['clean_mode'])
							|| (self::CLEANING_MODE_NOT_MATCHING_TAG === $input_parameters['clean_mode'])
						) {
							
							$rsOne = $this->_get_cache_keys_matching_any_tags($input_parameters['tags']);
							
							if(null !== $rsOne) {
								if(!empty($rsOne)) {
									foreach($rsOne as $key1 => $val1) {
										unset($rsOne[$key1]);
										
										if($val1) {
										
											if(
												(self::CLEANING_MODE_MATCHING_ALL_TAG === $input_parameters['clean_mode'])
												|| (self::CLEANING_MODE_NOT_MATCHING_TAG === $input_parameters['clean_mode'])
											) {
												$isDeleteThisCacheStatus = false;
												
												$rsTwo = $this->_get_data($val1);	//cache data
												
												if($rsTwo && isset($rsTwo['t']) && $rsTwo['t'] && !empty($rsTwo['t'])) {
													if(self::CLEANING_MODE_MATCHING_ALL_TAG === $input_parameters['clean_mode']) {
														$rsThree = array_diff($input_parameters['tags'], $rsTwo['t']);
														if($rsThree && !empty($rsThree)) {
															
														} else {
															$isDeleteThisCacheStatus = true;
														}
													} else if(self::CLEANING_MODE_NOT_MATCHING_TAG === $input_parameters['clean_mode']) {
														foreach($input_parameters['tags'] as $key2 => $val2) {
															if($val2) {
																if(!in_array($val2,$rsTwo['t'])) {
																	$isDeleteThisCacheStatus = true;
																	break 1;
																}
															}
															
														}
													}
													
												}
												
												if(true === $isDeleteThisCacheStatus) {
													$keyCachesNeedDeleteAll[$val1] = 1;
												}
											} else if(self::CLEANING_MODE_MATCHING_ANY_TAG === $input_parameters['clean_mode']) {
												$keyCachesNeedDeleteAll[$val1] = 1;
											}
											
										}//if($val1) {
											
										unset($key1,$val1);
									}
								}
							}
						} else if(
							(self::CLEANING_MODE_CONTAIN_IN_TAG === $input_parameters['clean_mode'])
						) {
							
							$keyGmeta = $this->_get_key_gmeta(self::GMETA_TYPE_TAGS, '');
							$rsGmeta = $this->_get_gmeta($keyGmeta);
						
							$tagsNeedGet = array();
							
							if($rsGmeta && isset($rsGmeta['d']['tags']) && !empty($rsGmeta['d']['tags'])) {
								foreach($rsGmeta['d']['tags'] as $tag => $val1) {
									if($tag) {
										foreach($input_parameters['tags'] as $inputTag) {
											
											if(false !== strpos($tag, $inputTag)) {
												$tagsNeedGet[$tag] = 1;
												break 1;
											}
										}
									}
								}
							}
							
							$rsGmeta = null;
							
							if(!empty($tagsNeedGet)) {
								
								$rsOne = $this->_get_cache_keys_matching_any_tags(array_keys($tagsNeedGet));
								
								if(null !== $rsOne) {
									if(!empty($rsOne)) {
										foreach($rsOne as $key1 => $val1) {
											if($val1) {
												$keyCachesNeedDeleteAll[$val1] = 1;
											}
										}
									}
								}
								
							}
						}
					}
					
				}
				
				if(!empty($keyCachesNeedDeleteAll)) {
					foreach($keyCachesNeedDeleteAll as $key1 => $val1) {
						unset($keyCachesNeedDeleteAll[$key1]);
						$this->_delete_cache($key1);
					}
				}
				
			}
		}
		
	}
	
	public function clean_all_methods()
	{
		if(false !== $this->_options) {
			foreach($this->_options['cache_methods'] as $method => $val1) {
				$this->_clean_all_by_method($method);
			}
			
			if($this->_memcache) {
				$this->_memcache->flush();
			}
			
			if(isset($this->_options['cache_methods']['file']['cache_dir'])) {
				$cacheDir = $this->_options['cache_methods']['file']['cache_dir'];
				$cacheDir = preg_replace('#[\/\\\]+$#is','',$cacheDir).'/';
				if(file_exists($cacheDir) && is_dir($cacheDir) && is_readable($cacheDir) && is_writable($cacheDir)) {
					$files = glob($cacheDir.'*');
					if($files && is_array($files) && !empty($files)) {
						foreach($files as $file) {
							if($file && is_file($file)) {
								unlink($file);
								clearstatcache(true, $file);
							}
						}
					}
				}
				
			}
		}
	}
	
	private function _clean_all_by_method($method)
	{
		if('apc' === $method) {
			apc_clear_cache('user');
		} else if('memcache' === $method) {
			if($this->_memcache) {
				if('memcached' === $this->_memcacheType) {
					$keys = $this->_memcache->getAllKeys();
					if($keys) {
						$keys = array_values($keys);
						foreach ($keys as $index => $key) {
							if (false === strpos($key,$this->_key_salt)) {
								unset($keys[$index]);
							}
						}
						
						if(!empty($keys)) {
							$this->_memcache->deleteMulti($keys);
						}
					} else {
						$this->_memcache->flush();
					}
				} else {
					$this->_memcache->flush();
				}
			}
		} else if('file' === $method) {
			if(isset($this->_options['cache_methods']['file']['cache_dir'])) {
				$cacheDir = $this->_options['cache_methods']['file']['cache_dir'];
				$cacheDir = preg_replace('#[\/\\\]+$#is','',$cacheDir).'/';
				if(file_exists($cacheDir) && is_dir($cacheDir) && is_readable($cacheDir) && is_writable($cacheDir)) {
					$files = glob($cacheDir.'*'.$this->_key_salt.'.*');
					if($files && is_array($files) && !empty($files)) {
						foreach($files as $file) {
							if($file && is_file($file)) {
								unlink($file);
								clearstatcache(true, $file);
							}
						}
					}
				}
				
			}
		}
	
	}
	
	//Helper function to validate key cache
	private function _safe_keydata($keyData)
	{
		$keyData = trim(preg_replace('#[^0-9a-z\_\-]+#is',' ', strtolower($keyData)));
		
		return preg_replace('#[\s \t]+#is','-',$keyData);
	}
	
	private function _get_cache_timeout_by_method($method)
	{
		
		$cacheTimeout = 0;
		
		if(isset($this->_options['cache_methods'][$method]['cache_timeout'])) {
			$cacheTimeout = $this->_options['cache_methods'][$method]['cache_timeout'];
		} else {
			$cacheTimeout = $this->_options['cache_timeout'];
		}
		
		$cacheTimeout = (int)$cacheTimeout;
		
		return $cacheTimeout;
	}
	
	
	private function _get_cache_expire_by_method($method,$timeout = false)
	{
		$expire = 0;
		
		if(false === $timeout) {
			$timeout = $this->_get_cache_timeout_by_method($method);
		}
		
		$timeout = (int)$timeout;
		if($timeout > 0) {
			$expire = $this->_requestTime + $timeout;
		}
		
		$expire = (int)$expire;
		
		return $expire;
	}
	
	private function _is_expired($expire)
	{
		$expire = (int)$expire;
		if($expire > 0) {
			if($this->_requestTime >= $expire) {
				return true;
			}
		}
		
		return false;
	}
	
	private function _get_cache_keys_matching_any_tags($tags)
	{
		if(!empty($tags)) {
			
			$data = $this->_process_metatags(false,$tags,'get_cache_keys_matching_any_tags');
			
			if(
				isset($data['d']['cache_keys'])
				&& !empty($data['d']['cache_keys'])
			) {
				return array_keys($data['d']['cache_keys']);
			}
		}
		
		return null;
	}
	
	/*
	* Store Cache's Tags. For best performance, shouldn't gzcompress data
	*/
	private function _process_metatags($keyCache, $tags, $actionType)
	{
		$resultData = array();
		
		if(!empty($tags)) {
			
			$tags = (array)$tags;
			$tags = array_values($tags);
			$tags = array_unique($tags);
			
			foreach($tags as $tag) {
				
				$keyTag = $this->_get_key($tag,'tags');
				
				$data = $this->_get_data($keyTag);
				
				if(null === $data) {
					if('add' === $actionType) {
						$data = $this->_get_template_data();
					}
				}
				
				$updateNewDataStatus = false;
				
				if('add' === $actionType) {
					if($keyCache) {
						if(!isset($data['d']['cache_keys'][$keyCache])) {
							$data['d']['cache_keys'][$keyCache] = 1;
							$updateNewDataStatus = true;
						}
					}
				} else if('delete' === $actionType) {
					if(isset($data['d']['cache_keys'][$keyCache])) {
						unset($data['d']['cache_keys'][$keyCache]);
						$updateNewDataStatus = true;
					}
				}
				
				if($updateNewDataStatus) {
					if(
						isset($data['d']['cache_keys'])
						&& !empty($data['d']['cache_keys'])
					) {
						foreach($this->_options['cache_methods'] as $method => $val1) {
							if(isset($data['e'])) {
								unset($data['e']);
							}
							$this->_set_data($keyTag,$data,$method,0);
						}
						
						$this->_process_gmeta(array(
							'gmeta_type' => self::GMETA_TYPE_TAGS
							,'tags' => array($tag)
							,'action_type' => 'add'
						));
					} else {
						
						foreach($this->_options['cache_methods'] as $method => $val1) {
							$this->_delete_data_by_method($keyTag,$method);
						}
						
						$this->_process_gmeta(array(
							'gmeta_type' => self::GMETA_TYPE_TAGS
							,'tags' => array($tag)
							,'action_type' => 'delete'
						));
					}
				} else if(in_array($actionType,array(
					'get_cache_keys_matching_any_tags'
				))) {
					if(
						isset($data['d']['cache_keys'])
						&& !empty($data['d']['cache_keys'])
					) {
						if(
							!isset($resultData['d']['cache_keys'])
						) {
							$resultData['d']['cache_keys'] = array();
						}
						
						$resultData['d']['cache_keys'] = array_merge($resultData['d']['cache_keys'], $data['d']['cache_keys']);
					}
				}
				
				$data = null;
				
			}
			
		}
		
		return $resultData;
	}
	
	/*
	* Store Cache's Information : expire time. For best performance, shouldn't gzcompress data
	*/
	private function _process_gmeta($input_parameters)
	{
		$data = null;
		
		if(
			isset($input_parameters['action_type'])
			&& ($input_parameters['action_type'])
		) {
			
			$actionType = $input_parameters['action_type'];
			
			$keyCache = false;
			
			$tags = false;
			
			$keyGmetaPlus = '';
			
			if(
				isset($input_parameters['key_cache'])
				&& ($input_parameters['key_cache'])
			) {
				$keyCache = $input_parameters['key_cache'];
			}
			
			if(
				isset($input_parameters['tags'])
				&& ($input_parameters['tags'])
				&& !empty($input_parameters['tags'])
			) {
				$tags = (array)$input_parameters['tags'];
				$tags = array_values($tags);
				$tags = array_unique($tags);
			}
			
			if(self::GMETA_TYPE_CACHES === $input_parameters['gmeta_type']) {
				if($keyCache) {
					$keyGmetaPlus = hash('crc32b', $keyCache);
					$keyGmetaPlus = '_'.substr($keyGmetaPlus,0,1);
				}
			}
			
			$keyGmeta = $this->_get_key_gmeta($input_parameters['gmeta_type'], $keyGmetaPlus);
			
			$updateNewDataStatus = false;
			
			$data = $this->_get_gmeta($keyGmeta);
			
			if('add' === $actionType) {
				if($keyCache) {
					
					$cacheGmetaData = array();
					
					if(
						!isset($input_parameters['cache_timeout'])
					) {
						$input_parameters['cache_timeout'] = false;
					}
					
					$shortKeysMethodsCache = $this->_shortKeysMethodsCache;
					foreach($this->_options['cache_methods'] as $method => $val1) {
						$cacheGmetaData['e'][$shortKeysMethodsCache[$method]] = $this->_get_cache_expire_by_method($method,$input_parameters['cache_timeout']);
					}
					
					if(isset($data['d']['cache_keys'][$keyCache])) {
						if($data['d']['cache_keys'][$keyCache] != $cacheGmetaData) {
							$data['d']['cache_keys'][$keyCache] = $cacheGmetaData;
							$updateNewDataStatus = true;
						}
					} else {
						$data['d']['cache_keys'][$keyCache] = $cacheGmetaData;
						$updateNewDataStatus = true;
					}
					
					
				} else if($tags) {
					foreach($tags as $tag) {
						if($tag) {
							if(!isset($data['d']['tags'][$tag])) {
								$data['d']['tags'][$tag] = 1;
								$updateNewDataStatus = true;
							}
						}
					}
				}
			} else if('delete' === $actionType) {
				if($keyCache) {
					if(isset($data['d']['cache_keys'][$keyCache])) {
						unset($data['d']['cache_keys'][$keyCache]);
						$updateNewDataStatus = true;
					}
				} else if($tags) {
					foreach($tags as $tag) {
						if($tag) {
							if(isset($data['d']['tags'][$tag])) {
								unset($data['d']['tags'][$tag]);
								$updateNewDataStatus = true;
							}
						}
					}
				}
			}
			
			if($updateNewDataStatus) {
				if(
					(
						isset($data['d']['cache_keys'])
						&& !empty($data['d']['cache_keys'])
					)
					
					||
					
					(
						isset($data['d']['tags'])
						&& !empty($data['d']['tags'])
					)
					
				) {
					$this->_set_gmeta($keyGmeta, $data);
					
					if(self::GMETA_TYPE_CACHES === $input_parameters['gmeta_type']) {
						$this->_process_keys_gmeta(array(
							'gmeta_type' => $input_parameters['gmeta_type']
							,'key_gmeta' => $keyGmeta
							,'action_type' => 'add'
						));
					}
					
				} else {
					foreach($this->_options['cache_methods'] as $method => $val1) {
						$this->_delete_data_by_method($keyGmeta,$method);
					}
					
					if(self::GMETA_TYPE_CACHES === $input_parameters['gmeta_type']) {
						$this->_process_keys_gmeta(array(
							'gmeta_type' => $input_parameters['gmeta_type']
							,'key_gmeta' => $keyGmeta
							,'action_type' => 'delete'
						));
					}
					
				}
			}
			
		}
		
		return $data;
	}
	
	/*
	* Store Information Gmeta's Key. For best performance, shouldn't gzcompress data
	*/
	private function _process_keys_gmeta($input_parameters)
	{
		$data = null;
		
		if(
			isset($input_parameters['action_type'])
			&& ($input_parameters['action_type'])
		) {
			
			$actionType = $input_parameters['action_type'];
			
			$updateNewDataStatus = false;
			
			$inputKeyGmeta = false;
			
			if(
				isset($input_parameters['key_gmeta'])
				&& ($input_parameters['key_gmeta'])
			) {
				$inputKeyGmeta = $input_parameters['key_gmeta'];
			}
			
			$keyGmeta = $this->_get_key_gmeta($input_parameters['gmeta_type'], 'store_keys_gmeta');
			
			$data = $this->_get_gmeta($keyGmeta);
			
			if('add' === $actionType) {
				if($inputKeyGmeta) {
					if(!isset($data['d']['keys_gmeta'][$inputKeyGmeta])) {
						$data['d']['keys_gmeta'][$inputKeyGmeta] = 1;
						$updateNewDataStatus = true;
					}
				}
			} else if('delete' === $actionType) {
				if($inputKeyGmeta) {
					if(isset($data['d']['keys_gmeta'][$inputKeyGmeta])) {
						unset($data['d']['keys_gmeta'][$inputKeyGmeta]);
						$updateNewDataStatus = true;
					}
				}
			}
			
			if($updateNewDataStatus) {
				if(
					(
						isset($data['d']['keys_gmeta'])
						&& !empty($data['d']['keys_gmeta'])
					)
				) {
					$this->_set_gmeta($keyGmeta, $data);
				} else {
					foreach($this->_options['cache_methods'] as $method => $val1) {
						$this->_delete_data_by_method($keyGmeta, $method);
					}
				}
			}
			
		}
		
		return $data;
	}
	
	private function _get_key_gmeta($gmetaType, $keyGmetaPlus = '')
	{
		return $this->_get_key(
			$this->_hash_key($this->_serialize(array($gmetaType , __CLASS__ , __METHOD__ , $this->_key_salt , $keyGmetaPlus)))
			,'gmeta'
		);
	}
	
	private function _get_gmeta($keyGmeta)
	{
		
		$data = $this->_get_data($keyGmeta);
		
		if(null === $data) {
			$data = $this->_get_template_data();
		} else {
			$data['d'] = $this->_unserialize(gzuncompress($data['d']));
		}
		
		return $data;
	}
	
	private function _set_gmeta($keyGmeta, $data)
	{
		$data['d'] = gzcompress($this->_serialize($data['d']),2);
		foreach($this->_options['cache_methods'] as $method => $val1) {
			if(isset($data['e'])) {
				unset($data['e']);
			}
			$this->_set_data($keyGmeta,$data,$method,0);
		}
	}
	
}



<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	,WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile
	,WpPepVN\System
	,WpPepVN\Utils
;

class StaticVar
{
	private $_key = 0;
	
	private $_cacheObject = false;
	
	private $_defaultData = array();
	
	private $_configs = array();
	
	public function __construct($key, $defaultData, $configs = array()) 
    {
		$this->_key = md5($key);
		
		$this->_defaultData = (array)$defaultData;
		
		$this->_configs['folder'] = WP_CONTENT_PEPVN_DIR . 'static-vars' . DIRECTORY_SEPARATOR;
		
		if(isset($configs['cacheObject']) && $configs['cacheObject']) {
			$this->_cacheObject = $configs['cacheObject'];
		} else if(isset($configs['folder'])) {
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
		
		if(!$this->_cacheObject) {
			$this->_initCacheObject();
		}
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
				'cache_timeout' => (86400 * 30)			//seconds
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
	
	public function get($keyCachePlus = '')
	{
		
		$resultData = $this->_cacheObject->get_cache($this->_key . $keyCachePlus);
		
		if(null === $resultData) {
			$resultData = $this->_defaultData;
		}
		
		return $resultData;
		
	}
	
	public function save($data, $method='m', $keyCachePlus = '')
	{
		if('m' === $method) {
			$data = Utils::mergeArrays(array(
				$this->get($keyCachePlus)
				, $data
			));
		}
		
		$this->_cacheObject->set_cache($this->_key . $keyCachePlus, $data);
		
		return true;
	}
	
	public function remove($keyCachePlus = '')
	{
		$this->_cacheObject->delete_cache($this->_key . $keyCachePlus);
		
		return true;
	}
}
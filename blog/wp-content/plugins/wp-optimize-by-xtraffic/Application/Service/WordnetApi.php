<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\Text
	,WpPepVN\DependencyInjection
	,WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	,WPOptimizeByxTraffic\Application\Service\PepVN_Data
;

class WordnetApi
{
	
	protected static $_tempData = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
	}
    
	public function initFrontend() 
    {
		
	}
	
	public function initBackend() 
    {
		
	}
	
	public function getSynonyms($word, $lang)
	{
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $word
			, $lang
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1, true);
		
		if(null !== $resultData) {
			return $resultData;
		}
		
		$resultData = array($word);
		
		if(!isset(self::$_tempData['getSynonyms_NumRequest'])) {
			self::$_tempData['getSynonyms_NumRequest'] = 0;
		}
		
		self::$_tempData['getSynonyms_NumRequest']++;
		
		if(self::$_tempData['getSynonyms_NumRequest'] > 100) {
			return $resultData;
		}
		
		$remoteUrl = false;
		
		if('en' === $lang) {
			//http://wordnetweb.princeton.edu/perl/webwn?s=happy
			$remoteUrl = 'http://wordnetweb.princeton.edu/perl/webwn?s=' . rawurlencode($word);
		} else if('vi' === $lang) {
			//http://viet.wordnet.vn/wnms/editor/search/by-word/hạnh%20phúc
			$remoteUrl = 'http://viet.wordnet.vn/wnms/editor/search/by-word/' . rawurlencode($word);
		}
		
		$remote = $this->di->getShared('remote');

		$remoteData = $remote->get($remoteUrl,array(
			'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
		));
		
		if(false !== $remoteData) {
			if('en' === $lang) {
				preg_match_all('/\;s=([a-z]+)/', $remoteData, $matches);
				unset($remoteData);
				if(isset($matches[1]) && $matches[1] && !empty($matches[1])) {
					$resultData = array_merge($resultData, $matches[1]);
				}
				unset($matches);
			} else if('vi' === $lang) {
				preg_match_all('#<span\s+id="sense_words"[^>]+>([^<>]+)</span>#is', $remoteData, $matches);
				if(isset($matches[1]) && $matches[1] && !empty($matches[1])) {
					$matches = $matches[1];
					foreach($matches as $key1 => $value1) {
						unset($matches[$key1]);
						$value1 = trim($value1);
						if($value1) {
							$value1 = explode(PHP_EOL,$value1);
							$value1 = PepVN_Data::cleanArray($value1);
							foreach($value1 as $key2 => $value2) {
								unset($value1[$key2]);
								$value2 = trim($value2);
								if($value2) {
									$value2 = PepVN_Data::splitAndCleanKeywords($value2);
									if(!empty($value2)) {
										$resultData = array_merge($resultData, $value2);
									}
								}
								unset($key2,$value2);
							}
						}
						unset($key1,$value1);
					}
				}
				unset($matches);
			}
		}
		
        $resultData = array_unique($resultData);
		$tmp = $resultData;
		$resultData = array();
		
		foreach($tmp as $key1 => $value1) {
			unset($tmp[$key1]);
			$value1 = PepVN_Data::strtolower($value1);
			$resultData[$value1] = PepVN_Data::mb_strlen($value1);
			unset($key1,$value1);
		}
		
		arsort($resultData);
		
		$resultData = array_keys($resultData);
		
		TempDataAndCacheFile::set_cache($keyCache1, $resultData, true);
        
		return $resultData;
    }
    
}
<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\TempData
	, WpPepVN\Text
	, WpPepVN\Utils
	, WpPepVN\Hash
	, WpPepVN\System
	, WpPepVN\DependencyInjection
	, WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	, WPOptimizeByxTraffic\Application\Service\PepVN_Data
	
	, LanguageDetector\Config as LanguageDetectorConfig
	, LanguageDetector\Learn as LanguageDetectorLearn
	, LanguageDetector\AbstractFormat as LanguageDetectorAbstractFormat
	, LanguageDetector\Detect as LanguageDetectorDetect
;

/*
*	This class extends class \WpPepVN\TempData so all result is cached in var _tempData
*/

class Language
{
	private $di;
	
	private static $_tempData = array();
	
	private static $_configs = false;
	
	private static $_detect = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		if(false === self::$_configs) {
			
			self::$_configs = array();
			
			self::$_configs['store_dir'] = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes' . DIRECTORY_SEPARATOR . 'languagedetector' . DIRECTORY_SEPARATOR . 'crodas' . DIRECTORY_SEPARATOR;
			
			self::$_configs['samples_dir'] =  self::$_configs['store_dir'] . 'samples' . DIRECTORY_SEPARATOR;
			
			self::$_configs['datafile_path'] = self::$_configs['store_dir'] . 'datafile.php';
			
			self::$_configs['learn_status_file_path'] = self::$_configs['store_dir'] . 'learn_status.txt';
			
			/*
				https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
				http://www.w3.org/International/questions/qa-html-language-declarations
				http://www.w3.org/International/questions/qa-http-and-lang
			*/
			
			self::$_configs['lang_code'] = array(
				'english' => 'en'
				,'vietnamese' => 'vi'
				,'albanian' => 'sq'
				,'arabic' => 'ar'
				,'catalan' => 'ca'
				,'danish' => 'da'
				,'dutch' => 'nl'
				,'esperanto' => 'eo'
				,'estonian' => 'et'
				,'euskara' => 'eu'
				,'finnish' => 'fi'
				,'french' => 'fr'
				,'german' => 'de'
				,'guarani' => 'gn'
				,'hebrew' => 'he'
				,'irish' => 'ga'
				,'italian' => 'it'
				,'latin' => 'la'
				,'norwegian' => 'no'
				,'portuguese' => 'pt'
				,'romanian' => 'ro'
				,'spanish' => 'es'
				,'swedish' => 'sv'
				,'welsh' => 'cy'
				
			);
			
			
			$hook = $this->di->getShared('hook');
			$hook->add_action('cronjob', array($this, 'action_cronjob'), WP_PEPVN_PRIORITY_LAST);
			
		}
	}
    
    public function init() 
    {
        
    }
	
    public function action_cronjob() 
    {
		$this->initLearn();
	}
	
    public function initLearn($forceStatus = false) 
    {
		$createDataStatus = true;
		
		if(!$forceStatus) {
			
			if(
				is_file(self::$_configs['datafile_path'])
				&& (filesize(self::$_configs['datafile_path'])>0)
			) {
				$createDataStatus = false;
			}
			
			if($createDataStatus) {
				if(is_file(self::$_configs['learn_status_file_path'])) {
					$tmp = filemtime(self::$_configs['learn_status_file_path']);
					$tmp = (int)$tmp;
					if($tmp>0) {
						if(($tmp + 7200) <= time()) {	//is timeout
							
						} else {
							$createDataStatus = false;
						}
					}

				}
			}
			
		}
		
		if(!$createDataStatus) {
			return true;
		}
		
		file_put_contents(self::$_configs['learn_status_file_path'], time());
		
		System::unlink(self::$_configs['datafile_path']);
		
		@set_time_limit(0);
		@ini_set('memory_limit', '1G');
		@mb_internal_encoding('UTF-8');

		$config = new LanguageDetectorConfig();
		$config->useMb(true);

		$c = new LanguageDetectorLearn($config);

		$samplesFiles = glob(self::$_configs['samples_dir'] . '*');
		
		if($samplesFiles && !empty($samplesFiles)) {
			foreach ($samplesFiles as $file) {
				$c->addSample(basename($file), file_get_contents($file));
			}
		}

		$c->addStepCallback(function($lang, $status) {
			//echo "Learning {$lang}: $status\n";
		});
		
		$c->save(LanguageDetectorAbstractFormat::initFormatByPath(self::$_configs['datafile_path']));

		file_put_contents(self::$_configs['learn_status_file_path'], time());
		
		return true;
    }
	
	
    public function detect($text) 
    {
		if(false === self::$_detect) {
			if(
				is_file(self::$_configs['datafile_path'])
				&& (filesize(self::$_configs['datafile_path'])>0)
			) {
				self::$_detect = LanguageDetectorDetect::initByPath(self::$_configs['datafile_path']);
			}
		}
		
		$lang = false;
		
		if(self::$_detect) {
			$k = wppepvn_hash_key(array(
				__CLASS__ . __METHOD__
				, $text
			));
			
			$lang = TempDataAndCacheFile::get_cache($k,true,true);
			
			if(null === $lang) {
				$lang = self::$_detect->detect($text);
				TempDataAndCacheFile::set_cache($k,$lang,true,true);
			}
		}
		
		if($lang && is_string($lang)) {
			if(isset(self::$_configs['lang_code'][$lang])) {
				return self::$_configs['lang_code'][$lang];
			} else {
				return $lang;
			}
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$lang = $wpExtend->get_bloginfo('language');
		
		return $lang;
	}
	
	
}
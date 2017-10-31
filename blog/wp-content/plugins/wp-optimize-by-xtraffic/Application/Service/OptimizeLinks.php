<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\Text
	,WpPepVN\DependencyInjection
	,WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
;

class OptimizeLinks
{
	const OPTION_NAME = 'optimize_links';
	
	protected static $_tempData = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
	}
    
	public function initFrontend() 
    {
        
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		$options = self::getOption();
		
		if(isset($options['optimize_links_enable']) && ('on' === $options['optimize_links_enable'])) {
			
			$wpExtend = $this->di->getShared('wpExtend');
			
			$addFilterTheContentStatus = true;
			
			if(true === $addFilterTheContentStatus) {
				if($wpExtend->is_single()) {
					if(isset($options['process_in_post']) && ('on' === $options['process_in_post'])) {
						
					} else {
						$addFilterTheContentStatus = false;
					}
				}
			}
			
			if(true === $addFilterTheContentStatus) {
				if($wpExtend->is_page()) {
					if(isset($options['process_in_page']) && ('on' === $options['process_in_page'])) {
						
					} else {
						$addFilterTheContentStatus = false;
					}
				}
			}
			
			if(true === $addFilterTheContentStatus) {
				if(isset($options['process_only_in_single']) && ('on' === $options['process_only_in_single'])) {
					if(
						!$wpExtend->is_single()
						&& !$wpExtend->is_page()
						&& !$wpExtend->is_singular()
						&& !$wpExtend->is_feed()
					) {
						$addFilterTheContentStatus = false;
					}
				}
			}
			
			if(true === $addFilterTheContentStatus) {
				if(isset($options['autolinks_exclude_url']) && ($options['autolinks_exclude_url'])) {
					$options['autolinks_exclude_url'] = trim($options['autolinks_exclude_url']);
					if($options['autolinks_exclude_url']) {
						$tmp = PepVN_Data::cleanPregPatternsArray($options['autolinks_exclude_url']);
						if(preg_match('#('.implode('|',$tmp).')#i',PepVN_Data::$defaultParams['parseedUrlFullRequest']['url_no_parameters'])) {
							$addFilterTheContentStatus = false;
						}
					}
				}
			}
			
			if(true === $addFilterTheContentStatus) {
				add_filter('the_content', array($this,'process_text'), $priorityLast);
			}
			
			if($wpExtend->is_feed()) {
				if(isset($options['process_in_feed']) && ('on' === $options['process_in_feed'])) {
					add_filter('the_content_feed', array($this,'process_text'), $priorityLast);
					if(isset($options['process_in_comment']) && ('on' === $options['process_in_comment'])) {
						add_filter('comment_text_rss', array($this,'process_text'), $priorityLast);
					}
				}
			}
			
			if(isset($options['process_in_comment']) && ('on' === $options['process_in_comment'])) {
				add_filter('comment_excerpt', array($this,'process_text'), $priorityLast);
				add_filter('comment_text', array($this,'process_text'), $priorityLast);
			}
			
		}
		
	}
	
	
	public function initBackend() 
    {
		
	}
	
	public static function getDefaultOption()
	{
		return array(
			'optimize_links_enable' => ''
			,'process_in_post' => ''
			,'link_to_postself' => ''
			,'process_in_page' => ''
			,'link_to_pageself' => ''
			,'process_in_comment' => ''
			,'process_in_feed' => ''
			,'exclude_heading' => ''
			,'link_to' => array()
			,'autolinks_case_sensitive' => ''
			,'autolinks_new_window' => ''
			,'process_only_in_single' => 'on'
			,'autolinks_exclude_url' => ''
			,'data_custom' => ''
			,'data_custom_url' => ''
			,'use_cats_as_keywords' => ''
			,'use_tags_as_keywords' => ''
			,'maxlinks' => 3
			,'external_nofollow' => ''
			,'external_new_window' => ''
			,'external_exclude_url' => ''
			,'nofollow_url' => ''
		);
	}
	
	public static function getOption($cache_status = true)
	{
	
		return WpOptions::get_option(self::OPTION_NAME,self::getDefaultOption(),array(
			'cache_status' => $cache_status
		));
		
	}
	
	public static function updateOption($data)
	{
		//$data = array_merge(self::getOption(false), $data);
		return WpOptions::update_option(self::OPTION_NAME,$data);
	}
	
	public function migrateOptions() 
	{
		
		$newOptions = array();
		
		$oldOptionID = 'WPOptimizeByxTraffic';
		$oldOptions = get_option($oldOptionID);
		
		$keyFromOldToNew = array(
			'optimize_links_enable' => 'optimize_links_enable'
			,'optimize_links_process_in_post' => 'process_in_post'
			,'optimize_links_allow_link_to_postself' => 'link_to_postself'
			,'optimize_links_process_in_page' => 'process_in_page'
			,'optimize_links_allow_link_to_pageself' => 'link_to_pageself'
			,'optimize_links_process_in_comment' => 'process_in_comment'
			,'optimize_links_process_in_feed' => 'process_in_feed'
			,'optimize_links_excludeheading' => 'exclude_heading'
			,'optimize_links_onlysingle' => 'process_only_in_single'
			,'optimize_links_casesens' => 'autolinks_case_sensitive'
			,'optimize_links_open_autolink_new_window' => 'autolinks_new_window'
			,'optimize_links_ignorepost' => 'autolinks_exclude_url'
			,'optimize_links_customkey' => 'data_custom'
			,'optimize_links_customkey_url' => 'data_custom_url'
			,'optimize_links_use_cats_as_keywords' => 'use_cats_as_keywords'
			,'optimize_links_use_tags_as_keywords' => 'use_tags_as_keywords'
			,'optimize_links_maxlinks' => 'maxlinks'
			,'optimize_links_nofolo' => 'external_nofollow'
			,'optimize_links_blanko' => 'external_new_window'
			,'optimize_links_nofolo_blanko_exclude_urls' => 'external_exclude_url'
			,'optimize_links_nofollow_urls' => 'nofollow_url'
		);
		
		if($oldOptions && is_array($oldOptions) && !empty($oldOptions)) {
			
			foreach($keyFromOldToNew as $oldKey => $newKey) {
				if(isset($oldOptions[$oldKey])) {
					$newOptions[$newKey] = $oldOptions[$oldKey];
					unset($oldOptions[$oldKey]);
				}
				
			}
		}
		
		if(!empty($newOptions)) {
			self::updateOption(array_merge(self::getOption(),$newOptions));
			self::getOption(false);
		}
		
		update_option($oldOptionID, $oldOptions);
		
	}
	
	private function _explode_and_clean_data($input_data) 
	{
		$resultData = array();
		
		$input_data = explode(',',$input_data);
		foreach($input_data as $value1) {
			$value1 = trim($value1); 
			if(isset($value1[0])) {
				$resultData[] = $value1;
			}
		}
		
		return $resultData;
		
	}
	
	private function _parse_data_custom_keywords($text, $input_configs)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$text
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);

		if(null === $resultData) {
			
			/*
				$resultData = array(
					'keyword' => array(
						link1
						,link2
					)
				);
			*/
			$resultData = array();
			
			$case_sensitive = false;
			if(isset($input_configs['case_sensitive']) && ('on' === $input_configs['case_sensitive'])) {
				$case_sensitive = true;
			}
			
			$text = (array)$text;
			$text = implode(PHP_EOL,$text);
			$text = trim($text);
			$text = explode(PHP_EOL,$text);

			foreach($text as $key1 => $value1) {
				
				$value1 = preg_replace('#\[\[[^\[\]]+\]\]#','',$value1);
				
				$value1 = trim($value1);
				
				$temp1 = $this->_explode_and_clean_data($value1);
				
				if(!empty($temp1)) {
					
					$keywords1 = array();
					
					$links1 = array();
					
					foreach($temp1 as $key2 => $value2) {
						
						unset($temp1[$key2]);
						
						$value2 = trim($value2);
						
						if(preg_match('#^https?://.+#i',$value2)) {
							$links1[] = $value2;
						} else {
							if(!$case_sensitive) {
								$keywords1[] = mb_strtolower($value2, 'UTF-8');
							} else {
								$keywords1[] = $value2;
							}
						}
					}
					
					if(!empty($keywords1)) {
						foreach($keywords1 as $key2 => $value2) {
							unset($keywords1[$key2]);
							
							if(!isset($resultData[$value2])) {
								$resultData[$value2] = array();
							}
							
							$resultData[$value2] = array_merge($resultData[$value2], $links1);
						}
					}
					
					unset($keywords1,$links1);
				}
			}
			
			if(!empty($resultData)) {
				foreach($resultData as $key1 => $value1) {
					if(!empty($value1)) {
						$value1 = PepVN_Data::cleanArray($value1);
						$value1 = array_unique($value1);
						$resultData[$key1] = $value1;
					}
				}
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $resultData);
			
		}

		return $resultData;
		
	}
	
	
	
	private function _fetch_raw_data_custom_keywords()
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1,true,true);
		
		if(null === $resultData) {
			
			$options = self::getOption();
			
			$wpExtend = $this->di->getShared('wpExtend');
		
			$resultData = '';
			
			$data_custom = trim($options['data_custom']); 
			
			unset($options['data_custom']);
			
			if (!empty($options['data_custom_url'])) {
				
				$remote = $this->di->getShared('remote');

				$rsDataCustomUrl = $remote->get($options['data_custom_url'],array(
					'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
				));
				
				if(false !== $rsDataCustomUrl) {
					$data_custom .= PHP_EOL . trim($rsDataCustomUrl);
				}
			}
			
			if(isset($options['use_tags_as_keywords']) && ('on' === $options['use_tags_as_keywords'])) {
				$tags = $wpExtend->get_tags();
				if ($tags) {
					foreach($tags as $tag) {
						if($tag) {
							if (isset($options['link_to']['tags'])) {//link_to_tags
								$data_custom .= PHP_EOL . $tag->name . ',' . $wpExtend->get_tag_link((int)$tag->term_id);
							} else {
								$data_custom .= PHP_EOL . $tag->name;
							}
						}
					}
				}
			}
			
			if(isset($options['use_cats_as_keywords']) && ('on' === $options['use_cats_as_keywords'])) {
				
				$categories = $wpExtend->get_categories();
				if ($categories) {
					foreach($categories as $category) {
						if($category) {
							if (isset($options['link_to']['cats'])) {//link_to_cats
								$data_custom .= PHP_EOL . $category->name . ',' . $wpExtend->get_category_link((int)$category->term_id);
							} else {
								$data_custom .= PHP_EOL . $category->name;
							}
						}
					}
				}
				
			}
			
			$data_custom = trim($data_custom);
			
			if (!empty($data_custom) ) {
				$resultData = $data_custom;
			}
			
			TempDataAndCacheFile::set_cache($keyCache1,$resultData,true,true);
		}
		
		return $resultData;
	}
	
	
	
	private function _get_data_custom_keywords()
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
		));
		
		$group_keywords1 = TempDataAndCacheFile::get_cache($keyCache1,true,true);
		
		if(null === $group_keywords1) {
			
			$options = self::getOption();
			
			$wpExtend = $this->di->getShared('wpExtend');
		
			$group_keywords1 = array();
			
			$data_custom = $this->_fetch_raw_data_custom_keywords();
			
			if (!empty($data_custom) ) {
				$group_keywords1 = $this->_parse_data_custom_keywords($data_custom, array(
					'case_sensitive' => $options['autolinks_case_sensitive']
				));
			}
			
			TempDataAndCacheFile::set_cache($keyCache1,$group_keywords1,true,true);
		}
		
		return $group_keywords1;
	}
	
	private function _get_terms_name()
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$wpExtend = $this->di->getShared('wpExtend');
			
			$terms = $wpExtend->getTermsName();
			
			foreach($terms as $key1 => $value1) {
				
				unset($terms[$key1]);
				
				if($value1 && !empty($value1)) {
					
					$value1 = implode(PHP_EOL,$value1);
					
					$value1 = Text::strtolower($value1);
					
					$value1 = explode(PHP_EOL,$value1);
					
					$value1 = PepVN_Data::cleanArray($value1);
					
					$value1 = array_unique($value1);
					
					$resultData[$key1] = $value1;
					
				}
				
				unset($value1,$key1);
				
			}
			
			TempDataAndCacheFile::set_cache($keyCache1,$resultData);
		}
		
		return $resultData;
	}
	
	private function _parseCustomAttributes($text)
	{
		$k = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $text
		));
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		}
		
		$resultData = array();
		
		preg_match('#\[\[([^\[\]]+)\]\]#',$text,$matched1);
		
		if(isset($matched1[1]) && $matched1[1]) {
			
			$matched1 = html_entity_decode($matched1[1], ENT_QUOTES | ENT_HTML401 | ENT_XML1 | ENT_XHTML | ENT_HTML5, 'UTF-8');
			
			$resultData = Utils::parseAttributesNamesAndValues($matched1);
			
		}
		
		self::$_tempData[$k] = $resultData;
		
		return $resultData;
	}
	
	
	private function _get_imploded_terms_name()
	{
		$keyCache = Utils::hashKey(array(
			__CLASS__ . __METHOD__
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache,false,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array(
			'category' => ''
			,'post_tag' => ''
		);
		
		$terms = $this->_get_terms_name();
		
		if(isset($terms['category']) && $terms['category'] && !empty($terms['category'])) {
			$tmp = implode('|',$terms['category']);
			$tmp = preg_replace('#[\|]+#is','|',$tmp);
			$tmp = preg_replace('#[\s \t]+#is',' ',$tmp);
			$tmp = trim($tmp);
			$resultData['category'] = '|'.$tmp.'|';
			unset($terms['category'],$tmp);
		}
		
		if(isset($terms['post_tag']) && $terms['post_tag'] && !empty($terms['post_tag'])) {
			$tmp = implode('|',$terms['post_tag']);
			$tmp = preg_replace('#[\|]+#is','|',$tmp);
			$tmp = preg_replace('#[\s \t]+#is',' ',$tmp);
			$tmp = trim($tmp);
			$resultData['post_tag'] = '|'.$tmp.'|';
			unset($terms['post_tag'],$tmp);
		}
		
		unset($terms);
		
		TempDataAndCacheFile::set_cache($keyCache,$resultData,false,true);
		
		return $resultData;
	}
	
	
	
	/*
		$keywords = array(
			'keyword' => weight
		);
	*/
	private function _parseWeightsOfKeywords($keywords)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $keywords
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1,true,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$data_custom = $this->_fetch_raw_data_custom_keywords();
		
		if(!empty($data_custom)) {
			
			$data_custom = (array)$data_custom;
			$data_custom = implode(PHP_EOL,$data_custom);
			$data_custom = trim($data_custom);
			$data_custom = Text::strtolower($data_custom);
			$data_custom = explode(PHP_EOL,$data_custom);
			
			$data_custom = PepVN_Data::cleanArray($data_custom);
			
			if(!empty($data_custom)) {
				
				$data_custom = array_unique($data_custom);
				
			}
		}
		
		if(!$data_custom || empty($data_custom) || !is_array($data_custom)) {
			$data_custom = array();
		}
		
		
		$get_imploded_terms_name = $this->_get_imploded_terms_name();
		
		foreach($keywords as $keyword => $weight) {
			
			$keyword_preg_quote = preg_quote($keyword,'#');
			
			$maxWeight = 1;
			
			if($maxWeight>0.7) {
				if($get_imploded_terms_name['post_tag']) {
					if(false !== stripos($get_imploded_terms_name['post_tag'],'|'.$keyword.'|')) {
						$maxWeight = 0.7;
					}
					
				}
			}
			
			if($maxWeight>0.5) {
				if($get_imploded_terms_name['category']) {
					if(false !== stripos($get_imploded_terms_name['category'],'|'.$keyword.'|')) {
						$maxWeight = 0.5;
					}
					
				}
			}
			
			foreach($data_custom as $key2 => $value2) {
				//if(preg_match('#(\,|\;)('.$keyword_preg_quote.')(\,|\;)#i',','.$value2.',')) 
				if(false !== stripos($value2,$keyword)) {
					
					$value2 = $this->_parseCustomAttributes($value2);
					
					if($value2 && isset($value2['w']) && $value2['w']) {
						$value2['w'] = (int)$value2['w'];
						if($value2['w'] > $maxWeight) {
							$maxWeight = $value2['w'];
						}
					}
				}
			}
			
			$keywords[$keyword] = ceil($weight * $maxWeight);
		}
		
		TempDataAndCacheFile::set_cache($keyCache1,$keywords,true,true);
		
		return $keywords;
		
	}
	
	
	public function process_text($text)
	{
		
		global $post;
		
		$currentPostId = 0;
		if(isset($post->ID) && $post->ID) {
			$currentPostId = (int)$post->ID;
		}
		
		if($currentPostId < 1) {
			return $text;
		}
		
		$options = self::getOption();
		
		$classMethodKey = Hash::crc32b(__CLASS__ . '_' . __METHOD__);
		
		$keyCacheProcessText = Utils::hashKey(array(
			$classMethodKey
			,$text
			,'keyCacheProcessText'
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCacheProcessText,false,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$analyzeText = $this->di->getShared('analyzeText');
		
		//$rsGetTerms = $wpExtend->getTermsByPostId($post->ID);
		
		$autolinks_case_sensitive = false;
		if(isset($options['autolinks_case_sensitive']) && ('on' === $options['autolinks_case_sensitive'])) {
			$autolinks_case_sensitive = true;
		}
		
		$maxlinks = (int)$options['maxlinks'];
		if($maxlinks<0) {
			$maxlinks = 0;
		}
		
		$currentPostType = '';
		if(isset($post->post_type) && $post->post_type) {
			$currentPostType = $post->post_type;
		}
		
		$patternsEscaped = array();
		
		$rsOne = PepVN_Data::escapeHtmlTagsAndContents($text,'a;pre;script;style;link;meta;input;textarea;iframe;video;audio;object');
		$text = $rsOne['content'];
		if(!empty($rsOne['patterns'])) {
			$patternsEscaped = array_merge($patternsEscaped, $rsOne['patterns']);
		}
		unset($rsOne);
		
		if(isset($options['exclude_heading']) && ('on' === $options['exclude_heading'])) {
			//escape a and h1 -> h6
			$rsOne = PepVN_Data::escapeHtmlTagsAndContents($text,'a;h1;h2;h3;h4;h5;h6');
			$text = $rsOne['content'];
			if(!empty($rsOne['patterns'])) {
				$patternsEscaped = array_merge($patternsEscaped, $rsOne['patterns']);
			}
			unset($rsOne);
		}
		
		$rsOne = PepVN_Data::escapeHtmlTags($text);
		$text = $rsOne['content'];
		if(!empty($rsOne['patterns'])) {
			$patternsEscaped = array_merge($patternsEscaped, $rsOne['patterns']);
		}
		unset($rsOne);
		
		$text = ' '.trim($text).' ';
		
		$group_keywords1 = $this->_get_data_custom_keywords();
		
		$numberTotalLinksAdded = 0;
		
		$targetPostTypesForSearch = array(
			'post'
			,'page'
		);
		
		if($group_keywords1) {
			
			if(!empty($group_keywords1)) {
				
				//calculate weights of keywords
				
				$group_keywords2 = array_keys($group_keywords1);
				
				if(!$autolinks_case_sensitive) {
					$group_keywords2 = implode(';',$group_keywords2);
					$group_keywords2 = PepVN_Data::strtolower($group_keywords2);
					$group_keywords2 = $analyzeText->frequencyOfAppearanceKeywordsInText($group_keywords2, PepVN_Data::strtolower($text));
				} else {
					$group_keywords2 = implode(';',$group_keywords2);
					$group_keywords2 = $analyzeText->frequencyOfAppearanceKeywordsInText($group_keywords2, $text);
				}
				
				if(!empty($group_keywords2)) {
					
					$group_keywords2 = $this->_parseWeightsOfKeywords($group_keywords2);
					
					arsort($group_keywords2);
					
					$numberTotalLinks = 0;
					
					foreach($group_keywords2 as $key1 => $value1) {
						
						if($maxlinks > 0) {
							if($numberTotalLinksAdded >= $maxlinks) {
								break;
							}
						}
						
						$targetKeywordClean = PepVN_Data::strtolower(PepVN_Data::cleanKeyword($key1));
						
						$checkStatus1 = false;
						
						$targetLink1 = false;
						
						if(isset($group_keywords1[$key1])) {
							
							$targetLink2 = false;
							$targetLinkTitle2 = false;
							
							if(($group_keywords1[$key1]) && (!empty($group_keywords1[$key1]))) {
								
								$targetLinks1 = $group_keywords1[$key1];
								
								if(!empty($targetLinks1)) {
									
									shuffle($targetLinks1);
									
									foreach($targetLinks1 as $key2 => $value2) {
										
										$value2 = trim($value2);
										
										if($value2) {
											
											if(!isset(PepVN_Data::$cacheData[$classMethodKey]['linksAdded'][$value2])) {
												$targetLink2 = $value2;
												$targetLinkTitle2 = $key1;
												break;
											}
										}
									}
									
								}
							}
							
							if(!$targetLink2) {
								
								if ($targetPostTypesForSearch && (!empty($targetPostTypesForSearch))) {
									
									$rsTwo = $analyzeText->search_posts(array(
										'keyword' => $key1
										,'post_types' => $targetPostTypesForSearch
									));
									
									foreach($rsTwo as $keyTwo => $valueTwo) {
										
										unset($rsTwo[$keyTwo]);
										
										$checkStatus2 = false;
										
										if($valueTwo['post_id'] != $currentPostId) {
											$checkStatus2 = true;
										} else {
											if ($currentPostType === 'post') {
												if ('on' === $options['link_to_postself']) {
													$checkStatus2 = true;
												}
											} else if ($currentPostType === 'page') {
												if ('on' === $options['link_to_pageself']) {
													$checkStatus2 = true;
												}
											
											}
										}
										
										if($checkStatus2) {
											if(isset(PepVN_Data::$cacheData[$classMethodKey]['linksAdded'][$valueTwo['post_link']])) {
												$checkStatus2 = false;
											}
										}
										
										if($checkStatus2) {
											$targetLink2 = $valueTwo['post_link'];
											$targetLinkTitle2 = $valueTwo['post_title'];
											break;
										}
										
									}
									
									unset($rsTwo);
									
								}
								
							}
							
							
							if($targetLink2) {
								
								$patterns2 = '#([\s ,;\.\t\'\"]+)('.Utils::preg_quote($key1).')([\s ,;\.\t\'\"]+)#';
								
								if(!$autolinks_case_sensitive) {
									$patterns2 .= 'i';
								}
								
								$replace2 = '\1<a href="'.$targetLink2.'" '.('on' === $options['autolinks_new_window'] ? ' target="_bank" ' : '').' itemprop="url" title="';
								
								if($targetLinkTitle2) {
									$targetLinkTitle2 = PepVN_Data::cleanKeyword($targetLinkTitle2);
								}
								
								if($targetLinkTitle2) {
									$replace2 .= $targetLinkTitle2.'">';
								} else {
									$replace2 .= '\2">';
								}
								$replace2 .= '<strong>\2</strong></a>\3';
								
								$text = preg_replace($patterns2, $replace2,  $text, 1, $count2);
								
								$count2 = (int)$count2;

								if($count2>0) {
									
									PepVN_Data::$cacheData[$classMethodKey]['linksAdded'][$targetLink2] = 1;
									PepVN_Data::$cacheData[$classMethodKey]['keywordsAdded'][$targetKeywordClean] = 1; 
									
									$rsTwo = PepVN_Data::escapeHtmlTagsAndContents($text,'a;strong');
									
									$text = $rsTwo['content'];
									
									if(!empty($rsTwo['patterns'])) {
										$patternsEscaped = array_merge($patternsEscaped,$rsTwo['patterns']);
									}
									
									unset($rsTwo);
									
									$numberTotalLinksAdded += $count2;
									
									if($maxlinks > 0) {
										if($numberTotalLinksAdded >= $maxlinks) {
											break;
										}
									}
									
								}
							}
							
						}
						
					}
					
				}
				
			}
		}
		
        if(!empty($patternsEscaped)) {
            $text = str_replace(array_values($patternsEscaped), array_keys($patternsEscaped), $text);
        }
		
        unset($patternsEscaped);
		
		$text = $this->process_attributes_links($text,$currentPostId);
		
		$text = trim($text);
		
		TempDataAndCacheFile::set_cache($keyCacheProcessText,$text,false,true); 
		
		return $text;
		
	}
	
	public function process_attributes_links($text,$currentPostId) 
	{
		$options = self::getOption();
		
		if(!isset($options['nofollow_url'])) {
			$options['nofollow_url'] = '';
		}
		$options['nofollow_url'] = (string)$options['nofollow_url'];
		$options['nofollow_url'] = trim($options['nofollow_url']);
		
		if(
			(isset($options['nofollow_url']) && $options['nofollow_url'])
			|| (isset($options['external_new_window']) && ('on' === $options['external_new_window']))
			|| (isset($options['external_nofollow']) && ('on' === $options['external_nofollow']))
		) {
			
			if(!isset($options['external_new_window'])) {
				$options['external_new_window'] = '';
			}
			
			if(!isset($options['external_exclude_url'])) {
				$options['external_exclude_url'] = '';
			}
			$options['external_exclude_url'] = (string)$options['external_exclude_url'];
			$options['external_exclude_url'] = trim($options['external_exclude_url']);
			
			$nofollow_url = PepVN_Data::cleanPregPatternsArray($options['nofollow_url']);
			$nofollow_url = implode('|',$nofollow_url);
			$nofollow_url = trim($nofollow_url);
			
			
			$external_exclude_url = PepVN_Data::cleanPregPatternsArray($options['external_exclude_url']);
			$external_exclude_url = implode('|',$external_exclude_url);
			$external_exclude_url = trim($external_exclude_url);
			
			$keyCacheProcessText = Utils::hashKey(array(
				__CLASS__ . __METHOD__
				,$text
				,$nofollow_url
				,$external_exclude_url
				,$options['external_new_window']
				,'process_text'
			));
			
			$tmp = TempDataAndCacheFile::get_cache($keyCacheProcessText,false,true); 
			
			if(null !== $tmp) {
				return $tmp; 
			}
			
			$patternSelfHost = '#^https?://'.Utils::preg_quote(PepVN_Data::$defaultParams['parseedUrlFullRequest']['host']).'.+#i';
			
			$arraySearchAndReplace = array();
			
			$rsOne = PepVN_Data::escapeHtmlTags($text);
			
			unset($rsOne['content']);
			if(!empty($rsOne['patterns'])) {
				
				foreach($rsOne['patterns'] as $keyOne => $valueOne) {
					unset($rsOne['patterns'][$keyOne]);
					
					if(preg_match('#<a[^>]+>#i',$keyOne,$matched1)) {
						
						if(preg_match('#href=(\'|")(https?://[^"\']+)\1#i',$keyOne,$matched2)) { 
							
							$oldValue = $keyOne;
							
							$newValue = $keyOne;
							
							if(isset($matched2[2]) && $matched2[2]) {
								
								$matched2[2] = trim($matched2[2]);
								
								$isNofollowStatus1 = false;
								
								$isExternalLinksStatus1 = false;
								
								if(!preg_match($patternSelfHost,$matched2[2])) {
									$isExternalLinksStatus1 = true;
								}
								
								if($nofollow_url && (preg_match('#('.$nofollow_url.')#i',$matched2[2],$matched3))) {
									$isNofollowStatus1 = true;
								} else {
									if($options['external_nofollow']) {
										if($isExternalLinksStatus1) {//is external links = true
											if($external_exclude_url && (preg_match('#('.$external_exclude_url.')#i',$matched2[2],$matched3))) {
											} else {
												$isNofollowStatus1 = true;
											}
										}
									}
								}
								
								
								if($options['external_new_window']) {
									if($isExternalLinksStatus1) {
										
										$newValue = preg_replace('#target=(\'|")([^"\']+)\1#i','',$newValue);
										
										$newValue = preg_replace('#<a(.+)#is', '<a target="_blank" \\1', $newValue);
										
									}
								}
								
								if($isNofollowStatus1) {
									if(preg_match('#rel=(\'|")([^"\']+)\1#i',$newValue,$matched3)) {
										$newValue = preg_replace('#(rel=)(\'|")([^"\']+)\2#i','\1\2\3 nofollow \2',$newValue);
									} else {
										$newValue = preg_replace('#<a(.+)#is', '<a rel="nofollow" \1', $newValue);
									}
									
								}
								
							}
							
							if($oldValue !== $newValue) {
								
								$arraySearchAndReplace[$oldValue] = $newValue;
							}
							
						}
						unset($matched2);
					}
					unset($matched2);
					unset($keyOne, $valueOne);
					
				}
				
				if(!empty($arraySearchAndReplace)) {
					$text = str_replace(array_keys($arraySearchAndReplace),array_values($arraySearchAndReplace), $text);
				}
				
				unset($arraySearchAndReplace);
			}
			
			TempDataAndCacheFile::set_cache($keyCacheProcessText, $text,false,true);
		}
		
		return $text;
		
	}
	
}


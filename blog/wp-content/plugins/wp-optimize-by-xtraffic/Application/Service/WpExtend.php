<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\TempData
	, WpPepVN\Text
	, WpPepVN\Utils
	, WpPepVN\Hash
	, WpPepVN\System
	, WpPepVN\Hook
	, WpPepVN\DependencyInjection
	, WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	, WPOptimizeByxTraffic\Application\Service\PepVN_Data
;

/*
*	This class extends class \WpPepVN\TempData so all result is cached in var _tempData
*/

class WpExtend extends TempData
{
	private static $_wpextend_tempData = array();
	
	private $di = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		parent::__construct();
	}
    
    public function init() 
    {
        
    }
	
    public static function cleanCache() 
    {
		self::$_wpextend_tempData = array();
		self::$_tempData = array();
	}
	
	private function _function_exists($name)
	{
		$k = 'fcex' . $name;
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			self::$_wpextend_tempData[$k] = System::function_exists($name);
			return self::$_wpextend_tempData[$k];
		}
	}
	
	private function _wpextend_call_methods($method, $args)
	{
		if($this->_function_exists($method)) {
			
			$key = $this->_tempData_hashKey(array($method, $args));
			
			$bag = $this->_bag;
			
			if(!isset(self::$_tempData[$bag][$key])) {
				self::$_tempData[$bag][$key] = call_user_func_array($method, $args);
				return self::$_tempData[$bag][$key];
			} else {
				return self::$_tempData[$bag][$key];
			}
			
		} else {
			return null;
		}
		
	}
	
	public function __call($method,$args)
    {
		return $this->_wpextend_call_methods($method,$args);
    }
    
	public function is_subdirectory_install()
	{
		if(strlen($this->site_url()) > strlen($this->home_url())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function json_encode($data)
	{
		if(System::function_exists('wp_json_encode')) {
			return wp_json_encode($data);
		} else {
			return json_encode($data);
		}
	}
	
	public function getABSPATH()
	{
		$path = ABSPATH;
		$siteUrl = $this->site_url();
		$homeUrl = $this->home_url();
		$diff = str_replace($homeUrl, '', $siteUrl);
		$diff = trim($diff,DIRECTORY_SEPARATOR);

		$pos = strrpos($path, $diff);

		if($pos !== false){
			$path = substr_replace($path, '', $pos, strlen($diff));
			$path = trim($path,DIRECTORY_SEPARATOR);
			$path = DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR;
		}
		
		return $path;
	}
	
	public function isCurrentUserCanManagePlugin()
	{
		$status = false;
		
		if($this->is_user_logged_in()) {
			
			if($this->is_multisite()) {
				if(
					$this->current_user_can('manage_network_plugins')
				) {
					$status = true;
				}
			} else {
				if($this->current_user_can('activate_plugins')) {
					if($this->current_user_can('delete_plugins')) {
						if($this->current_user_can('install_plugins')) {
							if($this->current_user_can('update_plugins')) {
								$status = true;
							}
						}
					}
				}
			}
		}
		
		return $status;
	}
	
	public static function getMySQLVersion() 
	{
		global $wpdb;
		
		$rsOne = $wpdb->get_results('SHOW VARIABLES LIKE "%version%"');
		
		if($rsOne) {
			
			foreach($rsOne as $valueOne) {

				if($valueOne) {
				
					if(isset($valueOne->Variable_name) && $valueOne->Variable_name && isset($valueOne->Value) && $valueOne->Value) {
						
						$variableName = $valueOne->Variable_name;
						
						$variableName = (string)$variableName;
						$variableName = trim($variableName);
						$variableName = strtolower($variableName);
						
						if('version' === $variableName1) {
							$variableValue = $valueOne->Value;
							return $variableValue;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	
	public function fetchAttachmentsId($text,$post_id)
	{
		$post_id = (int)$post_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $post_id
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1,false,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array();
		
		//<figure id="attachment_26"
		preg_match_all('#<figure[\s \t]+[^>]*id=(\'|\")attachment_([0-9]+)\1#is',$text,$matched1);
		
		if(isset($matched1[2][0]) && $matched1[2][0]) {
			$matched1 = $matched1[2];
			foreach($matched1 as $key1 => $value1) {
				unset($matched1[$key1]);
				$value1 = (int)$value1;
				if($value1>0) {
					$resultData[] = $value1;
				}
			}
		}
		
		//[caption id="attachment_26"
		preg_match_all('#[caption[\s \t]+[^\]]*id=(\'|\")attachment_([0-9]+)\1#is',$text,$matched1);
		
		if(isset($matched1[2][0]) && $matched1[2][0]) {
			$matched1 = $matched1[2];
			foreach($matched1 as $key1 => $value1) {
				unset($matched1[$key1]);
				$value1 = (int)$value1;
				if($value1>0) {
					$resultData[] = $value1;
				}
			}
		}
		
		$resultData = array_unique($resultData);
		
		TempDataAndCacheFile::set_cache($keyCache1,$resultData,false,true);
		
		return $resultData;
	}
	
	public function parseAttachmentData($attachment)
	{
		
		if(is_object($attachment)) {
			$attachment = (array)$attachment;
		}
		
		$attachment['ID'] = (int)$attachment['ID'];
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $attachment['ID']
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1,false,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$tmp = get_post($attachment['ID'], ARRAY_A);
		
		if($tmp) {
			$attachment = array_merge($attachment, $tmp);
		}
		unset($tmp);
		
		$attachment['attachment_url'] = $this->wp_get_attachment_url($attachment['ID']);
		
		$attachment['attachment_link'] = $this->get_attachment_link($attachment['ID']);
		
		$attachment['metadata'] = wp_get_attachment_metadata($attachment['ID'], true);
		
		$attachment['modified_time'] = get_post_modified_time('U',true,$attachment['ID'],false);
		
		TempDataAndCacheFile::set_cache($keyCache1,$attachment,false,true);
		
		return $attachment;
	}
	
	public function parsePostData($post)
	{
		if(is_object($post)) {
			$post = (array)$post;
		}
		
		$post['ID'] = (int)$post['ID'];
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $post['ID']
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1,false,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		//$post['post_content'] = $this->do_shortcode($post['post_content']);//should not use this
		
		$post['cacheTags'] = array();
		
		$post['cacheTags'][] = 'post_id-'.$post['ID'];
		
		if(!isset($post['post_excerpt']) || !$post['post_excerpt']) {
			$post['post_excerpt'] = $post['post_content'];
		}
		
		$post['post_excerpt'] = strip_shortcodes($post['post_excerpt']);
		
		$post['post_excerpt'] = Text::removeShortcode($post['post_excerpt'], ' ');
		
		$post['post_excerpt'] = Text::reduceWords($post['post_excerpt'],360,'...');
		
		$post['postPermalink'] = $this->get_permalink($post['ID']);
		
		$post['postImages'] = array();
		
		preg_match_all('#<img[^>]+src=(\'|\")([^\'\"]+)\1[^>]+\/?>#is',$post['post_content'],$matched1);
		if(isset($matched1[2]) && $matched1[2]) {
			foreach($matched1[2] as $key1 => $value1) {
				$tmp = Hash::crc32b($value1);
				$post['postImages'][$tmp] = array(
					'src' => $value1
				);
			}
		}
		
		$post['postThumbnailId'] = 0;
		$post['postThumbnailUrl'] = '';
		
		$post_thumbnail_id = $this->get_post_thumbnail_id($post['ID']);
		if($post_thumbnail_id) {
			$post['postThumbnailId'] = $post_thumbnail_id;
			$post_thumbnail_url = $this->wp_get_attachment_url($post_thumbnail_id);
			if($post_thumbnail_url) {
				$post['postThumbnailUrl'] = $post_thumbnail_url;
				$tmp = Hash::crc32b($post_thumbnail_url);
				$post['postImages'][$tmp] = array(
					'src' => $post_thumbnail_url
				);
			}
		}
		$post['postThumbnailId'] = (int)$post['postThumbnailId'];
		$post['postThumbnailUrl'] = trim($post['postThumbnailUrl']);
		
		$post['postAttachments'] = array();
		
		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_parent' => $post['ID']
		));

		if ( $attachments ) {
			foreach ( $attachments as $key1 => $attachment ) {
				unset($attachments[$key1]);
				$attachment = $this->parseAttachmentData($attachment);
				
				$post['postAttachments'][$attachment['ID']] = $attachment;
				
				unset($attachment);
			}
			
		}
		
		$rsOne = $this->fetchAttachmentsId($post['post_content'],$post['ID']);
		
		if(!empty($rsOne)) {
			
			foreach($rsOne as $key1 => $value1) {
				unset($rsOne[$key1]);
				if(!isset($post['postAttachments'][$value1])) {
					$tmp = $this->parseAttachmentData(array(
						'ID' => $value1
					));
					
					if($tmp) {
						$post['postAttachments'][$value1] = $tmp;
					}
					
					unset($tmp);
				}
				
			}
			
		}
		unset($rsOne);
		
		foreach($post['postAttachments'] as $key1 => $value1) {
			$tmp = Hash::crc32b($value1['attachment_url']);
			
			$post['postImages'][$tmp] = array(
				'src' => $value1['attachment_url']
			);
			
			$post['cacheTags'][] = 'post_id-'.$value1['ID'];
			
			unset($key1,$value1);
		}
		
		$post['postType'] = '';
		$tmp = $this->get_post_type($post['ID']);
		if($tmp) {
			$post['postType'] = $tmp;
		}
		
		$post['postFormat'] = '';
		$tmp = $this->get_post_format($post['ID']);
		if ($tmp) {
			$post['postFormat'] = $tmp;
		}
		
		$post['postTerms'] = $this->getTermsByPostId($post['ID']);
		if(!$post['postTerms'] || !is_array($post['postTerms'])) {
			$post['postTerms'] = array();
		}
		
		foreach($post['postTerms'] as $key1 => $value1) {
			$post['cacheTags'][] = 'term_id-'.$value1['term_id'];
			foreach($value1['parentTerms'] as $key2 => $value2) {
				$post['cacheTags'][] = 'term_id-'.$value2;
			}
		}
		
		$post['post_parent'] = (int)$post['post_parent'];
		if($post['post_parent']>0) {
			$post['cacheTags'][] = 'post_id-'.$post['post_parent'];
		}
		
		$post['postContentRawText'] = $post['post_content'];
		$post['postContentRawText'] = strip_tags($post['postContentRawText']);
		$post['postContentRawText'] = strip_shortcodes($post['postContentRawText']);
		$post['postContentRawText'] = Text::removeShortcode($post['postContentRawText']);
		$post['postContentRawText'] = Text::reduceLine($post['postContentRawText']);
		$post['postContentRawText'] = Text::reduceSpace($post['postContentRawText']);
		//unset($post['post_content']);
		
		if(Hook::has_filter('parse_post_data')) {
			$post = Hook::apply_filters('parse_post_data',$post);
		}
		
		$post['cacheTags'] = array_unique($post['cacheTags']);
		
		TempDataAndCacheFile::set_cache($keyCache1,$post,false,true);
		
		return $post;
	}
	
	public function getAndParsePostByPostId($post_id)
	{
		$post_id = (int)$post_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__
			,__METHOD__
			,$post_id 
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1,false,true);
		
		if(null === $resultData) {
			$resultData = $this->get_post($post_id);
			if($resultData && isset($resultData->ID) && $resultData->ID) {
				$resultData = $this->parsePostData($resultData);
			} else {
				$resultData = false;
			}
			
			TempDataAndCacheFile::set_cache($keyCache1,$resultData,false,true);
		}
		
		return $resultData;
	}
	
	public function getTermTaxonomyIdByTaxonomyAndPostId($post_id, $taxonomy)
	{
		$post_id = (int)$post_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$post_id
			,$taxonomy
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1,true,true);
		
		if(null === $resultData) {
			$resultData = array();
			
			$terms = $this->get_the_terms($post_id, $taxonomy);
			
			if($terms && !empty($terms)) {
				$resultData = wp_list_pluck($terms, 'term_taxonomy_id');
			}
			
			unset($terms);
			
			if($resultData && !empty($resultData)) {
				$resultData = array_values($resultData);
				$resultData = array_unique($resultData);
			}
			
			TempDataAndCacheFile::set_cache($keyCache1,$resultData,true,true);
		}
		
		return $resultData;
	}
	
	public function getTermsByPostId($post_id)
	{
		$post_id = (int)$post_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$post_id 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$groupsTerms = array();
			
			if($post_id > 0) {
				
				$groupsTerms['tags'] = $this->get_the_tags($post_id);
				$groupsTerms['category'] = $this->get_the_category($post_id);
				
				foreach($groupsTerms as $keyOne => $valueOne) {
					unset($groupsTerms[$keyOne]);
					if ($valueOne) {
						
						foreach($valueOne as $keyTwo => $valueTwo) {
							unset($valueOne[$keyTwo]);
							if($valueTwo) {
								
								if ($valueTwo && (!is_wp_error($valueTwo))) {
								
									if(isset($valueTwo->term_id) && $valueTwo->term_id) {
										$valueTwo->term_id = (int)$valueTwo->term_id;
										$linkTerm = '';
										if('tags' === $keyOne) {
											$linkTerm = $this->get_tag_link($valueTwo->term_id);
										} else if('category' === $keyOne) {
											$linkTerm = $this->get_category_link($valueTwo->term_id);
										}
										
										if($linkTerm) {
											$linkTerm = esc_url($linkTerm);
										}
										
										$rsTermData = array(
											'name' => $valueTwo->name
											,'term_id' => $valueTwo->term_id
											,'link' => $linkTerm
											,'linkTerm' => $linkTerm
											,'slug' => ''
											,'description' => $valueTwo->description
											,'taxonomy' => $valueTwo->taxonomy
											,'termType' => $keyOne
										);
										
										if(isset($valueTwo->slug)) {
											$rsTermData['slug'] = $valueTwo->slug;
										}
										
										$rsTermData['parentTerms'] = $this->getParentsTermsIdByTermIdAndTaxonomy($valueTwo->term_id, $valueTwo->taxonomy);
										
										$resultData[] = $rsTermData;
										
									}
									
								}
							}
						}
					}
				}
				
				unset($groupsTerms);
				
				$rsGetAllAvailableTaxonomies = $this->get_taxonomies(
					array(
					  'public'   => true
					)
					, 'objects'
					, 'and'
				);
				
				$arrayTaxonomiesNameExclude = array(
					'category'
					,'post_tag'
				);
				
				foreach($rsGetAllAvailableTaxonomies as $keyOne => $valueOne) {
					unset($rsGetAllAvailableTaxonomies[$keyOne]);
					if($valueOne) {
						if(isset($valueOne->name) && $valueOne->name) {
							if(!in_array($valueOne->name, $arrayTaxonomiesNameExclude)) {
								
								$rsGetTheTerms = $this->get_the_terms($post_id, $valueOne->name);
								
								if($rsGetTheTerms) {
									
									if(is_array($rsGetTheTerms) && (!empty($rsGetTheTerms))) {
										
										foreach($rsGetTheTerms as $keyTwo => $valueTwo) {
											unset($rsGetTheTerms[$keyTwo]);
											
											if($valueTwo) {
												
												if(isset($valueTwo->name) && $valueTwo->name) {
													
													$linkTerm = $this->get_term_link( $valueTwo->term_id, $valueTwo->taxonomy);
													
													$rsTermData = array(
														'name' => $valueTwo->name
														,'term_id' => $valueTwo->term_id
														,'link' => $linkTerm
														,'linkTerm' => $linkTerm
														,'slug' => ''
														,'description' => $valueTwo->description
														,'taxonomy' => $valueTwo->taxonomy
														,'termType' => $valueTwo->taxonomy
													);
													
													$rsTermData['parentTerms'] = $this->getParentsTermsIdByTermIdAndTaxonomy($valueTwo->term_id, $valueTwo->taxonomy);
													
													if(isset($valueTwo->slug)) {
														$rsTermData['slug'] = $valueTwo->slug;
													}
													
													$resultData[] = $rsTermData;
												}
											}
										}
									}
								}
							}
							
						}
					}
				}
				
			}
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
		}
		
		return $resultData;
	}
	
	public function getAndParseCategories($input_term_id = 0)
	{
		$input_term_id = (int)$input_term_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$input_term_id 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$terms = $this->get_categories($input_term_id);
			
			if ($terms) {
				foreach($terms as $index => $term) {
					unset($terms[$index]);
					if($term) {
						if(isset($term->term_id) && $term->term_id) {
							$term->term_id = (int)$term->term_id;
							$resultData[$term->name] = array(
								'name' => $term->name
								,'term_id' => $term->term_id
								,'categoryLink' => $this->get_category_link($term->term_id)
							);
						}
					}
				}
			}
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
			
		}
		
		return $resultData;
	}
	
	
	public function getAndParseTags($input_term_id = 0)
	{
		$input_term_id = (int)$input_term_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$input_term_id 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$terms = $this->get_tags($input_term_id);
			
			if ($terms) {
				foreach($terms as $index => $term) {
					unset($terms[$index]);
					if($term) {
						if(isset($term->term_id) && $term->term_id) {
							$term->term_id = (int)$term->term_id;
							$resultData[$term->name] = array(
								'name' => $term->name
								,'term_id' => $term->term_id
								,'tagLink' => $this->get_tag_link($term->term_id)
							);
							
						}
					}
				}
			}
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
			
		}
		
		return $resultData;
	}
	
	public function parseTaxonomy($taxonomy)
	{
		if(is_object($taxonomy)) {
			$taxonomy = (array)$taxonomy;
		}
		
		$taxonomy['term_taxonomy_id'] = (int)$taxonomy['term_taxonomy_id'];
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $taxonomy['term_taxonomy_id']
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		if($taxonomy && isset($taxonomy['term_id'])) {
			
			$termData = get_term( $taxonomy['term_id'], $taxonomy['taxonomy'], ARRAY_A );
			
			if($termData && isset($termData['term_id'])) {
				$termData['termLink'] = '';
				
				$term_link = get_term_link( $termData['term_id'], $taxonomy['taxonomy']);
				
				// If there was an error, continue to the next term.
				if ( !is_wp_error( $term_link ) ) {
					$termData['termLink'] = esc_url($term_link);
				}
				
				$args = array(
					'cache_results'		=> true,
					'post_type'			=> 'any',
					'orderby'			=> 'modified',
					'order'				=> 'DESC',
					'post_status'		=> 'publish',
					'has_password'		=> false,
					'posts_per_page'	=> 1,
					'offset'			=> 0,
					'tax_query' => array(
						array(
							'taxonomy' => $taxonomy['taxonomy'],
							'field'    => 'term_id',
							'terms'    => array( $taxonomy['term_id'] ),
						),
					),
				);
				
				$termData['latestPost'] = false;
				
				$posts = get_posts( $args );
				unset($args);
				
				if($posts) {
					if(is_array($posts) && !empty($posts)) {
						$termData['latestPost'] = (array)$posts[0];
					}
				}
				unset($posts);
				
				$taxonomy = array_merge($taxonomy,$termData);
				unset($termData);
				
			}
		}
		
		PepVN_Data::$cacheObject->set_cache($keyCache1, $taxonomy);
		
		return $taxonomy;
		
	}
	
	public function getAndParseTermByTermTaxonomyId($term_taxonomy_id)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $term_taxonomy_id
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		global $wpdb;
		
		$term_taxonomy_id = (int)$term_taxonomy_id;
		
		$tableTermTaxonomyName = $wpdb->term_taxonomy;
		
		$resultData = false;
		
		$taxonomy = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM `'.$tableTermTaxonomyName.'` WHERE `'.$tableTermTaxonomyName.'`.`term_taxonomy_id` = %d LIMIT 1',
                $term_taxonomy_id
            )
        );
		
		if($taxonomy && isset($taxonomy->term_taxonomy_id)) {
			$taxonomy = $this->parseTaxonomy($taxonomy);
			if($taxonomy && isset($taxonomy['term_taxonomy_id'])) {
				$resultData = $taxonomy;
			}
		}
		
		unset($taxonomy);
		
		PepVN_Data::$cacheObject->set_cache($keyCache1,$resultData);
		
		return $resultData;
	}
	
	public function getAndParseTermByTermIdAndTaxonomy($term_id, $taxonomy)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $term_id
			, $taxonomy
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = false;
		
		$term_id = (int)$term_id;
		if($term_id>0) {
			if($taxonomy) {
				
				$term = get_term_by('id', $term_id, $taxonomy, OBJECT);
				
				if($term) {
					if(isset($term->term_taxonomy_id) && $term->term_taxonomy_id) {
						$term_taxonomy_id = (int)$term->term_taxonomy_id;
						unset($term);
						
						if($term_taxonomy_id > 0) {
							
							$term = $this->getAndParseTermByTermTaxonomyId($term_taxonomy_id);
							if($term && isset($term['term_taxonomy_id'])) {
								$resultData = $term;
							}
							
						}
					}
				}
				
				unset($term);
			}
		}
		
		PepVN_Data::$cacheObject->set_cache($keyCache1,$resultData);
		
		return $resultData;
	}
	
	public function getTermsTaxonomiesByTermId($term_id)
	{
		
		$term_id = (int)$term_id;
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $term_id
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		global $wpdb;
		
		$tableTermTaxonomyName = $wpdb->term_taxonomy;
		
		$resultData = array();
		
		$taxonomies = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM `'.$tableTermTaxonomyName.'` WHERE `'.$tableTermTaxonomyName.'`.`term_id` = %d'
                , $term_id
            )
			, OBJECT
        );
		
		if($taxonomies && !empty($taxonomies)) {
			foreach($taxonomies as $key1 => $taxonomy) {
				unset($taxonomies[$key1]);
				
				if($taxonomy && isset($taxonomy->term_taxonomy_id)) {
					$taxonomy = $this->parseTaxonomy($taxonomy);
					if($taxonomy && isset($taxonomy['term_taxonomy_id'])) {
						$resultData[$taxonomy['term_taxonomy_id']] = $taxonomy;
					}
				}
				
				unset($taxonomy);
				
			}
		}
		
		PepVN_Data::$cacheObject->set_cache($keyCache1,$resultData);
		
		return $resultData;
	}
	
	
	public function getParentsTermsIdByTermIdAndTaxonomy($term_id, $taxonomy)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $term_id
			, $taxonomy
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array();
		
		$term_id = (int)$term_id;
		if($term_id>0) {
			if($taxonomy) {
			
				$term = get_term_by('id', $term_id, $taxonomy, OBJECT);
				
				if($term) {
					if(isset($term->parent) && $term->parent) {
						$parentId = (int)$term->parent;
						unset($term);
						
						if($parentId > 0) {
							$resultData[] = $parentId;
							$resultData = array_merge($resultData,$this->getParentsTermsIdByTermIdAndTaxonomy($parentId, $taxonomy));
						}
					}
				}
				
			}
		}
		
		$resultData = array_unique($resultData);
		
		PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
		
		return $resultData;
	}
	
	public function parseUserData($userData)
	{
		if(is_object($userData)) {
			$userData = (array)$userData;
		}
		
		$userData['ID'] = (int)$userData['ID'];
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $userData['ID']
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		if(is_object($userData['data'])) {
			$userData['data'] = (array)$userData['data'];
		}
		
		$userData['userMeta'] = get_user_meta($userData['ID']);
		
		$args = array(
			'cache_results'		=> true,
			'post_type'			=> 'any',
			'orderby'			=> 'modified',
			'order'				=> 'DESC',
			'post_status'		=> 'publish',
			'has_password'		=> false,
			'posts_per_page'	=> 1,
			'offset'			=> 0,
			'author'			=> $userData['ID'],
		);
		
		$userData['latestPost'] = false;

		$posts = get_posts( $args );
		unset($args);

		if($posts) {
			if(is_array($posts) && !empty($posts)) {
				$userData['latestPost'] = (array)$posts[0];
			}
		}
		unset($posts);
		
		$userData['authorPostsUrl'] = '';
		$tmp = $this->get_author_posts_url($userData['ID']);
		if($tmp) {
			$userData['authorPostsUrl'] = esc_url($tmp);
		}
		
		
		return $userData;
	}
	
	public function getUserBy($field, $value)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $field
			, $value
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$user = get_user_by( $field, $value );
		
		PepVN_Data::$cacheObject->set_cache($keyCache1, $user);
		
		return $user;
		
	}
	
	public function getUsers($args = array())
	{
		$current_blog_id = $this->get_current_blog_id();
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $current_blog_id
			, $args
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$args = array_merge(array(
			'blog_id'      => $current_blog_id,
			'role'         => '',
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'date_query'   => array(),        
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'ID',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '2',//Limit the total number of users returned.
			'count_total'  => false,
			'fields'       => 'all',
			'who'          => ''
		), $args);
		
		$users = new \WP_User_Query( $args );
		
		if($users) {
			if($args['count_total']) {
				$users = $users->get_total();
				$users = (int)$users;
			} else {
				$users = $users->get_results();
			}
		} else {
			$users = false;
		}
		
		
		PepVN_Data::$cacheObject->set_cache($keyCache1, $users);
		
		return $users;
		
	}
	
	public function getWpOptimizeByxTrafficPluginPromotionInfo()
	{
		$resultData = array();
		
		$resultData['data'] = array(
			'plugin_name' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME
			, 'plugin_version' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION
			, 'plugin_wp_url' => 'https://wordpress.org/plugins/wp-optimize-by-xtraffic/'
			, 'current_site_domain' => PepVN_Data::$defaultParams['fullDomainName']
			, 'current_time_mysql' => $this->current_time('mysql', $this->get_option('gmt_offset'))
		);
		
		$resultData['html_comment_text'] = '<!-- 
+ This website has been optimized by plugin "'.$resultData['data']['plugin_name'].'".
+ Served from : '.$resultData['data']['current_site_domain'].' @ '.$resultData['data']['current_time_mysql'].' by "'.$resultData['data']['plugin_name'].'".
+ Learn more here : '.$resultData['data']['plugin_wp_url'].'
-->';
		
		return $resultData;
	}
	
	public function isWpAjax()
	{
		$k = 'isWpAjax';
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			if(defined('DOING_AJAX') && DOING_AJAX) {
				self::$_wpextend_tempData[$k] = true;
			} else {
				self::$_wpextend_tempData[$k] = false;
			}
			
			return self::$_wpextend_tempData[$k];
		}
	}
	
	public function getTermsName()
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array();
		
		$rsGetAllAvailableTaxonomies = $this->get_taxonomies(
			array(
			  'public'   => true
			)
			, 'objects'
			, 'and'
		);
		
		foreach($rsGetAllAvailableTaxonomies as $keyOne => $valueOne) {
			
			unset($rsGetAllAvailableTaxonomies[$keyOne]);
			
			if($valueOne) {
				
				if(isset($valueOne->name) && $valueOne->name) {
					
					$terms = get_terms( $valueOne->name, array(
						'orderby'           => 'name', 
						'order'             => 'ASC',
						'hide_empty'        => false, 
					));
					
					if($terms) {
						
						if(is_array($terms) && !empty($terms) && !is_wp_error($terms)) {
							
							foreach($terms as $keyTwo => $valueTwo) {
								
								unset($terms[$keyTwo]);
								
								if($valueTwo) {
									
									if(isset($valueTwo->name) && $valueTwo->name) {
										
										$resultData[$valueOne->name][] = $valueTwo->name;
										
									}
								}
							}
						}
					}
					
					
				}
			}
		}
		
		PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
		
		return $resultData;
	}
	
	public function getAllPostTypes($output = 'objects')
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$output 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$args = array(
				'public'   => true,
				'_builtin' => true
			);

			//$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'

			$post_types = $this->get_post_types( $args, $output, $operator );
			
			if($post_types && is_array($post_types) && !empty($post_types)) {
				$resultData = array_merge($resultData, $post_types);
			}
			unset($post_types);
			
			$args = array(
				'public'   => true,
				'_builtin' => false
			);

			//$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'

			$post_types = $this->get_post_types( $args, $output, $operator );
			
			if($post_types && is_array($post_types) && !empty($post_types)) {
				$resultData = array_merge($resultData, $post_types);
			}
			unset($post_types);
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
		}
		
		return $resultData;
	}
	
	public function getAllTaxonomies(
		$output = 'objects'	//'names' or 'objects'
	) {
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$output 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$args = array(
				'public'   => true,
				'_builtin' => true
			); 
			//$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$taxonomies = get_taxonomies( $args, $output, $operator ); 
			
			if($taxonomies && is_array($taxonomies) && !empty($taxonomies)) {
				$resultData = array_merge($resultData, $taxonomies);
			}
			unset($taxonomies);
			
			$args = array(
				'public'   => true,
				'_builtin' => false
			); 
			//$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$taxonomies = get_taxonomies( $args, $output, $operator ); 
			
			if($taxonomies && is_array($taxonomies) && !empty($taxonomies)) {
				$resultData = array_merge($resultData, $taxonomies);
			}
			unset($taxonomies);
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
		}
		
		return $resultData;
	}
	
	public function getAllThemeSupportPostFormats()
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			//,$output 
		));
		
		$resultData = PepVN_Data::$cacheObject->get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			if ( $this->current_theme_supports( 'post-formats' ) ) {
				
				$post_formats = $this->get_theme_support( 'post-formats' );

				if ( isset($post_formats[0]) && is_array( $post_formats[0] ) ) {
					$resultData = $post_formats[0];
				}
				
				unset($post_formats);
			}
			
			$resultData = array_unique($resultData);
			
			PepVN_Data::$cacheObject->set_cache($keyCache1, $resultData);
			
		}
		
		return $resultData;
	}
	
	public function isRequestIsAutoSavePosts()
	{
		$resultData = false;
		
		if(isset($_POST['data']['wp_autosave']['post_id'])) {
			$resultData = true;
		}
		
		return $resultData;
	}
	
	public function getTypeOfPage($args = null, $args2 = null)
	{
		
		$k = Utils::hashKey(array(
			'gtpocrpg'
			,$args
			,$args2
		));
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			
			self::$_wpextend_tempData[$k] = null;
			
			if ( $this->is_singular($args) ) {//Is the query for an existing single post of any post type (post, attachment, page, ... )?
				
				self::$_wpextend_tempData[$k]['singular'] = 'singular';
				
				if ( $this->is_attachment($args) ) {
					self::$_wpextend_tempData[$k]['attachment'] = 'attachment';
				} else if ( $this->is_page($args) ) {//This Conditional Tag checks if Pages are being displayed. This is a boolean function, meaning it returns either TRUE or FALSE. This tag must be used BEFORE The Loop and does not work inside The Loop
					self::$_wpextend_tempData[$k]['page'] = 'page';
				} else if ( $this->is_single($args) ) {
					self::$_wpextend_tempData[$k]['post'] = 'post';
				}
				
			}
			
			if ( $this->is_date($args) ) {//if the page is a date based archive page. Similar to is_category()
				self::$_wpextend_tempData[$k]['date'] = 'date';
				self::$_wpextend_tempData[$k]['datebased'] = 'datebased';
			} 
			
			if ( $this->is_author($args) ) {
				self::$_wpextend_tempData[$k]['author'] = 'author';
			} 
			
			if ( $this->is_category($args) ) {
				self::$_wpextend_tempData[$k]['category'] = 'category';
			}
			
			if ( $this->is_tag($args) ) {
				self::$_wpextend_tempData[$k]['tag'] = 'tag';
			}
			
			if ( $this->is_feed($args) ) {
				self::$_wpextend_tempData[$k]['feed'] = 'feed';
			}

			if ( $this->is_tax($args,$args2) ) {//This Conditional Tag checks if a custom taxonomy archive page is being displayed. This is a boolean function, meaning it returns either TRUE or FALSE. Note that is_tax() returns false on category archives and tag archives. You should use is_category() and is_tag() respectively when checking for category and tag archives.
				self::$_wpextend_tempData[$k]['tax'] = 'tax';
			}
			
			if ( $this->is_post_type_archive($args) ) {
				self::$_wpextend_tempData[$k]['archive'] = 'archive';
			}
			
			if(null === $args) {
				
				if ( $this->is_day() ) {//if the page is a date based archive page.
					self::$_wpextend_tempData[$k]['day'] = 'day';
					self::$_wpextend_tempData[$k]['datebased'] = 'datebased';
				}
				
				if ( $this->is_month() ) {//if the page is a month based archive page.
					self::$_wpextend_tempData[$k]['month'] = 'month';
					self::$_wpextend_tempData[$k]['datebased'] = 'datebased';
				}

				if ( $this->is_year() ) {//if the page is a year based archive page.
					self::$_wpextend_tempData[$k]['year'] = 'year';
					self::$_wpextend_tempData[$k]['datebased'] = 'datebased';
				}

				if ( $this->is_archive() ) {//This Conditional Tag checks if any type of Archive page is being displayed. An Archive is a Category, Tag, Author or a Date based pages. This is a boolean function, meaning it returns either TRUE or FALSE.
					self::$_wpextend_tempData[$k]['archive'] = 'archive';
				}
				
				if ( $this->is_front_page() ) {//This is for what is displayed at your site's main URL.
					self::$_wpextend_tempData[$k]['front_page'] = 'front_page';
				}
				
				if ( $this->is_home() ) {//This is for what is displayed at your site's main URL.
					self::$_wpextend_tempData[$k]['home'] = 'home';
				}
				
				if ( $this->is_search() ) {//This Conditional Tag checks if search result page archive is being displayed. This is a boolean function, meaning it returns either TRUE or FALSE.
					self::$_wpextend_tempData[$k]['search'] = 'search';
				}
				
				if ( $this->is_404() ) {
					self::$_wpextend_tempData[$k]['error_404'] = 'error_404';
				}
			}
			
			if(null === self::$_wpextend_tempData[$k]) {
				self::$_wpextend_tempData[$k]['others'] = 'others';
			}
			
			return self::$_wpextend_tempData[$k];
		}
	}
	
	
	public function getCacheTagsForCurrentRequest()
	{
		
		$cacheTags = array();
		
		$device = $this->di->getShared('device');
		
		$cacheTags[] = 'usid-'.$this->get_current_user_id();	// current user id is request site
		
		$cacheTags[] = 'sw-'.$device->get_device_screen_width();	//current user screen's width (px) is request site
		
		global $wp_query;
		
		if(isset($wp_query) && $wp_query) {
			if(is_object($wp_query)) {
				if(isset($wp_query->queried_object) && $wp_query->queried_object) {
					if(is_object($wp_query->queried_object)) {
						
						if(isset($wp_query->queried_object->post_type)) {
							if(
								isset($wp_query->queried_object->ID) && $wp_query->queried_object->ID
							) {
								
								$tmp = $wp_query->queried_object->ID;
								$tmp = (int)$tmp;
								$keyTmp = 'psid-'.$tmp;		//post's author id. http://dev-wp.example.local/post_name/
								$cacheTags[] = $keyTmp;
								
								if(isset($wp_query->queried_object->post_author) && $wp_query->queried_object->post_author) {
									$tmp = $wp_query->queried_object->post_author;
									$tmp = (int)$tmp;
									$keyTmp = 'psautid-'.$tmp;	//post's author id. http://dev-wp.example.local/post_name/
									$cacheTags[] = $keyTmp;
								}
								
							}
						}
						
						if(isset($wp_query->queried_object->taxonomy)) {
							if(isset($wp_query->queried_object->term_id) && $wp_query->queried_object->term_id) {
								$tmp = $wp_query->queried_object->term_id;
								$tmp = (int)$tmp;
								$keyTmp = 'tmid-'.$tmp;	//term_id. Category, Tags, ... . Ex : http://dev-wp.example.local/category/name/
								$cacheTags[] = $keyTmp;
							}
						}
						
						if(isset($wp_query->queried_object->data)) {
							if(
								isset($wp_query->queried_object->data->user_login)
								&& isset($wp_query->queried_object->data->user_pass)
								&& isset($wp_query->queried_object->data->ID)
							) {
								$tmp = $wp_query->queried_object->data->ID;
								$tmp = (int)$tmp;
								$keyTmp = 'autid-'.$tmp;	//author id. Ex : http://wp.example.local/author/admin/
								$cacheTags[] = $keyTmp;
							}
							
						}
						
					}
				}
			}
		}
		
		$cacheTags[] = 'pmlh-'.Hash::crc32b($this->getCurrentPermalink());	//permalink_crc32b : permalink hash crc32 current request
		
		if(is_ssl()) {
			$cacheTags[] = 'https';
		} else {
			$cacheTags[] = 'http';
		}
		
		if(is_feed()) {
			$cacheTags[] = 'feed';
		}
		
		$typeOfCurrentPage = $this->getTypeOfPage();
		
		foreach($typeOfCurrentPage as $typeOfPage) {
			$keyTmp = 'tp-'.$typeOfPage;
			$cacheTags[] = $keyTmp;
		}
		
		$cacheTags = array_values($cacheTags);
		$cacheTags = array_unique($cacheTags);
		
		return $cacheTags;
	}
	
	public function getCurrentPermalink()
	{
		
		$k = crc32('getCurrentPermalink');
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			
			global $wp_query;
			
			self::$_wpextend_tempData[$k] = false;
			
			$typeOfCurrentPage = $this->getTypeOfPage();
			
			$tmp = 0;
			
			if(
				isset($typeOfCurrentPage['post'])
				|| isset($typeOfCurrentPage['page'])
			) {
				$tmp = $wp_query->get_queried_object();
				$tmp = $this->parsePostData($tmp);
				self::$_wpextend_tempData[$k] = $tmp['postPermalink'];
			} else if(
				isset($typeOfCurrentPage['category'])
			) {
				$tmp = $wp_query->get_queried_object();
				$tmp = $this->parseTaxonomy($tmp);
				self::$_wpextend_tempData[$k] = $tmp['termLink'];
			} else if(
				isset($typeOfCurrentPage['tag'])
			) {
				$tmp = $wp_query->get_queried_object();
				$tmp = $this->parseTaxonomy($tmp);
				self::$_wpextend_tempData[$k] = $tmp['termLink'];
			} else if(
				isset($typeOfCurrentPage['author'])
			) {
				$tmp = $wp_query->get_queried_object();
				$tmp = $this->parseUserData($tmp);
				self::$_wpextend_tempData[$k] = $tmp['authorPostsUrl'];
			}
			
			if(!self::$_wpextend_tempData[$k]) {
				if($this->is_user_logged_in()) {
					self::$_wpextend_tempData[$k] = PepVN_Data::$defaultParams['urlFullRequest'];
				} else {
					self::$_wpextend_tempData[$k] = PepVN_Data::$defaultParams['parseedUrlFullRequest']['url_no_parameters'];
					
					$agfk = crc32('getCurrentPermalink_arrayGetFields');
					
					if(!isset(self::$_wpextend_tempData[$agfk])) {
						self::$_wpextend_tempData[$agfk] = array(
							'attachment_id'
							,'p'
							,'preview'
							,'page'
							,'paged'
							,'tab'
							,'action'
							,'load'
							,'c'
							,'download'
							,'content'
							,'mode'
							,'id'
							,'theme'
							,'inline'
							,'type'
							,'widgets-access'
							,'post_type'
							,'step'
							,'backto'
							,'n'
							,'activated'
							,'link_cat'
							,'stylesheet'
							,'preview_id'
							,'preview_nonce'
							,'from'
							,'h'
							,'link_id'
							,'liveupdate'
							,'networkwide'
							,'doing_wp_cron'
							,'ref'
							,'redirect'
							,'import'
							,'repair'
							,'key'
							,'error'
							,'dt'
							,'user'
							,'menu'
							,'post'
							,'edit-menu-item'
							,'browser-uploader'
							,'rsd'
							,'revision'
							,'plugins'
							,'checked'
							,'post_status'
							,'_wp_http_referer'
							,'newuseremail'
							,'noapi'
							,'orderby'
							,'order'
							,'added'
							,'new'
							,'author'
							,'attachment-filter'
							,'default_password_nag'
							,'welcome'
							,'file'
							,'m'
							,'cat'
							,'message'
							,'from'
							,'replytocom'
							,'settings-updated'
							,'doing_wp_cron'
							,'hotkeys_highlight_first'
							,'hotkeys_highlight_last'
							,'edit'
							,'replytocom'
							,'w'
							,'s'
						);
						
						self::$_wpextend_tempData[$agfk] = array_unique(self::$_wpextend_tempData[$agfk]);
					}
					
					$validFields = array();
					
					foreach(self::$_wpextend_tempData[$agfk] as $field) {
						if(isset($_GET[$field])) {
							$validFields[] = $field.'='.rawurlencode($_GET[$field]);
						}
					}
					
					if(!empty($validFields)) {
						self::$_wpextend_tempData[$k] .= '?'.implode('&',$validFields);
					}
					unset($validFields);
				}
			}
			
			unset($tmp);
			
			return self::$_wpextend_tempData[$k];
		}
	}
	
	private function _getimagesize($filePath)
	{
		$k = Hash::crc32b(
			__CLASS__ . __METHOD__ . $filePath
		);
		
		$tmp = TempDataAndCacheFile::get_cache($k);
		
		if(null !== $tmp) {
			return $tmp;
		}
	
		$tmp = getimagesize($filePath);
		
		TempDataAndCacheFile::set_cache($k,$tmp);
		
		return $tmp;
		
	}
	
	public function getImageInfo($path)
	{
		$resultData = false;
		
		$imageFilePath = false;
		
		if(Utils::isImageUrl($path)) {
			if(Utils::isUrlSameDomain($path,PepVN_Data::$defaultParams['fullDomainName'],true)) {
				$imageFilePath = str_replace(WP_PEPVN_SITE_UPLOADS_URL,WP_PEPVN_SITE_UPLOADS_DIR,$path);
			}
		} else if(Utils::isImageFilePath($path)) {
			$imageFilePath = $path;
		}
		
		if($imageFilePath) {
			if(is_file($imageFilePath)) {
				if(is_readable($imageFilePath)) {
					$resultData = $this->_getimagesize($imageFilePath);
					$resultData['width'] = (int)$resultData[0];
					$resultData['height'] = (int)$resultData[1];
				}
			}
		}
		
		return $resultData;
		
	}
	
	public function get_woocommerce_urls()
	{
		$keyCache1 = Hash::crc32b(
			__CLASS__ . __METHOD__
		);
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array();
		
		if ( System::class_exists( '\WooCommerce' ) ) {
		
			if(System::function_exists('woocommerce_get_page_id')) {
				
				global $woocommerce;
				
				if(isset($woocommerce) && $woocommerce) {
					
					if(isset($woocommerce->cart) && $woocommerce->cart) {
						
						if(
							method_exists($woocommerce->cart,'get_cart_url')
							&& method_exists($woocommerce->cart,'get_checkout_url')
						) {
							
							$resultData['cart_url'] = $woocommerce->cart->get_cart_url();
							$resultData['checkout_url'] = $woocommerce->cart->get_checkout_url();
							
							$pageId1 = $this->woocommerce_get_page_id( 'shop' );
							if($pageId1) {
								$pageId1 = (int)$pageId1;
								if($pageId1>0) {
									$resultData['shop_page_url'] = $this->get_permalink( $pageId1 );
								}
							}
							
							
							$pageId1 = $this->get_option( 'woocommerce_myaccount_page_id' );
							if($pageId1) {
								$pageId1 = (int)$pageId1;
								if($pageId1>0) {
									$resultData['myaccount_page_url'] = $this->get_permalink( $pageId1 );
									$resultData['logout_url'] = $this->wp_logout_url( $resultData['myaccount_page_url'] );
								}
							}
							
							$pageId1 = $this->woocommerce_get_page_id( 'pay' );
							if($pageId1) {
								$pageId1 = (int)$pageId1;
								if($pageId1>0) {
									$resultData['payment_page_url'] = $this->get_permalink( $pageId1 ); 
								}
							}
							
						}
					}
					
					
				}
				
			}
			
		}
		
		TempDataAndCacheFile::set_cache($keyCache1,$resultData);
		
		return $resultData;
		
	}
	
	
	public function currentDate($type = 'mysql', $gmt = 0)
	{
		
		$k = Hash::crc32b('currentDate'.$type.$gmt);
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			
			$tmp = current_time( $type, $gmt);
			$tmp = explode(' ',$tmp);
			
			self::$_wpextend_tempData[$k] = $tmp[0];
			
			return self::$_wpextend_tempData[$k];
		}
	}
	
	public function isPagenow($pagesnow) 
	{
		return wppepvn_is_pagenow($pagesnow);
	}
	
	public function isLoginPage() 
	{
		
		$k = 'isLoginPage';
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			
			self::$_wpextend_tempData[$k] = $this->isPagenow(array('wp-login.php', 'wp-register.php'));
			
			return self::$_wpextend_tempData[$k];
		}
		
	}
	
	public function getPaged() 
	{
		$k = Hash::crc32b('getPaged');
		
		if(isset(self::$_wpextend_tempData[$k])) {
			return self::$_wpextend_tempData[$k];
		} else {
			
			if ( get_query_var('paged') ) { 
				$paged = get_query_var('paged'); 
			} elseif ( get_query_var('page') ) { 
				$paged = get_query_var('page'); 
			} else { 
				$paged = 1; 
			}
			
			$paged = (int)$paged;
			
			if($paged < 1) {
				$paged = 1;
			}
			
			self::$_wpextend_tempData[$k] = $paged;
			
			return self::$_wpextend_tempData[$k];
		}
	}
	
}
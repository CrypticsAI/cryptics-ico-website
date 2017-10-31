<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\Text
	, WpPepVN\Utils
	, WpPepVN\System
	, WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	, WPOptimizeByxTraffic\Application\Service\PepVN_Data
;

class TemplateReplaceVars
{
	private static $_tempData = array();
	
	private $di = false;
	
	public function __construct($di) 
    {
		$this->di = $di;
	}
    
	private function _parseCustomTemplateOfPosts($input_parameters)
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$classMethodKey = crc32(__CLASS__ . __METHOD__);
		
		$resultData = false;
		
		if(isset($input_parameters['text'])) {
			unset($input_parameters['text']);
		}
		
		$keyCache1 = Utils::hashKey(array(
			$classMethodKey
			, $input_parameters
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array();
			
			$arrayPatterns1 = array(
				'separator' => '-'
				
				,'post_title' => ''
				,'post_excerpt' => ''
				
				,'post_parent_title' => ''
				,'post_parent_excerpt' => ''
				
				,'category_name' => ''
				,'category_description'
				
				,'tags_name' => ''
				,'tags_description' => ''
				
				,'product_cat' => ''	//woocommerce
				,'product_tag' => ''	//woocommerce
				
				,'attachment_title' => ''
				,'attachment_description' => ''
				,'attachment_caption' => ''
				
				,'first_post_title' => ''
				,'first_post_excerpt' => ''
				
				,'author_username' => ''
				,'author_first_name' => ''
				,'author_last_name' => ''
				,'author_nickname' => ''
				,'author_display_name' => ''
				,'author_description' => ''
				
				,'site_title' => $wpExtend->get_bloginfo( 'name' )
				,'site_description' => $wpExtend->get_bloginfo( 'description' )
				,'site_domain' => PepVN_Data::$defaultParams['fullDomainName']
				,'site_url' => $wpExtend->site_url()
				,'home_url' => esc_url($wpExtend->home_url('/'))
			);
			
			if(isset($input_parameters['patterns']) && $input_parameters['patterns']) {
				$input_parameters['patterns'] = (array)$input_parameters['patterns'];
				if(!empty($input_parameters['patterns'])) {
					foreach($input_parameters['patterns'] as $key1 => $value1) {
						$key1 = trim($key1);
						if($key1) {
							$value1 = trim($value1);
							if($value1) {
								$arrayPatterns1[$key1] = $value1;
							}
						}
					}
				}
			}
			
			$postData = false;
			
			$userData = false;
			
			$attachmentData = false;
			
			if(isset($input_parameters['post_id']) && $input_parameters['post_id']) {
				$input_parameters['post_id'] = (int)$input_parameters['post_id'];
				if($input_parameters['post_id'] > 0) {
					$postData = $wpExtend->getAndParsePostByPostId($input_parameters['post_id']);
				}
			}
			
			if(isset($input_parameters['user_id']) && $input_parameters['user_id']) {
				$input_parameters['user_id'] = (int)$input_parameters['user_id'];
				if($input_parameters['user_id'] > 0) {
					$userData = $wpExtend->getUserBy('id', $input_parameters['user_id']);
					if($userData && isset($userData->ID)) {
						$userData = $wpExtend->parseUserData($userData);
					}
				}
			}
			
			if(isset($input_parameters['attachment_id']) && $input_parameters['attachment_id']) {
				$input_parameters['attachment_id'] = (int)$input_parameters['attachment_id'];
				if($input_parameters['attachment_id'] > 0) {
					$attachmentData = $wpExtend->parseAttachmentData(array('ID' => $input_parameters['attachment_id']));
				}
			}
			
			$rsGetTerms = array();
			
			if($postData) {
				$rsGetTerms = $wpExtend->getTermsByPostId($postData['ID']);
			}
			
			foreach($arrayPatterns1 as $key1 => $value1) {
				
				if(empty($value1)) {
					
					if( ('post_title' === $key1) && isset($postData['post_title']) ) {
						$value1 = $postData['post_title'];
					} else if( ('post_excerpt' === $key1) && isset($postData['post_excerpt']) ) {
						$value1 = $postData['post_excerpt'];
					} else if( 
						(
							('post_parent_title' === $key1)
							|| ('post_parent_excerpt' === $key1)
						) 
						&& isset($postData['post_parent']) 
						&& $postData['post_parent']
					) {
						$post_parent_id = (int)$postData['post_parent'];
						if($post_parent_id>0) {
							$post_parent_data = $wpExtend->getAndParsePostByPostId($post_parent_id);
							if($post_parent_data && isset($post_parent_data['ID']) && $post_parent_data['ID']) {
								if( ('post_parent_title' === $key1) && isset($post_parent_data['post_title']) ) {
									$value1 = $post_parent_data['post_title'];
								} else if( ('post_parent_excerpt' === $key1) && isset($post_parent_data['post_excerpt']) ) {
									$value1 = $post_parent_data['post_excerpt'];
								}
							}
						}
					} else if( 
						(
							('author_username' === $key1)
							|| ('author_first_name' === $key1)
							|| ('author_last_name' === $key1)
							|| ('author_nickname' === $key1)
							|| ('author_display_name' === $key1)
							|| ('author_description' === $key1)
						) 
						&& $userData
						&& isset($userData['ID']) 
						&& $userData['ID']
					) {
						if('author_username' === $key1) {
							$value1 = $userData['data']['user_login'];
						} else if('author_display_name' === $key1) {
							$value1 = $userData['data']['display_name'];
						} else if('author_first_name' === $key1) {
							if(isset($userData['userMeta']['first_name'][0])) {
								$value1 = $userData['userMeta']['first_name'][0];
							}
						} else if('author_last_name' === $key1) {
							if(isset($userData['userMeta']['last_name'][0])) {
								$value1 = $userData['userMeta']['last_name'][0];
							}
						} else if('author_nickname' === $key1) {
							if(isset($userData['userMeta']['nickname'][0])) {
								$value1 = $userData['userMeta']['nickname'][0];
							}
						} else if('author_description' === $key1) {
							if(isset($userData['userMeta']['description'][0])) {
								$value1 = $userData['userMeta']['description'][0];
							}
						}
					} else if( 
						(
							('attachment_title' === $key1)
							|| ('attachment_description' === $key1)
							|| ('attachment_caption' === $key1)
						) 
						&& $attachmentData
						&& isset($attachmentData['ID']) 
						&& $attachmentData['ID']
					) {
						if('attachment_title' === $key1) {
							$value1 = $attachmentData['post_title'];
						} else if('attachment_description' === $key1) {
							$value1 = $attachmentData['post_content'];
						} else if('attachment_caption' === $key1) {
							$value1 = $attachmentData['post_excerpt'];
						}
					} else {
						
						$rsTwo = array();
						
						foreach($rsGetTerms as $key2 => $value2) {
							if($value2) {
								if(isset($value2['term_id']) && $value2['term_id']) {
									$termTypeTmp = $value2['termType'].'_';
									if(false !== strpos($key1,$termTypeTmp)) {
										$key1Tmp = str_replace($termTypeTmp,'',$key1);
										$key1Tmp = trim($key1Tmp);
										if($key1Tmp && isset($value2[$key1Tmp])) {
											$rsTwo[] = $value2[$key1Tmp];
										}
									}
									
								}
							}
						}
						
						$rsTwo = PepVN_Data::cleanArray($rsTwo);
						
						if(!empty($rsTwo)) {
							$rsTwo = array_unique($rsTwo);
							$value1 = implode(' ',$rsTwo);
							unset($rsTwo);
						}
					}
				}
				
				$value1 = Text::decodeText($value1);
				$value1 = strip_tags($value1);
				$value1 = Text::removeQuotes($value1);
				
				$value1 = Text::reduceLine($value1);
				$value1 = Text::reduceSpace($value1);
				
				$value1 = trim($value1);
				
				$resultData['[['.$key1.']]'] = $value1;
				
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $resultData);
			
		}
		
		return $resultData;
	}
	
	
	public function parseCustomTemplate($input_parameters)
	{
		$rsOne = $this->_parseCustomTemplateOfPosts($input_parameters);
		if($rsOne && is_array($rsOne) && !empty($rsOne)) {
			$input_parameters['text'] = str_replace(array_keys($rsOne), array_values($rsOne), $input_parameters['text']);
		}
		
		/*
		$separatorValue = '';
		
		if(isset($rsOne['[[separator]]'])) {
			$separatorValue = $rsOne['[[separator]]'];
		}
		
		preg_replace('#[\s \t '.preg_quote($separatorValue,'#').']+#is',
		*/
		
		$input_parameters['text'] = trim($input_parameters['text']);
		
		return $input_parameters['text'];
	}
	
}
<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\System
	,WpPepVN\Text
	,WpPepVN\DependencyInjection
	,WPOptimizeByxTraffic\Application\Service\PepVN_Images
;

class OptimizeImages
{
	const OPTION_NAME = 'optimize_images';
	
	protected static $_tempData = array();
	
	public $di;
	
	private $_isSystemReadyToHandleImagesStatus = false;
	
	private $_folderStorePath = '';
	private $_folderStoreUrl = '';
	private $_folderCachePath = '';
	private $_folderFontPath = '';
	
	protected $imageExtensionsAllow = array(
		'jpg'
		,'png'
		,'gif'
		,'jpeg'
	);
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if($wpExtend->is_admin()) {
			$adminNotice = $this->di->getShared('adminNotice');
		}
		
		if(System::function_exists('gd_info')) {
			
			$this->_isSystemReadyToHandleImagesStatus = true;
			
			$this->_folderStorePath = WP_UPLOADS_PEPVN_DIR;
			
			$this->_folderStoreUrl = WP_UPLOADS_PEPVN_URL;
			
			$this->_folderCachePath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'images' . DIRECTORY_SEPARATOR ;
			
			$this->_folderFontPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR;
			
			$arrayFoldersPathNeedCheckReadableAndWritable = array(
				$this->_folderStorePath
				, $this->_folderCachePath
			);
			
			foreach($arrayFoldersPathNeedCheckReadableAndWritable as $value1) {
				if($value1) {
					
					if(is_dir($value1) && is_readable($value1) && is_writable($value1)) {
					} else {
						System::mkdir($value1);
						
						if(is_dir($value1) && is_readable($value1) && is_writable($value1)) {
							
						} else {
							
							$this->_isSystemReadyToHandleImagesStatus = false;
							
							if($wpExtend->is_admin() && $wpExtend->isCurrentUserCanManagePlugin()) {
								$adminNotice->add_notice(
									sprintf(
										'%s : '
										. 'Your server must set "readable" & "writable" folder "%s" to use "Optimize Image File"'
										
										,'<b>'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME.'</b>'
										, $value1
									)
									,'error'
								);
							}
						}
					}
					
				}
			}
			
		} else {
			if($wpExtend->is_admin() && $wpExtend->isCurrentUserCanManagePlugin()) {
				$adminNotice->add_notice(
					sprintf(
						'%s : '
						. 'Your server need to install the GD library to use %s'
						. '(%s details here %s)'
						
						,'<b>'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME.'</b>'
						,'"<b>Optimize Image File</b>"'
						,'<a href="http://php.net/manual/en/book.image.php" target="_blank"><b>'
						,'</b></a>'
					)
					,'error'
				);
			}
		}
		
	}
    
	public function initFrontend() 
    {
        
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		$options = self::getOption();
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if(
			(isset($options['optimize_images_optimize_image_file_enable']) && ('on' === $options['optimize_images_optimize_image_file_enable']))
			|| (isset($options['optimize_images_alttext']) && ($options['optimize_images_alttext']))
			|| (isset($options['optimize_images_titletext']) && ($options['optimize_images_titletext']))
		) {
			add_filter('the_content', array(&$this,'process_text'), $priorityLast);
			
			add_filter( 'post_thumbnail_html', array(&$this,'wp_add_filter_post_thumbnail_html'), $priorityLast, 5);
		}
		
	}
	
	public function initBackend() 
    {
		
	}
	
	public function wp_add_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) 
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			, $html
			, $post_id
			, $post_thumbnail_id
			, $size
			, $attr
		)); 
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if(!$html) {
			$html = '';
		}
		$html = (string)$html;
		
		if(!$post_thumbnail_id) {
			$post_thumbnail_id = 0;
		}
		$post_thumbnail_id = (int)$post_thumbnail_id;
		
		if($post_thumbnail_id>0) {
		} else {
			if($post_id) {
				$post_id = (int)$post_id;
				if($post_id>0) {
					$post_thumbnail_id = $wpExtend->get_post_thumbnail_id($post_id);
					$post_thumbnail_id = (int)$post_thumbnail_id;
				}
			}
		}
		
		if($post_thumbnail_id>0) {
			
			$attachment_metadata = $wpExtend->wp_get_attachment_metadata($post_thumbnail_id);
			
			if($attachment_metadata && is_array($attachment_metadata)) {
				
				$image_src = $wpExtend->wp_get_attachment_image_src($post_thumbnail_id, $size);
				
				if($image_src && is_array($image_src)) {
					
					$imgName = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
					
					$imgInfo = pathinfo($image_src[0]);
					if(isset($imgInfo['filename'])) {
						$imgName = $imgInfo['filename'];
					}
					
					$max_size_image_tag = false;
					if(!empty($html)) {
						$max_size_image_tag = $this->get_max_size_image_tag($html);
					}
					
					$processImageOptions1 = array(
						'optimized_image_file_name' => $imgName
						,'original_image_src' => $image_src[0]
						,'resize_max_width' => $image_src[1]
						,'resize_max_height' => $image_src[2]
						,'action' => 'do_process_image'
					);
					
					if(isset($max_size_image_tag['width']) && ($max_size_image_tag['width']>0)) {
						$processImageOptions1['resize_max_width'] = $max_size_image_tag['width'];
					}
					
					if(isset($max_size_image_tag['height']) && ($max_size_image_tag['height']>0)) {
						$processImageOptions1['resize_max_height'] = $max_size_image_tag['height'];
					}
					
					$device_screen_width = $this->_check_get_screen_width();
					
					if($device_screen_width>0) {
						if($processImageOptions1['resize_max_width'] > $device_screen_width) {
							if($processImageOptions1['resize_max_height']>0) {
								$processImageOptions1['resize_max_height'] = ($device_screen_width * $processImageOptions1['resize_max_height']) / $processImageOptions1['resize_max_width'];
							}
							$processImageOptions1['resize_max_width'] = $device_screen_width;
						}
					}
					
					$processImageOptions1['resize_max_width'] = (int)$processImageOptions1['resize_max_width'];
					$processImageOptions1['resize_max_height'] = (int)$processImageOptions1['resize_max_height'];
					
					$rsProcessImage1 = $this->process_image($processImageOptions1);
					
					unset($processImageOptions1);
					
					if($rsProcessImage1['image_optimized_file_url']) {
						$image_src[0] = $rsProcessImage1['image_optimized_file_url'];
						
					}
					
					if(strlen($html)>0) {
						$html = preg_replace('#\s*?src=(\'|\")[^\'\"]*?\1#is',' src=$1'.$image_src[0].'$1',$html);
					} else {
						$imgClass = 'wp-post-image';
						if(!is_array($size)) {
							$imgClass .= ' attachment-'.(string)$size;
						}
						$html = ' <img width="'.$image_src[1].'" height="'.$image_src[2].'" src="'.$image_src[0].'" class="'.$imgClass.'" alt="'.$attachment_metadata['image_meta']['caption'].' - '.$attachment_metadata['image_meta']['title'].'"> ';
					}
					
					if(
						isset($rsProcessImage1['image_optimized_width'])
						&& isset($rsProcessImage1['image_optimized_height'])
					) {
						$html = $this->check_set_size_image_tag($html, array(
							'width' => $rsProcessImage1['image_optimized_width']
							,'height' => $rsProcessImage1['image_optimized_height']
						),true);
					}
					
				}
			}
			
		}
		
		$hook = $this->di->getShared('hook');
		if($hook->has_filter('post_thumbnail_html')) {
			$html = $hook->apply_filters('post_thumbnail_html', $html, $post_id, $post_thumbnail_id, $size, $attr);
		}
		
		TempDataAndCacheFile::set_cache($keyCache1,$html);
		
		return $html;
	}
	
	
	public function process_image_tag($img_tag) 
	{
		$parseImgTag = Utils::parseAttributesHtmlTag($img_tag);
		
		if(isset($parseImgTag['attributes']['src']) && $parseImgTag['attributes']['src']) {
			
			$imgSrc = $parseImgTag['attributes']['src'];
			
			$imgName = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
			
			$imgInfo = pathinfo($imgSrc);
			if(isset($imgInfo['filename'])) {
				$imgName = $imgInfo['filename'];
			}
			
			$max_size_image_tag = $this->get_max_size_image_tag($img_tag);
			
			$processImageOptions1 = array(
				'optimized_image_file_name' => $imgName
				,'original_image_src' => $imgSrc
				,'resize_max_width' => (isset($parseImgTag['attributes']['width']) ? $parseImgTag['attributes']['width'] : 0)
				,'resize_max_height' => (isset($parseImgTag['attributes']['height']) ? $parseImgTag['attributes']['height'] : 0)
				,'action' => 'do_process_image'
			);
			
			
			
			if(isset($max_size_image_tag['width']) && ($max_size_image_tag['width']>0)) {
				$processImageOptions1['resize_max_width'] = $max_size_image_tag['width'];
			}
			
			if(isset($max_size_image_tag['height']) && ($max_size_image_tag['height']>0)) {
				$processImageOptions1['resize_max_height'] = $max_size_image_tag['height'];
			}
			
			$device_screen_width = $this->_check_get_screen_width();
			
			if($device_screen_width>0) {
				if($processImageOptions1['resize_max_width'] > $device_screen_width) {
					if($processImageOptions1['resize_max_height']>0) {
						$processImageOptions1['resize_max_height'] = ($device_screen_width * $processImageOptions1['resize_max_height']) / $processImageOptions1['resize_max_width'];
					}
					$processImageOptions1['resize_max_width'] = $device_screen_width;
				}
			}
			
			$processImageOptions1['resize_max_width'] = (int)$processImageOptions1['resize_max_width'];
			$processImageOptions1['resize_max_height'] = (int)$processImageOptions1['resize_max_height'];
			
			$rsProcessImage1 = $this->process_image($processImageOptions1);
			
			unset($processImageOptions1);
			
			if($rsProcessImage1['image_optimized_file_url']) {
				$imgSrc = $rsProcessImage1['image_optimized_file_url'];
				$img_tag = Utils::setAttributeHtmlTag($img_tag,'src',$imgSrc,true);
			}
			
		}
		
		return $img_tag;
		
	}
	
	public function preview_processed_image() 
	{
	
		$resultData = array(
			'status' => 1
		);
		
		$checkStatus1 = false;
		
		$dataSent = PepVN_Data::getDataSent();
		if($dataSent && isset($dataSent['localTimeSent']) && $dataSent['localTimeSent']) {
			
			foreach($dataSent as $key1 => $value1) {
				$keyTemp1 = preg_replace('#\[\]$#i','', $key1);
				if($keyTemp1 != $key1) {
					unset($dataSent[$key1]);
					$dataSent[$keyTemp1] = $value1;
				}
			}
		
		
			if(isset($dataSent['optimize_images_watermarks_preview_processed_image_example_image_url']) && $dataSent['optimize_images_watermarks_preview_processed_image_example_image_url']) {
				$imgSrc = $dataSent['optimize_images_watermarks_preview_processed_image_example_image_url'];
				if(Utils::isUrl($imgSrc)) {
				
					if($this->_isSystemReadyToHandleImagesStatus) {
						
						if(Utils::isImageUrl($imgSrc)) {
						
							$rsSettingWatermarksFirstOptions = $this->parse_watermarks_first_options(array(
								'options' => $dataSent
							));
							
							$paramsWatermarkOptions = $rsSettingWatermarksFirstOptions['paramsWatermarkOptions'];
							$options = $rsSettingWatermarksFirstOptions['options'];
							
							$preview_Key = $this->_createKey(array(
								$imgSrc
								,'options' => $options
								,'paramsWatermarkOptions' => $paramsWatermarkOptions
							));
							
							$preview_FolderPath = $this->_folderStorePath . 'preview' . DIRECTORY_SEPARATOR;
							
							if(!is_dir($preview_FolderPath)) {
								System::mkdir($preview_FolderPath);
							}
							
							if(is_dir($preview_FolderPath) && is_readable($preview_FolderPath) && is_writable($preview_FolderPath)) {
								
								$rsProcessImage1 = $this->process_image(array(
									'optimized_image_folder_path' => $preview_FolderPath
									,'optimized_image_file_name' => 'preview_'.$preview_Key
									,'original_image_src' => $imgSrc
									,'options' => $options
									,'paramsWatermarkOptions' => $paramsWatermarkOptions
									,'action' => 'do_process_image'
								));
								
								if($rsProcessImage1['image_optimized_file_path']) {
									$imgSrc2 = str_replace($this->_folderStorePath,$this->_folderStoreUrl,$rsProcessImage1['image_optimized_file_path']);
									$imgSrc2 .= '?xtrts='.PepVN_Data::$defaultParams['requestTime'].mt_rand();
									
									$resultData['img_processed_url'] = $imgSrc2;
								}
							}
						}
					}
				}
			}
		}
		
		PepVN_Data::encodeResponseData($resultData,true);
		
	}
	
	
	public function preview_processed_image_action() 
	{
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG)) {
			echo 'error';exit();
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if($wpExtend->is_admin()) {
			if($wpExtend->isCurrentUserCanManagePlugin()) {
				$this->preview_processed_image();
			}
		}
		
		exit(); die(); // this is required to return a proper result
		
	}
	
	public static function getDefaultOption()
	{
		return array(
			'optimize_images_alttext' => ''
			,'optimize_images_titletext' => ''
			,'optimize_images_override_alt' => ''
			,'optimize_images_override_title' => ''
			
			,'optimize_images_optimize_image_file_enable' => ''
			,'optimize_images_only_handle_file_when_uploading_enable' => ''
			,'optimize_images_file_minimum_width_height' => 150
			,'optimize_images_file_maximum_width_height' => 0
			,'optimize_images_auto_resize_images_enable' => ''
			,'optimize_images_watermarks_enable' => ''
			,'optimize_images_watermarks_watermark_position' => array()
			,'optimize_images_watermarks_watermark_type' => array()
			,'optimize_images_watermarks_watermark_image_url' => ''
			,'optimize_images_watermarks_watermark_image_width' => '20%'
			,'optimize_images_watermarks_watermark_image_margin_x' => 10
			,'optimize_images_watermarks_watermark_image_margin_y' => 10
			,'optimize_images_watermarks_watermark_text_value' => PepVN_Data::$defaultParams['fullDomainName']
			,'optimize_images_watermarks_watermark_text_font_name' => 'arial'
			,'optimize_images_watermarks_watermark_text_size' => '20%'
			,'optimize_images_watermarks_watermark_text_color' => 'ffffff'
			,'optimize_images_watermarks_watermark_text_margin_x' => '10'
			,'optimize_images_watermarks_watermark_text_margin_y' => '10'
			,'optimize_images_watermarks_watermark_text_opacity_value' => '80'
			,'optimize_images_watermarks_watermark_text_background_enable' => ''
			,'optimize_images_watermarks_watermark_text_background_color' => '222222'
			,'optimize_images_image_quality_value' => '100'
			,'optimize_images_rename_img_filename_value' => ''
			,'optimize_images_maximum_files_handled_each_request' => 2
			,'optimize_images_handle_again_files_different_configuration_enable' => ''
			,'optimize_images_remove_files_available_different_configuration_enable' => ''
			
			,'optimize_images_file_exclude_external_url_enable' => 'on'
			,'optimize_images_file_exclude_external_url' => ''
			
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
		return WpOptions::update_option(self::OPTION_NAME,$data);
	}
	
	public function migrateOptions() 
	{
		
		$newOptions = array();
		
		$oldOptionID = 'WPOptimizeByxTraffic';
		$oldOptions = get_option($oldOptionID);
		
		$keyFromOldToNew = array(
			'optimize_images_alttext' => 'optimize_images_alttext'
			,'optimize_images_titletext' => 'optimize_images_titletext'
			,'optimize_images_override_alt' => 'optimize_images_override_alt'
			,'optimize_images_override_title' => 'optimize_images_override_title'
			,'optimize_images_optimize_image_file_enable' => 'optimize_images_optimize_image_file_enable'
			,'optimize_images_file_minimum_width_height' => 'optimize_images_file_minimum_width_height'
			,'optimize_images_file_maximum_width_height' => 'optimize_images_file_maximum_width_height'
			,'optimize_images_auto_resize_images_enable' => 'optimize_images_auto_resize_images_enable'
			,'optimize_images_watermarks_enable' => 'optimize_images_watermarks_enable'
			,'optimize_images_watermarks_watermark_text_value' => 'optimize_images_watermarks_watermark_text_value'
			,'optimize_images_watermarks_watermark_text_font_name' => 'optimize_images_watermarks_watermark_text_font_name'
			,'optimize_images_watermarks_watermark_text_size' => 'optimize_images_watermarks_watermark_text_size'
			,'optimize_images_watermarks_watermark_text_color' => 'optimize_images_watermarks_watermark_text_color'
			,'optimize_images_watermarks_watermark_text_margin_x' => 'optimize_images_watermarks_watermark_text_margin_x'
			,'optimize_images_watermarks_watermark_text_margin_y' => 'optimize_images_watermarks_watermark_text_margin_y'
			,'optimize_images_watermarks_watermark_text_opacity_value' => 'optimize_images_watermarks_watermark_text_opacity_value'
			,'optimize_images_watermarks_watermark_text_background_enable' => 'optimize_images_watermarks_watermark_text_background_enable'
			,'optimize_images_watermarks_watermark_text_background_color' => 'optimize_images_watermarks_watermark_text_background_color'
			,'optimize_images_watermarks_watermark_image_url' => 'optimize_images_watermarks_watermark_image_url'
			,'optimize_images_watermarks_watermark_image_width' => 'optimize_images_watermarks_watermark_image_width'
			,'optimize_images_watermarks_watermark_image_margin_x' => 'optimize_images_watermarks_watermark_image_margin_x'
			,'optimize_images_watermarks_watermark_image_margin_y' => 'optimize_images_watermarks_watermark_image_margin_y'
			,'optimize_images_image_quality_value' => 'optimize_images_image_quality_value'
			,'optimize_images_rename_img_filename_value' => 'optimize_images_rename_img_filename_value'
			,'optimize_images_maximum_files_handled_each_request' => 'optimize_images_maximum_files_handled_each_request'
			,'optimize_images_handle_again_files_different_configuration_enable' => 'optimize_images_handle_again_files_different_configuration_enable'
			,'optimize_images_remove_files_available_different_configuration_enable' => 'optimize_images_handle_again_files_different_configuration_enable'
			
		);
		
		if($oldOptions && is_array($oldOptions) && !empty($oldOptions)) {
			
			foreach($keyFromOldToNew as $oldKey => $newKey) {
				if(isset($oldOptions[$oldKey])) {
					$newOptions[$newKey] = $oldOptions[$oldKey];
					unset($oldOptions[$oldKey]);
				}
				
			}
		}
		
		
		$keyFromOldToNew = array(
			'optimize_images_watermarks_watermark_position' => 'optimize_images_watermarks_watermark_position'
			,'optimize_images_watermarks_watermark_type' => 'optimize_images_watermarks_watermark_type'
			
		);
		
		if($oldOptions && is_array($oldOptions) && !empty($oldOptions)) {
			
			foreach($keyFromOldToNew as $oldKey => $newKey) {
				if(isset($oldOptions[$oldKey])) {
					if($oldOptions[$oldKey] && is_array($oldOptions[$oldKey]) && !empty($oldOptions[$oldKey])) {
						foreach($oldOptions[$oldKey] as $oldValue) {
							$newOptions[$newKey][$oldValue] = $oldValue;
						}
						unset($oldOptions[$oldKey]);
					}
				}
				
			}
		}
		
		if(!empty($newOptions)) {
			self::updateOption(array_merge(self::getOption(),$newOptions));
			self::getOption(false);
		}
		
		update_option($oldOptionID, $oldOptions);
		
	}
	
	public function on_plugin_activation()
	{
		$this->migrateOptions();
		
		//set chmod executable file optimize image
		$dir = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes/optimize-images/';
		$rsScandirR = System::scandirR($dir);
		if(isset($rsScandirR['files']) && ($rsScandirR['files']) && !empty($rsScandirR['files'])) {
			foreach($rsScandirR['files'] as $key1 => $value1) {
				if($value1 && is_file($value1)) {
					if(preg_match('#(gifsicle|jpegtran|optipng|pngquant)$#',$value1)) {
						if(!is_executable($value1)) {
							if(!System::isSafeMode()) {
								@chmod($value1, 0755);
							}
						}
					}
				}
			}
		}
		
		$this->_remove_old_structure_file();
	}
	
	private function _fixFileName($file_name)
	{
		return Utils::safeFileName($file_name);
	}

	
	private function _isImageFileCanProcess($filePath)
	{
		
		$resultData = false;
		
		if($filePath && is_file($filePath)) {
			if(filesize($filePath)>0) {
				$rsOne = $this->_getimagesize($filePath);
				if(isset($rsOne[0]) && $rsOne[0]) {	//0 : width 
					$rsOne[0] = (int)$rsOne[0];
					if($rsOne[0] > 10) {	//width > 10 px
						if(isset($rsOne[1]) && $rsOne[1]) {
							$rsOne[1] = (int)$rsOne[1];
							if($rsOne[1] > 10) {	//height > 10 px
								if(!PepVN_Images::isAnimation($filePath)) { //not Animation gif
									$resultData = true;
								}
							}
						}
					}
				}
			}
			
		}
		
		return $resultData;
	}
	
	
	private function _clean_unuse_image($input_parameters)
	{
		if(
			isset($input_parameters['optimized_image_folder_path'])
			&& $input_parameters['optimized_image_folder_path']
			&& isset($input_parameters['key_file'])
			&& $input_parameters['key_file']
			
		) {
			
			if(
				is_dir($input_parameters['optimized_image_folder_path'])
				&& is_readable($input_parameters['optimized_image_folder_path'])
				&& is_writable($input_parameters['optimized_image_folder_path'])
			) {
				
				$globPaths1 = $input_parameters['optimized_image_folder_path'] . '*.*';
				$globPaths1 = glob($globPaths1);
				
				if($globPaths1 && ($globPaths1) && (!empty($globPaths1))) {
					$pattern1 = '#-'.Utils::preg_quote($input_parameters['key_file']).'(\.|\-)#';
					foreach ($globPaths1 as $filename1) {
						
						if($filename1 && is_file($filename1)) {
							if(Utils::isImageFilePath($filename1)) {
								if(!preg_match($pattern1,$filename1)) {	//image name not match key_file will be deleted
									if(is_writable($filename1)) {
										System::unlink($filename1);
									}
								}
							}
						}
						
					}
				}
			}
		}
	}
	
	private function _createKey($input_data)
	{
		//important : don't change here, it make id for data (images url,...)
		return md5(preg_replace('#[\s \t]+#is','',serialize($input_data)));
	}
	
	
	private function _hashCreateKey($str,$length = 8)
	{
		//important : don't change here, it make id for data (images url,...)
		$length1 = $length * 2;
		
		$resultData = md5($str);
		
		while(strlen($resultData) < $length1) {
			
			$resultData .= md5($resultData);
			
		}
		
		$resultData .= md5($resultData);
		$resultData .= md5($resultData);
		
		$totalChars = strlen($resultData);
		$totalChars = (int)$totalChars;
		$stepNum = ceil($totalChars / $length);
		
		$valueTemp = str_split($resultData,1);
		$valueTemp_Count = count($valueTemp);
		$valueTemp_Count = (int)$valueTemp_Count;
		
		$resultData = '';
		
		for($i=0;$i<$valueTemp_Count;$i++) {
			if(0 === ($i % $stepNum)) {
				$resultData .= $valueTemp[$i];
				if(strlen($resultData) >= $length) {
					break;
				}
			}
		}
		
		return $resultData; 
		
	}
	
	private function _getImageFileInfo($filePath)
	{
		$k = Hash::crc32b(
			__CLASS__ . __METHOD__ . $filePath
		);
		
		$tmp = TempDataAndCacheFile::get_cache($k);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$tmp = PepVN_Images::getImageInfo($filePath, false);
		
		TempDataAndCacheFile::set_cache($k,$tmp);
		
		return $tmp;
	}
	
	private function _openImage($filePath)
	{
		$resultData = PepVN_Images::getImageInfo($filePath, true);
		
		if(isset($resultData['image_resource']) && $resultData['image_resource']) {
			return $resultData['image_resource'];
		}
		
		return false;
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
	
	public function process_image($input_parameters)
	{
		$resultData = array();
		
		$resultData['image_original_file_path'] = false;
		$resultData['image_optimized_file_path'] = false;
		$resultData['image_optimized_file_url'] = false;
		
		if(false === $this->_isSystemReadyToHandleImagesStatus) {
			return $resultData;
		}
		
		if(!isset(PepVN_Data::$params['optimize_images']['number_images_processed_request'])) {
			PepVN_Data::$params['optimize_images']['number_images_processed_request'] = 0;
		}
		
		$options = false;
		$paramsWatermarkOptions = false;
		
		if(isset($input_parameters['options']) && $input_parameters['options']) {
			$options = $input_parameters['options'];
		}
		
		if(isset($input_parameters['paramsWatermarkOptions']) && $input_parameters['paramsWatermarkOptions']) {
			$paramsWatermarkOptions = $input_parameters['paramsWatermarkOptions'];
		}
		
		unset($input_parameters['options']);
		
		unset($input_parameters['paramsWatermarkOptions']);
		
		if(!$options) {
			$options = self::getOption();
		}
		
		if(isset($options['optimize_images_optimize_image_file_enable']) && ('on' === $options['optimize_images_optimize_image_file_enable'])) {
			
		} else {
			return $resultData;
		}
		
		if(!$paramsWatermarkOptions) {
		
			$rsSettingWatermarksFirstOptions = $this->parse_watermarks_first_options(array(
				'options' => $options
			));
			
			$paramsWatermarkOptions = $rsSettingWatermarksFirstOptions['paramsWatermarkOptions'];
			$options = $rsSettingWatermarksFirstOptions['options'];
			
			unset($rsSettingWatermarksFirstOptions);
		}
		
		
		if(isset($input_parameters['plusOptions']) && is_array($input_parameters['plusOptions'])) {
			$options = Utils::mergeArrays(array(
				$options
				,$input_parameters['plusOptions']
			));
		}
		
		if(isset($paramsWatermarkOptions['text']) && is_array($paramsWatermarkOptions['text']) && !empty($paramsWatermarkOptions['text'])) {
			
		} else {
			$paramsWatermarkOptions['text'] = false;
		}
		
		if(isset($paramsWatermarkOptions['image']) && is_array($paramsWatermarkOptions['image']) && !empty($paramsWatermarkOptions['image'])) {
			
		} else {
			$paramsWatermarkOptions['image'] = false;
		}
		
		if(isset($options['optimize_images_watermarks_enable']) && ('on' === $options['optimize_images_watermarks_enable'])) {
			
		} else {
			$paramsWatermarkOptions['text'] = false;
			$paramsWatermarkOptions['image'] = false;
		}
		
		$isProcessStatus = false;
		
		if(($paramsWatermarkOptions['text']) || ($paramsWatermarkOptions['image'])) {
			$isProcessStatus = true;
		}
		
		$options['optimize_images_image_quality_value'] = abs((int)$options['optimize_images_image_quality_value']);
		if(($options['optimize_images_image_quality_value'] >= 10) && ($options['optimize_images_image_quality_value'] < 100)) {
			$isProcessStatus = true;
		}
		
		$options['optimize_images_rename_img_filename_value'] = trim($options['optimize_images_rename_img_filename_value']);
		if($options['optimize_images_rename_img_filename_value']) {
			$isProcessStatus = true;
		}
		
		$optimize_images_file_exclude_external_url_pattern = false;
		if(isset($options['optimize_images_file_exclude_external_url']) && ($options['optimize_images_file_exclude_external_url'])) {
			$options['optimize_images_file_exclude_external_url'] = trim($options['optimize_images_file_exclude_external_url']);
			if($options['optimize_images_file_exclude_external_url']) {
				$tmp = PepVN_Data::cleanPregPatternsArray($options['optimize_images_file_exclude_external_url']);
				
				if($tmp && !empty($tmp)) {
					$optimize_images_file_exclude_external_url_pattern = '#('.implode('|',$tmp).')#i';
				}
				
				unset($tmp);
				
			}
		}
		
		if($isProcessStatus) {
			if(isset($input_parameters['original_image_src']) && $input_parameters['original_image_src']) {
				if(Utils::isImageUrl($input_parameters['original_image_src'])) {
					if(isset($options['optimize_images_file_exclude_external_url_enable']) && ('on' === $options['optimize_images_file_exclude_external_url_enable'])) {
						if(!Utils::isUrlSameDomain($input_parameters['original_image_src'],PepVN_Data::$defaultParams['domainName'], false)) {
							$isProcessStatus = false;
						}
					}
					
					if($isProcessStatus) {
						if($optimize_images_file_exclude_external_url_pattern) {
							if(preg_match($optimize_images_file_exclude_external_url_pattern, $input_parameters['original_image_src'])) {
								$isProcessStatus = false;
							}
						}
					}
					
				} else {
					$isProcessStatus = false;
				}
			} else {
				$isProcessStatus = false;
			}
		}
		
		if($isProcessStatus) {
			$options['optimize_images_maximum_files_handled_each_request'] = (int)$options['optimize_images_maximum_files_handled_each_request'];
			if($options['optimize_images_maximum_files_handled_each_request']>0) {
				if(PepVN_Data::$params['optimize_images']['number_images_processed_request'] >= $options['optimize_images_maximum_files_handled_each_request']) {
					$isProcessStatus = false;
				}
			}
		}
		
		
		if($isProcessStatus) {
			if(!isset($input_parameters['optimized_image_file_name'])) {
				$input_parameters['optimized_image_file_name'] = '';
			}
			
			if(!$input_parameters['optimized_image_file_name']) {
				$imgPathInfo = pathinfo($input_parameters['original_image_src']);
				if(isset($imgPathInfo['filename']) && $imgPathInfo['filename']) {
					$input_parameters['optimized_image_file_name'] = trim($imgPathInfo['filename']);
				}
			}
			
			if(!$input_parameters['optimized_image_file_name']) {
				$isProcessStatus = false;
			}
		}
		
		if(!$isProcessStatus) {
			return $resultData;
		}
		
		$options['optimize_images_file_maximum_width_height'] = (int)$options['optimize_images_file_maximum_width_height'];
		$options['optimize_images_file_minimum_width_height'] = (int)$options['optimize_images_file_minimum_width_height'];
		
		if(!isset($input_parameters['resize_max_width'])) {
			$input_parameters['resize_max_width'] = 0;
		}
		$input_parameters['resize_max_width'] = (int)$input_parameters['resize_max_width'];
		$input_parameters['resize_max_width'] = abs($input_parameters['resize_max_width']);
		
		if(!isset($input_parameters['resize_max_height'])) {
			$input_parameters['resize_max_height'] = 0;
		}
		$input_parameters['resize_max_height'] = (int)$input_parameters['resize_max_height'];
		$input_parameters['resize_max_height'] = abs($input_parameters['resize_max_height']);
		
		if(!isset($input_parameters['optimized_image_folder_path'])) {
			$input_parameters['optimized_image_folder_path'] = '';
		}
		
		if(!$input_parameters['optimized_image_folder_path']) {
			/*
			* Dont't edit here, it make key for each image's url
			*/
			$input_parameters['optimized_image_folder_path'] = $this->_folderStorePath;
			
			$imgKey1 = array();
			$imgKey1[] = PepVN_Data::$defaultParams['fullDomainName'];	//add domain in case use watermark for each domain
			$imgKey1[] = $input_parameters['original_image_src'];
			$imgKey1 = $this->_createKey($imgKey1);
			$imgKey1 = $this->_hashCreateKey($imgKey1,8);
			
			$input_parameters['optimized_image_folder_path'] .= substr($imgKey1,0,2) . DIRECTORY_SEPARATOR . $imgKey1 . DIRECTORY_SEPARATOR;
			
			/*
			* Dont't edit here, it make key for each image's url
			*/
		}
		
		$keyConfigsProcessedData = '';
		
		if(isset($options['optimize_images_handle_again_files_different_configuration_enable']) && ('on' === $options['optimize_images_handle_again_files_different_configuration_enable'])) {
			/*
			* Dont't edit here, it make key for each image's url
			*/
			$fieldsKeysProcessedData = array(
				'optimize_images_watermarks_enable'
				,'optimize_images_watermarks_watermark_position'
				,'optimize_images_watermarks_watermark_type'
				,'optimize_images_watermarks_watermark_text_value'
				,'optimize_images_watermarks_watermark_text_font_name'
				,'optimize_images_watermarks_watermark_text_size'
				,'optimize_images_watermarks_watermark_text_color'
				,'optimize_images_watermarks_watermark_text_margin_x'
				,'optimize_images_watermarks_watermark_text_margin_y'
				,'optimize_images_watermarks_watermark_text_opacity_value'
				,'optimize_images_watermarks_watermark_text_background_enable'
				,'optimize_images_watermarks_watermark_text_background_color'
				,'optimize_images_watermarks_watermark_image_url'
				,'optimize_images_watermarks_watermark_image_width'
				,'optimize_images_watermarks_watermark_image_margin_x'
				,'optimize_images_watermarks_watermark_image_margin_y'
				,'optimize_images_image_quality_value'
				,'optimize_images_rename_img_filename_value'
			);
			
			$keyConfigsProcessedData = array();
			foreach($fieldsKeysProcessedData as $value1) {
				if($value1) {
					if(isset($options[$value1])) {
						$keyConfigsProcessedData[$value1] = $options[$value1];
					}
				}
			}
			
			$keyConfigsProcessedData = $this->_createKey($keyConfigsProcessedData);
			/*
			* Dont't edit here, it make key for each image's url
			*/
		}
		
		$keyConfigsProcessedData = trim($keyConfigsProcessedData);
		
		/*
		* Dont't edit here, it make key for each image's url
		*/
		$keyFileConfigsProcessedData = $keyConfigsProcessedData;
		$keyFileConfigsProcessedData = md5($keyFileConfigsProcessedData);
		$keyFileConfigsProcessedData = $this->_hashCreateKey($keyFileConfigsProcessedData,2);
		/*
		* Dont't edit here, it make key for each image's url
		*/
		
		$input_parameters['optimized_image_file_name'] = $this->_fixFileName($input_parameters['optimized_image_file_name']);
		
		$imgOptimizedFilePathExists1  = false;
		
		$imgOptimizedFilePath1 = $input_parameters['optimized_image_folder_path'] . $input_parameters['optimized_image_file_name'] . '-' . $keyFileConfigsProcessedData;
		
		if( 
			($input_parameters['resize_max_width']>0)
			|| ($input_parameters['resize_max_height']>0)
		) {
			$imgOptimizedFilePath1 .= '-'.$input_parameters['resize_max_width'].'x'.$input_parameters['resize_max_height'];
		}
		
		foreach($this->imageExtensionsAllow as $key1 => $value1) {
			if($value1) {
				$tmp = $imgOptimizedFilePath1.'.'.$value1;
				if(is_file($tmp)) {
					if(filesize($tmp)>0) {
						$imgOptimizedFilePathExists1 = $tmp;
						break;
					}
				}
			
			}
		}
		
		if($imgOptimizedFilePathExists1) {
			
			$rsGetimagesize = $this->_getimagesize($imgOptimizedFilePathExists1);
			
			if($rsGetimagesize && isset($rsGetimagesize[0]) && ($rsGetimagesize[0])) {
				$resultData['image_optimized_file_path'] = $imgOptimizedFilePathExists1;
				$resultData['image_optimized_file_url'] = str_replace($this->_folderStorePath, $this->_folderStoreUrl, $resultData['image_optimized_file_path']);
				
				$resultData['image_optimized_width'] = (int)$rsGetimagesize[0];
				$resultData['image_optimized_height'] = (int)$rsGetimagesize[1];
				
				if(isset($rsGetimagesize['mime'])) {
					$resultData['image_optimized_mime'] = $rsGetimagesize['mime'];
				}
				
				return $resultData;
			}
			
		}
		
		$checkStatus1 = false;
		
		if(isset($input_parameters['action']) && $input_parameters['action']) {
			if('do_process_image' === $input_parameters['action']) {
				$checkStatus1 = true;
			}// else only check if file images processed exist
		}
		
		if(!$checkStatus1) {
			return $resultData;
		}
		
		$remote = $this->di->getShared('remote');
		
		if(isset($input_parameters['original_image_src']) && $input_parameters['original_image_src']) {
			
			if(Utils::isUrl($input_parameters['original_image_src'])) {
				
				$imgOptimizedFilePath = false;
				
				if($input_parameters['optimized_image_folder_path'] && is_dir($input_parameters['optimized_image_folder_path'])) {
				} else {
					System::mkdir($input_parameters['optimized_image_folder_path']);
				}
				
				if(is_dir($input_parameters['optimized_image_folder_path'])  && is_readable($input_parameters['optimized_image_folder_path']) && is_writable($input_parameters['optimized_image_folder_path'])) {
					
					if(!$resultData['image_optimized_file_path']) {
					
						$imgFolderCachePath = $this->_folderCachePath;
						
						if($imgFolderCachePath && is_dir($imgFolderCachePath) && is_readable($imgFolderCachePath) && is_writable($imgFolderCachePath)) {
							
							$imgOriginalCacheFilePath = $imgFolderCachePath . DIRECTORY_SEPARATOR;
							$imgOriginalCacheFilePath .= md5($input_parameters['original_image_src']).'.txt'; 
							
							if(!is_file($imgOriginalCacheFilePath)) {
								
								file_put_contents($imgOriginalCacheFilePath,'');
								
								$imgSrcContent = $remote->get($input_parameters['original_image_src'],array(
									'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
								));
								
								if($imgSrcContent) {
									file_put_contents($imgOriginalCacheFilePath,$imgSrcContent);
								}
							}
							
							if(is_file($imgOriginalCacheFilePath)) {
								
								$valueTemp1 = filesize($imgOriginalCacheFilePath);
								
								if($valueTemp1 && ($valueTemp1>0)) {
									
									if($this->_isImageFileCanProcess($imgOriginalCacheFilePath)) {
										
										$imgOriginalCacheFile_RsGetImageFileInfo = $this->_getImageFileInfo($imgOriginalCacheFilePath);
										
										if(isset($imgOriginalCacheFile_RsGetImageFileInfo['image_type']) && $imgOriginalCacheFile_RsGetImageFileInfo['image_type']) {
											
											PepVN_Data::$params['optimize_images']['number_images_processed_request']++;
											
											$imgOptimizedFilePath1 .= '.'.$imgOriginalCacheFile_RsGetImageFileInfo['image_type'];
											
											if(!is_file($imgOptimizedFilePath1)) {
												file_put_contents($imgOptimizedFilePath1,'');
											}
											
											$isCanProcessFileStatus1 = true;
											
											if(
												($options['optimize_images_file_minimum_width_height']>0) 
												|| ($options['optimize_images_file_maximum_width_height']>0) 
											) {
												
												$isCanProcessFileStatus1 = false;
												
												if(
													isset($imgOriginalCacheFile_RsGetImageFileInfo['width'])
													&& ($imgOriginalCacheFile_RsGetImageFileInfo['width'] > 0)
													&& isset($imgOriginalCacheFile_RsGetImageFileInfo['height'])
													&& ($imgOriginalCacheFile_RsGetImageFileInfo['height'] > 0)
												) {
													
													$isCanProcessFileStatus1 = true;
													
													if($isCanProcessFileStatus1) {
														if($options['optimize_images_file_minimum_width_height']>0) {
															if(
																($imgOriginalCacheFile_RsGetImageFileInfo['width'] >= $options['optimize_images_file_minimum_width_height'])
																&& ($imgOriginalCacheFile_RsGetImageFileInfo['height'] >= $options['optimize_images_file_minimum_width_height'])
															) {
																
															} else {
																$isCanProcessFileStatus1 = false;
															}
														}
													}
													
													if($isCanProcessFileStatus1) {
														if($options['optimize_images_file_maximum_width_height']>0) {
															if(
																($imgOriginalCacheFile_RsGetImageFileInfo['width'] <= $options['optimize_images_file_maximum_width_height'])
																&& ($imgOriginalCacheFile_RsGetImageFileInfo['height'] <= $options['optimize_images_file_maximum_width_height'])
															) {
																
															} else {
																$isCanProcessFileStatus1 = false;
															}
														}
													}
												}
											}
											
											if(!$isCanProcessFileStatus1) {
												return $resultData;
											}
											
											$resultData['image_original_file_path'] = $imgOriginalCacheFilePath;
											
											$pepVN_PHPImage = new PepVN_PHPImage($imgOriginalCacheFilePath);
											
											if($options['optimize_images_image_quality_value']>95) {
												$options['optimize_images_image_quality_value'] = 95;
											}
											$pepVN_PHPImage->setQuality($options['optimize_images_image_quality_value']);
											
											$imgOriginalCacheFile_RatioWidthPerHeight = $imgOriginalCacheFile_RsGetImageFileInfo['width'] / $imgOriginalCacheFile_RsGetImageFileInfo['height'];
											$imgOriginalCacheFile_RatioWidthPerHeight = (float)$imgOriginalCacheFile_RatioWidthPerHeight;
											
											$resizeImgNewWidth = 0;
											$resizeImgNewHeight = 0;
											
											$isCanWatermarkStatus1 = true;
											
											if(
												($input_parameters['resize_max_width']>0)
												|| ($input_parameters['resize_max_height']>0)
											) {
												
												if($isCanWatermarkStatus1) {
													if($options['optimize_images_file_minimum_width_height']>0) {
														
														if($input_parameters['resize_max_width'] > 0) {
															if(
																($input_parameters['resize_max_width'] < $options['optimize_images_file_minimum_width_height'])
															) {
																$isCanWatermarkStatus1 = false;
															}
														}
														
														if($input_parameters['resize_max_height'] > 0) {
															if(
																($input_parameters['resize_max_height'] < $options['optimize_images_file_minimum_width_height'])
															) {
																$isCanWatermarkStatus1 = false;
															}
														}
													}
												}
												
												if($isCanWatermarkStatus1) {
													if($options['optimize_images_file_maximum_width_height']>0) {
														
														if($input_parameters['resize_max_width'] > 0) {
															if(
																($input_parameters['resize_max_width'] > $options['optimize_images_file_maximum_width_height'])
															) {
																$isCanWatermarkStatus1 = false;
															}
														}
														
														if($input_parameters['resize_max_height'] > 0) {
															if(
																($input_parameters['resize_max_height'] > $options['optimize_images_file_maximum_width_height'])
															) {
																$isCanWatermarkStatus1 = false;
															}
														}
													}
												}
												
												$resizeImgNewWidth = $input_parameters['resize_max_width'];
												$resizeImgNewHeight = $input_parameters['resize_max_height'];
												
												if($resizeImgNewWidth>$imgOriginalCacheFile_RsGetImageFileInfo['width']) {
													$resizeImgNewWidth = $imgOriginalCacheFile_RsGetImageFileInfo['width'];
												}
												
												if($resizeImgNewHeight>$imgOriginalCacheFile_RsGetImageFileInfo['height']) {
													$resizeImgNewHeight = $imgOriginalCacheFile_RsGetImageFileInfo['height'];
												}
												
												if($resizeImgNewWidth>0) {
													if(0 === $resizeImgNewHeight) {
														$resizeImgNewHeight = ceil($resizeImgNewWidth / $imgOriginalCacheFile_RatioWidthPerHeight);
													}
												}
												
												if($resizeImgNewHeight>0) {
													if(0 === $resizeImgNewWidth) {
														$resizeImgNewWidth = ceil($resizeImgNewHeight * $imgOriginalCacheFile_RatioWidthPerHeight);
													}
												}
												
												if(
													($resizeImgNewWidth>0)
													&& ($resizeImgNewHeight>0)
												) {
													$pepVN_PHPImage->resize($resizeImgNewWidth, $resizeImgNewHeight, false, false);
												}
												
											}
											
											if(
												($resizeImgNewWidth>0)
												&& ($resizeImgNewHeight>0)
											) {
												$targetImgNeedWatermark_Width = $resizeImgNewWidth;
												$targetImgNeedWatermark_Height = $resizeImgNewHeight;
											} else {
												$targetImgNeedWatermark_Width = $imgOriginalCacheFile_RsGetImageFileInfo[0];
												$targetImgNeedWatermark_Height = $imgOriginalCacheFile_RsGetImageFileInfo[1];
											}
											
											if($paramsWatermarkOptions['text'] && $isCanWatermarkStatus1) {
												
												$watermarkTextBoxWidth = 0;
												$watermarkTextBoxHeight = 0;
												$textFontSize = 12;
												$boxPaddingX = 0;
												$boxPaddingY = 0;
												
												if($paramsWatermarkOptions['text']) {
													
													if(isset($paramsWatermarkOptions['text']['fontSize']) && $paramsWatermarkOptions['text']['fontSize']) {
														$paramsWatermarkOptions['text']['fontSize'] = (int)$paramsWatermarkOptions['text']['fontSize'];
														if($paramsWatermarkOptions['text']['fontSize']>0) {
															$textFontSize = $paramsWatermarkOptions['text']['fontSize'];
														}
													}
													
													if(isset($options['optimize_images_watermarks_watermark_text_size']) && $options['optimize_images_watermarks_watermark_text_size']) {
														if(false !== stripos($options['optimize_images_watermarks_watermark_text_size'],'%')) {
															$valueTemp = $options['optimize_images_watermarks_watermark_text_size'];
															$valueTemp = preg_replace('#[^0-9]+#i','',$valueTemp);
															$valueTemp = (int)$valueTemp;
															$valueTemp = abs($valueTemp);
															if($valueTemp>100) {
																$valueTemp = 100;
															}
															
															$watermarkTextBoxWidth = $targetImgNeedWatermark_Width * ($valueTemp / 100);
															$watermarkTextBoxWidth = floor($watermarkTextBoxWidth);
															$watermarkTextBoxWidth = (int)$watermarkTextBoxWidth;
															
															$watermarkTextBoxHeight = $imgOriginalCacheFile_RsGetImageFileInfo[1] * ($valueTemp / 100);
															$watermarkTextBoxHeight = floor($watermarkTextBoxHeight);
															$watermarkTextBoxHeight = (int)$watermarkTextBoxHeight;
															
															$textFontSize = PepVN_Images::fitTextSizeToBoxSize(
																0	//fontSize
																,0	//angle
																,$paramsWatermarkOptions['text']['fontFile']	//fontFile
																,$options['optimize_images_watermarks_watermark_text_value']	//text
																,$watermarkTextBoxWidth	//box_width
																,$watermarkTextBoxHeight	//box_height
															);
														}
													}
													
												}
												
												$textFontSize = (int)$textFontSize;
												if($textFontSize > 0) {
													$paramsWatermarkOptions['text']['fontSize'] = $textFontSize;
													
													$rsCalculateText = PepVN_Images::calculateText(
														$textFontSize	//fontSize
														,0	//angle
														,$paramsWatermarkOptions['text']['fontFile']	//fontFile
														,$options['optimize_images_watermarks_watermark_text_value']	//text
													);
													
													$watermarkTextBoxWidth = $rsCalculateText['actualWidth'];
													$watermarkTextBoxHeight = $rsCalculateText['actualHeight'];
												}
												
												$watermarkTextBoxWidth = (int)$watermarkTextBoxWidth;
												$watermarkTextBoxHeight = (int)$watermarkTextBoxHeight;
												
												if(($watermarkTextBoxWidth > 0) && ($watermarkTextBoxHeight > 0)) {
													if(isset($paramsWatermarkOptions['text']['boxColor']) && $paramsWatermarkOptions['text']['boxColor']) {
														
														$boxPaddingX = $paramsWatermarkOptions['text']['boxPaddingX'];
														if(false !== stripos($boxPaddingX,'%')) {
															$boxPaddingX = preg_replace('#[^0-9]+#is','',$boxPaddingX);
															$boxPaddingX = abs((int)$boxPaddingX);
															$boxPaddingX = $watermarkTextBoxWidth * ($boxPaddingX / 100);
														}
														
														$boxPaddingX = abs((int)$boxPaddingX);
														
														$watermarkTextBoxWidth += ($boxPaddingX * 2);
														
														
														
														$boxPaddingY = $paramsWatermarkOptions['text']['boxPaddingY'];
														if(false !== stripos($boxPaddingY,'%')) {
															$boxPaddingY = preg_replace('#[^0-9]+#is','',$boxPaddingY);
															$boxPaddingY = abs((int)$boxPaddingY);
															$boxPaddingY = $watermarkTextBoxWidth * ($boxPaddingY / 100);
														}
														
														$boxPaddingY = abs((int)$boxPaddingY);
														
														$watermarkTextBoxHeight += ($boxPaddingY * 2);
														
													}
												}
												
												$options['optimize_images_watermarks_watermark_text_margin_x'] = (int)$options['optimize_images_watermarks_watermark_text_margin_x'];
												$options['optimize_images_watermarks_watermark_text_margin_y'] = (int)$options['optimize_images_watermarks_watermark_text_margin_y'];
												
												if(isset($options['optimize_images_watermarks_watermark_position']) && $options['optimize_images_watermarks_watermark_position']) {
													$options_optimize_images_watermarks_watermark_position = $options['optimize_images_watermarks_watermark_position'];
													$options_optimize_images_watermarks_watermark_position = (array)$options_optimize_images_watermarks_watermark_position;
													foreach($options_optimize_images_watermarks_watermark_position as $value1) {
														if($value1) {
															$value1 = trim($value1);
															if($value1) {
															
																if($paramsWatermarkOptions['text']) {
																	
																	if(($watermarkTextBoxWidth > 0) && ($watermarkTextBoxHeight > 0)) {
																		
																		$paramsWatermarkOptions_Temp1 = $paramsWatermarkOptions['text'];
																		
																		if(false !== stripos($value1,'top_')) {
																			$paramsWatermarkOptions_Temp1['y'] = 0 + abs((int)$options['optimize_images_watermarks_watermark_text_margin_y']);
																		} else if(false !== stripos($value1,'middle_')) {
																			$paramsWatermarkOptions_Temp1['y'] = floor(($imgOriginalCacheFile_RsGetImageFileInfo[1] - $watermarkTextBoxHeight)/2) + $options['optimize_images_watermarks_watermark_text_margin_y'];
																		} else if(false !== stripos($value1,'bottom_')) {
																			$paramsWatermarkOptions_Temp1['y'] = $imgOriginalCacheFile_RsGetImageFileInfo[1] - $watermarkTextBoxHeight - abs((int)$options['optimize_images_watermarks_watermark_text_margin_y']);
																		}
																		
																		
																		if(false !== stripos($value1,'_left')) {
																			$paramsWatermarkOptions_Temp1['x'] = 0 + abs((int)$options['optimize_images_watermarks_watermark_text_margin_x']);
																		} else if(false !== stripos($value1,'_center')) {
																			$paramsWatermarkOptions_Temp1['x'] = floor(abs($targetImgNeedWatermark_Width - $watermarkTextBoxWidth)/2) + $options['optimize_images_watermarks_watermark_text_margin_x'];
																		} else if(false !== stripos($value1,'_right')) {
																			$paramsWatermarkOptions_Temp1['x'] = $targetImgNeedWatermark_Width - $watermarkTextBoxWidth - abs((int)$options['optimize_images_watermarks_watermark_text_margin_x']);
																		}
																		
																		$paramsWatermarkOptions_Temp1['boxPaddingX'] = abs((int)$boxPaddingX); 
																		$paramsWatermarkOptions_Temp1['boxPaddingY'] = abs((int)$boxPaddingY); 
																		
																		$pepVN_PHPImage->text($options['optimize_images_watermarks_watermark_text_value'], $paramsWatermarkOptions_Temp1);
																		
																	}
																}
															}
															
														}
													}//loop watermark_position
												}
											}
											
											if($paramsWatermarkOptions['image'] && $isCanWatermarkStatus1) {
											
												if(isset($paramsWatermarkOptions['image']['watermark_image_file_path']) && $paramsWatermarkOptions['image']['watermark_image_file_path']) {
												
													$options['optimize_images_watermarks_watermark_image_margin_x'] = (int)$options['optimize_images_watermarks_watermark_image_margin_x'];
													$options['optimize_images_watermarks_watermark_image_margin_y'] = (int)$options['optimize_images_watermarks_watermark_image_margin_y'];
													
													$watermarkActualBoxWidth = 0;
													$watermarkActualBoxHeight = 0;
													
													$watermarkNewBoxWidth = 0;
													$watermarkNewBoxHeight = 0;
													
													$watermarkImg_RsGetImageInfo = PepVN_Images::getImageInfo($paramsWatermarkOptions['image']['watermark_image_file_path'],true);
													
													if(isset($watermarkImg_RsGetImageInfo['image_resource']) && $watermarkImg_RsGetImageInfo['image_resource']) {
														$watermarkActualBoxWidth = $watermarkImg_RsGetImageInfo['width'];
														$watermarkActualBoxHeight = $watermarkImg_RsGetImageInfo['height'];
													}
													
													$watermarkImg_Resource = false;
													
													$watermarkActualBoxWidth = (int)$watermarkActualBoxWidth;
													$watermarkActualBoxHeight = (int)$watermarkActualBoxHeight;
													
													$watermarkActualBoxRatio_WidthPerHeight = 0;
													
													$watermarkNewBoxWidth = $watermarkActualBoxWidth;
													$watermarkNewBoxHeight = $watermarkActualBoxHeight;
													
													$watermarkImageResource = false;
													
													if(($watermarkActualBoxWidth>0) && ($watermarkActualBoxHeight>0)) {
														
														$watermarkActualBoxRatio_WidthPerHeight = $watermarkActualBoxWidth / $watermarkNewBoxHeight;
														$watermarkActualBoxRatio_WidthPerHeight = (float)$watermarkActualBoxRatio_WidthPerHeight;
														
														if(isset($options['optimize_images_watermarks_watermark_image_width']) && $options['optimize_images_watermarks_watermark_image_width']) {
															
															if(false !== stripos($options['optimize_images_watermarks_watermark_image_width'],'%')) {
																
																$percentNewSize = $options['optimize_images_watermarks_watermark_image_width'];
																$percentNewSize = preg_replace('#[^0-9]+#i','',$percentNewSize);
																$percentNewSize = abs((int)$percentNewSize);
																if($percentNewSize>100) {
																	$percentNewSize = 100;
																}
																
																$watermarkNewBoxWidth = floor($targetImgNeedWatermark_Width * ($percentNewSize/100));
																$watermarkNewBoxWidth = (int)$watermarkNewBoxWidth;
																
																$watermarkNewBoxHeight = floor($watermarkNewBoxWidth / $watermarkActualBoxRatio_WidthPerHeight);
																$watermarkNewBoxHeight = (int)$watermarkNewBoxHeight;
																
															} else {
																$options['optimize_images_watermarks_watermark_image_width'] = abs((int)$options['optimize_images_watermarks_watermark_image_width']);
																if($options['optimize_images_watermarks_watermark_image_width']>0) {
																	
																	$watermarkNewBoxWidth = $options['optimize_images_watermarks_watermark_image_width'];
																	
																	$watermarkNewBoxHeight = floor($watermarkNewBoxWidth / $watermarkActualBoxRatio_WidthPerHeight);
																	$watermarkNewBoxHeight = (int)$watermarkNewBoxHeight;
																}
																
															}
														}
														
														$watermarkImg_Resource = PepVN_Images::create_blank_transparent_image_resource($watermarkNewBoxWidth,$watermarkNewBoxHeight);
														
														imagecopyresampled(
															$watermarkImg_Resource,	//dst_image
															$watermarkImg_RsGetImageInfo['image_resource'],	//src_image
															0,	//dst_x
															0,	//dst_y
															0,	//src_x
															0,	//src_y
															$watermarkNewBoxWidth,	//dst_w
															$watermarkNewBoxHeight,	//dst_h
															$watermarkActualBoxWidth,	//src_w
															$watermarkActualBoxHeight	//src_h
														);
														
														if($watermarkImg_Resource && is_resource($watermarkImg_Resource) && isset($options['optimize_images_watermarks_watermark_position']) && $options['optimize_images_watermarks_watermark_position']) {
															$options_optimize_images_watermarks_watermark_position = $options['optimize_images_watermarks_watermark_position'];
															$options_optimize_images_watermarks_watermark_position = (array)$options_optimize_images_watermarks_watermark_position;
															foreach($options_optimize_images_watermarks_watermark_position as $value1) {
																if($value1) {
																	$value1 = trim($value1);
																	if($value1) {
																		
																		$paramsWatermarkOptions_Temp1 = array();
																		$paramsWatermarkOptions_Temp1['x'] = 0;
																		$paramsWatermarkOptions_Temp1['y'] = 0;
																		
																		if(false !== stripos($value1,'top_')) {
																			$paramsWatermarkOptions_Temp1['y'] = 0 + abs((int)$options['optimize_images_watermarks_watermark_image_margin_y']);
																		} else if(false !== stripos($value1,'middle_')) {
																			$paramsWatermarkOptions_Temp1['y'] = floor(($imgOriginalCacheFile_RsGetImageFileInfo[1] - $watermarkNewBoxHeight)/2) + $options['optimize_images_watermarks_watermark_image_margin_y'];
																		} else if(false !== stripos($value1,'bottom_')) {
																			$paramsWatermarkOptions_Temp1['y'] = $imgOriginalCacheFile_RsGetImageFileInfo[1] - $watermarkNewBoxHeight - abs((int)$options['optimize_images_watermarks_watermark_image_margin_y']);
																		}
																		
																		if(false !== stripos($value1,'_left')) {
																			$paramsWatermarkOptions_Temp1['x'] = 0 + abs((int)$options['optimize_images_watermarks_watermark_image_margin_x']);
																		} else if(false !== stripos($value1,'_center')) {
																			$paramsWatermarkOptions_Temp1['x'] = floor(abs($targetImgNeedWatermark_Width - $watermarkNewBoxWidth)/2) + $options['optimize_images_watermarks_watermark_image_margin_x'];
																		} else if(false !== stripos($value1,'_right')) {
																			$paramsWatermarkOptions_Temp1['x'] = $targetImgNeedWatermark_Width - $watermarkNewBoxWidth - abs((int)$options['optimize_images_watermarks_watermark_image_margin_x']);
																		}
																		
																		$pepVN_PHPImage->drawFromResource(
																			$watermarkImg_Resource
																			,$paramsWatermarkOptions_Temp1
																		);
																		
																	}
																	
																}
															
																
															}//loop watermark_position
														}
														
													}
													
													
													if(
														isset($watermarkImg_RsGetImageInfo['image_resource'])
														&& $watermarkImg_RsGetImageInfo['image_resource'] 
														&& is_resource($watermarkImg_RsGetImageInfo['image_resource'])
													) {
														imagedestroy($watermarkImg_RsGetImageInfo['image_resource']);
													}
													$watermarkImg_RsGetImageInfo = false;
													
													if(
														$watermarkImg_Resource
														&& is_resource($watermarkImg_Resource)
													) {
														imagedestroy($watermarkImg_Resource);
													}
													$watermarkImg_Resource = false;
												}
												
											}
											
											$pepVN_PHPImage->save($imgOptimizedFilePath1,false,true);
											$pepVN_PHPImage->cleanup();
											
											unset($pepVN_PHPImage);
										}
										
										$valueTemp1 = $imgOptimizedFilePath1;
										
										if($valueTemp1 && is_file($valueTemp1)) {
											
											$this->optimize_lossy_image_file($valueTemp1);
											
											$valueTemp2 = filesize($valueTemp1);
											if($valueTemp2 && ($valueTemp2>0)) {
												$rs_getimagesize = $this->_getimagesize($valueTemp1);
												if($rs_getimagesize && isset($rs_getimagesize[0]) && ($rs_getimagesize[0])) {
												
													$resultData['image_optimized_file_path'] = $valueTemp1;
													$resultData['image_optimized_file_url'] = str_replace($this->_folderStorePath,$this->_folderStoreUrl,$resultData['image_optimized_file_path']);
													$resultData['image_optimized_width'] = (int)$rs_getimagesize[0];
													$resultData['image_optimized_height'] = (int)$rs_getimagesize[1];
													
													if(isset($rs_getimagesize['mime'])) {
														$resultData['image_optimized_mime'] = $rs_getimagesize['mime'];
													}
													
												}
											}
											
										}
										
									}
									
								}
							}
							
							
						}
						
					}
					
					if($resultData['image_optimized_file_path']) {
						
						if($keyConfigsProcessedData) {
						
							if(isset($options['optimize_images_remove_files_available_different_configuration_enable']) && ('on' === $options['optimize_images_remove_files_available_different_configuration_enable'])) {
								
								$this->optimize_images_clean_unuse_image(array(
									'optimized_image_folder_path' => $input_parameters['optimized_image_folder_path']
									,'key_file' => $keyFileConfigsProcessedData
								));
								
							}
						}
					}
				}
			}
		}
		
		return $resultData;
	}
	
	public function parse_watermarks_first_options($input_parameters)
	{
		$resultData = array();
		
		$options = false;
		
		if(isset($input_parameters['options']) && $input_parameters['options']) {
			$options = $input_parameters['options'];
			unset($input_parameters['options']);
		}
		
		if(!$options) {
			$options = self::getOption();
		}
		
		$keyCache1 = Utils::hashKey(array(
			$options
			,'parse_watermarks_first_options'
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$remote = $this->di->getShared('remote');
		
		$options['optimize_images_watermarks_watermark_text_background_opacity_value'] = 100;
		$options['optimize_images_watermarks_watermark_opacity_value'] = 100;
		
		$options['optimize_images_watermarks_watermark_text_outline_enable'] = '';
		
		//setting watermark
		$paramsWatermarkOptions = array();
		$paramsWatermarkOptions['text'] = array(
		);
		
		$paramsWatermarkOptions['image'] = array(
		);
		
		if(isset($options['optimize_images_watermarks_watermark_type']) && $options['optimize_images_watermarks_watermark_type']) {
			
			$options['optimize_images_watermarks_watermark_type'] = (array)$options['optimize_images_watermarks_watermark_type'];
			
			if(isset($options['optimize_images_watermarks_watermark_position']) && $options['optimize_images_watermarks_watermark_position']) {
				
				if(!empty($options['optimize_images_watermarks_watermark_position'])) {
					
					$options['optimize_images_watermarks_watermark_opacity_value'] = (int)$options['optimize_images_watermarks_watermark_opacity_value'];
					$options['optimize_images_watermarks_watermark_opacity_value'] = abs($options['optimize_images_watermarks_watermark_opacity_value']);
					if($options['optimize_images_watermarks_watermark_opacity_value'] > 0) {
						
						if($options['optimize_images_watermarks_watermark_opacity_value'] > 100) {
							$options['optimize_images_watermarks_watermark_opacity_value'] = 100;
						}
						
						if(isset($options['optimize_images_watermarks_watermark_type']['text'])) {
							
							if(isset($options['optimize_images_watermarks_watermark_text_value']) && $options['optimize_images_watermarks_watermark_text_value']) {
								$options['optimize_images_watermarks_watermark_text_value'] = trim($options['optimize_images_watermarks_watermark_text_value']);
								if($options['optimize_images_watermarks_watermark_text_value']) {
								
									if(isset($options['optimize_images_watermarks_watermark_text_font_name']) && $options['optimize_images_watermarks_watermark_text_font_name']) {
										$options['optimize_images_watermarks_watermark_text_font_name'] = preg_replace('#[^a-z0-9\-\_]+#i','',$options['optimize_images_watermarks_watermark_text_font_name']);
										$options['optimize_images_watermarks_watermark_text_font_name'] = strtolower($options['optimize_images_watermarks_watermark_text_font_name']);
										$options['optimize_images_watermarks_watermark_text_font_name'] = trim($options['optimize_images_watermarks_watermark_text_font_name']);
										if($options['optimize_images_watermarks_watermark_text_font_name']) {
										
											if(isset($options['optimize_images_watermarks_watermark_text_size']) && $options['optimize_images_watermarks_watermark_text_size']) {
												$options['optimize_images_watermarks_watermark_text_size'] = trim($options['optimize_images_watermarks_watermark_text_size']);
												if($options['optimize_images_watermarks_watermark_text_size']) {
												
													if(isset($options['optimize_images_watermarks_watermark_text_color']) && $options['optimize_images_watermarks_watermark_text_color']) {
														$options['optimize_images_watermarks_watermark_text_color'] = preg_replace('#[^a-z0-9]+#i','',$options['optimize_images_watermarks_watermark_text_color']);
														$options['optimize_images_watermarks_watermark_text_color'] = strtolower($options['optimize_images_watermarks_watermark_text_color']);
														$options['optimize_images_watermarks_watermark_text_color'] = trim($options['optimize_images_watermarks_watermark_text_color']);
														if($options['optimize_images_watermarks_watermark_text_color']) {
															
															if(isset($options['optimize_images_watermarks_watermark_text_opacity_value']) && $options['optimize_images_watermarks_watermark_text_opacity_value']) {
																$options['optimize_images_watermarks_watermark_text_opacity_value'] = trim($options['optimize_images_watermarks_watermark_text_opacity_value']);
																if($options['optimize_images_watermarks_watermark_text_opacity_value']) {
																
																	$options['optimize_images_watermarks_watermark_text_opacity_value'] = (int)$options['optimize_images_watermarks_watermark_text_opacity_value'];
																	$options['optimize_images_watermarks_watermark_text_opacity_value'] = abs($options['optimize_images_watermarks_watermark_text_opacity_value']);
																	if($options['optimize_images_watermarks_watermark_text_opacity_value']>100) {
																		$options['optimize_images_watermarks_watermark_text_opacity_value'] = 100;
																	}
																	
																	$paramsWatermarkOptions['text'] = array(
																		'fontSize' => 12,
																		'fontColor' => PepVN_Data::hex2rgb('#'.$options['optimize_images_watermarks_watermark_text_color']),
																		'opacity' => ($options['optimize_images_watermarks_watermark_text_opacity_value']/100),//0.66
																		'x' => 10,
																		'y' => 10,
																		'width' => null,
																		'height' => null, 
																		'alignHorizontal' => 'center', 
																		'alignVertical' => 'center',
																		'angle' => 0,
																		'strokeWidth' => 0,
																		'strokeColor' => '',
																		'fontFile' => $this->_folderFontPath.$options['optimize_images_watermarks_watermark_text_font_name'].'.ttf',
																		'autoFit' => false,
																		'boxColor' => 0,
																		'boxOpacity' => ($options['optimize_images_watermarks_watermark_text_opacity_value']/100),
																		'boxPaddingX' => '3%',
																		'boxPaddingY' => '4%', 
																		'debug' => false 
																	);
																	
																	if(isset($options['optimize_images_watermarks_watermark_text_size']) && $options['optimize_images_watermarks_watermark_text_size']) {
																		$options['optimize_images_watermarks_watermark_text_size'] = preg_replace('#[^0-9\%]+#i','',$options['optimize_images_watermarks_watermark_text_size']);
																		$options['optimize_images_watermarks_watermark_text_size'] = trim($options['optimize_images_watermarks_watermark_text_size']);
																		if(false === stripos($options['optimize_images_watermarks_watermark_text_size'],'%')) {
																			$paramsWatermarkOptions['text']['fontSize'] = (int)$options['optimize_images_watermarks_watermark_text_size'];
																		}
																	}
																	
																	if(isset($options['optimize_images_watermarks_watermark_text_background_enable']) && $options['optimize_images_watermarks_watermark_text_background_enable']) {
																		
																		if(isset($options['optimize_images_watermarks_watermark_text_background_color']) && $options['optimize_images_watermarks_watermark_text_background_color']) {
																			$options['optimize_images_watermarks_watermark_text_background_color'] = preg_replace('#[^a-z0-9]+#i','',$options['optimize_images_watermarks_watermark_text_background_color']);
																			$options['optimize_images_watermarks_watermark_text_background_color'] = strtolower($options['optimize_images_watermarks_watermark_text_background_color']);
																			$options['optimize_images_watermarks_watermark_text_background_color'] = trim($options['optimize_images_watermarks_watermark_text_background_color']);
																			if($options['optimize_images_watermarks_watermark_text_background_color']) {
																				
																				$paramsWatermarkOptions['text']['boxColor'] = PepVN_Data::hex2rgb('#'.$options['optimize_images_watermarks_watermark_text_background_color']);
																				
																				if(isset($options['optimize_images_watermarks_watermark_text_background_opacity_value']) && $options['optimize_images_watermarks_watermark_text_background_opacity_value']) {
																					$options['optimize_images_watermarks_watermark_text_background_opacity_value'] = trim($options['optimize_images_watermarks_watermark_text_background_opacity_value']);
																					if($options['optimize_images_watermarks_watermark_text_background_opacity_value']) {
																					
																						$options['optimize_images_watermarks_watermark_text_background_opacity_value'] = (int)$options['optimize_images_watermarks_watermark_text_background_opacity_value'];
																						$options['optimize_images_watermarks_watermark_text_background_opacity_value'] = abs($options['optimize_images_watermarks_watermark_text_background_opacity_value']);
																						if($options['optimize_images_watermarks_watermark_text_background_opacity_value']>100) {
																							$options['optimize_images_watermarks_watermark_text_background_opacity_value'] = 100;
																						}
																						
																						$paramsWatermarkOptions['text']['boxOpacity'] = $options['optimize_images_watermarks_watermark_text_opacity_value'] / 100;
																					}
																				}
																			}
																		}
																	}//optimize_images_watermarks_watermark_text_background_enable
																	
																	if(isset($options['optimize_images_watermarks_watermark_text_outline_enable']) && $options['optimize_images_watermarks_watermark_text_outline_enable']) {
																		
																		
																		if(isset($options['optimize_images_watermarks_watermark_text_outline_width']) && $options['optimize_images_watermarks_watermark_text_outline_width']) {
																			$options['optimize_images_watermarks_watermark_text_outline_width'] = preg_replace('#[^0-9]+#i','',$options['optimize_images_watermarks_watermark_text_outline_width']);
																			$options['optimize_images_watermarks_watermark_text_outline_width'] = (int)$options['optimize_images_watermarks_watermark_text_outline_width'];
																			if($options['optimize_images_watermarks_watermark_text_outline_width']>0) {
																			
																				if(isset($options['optimize_images_watermarks_watermark_text_outline_color']) && $options['optimize_images_watermarks_watermark_text_outline_color']) {
																					$options['optimize_images_watermarks_watermark_text_outline_color'] = preg_replace('#[^a-z0-9]+#i','',$options['optimize_images_watermarks_watermark_text_outline_color']);
																					$options['optimize_images_watermarks_watermark_text_outline_color'] = strtolower($options['optimize_images_watermarks_watermark_text_outline_color']);
																					$options['optimize_images_watermarks_watermark_text_outline_color'] = trim($options['optimize_images_watermarks_watermark_text_outline_color']);
																					if($options['optimize_images_watermarks_watermark_text_outline_color']) {
																						
																						$paramsWatermarkOptions['text']['strokeWidth'] = $options['optimize_images_watermarks_watermark_text_outline_width'];
																						$paramsWatermarkOptions['text']['strokeColor'] = PepVN_Data::hex2rgb('#'.$options['optimize_images_watermarks_watermark_text_outline_color']);
																						
																					}
																				}
																				
																			}
																		}
																		
																	}//optimize_images_watermarks_watermark_text_outline_enable
																	
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
							
							
						}//type text 
						
						if(isset($options['optimize_images_watermarks_watermark_type']['image'])) {
							
							if(isset($options['optimize_images_watermarks_watermark_image_url']) && $options['optimize_images_watermarks_watermark_image_url']) {
								$options['optimize_images_watermarks_watermark_image_url'] = trim($options['optimize_images_watermarks_watermark_image_url']);
								if($options['optimize_images_watermarks_watermark_image_url']) {
								
									if(Utils::isUrl($options['optimize_images_watermarks_watermark_image_url']) && Utils::isImageUrl($options['optimize_images_watermarks_watermark_image_url'])) {
										
										$imgWatermarkFilePath1 = $this->_folderCachePath;
										$imgWatermarkFilePath1 .= md5($options['optimize_images_watermarks_watermark_image_url']).'.txt';
										
										if(!is_file($imgWatermarkFilePath1)) {
											file_put_contents($imgWatermarkFilePath1,'');
											
											$valueTemp1 = $remote->get($options['optimize_images_watermarks_watermark_image_url'],array(
												'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
											));
											
											if($valueTemp1) {
												file_put_contents($imgWatermarkFilePath1,$valueTemp1);
											}
										}
										
										if(is_file($imgWatermarkFilePath1)) {
											$valueTemp1 = filesize($imgWatermarkFilePath1);
											if($valueTemp1 && ($valueTemp1>0)) {
												
												if($this->_isImageFileCanProcess($imgWatermarkFilePath1)) {
													
													$paramsWatermarkOptions['image']['watermark_image_file_path'] = $imgWatermarkFilePath1;
													
													if(isset($options['optimize_images_watermarks_watermark_image_width']) && $options['optimize_images_watermarks_watermark_image_width']) {
														$options['optimize_images_watermarks_watermark_image_width'] = preg_replace('#[^0-9\%]+#i','',$options['optimize_images_watermarks_watermark_image_width']);
														$options['optimize_images_watermarks_watermark_image_width'] = trim($options['optimize_images_watermarks_watermark_image_width']);
														if(false === stripos($options['optimize_images_watermarks_watermark_image_width'],'%')) {
															$options['optimize_images_watermarks_watermark_image_width'] = (int)$options['optimize_images_watermarks_watermark_image_width'];
														}
													}
													
													if(isset($options['optimize_images_watermarks_watermark_image_margin_x']) && $options['optimize_images_watermarks_watermark_image_margin_x']) {
														$options['optimize_images_watermarks_watermark_image_margin_x'] = preg_replace('#[^0-9]+#i','',$options['optimize_images_watermarks_watermark_image_margin_x']);
														$options['optimize_images_watermarks_watermark_image_margin_x'] = trim($options['optimize_images_watermarks_watermark_image_margin_x']);
														$options['optimize_images_watermarks_watermark_image_margin_x'] = (int)$options['optimize_images_watermarks_watermark_image_margin_x'];
														$options['optimize_images_watermarks_watermark_image_margin_x'] = abs($options['optimize_images_watermarks_watermark_image_margin_x']);
														
													}
													
													if(isset($options['optimize_images_watermarks_watermark_image_margin_y']) && $options['optimize_images_watermarks_watermark_image_margin_y']) {
														$options['optimize_images_watermarks_watermark_image_margin_y'] = preg_replace('#[^0-9]+#i','',$options['optimize_images_watermarks_watermark_image_margin_y']);
														$options['optimize_images_watermarks_watermark_image_margin_y'] = trim($options['optimize_images_watermarks_watermark_image_margin_y']);
														$options['optimize_images_watermarks_watermark_image_margin_y'] = (int)$options['optimize_images_watermarks_watermark_image_margin_y'];
														$options['optimize_images_watermarks_watermark_image_margin_y'] = abs($options['optimize_images_watermarks_watermark_image_margin_y']);
													}
												}
											}
										}
									}
								}
							}
							
						}//type image
						
					}
					
				}
				
				
			}
			
		}
		
		$options['optimize_images_image_quality_value'] = abs((int)$options['optimize_images_image_quality_value']);
		if(($options['optimize_images_image_quality_value'] >= 10) && ($options['optimize_images_image_quality_value'] <= 100)) {
		} else {
			$options['optimize_images_image_quality_value'] = 100;
		}
		
		$options['optimize_images_rename_img_filename_value'] = (string)$options['optimize_images_rename_img_filename_value'];
		$options['optimize_images_rename_img_filename_value'] = trim($options['optimize_images_rename_img_filename_value']);
		
		$options['optimize_images_maximum_files_handled_each_request'] = abs((int)$options['optimize_images_maximum_files_handled_each_request']);
		
		$resultData['options'] = $options;
		$resultData['paramsWatermarkOptions'] = $paramsWatermarkOptions;
		
		TempDataAndCacheFile::set_cache($keyCache1,$resultData);
		
		return $resultData;
	}
	
	private function _check_get_screen_width()
	{
		$keyCache1 = crc32(__CLASS__ . __METHOD__);
		if(!isset(self::$_tempData[$keyCache1])) {
			self::$_tempData[$keyCache1] = 0;
			
			$options = self::getOption();
			
			if(
				isset($options['optimize_images_auto_resize_images_enable'])
				&& ('on' === $options['optimize_images_auto_resize_images_enable'])
			) {
				$device = $this->di->getShared('device');
				self::$_tempData[$keyCache1] = $device->get_device_screen_width();
			}
		}
		
		return self::$_tempData[$keyCache1];
	}
	
	
	/*
	Use for rename image's file & image's attributes
	*/
	private function _get_patterns_for_parse_custom_tag_of_image($input_parameters)
	{
		$classMethodKey = crc32(__CLASS__ . __METHOD__);
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$resultData = false;
		
		$postObj = false;
		
		if(isset($input_parameters['post_id']) && $input_parameters['post_id']) {
			$input_parameters['post_id'] = (int)$input_parameters['post_id'];
			if($input_parameters['post_id'] > 0) {
				$postObj = $wpExtend->get_post($input_parameters['post_id']);
			}
		} else {
			global $post;
			if($post) {
				if(isset($post->ID) && $post->ID) {
					$postObj = $post;
				}
			}
		}
		
		if($postObj) {
			
			if(isset($postObj->ID) && $postObj->ID) {
				
				$keyCache1 = Utils::hashKey(array(
					$classMethodKey
					, $input_parameters
					, $postObj->ID
				));
				
				$resultData = TempDataAndCacheFile::get_cache($keyCache1);
				
				if(null === $resultData) {
					
					$resultData = array();
					
					$arrayPatterns1 = array(
						'title' => ''
						,'category' => ''
						,'tags' => ''
						,'product_cat' => ''
						,'product_tag' => ''
						,'img_name' => ''
						,'img_title' => ''
						,'img_alt' => ''
					);
					
					if(isset($input_parameters['patterns']) && $input_parameters['patterns']) {
						$input_parameters['patterns'] = (array)$input_parameters['patterns'];
						$input_parameters['patterns'] = PepVN_Data::cleanArray($input_parameters['patterns']);
						if(!empty($input_parameters['patterns'])) {
							foreach($input_parameters['patterns'] as $key1 => $value1) {
								$key1 = trim($key1);
								if($key1) {
									$arrayPatterns1[$key1] = $value1;
								}
							}
						}
					}
					
					$rsGetTerms = $wpExtend->getTermsByPostId($postObj->ID);
					
					foreach($arrayPatterns1 as $key1 => $value1) {
						
						if(empty($value1)) {
							
							if('title' === $key1) {
								$value1 = $postObj->post_title;
							} else {
								
								$rsTwo = array();
								
								foreach($rsGetTerms as $key2 => $value2) {
									if($value2) {
										if(isset($value2['term_id']) && $value2['term_id']) {
											if($key1 === $value2['termType']) {
												$rsTwo[] = $value2['name'];
											}
										}
									}
								}
								$rsTwo = PepVN_Data::cleanArray($rsTwo);
								if(!empty($rsTwo)) {
									$value1 = implode(' ',$rsTwo);
								}
							}
						}
						
						$value1 = trim($value1);
						$resultData['%'.$key1] = $value1;
						
					}
					
					TempDataAndCacheFile::set_cache($keyCache1, $resultData);
					
				}
			}
		}
		
		return $resultData;
	}
	
	
	
	/*
		$input_parameters = array(
			'raw_tag' => string
			,'post_id' => interger
			,'patterns' => array(
				'title' => string
				,'category' => string
				,'tags' => string
				,'img_name' => string
				,'img_title' => string
				,'img_alt' => string
			)
		)
		optimize_images_parse_custom_tag_of_image($input_parameters);
	*/
	
	public function parse_custom_tag_of_image($input_parameters)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__
			,__METHOD__
			, $input_parameters
		));
		
		$input_raw_tag = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null === $input_raw_tag) {
			
			$input_raw_tag = '';
			
			if(isset($input_parameters['raw_tag']) && $input_parameters['raw_tag']) {
				
				$input_raw_tag = $input_parameters['raw_tag'];
				$input_raw_tag = (string)$input_raw_tag;
				$input_raw_tag = trim($input_raw_tag);
				
				$patterns = $this->_get_patterns_for_parse_custom_tag_of_image($input_parameters);
				if($patterns && is_array($patterns)) {
					if(!empty($patterns)) {
						$input_raw_tag = str_replace(array_keys($patterns),array_values($patterns),$input_raw_tag);
					}
				}
				
				$input_raw_tag = preg_replace('#[% \s]+#is',' ',$input_raw_tag);
				$input_raw_tag = Utils::removeQuotes($input_raw_tag);
				$input_raw_tag = preg_replace('#^[\- \s]+#is','',$input_raw_tag);
				$input_raw_tag = preg_replace('#[\- \s]+$#is','',$input_raw_tag);
				$input_raw_tag = preg_replace('#[\-]+[\s ]+[\-]+#is',' - ',$input_raw_tag);
				
				$input_raw_tag = Text::reduceSpace($input_raw_tag);
				$input_raw_tag = trim($input_raw_tag);
				
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $input_raw_tag);
		}
		
		return $input_raw_tag;
	}
	
	
	public function check_set_size_image_tag($image_tag_text, $image_size, $override_status = false)
	{
		
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$image_tag_text
			,$image_size
			,$override_status
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		if(
			(
				isset($image_size['width'])
				&& ($image_size['width'])
			)
				||
			(
				isset($image_size['height'])
				&& ($image_size['height'])
			)
		) {
			$width = 0;
			$height = 0;
			
			if(preg_match('#<img[^>]+width=(\'|\")([0-9]+)\1[^>]*?>#is',$image_tag_text,$matched1)) {
				if(isset($matched1[2]) && $matched1[2]) {
					$matched1 = (int)$matched1[2];
					if($matched1>0) {
						$width = $matched1;
					}
				}
			}
			unset($matched1);
			
			if(preg_match('#<img[^>]+height=(\'|\")([0-9]+)\1[^>]*?>#is',$image_tag_text,$matched1)) {
				if(isset($matched1[2]) && $matched1[2]) {
					$matched1 = (int)$matched1[2];
					if($matched1>0) {
						$height = $matched1;
					}
				}
			}
			unset($matched1);
			
			$quoteMarkUse = '';
			
			if(preg_match('#<img[^>]+src=(\'|\")[^\'\"]+\1[^>]*?>#is',$image_tag_text,$matched1)) {
				if(isset($matched1[1]) && $matched1[1]) {
					$matched1 = trim($matched1[1]);
					$quoteMarkUse = $matched1;
				}
			}
			unset($matched1);
			
			if(!$quoteMarkUse) {
				$quoteMarkUse = '"';
			}
			
			if(
				isset($image_size['width'])
				&& ($image_size['width'])
			) {
				$checkStatus1 = false;
				if($width>0) {
					if($override_status) {
						$checkStatus1 = true;
						$image_tag_text = preg_replace('#width=(\'|\")([0-9]+)\1#is',' ',$image_tag_text);
					}
				} else {
					$checkStatus1 = true;
				}
				
				if($checkStatus1) {
					$image_tag_text = preg_replace('#<img[\s \t]+#is','<img width='.$quoteMarkUse.$image_size['width'].$quoteMarkUse.' ', $image_tag_text);
				}
			}
			
			if(
				isset($image_size['height'])
				&& ($image_size['height'])
			) {
				$checkStatus1 = false;
				if($height>0) {
					if($override_status) {
						$checkStatus1 = true;
						$image_tag_text = preg_replace('#height=(\'|\")([0-9]+)\1#is',' ',$image_tag_text);
					}
				} else {
					$checkStatus1 = true;
				}
				
				if($checkStatus1) {
					$image_tag_text = preg_replace('#<img[\s \t]+#is','<img height='.$quoteMarkUse.$image_size['height'].$quoteMarkUse.' ', $image_tag_text);
				}
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $image_tag_text);
			
		}
		
		return $image_tag_text;
	}
	
	
	public function get_max_size_image_tag($image_tag_text)
	{
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$image_tag_text
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		$resultData = array(
			'width' => 0
			,'height' => 0
		);
		
		if(preg_match('#<img[^>]+width=(\'|\")([0-9]+)\1[^>]*?>#is',$image_tag_text,$matched1)) {
			if(isset($matched1[2]) && $matched1[2]) {
				$matched1 = (int)$matched1[2];
				if($matched1>0) {
					$resultData['width'] = $matched1;
				}
			}
		}
		unset($matched1);
		
		if(preg_match('#<img[^>]+height=(\'|\")([0-9]+)\1[^>]*?>#is',$image_tag_text,$matched1)) {
			if(isset($matched1[2]) && $matched1[2]) {
				$matched1 = (int)$matched1[2];
				if($matched1>0) {
					$resultData['height'] = $matched1;
				}
			}
		}
		unset($matched1);
		
		$screen_width = $this->_check_get_screen_width();
		if(0 === $resultData['width']) {
			if($screen_width>0) {
				$resultData['width'] = $screen_width + 1;
			}
		}
		
		if($resultData['width'] > $screen_width) {
			if($resultData['height'] > 0) {
				$resultData['height'] = ($screen_width * $resultData['height']) / $resultData['width'];
				$resultData['height'] = ceil($resultData['height']);
			}
			$resultData['width'] = $screen_width;
		}
		
		TempDataAndCacheFile::set_cache($keyCache1,$resultData);
		
		return $resultData;
	}
	
	
	public function process_text($text)
	{
		
		$device_screen_width = $this->_check_get_screen_width();
		
		$options = self::getOption();
		
		if(!isset($options['optimize_images_alttext'])) {
			$options['optimize_images_alttext'] = '';
		}
		if(!isset($options['optimize_images_titletext'])) {
			$options['optimize_images_titletext'] = '';
		}
		
		$optimize_images_alttext = trim($options['optimize_images_alttext']);
		$optimize_images_titletext = trim($options['optimize_images_titletext']);
		
		if(
			('on' !== $options['optimize_images_optimize_image_file_enable'])
			&& !$optimize_images_alttext
			&& !$optimize_images_titletext
		) {
			return $text;
		}
		
		global $wpdb, $post;
		
		$classMethodKey = crc32(__CLASS__ . __METHOD__);
		
		$currentPostId = 0;
		
		if(isset($post->ID) && $post->ID) {
			$currentPostId = $post->ID;
		}
		
		$currentPostId = (int)$currentPostId;
		if(!$currentPostId) {
			if(System::function_exists('get_the_ID')) {
				$tmp = get_the_ID();
				$tmp = (int)$tmp;
				if($tmp>0) {
					$currentPostId = $tmp;
				}
			}
		}
		
		$parametersPrimary = array();
		
		$patternsEscaped = array();
		
		$patternsReplace = array();
		$patternsReplaceImgSrc = array();
		$patternsPregReplace = array();
		
		$rsSettingWatermarksFirstOptions = $this->parse_watermarks_first_options(array(
			'options' => $options
		));
		
		$paramsWatermarkOptions = $rsSettingWatermarksFirstOptions['paramsWatermarkOptions'];
		$options = $rsSettingWatermarksFirstOptions['options'];
		unset($rsSettingWatermarksFirstOptions);
		
		$options['optimize_images_rename_img_filename_value'] = trim($options['optimize_images_rename_img_filename_value']);
		
		$keyCacheProcessText = Utils::hashKey(array(
			$classMethodKey
			,$text
			,'process_text'
			,'dvsw_'.$device_screen_width
			,$options
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCacheProcessText,true);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		
		//Begin process image file
		if(('on' === $options['optimize_images_optimize_image_file_enable']) && $this->_isSystemReadyToHandleImagesStatus) {
			
			preg_match_all('#<img[^<>]+(\'|\")(https?:)?//'.Utils::preg_quote(PepVN_Data::$defaultParams['fullDomainName']).'[^\'\"]+\.('.implode('|',$this->imageExtensionsAllow).')\??[^\'\"]*\1#is',$text,$matched1);
			
			if(isset($matched1[0]) && $matched1[0] && (!empty($matched1[0]))) {
				
				$matched1 = $matched1[0];
				
				foreach($matched1 as $key1 => $value1) {
					
					unset($matched1[$key1]);
					
					if($value1) {
					
						preg_match('#(\'|\")((https?:)?//[^\"\']+)\1#i',$value1,$matched2);
						
						if(isset($matched2[2]) && $matched2[2]) {
							
							$matched2 = trim($matched2[2]);
							
							if($matched2) {
								
								$imgName1 = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
								
								$imgInfo1 = pathinfo($matched2);
								if(isset($imgInfo1['filename'])) {
									$imgName1 = $imgInfo1['filename'];
								}
								
								$processImageOptions1 = array(
									'optimized_image_file_name' => trim($imgName1)
									,'original_image_src' => $matched2
									,'action' => 'do_process_image'
								);
								
								if($device_screen_width>0) {
									$processImageOptions1['resize_max_width'] = $device_screen_width;
								}
								
								$rsProcessImage1 = $this->process_image($processImageOptions1);
								
								unset($processImageOptions1);
								
								if($rsProcessImage1['image_optimized_file_url']) {
									$valueTemp1 = '//'.PepVN_Data::removeProtocolUrl($matched2);
									$valueTemp2 = '//'.PepVN_Data::removeProtocolUrl($rsProcessImage1['image_optimized_file_url']);
									if($valueTemp1 !== $valueTemp2) {
										$patternsReplaceImgSrc[$valueTemp1] = $valueTemp2;
									}
									unset($valueTemp1,$valueTemp2);
								}
								
								unset($rsProcessImage1);
							}
						}
					}
				}
				
			}
			
		}
		unset($matched1);
		
		
		//Begin process image's attributes
		
		if(preg_match_all('#<img[^>]+\\\?>#i', $text, $matched1)) {
			
			if(isset($matched1[0]) && is_array($matched1[0]) && (!empty($matched1[0]))) {
				
				$matched1 = $matched1[0];
				
				foreach($matched1 as $keyOne => $valueOne) {
					
					unset($matched1[$keyOne]);
					
					$oldImgTag = $valueOne;
					$newImgTag = $valueOne;
					
					$imgTitle1 = '';
					$imgAlt1 = '';
					$imgName = '';
					$imgFullName = '';
					
					$imgSrc = '';
					$imgInfo = false; 
					
					$matched2 = 0;
					
					if(preg_match('#title=(\'|")([^"\']+)\1#i',$valueOne,$matched2)) {
						if(isset($matched2[2]) && $matched2[2]) {
							$imgTitle1 = trim($matched2[2]);
						}
					}
					
					$matched2 = 0;
					if(preg_match('#alt=(\'|")([^"\']+)\1#i',$valueOne,$matched2)) {
						if(isset($matched2[2]) && $matched2[2]) {
							$imgAlt1 = trim($matched2[2]);
						}
					}
					
					$matched2 = 0;
					if(preg_match('#src=(\'|")(https?://[^"\']+)\1#i',$valueOne,$matched2)) {
						if(isset($matched2[2]) && $matched2[2]) {
							$imgSrc = trim($matched2[2]);
							$imgName = trim($matched2[2]);
						}
					}
					
					$matched2 = 0;
					
					$imgName = trim($imgName);
					if($imgName) {
						$imgInfo = pathinfo($imgName);
						if(isset($imgInfo['filename'])) {
							$imgName = $imgInfo['filename'];
							$imgFullName = $imgInfo['basename'];
							
						}
					}
					$imgName = trim($imgName);
					
					$imgTitle1 = PepVN_Data::cleanKeyword($imgTitle1);
					$imgAlt1 = PepVN_Data::cleanKeyword($imgAlt1);
					$imgName = PepVN_Data::cleanKeyword($imgName);
					
					if(
						($imgAlt1 && ('on' === $options['optimize_images_override_alt']))
						|| !$imgAlt1
					) {
						$optimize_images_alttext1 = $this->parse_custom_tag_of_image(array(
							'raw_tag' => $optimize_images_alttext
							,'post_id' => $currentPostId
							,'patterns' => array(
								'img_name' => $imgName
								,'img_title' => $imgTitle1
								,'img_alt' => $imgAlt1
							)
						));
						
						if($optimize_images_alttext1) {
							$newImgTag = preg_replace('#alt=(\'|")([^"\']+)?\1#i','',$newImgTag);
							$newImgTag = preg_replace('#<img(.+)#is', '<img alt="'.$optimize_images_alttext1.'" \\1', $newImgTag);
						}
					}
					
					if(
						($imgTitle1 && ('on' === $options['optimize_images_override_alt']))
						|| !$imgTitle1
					) {
						$optimize_images_titletext1 = $this->parse_custom_tag_of_image(array(
							'raw_tag' => $optimize_images_titletext
							,'post_id' => $currentPostId
							,'patterns' => array(
								'img_name' => $imgName
								,'img_title' => $imgTitle1
								,'img_alt' => $imgAlt1
							)
						));
						
						if($optimize_images_titletext1) {
							$newImgTag = preg_replace('#title=(\'|")([^"\']+)?\1#i','',$newImgTag);
							$newImgTag = preg_replace('#<img(.+)#is', '<img title="'.$optimize_images_titletext1.'" \\1', $newImgTag);
						}
					}
					
					if($imgSrc) {
						
						//Begin Process Image File 
						
						$checkStatus2 = false;
						
						if($this->_isSystemReadyToHandleImagesStatus) {
							if(Utils::isUrl($imgSrc)) { 
								$checkStatus2 = true;
							}
						}
						
						if($checkStatus2) {
							
							$imgNewName = $imgName;
							
							if($options['optimize_images_rename_img_filename_value']) {
								
								$optimize_images_rename_img_filename_value = $options['optimize_images_rename_img_filename_value'];
								
								$optimize_images_rename_img_filename_value = $this->parse_custom_tag_of_image(array(
									'raw_tag' => $optimize_images_rename_img_filename_value
									,'post_id' => $currentPostId
									,'patterns' => array(
										'img_name' => $imgName
										,'img_title' => $imgTitle1
										,'img_alt' => $imgAlt1
									)
								));
								
								if($optimize_images_rename_img_filename_value) {
									$imgNewName = $optimize_images_rename_img_filename_value;
								}
							}
							
							$max_size_image_tag = $this->get_max_size_image_tag($oldImgTag);
							
							$imgOptimizedFilePathExists1 = false;
							
							$processImageOptions1 = array(
								'optimized_image_file_name' => $imgNewName
								,'original_image_src' => $imgSrc
								,'options' => $options
								,'paramsWatermarkOptions' => $paramsWatermarkOptions
							);
							
							if($max_size_image_tag['width'] > 0) {
								$processImageOptions1['resize_max_width'] = $max_size_image_tag['width'];
							}
							if($max_size_image_tag['height'] > 0) {
								$processImageOptions1['resize_max_height'] = $max_size_image_tag['height'];
							}
							$rsProcessImage1 = $this->process_image($processImageOptions1);
							unset($processImageOptions1);
							
							if($rsProcessImage1['image_optimized_file_path']) {
								$imgOptimizedFilePathExists1 = $rsProcessImage1['image_optimized_file_path'];
							}
							
							if(false === $imgOptimizedFilePathExists1) {
							
								if(isset($options['optimize_images_optimize_image_file_enable']) && ('on' === $options['optimize_images_optimize_image_file_enable'])) {
									
									$processImageOptions1 = array(
										'optimized_image_file_name' => $imgNewName
										,'original_image_src' => $imgSrc
										,'options' => $options
										,'paramsWatermarkOptions' => $paramsWatermarkOptions
										,'action' => 'do_process_image'
									);
									
									if($max_size_image_tag['width'] > 0) {
										$processImageOptions1['resize_max_width'] = $max_size_image_tag['width'];
									}
									if($max_size_image_tag['height'] > 0) {
										$processImageOptions1['resize_max_height'] = $max_size_image_tag['height'];
									}
									$rsProcessImage1 = $this->process_image($processImageOptions1);
									unset($processImageOptions1);
									
									if($rsProcessImage1['image_optimized_file_path']) {
										$imgOptimizedFilePathExists1 = $rsProcessImage1['image_optimized_file_path'];
									}
								}
							}
							
							
							if($imgOptimizedFilePathExists1) {
								if(is_file($imgOptimizedFilePathExists1)) {
									if(filesize($imgOptimizedFilePathExists1)>0) {
										
										$rs_getimagesize1 = $this->_getimagesize($imgOptimizedFilePathExists1);
										
										if(isset($rs_getimagesize1[0]) && $rs_getimagesize1[0]) {
											
											$newImgTag = $this->check_set_size_image_tag($newImgTag,array(
												'width' => $rs_getimagesize1[0]
												,'height' => $rs_getimagesize1[1]
											),true);
											
											$imgSrc2 = str_replace($this->_folderStorePath,$this->_folderStoreUrl,$imgOptimizedFilePathExists1);
											
											$newImgTag = str_replace($imgSrc,$imgSrc2,$newImgTag);
											
											$patternsReplaceImgSrc[$imgSrc] = $imgSrc2;
											
										}
										
									}
								}
							}
							
						}
						
					}
					
					if($oldImgTag !== $newImgTag) {
						$patternsReplace[$oldImgTag] = $newImgTag; 
					}
					
				}
			
			}
			
			
			if($patternsReplace && !empty($patternsReplace)) {
				$text = str_replace(array_keys($patternsReplace),array_values($patternsReplace),$text);
			}
			unset($patternsReplace);
			
			if($patternsPregReplace && !empty($patternsPregReplace)) {
				$text = preg_replace(array_keys($patternsPregReplace),array_values($patternsPregReplace),$text);
			}
			unset($patternsPregReplace);
			
			
			if($patternsReplaceImgSrc && !empty($patternsReplaceImgSrc)) {
				
				$patternsReplaceImgSrc2 = array();
				
				foreach($patternsReplaceImgSrc as $keyOne => $valueOne) {
					unset($patternsReplaceImgSrc[$keyOne]);
					if($keyOne && $valueOne) {
						$tmp1 = '#([\'\"\s \t]*?)'.PepVN_Data::preg_quote($keyOne).'([\'\"\s \t]*?)#is';
						$tmp2 = '\1'.$valueOne.'\2';
						$patternsReplaceImgSrc2[$tmp1] = $tmp2;
					}
				}
				
				if(!empty($patternsReplaceImgSrc2)) {
					$text = preg_replace(array_keys($patternsReplaceImgSrc2),array_values($patternsReplaceImgSrc2),$text);
				}
			}
			
		}
		
		/*
		if('on' === $options['optimize_images_images_lazy_load_enable']) {
			//$text = $this->process_allimagestags_lazyload($text,0);
		}
		*/
		
		$text = trim($text);
		
		TempDataAndCacheFile::set_cache($keyCacheProcessText,$text,true);
		
		return $text;

	} 
	
	public function isHasGmagick()
	{
		if(System::extension_loaded('gmagick')) {
			if(System::class_exists('\Gmagick')) {
				return true;
			}
		}
		
		return false;
	}
	
	public function isHasImagick()
	{
		if(System::extension_loaded('imagick')) {
			if(System::class_exists('\Imagick')) {
				return true;
			}
		}
		
		return false;
	}
	
	public function optimize_lossy_image_file($original_file_path)
	{
		
		if(is_file($original_file_path) && is_readable($original_file_path)) {
			clearstatcache(true, $original_file_path);
			$original_file_size = filesize($original_file_path);
			
			if($original_file_size > 0) {
				
				$rsGetImageFileInfo = $this->_getImageFileInfo($original_file_path);
				
				if(isset($rsGetImageFileInfo['image_type']) && $rsGetImageFileInfo['image_type']) {
					
					$optimizedImageTmpFilePath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_GENERAL_DIR . md5($original_file_path . mt_rand()) .'_'. basename($original_file_path) . '.tmp';
					
					copy($original_file_path,$optimizedImageTmpFilePath);
					
					if(is_file($optimizedImageTmpFilePath)) {
						
						if(
							('gif' === $rsGetImageFileInfo['image_type'])
							&& PepVN_Images::isAnimation($original_file_path)
						) {
							
						} else {
							
							if($this->isHasGmagick()) {
								try {
									$gmagick = new \Gmagick( $optimizedImageTmpFilePath );
									$gmagick->stripimage();
									$gmagick->setimageformat( strtoupper($rsGetImageFileInfo['image_type']) );
									$gmagick->writeimage( $optimizedImageTmpFilePath );
									$gmagick->destroy();
									unset($gmagick);
								} catch ( Exception $e ) {
									
								}
							}
							
							if($this->isHasImagick()) {
								try {
									$imagick = new \Imagick( $optimizedImageTmpFilePath );
									$imagick->stripImage();
									$imagick->setImageFormat( strtoupper($rsGetImageFileInfo['image_type']) );
									$imagick->setImageCompressionQuality(90);
									$imagick->writeImage( $optimizedImageTmpFilePath );
									$imagick->clear();
									unset($gmagick);
								} catch ( Exception $e ) {
									
								}
							}
							
							clearstatcache(true, $optimizedImageTmpFilePath);
							$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
							
							if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
								$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
								if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
									System::unlink($original_file_path);
									copy($optimizedImageTmpFilePath,$original_file_path);
								}
							}
						}
						
						if(
							!System::isSafeMode()
							&& !System::isDisableFunction('exec')
							&& ( strtolower(PHP_OS) === 'linux')
						) {
							
							if('jpg' === $rsGetImageFileInfo['image_type']) {
								
								$toolPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes/optimize-images/linux/';
								
								if(System::isOS64b()) {
									$toolPath .= 'x64/';
								}
								
								$toolPath .= 'jpegtran';
								
								if(is_file($toolPath) && is_executable($toolPath)) {
									
									// run jpegtran - non-progressive
									copy($original_file_path,$optimizedImageTmpFilePath);
									exec( $toolPath . ' -copy none -optimize -outfile ' . escapeshellarg( $optimizedImageTmpFilePath ) . ' ' . escapeshellarg( $optimizedImageTmpFilePath ) );
									
									clearstatcache(true, $optimizedImageTmpFilePath);
									if(is_file($optimizedImageTmpFilePath)) {
										clearstatcache(true, $optimizedImageTmpFilePath);
										$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
										
										clearstatcache(true, $original_file_path);
										$original_file_size = filesize($original_file_path);
										
										if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
											$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
											if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
												System::unlink($original_file_path);
												copy($optimizedImageTmpFilePath,$original_file_path);
											}
										}
									}
									
									
									// run jpegtran - progressive
									copy($original_file_path,$optimizedImageTmpFilePath);
									exec( $toolPath . ' -copy none -optimize -progressive -outfile ' . escapeshellarg( $optimizedImageTmpFilePath ) . ' ' . escapeshellarg( $optimizedImageTmpFilePath ) );
									
									clearstatcache(true, $optimizedImageTmpFilePath);
									if(is_file($optimizedImageTmpFilePath)) {
										clearstatcache(true, $optimizedImageTmpFilePath);
										$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
										
										clearstatcache(true, $original_file_path);
										$original_file_size = filesize($original_file_path);
										
										if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
											$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
											if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
												System::unlink($original_file_path);
												copy($optimizedImageTmpFilePath,$original_file_path);
											}
										}
										
									}
								}
								
							} else if('png' === $rsGetImageFileInfo['image_type']) {
								
								$toolPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes/optimize-images/linux/pngquant';
								if(is_file($toolPath) && is_executable($toolPath)) {
									copy($original_file_path,$optimizedImageTmpFilePath);
									exec( $toolPath . ' --quality=70-90 --force --speed=5 --output='.escapeshellarg( $optimizedImageTmpFilePath ).' ' . escapeshellarg( $optimizedImageTmpFilePath ) );
									
									clearstatcache(true, $optimizedImageTmpFilePath);
									if(is_file($optimizedImageTmpFilePath)) {
										
										$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
										
										clearstatcache(true, $original_file_path);
										$original_file_size = filesize($original_file_path);
										
										if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
											$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
											if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
												System::unlink($original_file_path);
												copy($optimizedImageTmpFilePath,$original_file_path);
											}
										}
										
									}
								}
								
								
								$toolPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes/optimize-images/linux/optipng';
								if(is_file($toolPath) && is_executable($toolPath)) {
									copy($original_file_path,$optimizedImageTmpFilePath);
									exec( $toolPath . ' -o1 -clobber -fix -quiet -strip all ' . escapeshellarg( $optimizedImageTmpFilePath ) );
									
									clearstatcache(true, $optimizedImageTmpFilePath);
									if(is_file($optimizedImageTmpFilePath)) {
										$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
										
										clearstatcache(true, $original_file_path);
										$original_file_size = filesize($original_file_path);
										
										if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
											$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
											if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
												System::unlink($original_file_path);
												copy($optimizedImageTmpFilePath,$original_file_path);
											}
										}
									}
								}
							} else if('gif' === $rsGetImageFileInfo['image_type']) {
								
								$toolPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes/optimize-images/linux/gifsicle';
								if(is_file($toolPath) && is_executable($toolPath)) {
									copy($original_file_path,$optimizedImageTmpFilePath);
									exec( $toolPath . ' -b -O6 --careful -o ' . escapeshellarg( $optimizedImageTmpFilePath ) .' '.escapeshellarg( $optimizedImageTmpFilePath ) );
									
									clearstatcache(true, $optimizedImageTmpFilePath);
									if(is_file($optimizedImageTmpFilePath)) {
										$optimizedImageTmpFileSize = filesize($optimizedImageTmpFilePath);
										
										clearstatcache(true, $original_file_path);
										$original_file_size = filesize($original_file_path);
										
										if(($optimizedImageTmpFileSize > 0) && ($original_file_size > $optimizedImageTmpFileSize)) {
											$tmp = PepVN_Images::getImageInfo($optimizedImageTmpFilePath, false);
											if(isset($tmp['image_type']) && $tmp['image_type'] && ($rsGetImageFileInfo['image_type'] === $tmp['image_type'])) {
												System::unlink($original_file_path);
												copy($optimizedImageTmpFilePath,$original_file_path);
											}
										}
										
									}
								}
							}
							
						}
						
						clearstatcache(true, $optimizedImageTmpFilePath);
						if(is_file($optimizedImageTmpFilePath)) {
							System::unlink($optimizedImageTmpFilePath);
						}
					}
					
					
				}
				
			}
			
		}
		
	}
	
	private function _remove_old_structure_file()
	{
		
		$dir = $this->_folderStorePath;
		
		$objects = scandir($dir);
		
		if(is_array($objects)) {
			$objects = array_diff($objects, array('.','..')); 
			foreach($objects as $objIndex => $objPath) {
				unset($objects[$objIndex]);
				if($objPath) {
					if(strlen($objPath) > 5) {
						$objFullPath = $dir . $objPath;
						if(is_dir($objFullPath)) {
							if(is_readable($objFullPath) && is_writable($objFullPath)) {
								System::rmdirR($objFullPath);
							}
						}
					}
					
				}
			}
		}
		
	}
	
	
	public function findAndOptimizeLossyImageFiles($dir)
	{
		$imageExtensionsAllow = $this->imageExtensionsAllow;
		
		$matchPattern = '#('.implode('|',$imageExtensionsAllow).')$#is';
		
		$rsScandirR = System::scandirR($dir,$matchPattern);
		
		unset($rsScandirR['dirs']);
		
		$rsScandirR['files'] = array_unique($rsScandirR['files']);
		
		foreach($rsScandirR['files'] as $key1 => $value1) {
			
			unset($rsScandirR['files'][$key1]);
			
			$this->optimize_lossy_image_file($value1);
			
		}
		
	}
}


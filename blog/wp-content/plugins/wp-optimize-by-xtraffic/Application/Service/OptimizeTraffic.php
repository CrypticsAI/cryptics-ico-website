<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WPOptimizeByxTraffic\Application\Service\AnalyzeText
	,WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\Text
	,WpPepVN\DependencyInjection
;

class OptimizeTraffic
{
	const OPTION_NAME = 'optimize_traffic';
	
	protected static $_tempData = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
	}
    
	public function initFrontend() 
    {
        $wpExtend = $this->di->getShared('wpExtend');
		
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		$options = self::getOption();
		
		if(isset($options['optimize_traffic_modules']) && !empty($options['optimize_traffic_modules'])) {
			
			if(
				(
					$wpExtend->is_single()
					|| $wpExtend->is_page()
					|| $wpExtend->is_singular()
				)
				&& !$wpExtend->is_feed()
				&& !$wpExtend->is_front_page()
				&& !$wpExtend->is_home()
			) {
				add_filter('the_content', array($this,'process_text'), $priorityLast);
			}
		}
		
		
	}
	
	
	public function initBackend() 
    {
		
	}
	
	public static function getDefaultOption()
	{
		return array(
			'optimize_traffic_modules' => array()
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
	
	public function create_traffic_module_options($input_parameters = false)
	{
		$resultData = array(
			'module' => ''
			,'module_id' => ''
		);
		
		if(!$input_parameters) {
			$input_parameters = array();
		}
		
		$moduleId = '';
		if(isset($input_parameters['module_id']) && $input_parameters['module_id']) {
			$moduleId = $input_parameters['module_id'];
		}
		
		if(!$moduleId) {
			$moduleId = Hash::crc32b(Utils::randomHash());
		}
		
		$resultData['module_id'] = $moduleId;
		
		if('traffic_module_sample_id' === $moduleId) {
			$input_parameters['moduleOptionsData']['enable_thumbnails'] = 'on';
			$input_parameters['moduleOptionsData']['enable_items_title'] = 'on';
			$input_parameters['moduleOptionsData']['enable_items_excerpt'] = 'on';
			$input_parameters['moduleOptionsData']['title_of_module'] = 'Related articles :';
			
			$input_parameters['moduleOptionsData']['enable_open_links_in_new_windows'] = 'on';
			
		}
		
		if(!isset($input_parameters['moduleOptionsData']['maximum_number_characters_items_title'])) {
			$input_parameters['moduleOptionsData']['maximum_number_characters_items_title'] = '60';
		}
		
		if(!isset($input_parameters['moduleOptionsData']['maximum_number_characters_items_excerpt'])) {
			$input_parameters['moduleOptionsData']['maximum_number_characters_items_excerpt'] = '120';
		}
		
		$resultData['module'] .= '

		<div id="'.$moduleId.'" class="wppepvn_green_block optimize_traffic_module_container">
			
			<h5 class="optimize_traffic_module_container_head">Traffic Module - ID : <span>'.$moduleId.'</span> - <a href="#" class="optimize_traffic_module_button_remove">Remove Module</a> - <a href="#" style="font-size: 80%;" class="optimize_traffic_module_button_minimize_maximize">Minimize/Maximize</a></h5>
			
			<input type="hidden" name="optimize_traffic_modules['.$moduleId.'][module_id]" value="'.$moduleId.'" /> 
			
			<div class="optimize_traffic_module_container_body">
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					<h6>
						<span class="optimize_traffic_module_options_tilte">Module Type</span> : 
						<select name="optimize_traffic_modules['.$moduleId.'][module_type]" style="width: 200px;margin-left: 2%;">
							<option value="fixed" '.(
								(isset($input_parameters['moduleOptionsData']['module_type']) && ('fixed' === $input_parameters['moduleOptionsData']['module_type'])) ? ' selected="selected" ' : ''
							).' >Fixed</option>
							<option value="flyout" '.(
								(isset($input_parameters['moduleOptionsData']['module_type']) && ('flyout' === $input_parameters['moduleOptionsData']['module_type'])) ? ' selected="selected" ' : ''
							).' >Flyout</option>
						</select>
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>Fixed : '.__('Module will appear at fixed location as your choice in post\'s content',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
									<li>Flyout : '.__('Module will appear on the right or left of user\'s screen',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
					</h6>
					
					
				</div>
				
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">Module Style</span> : 
						<select name="optimize_traffic_modules['.$moduleId.'][module_style]" style="width: 200px;margin-left: 2%;">
							<option value="style_1"  '.(
								(isset($input_parameters['moduleOptionsData']['module_style']) && ('style_1' === $input_parameters['moduleOptionsData']['module_style'])) ? ' selected="selected" ' : ''
							).' >Style 1</option>
							<option value="style_2" '.(
								(isset($input_parameters['moduleOptionsData']['module_style']) && ('style_2' === $input_parameters['moduleOptionsData']['module_style'])) ? ' selected="selected" ' : ''
							).' >Style 2</option>
						</select>
					</h6>
					
				</div>
				
				<div class="optimize_traffic_module_options postbox wppepvn_hide" style="padding-top: 12px; padding-bottom: 12px;display:none;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">Display Animation Type</span> : 
						<select name="optimize_traffic_modules['.$moduleId.'][animation_type]" style="width: 200px;margin-left: 2%;">
							<option value="slideout">Slideout</option>
							<option value="fade">Fade</option>
						</select>
					</h6>
					
				</div>
				
				<div class="optimize_traffic_module_options postbox wppepvn_hide" style="padding-top: 12px; padding-bottom: 12px;">
					<h6>
						'.__('When should the Module appear?',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'
					</h6>
					
					<br />
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('When user scroll length of site\'s height',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'(px or %)</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][module_appear_when_user_scroll_length]" value="'.(
							isset($input_parameters['moduleOptionsData']['module_appear_when_user_scroll_length']) ? $input_parameters['moduleOptionsData']['module_appear_when_user_scroll_length'] : ''
						).'" type="text" style="width:300px;margin-left: 2%;" />
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>'.__('When the user scrolls to the location you set, the module will appear.',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
									<li>'.__('You can set up the "80%" or "80" (px). All values are based on the height of the site',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
					</h6>
					
					<br />
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('When user view for seconds',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][module_appear_when_user_read_for_seconds]" value="'.(
							isset($input_parameters['moduleOptionsData']['module_appear_when_user_read_for_seconds']) ? $input_parameters['moduleOptionsData']['module_appear_when_user_read_for_seconds'] : ''
						).'" type="text" style="width:300px;margin-left: 2%;" />
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>'.__('When the user access and view your website in number of seconds, the module will appear.',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
						
					</h6>
					
					
				</div>
				
				
				<div class="optimize_traffic_module_options postbox wppepvn_hide" style="padding-top: 12px; padding-bottom: 12px;">
													
					<h6>
						<span class="optimize_traffic_module_options_tilte">Margin bottom (px)</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][module_margin_bottom]" value="'.(
							isset($input_parameters['moduleOptionsData']['module_margin_bottom']) ? $input_parameters['moduleOptionsData']['module_margin_bottom'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
					</h6>
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">Margin left (px)</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][module_margin_left]" value="'.(
							isset($input_parameters['moduleOptionsData']['module_margin_left']) ? $input_parameters['moduleOptionsData']['module_margin_left'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
					</h6>
					
				</div>
				
';
				
				$resultData['module'] .= '
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Position of Module',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<select name="optimize_traffic_modules['.$moduleId.'][module_position]" style="width: 200px;margin-left: 2%;" pepvn_data_val="'.(
							isset($input_parameters['moduleOptionsData']['module_position']) ? $input_parameters['moduleOptionsData']['module_position'] : ''
						).'" >';
				
				$resultData['module'] .= '
						</select> 
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>'.__('When the position is %, module will appear in post\'s content corresponding to the value you set',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
									<li>'.__('When the position is Left/Right, module will appear on Left/Right side of user\'s screen',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
						
					</h6>
					
				</div>
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Title of Module',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][title_of_module]" value="'.(
							isset($input_parameters['moduleOptionsData']['title_of_module']) ? $input_parameters['moduleOptionsData']['title_of_module'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
					</h6>
					
				</div>
				
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">Custom class (CSS) of Module</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][custom_class_css_of_module]"  value="'.(
							isset($input_parameters['moduleOptionsData']['custom_class_css_of_module']) ? $input_parameters['moduleOptionsData']['custom_class_css_of_module'] : ''
						).'" type="text" style="width:300px;margin-left: 2%;" placeholder="Ex : your_custom_class_1 your_custom_class_2" />
						
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>'.__('This option will help you design module according to your wishes through CSS',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
					</h6>
					
					<h6 style="display:none;">
						<span class="optimize_traffic_module_options_tilte">Custom ID (CSS) of Module</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][custom_id_css_of_module]" type="text" style="width:300px;margin-left: 2%;" placeholder="Ex : your_custom_id" />
					</h6>
					
				</div>
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Maximum Number of Items',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<select name="optimize_traffic_modules['.$moduleId.'][module_mumber_of_items]" style="width: 200px;margin-left: 2%;">';
				for($iOne = 1; $iOne<11; $iOne++) {
					$resultData['module'] .= '
							<option value="'.$iOne.'" '.(
						(
							isset($input_parameters['moduleOptionsData']['module_mumber_of_items']) 
							&& ($iOne == $input_parameters['moduleOptionsData']['module_mumber_of_items'])
						) ? ' selected="selected" ' : ''
					).' >'.$iOne.'</option>';
				}
							
				$resultData['module'] .= '
						</select>
						
						<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
							base64_encode(
								'<ul>
									<li>'.__('Maximum number of items (posts) in this module',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
								</ul>'
							)
						).'"></span>
					</h6>
					
				</div>
				
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">Thumbnails</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][enable_thumbnails]" type="checkbox" style="margin-left: 2%;"  '.(
							isset($input_parameters['moduleOptionsData']['enable_thumbnails']) ? ' checked="checked" ' : ''
						).'  /> '.__('Enable Thumbnails',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'
					</h6>
					
					<div class="wppepvn_hide  wpoptimizebyxtraffic_enabled_thumbnails">
						
						<h6>
							<span class="optimize_traffic_module_options_tilte">'.__('Default Thumbnail Url (include http:// or https://)',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
							<input name="optimize_traffic_modules['.$moduleId.'][default_thumbnail_url]" value="'.(
							isset($input_parameters['moduleOptionsData']['default_thumbnail_url']) ? $input_parameters['moduleOptionsData']['default_thumbnail_url'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;"  />
						
							<span class="wppepvn_help_icon wppepvn_tooltip" title="" data_content="'.(
								base64_encode(
									'<ul>
										<li>'.__('When the item does not have thumbnail image, plugin will get this image to make an thumbnail',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</li>
									</ul>'
								)
							).'"></span>
							
						</h6>
						
						<h6>
							<span class="optimize_traffic_module_options_tilte">'.__('Thumbnail Width',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).' (px)</span> : 
							<input name="optimize_traffic_modules['.$moduleId.'][thumbnail_width]" value="'.(
							isset($input_parameters['moduleOptionsData']['thumbnail_width']) ? (int)$input_parameters['moduleOptionsData']['thumbnail_width'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
						</h6>
						
						<h6>
							<span class="optimize_traffic_module_options_tilte">'.__('Thumbnail Height',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).' (px)</span> : 
							<input name="optimize_traffic_modules['.$moduleId.'][thumbnail_height]" value="'.(
							isset($input_parameters['moduleOptionsData']['thumbnail_height']) ? (int)$input_parameters['moduleOptionsData']['thumbnail_height'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
						</h6>
						
					</div>
					
				</div>
				
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;"> 
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Maximum width of each item?',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).' (px)</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][maximum_width_each_item]" value="'.(
							isset($input_parameters['moduleOptionsData']['maximum_width_each_item']) ? (int)$input_parameters['moduleOptionsData']['maximum_width_each_item'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
					</h6>
					
				</div>
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Items\'s Title',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][enable_items_title]" type="checkbox" style="margin-left: 2%;" '.(
							isset($input_parameters['moduleOptionsData']['enable_items_title']) ? ' checked="checked" ' : ''
						).'  /> Enable Items\'s Title
					</h6>
					
					<div class="wppepvn_hide wpoptimizebyxtraffic_enabled_items_title">
						<h6>
							<span class="optimize_traffic_module_options_tilte">'.__('Maximum number of characters for items\'s title?',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
							<input name="optimize_traffic_modules['.$moduleId.'][maximum_number_characters_items_title]"  value="'.(
							isset($input_parameters['moduleOptionsData']['maximum_number_characters_items_title']) ? (int)$input_parameters['moduleOptionsData']['maximum_number_characters_items_title'] : ''
						).'" type="text" style="width:300px;margin-left: 2%;" />
						</h6>
					</div>
					
				</div>
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Items\'s Excerpt',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][enable_items_excerpt]" type="checkbox" style="margin-left: 2%;" '.(
							isset($input_parameters['moduleOptionsData']['enable_items_excerpt']) ? ' checked="checked" ' : ''
						).'  /> '.__('Enable Items\'s Excerpt',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'
					</h6>
					
					<div class="wppepvn_hide wpoptimizebyxtraffic_enabled_items_excerpt">
						<h6>
							<span class="optimize_traffic_module_options_tilte">'.__('Maximum number of characters for items\'s excerpt?',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
							<input name="optimize_traffic_modules['.$moduleId.'][maximum_number_characters_items_excerpt]"  value="'.(
							isset($input_parameters['moduleOptionsData']['maximum_number_characters_items_excerpt']) ? (int)$input_parameters['moduleOptionsData']['maximum_number_characters_items_excerpt'] : ''
						).'"  type="text" style="width:300px;margin-left: 2%;" />
						</h6>
					</div>
					
				</div>
				
				
				
				<div class="optimize_traffic_module_options postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Open Links In New Window',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</span> : 
						<input name="optimize_traffic_modules['.$moduleId.'][enable_open_links_in_new_windows]" type="checkbox" style="margin-left: 2%;" '.(
							isset($input_parameters['moduleOptionsData']['enable_open_links_in_new_windows']) ? ' checked="checked" ' : ''
						).'  /> '.__('Enable Open Links In New Window',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'
					</h6>
					
					
				</div> 
				
				
				
				<div class="optimize_traffic_module_options optimize_traffic_module_preview_container postbox" style="padding-top: 12px; padding-bottom: 12px;">
					
					<h6>
						<span class="optimize_traffic_module_options_tilte">'.__('Preview Module',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).' ( <a href="#" class="optimize_traffic_module_preview_button_show_me"><b>'.__('Show me',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG).'</b></a> )</span> : 
					</h6>
					
					<div class="optimize_traffic_module_preview postbox" style="padding: 12px;margin-top: 26px;">
						
					</div>
					
					
					
				</div>
			</div>
			
		</div>
		
		';
		
		
		return $resultData;
		
		
	}
	
	public function create_traffic_module($input_parameters)
	{
		$keyCache1 = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$input_parameters
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $resultData) {
			return $resultData;
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		global $wpdb;
		
		$resultData = array(
			'module' => ''
			,'module_id' => ''
		);
		
		if(isset($input_parameters['option']['module_id']) && $input_parameters['option']['module_id']) {
			
			$nsModule = 'wppepvn_module_traffic';
			
			$resultData['module_id'] = $input_parameters['option']['module_id'];
			
			$isModuleNoText = true;
			if(isset($input_parameters['option']['enable_items_title']) && $input_parameters['option']['enable_items_title']) {
				$isModuleNoText = false;
			}
			
			if(isset($input_parameters['option']['enable_items_excerpt']) && $input_parameters['option']['enable_items_excerpt']) {
				$isModuleNoText = false;
			}
			
			if($isModuleNoText) {
				$input_parameters['option']['module_style'] = 'style_2';
			}
			
			if(!isset($input_parameters['option']['title_of_module'])) {
				$input_parameters['option']['title_of_module'] = '';//Related article
			}
			
			if(!isset($input_parameters['option']['custom_class_css_of_module'])) {
				$input_parameters['option']['custom_class_css_of_module'] = '';
			}
			
			if(!isset($input_parameters['option']['custom_id_css_of_module'])) {
				$input_parameters['option']['custom_id_css_of_module'] = '';
			}
			
			if(!isset($input_parameters['option']['module_type'])) {
				$input_parameters['option']['module_type'] = 'fixed';
			}
			
			
			if(!isset($input_parameters['option']['module_mumber_of_items'])) {
				$input_parameters['option']['module_mumber_of_items'] = 1;
			}
			$input_parameters['option']['module_mumber_of_items'] = abs((int)$input_parameters['option']['module_mumber_of_items']);
			if($input_parameters['option']['module_mumber_of_items']>10) {
				$input_parameters['option']['module_mumber_of_items'] = 10;
			} else if($input_parameters['option']['module_mumber_of_items']<1) {
				$input_parameters['option']['module_mumber_of_items'] = 1;
			}
			
			
			if(!isset($input_parameters['option']['thumbnail_width'])) {
				$input_parameters['option']['thumbnail_width'] = 0;
			}
			$input_parameters['option']['thumbnail_width'] = abs((int)$input_parameters['option']['thumbnail_width']);
			
			if(!isset($input_parameters['option']['thumbnail_height'])) {
				$input_parameters['option']['thumbnail_height'] = 0;
			}
			$input_parameters['option']['thumbnail_height'] = abs((int)$input_parameters['option']['thumbnail_height']);
			
			
			if(!isset($input_parameters['option']['maximum_width_each_item'])) {
				$input_parameters['option']['maximum_width_each_item'] = 0;
			}
			$input_parameters['option']['maximum_width_each_item'] = abs((int)$input_parameters['option']['maximum_width_each_item']);
			
			
			
			if(isset($input_parameters['option']['maximum_number_characters_items_title']) && $input_parameters['option']['maximum_number_characters_items_title']) {
			} else {
				$input_parameters['option']['maximum_number_characters_items_title'] = 60;
			}
			$input_parameters['option']['maximum_number_characters_items_title'] = abs((int)$input_parameters['option']['maximum_number_characters_items_title']);
			
			
			
			if(isset($input_parameters['option']['maximum_number_characters_items_excerpt']) && $input_parameters['option']['maximum_number_characters_items_excerpt']) {
			} else {
				$input_parameters['option']['maximum_number_characters_items_excerpt'] = 120;
			}
			$input_parameters['option']['maximum_number_characters_items_excerpt'] = abs((int)$input_parameters['option']['maximum_number_characters_items_excerpt']);
			
			
			if(isset($input_parameters['option']['module_appear_when_user_scroll_length']) && $input_parameters['option']['module_appear_when_user_scroll_length']) {
			} else {
				$input_parameters['option']['module_appear_when_user_scroll_length'] = '80%';
			}
			
			
			if(isset($input_parameters['option']['module_appear_when_user_read_for_seconds']) && $input_parameters['option']['module_appear_when_user_read_for_seconds']) {
			} else {
				$input_parameters['option']['module_appear_when_user_read_for_seconds'] = 0;
			}
			$input_parameters['option']['module_appear_when_user_read_for_seconds'] = abs((int)$input_parameters['option']['module_appear_when_user_read_for_seconds']);
			
			
			
			if(isset($input_parameters['option']['module_margin_bottom']) && $input_parameters['option']['module_margin_bottom']) {
			} else {
				$input_parameters['option']['module_margin_bottom'] = 0;
			}
			$input_parameters['option']['module_margin_bottom'] = (int)$input_parameters['option']['module_margin_bottom'];
			
			
			if(isset($input_parameters['option']['module_margin_left']) && $input_parameters['option']['module_margin_left']) {
			} else {
				$input_parameters['option']['module_margin_left'] = 0;
			}
			$input_parameters['option']['module_margin_left'] = (int)$input_parameters['option']['module_margin_left'];
			
			if(isset($input_parameters['data']['posts_ids']) && (!PepVN_Data::isEmptyArray($input_parameters['data']['posts_ids']))) {
			} else {
				
				$input_parameters['data']['posts_ids'] = array();
			
				$queryString1 = '
SELECT ID
FROM `'.$wpdb->posts.'`
WHERE ( ( post_status = "publish") AND ( post_type = "post" ) )
ORDER BY RAND()
LIMIT 0,'.$input_parameters['option']['module_mumber_of_items'];
	
				$rsOne = $wpdb->get_results($queryString1);
				
				if($rsOne && !empty($rsOne)) {
					foreach($rsOne as $keyOne => $valueOne) {
						if($valueOne) {
							if(isset($valueOne->ID) && $valueOne->ID) {
								$input_parameters['data']['posts_ids'][] = $valueOne->ID;
							}
						}
					}
				}
			
			}
			
			$input_parameters['data']['posts_ids'] = (array)$input_parameters['data']['posts_ids'];
			$input_parameters['data']['posts_ids'] = array_unique($input_parameters['data']['posts_ids']);
			
			$moduleDataPlus = array();
			
			$moduleClassPlus = array();
			$moduleClassPlus[] = $nsModule.'_'.$input_parameters['option']['module_type'];
			$moduleClassPlus[] = $nsModule.'_'.$input_parameters['option']['module_style'];
			$moduleClassPlus[] = $input_parameters['option']['custom_class_css_of_module'];
			
			if(isset($input_parameters['option']['enable_thumbnails']) && $input_parameters['option']['enable_thumbnails']) {
				$moduleClassPlus[] = $nsModule.'_enable_thumbnails';
			}
			
			if('flyout' === $input_parameters['option']['module_type']) {
				if(isset($input_parameters['option']['module_position']) && $input_parameters['option']['module_position']) {
					$moduleClassPlus[] = $nsModule.'_side_'.$input_parameters['option']['module_position'];
				}
				
				$moduleClassPlus[] = 'wpoptxtr_shawy';
			}
			
			$moduleStylePlus = array();
			if('flyout' === $input_parameters['option']['module_type']) {
				if('style_1' === $input_parameters['option']['module_style']) {
					if($input_parameters['option']['maximum_width_each_item']>0) {
						$valueTemp = $input_parameters['option']['maximum_width_each_item'];
						$valueTemp = $valueTemp * 1.1;
						$valueTemp = (int)$valueTemp;
						$moduleStylePlus[] = 'width:'.$valueTemp.'px;max-width:'.$valueTemp.'px;';
					}
					
					
				}
			}
			
			if(0 != $input_parameters['option']['module_margin_bottom']) {
				$valueTemp = (int)$input_parameters['option']['module_margin_bottom'];
				$moduleStylePlus[] = 'margin-bottom:'.$valueTemp.'px;';
			}
			
			if(0 != $input_parameters['option']['module_margin_left']) {
				$valueTemp = (int)$input_parameters['option']['module_margin_left'];
				$moduleStylePlus[] = 'margin-left:'.$valueTemp.'px;';
			}
			
			$moduleDataPlus[] = 'pepvn_data_module_appear_when_user_read_for_seconds="'.$input_parameters['option']['module_appear_when_user_read_for_seconds'].'"';
			$moduleDataPlus[] = 'pepvn_data_module_appear_when_user_scroll_length="'.$input_parameters['option']['module_appear_when_user_scroll_length'].'"';
			$moduleDataPlus[] = 'pepvn_data_module_position="'.$input_parameters['option']['module_position'].'"';
			$moduleDataPlus[] = 'pepvn_data_module_id="'.$input_parameters['option']['module_id'].'"';
			
			$resultData['module'] .= '
	
			<div class="wppepvn_module_traffic '.implode(' ',$moduleClassPlus).'" id="'.$input_parameters['option']['module_id'].'" pepvn_data_options="'.Utils::encodeVar($input_parameters['option']).'" style="'.implode(';',$moduleStylePlus).'" '.implode(' ',$moduleDataPlus).' >';
			
			if('flyout' === $input_parameters['option']['module_type']) {
				$resultData['module'] .= '
				<span class="wppepvn_module_traffic_button_show_wrapper"></span>
				
				<span class="wppepvn_module_traffic_button_close"></span>
				';
			}
			
			$resultData['module'] .= '
				
				<span class="wppepvn_module_traffic_title"><strong>'.$input_parameters['option']['title_of_module'].'</strong></span>
				
				<ul>';
			
			foreach($input_parameters['data']['posts_ids'] as $keyOne => $valueOne) {
				
				if($valueOne) {
					$valueOne = (int)$valueOne;
					if($valueOne>0) {
						
						$rsGetPost1 = $wpExtend->getAndParsePostByPostId($valueOne);
						
						if($rsGetPost1) {
							$rsGetPost1 = (object)$rsGetPost1;
							if(isset($rsGetPost1->postPermalink) && $rsGetPost1->postPermalink) {
								
								$thumbnailUrl1 = '';
								
								if(isset($input_parameters['option']['enable_thumbnails']) && $input_parameters['option']['enable_thumbnails']) {
									
									if(isset($input_parameters['option']['default_thumbnail_url']) && $input_parameters['option']['default_thumbnail_url']) {
										$thumbnailUrl1 = $input_parameters['option']['default_thumbnail_url'];
									}
									
									if(isset($rsGetPost1->postThumbnailUrl) && $rsGetPost1->postThumbnailUrl) {
										$thumbnailUrl1 = $rsGetPost1->postThumbnailUrl;
									} else {
										if(isset($rsGetPost1->postImages) && $rsGetPost1->postImages && (!PepVN_Data::isEmptyArray($rsGetPost1->postImages))) {
											$postImages1 = $rsGetPost1->postImages;
											shuffle($postImages1);
											$thumbnailUrl1 = $postImages1[0]['src'];
										}
									}
									
									
								}
								
								$thumbnailUrl1 = trim($thumbnailUrl1);
								
								$itemClassPlus = array();
								if($thumbnailUrl1) {
									$itemClassPlus[] = $nsModule.'_item_has_thumbnail';
								}
								
								$resultData['module'] .= '
					<li class="wppepvn_module_traffic_item '.implode(' ',$itemClassPlus).'" style="';
								if($input_parameters['option']['maximum_width_each_item']>0) {
									$resultData['module'] .= 'width:'.$input_parameters['option']['maximum_width_each_item'].'px;max-width:'.$input_parameters['option']['maximum_width_each_item'].'px;';
								}
								
								$resultData['module'] .= '" >
					
						<a href="'.$rsGetPost1->postPermalink.'" class="wppepvn_module_traffic_item_anchor" '.(
							isset($input_parameters['option']['enable_open_links_in_new_windows']) ? ' target="_blank" ' : ''
						).' title="'.$rsGetPost1->post_title.'" >';
								
								if($thumbnailUrl1) {
									
									$styleImg1 = '';
									
									if($input_parameters['option']['thumbnail_width']>0) {
										$styleImg1 .= 'width:'.$input_parameters['option']['thumbnail_width'].'px;max-width:'.$input_parameters['option']['thumbnail_width'].'px;';
									}
									if($input_parameters['option']['thumbnail_height']>0) {
										$styleImg1 .= 'height:'.$input_parameters['option']['thumbnail_height'].'px;max-height:'.$input_parameters['option']['thumbnail_height'].'px;';
									}
									
									
								
									if('style_1' === $input_parameters['option']['module_style']) {
										
										$resultData['module'] .= '
							<img class="wppepvn_module_traffic_item_img" src="'.$thumbnailUrl1.'" style="'.$styleImg1.'" /> ';
											
									} else {
										
										$resultData['module'] .= '
						<span class="wppepvn_module_traffic_item_img" style="'.$styleImg1.'" >
							<img src="'.$thumbnailUrl1.'" style="'.$styleImg1.'" />
						</span>';
									}
										
										
									
								}
								
								$postTitle1 = $rsGetPost1->post_title;
								$postTitle1 = Text::removeShortcode($postTitle1);
								if($input_parameters['option']['maximum_number_characters_items_title']>0) {
									if(PepVN_Data::mb_strlen($postTitle1) > $input_parameters['option']['maximum_number_characters_items_title']) {
										$postTitle1 = PepVN_Data::mb_substr($postTitle1, 0, $input_parameters['option']['maximum_number_characters_items_title']).'...';
									}
								}
								
								
								$postExcerpt1 = $rsGetPost1->post_excerpt;
								if(!$postExcerpt1) {
									$postExcerpt1 = $rsGetPost1->postContentRawText;
									$postExcerpt1 = PepVN_Data::mb_substr($postExcerpt1, 0, 350).'...';
								}
								
								$postExcerpt1 = Text::removeShortcode($postExcerpt1);
								
								if($input_parameters['option']['maximum_number_characters_items_excerpt']>0) {
									if(PepVN_Data::mb_strlen($postExcerpt1) > $input_parameters['option']['maximum_number_characters_items_excerpt']) {
										$postExcerpt1 = PepVN_Data::mb_substr($postExcerpt1, 0, $input_parameters['option']['maximum_number_characters_items_excerpt']).'...';
									}
								}
								
								
								
								if('style_1' === $input_parameters['option']['module_style']) {
								
									if(isset($input_parameters['option']['enable_items_title']) && $input_parameters['option']['enable_items_title']) {
										$resultData['module'] .= '<strong class="wppepvn_module_traffic_item_title">'.$postTitle1.'</strong>';
									}
								
									if(isset($input_parameters['option']['enable_items_excerpt']) && $input_parameters['option']['enable_items_excerpt']) {
										if($postExcerpt1) {
											$resultData['module'] .= '<br /><span class="wppepvn_module_traffic_item_excerpt">'.$postExcerpt1.'</span>';
										}
									}
									
								} else {
									$resultData['module'] .= '
						<span class="wppepvn_module_traffic_item_text">';
									
									if(isset($input_parameters['option']['enable_items_title']) && $input_parameters['option']['enable_items_title']) {
										$resultData['module'] .= '
							<span class="wppepvn_module_traffic_item_title"><strong>'.$postTitle1.'</strong></span>';
									}
									
									
								
									if(isset($input_parameters['option']['enable_items_excerpt']) && $input_parameters['option']['enable_items_excerpt']) {
										if($postExcerpt1) {
											$resultData['module'] .= '
							<span class="wppepvn_module_traffic_item_excerpt">'.$postExcerpt1.'</span>';
									}
									
								}
								
									$resultData['module'] .= '
						</span>';
								}
						
						
						
						
						
								$resultData['module'] .= '
						</a>
					</li>';
								
							
							}
						}
						
					}
				}
			}
			
			$resultData['module'] .= '
				</ul>
			</div>';
			
		}
		
		TempDataAndCacheFile::set_cache($keyCache1, $resultData);
		
		return $resultData;
	}
	
	
	
	
	public function preview_optimize_traffic_modules($input_parameters)
	{
		$resultData = array();
		
		if(
			isset($input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules']) 
			&& $input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules']
		) {
			$input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules'] = (array)$input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules'];
			
			foreach($input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules'] as $key1 => $value1) {
				unset($input_parameters['preview_optimize_traffic_modules']['optimize_traffic_modules'][$key1]);
				
				$resultData = $this->create_traffic_module(array(
					'option' => $value1
				));
				
			}
			
		}
		
		return $resultData;
	}
	
	
	private function _clean_terms($input_terms)
	{
		$keyCache1 = Utils::hashKey(array(
			__METHOD__
			,$input_terms
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null !== $resultData) {
			return $resultData;
		}
		
		$input_terms = (array)$input_terms;
		
		$input_terms = implode(';',$input_terms);
		$input_terms = PepVN_Data::strtolower($input_terms);
		$input_terms = AnalyzeText::analysisKeyword_RemovePunctuations($input_terms, ';');
		$input_terms = Text::reduceSpace($input_terms);
		
		$input_terms = explode(';',$input_terms);
		$input_terms = PepVN_Data::cleanArray($input_terms);
		
		TempDataAndCacheFile::set_cache($keyCache1, $input_terms);
		
		return $input_terms;
		
	}
	
	private function _remove_escaped_string($input_text)
	{
		$input_text = preg_replace('#_[a-z0-9\_]+_#',' ',$input_text);
		
		return $input_text;
	}
	
	public function search_post_by_text($input_text, $input_options = false)
	{
		$classMethodKey = crc32(__CLASS__ . __METHOD__);
		
		if(!$input_options) {
			$input_options = array();
		}
		
		$input_options['limit'] = (int)$input_options['limit'];
		
		if(!isset($input_options['exclude_posts_ids'])) {
			$input_options['exclude_posts_ids'] = array(0);
		}
		
		$input_options['exclude_posts_ids'] = (array)$input_options['exclude_posts_ids'];
		if(
			isset(PepVN_Data::$cacheData[$classMethodKey]['posts_ids_added']) 
			&& PepVN_Data::$cacheData[$classMethodKey]['posts_ids_added']
			&& !PepVN_Data::isEmptyArray(PepVN_Data::$cacheData[$classMethodKey]['posts_ids_added'])
		) {
			$valueTemp = array_keys(PepVN_Data::$cacheData[$classMethodKey]['posts_ids_added']);
			$input_options['exclude_posts_ids'] = array_merge($input_options['exclude_posts_ids'], $valueTemp);
		}
		
		$input_options['exclude_posts_ids'] = array_unique($input_options['exclude_posts_ids']);
		arsort($input_options['exclude_posts_ids']);
		$input_options['exclude_posts_ids'] = array_values($input_options['exclude_posts_ids']);
		
		if(!isset($input_options['key_cache'])) {
			$input_options['key_cache'] = array();
		}
		$input_options['key_cache'] = (array)$input_options['key_cache'];
		
		if(!isset($input_options['post_id_less_than'])) {
			$input_options['post_id_less_than'] = 0;
		}
		
		$keyCacheProcessText = Utils::hashKey(array(
			$classMethodKey
			,$input_text
			,$input_options
			,'search_post_by_text'
		));
		
		$rsSearchPosts = TempDataAndCacheFile::get_cache($keyCacheProcessText,true,true); 
		
		if(null === $rsSearchPosts) {
			
			global $post;
			
			$currentPostId = 0;
			
			if(isset($post->ID) && $post->ID) {
				$currentPostId = (int)$post->ID;
			}
			
			$rsSearchPosts = array();
			
			$wpExtend = $this->di->getShared('wpExtend');
			$analyzeText = $this->di->getShared('analyzeText');
			
			$keyCacheGroupNameOfTagsAndCategories = array(
				$classMethodKey
				,'groupNameOfTagsAndCategories'
			);
			
			$keyCacheGroupNameOfTagsAndCategories = Utils::hashKey($keyCacheGroupNameOfTagsAndCategories);
			
			$groupNameOfTagsAndCategories = TempDataAndCacheFile::get_cache($keyCacheGroupNameOfTagsAndCategories,true,true); 
			
			if(null === $groupNameOfTagsAndCategories) {
				
				$groupNameOfTagsAndCategories = array();
				
				$valueTemp = $wpExtend->getAndParseCategories();
				$valueTemp = array_keys($valueTemp);
				$groupNameOfTagsAndCategories = array_merge($groupNameOfTagsAndCategories, $valueTemp);
				unset($valueTemp);
				
				$valueTemp = $wpExtend->getAndParseTags();
				$valueTemp = array_keys($valueTemp);
				$groupNameOfTagsAndCategories = array_merge($groupNameOfTagsAndCategories, $valueTemp);
				unset($valueTemp);
				
				$groupNameOfTagsAndCategories = array_values($groupNameOfTagsAndCategories);
				
				$groupNameOfTagsAndCategories = $this->_clean_terms($groupNameOfTagsAndCategories);
				
				TempDataAndCacheFile::set_cache($keyCacheGroupNameOfTagsAndCategories, $groupNameOfTagsAndCategories,true,true);
			}
			
			$rsGetKeywordsFromText = AnalyzeText::analysisKeyword_GetKeywordsFromText(array(
				'contents' => $input_text
				,'min_word' => 2
				,'max_word' => 6
				,'min_occur' => 2
				,'min_char_each_word' => 2
			));
			
			$groupKeywordsFromText = array();
			if(isset($rsGetKeywordsFromText['data']) && is_array($rsGetKeywordsFromText['data'])) {
				foreach($rsGetKeywordsFromText['data'] as $keyOne => $valueOne) {
					if($valueOne && is_array($valueOne)) {
						foreach($valueOne as $keyTwo => $valueTwo) {
							if($keyTwo) {
								$groupKeywordsFromText[$keyTwo] = (int)$valueTwo;
							}
						}
					}
				}
			}
			
			arsort($groupKeywordsFromText);
			
			$groupKeywordsFromText2 = $groupKeywordsFromText;
			$groupKeywordsFromText2 = array_slice($groupKeywordsFromText2,0,10);
			
			$groupKeywordsFromText3 = array();
			foreach($groupKeywordsFromText as $keyOne => $valueOne) {
				unset($groupKeywordsFromText[$keyOne]);
				if(in_array($keyOne, $groupNameOfTagsAndCategories)) {
					$groupKeywordsFromText3[$keyOne] = $valueOne;
				}
			}
			unset($groupNameOfTagsAndCategories);
			arsort($groupKeywordsFromText3);
			$groupKeywordsFromText3 = array_slice($groupKeywordsFromText3,0,10);
			
			$groupKeywordsFromText4 = array();
			
			foreach($groupKeywordsFromText2 as $keyOne => $valueOne) {
				unset($groupKeywordsFromText2[$keyOne]);
				if(!isset($groupKeywordsFromText4[$keyOne])) {
					$groupKeywordsFromText4[$keyOne] = 0;
				}
				$groupKeywordsFromText4[$keyOne] += (int)$valueOne;
			}
			
			foreach($groupKeywordsFromText3 as $keyOne => $valueOne) {
				unset($groupKeywordsFromText3[$keyOne]);
				if(!isset($groupKeywordsFromText4[$keyOne])) {
					$groupKeywordsFromText4[$keyOne] = 0;
				}
				$groupKeywordsFromText4[$keyOne] += (int)$valueOne * 2;
			}
			
			foreach($groupKeywordsFromText4 as $keyOne => $valueOne) {
				$valueOne = ceil($valueOne * (PepVN_Data::countWords($keyOne) * 2));
				preg_match_all('#(\p{Lu}){1,}(\p{Lu}|\p{Ll}){1,}#us',$keyOne,$matched1);
				if(isset($matched1[0]) && $matched1[0]) {
					$count1 = count($matched1[0]);
					$valueOne = $valueOne * ($count1 + 1) * 10;
				}
				unset($matched1);
				
				$groupKeywordsFromText4[$keyOne] = $valueOne;
			}
			
			arsort($groupKeywordsFromText4);
			
			$groupKeywordsFromText4 = array_slice($groupKeywordsFromText4,0,15);	//max 30 keywords
			
			$terms_taxonomy_ids = array();
			
			$typeOfPage = $wpExtend->getTypeOfPage();
			if(isset($typeOfPage['post'])) {
				if($currentPostId) {
					$terms_taxonomy_ids = array_merge($terms_taxonomy_ids, $wpExtend->getTermTaxonomyIdByTaxonomyAndPostId($currentPostId,'category'));
					$terms_taxonomy_ids = array_merge($terms_taxonomy_ids, $wpExtend->getTermTaxonomyIdByTaxonomyAndPostId($currentPostId,'post_tag'));
					$terms_taxonomy_ids = array_unique($terms_taxonomy_ids);
				}
			}
			
			$rsSearchPosts = $analyzeText->find_related_posts(array(
				'keywords' => $groupKeywordsFromText4
				,'limit' => ($input_options['limit'] * 3) //$input_options['limit']
				,'terms_taxonomy_ids' => $terms_taxonomy_ids
				//,'exclude_posts_ids' => $input_options['exclude_posts_ids']
				//,'post_id_less_than' => $input_options['post_id_less_than']
			)); 
			
			if($rsSearchPosts && !empty($rsSearchPosts)) {
				
				$exclude_posts_ids_flipped = array_flip($input_options['exclude_posts_ids']);
				
				$numberPostsAdded = 0;
				
				foreach($rsSearchPosts as $key1 => $value1) {
					
					$checkStatus1 = false;
					
					if(isset($value1['post_id']) && $value1['post_id']) {
						
						if(!isset($exclude_posts_ids_flipped[$value1['post_id']])) {
							
							if($input_options['post_id_less_than']>0) {
								if($value1['post_id'] < $input_options['post_id_less_than']) {
									$checkStatus1 = true;
								}
							} else {
								$checkStatus1 = true;
							}
						}
						
					}
					
					if(!$checkStatus1) {
						unset($rsSearchPosts[$key1]);
					} else {
						$numberPostsAdded++;
						if($input_options['limit']>0) {
							if($numberPostsAdded > $input_options['limit']) {
								unset($rsSearchPosts[$key1]);
							}
						}
					}
				}
			}
			
			TempDataAndCacheFile::set_cache($keyCacheProcessText, $rsSearchPosts,true,true);
		}
		
		if($rsSearchPosts && !empty($rsSearchPosts)) {
			foreach($rsSearchPosts as $key1 => $value1) {
				PepVN_Data::$cacheData[$classMethodKey]['posts_ids_added'][$value1['post_id']] = $value1;
			}
		}
		
		return $rsSearchPosts;
	}
	
	
	public function process_text($input_text, $postId = 0)
	{
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$classMethodKey = crc32(__CLASS__ . __METHOD__);
		
		global $wpdb;
		
		$postId = (int)$postId;
		
		if($postId) {
			$post = $wpExtend->get_post($post_id);
		} else {
			global $post;
			if(isset($post->ID) && $post->ID) {
				$postId = (int)$post->ID;
			}
		}
		
		if($postId < 1) {
			return $input_text;
		}
		
		$options = self::getOption();
		
		if(isset($options['optimize_traffic_modules']) && !PepVN_Data::isEmptyArray($options['optimize_traffic_modules'])) {
		} else {
			return $input_text;
		}
		
		$keyCacheProcessText = Utils::hashKey(array(
			$classMethodKey
			,'process_text'
			,$options['optimize_traffic_modules']
			,$input_text
		));
		
		$tmp = TempDataAndCacheFile::get_cache($keyCacheProcessText,false,true); 
		
		if(null !== $tmp) {
			return $tmp; 
		}
		
		$patternsEscaped1 = array();
		
		$rsOne = PepVN_Data::escapeByPattern($input_text,array(
			'pattern' => '#<([a-z]+)[^><]+class=(\'|\")[^><\'\"]*?wp\-caption[^><\'\"]*?\2[^><]*?>.*?</\1>#is'
			,'target_patterns' => array(
				0
			)
			,'wrap_target_patterns' => ''
		));
		
		$input_text = $rsOne['content'];
		
		if(!empty($rsOne['patterns'])) {
			$patternsEscaped1 = array_merge($patternsEscaped1,$rsOne['patterns']);
		}
		unset($rsOne);
		
		$rsOne = PepVN_Data::escapeHtmlTagsAndContents($input_text,'a;table;pre;ol;ul;blockquote');
		$input_text = $rsOne['content'];
		if(!empty($rsOne['patterns'])) {
			$patternsEscaped1 = array_merge($patternsEscaped1, $rsOne['patterns']);
		}
		unset($rsOne);
		
		$original_InputText1 = $input_text;
		
		$post->ID = (int)$post->ID;
		
		$rsGetTerms = $wpExtend->getTermsByPostId($post->ID);
		
		$rsGetTerms2 = $rsGetTerms;
		$rsGetTerms2 = array_keys($rsGetTerms2);
		$rsGetTerms2 = $this->_clean_terms($rsGetTerms2);
		
		$parsePostData = $wpExtend->parsePostData($post);
		$postExcerpt1 = $parsePostData['post_excerpt'];
		
		//$allPostTextCombined = $post->post_title.' ' . PHP_EOL . ' ' . $post->post_excerpt . ' ' . PHP_EOL . implode(' ',$rsGetTerms2) . ' ' . PHP_EOL . ' ' . $post->post_content;
		$allPostTextCombined = $post->post_title.' ' . PHP_EOL . ' ' . $post->post_excerpt . ' ' . PHP_EOL . implode(' ',$rsGetTerms2) . ' ' . PHP_EOL . ' ' . $postExcerpt1;
		//$allPostTextCombined = $post->post_title.' ' . PHP_EOL . implode(' ',$rsGetTerms2);
		$allPostTextCombined = $this->_remove_escaped_string($allPostTextCombined);
		
		$patternsModulesReplaceText = array();
		
		$groupModules_ByModuleType = array();
		$groupModules_PositionsAddedToQueue = array();
		$groupModules_ByFixedTypeBeginOrEnd = array();
		
		foreach($options['optimize_traffic_modules'] as $keyOne => $valueOne) {
			if(isset($valueOne['module_type']) && $valueOne['module_type']) {
				if(isset($valueOne['module_position'])) {
					
					if(!in_array($valueOne['module_position'], $groupModules_PositionsAddedToQueue)) {
						
						$valueOne['module_mumber_of_items'] = abs((int)$valueOne['module_mumber_of_items']);
						if($valueOne['module_mumber_of_items']<1) {
							$valueOne['module_mumber_of_items'] = 1;
						} else if($valueOne['module_mumber_of_items']>10) {
							$valueOne['module_mumber_of_items'] = 10;
						}
						
						$groupModules_PositionsAddedToQueue[] = $valueOne['module_position'];
						$groupModules_ByModuleType[$valueOne['module_type']][$valueOne['module_position']] = $valueOne;
						
					}
				}
			}
		}
		
		
		$numberElementContentInText = 0;
		
		preg_match_all('/<(p|h1|h2|h3|h4|h5|h6)(\s+[^><]*?)?>.*?<\/\1>/is',$original_InputText1,$matchedElementContentInText);
		
		if(isset($matchedElementContentInText[0]) && $matchedElementContentInText[0]) {
			if(!empty($matchedElementContentInText[0])) {
				
				foreach($matchedElementContentInText[0] as $key1 => $value1) {
					$valueTemp1 = $value1;
					$valueTemp1 = AnalyzeText::cleanRawTextForProcessSearch($valueTemp1);
					
					$checkStatus2 = false;
					if($valueTemp1) {
						$valueTemp2 = explode(' ',$valueTemp1);
						if(count($valueTemp2)>5) {
							$checkStatus2 = true;
						} else {
							if(preg_match('#<(h1|h2|h3|h4|h5|h6)(\s+[^><]*?)?>.*?</\1>#is',$value1,$matched1)) {
								$checkStatus2 = true;
							}
						}
					}
					
					if(!$checkStatus2) {
						unset($matchedElementContentInText[0][$key1]);
					}
				}
			}
		
			$numberElementContentInText = count($matchedElementContentInText[0]);
			
		}
		
		
		if(
			isset($groupModules_ByModuleType['flyout']) 
			&& ($groupModules_ByModuleType['flyout'])
			&& (!empty($groupModules_ByModuleType['flyout']))
		) {
			foreach($groupModules_ByModuleType['flyout'] as $keyOne => $valueOne) {
				
				if(isset($valueOne['module_type'])) {
					
					$postsIdsFound1 = array();
					
					$rsSearchPost1 = $this->search_post_by_text($allPostTextCombined, array(
						'group_text_weight' => array(
							array(
								'text' => $post->post_title
								,'weight' => 16
							)
							
							,array(
								'text' => $post->post_excerpt
								,'weight' => 6
							)
							
							,array(
								'text' => implode(' ',$rsGetTerms2)
								,'weight' => 8
							)
							
							,array(
								'text' => $postExcerpt1
								,'weight' => 2
							)
							
						)
						,'exclude_posts_ids' => array($post->ID)
						,'post_id_less_than' => $post->ID
						,'limit' => $valueOne['module_mumber_of_items']
						,'key_cache' => $valueOne['module_type'].'_'.$valueOne['module_position']
					));
					
					if($rsSearchPost1) {
						
						foreach($rsSearchPost1 as $keyTwo => $valueTwo) {
							$postsIdsFound1[] = $valueTwo['post_id'];
						}
					}
					
					
					if($postsIdsFound1 && !empty($postsIdsFound1)) {
						$rsCreateTrafficModule1 = $this->create_traffic_module(array(
							'option' => $valueOne
							,'data' => array(
								'posts_ids' => $postsIdsFound1
							)
						));
						
						if($rsCreateTrafficModule1['module']) {
							$input_text .= ' '.$rsCreateTrafficModule1['module'];
						}
						
					}
					
					
					
				}
			}
		}
		
		
		if(isset($groupModules_ByModuleType['fixed']) && !PepVN_Data::isEmptyArray($groupModules_ByModuleType['fixed'])) {
			
			foreach($groupModules_ByModuleType['fixed'] as $keyOne => $valueOne) {
				if(isset($valueOne['module_position'])) {
					$valueOne['module_position'] = (int)$valueOne['module_position'];
					if(
						(0 == $valueOne['module_position'])
						|| (100 == $valueOne['module_position'])
					) {
						$groupModules_ByFixedTypeBeginOrEnd[$valueOne['module_position']] = $valueOne;
						unset($groupModules_ByModuleType['fixed'][$keyOne]);
					}
				}
			}
			
			
			if($groupModules_ByFixedTypeBeginOrEnd && !empty($groupModules_ByFixedTypeBeginOrEnd)) {
				
				ksort($groupModules_ByFixedTypeBeginOrEnd);
				
				foreach($groupModules_ByFixedTypeBeginOrEnd as $keyOne => $valueOne) {
					
					if(isset($valueOne['module_position'])) {
						$valueOne['module_position'] = (int)$valueOne['module_position'];
						
						$postsIdsFound1 = array();
						
						$rsSearchPost1 = $this->search_post_by_text($allPostTextCombined, array(
							'group_text_weight' => array(
								array(
									'text' => $post->post_title
									,'weight' => 16
								)
								,array(
									'text' => $post->post_excerpt
									,'weight' => 6
								)
								,array(
									'text' => implode(' ',$rsGetTerms2)
									,'weight' => 8
								)
								,array(
									'text' => $postExcerpt1
									,'weight' => 2
								)
							)
							,'exclude_posts_ids' => array($post->ID)
							//,'post_id_less_than' => $post->ID
							,'limit' => $valueOne['module_mumber_of_items']
							,'key_cache' => $valueOne['module_type'].'_'.$valueOne['module_position']
						));
						
						if($rsSearchPost1) {
							
							foreach($rsSearchPost1 as $keyTwo => $valueTwo) {
								$postsIdsFound1[] = $valueTwo['post_id'];
							}
						}
						
						if(!empty($postsIdsFound1)) {
							$rsCreateTrafficModule1 = $this->create_traffic_module(array(
								'option' => $valueOne
								,'data' => array(
									'posts_ids' => $postsIdsFound1
								)
							));
							
							if($rsCreateTrafficModule1['module']) {
								
								if(0 == $valueOne['module_position']) {
									$input_text = $rsCreateTrafficModule1['module'].' '.$input_text;
								} else if(100 == $valueOne['module_position']) {
									$input_text .= ' '.$rsCreateTrafficModule1['module'];
								}
							}
						}
					}
				}
			}
			
			
			if($numberElementContentInText>0) {
				
				ksort($groupModules_ByModuleType['fixed']);
				
				$arrayMatchedElementContentInTextIsProcessed = array();
			
				foreach($groupModules_ByModuleType['fixed'] as $keyOne => $valueOne) {
					if(isset($valueOne['module_position'])) {
						$valueOne['module_position'] = (int)$valueOne['module_position'];
						
						$originalTextNeedProcess1 = '';
						$rawTextNeedProcess1 = '';
						$iNumber1 = 0;
						foreach($matchedElementContentInText[0] as $keyTwo => $valueTwo) {
							
							if(!in_array($valueTwo,$arrayMatchedElementContentInTextIsProcessed)) {
								$originalTextNeedProcess1 .= ' '.$valueTwo;
							}
							
							$iNumber1++;
							$currentPercentPos = ($iNumber1 / $numberElementContentInText) * 100;
							$currentPercentPos = (int)$currentPercentPos;
							
							if(($currentPercentPos >= $valueOne['module_position']) && $originalTextNeedProcess1) {
								if(preg_match('#<(p)(\s+[^><]*?)?>.*?</\1>#is',$valueTwo,$matched2)) {
									
									$originalTextNeedProcess1 = $this->_remove_escaped_string($originalTextNeedProcess1);
									
									$postsIdsFound1 = array();
									
									$rsSearchPost1 = $this->search_post_by_text($originalTextNeedProcess1, array(
										'group_text_weight' => array(
											array(
												'text' => $post->post_title
												,'weight' => 16
											)
											
											,array(
												'text' => $post->post_excerpt
												,'weight' => 6
											)
											
											,array(
												'text' => implode(' ',$rsGetTerms2)
												,'weight' => 8
											)
											
											,array(
												'text' => $originalTextNeedProcess1
												,'weight' => 2
											)
										)
										,'exclude_posts_ids' => array($post->ID)
										//,'post_id_less_than' => $post->ID
										,'limit' => $valueOne['module_mumber_of_items']
										,'key_cache' => $valueOne['module_type'].'_'.$valueOne['module_position']
									));
									
									if($rsSearchPost1) {
										
										foreach($rsSearchPost1 as $keyThree => $valueThree) {
											$postsIdsFound1[] = $valueThree['post_id'];
										}
									}
									
									if($postsIdsFound1 && !empty($postsIdsFound1)) {
										$rsCreateTrafficModule1 = $this->create_traffic_module(array(
											'option' => $valueOne
											,'data' => array(
												'posts_ids' => $postsIdsFound1
											)
										));
										
										if($rsCreateTrafficModule1['module']) {
											$originalTextNeedProcess1 = '';
											$patternsModulesReplaceText_K = $valueTwo;
											$patternsModulesReplaceText_V = $valueTwo.' '.$rsCreateTrafficModule1['module'];
											$patternsModulesReplaceText[$patternsModulesReplaceText_K] = $patternsModulesReplaceText_V;
											break;
										}
										
									}
									
								}
							
							}
							
						}
						
					}
				}
				
				$arrayMatchedElementContentInTextIsProcessed = 0;
				
			}
			
		}
		
		if(!empty($patternsModulesReplaceText)) {
			
			$tmp = array();
			
			foreach($patternsModulesReplaceText as $key1 => $value1) {
				unset($patternsModulesReplaceText[$key1]);
				$key1 = '#'.Utils::preg_quote($key1).'#';
				$tmp[$key1] = $value1;
			}
			
			if(!empty($tmp)) {
				$input_text = preg_replace(array_keys($tmp), array_values($tmp), $input_text, 1);
			}
			unset($tmp);
		}
		
		unset($patternsModulesReplaceText);
		
		if(!empty($patternsEscaped1)) {
			$input_text = str_replace(array_values($patternsEscaped1),array_keys($patternsEscaped1),$input_text);
			
		}
		unset($patternsEscaped1);
		
		TempDataAndCacheFile::set_cache($keyCacheProcessText, $input_text, false, true);
		
		return $input_text;
	}
	
	
}


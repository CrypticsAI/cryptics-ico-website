<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WpPepVN\Utils
	,WpPepVN\DependencyInjection
;

class HeaderFooter
{
	const OPTION_NAME = 'header_footer';
	
	protected static $_tempData = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
	}
    
	public function initFrontend() 
    {
        
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		add_filter('the_content',  array($this, 'add_filter_the_content'), $priorityLast);
		
		add_action('wp_head',  array($this, 'add_action_wp_head'), $priorityLast);
		add_action('login_head',  array($this, 'add_action_wp_head'), $priorityLast);
		
		add_action('wp_footer',  array($this, 'add_action_wp_footer'), $priorityLast);
		
//		add_filter('wp_footer',  array(&$this, 'header_footer_the_footer_filter'), $priorityLast);
		//add_filter('the_content',  array(&$this, 'header_footer_the_content_filter'), $priorityLast);
		
		//add_action('login_head', array(&$this, 'header_footer_the_head_filter'), $priorityLast);
		
	}
	
	
	public function initBackend() 
    {
		
		add_action('admin_head',  array($this, 'add_action_admin_head'), WP_PEPVN_PRIORITY_LAST);
		
	}
	
	public static function getDefaultOption()
	{
		return array(
			'code_add_head_all' => ''
			,'code_add_head_home' => ''
			,'code_add_footer_all' => ''
			,'code_add_footer_home' => ''
			,'code_add_before_articles_all' => ''
			,'code_add_after_articles_all' => ''
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
	
	public function add_filter_the_content($text) 
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		if (!$wpExtend->is_singular() || $wpExtend->is_home() || $wpExtend->is_front_page()) {
			return $text;
		}
	
		$options = HeaderFooter::getOption();
		
		$text = $options['code_add_before_articles_all'] . $text . $options['code_add_after_articles_all'];
		
		return $text;
	}
	
	public function add_action_wp_head() 
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$typeOfCurrentPage = $wpExtend->getTypeOfPage();
		
		$options = HeaderFooter::getOption();
		
		echo '<script language="javascript" type="text/javascript" xtraffic-exclude>
var wppepvn_site_url = "',$wpExtend->site_url(),'/";
var wppepvn_admin_ajax_url = "',$wpExtend->admin_url('admin-ajax.php'),'";
var wppepvn_cronjob_url = wppepvn_admin_ajax_url + "?action=wppepvn_cronjob";
var wp_optimize_by_xtraffic_plugin_root_uri = "',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI,'";
</script>';
		
		if(
			isset($typeOfCurrentPage['front_page'])
			|| isset($typeOfCurrentPage['home'])
		) {
			echo $options['code_add_head_home'];
		}
		
		echo $options['code_add_head_all'];
	}
	
	public function add_action_wp_footer() 
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$typeOfCurrentPage = $wpExtend->getTypeOfPage();
		
		$options = HeaderFooter::getOption();
		
		if(
			isset($typeOfCurrentPage['front_page'])
			|| isset($typeOfCurrentPage['home'])
		) {
			echo $options['code_add_footer_home'];
		}
		
		echo $options['code_add_footer_all'];
		
	}
	
	
	public function add_action_admin_head() 
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		echo '<script language="javascript" type="text/javascript" xtraffic-exclude>
var wppepvn_site_url = "',$wpExtend->site_url(),'/";
var wppepvn_admin_ajax_url = "',$wpExtend->admin_url('admin-ajax.php'),'";
var wppepvn_cronjob_url = wppepvn_admin_ajax_url + "?action=wppepvn_cronjob";
var wp_optimize_by_xtraffic_plugin_root_uri = "',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI,'";
</script>';
		
	}
	
	public function migrateOptions() 
	{
		
		$newOptions = array();
		
		$oldOptionID = 'WPOptimizeByxTraffic';
		$oldOptions = get_option($oldOptionID);
		
		$keyFromOldToNew = array(
			'header_footer_code_add_head_home' => 'code_add_head_home'
			,'header_footer_code_add_head_all' => 'code_add_head_all'
			,'header_footer_code_add_footer_home' => 'code_add_footer_home'
			,'header_footer_code_add_footer_all' => 'code_add_footer_all'
			,'header_footer_code_add_before_articles_all' => 'code_add_before_articles_all'
			,'header_footer_code_add_after_articles_all' => 'code_add_after_articles_all'
		);
		
		if($oldOptions && is_array($oldOptions) && !empty($oldOptions)) {
			
			foreach($keyFromOldToNew as $oldKey => $newKey) {
				if(isset($oldOptions[$oldKey])) {
					$newOptions[$newKey] = base64_decode($oldOptions[$oldKey]);
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
	
}

<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Service;

use WpPepVN\DependencyInjection\Injectable
	, WpPepVN\Text
	, WpPepVN\DependencyInjection
;

class AdminPage extends Injectable
{
	
	public $di = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		if(isset($_GET['page']) && $_GET['page']) {
			$slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_';
			if(0 === strpos($_GET['page'],$slug)) {
				$wpRegisterStyleScript = $this->di->getShared('wpRegisterStyleScript');
				add_action( 'admin_enqueue_scripts', array($wpRegisterStyleScript, 'admin_enqueue_scripts'));
			}
		}
	}
	
	public function handle() 
    {
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		
		$moduleBackend = $this->di->get('moduleBackend');
		$hook = $this->di->getShared('hook');
		
		$register_admin_page = array();
		
		if($hook->has_filter('register_admin_page')) {
			$register_admin_page = $hook->apply_filters('register_admin_page', $register_admin_page);
		}
		
		$register_admin_page = (array)$register_admin_page;
		
		$page = '';
		
		if(isset($_GET['page']) && $_GET['page']) {
			$page = $_GET['page'];
			$page = str_replace(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_','',$page);
			$page = trim($page);
		}
		
		if($page) {
			
			$page = Text::camelize($page);
			
			if(isset($register_admin_page[$page])) {
				echo $moduleBackend->router_handle($page,$register_admin_page[$page])->getContent();
			} else {
				echo $moduleBackend->router_handle($page)->getContent();
			}
		}
		
		unset($register_admin_page);
		
	}
	
	public static function isAdminPageHandle() 
    {
		$status = false;
		
		if(isset($_GET['page']) && $_GET['page']) {
			if(0 === strpos($_GET['page'],WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_')) {
				$status = true;
			}
		}
		
		return $status;
	}

}
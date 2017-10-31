<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Module\Backend\Service\AdminPage
	, WpPepVN\DependencyInjection
;

class Module extends \WpPepVN\Mvc\Module
{
    const MODULE_DIR = __DIR__;
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function init(DependencyInjection $di) 
    {
        parent::init($di);
		
		$namespace = __NAMESPACE__;
        
        $config = include_once(self::MODULE_DIR . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'config.php');
		
		$config['application']['viewsBasePath'] = self::MODULE_DIR . DIRECTORY_SEPARATOR;
		$config['application']['controllerDir'] = self::MODULE_DIR . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR;
        
        include_once(self::MODULE_DIR . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'service.php');
        
		$this->di = $di;
		
		$this->init_admin_menu();
		
		$adminNotice = $this->di->getShared('adminNotice');
		
		$this->di->set('moduleBackend', $this, true);
		
		$adminNotice->init_ajax_backend();
		
		$priorityFirst = WP_PEPVN_PRIORITY_FIRST;
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		$keyTriggerClearCache = WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY;
		if(isset($_GET[$keyTriggerClearCache]) && $_GET[$keyTriggerClearCache]) {
			$cacheManager = $this->di->getShared('cacheManager');
			$cacheManager->clean_cache(',all,');
		}
		
		$headerFooter = $this->di->getShared('headerFooter');
		$headerFooter->initBackend();
		
		if(isset($_GET['findAndOptimizeLossyImageFilesStatus'])) {
			$optimizeImages = $this->di->getShared('optimizeImages');
			$optimizeImages->findAndOptimizeLossyImageFiles(WP_PEPVN_SITE_UPLOADS_DIR);
		}
	}
	
	
	public function init_admin_menu() 
    {
		$adminMenu = $this->di->getShared('adminMenu');
		
		add_action('admin_bar_menu', array($adminMenu, 'init_admin_bar_menu'), 80); //best position is 80
		add_action('admin_menu', array($adminMenu, 'init_admin_menu'), WP_PEPVN_PRIORITY_FIRST);
	}
	
	
}
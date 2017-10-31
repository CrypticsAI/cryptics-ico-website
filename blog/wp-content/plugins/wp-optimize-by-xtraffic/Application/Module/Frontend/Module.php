<?php 
namespace WPOptimizeByxTraffic\Application\Module\Frontend;

use WPOptimizeByxTraffic\Application\Service\OutputBuffer
	, WPOptimizeByxTraffic\Application\Service\HeaderFooter
	, WPOptimizeByxTraffic\Application\Service\PepVN_Data
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
        
        $config = include_once(self::MODULE_DIR . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'config.php');
		
		$config['application']['viewsBasePath'] = self::MODULE_DIR . DIRECTORY_SEPARATOR;
		$config['application']['controllerDir'] = self::MODULE_DIR . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR;
        
        include_once(self::MODULE_DIR . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'service.php');
        
		$di->set('config', function() use ($config) {
			return $config;
		}, true);
		
		$router = $di->getShared('router');
		$router->setControllerDir($config['application']['controllerDir']);
		$router->setNamespace('\\'.__NAMESPACE__.'\\Controller');
		$di->set('router', $router, true);
		
		$this->di = $di;
		
		add_action( 'wp', array($this, 'add_action_wp'), WP_PEPVN_PRIORITY_LAST );
	}
	
	public function add_action_wp()
	{
		
		$wpRegisterStyleScript = $this->di->getShared('wpRegisterStyleScript');
		$wpRegisterStyleScript->frontend_enqueue_scripts();
		unset($wpRegisterStyleScript);
		
		$outputBuffer = $this->di->getShared('outputBuffer');
		$outputBuffer->initFrontend();
		unset($outputBuffer);
		
		$headerFooter = $this->di->getShared('headerFooter');
		$headerFooter->initFrontend();
		unset($headerFooter);
		
		$optimizeLinks = $this->di->getShared('optimizeLinks');
		$optimizeLinks->initFrontend();
		unset($optimizeLinks);
		
		$optimizeTraffic = $this->di->getShared('optimizeTraffic');
		$optimizeTraffic->initFrontend();
		unset($optimizeTraffic);
		
		$optimizeImages = $this->di->getShared('optimizeImages');
		$optimizeImages->initFrontend();
		unset($optimizeImages);
		
		$hook = $this->di->getShared('hook');
		$hook->add_filter('output_buffer_before_return', array($this, 'addPromotionTextToHtml'), (WP_PEPVN_PRIORITY_LAST - 1));
		
	}
	
	public function addPromotionTextToHtml($buffer)
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$tmp = $wpExtend->getWpOptimizeByxTrafficPluginPromotionInfo();
		
		$buffer = preg_replace('#<!--[^>]+'.preg_quote($tmp['data']['plugin_wp_url'],'#').'[^>]+-->#is','',$buffer);
		
		$buffer = PepVN_Data::appendTextToTagBodyOfHtml($tmp['html_comment_text'],$buffer);
		
		return $buffer;
	}
	
}
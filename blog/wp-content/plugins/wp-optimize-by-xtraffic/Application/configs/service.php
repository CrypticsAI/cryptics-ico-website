<?php 
use WpPepVN\Cache\Frontend\Data as CacheFrontendData
    ,WpPepVN\Cache\Backend\Apc as CacheBackendApc
    ,WpPepVN\Cache\Backend\File as CacheBackendFile
	,WpPepVN\Cache\Frontend\None as CacheFrontendNone
	,WpPepVN\Cache\Backend\Memory as CacheBackendMemory
	,WpPepVN\Cache\Frontend\Output as CacheFrontendOutput
	,WpPepVN\Cache\Frontend\Igbinary as CacheFrontendIgbinary
	,WpPepVN\Remote as WpPepVN_Remote
	,WpPepVN\System
	,WpPepVN\Session\Adapter\Files as WpPepVNSession
	,WpPepVN\DependencyInjection
	
	,WPOptimizeByxTraffic\Application\Service\OutputBuffer as ServiceOutputBuffer
	,WPOptimizeByxTraffic\Application\Service\HeaderFooter as ServiceHeaderFooter
	,WPOptimizeByxTraffic\Application\Service\OptimizeLinks as ServiceOptimizeLinks
	,WPOptimizeByxTraffic\Application\Service\OptimizeTraffic as ServiceOptimizeTraffic
	,WPOptimizeByxTraffic\Application\Service\OptimizeImages as ServiceOptimizeImages
	,WPOptimizeByxTraffic\Application\Service\AnalyzeText as ServiceAnalyzeText
	,WPOptimizeByxTraffic\Application\Service\Device as ServiceDevice
	,WPOptimizeByxTraffic\Application\Service\PluginManager as ServicePluginManager
	,WPOptimizeByxTraffic\Application\Service\AjaxHandle as ServiceAjaxHandle
	,WPOptimizeByxTraffic\Application\Service\StaticVar as ServiceStaticVar
	,WPOptimizeByxTraffic\Application\Service\Dashboard as ServiceDashboard
	,WPOptimizeByxTraffic\Application\Service\Queue as ServiceQueue
	,WPOptimizeByxTraffic\Application\Service\BackgroundQueueJobsManager as ServiceBackgroundQueueJobsManager
	,WPOptimizeByxTraffic\Application\Service\TemplateReplaceVars as ServiceTemplateReplaceVars
	,WPOptimizeByxTraffic\Application\Service\Language as ServiceLanguage
	,WPOptimizeByxTraffic\Application\Service\Cronjob as ServiceCronjob
	,WPOptimizeByxTraffic\Application\Service\WordnetApi as ServiceWordnetApi
	,WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	,WPOptimizeByxTraffic\Application\Service\WpActionManager
;


$session = new WpPepVNSession(array(
	'uniqueId' => WP_PEPVN_NS_SHORT
));
$session->start();
$di->set('session', $session, true);

/*
* This store only general and simple data. Don't store large data in this staticVar object
*/
$serviceStaticVar = new ServiceStaticVar(md5('WPOptimizeByxTraffic_ServiceStaticVar_General'), array());
$di->set('staticVar',$serviceStaticVar,true);

$wpExtend = new \WPOptimizeByxTraffic\Application\Service\WpExtend($di);
$di->set('wpExtend', $wpExtend, true);

if($wpExtend->is_admin()) {
	$di->set('adminNotice', function() use ($di) {
		$adminNotice = new \WPOptimizeByxTraffic\Application\Module\Backend\Service\AdminNotice();
		return $adminNotice;
	}, true);
}

$di->set('notice', function() {
    return new \WPOptimizeByxTraffic\Application\Service\Notice();
}, true);

$cacheManager = new \WPOptimizeByxTraffic\Application\Service\CacheManager($di);
$di->set('cacheManager', $cacheManager, true);

$di->set('crypt', function() use ($di) {
	$crypt = new \WpPepVN\Crypt($di);
	
	$keyCrypt = hash('crc32b', md5(sha1(WP_PEPVN_SITE_SALT).'_crypt'));
	
	$keyCrypt .= hash('crc32b', md5($keyCrypt));
	
	$crypt->setKey();
	$crypt->setCipher('rijndael-256');
	$crypt->setMode('cbc');
	$crypt->setPadding(0);
	
	return $crypt;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config,$di) {

    $view = new \WpPepVN\Mvc\View();
	
    $view->registerEngines(array(
		'.php' => '\\WpPepVN\\Mvc\\View\\Engine\\Php'
    ));
	
	$view->setBasePath(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR);
	$view->setLayoutsDir( 'layouts' . DIRECTORY_SEPARATOR);
	$view->setPartialsDir('layouts' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR);
	$view->setViewsDir('views' . DIRECTORY_SEPARATOR);
	$view->setTemplateAfter('layout');
	
    return $view;
}, true);

$di->set(
    'cache'
    , function () use ($config,$di) {
		
		$cacheStatus = false;
		
		$dirPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'phcnscf' . DIRECTORY_SEPARATOR;
		
		System::mkdir($dirPath);
		
		if(is_dir($dirPath) && is_readable($dirPath) && is_writable($dirPath)) {
			$cacheStatus = true;
		}

		if($cacheStatus) {
            return new CacheBackendFile(new CacheFrontendData(array(
                'lifetime' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
            )), array(
                'cacheDir' => $dirPath,
                'prefix'   => WP_PEPVN_CACHE_PREFIX
            ));
		} else {
            return new CacheBackendMemory(new CacheFrontendNone());
        }
    }
	, true
);

$di->set(
    'cachePermanent'
    , function () use ($config,$di) {
		
		$cacheStatus = false;
		
		$dirPath = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'phcnscfpm' . DIRECTORY_SEPARATOR;
		
		System::mkdir($dirPath);
		
		if(is_dir($dirPath) && is_readable($dirPath) && is_writable($dirPath)) {
			$cacheStatus = true;
		}
		
		if($cacheStatus) {
            return new CacheBackendFile(new CacheFrontendData(array(
                'lifetime' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
            )), array(
                'cacheDir' => $dirPath,
                'prefix'   => WP_PEPVN_CACHE_PREFIX
            ));
		} else {
            return new CacheBackendMemory(new CacheFrontendNone());
        }
    }
	, true
);

/*
*	Config for only this plugin
*/
/*
$url = $di->get('url');
$url->setDI($di);
$url->setBasePath(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR);
$url->setBaseUri(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/');
$url->setStaticBaseUri(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/');
$di->set('url', $url);
*/
$di->set('url', function() use ($di) {
	$url = new \WpPepVN\Mvc\Url();
    $url->setDI($di);
	$url->setBasePath(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR);
	$url->setBaseUri(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/');
	$url->setStaticBaseUri(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/');
	
	return $url;
},true);

$translate = $di->getShared('translate');
$translate->load_plugin_textdomain( WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG, WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . '/languages/' );
$di->set('translate', $translate, true);

$di->set('wpRegisterStyleScript', function() {
    return new \WPOptimizeByxTraffic\Application\Service\WpRegisterStyleScript();
}, true);

$remote = new WpPepVN_Remote($di);
$remote->setCacheFileObject($di->getShared('cachePermanent'));
$di->set('remote', $remote, true);

$di->set('outputBuffer', function() use ($di) {
    return new ServiceOutputBuffer($di);
}, true);

$di->set('headerFooter', function() use ($di) {
    return new ServiceHeaderFooter($di);
}, true);

$di->set('optimizeLinks', function() use ($di) {
    return new ServiceOptimizeLinks($di);
}, true);

$di->set('optimizeTraffic', function() use ($di) {
    return new ServiceOptimizeTraffic($di);
}, true);

$di->set('optimizeImages', function() use ($di) {
    return new ServiceOptimizeImages($di);
}, true);

$ajaxHandle = new ServiceAjaxHandle($di);
$di->set('ajaxHandle', $ajaxHandle, true);

$di->set('analyzeText', function() use ($di) {
    return new ServiceAnalyzeText($di);
}, true);

$di->set('device', function() use ($di) {
    return new ServiceDevice();
}, true);

$dashboard = new ServiceDashboard($di);
$di->set('dashboard', $dashboard, true);

$di->set('pluginManager', function() use ($di) {
    return new ServicePluginManager($di);
}, true);

$di->set('queue', function() use ($di) {
    return new ServiceQueue($di, 'WPOptimizeByxTraffic\Application\Service\Queue', array());
}, true);

$language = new ServiceLanguage($di);
$di->set('language', $language, true);

$backgroundQueueJobsManager = new ServiceBackgroundQueueJobsManager($di);
$di->set('backgroundQueueJobsManager', $backgroundQueueJobsManager, true);

$wpActionManager = new WpActionManager($di);
$di->set('wpActionManager', $wpActionManager, true);

$di->set('templateReplaceVars', function() use ($di) {
    return new ServiceTemplateReplaceVars($di);
}, true);

$di->set('cronjob', function() use ($di) {
    return new ServiceCronjob($di);
}, true);

$di->set('wordnetApi', function() use ($di) {
    return new ServiceWordnetApi($di);
}, true);

$di->set('config', function() use (&$config) {
    return $config;
});


TempDataAndCacheFile::init($di);

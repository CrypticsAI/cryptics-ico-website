<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Service;

use WpPepVN\DependencyInjection\Injectable
	,WpPepVN\DependencyInjection
;

class AdminMenu extends Injectable
{
	public $admin_menu_page = false;
	
	public $di = false;
	
	public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
	}
	
	public function init_admin_menu() 
    {
		
		$url = $this->di->get('url');
		
		$adminPage = $this->di->get('adminPage');
		
		$dashboard_menu_slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_dashboard';
		
		$admin_page = add_menu_page( 
			'WP Optimize By xTraffic'	//page_title
			,'WP Optimize'	//menu_title
			, 'manage_options'	//capability
			, $dashboard_menu_slug	//menu_slug
			, array( &$adminPage, 'handle' )	//function
			, $url->getStatic('/images/icons/icon.png')	//icon_url
			, '80.01'.mt_rand() //position

		);
		
		// Sub menu pages
		$submenu_pages = array(
			
			array( 
				
				$dashboard_menu_slug
				, 'Dashboard'	//page_title
				, 'Dashboard'	//menu_title
				, 'manage_options'	//capability
				, $dashboard_menu_slug	//menu_slug
				, array( &$adminPage, 'handle' )	//function
				
			)
			
			,array( 
				
				$dashboard_menu_slug //parent_slug
				, 'Optimize Links'	//page_title
				, 'Optimize Links'	//menu_title
				, 'manage_options'	//capability
				, WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_optimize_links'	//menu_slug
				, array( &$adminPage, 'handle' )	//function
				
			)
			
			, array( 
				$dashboard_menu_slug //parent_slug
				, 'Optimize Traffic'	//page_title
				, 'Optimize Traffic'	//menu_title
				, 'manage_options'	//capability
				, WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_optimize_traffic'	//menu_slug
				, array( &$adminPage, 'handle' )	//function
				, null
			)
			
			,array( 
				
				$dashboard_menu_slug //parent_slug
				, 'Optimize Images'	//page_title
				, 'Optimize Images'	//menu_title
				, 'manage_options'	//capability
				, WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_optimize_images'	//menu_slug
				, array( &$adminPage, 'handle' )	//function
				
			)
			
			, array( 
				$dashboard_menu_slug //parent_slug
				, 'Header & Footer'	//page_title
				, 'Header & Footer'	//menu_title
				, 'manage_options'	//capability
				, WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_header_footer'	//menu_slug
				, array( &$adminPage, 'handle' )	//function
				, null
			)
			
		);
		
		if ( !empty( $submenu_pages ) ) {
			foreach ( $submenu_pages as $submenu_page ) {
				// Add submenu page
				$admin_page = add_submenu_page( $submenu_page[0], $submenu_page[1], $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );
			}
		}
		
		$this->admin_menu_page = $admin_page;
		
		return true;
	}
	
	
	public function init_admin_bar_menu() 
    {
		global $wp_admin_bar;
		
		$url = $this->di->get('url');
		$request = $this->di->getShared('request');
		
		$parentAdminBarIdClass = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-admin-bar-menu';
		
		$wp_admin_bar->add_menu( array(
			'id' => $parentAdminBarIdClass,
			'title' => '<span class="ab-icon"><img src="'.$url->getStatic('/images/icons/icon.png').'" /></span><span class="ab-label">WP Optimize</span>',
			'href' => FALSE,
			'meta' => array(
				'title' => 'WP Optimize',
				'class' => $parentAdminBarIdClass
			)
		) );
		
		
		$menuKey = $parentAdminBarIdClass.'-clear-all-caches'; 
		$wp_admin_bar->add_menu( array(
			'id' => $menuKey,
			'parent' => $parentAdminBarIdClass,
			'title' => __('Clear All Caches',WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG),
			'href' => $url->addParamsToUri(null,array(
				WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY => $request->getRequestTime()
			))
		)); 
	}
}
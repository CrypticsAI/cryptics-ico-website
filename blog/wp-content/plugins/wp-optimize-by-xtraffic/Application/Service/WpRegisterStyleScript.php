<?php 
namespace WPOptimizeByxTraffic\Application\Service;

class WpRegisterStyleScript
{
	private $_tempData = array();
	
	public function __construct() 
    {
		
	}
    
	
    /*
        @wp_register_style
        Use the wp_enqueue_scripts action to call this function. Calling it outside of an action can lead to problems
        wp_enqueue_scripts, admin_enqueue_scripts, login_enqueue_scripts
        add_action( 'wp_enqueue_scripts', array(&$this,'wp_register_script') );
    */
    public function wp_register_style() 
    {
        if(!isset($this->_tempData['wp_register_style_status'])) {
            $this->_tempData['wp_register_style_status'] = true;
            
            $slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
            $version = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
            
			/*
            $urlFileTemp = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css';
            $handleRegister = $slug.'-font-awesome';
			wp_register_style( $handleRegister,  $urlFileTemp, array(), $version, 'all');
			*/
            
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/css/bootstrap-wppepvn.'.(WP_PEPVN_DEBUG ? '' : 'min.').'css';
            $handleRegister = $slug.'-bootstrap-wppepvn';
			wp_register_style( $handleRegister,  $urlFileTemp, array(), $version, 'all');
            
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/css/wppepvn_libs.'.(WP_PEPVN_DEBUG ? '' : 'min.').'css';
            $handleRegister = $slug.'-wppepvn-libs';
			wp_register_style( $handleRegister,  $urlFileTemp, array(), $version, 'all');
			
            
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/css/admin.'.(WP_PEPVN_DEBUG ? '' : 'min.').'css';
            $handleRegister = $slug.'-admin';
			wp_register_style( $handleRegister,  $urlFileTemp, array(
				$slug.'-wppepvn-libs'
				,'wp-pointer'
			), $version, 'all');
			
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/css/frontend.'.(WP_PEPVN_DEBUG ? '' : 'min.').'css';
            $handleRegister = $slug.'-frontend';
			wp_register_style( $handleRegister,  $urlFileTemp, array(
				$slug.'-wppepvn-libs'
			), $version, 'all');
			
        }
    }
    
    /*
        @wp_register_script
        Use the wp_enqueue_scripts action to call this function. Calling it outside of an action can lead to problems
        wp_enqueue_scripts, admin_enqueue_scripts, login_enqueue_scripts
        add_action( 'wp_enqueue_scripts', array(&$this,'wp_register_script') );
		https://codex.wordpress.org/Function_Reference/wp_enqueue_script
    */
    public function wp_register_script() 
    {
        if(!isset($this->_tempData['wp_register_script'])) {
            $this->_tempData['wp_register_script'] = true;
            
            $slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
            $version = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
            
            $urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/libs/bootstrap-3.3.5-dist/js/bootstrap.min.js';
            $handleRegister = $slug.'-bootstrap';
            wp_register_script($handleRegister , $urlFileTemp, array('jquery'), $version, true);
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/js/jquery.plugins.'.(WP_PEPVN_DEBUG ? '' : 'min.').'js';
            $handleRegister = $slug.'-jquery-plugins';
            wp_register_script($handleRegister , $urlFileTemp, array('jquery','jquery-ui-core','jquery-ui-slider'), $version, true);
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/js/wppepvn_libs.'.(WP_PEPVN_DEBUG ? '' : 'min.').'js'.(WP_PEPVN_DEBUG ? '?_v='.time() : '');
            $handleRegister = $slug.'-wppepvn-libs';
            wp_register_script($handleRegister , $urlFileTemp, array($slug.'-jquery-plugins'), $version, true);
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/js/admin.'.(WP_PEPVN_DEBUG ? '' : 'min.').'js';
            $handleRegister = $slug.'-admin';
            wp_register_script($handleRegister , $urlFileTemp, array(
				$slug.'-bootstrap'
				,$slug.'-wppepvn-libs'
				,'wp-pointer'
			), $version, true);
			
			
			$urlFileTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/js/frontend.'.(WP_PEPVN_DEBUG ? '' : 'min.').'js';
            $handleRegister = $slug.'-frontend';
            wp_register_script($handleRegister , $urlFileTemp, array(
				$slug.'-wppepvn-libs'
			), $version, true);
        }
    }
    
	public function admin_enqueue_scripts() 
    {
		
		if(!isset($this->_tempData['admin_enqueue_scripts'])) {
            $this->_tempData['admin_enqueue_scripts'] = true;
            
            $slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
            $version = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
			
			$this->wp_register_style();
			$this->wp_register_script();
			
			wp_enqueue_media();
			
			wp_enqueue_script( 'jquery' );
			
			$handleRegister = $slug.'-bootstrap-wppepvn';
			wp_enqueue_style( $handleRegister );
			
			$handleRegister = $slug.'-frontend';
			wp_enqueue_style( $handleRegister );
			
			$handleRegister = $slug.'-admin';
			wp_enqueue_style( $handleRegister );
			
			$handleRegister = $slug.'-bootstrap';
			wp_enqueue_script( $handleRegister );
			
			$handleRegister = $slug.'-frontend';
			wp_enqueue_script( $handleRegister );
			
			$handleRegister = $slug.'-admin';
			wp_enqueue_script( $handleRegister );
			
		}
		
	}
	
	public function frontend_enqueue_scripts() 
    {
		
		if(!isset($this->_tempData['frontend_enqueue_scripts'])) {
            $this->_tempData['frontend_enqueue_scripts'] = true;
            
            $slug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
            $version = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
			
			$this->wp_register_style();
			$this->wp_register_script();
			
			wp_enqueue_script( 'jquery' );
			
			$handleRegister = $slug.'-frontend';
			wp_enqueue_script( $handleRegister );
			
			$handleRegister = $slug.'-frontend';
			wp_enqueue_style( $handleRegister );
			
		}
		
	}
	
	
}
<?php
/**
 * Plugin Name:  GetSale
 * Plugin URI:   https://getsale.io
 * Description:  GetSale &mdash; professional tool for creating popup windows.
 * Version:      1.0.5
 * Requires at least: 4.1
 * Tested up to: 4.8
 * Author:       GetSale Team
 * Author URI:   https://getsale.io
 * Text Domain:  getsale-popup-tool
 * Domain Path:  /languages
 **/

// Creating the widget
include 'getsale_options.php';

add_action('plugins_loaded', 'gtsl_load_textdomain');

function gtsl_load_textdomain() {
    load_plugin_textdomain('getsale-popup-tool', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('wp_enqueue_scripts', 'gtsl_scripts_method');
add_filter('plugin_action_links', 'gtsl_plugin_action_links', 10, 2);
add_action('wc_ajax_add_to_cart', 'gtsl_ajax_add_to_cart');

add_action('woocommerce_before_single_product', 'gtsl_product_view');
add_action('woocommerce_before_shop_loop', 'gtsl_category_view');

add_action('user_register', 'gtsl_registration', 10, 1);

function gtsl_registration() {
    setcookie('getsale_reg', true, time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

function gtsl_submit_order() {
    ?>
    <script type="text/javascript">
        if (window.getSale) {
            getSale.event('success-order');
            console.log('success-order');
        } else {
            (function (w, c) {
                w[c] = w[c] || [];
                w[c].push(function (getSale) {
                    getSale.event('success-order');
                    console.log('success-order')
                });
            })(window, 'getSaleCallbacks')
        }
    </script>
    <?php
}

function gtsl_product_view() {
    ?>
    <script type="text/javascript">
        if (window.getSale) {
            getSale.event('item-view');
            console.log('item-view');
        } else {
            (function (w, c) {
                w[c] = w[c] || [];
                w[c].push(function (getSale) {
                    getSale.event('item-view');
                    console.log('item-view')
                });
            })(window, 'getSaleCallbacks')
        }
    </script>
    <?php
}

function gtsl_category_view() {
    ?>
    <script type="text/javascript">
        if (window.getSale) {
            getSale.event('cat-view');
            console.log('ct-view');
        } else {
            (function (w, c) {
                w[c] = w[c] || [];
                w[c].push(function (getSale) {
                    getSale.event('cat-view');
                    console.log('cat-view')
                });
            })(window, 'getSaleCallbacks')
        }
    </script>
    <?php
}

function gtsl_ajax_add_to_cart() {
    setcookie('getsale_add', true, time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

add_action('woocommerce_thankyou', 'gtsl_submit_order');

function gtsl_plugin_action_links($actions, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__)))
        return $actions;
    $settings_link = '<a href="' . add_query_arg(array('page' => 'getsale_settings'), admin_url('plugins.php')) . '">' . __('Settings') . '</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'gtsl_plugin_description_links', 10, 4);

function gtsl_plugin_description_links($actions, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__)))
        return $actions;
    $settings_link = '<a href="' . add_query_arg(array('page' => 'getsale_settings'), admin_url('plugins.php')) . '">' . __('Settings') . '</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('wc_add_to_cart_message', 'gtsl_add_filter', 10, 4);

function gtsl_add_filter($products) {
    add_action('wp_enqueue_scripts', 'gtsl_scripts_add');
    return $products;
}

$options = get_option('getsale_option_name');

if (is_admin()) {
    $options = get_option('getsale_option_name');

    if (is_bool($options)) {
        gtsl_set_default_code();
    }

    $my_settings_page = new getsaleSettingsPage();
}

function gtsl_script_cookie() {
    if (isset($_COOKIE['getsale_add'])) {
        add_action('wp_enqueue_scripts', 'gtsl_scripts_add');
        setcookie('getsale_add', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }
}

function gtsl_scripts_add() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('getsale_add', plugins_url('js/add.js', __FILE__), array('jquery'));
        wp_enqueue_script('getsale_add');
    }
}

add_action('init', 'gtsl_script_cookie');

add_action('admin_enqueue_scripts', 'gtsl_script_translate');

function gtsl_script_translate() {
    wp_enqueue_script('getsale-main-script', dirname(plugin_basename(__FILE__)) . 'js/admin.js');
    wp_localize_script('getsale-main-script', 'gs', array(
        'authorization' => __('Authorization', 'getsale-popup-tool'),
        'enter_value' => __('Please, enter your Email and API Key from your GetSale account', 'getsale-popup-tool'),
        'registration' => __('If you don’t have GetSale account, you can register it <a href=\'https://getsale.io\'>here</a>', 'getsale-popup-tool'),
        'support' => __('Contact Us: <a href=\'mailto:support@getsale.io\'>support@getsale.io</a>', 'getsale-popup-tool'),
        'getsale_ver' => '1.0.5',
        'congrats' => __('Congratulations! Your website is successfully linked to your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool'),
        'widgets_create' => __('You can start creating widgets for your website using your <a href=\'https://getsale.io\'>GetSale account</a>!', 'getsale-popup-tool'),
        'api_key_success' => __('API Key is correct', 'getsale-popup-tool'),
        'email_success' => __('Email is correct', 'getsale-popup-tool'),
        'error403' => __('Attention! API Key is invalid. Please, check and enter API Key once again', 'getsale-popup-tool'),
        'error404' => __('Attention! This Email isn’t registered on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool'),
        'error500' => __('Attention! This website is already in use on <a href=\'https://getsale.io\'>GetSale</a>', 'getsale-popup-tool'),
        'error0' => __('You don\'t have Curl support in your PHP!', 'getsale-popup-tool'),
        'desc' => __('powerful cutting edge tool to create widgets and popups for your website!', 'getsale-popup-tool'),
        'description' => __('GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool'),
        'getsale_name' => __('GetSale Popup Tool', 'getsale-popup-tool'),
        'path' => plugins_url('img/ok.png', __FILE__),));
}

register_uninstall_hook(__FILE__, 'gtsl_plugin_uninstall');

function gtsl_plugin_uninstall() {
    delete_option('getsale_option_name');
}
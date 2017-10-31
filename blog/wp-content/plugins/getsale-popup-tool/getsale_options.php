<?php

class getsaleSettingsPage {
    public $options;
    public $settings_page_name = 'getsale_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'gtsl_add_plugin_page'));
        add_action('admin_init', array($this, 'gtsl_page_init'));
        add_action('admin_enqueue_scripts', array($this, 'gtsl_admin_styles'));
        $this->options = get_option('getsale_option_name');
    }

    public function gtsl_admin_styles() {
        wp_register_style('gtsl_admin_menu_styles', untrailingslashit(plugins_url('/', __FILE__)) . '/assets/css/getsale.css', array());
        wp_enqueue_style('gtsl_admin_menu_styles');
    }

    public function gtsl_add_plugin_page() {
        add_options_page('GetSale Settings', 'GetSale', 'manage_options', $this->settings_page_name, array(
            $this,
            'gtsl_create_admin_page'));
    }

    public function gtsl_create_admin_page() {
        $this->options = get_option('getsale_option_name');
        ?>
        <script type="text/javascript">
            <?php include('js/admin.js'); ?>
        </script>
        <div id='getsale_site_url' style='display: none'><?php echo get_site_url(); ?></div>
        <div class='wrap'>
            <div id='wrapper'>
                <form id='settings_form' method='post' action='options.php'>
                    <h1><?php _e('GetSale Popup Tool'); ?></h1>
                    <?php
                    gtsl_echo_before_text();
                    settings_fields('getsale_option_group');
                    do_settings_sections('getsale_settings');
                    ?>
                    <input type='submit' name='submit_btn'>
                </form>
            </div>
        </div>
        <?php
    }

    public function gtsl_page_init() {
        register_setting('getsale_option_group', 'getsale_option_name', array($this, 'gtsl_sanitize'));

        add_settings_section('setting_section_id', '', // Title
            array($this, 'gtsl_print_section_info'), $this->settings_page_name);

        add_settings_field('email', __('Email', 'getsale-popup-tool'), array(
            $this,
            'gtsl_email_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_api_key', __('API Key', 'getsale-popup-tool'), array(
            $this,
            'gtsl_api_key_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_reg_error', 'getsale_reg_error', array(
            $this,
            'gtsl_reg_error_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('getsale_project_id', 'getsale_project_id', array(
            $this,
            'gtsl_project_id_callback'), $this->settings_page_name, 'setting_section_id');
    }

    public function gtsl_sanitize($input) {
        $new_input = array();
        $domain = 'https://getsale.io';
        $url = get_site_url();
        if (($input['getsale_email'] !== '') && ($input['getsale_api_key'] !== '') && ($input['getsale_project_id'] == '')) {
            $reg_ans = gtsl_reg($domain, $input['getsale_email'], $input['getsale_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $new_input = get_option('getsale_option_name');
                    $new_input['getsale_project_id'] = $reg_ans->payload->projectId;
                    $new_input['getsale_email'] = trim($input['getsale_email']);
                    $new_input['getsale_api_key'] = trim($input['getsale_api_key']);
                    $new_input['getsale_reg_error'] = '';
                    update_option('uptolike_options', $new_input);
                }
                elseif ($reg_ans->status = 'error') {
                    $new_input = get_option('getsale_option_name');
                    $new_input['getsale_project_id'] = '';
                    $new_input['getsale_email'] = trim($input['getsale_email']);
                    $new_input['getsale_api_key'] = trim($input['getsale_api_key']);
                    $new_input['getsale_reg_error'] = $reg_ans->code;
                    update_option('uptolike_options', $new_input);
                }
            }
        }
        return $new_input;
    }

    public function gtsl_print_section_info() {
    }

    public function gtsl_email_callback() {
        printf('<input type="text" id="getsale_email" name="getsale_option_name[getsale_email]" value="%s" title="%s"/>', isset($this->options['getsale_email']) ? esc_attr(trim($this->options['getsale_email'])) : '', __('Enter Email', 'getsale-popup-tool'));
    }

    public function gtsl_api_key_callback() {
        printf('<input type="text" id="getsale_api_key" name="getsale_option_name[getsale_api_key]" value="%s" title="%s" />', isset($this->options['getsale_api_key']) ? esc_attr(trim($this->options['getsale_api_key'])) : '', __('Enter API Key', 'getsale-popup-tool'));
    }

    public function gtsl_reg_error_callback() {
        printf('<input type="text" id="getsale_reg_error" name="getsale_option_name[getsale_reg_error]" value="%s" />', isset($this->options['getsale_reg_error']) ? esc_attr($this->options['getsale_reg_error']) : '');
    }

    public function gtsl_project_id_callback() {
        printf('<input type="text" id="getsale_project_id" name="getsale_option_name[getsale_project_id]" value="%s" />', isset($this->options['getsale_project_id']) ? esc_attr($this->options['getsale_project_id']) : '');
    }
}

function gtsl_echo_before_text() {
    echo '<div id=\'before_install\' style=\'display:none;\'>' . __('GetSale Popup Tool has been successfully installed', 'getsale-popup-tool') . '<br/>' . __('To get started, you must enter Email and API Key, from from your <a href=\'https://getsale.io\'>GetSale account</a>', 'getsale-popup-tool') . '</div>
<div class="wrap" id="after_install" style="display:none;">
<p><b>' . __('GetSale Popup Tool', 'getsale-popup-tool') . '</b> &mdash; ' . __('professional tool for creating popup windows', 'getsale-popup-tool') . '</p>
<p>' . __('GetSale is a powerful tool for creating all types of widgets for your website. You can increase your sales dramatically creating special offer, callback widgets, coupons blasts and many more. Create, Show and Sell - this is our goal!', 'getsale-popup-tool') . '</p>
</div>
</div>
<script type=\'text/javascript\'>
    window.onload = function () {
        if (document.location.search == \'?option=com_installer&view=install\') {
            document.getElementById(\'before_install\').style.display = \'block\';
        } else document.getElementById(\'after_install\').style.display = \'block\';
    }
</script>';
}

function gtsl_reg($regDomain, $email, $key, $url) {
    $domain = $regDomain;
    if (($domain == '') OR ($email == '') OR ($key == '') OR ($url == '')) {
        return;
    }

    if (!function_exists('curl_init')) {
        $json_result = '';
        $json_result->status = 'error';
        $json_result->code = 0;
        $json_result->message = 'No Curl!';
        return $json_result;
    };

    $ch = curl_init();
    $jsondata = json_encode(array(
        'email' => trim($email),
        'key' => $key,
        'url' => $url,
        'cms' => 'wordpress'));

    $options = array(
        CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'),
        CURLOPT_URL => $domain . '/api/registration.json',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsondata,
        CURLOPT_RETURNTRANSFER => true);

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    curl_close($ch);
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))) {
        }
        elseif ($json_result->status = 'error') {
        }
    }
    return $json_result;
}

function gtsl_scripts_method() {
    $options = get_option('getsale_option_name');
    if ($options['getsale_project_id'] !== '') {
        wp_register_script('gtsl_handle', plugins_url('js/main.js', __FILE__), array('jquery'));

        $datatoBePassed = array('project_id' => $options['getsale_project_id']);
        wp_localize_script('gtsl_handle', 'getsale_vars', $datatoBePassed);

        wp_enqueue_script('gtsl_handle');
    }
}

function gtsl_set_default_code() {
    $options = get_option('getsale_option_name');
    if (is_bool($options)) {
        $options = array();
        $options['getsale_email'] = '';
        $options['getsale_api_key'] = '';
        $options['getsale_project_id'] = '';
        $options['getsale_reg_error'] = '';
        update_option('getsale_option_name', $options);
    }
}

add_action('admin_menu', 'gtsl_admin_actions');

function gtsl_admin_actions() {
    if (current_user_can('manage_options')) {
        if (function_exists('add_meta_box')) {
            add_menu_page('GetSale Settings', 'GetSale', 'manage_options', 'getsale_settings', 'gtsl_custom_menu_page', null, 100);
        }
    }
}

function gtsl_custom_menu_page() {
    $getsale_settings_page = new getsaleSettingsPage();
    if (!isset($getsale_settings_page)) {
        wp_die(__('Plugin GetSale has been installed incorrectly.'));
    }
    if (function_exists('add_plugins_page')) {
        add_plugins_page('GetSale Settings', 'GetSale', 'manage_options', 'getsale_settings', array(
            &$getsale_settings_page,
            'gtsl_create_admin_page'));
    }
}
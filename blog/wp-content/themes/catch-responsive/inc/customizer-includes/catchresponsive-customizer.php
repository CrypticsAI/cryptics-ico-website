<?php
/**
 * The main template for implementing Theme/Customzer Options
 *
 * @package Catch Themes
 * @subpackage Catch Responsive
 * @since Catch Responsive 1.0
 */

if ( ! defined( 'CATCHRESPONSIVE_THEME_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * Implements Catchresponsive theme options into Theme Customizer.
 *
 * @param $wp_customize Theme Customizer object
 * @return void
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport			= 'postMessage';

	/**
	  * Set priority of blogname (Site Title) to 1.
	  *  Strangly, if more than two options is added, Site title is moved below Tagline. This rectifies this issue.
	  */
	$wp_customize->get_control( 'blogname' )->priority			= 1;

	$wp_customize->get_setting( 'blogdescription' )->transport	= 'postMessage';

	$options  = catchresponsive_get_theme_options();

	$defaults = catchresponsive_get_default_theme_options();

	//Custom Controls
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-custom-controls.php';

	//@remove Remove this block when WordPress 4.8 is released
	if ( ! function_exists( 'has_custom_logo' ) ) {
		// Custom Logo (added to Site Title and Tagline section in Theme Customizer)
		$wp_customize->add_setting( 'catchresponsive_theme_options[logo]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $defaults['logo'],
			'sanitize_callback'	=> 'catchresponsive_sanitize_image'
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
			'label'		=> __( 'Logo', 'catch-responsive' ),
			'priority'	=> 100,
			'section'   => 'title_tagline',
	        'settings'  => 'catchresponsive_theme_options[logo]',
	    ) ) );

	    $wp_customize->add_setting( 'catchresponsive_theme_options[logo_disable]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $defaults['logo_disable'],
			'sanitize_callback' => 'catchresponsive_sanitize_checkbox',
		) );

		$wp_customize->add_control( 'catchresponsive_theme_options[logo_disable]', array(
			'label'    => __( 'Check to disable logo', 'catch-responsive' ),
			'priority' => 101,
			'section'  => 'title_tagline',
			'settings' => 'catchresponsive_theme_options[logo_disable]',
			'type'     => 'checkbox',
		) );

		$wp_customize->add_setting( 'catchresponsive_theme_options[logo_alt_text]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $defaults['logo_alt_text'],
			'sanitize_callback'	=> 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'catchresponsive_logo_alt_text', array(
			'label'    	=> __( 'Logo Alt Text', 'catch-responsive' ),
			'priority'	=> 102,
			'section' 	=> 'title_tagline',
			'settings' 	=> 'catchresponsive_theme_options[logo_alt_text]',
			'type'     	=> 'text',
		) );
	}

	$wp_customize->add_setting( 'catchresponsive_theme_options[move_title_tagline]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['move_title_tagline'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[move_title_tagline]', array(
		'label'    => __( 'Check to move Site Title and Tagline before logo', 'catch-responsive' ),
		'priority' => function_exists( 'has_custom_logo' ) ? 10 : 103,
		'section'  => 'title_tagline',
		'settings' => 'catchresponsive_theme_options[move_title_tagline]',
		'type'     => 'checkbox',
	) );
	// Custom Logo End

	// Color Scheme
	$wp_customize->add_setting( 'catchresponsive_theme_options[color_scheme]', array(
		'capability' 		=> 'edit_theme_options',
		'default'    		=> $defaults['color_scheme'],
		'sanitize_callback'	=> 'catchresponsive_sanitize_select',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[color_scheme]', array(
		'choices'  => catchresponsive_color_schemes(),
		'label'    => __( 'Color Scheme', 'catch-responsive' ),
		'priority' => 5,
		'section'  => 'colors',
		'settings' => 'catchresponsive_theme_options[color_scheme]',
		'type'     => 'radio',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[mobile_menu_color_scheme]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['mobile_menu_color_scheme'],
		'sanitize_callback' => 'catchresponsive_sanitize_select'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[mobile_menu_color_scheme]', array(
		'choices'  => catchresponsive_color_schemes(),
		'label'    => __( 'Mobile Menu Color Scheme', 'catch-responsive' ),
		'section'  => 'colors',
		'settings' => 'catchresponsive_theme_options[mobile_menu_color_scheme]',
		'type'     => 'radio',
	) );
	//End Color Scheme

	// Header Options (added to Header section in Theme Customizer)
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-header-options.php';

	//Theme Options
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-theme-options.php';

	//Featured Content Setting
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-featured-content-setting.php';

	//Featured Slider
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-featured-slider.php';

	//Social Links
	require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-social-icons.php';

	// Reset all settings to default
	$wp_customize->add_section( 'catchresponsive_reset_all_settings', array(
		'description'	=> __( 'Caution: Reset all settings to default. Refresh the page after save to view full effects.', 'catch-responsive' ),
		'priority' 		=> 700,
		'title'    		=> __( 'Reset all settings', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[reset_all_settings]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['reset_all_settings'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox',
		'transport'			=> 'postMessage',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[reset_all_settings]', array(
		'label'    => __( 'Check to reset all settings to default', 'catch-responsive' ),
		'section'  => 'catchresponsive_reset_all_settings',
		'settings' => 'catchresponsive_theme_options[reset_all_settings]',
		'type'     => 'checkbox',
	) );
	// Reset all settings to default end

	//Important Links
	$wp_customize->add_section( 'important_links', array(
		'priority' 		=> 999,
		'title'   	 	=> __( 'Important Links', 'catch-responsive' ),
	) );

	/**
	 * Has dummy Sanitizaition function as it contains no value to be sanitized
	 */
	$wp_customize->add_setting( 'important_links', array(
		'sanitize_callback'	=> 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Catchresponsive_Important_Links( $wp_customize, 'important_links', array(
        'label'   	=> __( 'Important Links', 'catch-responsive' ),
         'section'  	=> 'important_links',
        'settings' 	=> 'important_links',
        'type'     	=> 'important_links',
    ) ) );
    //Important Links End
}
add_action( 'customize_register', 'catchresponsive_customize_register' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously for catchresponsive.
 * And flushes out all transient data on preview
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_customize_preview() {
	wp_enqueue_script( 'catchresponsive_customizer', get_template_directory_uri() . '/js/catchresponsive-customizer.min.js', array( 'customize-preview' ), '20120827', true );

	//Flush transients on preview
	catchresponsive_flush_transients();
}
add_action( 'customize_preview_init', 'catchresponsive_customize_preview' );


/**
 * Custom scripts and styles on customize.php for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_customize_scripts() {
	wp_enqueue_script( 'catchresponsive_customizer_custom', get_template_directory_uri() . '/js/catchresponsive-customizer-custom-scripts.min.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20150630', true );

	$catchresponsive_misc_links = array(
		'reset_message'       => esc_html__( 'Refresh the customizer page after saving to view reset effects', 'catch-responsive' )
	);

	$catchresponsive_misc_links['color_list'] = catchresponsive_color_list();

	//Add Upgrade Button and old WordPress message via localized script
	wp_localize_script( 'catchresponsive_customizer_custom', 'catchresponsive_misc_links', $catchresponsive_misc_links );

	wp_enqueue_script( 'catchresponsive_customizer_custom' );
}
add_action( 'customize_controls_enqueue_scripts', 'catchresponsive_customize_scripts');


/**
 * Returns list of color keys of array with default values for each color scheme as index
 *
 * @since Catch Base 2.1
 */
function catchresponsive_color_list() {
	// Get default color scheme values
	$default 		= catchresponsive_get_default_theme_options();
	// Get default dark color scheme valies
	$default_dark 	= catchresponsive_default_dark_color_options();

	$catchresponsive_color_list['background_color']['light']	= $default['background_color'];
	$catchresponsive_color_list['background_color']['dark']	= $default_dark['background_color'];

	$catchresponsive_color_list['header_textcolor']['light']	= $default['header_textcolor'];
	$catchresponsive_color_list['header_textcolor']['dark']	= $default_dark['header_textcolor'];

	return $catchresponsive_color_list;
}


/**
 * Function to reset date with respect to condition
 */
function catchresponsive_reset_data() {
	$options  = catchresponsive_get_theme_options();
    if ( $options['reset_all_settings'] ) {
    	remove_theme_mods();

        // Flush out all transients	on reset
        catchresponsive_flush_transients();
    }
}
add_action( 'customize_save_after', 'catchresponsive_reset_data' );


//Active callbacks for customizer
require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-active-callbacks.php';

//Sanitize functions for customizer
require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer-sanitize-functions.php';

// Upgrade To Pro Button
require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/upgrade-button/class-customize.php';
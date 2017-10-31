<?php
/**
 * The template for adding additional theme options in Customizer
 *
 * @package Catch Themes
 * @subpackage Catch Responsive
 * @since Catch Responsive 1.0
 */

// Additional Color Scheme (added to Color Scheme section in Theme Customizer)
if ( ! defined( 'CATCHRESPONSIVE_THEME_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


	//Theme Options
	$wp_customize->add_panel( 'catchresponsive_theme_options', array(
	    'description'    => __( 'Basic theme Options', 'catch-responsive' ),
	    'capability'     => 'edit_theme_options',
	    'priority'       => 200,
	    'title'    		 => __( 'Theme Options', 'catch-responsive' ),
	) );


	// Breadcrumb Option
	$wp_customize->add_section( 'catchresponsive_breadcumb_options', array(
		'description'	=> __( 'Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance. You can enable/disable them on homepage and entire site.', 'catch-responsive' ),
		'panel'			=> 'catchresponsive_theme_options',
		'title'    		=> __( 'Breadcrumb Options', 'catch-responsive' ),
		'priority' 		=> 201,
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[breadcumb_option]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['breadcumb_option'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[breadcumb_option]', array(
		'label'    => __( 'Check to enable Breadcrumb', 'catch-responsive' ),
		'section'  => 'catchresponsive_breadcumb_options',
		'settings' => 'catchresponsive_theme_options[breadcumb_option]',
		'type'     => 'checkbox',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[breadcumb_onhomepage]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['breadcumb_onhomepage'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[breadcumb_onhomepage]', array(
		'label'    => __( 'Check to enable Breadcrumb on Homepage', 'catch-responsive' ),
		'section'  => 'catchresponsive_breadcumb_options',
		'settings' => 'catchresponsive_theme_options[breadcumb_onhomepage]',
		'type'     => 'checkbox',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[breadcumb_seperator]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['breadcumb_seperator'],
		'sanitize_callback'	=> 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[breadcumb_seperator]', array(
			'input_attrs' => array(
	            'style' => 'width: 40px;'
            	),
            'label'    	=> __( 'Separator between Breadcrumbs', 'catch-responsive' ),
			'section' 	=> 'catchresponsive_breadcumb_options',
			'settings' 	=> 'catchresponsive_theme_options[breadcumb_seperator]',
			'type'     	=> 'text'
		)
	);
   	// Breadcrumb Option End

   	/**
	 * Do not show Custom CSS option from WordPress 4.7 onwards
	 * @remove when WP 5.0 is released
	 */
	if ( !function_exists( 'wp_update_custom_css_post' ) ) {
		// Custom CSS Option
		$wp_customize->add_section( 'catchresponsive_custom_css', array(
			'description'	=> __( 'Custom/Inline CSS', 'catch-responsive'),
			'panel'  		=> 'catchresponsive_theme_options',
			'priority' 		=> 203,
			'title'    		=> __( 'Custom CSS Options', 'catch-responsive' ),
		) );

		$wp_customize->add_setting( 'catchresponsive_theme_options[custom_css]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $defaults['custom_css'],
			'sanitize_callback' => 'catchresponsive_sanitize_custom_css',
		) );

		$wp_customize->add_control( 'catchresponsive_theme_options[custom_css]', array(
				'label'		=> __( 'Enter Custom CSS', 'catch-responsive' ),
		        'priority'	=> 1,
				'section'   => 'catchresponsive_custom_css',
		        'settings'  => 'catchresponsive_theme_options[custom_css]',
				'type'		=> 'textarea',
		) );
   		// Custom CSS End
   	}

   	// Excerpt Options
	$wp_customize->add_section( 'catchresponsive_excerpt_options', array(
		'panel'  	=> 'catchresponsive_theme_options',
		'priority' 	=> 204,
		'title'    	=> __( 'Excerpt Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[excerpt_length]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['excerpt_length'],
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[excerpt_length]', array(
		'description' => __('Excerpt length. Default is 55 words', 'catch-responsive'),
		'input_attrs' => array(
            'min'   => 10,
            'max'   => 200,
            'step'  => 5,
            'style' => 'width: 60px;'
            ),
        'label'    => __( 'Excerpt Length (words)', 'catch-responsive' ),
		'section'  => 'catchresponsive_excerpt_options',
		'settings' => 'catchresponsive_theme_options[excerpt_length]',
		'type'	   => 'number',
	)	);


	$wp_customize->add_setting( 'catchresponsive_theme_options[excerpt_more_text]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['excerpt_more_text'],
		'sanitize_callback'	=> 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[excerpt_more_text]', array(
		'label'    => __( 'Read More Text', 'catch-responsive' ),
		'section'  => 'catchresponsive_excerpt_options',
		'settings' => 'catchresponsive_theme_options[excerpt_more_text]',
		'type'	   => 'text',
	) );
	// Excerpt Options End

	//Homepage / Frontpage Options
	$wp_customize->add_section( 'catchresponsive_homepage_options', array(
		'description'	=> __( 'Only posts that belong to the categories selected here will be displayed on the front page', 'catch-responsive' ),
		'panel'			=> 'catchresponsive_theme_options',
		'priority' 		=> 209,
		'title'   	 	=> __( 'Homepage / Frontpage Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[front_page_category]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['front_page_category'],
		'sanitize_callback'	=> 'catchresponsive_sanitize_category_list',
	) );

	$wp_customize->add_control( new Catchresponsive_Customize_Dropdown_Categories_Control( $wp_customize, 'catchresponsive_theme_options[front_page_category]', array(
        'label'   	=> __( 'Select Categories', 'catch-responsive' ),
        'name'	 	=> 'catchresponsive_theme_options[front_page_category]',
		'priority'	=> '6',
        'section'  	=> 'catchresponsive_homepage_options',
        'settings' 	=> 'catchresponsive_theme_options[front_page_category]',
        'type'     	=> 'dropdown-categories',
    ) ) );
	//Homepage / Frontpage Settings End


	// Layout Options
	$wp_customize->add_section( 'catchresponsive_layout', array(
		'capability'=> 'edit_theme_options',
		'panel'		=> 'catchresponsive_theme_options',
		'priority'	=> 211,
		'title'		=> __( 'Layout Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[theme_layout]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['theme_layout'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[theme_layout]', array(
		'choices'	=> catchresponsive_layouts(),
		'label'		=> __( 'Default Layout', 'catch-responsive' ),
		'section'	=> 'catchresponsive_layout',
		'settings'   => 'catchresponsive_theme_options[theme_layout]',
		'type'		=> 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[content_layout]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['content_layout'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[content_layout]', array(
		'choices'   => catchresponsive_get_archive_content_layout(),
		'label'		=> __( 'Archive Content Layout', 'catch-responsive' ),
		'section'   => 'catchresponsive_layout',
		'settings'  => 'catchresponsive_theme_options[content_layout]',
		'type'      => 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[single_post_image_layout]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['single_post_image_layout'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[single_post_image_layout]', array(
			'label'		=> __( 'Single Page/Post Image Layout ', 'catch-responsive' ),
			'section'   => 'catchresponsive_layout',
	        'settings'  => 'catchresponsive_theme_options[single_post_image_layout]',
	        'type'	  	=> 'select',
			'choices'  	=> catchresponsive_single_post_image_layout_options(),
	) );
   	// Layout Options End

	// Pagination Options
	$pagination_type	= $options['pagination_type'];

	$catchresponsive_navigation_description = sprintf( __( 'Numeric Option requires <a target="_blank" href="%1$s">WP-PageNavi Plugin</a>.<br/>Infinite Scroll Options requires <a target="_blank" href="%2$s">JetPack Plugin</a> with Infinite Scroll module Enabled.', 'catch-responsive' ), esc_url( 'https://wordpress.org/plugins/wp-pagenavi' ), esc_url( 'https://wordpress.org/plugins/jetpack/' ) );

	/**
	 * Check if navigation type is Jetpack Infinite Scroll and if it is enabled
	 */
	if ( ( 'infinite-scroll-click' == $pagination_type || 'infinite-scroll-scroll' == $pagination_type ) ) {
		if ( ! (class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) ) {
			$catchresponsive_navigation_description = sprintf( __( 'Infinite Scroll Options requires <a target="_blank" href="%s">JetPack Plugin</a> with Infinite Scroll module Enabled.', 'catch-responsive' ), esc_url( 'https://wordpress.org/plugins/jetpack/' ) );
		}
		else {
			$catchresponsive_navigation_description = '';
		}
	}
	/**
	* Check if navigation type is numeric and if Wp-PageNavi Plugin is enabled
	*/
	elseif ( 'numeric' == $pagination_type ) {
		if ( !function_exists( 'wp_pagenavi' ) ) {
			$catchresponsive_navigation_description = sprintf( __( 'Numeric Option requires <a target="_blank" href="%s">WP-PageNavi Plugin</a>.', 'catch-responsive' ), esc_url( 'https://wordpress.org/plugins/wp-pagenavi' ) );
		}
		else {
			$catchresponsive_navigation_description = '';
		}
    }

	$wp_customize->add_section( 'catchresponsive_pagination_options', array(
		'description'	=> $catchresponsive_navigation_description,
		'panel'  		=> 'catchresponsive_theme_options',
		'priority'		=> 212,
		'title'    		=> __( 'Pagination Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[pagination_type]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['pagination_type'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[pagination_type]', array(
		'choices'  => catchresponsive_get_pagination_types(),
		'label'    => __( 'Pagination type', 'catch-responsive' ),
		'section'  => 'catchresponsive_pagination_options',
		'settings' => 'catchresponsive_theme_options[pagination_type]',
		'type'	   => 'select',
	) );
	// Pagination Options End

	//Promotion Headline Options
    $wp_customize->add_section( 'catchresponsive_promotion_headline_options', array(
		'description'	=> __( 'To disable the fields, simply leave them empty.', 'catch-responsive' ),
		'panel'			=> 'catchresponsive_theme_options',
		'priority' 		=> 213,
		'title'   	 	=> __( 'Promotion Headline Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_headline_option]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['promotion_headline_option'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_headline_option]', array(
		'choices'  	=> catchresponsive_featured_slider_content_options(),
		'label'    	=> __( 'Enable Promotion Headline on', 'catch-responsive' ),
		'priority'	=> '0.5',
		'section'  	=> 'catchresponsive_promotion_headline_options',
		'settings' 	=> 'catchresponsive_theme_options[promotion_headline_option]',
		'type'	  	=> 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_headline]', array(
		'capability'		=> 'edit_theme_options',
		'default' 			=> $defaults['promotion_headline'],
		'sanitize_callback'	=> 'wp_kses_post'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_headline]', array(
		'description'	=> __( 'Appropriate Words: 10', 'catch-responsive' ),
		'label'    	=> __( 'Promotion Headline Text', 'catch-responsive' ),
		'priority'	=> '1',
		'section' 	=> 'catchresponsive_promotion_headline_options',
		'settings'	=> 'catchresponsive_theme_options[promotion_headline]',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_subheadline]', array(
		'capability'		=> 'edit_theme_options',
		'default' 			=> $defaults['promotion_subheadline'],
		'sanitize_callback'	=> 'wp_kses_post'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_subheadline]', array(
		'description'	=> __( 'Appropriate Words: 15', 'catch-responsive' ),
		'label'    	=> __( 'Promotion Subheadline Text', 'catch-responsive' ),
		'priority'	=> '2',
		'section' 	=> 'catchresponsive_promotion_headline_options',
		'settings'	=> 'catchresponsive_theme_options[promotion_subheadline]',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_headline_button]', array(
		'capability'		=> 'edit_theme_options',
		'default' 			=> $defaults['promotion_headline_button'],
		'sanitize_callback'	=> 'sanitize_text_field'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_headline_button]', array(
		'description'	=> __( 'Appropriate Words: 3', 'catch-responsive' ),
		'label'    	=> __( 'Promotion Headline Button Text ', 'catch-responsive' ),
		'priority'	=> '3',
		'section' 	=> 'catchresponsive_promotion_headline_options',
		'settings'	=> 'catchresponsive_theme_options[promotion_headline_button]',
		'type'		=> 'text',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_headline_url]', array(
		'capability'		=> 'edit_theme_options',
		'default' 			=> $defaults['promotion_headline_url'],
		'sanitize_callback'	=> 'esc_url_raw'
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_headline_url]', array(
		'label'    	=> __( 'Promotion Headline Link', 'catch-responsive' ),
		'priority'	=> '4',
		'section' 	=> 'catchresponsive_promotion_headline_options',
		'settings'	=> 'catchresponsive_theme_options[promotion_headline_url]',
		'type'		=> 'text',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[promotion_headline_target]', array(
		'capability'		=> 'edit_theme_options',
		'default' 			=> $defaults['promotion_headline_target'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[promotion_headline_target]', array(
		'label'    	=> __( 'Check to Open Link in New Window/Tab', 'catch-responsive' ),
		'priority'	=> '5',
		'section'  	=> 'catchresponsive_promotion_headline_options',
		'settings' 	=> 'catchresponsive_theme_options[promotion_headline_target]',
		'type'     	=> 'checkbox',
	) );
	// Promotion Headline Options End

	// Scrollup
	$wp_customize->add_section( 'catchresponsive_scrollup', array(
		'panel'    => 'catchresponsive_theme_options',
		'priority' => 215,
		'title'    => __( 'Scrollup Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[disable_scrollup]', array(
		'capability'		=> 'edit_theme_options',
        'default'			=> $defaults['disable_scrollup'],
		'sanitize_callback' => 'catchresponsive_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[disable_scrollup]', array(
		'label'		=> __( 'Check to disable Scroll Up', 'catch-responsive' ),
		'section'   => 'catchresponsive_scrollup',
        'settings'  => 'catchresponsive_theme_options[disable_scrollup]',
		'type'		=> 'checkbox',
	) );
	// Scrollup End

	// Search Options
	$wp_customize->add_section( 'catchresponsive_search_options', array(
		'description'=> __( 'Change default placeholder text in Search.', 'catch-responsive'),
		'panel'  => 'catchresponsive_theme_options',
		'priority' => 216,
		'title'    => __( 'Search Options', 'catch-responsive' ),
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[search_text]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['search_text'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[search_text]', array(
		'label'		=> __( 'Default Display Text in Search', 'catch-responsive' ),
		'section'   => 'catchresponsive_search_options',
        'settings'  => 'catchresponsive_theme_options[search_text]',
		'type'		=> 'text',
	) );
	// Search Options End
//Theme Option End
<?php
/**
 * The template for adding Featured Slider Options in Customizer
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
	// Featured Slider
	if ( 4.3 > get_bloginfo( 'version' ) ) {
		$wp_customize->add_panel( 'catchresponsive_featured_slider', array(
		    'capability'     => 'edit_theme_options',
		    'description'    => __( 'Featured Slider Options', 'catch-responsive' ),
		    'priority'       => 500,
			'title'    		 => __( 'Featured Slider', 'catch-responsive' ),
		) );

		$wp_customize->add_section( 'catchresponsive_featured_slider', array(
			'panel'			=> 'catchresponsive_featured_slider',
			'priority'		=> 1,
			'title'			=> __( 'Featured Slider Options', 'catch-responsive' ),
		) );
	}
	else {
		$wp_customize->add_section( 'catchresponsive_featured_slider', array(
			'priority'      => 500,
			'title'			=> __( 'Featured Slider', 'catch-responsive' ),
		) );
	}

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slider_option]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slider_option'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slider_option]', array(
		'choices'   => catchresponsive_featured_slider_content_options(),
		'label'    	=> __( 'Enable Slider on', 'catch-responsive' ),
		'priority'	=> '1.1',
		'section'  	=> 'catchresponsive_featured_slider',
		'settings' 	=> 'catchresponsive_theme_options[featured_slider_option]',
		'type'    	=> 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slide_transition_effect]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slide_transition_effect'],
		'sanitize_callback'	=> 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slide_transition_effect]' , array(
		'active_callback'	=> 'catchresponsive_is_slider_active',
		'choices'  			=> catchresponsive_featured_slide_transition_effects(),
		'label'				=> __( 'Transition Effect', 'catch-responsive' ),
		'priority'			=> '2',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings'			=> 'catchresponsive_theme_options[featured_slide_transition_effect]',
		'type'				=> 'select',
		)
	);

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slide_transition_delay]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slide_transition_delay'],
		'sanitize_callback'	=> 'absint',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slide_transition_delay]' , array(
		'active_callback'	=> 'catchresponsive_is_slider_active',
		'description'		=> __( 'seconds(s)', 'catch-responsive' ),
		'input_attrs' 		=> array(
				            	'style' => 'width: 40px;'
				        	),
		'label'    			=> __( 'Transition Delay', 'catch-responsive' ),
		'priority'			=> '2.1.1',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings' 			=> 'catchresponsive_theme_options[featured_slide_transition_delay]',
		)
	);

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slide_transition_length]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slide_transition_length'],
		'sanitize_callback'	=> 'absint',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slide_transition_length]' , array(
		'active_callback'	=> 'catchresponsive_is_slider_active',
		'description'		=> __( 'seconds(s)', 'catch-responsive' ),
		'input_attrs' 		=> array(
					            'style' => 'width: 40px;'
				            	),
		'label'    			=> __( 'Transition Length', 'catch-responsive' ),
		'priority'			=> '2.1.2',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings' 			=> 'catchresponsive_theme_options[featured_slide_transition_length]',
		)
	);

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slider_image_loader]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slider_image_loader'],
		'sanitize_callback' => 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slider_image_loader]', array(
		'active_callback'	=> 'catchresponsive_is_slider_active',
		'choices'   		=> catchresponsive_featured_slider_image_loader(),
		'label'    			=> __( 'Image Loader', 'catch-responsive' ),
		'priority'			=> '2.1.3',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings' 			=> 'catchresponsive_theme_options[featured_slider_image_loader]',
		'type'    			=> 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slider_type]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slider_type'],
		'sanitize_callback'	=> 'catchresponsive_sanitize_select',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slider_type]', array(
		'active_callback'	=> 'catchresponsive_is_slider_active',
		'choices'  			=> catchresponsive_featured_slider_types(),
		'label'    			=> __( 'Select Slider Type', 'catch-responsive' ),
		'priority'			=> '2.1.3',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings' 			=> 'catchresponsive_theme_options[featured_slider_type]',
		'type'	  			=> 'select',
	) );

	$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slide_number]', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> $defaults['featured_slide_number'],
		'sanitize_callback'	=> 'catchresponsive_sanitize_number_range',
	) );

	$wp_customize->add_control( 'catchresponsive_theme_options[featured_slide_number]' , array(
		'active_callback'	=> 'catchresponsive_is_demo_slider_inactive',
		'description'		=> __( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'catch-responsive' ),
		'input_attrs' 		=> array(
						            'style' => 'width: 45px;',
						            'min'   => 0,
						            'max'   => 20,
						            'step'  => 1,
						        	),
		'label'    			=> __( 'No of Slides', 'catch-responsive' ),
		'priority'			=> '2.1.4',
		'section'  			=> 'catchresponsive_featured_slider',
		'settings' 			=> 'catchresponsive_theme_options[featured_slide_number]',
		'type'	   			=> 'number',
		)
	);

	//loop for featured page sliders
	for ( $i=1; $i <= $options['featured_slide_number'] ; $i++ ) {
		$wp_customize->add_setting( 'catchresponsive_theme_options[featured_slider_page_'. $i .']', array(
			'capability'		=> 'edit_theme_options',
			'sanitize_callback'	=> 'catchresponsive_sanitize_page',
		) );

		$wp_customize->add_control( 'catchresponsive_theme_options[featured_slider_page_'. $i .']', array(
			'active_callback'	=> 'catchresponsive_is_demo_slider_inactive',
			'label'    			=> __( 'Featured Page', 'catch-responsive' ) . ' # ' . $i ,
			'priority'			=> '4' . $i,
			'section'  			=> 'catchresponsive_featured_slider',
			'settings' 			=> 'catchresponsive_theme_options[featured_slider_page_'. $i .']',
			'type'	   			=> 'dropdown-pages',
		) );
	}
// Featured Slider End
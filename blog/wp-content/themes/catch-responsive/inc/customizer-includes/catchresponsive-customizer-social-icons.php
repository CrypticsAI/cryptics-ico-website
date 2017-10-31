<?php
/**
 * The template for Social Links in Customizer
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

	// Social Icons
	$wp_customize->add_panel( 'catchresponsive_social_links', array(
	    'capability'     => 'edit_theme_options',
	    'description'	=> __( 'Note: Enter the url for correponding social networking website', 'catch-responsive' ),
	    'priority'       => 600,
		'title'    		 => __( 'Social Links', 'catch-responsive' ),
	) );


	$wp_customize->add_section( 'catchresponsive_social_links', array(
		'panel'			=> 'catchresponsive_social_links',
		'priority' 		=> 1,
		'title'   	 	=> __( 'Social Links', 'catch-responsive' ),
	) );

	$catchresponsive_social_icons 	=	catchresponsive_get_social_icons_list();

	$i 	=	1;

	$catchresponsive_social_icons 	=	catchresponsive_get_social_icons_list();

	foreach ( $catchresponsive_social_icons as $key => $value ){
		if ( 'skype_link' == $key ){
			$wp_customize->add_setting( 'catchresponsive_theme_options['. $key .']', array(
					'capability'		=> 'edit_theme_options',
					'sanitize_callback' => 'esc_attr',
				) );

			$wp_customize->add_control( 'catchresponsive_theme_options['. $key .']', array(
				'description'	=> __( 'Skype link can be of formats:<br>callto://+{number}<br> skype:{username}?{action}. More Information in readme file', 'catch-responsive' ),
				'label'    		=> $value['label'],
				'section'  		=> 'catchresponsive_social_links',
				'settings' 		=> 'catchresponsive_theme_options['. $key .']',
				'type'	   		=> 'url',
			) );
		}
		else {
			if ( 'email_link' == $key ){
				$wp_customize->add_setting( 'catchresponsive_theme_options['. $key .']', array(
						'capability'		=> 'edit_theme_options',
						'sanitize_callback' => 'sanitize_email',
					) );
			}
			elseif ( 'handset_link' == $key || 'phone_link' == $key ){
				$wp_customize->add_setting( 'catchresponsive_theme_options['. $key .']', array(
						'capability'		=> 'edit_theme_options',
						'sanitize_callback' => 'sanitize_text_field',
					) );
			}
			else {
				$wp_customize->add_setting( 'catchresponsive_theme_options['. $key .']', array(
						'capability'		=> 'edit_theme_options',
						'sanitize_callback' => 'esc_url_raw',
					) );
			}

			$wp_customize->add_control( 'catchresponsive_theme_options['. $key .']', array(
				'label'    => $value['label'],
				'section'  => 'catchresponsive_social_links',
				'settings' => 'catchresponsive_theme_options['. $key .']',
				'type'	   => 'url',
			) );
		}
	}
	// Social Icons End
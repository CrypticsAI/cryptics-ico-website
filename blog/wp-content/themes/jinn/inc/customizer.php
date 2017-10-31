<?php
/**
 * Jinn Theme Customizer.
 *
 * @package Jinn
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function jinn_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
        
        /*
         * Custom Customizer Customizations
         * #1: Settings, #2: Controls
         */
        
        /*
         * Highlight Color
         */
        // Highlight Color Setting
        $wp_customize->add_setting( 'highlight_color', array(
            'default'           => '#00adcf', // steelblue
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage'
        ) );
        
        // Highligh Color Control
        $wp_customize->add_control(
                new WP_Customize_Color_Control(
                        $wp_customize,
                        'highlight_color', array(
                            'label'         => __( 'Highlight Color', 'jinn' ),
                            'description'   => __( 'Change the color of site highlights, inluding links.', 'jinn' ),
                            'section'       => 'colors',
                        )
        ) );
        
        /*
         * Show Sitename in Menubar Checkbox
         */
        // Show Sitename in Menubar Setting
        $wp_customize->add_setting( 'show_sitename_in_menubar', array(
            'default'           => 1,
            'sanitize_callback' => 'jinn_sanitize_checkbox',
        ) );
        
        // Show Sitename in Menubar Control
        $wp_customize->add_control(
                new WP_Customize_Control(
                        $wp_customize,
                        'show_sitename_in_menubar',
                        array( 
                            'label'         => __( 'Show sitename in menu bar?', 'jinn' ),
                            'type'          => 'checkbox',
                            'section'       => 'title_tagline',
                        )
        ) );
        
        /* ///////////////// GRADIENT ////////////////// */
        /* 
         * Gradient Color #1 
         */
        // Gradient #1 Color Setting
        $wp_customize->add_setting( 'grad1_color', array(
            'default'           => '#085078',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage'
        ) );
        
        // Gradient #1 Color Control
        $wp_customize->add_control( 
                new WP_Customize_Color_Control(
                        $wp_customize,
                        'grad1_color', array(
                            'label'         => __( 'Header Gradient: Top-Left Color', 'jinn' ),
                            'description'   => __( 'Set or change the upper left gradient starting color.', 'jinn' ),
                            'section'       => 'colors',
                        )
        ) );
        
        /* 
         * Gradient Color #2 
         */
        // Gradient #2 Color Setting
        $wp_customize->add_setting( 'grad2_color', array(
            'default'           => '#85D8CE',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage'
        ) );
        
        // Gradient #2 Color Control
        $wp_customize->add_control( 
                new WP_Customize_Color_Control(
                        $wp_customize,
                        'grad2_color', array(
                            'label'         => __( 'Header Gradient: Center Color', 'jinn' ),
                            'description'   => __( 'Set or change the center gradient color.', 'jinn' ),
                            'section'       => 'colors',
                        )
        ) );
        
        /* 
         * Gradient Color #3 
         */
        // Gradient #3 Color Setting
        $wp_customize->add_setting( 'grad3_color', array(
            'default'           => '#F8FFF3',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage'
        ) );
        
        // Gradient #2 Color Control
        $wp_customize->add_control( 
                new WP_Customize_Color_Control(
                        $wp_customize,
                        'grad3_color', array(
                            'label'         => __( 'Header Gradient: Bottom-Right Color', 'jinn' ),
                            'description'   => __( 'Set or change the lower right gradient ending color.', 'jinn' ),
                            'section'       => 'colors',
                        )
        ) );
 
        
        /* ///////////////// SIDEBAR LAYOUT ////////////////// */
        
        /* 
         * Select Sidebar Layout 
         */
        // Add Sidebar Layout Section
        $wp_customize->add_section( 'jinn-options', array(
            'title'         => __( 'Theme Options', 'jinn' ),
            'capability'    => 'edit_theme_options',
            'description'   => __( 'Change the default display options for the theme.', 'jinn' ),
        ) );
        
        // Sidebar Layout setting
        $wp_customize->add_setting( 'layout_setting',
                array(
                    'default'           => 'no-sidebar',
                    'type'              => 'theme_mod',
                    'sanitize_callback' => 'jinn_sanitize_layout',
                    'transport'         => 'postMessage'
                ) );
        
        // Sidebar Layout Control
        $wp_customize->add_control( 'layout_control',
                array(
                    'settings'          => 'layout_setting',
                    'type'              => 'radio',
                    'label'             => __( 'Sidebar position', 'jinn' ),
                    'choices'           => array(
                            'no-sidebar'    => __( 'No sidebar (default)', 'jinn' ),
                            'sidebar-right' => __( 'Sidebar right', 'jinn' ),
                            'sidebar-left'  => __( 'Sidebar left', 'jinn' ),
                    ),
                    'section'           => 'jinn-options'
                ) );
        
        /**
         * Show Theme Info
         */
        $wp_customize->add_setting( 'show_theme_info',
                array(
                    'default'           => 1,
                    'sanitize_callback' => 'jinn_sanitize_checkbox'
                ));
        
        $wp_customize->add_control( 'show_theme_info',
                array(
                    'label'             => __( 'Show theme info in Footer?', 'jinn' ),
                    'type'              => 'checkbox',
                    'section'           => 'jinn-options'
                ));
        
        /**
         * Show Copyright Date
         */
        $wp_customize->add_setting( 'show_copyright',
                array(
                    'default'           => 1,
                    'sanitize_callback' => 'jinn_sanitize_checkbox'
                ));
        
        $wp_customize->add_control( 'show_copyright',
                array(
                    'label'             => __( 'Show copyright dates in Footer?', 'jinn' ),
                    'type'              => 'checkbox',
                    'section'           => 'jinn-options'
                ));
}
add_action( 'customize_register', 'jinn_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function jinn_customize_preview_js() {
	wp_enqueue_script( 'jinn_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20160507', true );
}
add_action( 'customize_preview_init', 'jinn_customize_preview_js' );

/*
 * Sanitize layout options
 */
function jinn_sanitize_layout ( $value ) {
    if ( !in_array( $value, array( 'no-sidebar', 'sidebar-right', 'sidebar-left' ) ) ) {
        $value = 'no-sidebar';
    }
    return $value;
}

/**
 * Checkbox sanitization callback
 * @link    https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php
 * 
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 * 
 * @param   bool    $checked    Whether the checkbox is checked.
 * @return  bool                Whether the checkbox is checked.
 */
function jinn_sanitize_checkbox( $checked ) {
    // Boolean check
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/*
 * Inject Customizer CSS when appropriate
 */
function jinn_customizer_css() {
    $highlight_color = get_theme_mod( 'highlight_color' );
    
    $use_gradient = get_theme_mod( 'use_gradient' );
    
    $gradient_color_1 = get_theme_mod( 'grad1_color', '#085078' );
    $gradient_color_2 = get_theme_mod( 'grad2_color', '#85D8CE' );
    $gradient_color_3 = get_theme_mod( 'grad3_color', '#F8FFF3' );
    
    ?>
    <style>
        .custom-header {
            background: radial-gradient( ellipse farthest-side at 100% 100%,
                <?php echo esc_attr( $gradient_color_3 ); ?> 10%,
                <?php echo esc_attr( $gradient_color_2 ); ?> 50%,
                <?php echo esc_attr( $gradient_color_1 ); ?> 120% );
            <?php echo $use_gradient ? 'height: 600px;' : ''; ?>
        }
        a,
        a:visited,
        a:hover,
        a:focus,
        a:active,
        .entry-content a,
        .entry-summary a {
            color: <?php echo esc_attr( $highlight_color ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'jinn_customizer_css' );
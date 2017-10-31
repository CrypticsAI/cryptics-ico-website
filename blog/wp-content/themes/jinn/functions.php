<?php
/**
 * components functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Jinn
 */

if ( ! function_exists( 'jinn_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the aftercomponentsetup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function jinn_setup() {
    
        // This theme styles the visual editor to resemble the theme style.
        $font_url = 'http://fonts.googleapis.com/css?family=Khula:300,400,600,700,800';
        add_editor_style( array( 'inc/editor-style.css', str_replace( ',', '%2C', $font_url ) ) );
    
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on components, use a find and replace
	 * to change 'jinn' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'jinn', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'jinn-featured-image', 800, 9999 );
	add_image_size( 'jinn-portfolio-featured-image', 800, 9999 );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'jinn' ),
                'social'  => esc_html__( 'Social Menu', 'jinn' ),
                'footer'  => esc_html__( 'Footer Menu', 'jinn' ),
                'front'   => esc_html__( 'Front Page Menu', 'jinn' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
                'gallery',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'jinn_custom_background_args', array(
		'default-color' => 'f1f1f1',
		'default-image' => '',
	) ) );
        
        // Add Custom Logo support (WordPress defaults)
        // @link https://make.wordpress.org/core/2016/03/10/custom-logo/
        add_theme_support( 'custom-logo', array(
                'height'        => 66,
                'width'         => 66,
                'flex-height'   => true,
                'flex-width'    => true,
                'header-text'   => array( 'site-title', 'site-description' ), // class names to replace with the logo
        ) );
        
}
endif;
add_action( 'after_setup_theme', 'jinn_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function jinn_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jinn_content_width', 640 );
}
add_action( 'after_setup_theme', 'jinn_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function jinn_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'jinn' ),
                'description'   => esc_html__( 'Widgets in this sidebar will appear throughout the site. It is the default sidebar if no others are in use.', 'jinn' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s ">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
        
        register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Pages', 'jinn' ),
                'description'   => esc_html__( 'Widgets in this sidebar will only appear on Pages. It replaces the standard sidebar.', 'jinn' ),
		'id'            => 'sidebar-page',
		'before_widget' => '<div id="%1$s" class="widget %2$s ">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
        
        register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Custom Post Types', 'jinn' ),
                'description'   => esc_html__( 'Widgets in this sidebar will only appear on JetPack Portfolio or Testimonial Posts. It replaces the standard sidebar.', 'jinn' ),
		'id'            => 'sidebar-custom',
		'before_widget' => '<div id="%1$s" class="widget %2$s ">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
        
        register_sidebar( array(
                'name'          => esc_html__( 'Footer Widgets', 'jinn' ),
                'description'   => esc_html__( 'Widgets appearing above the footer of the site.', 'jinn' ),
                'id'            => 'sidebar-footer',
                'before_widget' => '<div id="%1$s" class="widget small-6 medium-4 large-3 columns %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
        ) );
}
add_action( 'widgets_init', 'jinn_widgets_init' );

/**
 * Enqueue Foundation scripts and styles.
 * 
 * @link: http://wordpress.tv/2014/06/11/steve-zehngut-build-a-wordpress-theme-with-foundation-and-underscores/
 * @link: http://wordpress.tv/2014/03/31/steve-zehngut-theme-development-with-foundation-framework/
 * @link: http://www.justinfriebel.com/wordpress-underscores-with-the-foundation-framework-09-23-2014/
 * 
 */
function jinn_foundation_enqueue() {
    
        /* Add Foundation 6.2 CSS */
        wp_enqueue_style( 'foundation', get_stylesheet_directory_uri() . '/assets/foundation/css/foundation.min.css' );    // This is the Foundation CSS
        
        /* Add Foundation JS */
        wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/foundation/js/foundation.min.js', array( 'jquery' ), true );
        //wp_enqueue_script( 'foundation-what-input', get_template_directory_uri() . '/assets/foundation/js/what-input.js', array( 'jquery' ), true );
        
        /* Foundation Init JS */
        wp_enqueue_script( 'foundation-init-js', get_template_directory_uri() . '/foundation.js', array( 'jquery' ), true );   // Small (author) customized JS script to start the Foundation library, sitting freely in the Theme folder
        
        /* Add Custom Fonts */
        wp_enqueue_style( 'jinn-local-fonts', get_template_directory_uri() . '/assets/fonts/custom-fonts.css' );
        wp_enqueue_style( 'jinn-fawesome', get_template_directory_uri() . '/assets/fonts/font-awesome.css' );  
        
}
add_action( 'wp_enqueue_scripts', 'jinn_foundation_enqueue' );

/**
 * Enqueue scripts and styles.
 */
function jinn_scripts() {
	wp_enqueue_style( 'jinn-style', get_stylesheet_uri() );
        
        /* Include Dashicons for the front-end too */
        wp_enqueue_style( 'dashicons' );

	/* Conditional stylesheet only for Front Page Template */
        if ( is_page_template( 'page-templates/frontpage-portfolio.php' ) ) {
            wp_enqueue_script( 'jinn-front-scripts', get_stylesheet_directory_uri() . '/assets/js/frontpage-functions.js', array( 'jquery' ), '20160515', true ); 

            /* Slick Carousel */
            wp_enqueue_script( 'slick_carousel', get_stylesheet_directory_uri() . '/assets/js/slick/slick.min.js', array( 'jquery' ), '20160515', true ); 
            wp_enqueue_style( 'slick_style', get_stylesheet_directory_uri() . '/assets/js/slick/slick.css' );
            wp_enqueue_style( 'slick_theme_style', get_stylesheet_directory_uri() . '/assets/js/slick/slick-theme.css' );
        }

        /* Custom navigation script */
	wp_enqueue_script( 'jinn-navigation', get_template_directory_uri() . '/assets/js/navigation-custom.js', array(), '20120206', true );
        
        /* Toggle Main Search script */
        wp_enqueue_script( 'jinn-toggle-search', get_template_directory_uri() . '/assets/js/toggle-search.js', array( 'jquery' ), '20150925', true );

        /* Masonry for Footer widgets */
        wp_enqueue_script( 'jinn-masonry', get_template_directory_uri() . '/assets/js/masonry-settings.js', array( 'masonry' ), '20150925', true );
        
        /* Add dynamic back to top button */
        wp_enqueue_script( 'jinn-topbutton', get_template_directory_uri(). '/assets/js/topbutton.js', array( 'jquery' ), '20150926', true );

	wp_enqueue_script( 'jinn-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jinn_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * -----------------------------------------------------------------------------
 * JINN custom functions below
 * -----------------------------------------------------------------------------
 */

/*
 * Add Excerpts to Pages
 */
function jinn_add_excerpt_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'jinn_add_excerpt_to_pages' );

/**
 * Modify Underscores nav menus to work with Foundation
 */
function jinn_nav_menu( $menu ) {
    
    $menu = str_replace( 'menu-item-has-children', 'menu-item-has-children has-dropdown', $menu );
    $menu = str_replace( 'sub-menu', 'sub-menu dropdown', $menu );
    return $menu;
    
}
add_filter( 'wp_nav_menu', 'jinn_nav_menu' );

/**
 * Walker Menu for Front Page nav
 */
class jinn_front_page_walker extends Walker_Nav_Menu {
  
    // add classes to ul sub-menus
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        // depth dependent classes
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
        $display_depth = ( $depth + 1); // because it counts the first submenu as 0
        $classes = array(
                'sub-menu',
                ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
                ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
                'menu-depth-' . $display_depth
            );
        $class_names = implode( ' ', $classes );

        // build html
        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
    }

    // add main/sub classes to li's and links
     function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

        // depth dependent classes
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        // passed classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $li_class_names = esc_attr( implode( ' ', apply_filters( '', array_filter( $classes ), $item ) ) );
        $fa_class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        // build html
        /*
         * Card Front
         */
        $foundationTouch = 'ontouchstart="this.classList.toggle(\'hover\');"';
        $output .= $indent . '<li ' . $foundationTouch . ' id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . /* $class_names . */ '">';
        $output .= '<div class="large button card-front">';

        // link attributes
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
            $args->before,
            $attributes,
            $args->link_before,
            apply_filters( 'the_title', $item->title, $item->ID ),
            $args->link_after . '<i class="fa ' . $li_class_names . ' card-icon"></i>',
            $args->after
        );

        // build html
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        $output .= '</div>';

        /** 
         * Card Back
         */
        $output .= '<div class="panel card-back">';
        $output .= '<i class="fa ' . $fa_class_names . ' card-icon"></i>';
        $output .= '<div class="hub-info">';
        
        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
            $args->before,
            $attributes,
            $args->link_before,
            apply_filters( 'the_title', $item->attr_title, $item->ID ),
            $args->link_after,
            $args->after
        );
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        
        $output .= '<p>';
        $output .= isset( $item->description ) ? $item->description : '';
        $output .= '</p></div><!-- .hub-info -->';
        $output .= '<small class="clear">';
        $output .= isset( $item->xfn ) ? $item->xfn : ''; 
        $output .= '</small>';
        $output .= '</div><!-- .card-back -->';
    }
}
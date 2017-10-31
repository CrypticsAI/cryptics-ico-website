<?php
/**
 * Core functions and definitions
 *
 * Sets up the theme
 *
 * The first function, catchresponsive_initial_setup(), sets up the theme by registering support
 * for various features in WordPress, such as theme support, post thumbnails, navigation menu, and the like.
 *
 * Catchresponsive functions and definitions
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


if ( ! function_exists( 'catchresponsive_content_width' ) ) :
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function catchresponsive_content_width() {
		$content_width = 780; /* pixels */

		$GLOBALS['content_width'] = apply_filters( 'catchresponsive_content_width', $content_width );
	}
endif;
add_action( 'after_setup_theme', 'catchresponsive_content_width', 0 );


if ( ! function_exists( 'catchresponsive_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 */
	function catchresponsive_setup() {
		/**
		 * Get Theme Options Values
		 */
		$options 	= catchresponsive_get_theme_options();

		/**
		 * Make theme available for translation
		 * Translations can be filed in the /languages/ directory
		 * If you're building a theme based on catchresponsive, use a find and replace
		 * to change 'catch-responsive' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'catch-responsive', get_template_directory() . '/languages' );

		/**
		 * Add default posts and comments RSS feed links to head
		 */
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Enable support for Post Thumbnails on posts and pages
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		// Add Catch Responsive's custom image sizes
		add_image_size( 'catchresponsive-slider', 1200, 514, true ); // Used for Featured slider Ratio 21:9

		add_image_size( 'catchresponsive-featured-content', 350, 197, true ); // used in Featured Content Options Ratio 16:9

		//Archive Images
		add_image_size( 'catchresponsive-featured', 860, 484, true); // used in Archive Top Ratio 16:9
		add_image_size( 'catchresponsive-square', 200, 200, true ); // used in Archive Left/Right Ratio 1:1

    	/**
		 * This theme uses wp_nav_menu() in one location.
		 */
		register_nav_menus( array(
			'primary' 	=> __( 'Primary Menu', 'catch-responsive' ),
			'secondary' => __( 'Secondary Menu', 'catch-responsive' ),
		) );

		/**
		 * Enable support for Post Formats
		 */
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

		/**
		 * Setup the WordPress core custom background feature.
		 */
		if ( 'light' == $options['color_scheme'] ) {
			$default_bg_color = catchresponsive_get_default_theme_options();
			$default_bg_color = $default_bg_color['background_color'];
			$default_bg_image = 'body-bg.jpg';
		}
		elseif ( 'dark' == $options['color_scheme'] ) {
			$default_bg_color = catchresponsive_default_dark_color_options();
			$default_bg_color = $default_bg_color['background_color'];
			$default_bg_image = 'body-bg-dark.jpg';
		}

		add_theme_support( 'custom-background', apply_filters( 'catchresponsive_custom_background_args', array(
			'default-color' => $default_bg_color,
			'default-image'	=> get_template_directory_uri() . '/images/' . $default_bg_image,
		) ) );

		/*
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, icons, and column width.
		 */
		add_editor_style( array( 'css/editor-style.css', catchresponsive_fonts_url() ) );

		/**
		 * Setup title support for theme
		 * Supported from WordPress version 4.1 onwards
		 * More Info: https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
		 */
		add_theme_support( 'title-tag' );

		//@remove Remove check when WordPress 4.8 is released
		if ( function_exists( 'has_custom_logo' ) ) {
			/**
			* Setup Custom Logo Support for theme
			* Supported from WordPress version 4.5 onwards
			* More Info: https://make.wordpress.org/core/2016/03/10/custom-logo/
			*/
			add_theme_support( 'custom-logo' );
		}

		/**
		 * Setup Infinite Scroll using JetPack if navigation type is set
		 */
		$pagination_type	= $options['pagination_type'];

		if ( 'infinite-scroll-click' == $pagination_type ) {
			add_theme_support( 'infinite-scroll', array(
				'type'		=> 'click',
				'container' => 'main',
				'footer'    => 'page'
			) );
		}
		elseif ( 'infinite-scroll-scroll' == $pagination_type ) {
			//Override infinite scroll disable scroll option
        	update_option('infinite_scroll', true);

        	add_theme_support( 'infinite-scroll', array(
				'type'		=> 'scroll',
				'container' => 'main',
				'footer'    => 'page'
			) );
		}
	}
endif; // catchresponsive_setup
add_action( 'after_setup_theme', 'catchresponsive_setup' );


/**
 * Return the Google font stylesheet URL, if available.
 *
 * The use of Open Sans and Droid Sans by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since Catch Responsive 1.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function catchresponsive_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'catch-responsive' );

	/* Translators: If there are characters in your language that are not
	 * supported by Droid Sans, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$droid_sans = _x( 'on', 'Droid Sans font: on or off', 'catch-responsive' );

	if ( 'off' !== $open_sans || 'off' !== $droid_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open Sans:300,400,700,300italic,400italic,700italic';

		if ( 'off' !== $droid_sans )
			$font_families[] = 'Droid Sans:400,700';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}


/**
 * Enqueue scripts and styles
 *
 * @uses  wp_register_script, wp_enqueue_script, wp_register_style, wp_enqueue_style, wp_localize_script
 * @action wp_enqueue_scripts
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_scripts() {
	$options			= catchresponsive_get_theme_options();

	// Add Source Sans Pro and Bitter fonts, used in the main stylesheet.
	wp_enqueue_style( 'catchresponsive-fonts', catchresponsive_fonts_url(), array(), null );

	wp_enqueue_style( 'catchresponsive-style', get_stylesheet_uri() );

	wp_enqueue_script( 'catchresponsive-navigation', get_template_directory_uri() . '/js/navigation.min.js', array(), '20120206', true );

	// Load the html5 shiv.
	wp_enqueue_script( 'catchresponsive-html5', get_template_directory_uri() . '/js/html5.min.js', array(), '3.7.3' );
	wp_script_add_data( 'catchresponsive-html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'catchresponsive-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.min.js', array(), '20130115', true );

	/**
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	//For genericons
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/css/genericons/genericons.css', false, '3.4.1' );

	/**
	 * Enqueue the styles for the current color scheme for catchresponsive.
	 */
	if ( $options['color_scheme'] != 'light' ) {
		wp_enqueue_style( 'catchresponsive-dark', get_template_directory_uri() . '/css/colors/dark.css', array(), null );
	}

	/**
	 * Loads up Responsive Menu
	 */
	wp_enqueue_script( 'jquery-sidr', get_template_directory_uri() . '/js/jquery.sidr.min.js', array('jquery'), '2.2.1.1', false );

	/**
	 * Loads default sidr color scheme styles(Does not require handle prefix)
	 */
	if ( 'dark' == $options['mobile_menu_color_scheme'] ){
		wp_enqueue_style( 'jquery-sidr', get_template_directory_uri() . '/css/jquery.sidr.dark.min.css', false, '2.1.0' );
	}
	else {
		wp_enqueue_style( 'jquery-sidr', get_template_directory_uri() . '/css/jquery.sidr.light.min.css', false, '2.1.0' );
	}

	wp_enqueue_script( 'jquery-fitvids', get_template_directory_uri() . '/js/fitvids.min.js', array( 'jquery' ), '1.1', true );


	/**
	 * Loads up Cycle JS
	 */
	if ( $options['featured_slider_option'] != 'disabled' ) {
		wp_register_script( 'jquery.cycle2', get_template_directory_uri() . '/js/jquery.cycle/jquery.cycle2.min.js', array( 'jquery' ), '2.1.5', true );

		/**
		 * Condition checks for additional slider transition plugins
		 */
		// Scroll Vertical transition plugin addition
		if ( 'scrollVert' ==  $options['featured_slide_transition_effect'] ){
			wp_enqueue_script( 'jquery.cycle2.scrollVert', get_template_directory_uri() . '/js/jquery.cycle/jquery.cycle2.scrollVert.min.js', array( 'jquery.cycle2' ), '20140128', true );
		}
		// Flip transition plugin addition
		elseif ( 'flipHorz' ==  $options['featured_slide_transition_effect'] || 'flipVert' ==  $options['featured_slide_transition_effect'] ){
			wp_enqueue_script( 'jquery.cycle2.flip', get_template_directory_uri() . '/js/jquery.cycle/jquery.cycle2.flip.min.js', array( 'jquery.cycle2' ), '20140128', true );
		}
		// Shuffle transition plugin addition
		elseif ( 'tileSlide' ==  $options['featured_slide_transition_effect'] || 'tileBlind' ==  $options['featured_slide_transition_effect'] ){
			wp_enqueue_script( 'jquery.cycle2.tile', get_template_directory_uri() . '/js/jquery.cycle/jquery.cycle2.tile.min.js', array( 'jquery.cycle2' ), '20140128', true );
		}
		// Shuffle transition plugin addition
		elseif ( 'shuffle' ==  $options['featured_slide_transition_effect'] ){
			wp_enqueue_script( 'jquery.cycle2.shuffle', get_template_directory_uri() . '/js/jquery.cycle/jquery.cycle2.shuffle.min.js', array( 'jquery.cycle2' ), '20140128 ', true );
		}
		else {
			wp_enqueue_script( 'jquery.cycle2' );
		}
	}

	/**
	 * Loads up Scroll Up script
	 */
	if ( ! $options['disable_scrollup'] ) {
		wp_enqueue_script( 'catchresponsive-scrollup', get_template_directory_uri() . '/js/catchresponsive-scrollup.min.js', array( 'jquery' ), '20072014', true  );
	}

	/**
	 * Enqueue custom script for catchresponsive.
	 */
	wp_enqueue_script( 'catchresponsive-custom-scripts', get_template_directory_uri() . '/js/catchresponsive-custom-scripts.min.js', array( 'jquery' ), null );
}
add_action( 'wp_enqueue_scripts', 'catchresponsive_scripts' );


/**
 * Enqueue scripts and styles for Metaboxes
 * @uses wp_register_script, wp_enqueue_script, and  wp_enqueue_style
 *
 * @action admin_print_scripts-post-new, admin_print_scripts-post, admin_print_scripts-page-new, admin_print_scripts-page
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_enqueue_metabox_scripts() {
    //Scripts
	wp_enqueue_script( 'catchresponsive-metabox', get_template_directory_uri() . '/js/catchresponsive-metabox.min.js', array( 'jquery', 'jquery-ui-tabs' ), '2013-10-05' );

	//CSS Styles
	wp_enqueue_style( 'catchresponsive-metabox-tabs', get_template_directory_uri() . '/css/catchresponsive-metabox-tabs.css' );
}
add_action( 'admin_print_scripts-post-new.php', 'catchresponsive_enqueue_metabox_scripts', 11 );
add_action( 'admin_print_scripts-post.php', 'catchresponsive_enqueue_metabox_scripts', 11 );
add_action( 'admin_print_scripts-page-new.php', 'catchresponsive_enqueue_metabox_scripts', 11 );
add_action( 'admin_print_scripts-page.php', 'catchresponsive_enqueue_metabox_scripts', 11 );


/**
 * Default Options.
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-default-options.php';

/**
 * Custom Header.
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-custom-header.php';


/**
 * Structure for catchresponsive
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-structure.php';


/**
 * Customizer additions.
 */
require trailingslashit( get_template_directory() ) . 'inc/customizer-includes/catchresponsive-customizer.php';


/**
 * Custom Menus
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-menus.php';


/**
 * Load Slider file.
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-featured-slider.php';


/**
 * Load Featured Content.
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-featured-content.php';


/**
 * Load Breadcrumb file.
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-breadcrumb.php';


/**
 * Load Widgets and Sidebars
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-widgets.php';


/**
 * Load Social Icons
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-social-icons.php';


/**
 * Load Metaboxes
 */
require trailingslashit( get_template_directory() ) . 'inc/catchresponsive-metabox.php';


/**
 * Returns the options array for catchresponsive.
 * @uses  get_theme_mod
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_get_theme_options() {
	$catchresponsive_default_options = catchresponsive_get_default_theme_options();

	return array_merge( $catchresponsive_default_options , get_theme_mod( 'catchresponsive_theme_options', $catchresponsive_default_options ) ) ;
}


/**
 * Flush out all transients
 *
 * @uses delete_transient
 *
 * @action customize_save, catchresponsive_customize_preview (see catchresponsive_customizer function: catchresponsive_customize_preview)
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_flush_transients(){
	delete_transient( 'catchresponsive_featured_content' );

	delete_transient( 'catchresponsive_featured_slider' );

	delete_transient( 'catchresponsive_custom_css' );

	delete_transient( 'catchresponsive_footer_content' );

	delete_transient( 'catchresponsive_promotion_headline' );

	delete_transient( 'catchresponsive_featured_image' );

	delete_transient( 'catchresponsive_social_icons' );

	delete_transient( 'catchresponsive_scrollup' );

	delete_transient( 'all_the_cool_cats' );

	//Add Catchresponsive default themes if there is no values
	if ( !get_theme_mod('catchresponsive_theme_options') ) {
		set_theme_mod( 'catchresponsive_theme_options', catchresponsive_get_default_theme_options() );
	}
}
add_action( 'customize_save', 'catchresponsive_flush_transients' );

/**
 * Flush out category transients
 *
 * @uses delete_transient
 *
 * @action edit_category
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_flush_category_transients(){
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'catchresponsive_flush_category_transients' );


/**
 * Flush out post related transients
 *
 * @uses delete_transient
 *
 * @action save_post
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_flush_post_transients(){
	delete_transient( 'catchresponsive_featured_content' );

	delete_transient( 'catchresponsive_featured_slider' );

	delete_transient( 'catchresponsive_featured_image' );

	delete_transient( 'all_the_cool_cats' );
}
add_action( 'save_post', 'catchresponsive_flush_post_transients' );


if ( ! function_exists( 'catchresponsive_custom_css' ) ) :
	/**
	 * Enqueue Custon CSS
	 *
	 * @uses  set_transient, wp_head, wp_enqueue_style
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_custom_css() {
		//catchresponsive_flush_transients();
		$options 	= catchresponsive_get_theme_options();

		$defaults 	= catchresponsive_get_default_theme_options();

		if ( ( !$catchresponsive_custom_css = get_transient( 'catchresponsive_custom_css' ) ) ) {
			$catchresponsive_custom_css ='';

			// Has the text been hidden?
			if ( ! display_header_text() ) {
				$catchresponsive_custom_css    .=  ".site-title a, .site-description { position: absolute !important; clip: rect(1px 1px 1px 1px); clip: rect(1px, 1px, 1px, 1px); }". "\n";
			}

			//Custom CSS Option
			if ( !empty( $options['custom_css'] ) ) {
				$catchresponsive_custom_css	.=  $options['custom_css'] . "\n";
			}

			if ( '' != $catchresponsive_custom_css ){
				echo '<!-- refreshing cache -->' . "\n";

				$catchresponsive_custom_css = '<!-- '.get_bloginfo('name').' inline CSS Styles -->' . "\n" . '<style type="text/css" media="screen">' . "\n" . $catchresponsive_custom_css;

				$catchresponsive_custom_css .= '</style>' . "\n";

			}

			set_transient( 'catchresponsive_custom_css', htmlspecialchars_decode( $catchresponsive_custom_css ), 86940 );
		}

		echo $catchresponsive_custom_css;
	}
endif; //catchresponsive_custom_css
add_action( 'wp_head', 'catchresponsive_custom_css', 101  );


if ( ! function_exists( 'catchresponsive_content_nav' ) ) :
	/**
	 * Display navigation to next/previous pages when applicable
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_content_nav( $nav_id ) {
		global $wp_query, $post;

		// Don't print empty markup on single pages if there's nowhere to navigate.
		if ( is_single() ) {
			$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
			$next = get_adjacent_post( false, '', false );

			if ( ! $next && ! $previous )
				return;
		}

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$options			= catchresponsive_get_theme_options();

		$pagination_type	= $options['pagination_type'];

		$nav_class = ( is_single() ) ? 'site-navigation post-navigation' : 'site-navigation paging-navigation';

		/**
		 * Check if navigation type is Jetpack Infinite Scroll and if it is enabled, else goto default pagination
		 * if it's active then disable pagination
		 */
		if ( ( 'infinite-scroll-click' == $pagination_type || 'infinite-scroll-scroll' == $pagination_type ) && class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			return false;
		}

		?>
	        <nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>">
	        	<h3 class="screen-reader-text"><?php _e( 'Post navigation', 'catch-responsive' ); ?></h3>
				<?php
				/**
				 * Check if navigation type is numeric and if Wp-PageNavi Plugin is enabled
				 */
				if ( 'numeric' == $pagination_type && function_exists( 'wp_pagenavi' ) ) {
					wp_pagenavi();
	            }
	            else { ?>
	                <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'catch-responsive' ) ); ?></div>
	                <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'catch-responsive' ) ); ?></div>
	            <?php
	            } ?>
	        </nav><!-- #nav -->
		<?php
	}
endif; // catchresponsive_content_nav


if ( ! function_exists( 'catchresponsive_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_comment( $comment, $args, $depth ) {
		if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-body">
				<?php _e( 'Pingback:', 'catch-responsive' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'catch-responsive' ), '<span class="edit-link">', '</span>' ); ?>
			</div>

		<?php else : ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
						<?php printf( __( '%s <span class="says">says:</span>', 'catch-responsive' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'catch-responsive' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>
						<?php edit_comment_link( __( 'Edit', 'catch-responsive' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'catch-responsive' ); ?></p>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<?php
					comment_reply_link( array_merge( $args, array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="reply">',
						'after'     => '</div>',
					) ) );
				?>
			</article><!-- .comment-body -->

		<?php
		endif;
	}
endif; // catchresponsive_comment()


if ( ! function_exists( 'catchresponsive_the_attached_image' ) ) :
	/**
	 * Prints the attached image with a link to the next attached image.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_the_attached_image() {
		$post                = get_post();
		$attachment_size     = apply_filters( 'catchresponsive_attachment_size', array( 1200, 1200 ) );
		$next_attachment_url = wp_get_attachment_url();

		/**
		 * Grab the IDs of all the image attachments in a gallery so we can get the
		 * URL of the next adjacent image in a gallery, or the first image (if
		 * we're looking at the last image in a gallery), or, in a gallery of one,
		 * just the link to that image file.
		 */
		$attachment_ids = get_posts( array(
			'post_parent'    => $post->post_parent,
			'fields'         => 'ids',
			'numberposts'    => 1,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ID'
		) );

		// If there is more than 1 attachment in a gallery...
		if ( count( $attachment_ids ) > 1 ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if ( $attachment_id == $post->ID ) {
					$next_id = current( $attachment_ids );
					break;
				}
			}

			// get the URL of the next image attachment...
			if ( $next_id )
				$next_attachment_url = get_attachment_link( $next_id );

			// or get the URL of the first image attachment.
			else
				$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
		}

		printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
			esc_url( $next_attachment_url ),
			the_title_attribute( 'echo=0' ),
			wp_get_attachment_image( $post->ID, $attachment_size )
		);
	}
endif; //catchresponsive_the_attached_image


if ( ! function_exists( 'catchresponsive_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_entry_meta() {
		echo '<p class="entry-meta">';

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		printf( '<span class="posted-on">%1$s<a href="%2$s" rel="bookmark">%3$s</a></span>',
			sprintf( __( '<span class="screen-reader-text">Posted on</span>', 'catch-responsive' ) ),
			esc_url( get_permalink() ),
			$time_string
		);

		if ( is_singular() || is_multi_author() ) {
			printf( '<span class="byline"><span class="author vcard">%1$s<a class="url fn n" href="%2$s">%3$s</a></span></span>',
				sprintf( __( '<span class="screen-reader-text">Author</span>', 'catch-responsive' ) ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_html( get_the_author() )
			);
		}

		if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( esc_html__( 'Leave a comment', 'catch-responsive' ), esc_html__( '1 Comment', 'catch-responsive' ), esc_html__( '% Comments', 'catch-responsive' ) );
			echo '</span>';
		}

		edit_post_link( esc_html__( 'Edit', 'catch-responsive' ), '<span class="edit-link">', '</span>' );

		echo '</p><!-- .entry-meta -->';
	}
endif; //catchresponsive_entry_meta


if ( ! function_exists( 'catchresponsive_tag_category' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_tag_category() {
		echo '<p class="entry-meta">';

		if ( 'post' == get_post_type() ) {
			$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'catch-responsive' ) );
			if ( $categories_list && catchresponsive_categorized_blog() ) {
				printf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="screen-reader-text">Categories</span>', 'Used before category names.', 'catch-responsive' ) ),
					$categories_list
				);
			}

			$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'catch-responsive' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="screen-reader-text">Tags</span>', 'Used before tag names.', 'catch-responsive' ) ),
					$tags_list
				);
			}
		}

		echo '</p><!-- .entry-meta -->';
	}
endif; //catchresponsive_tag_category


if ( ! function_exists( 'catchresponsive_categorized_blog' ) ) :
	/**
	 * Returns true if a blog has more than 1 category
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
			// Create an array of all the categories that are attached to posts
			$all_the_cool_cats = get_categories( array(
				'hide_empty' => 1,
			) );

			// Count the number of categories that are attached to the posts
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'all_the_cool_cats', $all_the_cool_cats );
		}

		if ( '1' != $all_the_cool_cats ) {
			// This blog has more than 1 category so catchresponsive_categorized_blog should return true
			return true;
		} else {
			// This blog has only 1 category so catchresponsive_categorized_blog should return false
			return false;
		}
	}
endif; //catchresponsive_categorized_blog


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'catchresponsive_page_menu_args' );


/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'catchresponsive_enhanced_image_navigation', 10, 2 );


/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'footer-1' ) )
		$count++;

	if ( is_active_sidebar( 'footer-2' ) )
		$count++;

	if ( is_active_sidebar( 'footer-3' ) )
		$count++;

	if ( is_active_sidebar( 'footer-4' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
		case '4':
			$class = 'four';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}


if ( ! function_exists( 'catchresponsive_excerpt_length' ) ) :
	/**
	 * Sets the post excerpt length to n words.
	 *
	 * function tied to the excerpt_length filter hook.
	 * @uses filter excerpt_length
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_excerpt_length( $length ) {
		// Getting data from Customizer Options
		$options	= catchresponsive_get_theme_options();
		$length	= $options['excerpt_length'];
		return $length;
	}
endif; //catchresponsive_excerpt_length
add_filter( 'excerpt_length', 'catchresponsive_excerpt_length' );


if ( ! function_exists( 'catchresponsive_continue_reading' ) ) :
	/**
	 * Returns a "Custom Continue Reading" link for excerpts
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_continue_reading() {
		// Getting data from Customizer Options
		$options		=	catchresponsive_get_theme_options();
		$more_tag_text	= $options['excerpt_more_text'];

		return ' <a class="more-link" href="'. esc_url( get_permalink() ) . '">' .  $more_tag_text . '</a>';
	}
endif; //catchresponsive_continue_reading
add_filter( 'excerpt_more', 'catchresponsive_continue_reading' );


if ( ! function_exists( 'catchresponsive_excerpt_more' ) ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with catchresponsive_continue_reading().
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_excerpt_more( $more ) {
		return catchresponsive_continue_reading();
	}
endif; //catchresponsive_excerpt_more
add_filter( 'excerpt_more', 'catchresponsive_excerpt_more' );


if ( ! function_exists( 'catchresponsive_custom_excerpt' ) ) :
	/**
	 * Adds Continue Reading link to more tag excerpts.
	 *
	 * function tied to the get_the_excerpt filter hook.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_custom_excerpt( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$output .= catchresponsive_continue_reading();
		}
		return $output;
	}
endif; //catchresponsive_custom_excerpt
add_filter( 'get_the_excerpt', 'catchresponsive_custom_excerpt' );


if ( ! function_exists( 'catchresponsive_more_link' ) ) :
	/**
	 * Replacing Continue Reading link to the_content more.
	 *
	 * function tied to the the_content_more_link filter hook.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_more_link( $more_link, $more_link_text ) {
	 	$options		=	catchresponsive_get_theme_options();
		$more_tag_text	= $options['excerpt_more_text'];

		return str_replace( $more_link_text, $more_tag_text, $more_link );
	}
endif; //catchresponsive_more_link
add_filter( 'the_content_more_link', 'catchresponsive_more_link', 10, 2 );


if ( ! function_exists( 'catchresponsive_body_classes' ) ) :
	/**
	 * Adds Catchresponsive layout classes to the array of body classes.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_body_classes( $classes ) {
		$options = catchresponsive_get_theme_options();

		// Adds a class of group-blog to blogs with more than 1 published author
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		$layout = catchresponsive_get_theme_layout();

		switch ( $layout ) {
			case 'left-sidebar':
				$classes[] = 'two-columns content-right';
			break;

			case 'right-sidebar':
				$classes[] = 'two-columns content-left';
			break;

			case 'no-sidebar':
				$classes[] = 'no-sidebar content-width';
			break;

			case 'no-sidebar-one-column':
				$classes[] = 'no-sidebar one-column';
			break;

			case 'no-sidebar-full-width':
				$classes[] = 'no-sidebar full-width';
			break;
		}

		if ( "" != $options['content_layout'] ) {
			$classes[] = $options['content_layout'];
		}

		//Count number of menus avaliable and set class accordingly
		$mobile_menu_count = 1; // For primary menu

		if ( has_nav_menu( 'secondary' ) ) {
			$mobile_menu_count++;
		}

		if ( has_nav_menu( 'header-right' ) ) {
			$mobile_menu_count++;
		}

		switch ( $mobile_menu_count ) {
			case 1:
				$classes[] = 'mobile-menu-one';
				break;

			case 2:
				$classes[] = 'mobile-menu-two';
				break;

			case 3:
				$classes[] = 'mobile-menu-three';
				break;
		}

		$classes 	= apply_filters( 'catchresponsive_body_classes', $classes );

		return $classes;
	}
endif; //catchresponsive_body_classes
add_filter( 'body_class', 'catchresponsive_body_classes' );


if ( ! function_exists( 'catchresponsive_get_theme_layout' ) ) :
	/**
	 * Returns Theme Layout prioritizing the meta box layouts
	 *
	 * @uses  get_theme_mod
	 *
	 * @action wp_head
	 *
	 * @since Catch Responsive 3.8.1
	 */
	function catchresponsive_get_theme_layout() {
		$id = '';

		global $post, $wp_query;

	    // Front page displays in Reading Settings
		$page_on_front  = get_option('page_on_front') ;
		$page_for_posts = get_option('page_for_posts');

		// Get Page ID outside Loop
		$page_id = $wp_query->get_queried_object_id();

		// Blog Page or Front Page setting in Reading Settings
		if ( $page_id == $page_for_posts || $page_id == $page_on_front ) {
	        $id = $page_id;
	    }
	    elseif ( is_singular() ) {
	 		if ( is_attachment() ) {
				$id = $post->post_parent;
			}
			else {
				$id = $post->ID;
			}
		}

		//Get appropriate metabox value of layout
		if ( '' != $id ) {
			$layout = get_post_meta( $id, 'catchresponsive-layout-option', true );
		}
		else {
			$layout = 'default';
		}

		//Load options data
		$options = catchresponsive_get_theme_options();

		//check empty and load default
		if ( empty( $layout ) || 'default' == $layout ) {
			$layout = $options['theme_layout'];
		}

		return $layout;
	}
endif; //catchresponsive_get_theme_layout


if ( ! function_exists( 'catchresponsive_archive_content_image' ) ) :
	/**
	 * Template for Featured Image in Archive Content
	 *
	 * To override this in a child theme
	 * simply create your own catchresponsive_archive_content_image(), and that function will be used instead.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_archive_content_image() {
		$options = catchresponsive_get_theme_options();

		$featured_image = $options['content_layout'];

		if ( has_post_thumbnail() && 'excerpt-image-left' == $featured_image ) {
		?>
			<figure class="featured-image">
	            <a rel="bookmark" href="<?php the_permalink(); ?>">
	                <?php
	                	the_post_thumbnail( 'catchresponsive-square' );
					?>
				</a>
	        </figure>
	   	<?php
		}
	}
endif; //catchresponsive_archive_content_image
add_action( 'catchresponsive_before_entry_container', 'catchresponsive_archive_content_image', 10 );


if ( ! function_exists( 'catchresponsive_single_content_image' ) ) :
	/**
	 * Template for Featured Image in Single Post
	 *
	 * To override this in a child theme
	 * simply create your own catchresponsive_single_content_image(), and that function will be used instead.
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_single_content_image() {
		global $post, $wp_query;

		// Getting data from Theme Options
	   	$options = catchresponsive_get_theme_options();

		$featured_image = $options['single_post_image_layout'];

		// Get Page ID outside Loop
		$page_id = $wp_query->get_queried_object_id();

		if ( $post ) {
	 		if ( is_attachment() ) {
				$parent = $post->post_parent;
				$individual_featured_image = get_post_meta( $parent,'catchresponsive-featured-image', true );
			} else {
				$individual_featured_image = get_post_meta( $page_id,'catchresponsive-featured-image', true );
			}
		}

		if ( empty( $individual_featured_image ) || ( !is_page() && !is_single() ) ) {
			$individual_featured_image = 'default';
		}

		if ( ( 'disable' == $individual_featured_image || '' == get_the_post_thumbnail() || ( $individual_featured_image=='default' && 'disabled' == $featured_image) ) ) {
			echo '<!-- Page/Post Single Image Disabled or No Image set in Post Thumbnail -->';
			return false;
		}
		else {
			$class = '';

			if ( 'default' == $individual_featured_image ) {
				$class = $featured_image;
			}
			else {
				$class = 'from-metabox ' . $individual_featured_image;
			}

			?>
			<figure class="featured-image <?php echo $class; ?>">
                <?php
				if ( 'featured' == $individual_featured_image || ( $individual_featured_image=='default' && 'featured' == $featured_image ) ) {
					the_post_thumbnail( 'catchresponsive-featured' );
				}
				else {
					the_post_thumbnail( 'full' );
				} ?>
	        </figure>
	   	<?php
		}
	}
endif; //catchresponsive_single_content_image
add_action( 'catchresponsive_before_post_container', 'catchresponsive_single_content_image', 10 );
add_action( 'catchresponsive_before_page_container', 'catchresponsive_single_content_image', 10 );


if ( ! function_exists( 'catchresponsive_get_comment_section' ) ) :
	/**
	 * Comment Section
	 *
	 * @display comments_template
	 * @action catchresponsive_comment_section
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_get_comment_section() {
		if ( comments_open() || '0' != get_comments_number() )
			comments_template();
	}
endif;
add_action( 'catchresponsive_comment_section', 'catchresponsive_get_comment_section', 10 );


if ( ! function_exists( 'catchresponsive_promotion_headline' ) ) :
	/**
	 * Template for Promotion Headline
	 *
	 * To override this in a child theme
	 * simply create your own catchresponsive_promotion_headline(), and that function will be used instead.
	 *
	 * @uses catchresponsive_before_main action to add it in the header
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_promotion_headline() {
		//delete_transient( 'catchresponsive_promotion_headline' );

		global $post, $wp_query;
	   	$options 	= catchresponsive_get_theme_options();

		$promotion_headline 		= $options['promotion_headline'];
		$promotion_subheadline 		= $options['promotion_subheadline'];
		$promotion_headline_button 	= $options['promotion_headline_button'];
		$promotion_headline_target 	= $options['promotion_headline_target'];
		$enablepromotion 			= $options['promotion_headline_option'];

		//support qTranslate plugin
		if ( function_exists( 'qtrans_convertURL' ) ) {
			$promotion_headline_url = qtrans_convertURL($options['promotion_headline_url']);
		}
		else {
			$promotion_headline_url = $options['promotion_headline_url'];
		}

		// Front page displays in Reading Settings
		$page_on_front = get_option( 'page_on_front' ) ;
		$page_for_posts = get_option('page_for_posts');

		// Get Page ID outside Loop
		$page_id = $wp_query->get_queried_object_id();

		 if ( ( "" != $promotion_headline || "" != $promotion_subheadline || "" != $promotion_headline_url ) && ( 'entire-site' == $enablepromotion || ( ( is_front_page() || ( is_home() && $page_for_posts != $page_id ) ) && 'homepage' == $enablepromotion ) ) ) {

			if ( !$catchresponsive_promotion_headline = get_transient( 'catchresponsive_promotion_headline' ) ) {

				echo '<!-- refreshing cache -->';

				$catchresponsive_promotion_headline = '
				<div id="promotion-message">
					<div class="wrapper">
						<div class="section left">';

						if ( "" != $promotion_headline ) {
							$catchresponsive_promotion_headline .= '<h2>' . $promotion_headline . '</h2>';
						}

						if ( "" != $promotion_subheadline ) {
							$catchresponsive_promotion_headline .= '<p>' . $promotion_subheadline . '</p>';
						}

						$catchresponsive_promotion_headline .= '
						</div><!-- .section.left -->';

						if ( "" != $promotion_headline_url ) {
							if ( "1" == $promotion_headline_target ) {
								$headlinetarget = '_blank';
							}
							else {
								$headlinetarget = '_self';
							}

							$catchresponsive_promotion_headline .= '
							<div class="section right">
								<a class="promotion-button" href="' . esc_url( $promotion_headline_url ) . '" target="' . $headlinetarget . '">' . esc_html( $promotion_headline_button ) . '
								</a>
							</div><!-- .section.right -->';
						}

				$catchresponsive_promotion_headline .= '
					</div><!-- .wrapper -->
				</div><!-- #promotion-message -->';

				set_transient( 'catchresponsive_promotion_headline', $catchresponsive_promotion_headline, 86940 );
			}
			echo $catchresponsive_promotion_headline;
		 }
	}
endif; // catchresponsive_promotion_featured_content
add_action( 'catchresponsive_before_content', 'catchresponsive_promotion_headline', 30 );


/**
 * Footer Text
 *
 * @get footer text from theme options and display them accordingly
 * @display footer_text
 * @action catchresponsive_footer
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_footer_content() {
	//catchresponsive_flush_transients();
	if ( ( !$catchresponsive_footer_content = get_transient( 'catchresponsive_footer_content' ) ) ) {
		echo '<!-- refreshing cache -->';

		$catchresponsive_content = catchresponsive_get_content();

		$catchresponsive_footer_content =  '
    	<div id="site-generator">
    		<div class="wrapper">
    			<div id="footer-content" class="copyright">'
    				. $catchresponsive_content['left'] . ' &#124; ' . $catchresponsive_content['right'] .
    			'</div>
			</div><!-- .wrapper -->
		</div><!-- #site-generator -->';

    	set_transient( 'catchresponsive_footer_content', $catchresponsive_footer_content, 86940 );
    }

    echo $catchresponsive_footer_content;
}
add_action( 'catchresponsive_footer', 'catchresponsive_footer_content', 100 );


/**
 * Return the first image in a post. Works inside a loop.
 * @param [integer] $post_id [Post or page id]
 * @param [string/array] $size Image size. Either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).
 * @param [string/array] $attr Query string or array of attributes.
 * @return [string] image html
 *
 * @since Catch Responsive 1.0
 */

function catchresponsive_get_first_image( $postID, $size, $attr ) {
	ob_start();

	ob_end_clean();

	$image 	= '';

	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field('post_content', $postID ) , $matches);

	if ( isset( $matches [1] [0] ) ) {
		//Get first image
		$first_img = $matches [1] [0];

		return '<img class="pngfix wp-post-image" src="'. esc_url( $first_img ) .'">';
	}
	else {
		return false;
	}
}


if ( ! function_exists( 'catchresponsive_scrollup' ) ) {
	/**
	 * This function loads Scroll Up Navigation
	 *
	 * @action catchresponsive_footer action
	 * @uses set_transient and delete_transient
	 */
	function catchresponsive_scrollup() {
		//catchresponsive_flush_transients();
		if ( !$catchresponsive_scrollup = get_transient( 'catchresponsive_scrollup' ) ) {

			// get the data value from theme options
			$options = catchresponsive_get_theme_options();
			echo '<!-- refreshing cache -->';

			//site stats, analytics header code
			if ( ! $options['disable_scrollup'] ) {
				$catchresponsive_scrollup =  '<a href="#masthead" id="scrollup" class="genericon"><span class="screen-reader-text">' . __( 'Scroll Up', 'catch-responsive' ) . '</span></a>' ;
			}

			set_transient( 'catchresponsive_scrollup', $catchresponsive_scrollup, 86940 );
		}
		echo $catchresponsive_scrollup;
	}
}
add_action( 'catchresponsive_after', 'catchresponsive_scrollup', 10 );


if ( ! function_exists( 'catchresponsive_page_post_meta' ) ) :
	/**
	 * Post/Page Meta for Google Structure Data
	 */
	function catchresponsive_page_post_meta() {
		$catchresponsive_author_url = esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) );

		$catchresponsive_page_post_meta = '<span class="post-time">' . __( 'Posted on', 'catch-responsive' ) . ' <time class="entry-date updated" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" pubdate>' . esc_html( get_the_date() ) . '</time></span>';
	    $catchresponsive_page_post_meta .= '<span class="post-author">' . __( 'By', 'catch-responsive' ) . ' <span class="author vcard"><a class="url fn n" href="' . esc_url( $catchresponsive_author_url ) . '" title="View all posts by ' . esc_attr( get_the_author() ) . '" rel="author">' .get_the_author() . '</a></span>';

		return $catchresponsive_page_post_meta;
	}
endif; //catchresponsive_page_post_meta


if ( ! function_exists( 'catchresponsive_alter_home' ) ) :
	/**
	 * Alter the query for the main loop in homepage
	 *
	 * @action pre_get_posts action
	 */
	function catchresponsive_alter_home( $query ){
		if ( $query->is_main_query() && $query->is_home() ) {
			$options = catchresponsive_get_theme_options();

		    $cats = $options['front_page_category'];

			if ( is_array( $cats ) && !in_array( '0', $cats ) ) {
				$query->query_vars['category__in'] = $cats;
			}
		}
	}
endif; //catchresponsive_alter_home
add_action( 'pre_get_posts','catchresponsive_alter_home' );


if ( ! function_exists( 'catchresponsive_post_navigation' ) ) :
	/**
	 * Displays Single post Navigation
	 *
	 * @uses  the_post_navigation
	 *
	 * @action catchresponsive_after_post
	 *
	 * @since Catch Responsive 1.6
	 */
	function catchresponsive_post_navigation() {
		// Previous/next post navigation.
		if ( function_exists( 'the_post_navigation' ) ) {
			the_post_navigation( array(
				'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next &rarr;', 'catch-responsive' ) . '</span> ' .
					'<span class="screen-reader-text">' . __( 'Next post:', 'catch-responsive' ) . '</span> ' .
					'<span class="post-title">%title</span>',
				'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( '&larr; Previous', 'catch-responsive' ) . '</span> ' .
					'<span class="screen-reader-text">' . __( 'Previous post:', 'catch-responsive' ) . '</span> ' .
					'<span class="post-title">%title</span>',
			) );
		}
		else {
			// Don't print empty markup if there's nowhere to navigate.
			$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
			$next     = get_adjacent_post( false, '', false );

			if ( ! $next && ! $previous ) {
				return;
			}
			?>
			<nav class="navigation post-navigation" role="navigation">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'catch-responsive' ); ?></h2>
				<div class="nav-links">
					<?php
						previous_post_link( '<div class="nav-previous">%link</div>', '%title' );
						next_post_link( '<div class="nav-next">%link</div>', '%title' );
					?>
				</div><!-- .nav-links -->
			</nav><!-- .navigation -->
		<?php
		}
	}
endif; //catchresponsive_post_navigation
add_action( 'catchresponsive_after_post', 'catchresponsive_post_navigation', 10 );


/**
 * Migrate Logo to New WordPress core Custom Logo
 *
 *
 * Runs if version number saved in theme_mod "logo_version" doesn't match current theme version.
 */
function catchresponsive_logo_migrate() {
	$ver = get_theme_mod( 'logo_version', false );

	// Return if update has already been run
	if ( version_compare( $ver, '2.8' ) >= 0 ) {
		return;
	}

	/**
	 * Get Theme Options Values
	 */
	$options 	= catchresponsive_get_theme_options();

	// If a logo has been set previously, update to use logo feature introduced in WordPress 4.5
	if ( function_exists( 'the_custom_logo' ) ) {
		if ( isset( $options['logo'] ) && '' != $options['logo'] ) {
			// Since previous logo was stored a URL, convert it to an attachment ID
			$logo = attachment_url_to_postid( $options['logo'] );

			if ( is_int( $logo ) ) {
				set_theme_mod( 'custom_logo', $logo );
			}
		}

  		// Update to match logo_version so that script is not executed continously
		set_theme_mod( 'logo_version', '2.8' );
	}

}
add_action( 'after_setup_theme', 'catchresponsive_logo_migrate' );


/**
 * Migrate Custom CSS to WordPress core Custom CSS
 *
 * Runs if version number saved in theme_mod "custom_css_version" doesn't match current theme version.
 */
function catchresponsive_custom_css_migrate(){
	$ver = get_theme_mod( 'custom_css_version', false );

	// Return if update has already been run
	if ( version_compare( $ver, '4.7' ) >= 0 ) {
		return;
	}

	if ( function_exists( 'wp_update_custom_css_post' ) ) {
	    // Migrate any existing theme CSS to the core option added in WordPress 4.7.

	    /**
		 * Get Theme Options Values
		 */
	    $options = catchresponsive_get_theme_options();

	    if ( '' != $options['custom_css'] ) {
			$core_css = wp_get_custom_css(); // Preserve any CSS already added to the core option.
			$return   = wp_update_custom_css_post( $core_css . $options['custom_css'] );
	        if ( ! is_wp_error( $return ) ) {
	            // Remove the old theme_mod, so that the CSS is stored in only one place moving forward.
	            unset( $options['custom_css'] );
	            set_theme_mod( 'catchresponsive_theme_options', $options );

	            // Update to match custom_css_version so that script is not executed continously
				set_theme_mod( 'custom_css_version', '4.7' );
	        }
	    }
	}
}
add_action( 'after_setup_theme', 'catchresponsive_custom_css_migrate' );

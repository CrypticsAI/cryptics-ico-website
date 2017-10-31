<?php
/**
 * The template for Managing Theme Structure
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


if ( ! function_exists( 'catchresponsive_doctype' ) ) :
	/**
	 * Doctype Declaration
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_doctype() {
		echo '<!DOCTYPE html>' . PHP_EOL;
		echo '<html ' . get_language_attributes() . '>';
	}
endif;
add_action( 'catchresponsive_doctype', 'catchresponsive_doctype', 10 );


if ( ! function_exists( 'catchresponsive_head' ) ) :
	/**
	 * Header Codes
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_head() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}
endif;
add_action( 'catchresponsive_before_wp_head', 'catchresponsive_head', 10 );


if ( ! function_exists( 'catchresponsive_doctype_start' ) ) :
	/**
	 * Start div id #page
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_page_start() {
		?>
		<div id="page" class="hfeed site">
		<?php
	}
endif;
add_action( 'catchresponsive_header', 'catchresponsive_page_start', 10 );


if ( ! function_exists( 'catchresponsive_page_end' ) ) :
	/**
	 * End div id #page
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_page_end() {
		?>
		</div><!-- #page -->
		<?php
	}
endif;
add_action( 'catchresponsive_footer', 'catchresponsive_page_end', 200 );


if ( ! function_exists( 'catchresponsive_header_start' ) ) :
	/**
	 * Start Header id #masthead and class .wrapper
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_header_start() {
		?>
		<header id="masthead" role="banner">
    		<div class="wrapper">
		<?php
	}
endif;
add_action( 'catchresponsive_header', 'catchresponsive_header_start', 20 );


if ( ! function_exists( 'catchresponsive_header_end' ) ) :
	/**
	 * End Header id #masthead and class .wrapper
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_header_end() {
		?>
			</div><!-- .wrapper -->
		</header><!-- #masthead -->
		<?php
	}
endif;
add_action( 'catchresponsive_header', 'catchresponsive_header_end', 100 );


if ( ! function_exists( 'catchresponsive_content_start' ) ) :
	/**
	 * Start div id #content and class .wrapper
	 *
	 * @since Catch Responsive 1.0
	 *
	 */
	function catchresponsive_content_start() {
		?>
		<div id="content" class="site-content">
			<div class="wrapper">
	<?php
	}
endif;
add_action('catchresponsive_content', 'catchresponsive_content_start', 10 );

if ( ! function_exists( 'catchresponsive_content_end' ) ) :
	/**
	 * End div id #content and class .wrapper
	 *
	 * @since Catch Responsive 1.0
	 */
	function catchresponsive_content_end() {
		?>
			</div><!-- .wrapper -->
	    </div><!-- #content -->
		<?php
	}

endif;
add_action( 'catchresponsive_after_content', 'catchresponsive_content_end', 30 );


if ( ! function_exists( 'catchresponsive_footer_content_start' ) ) :
/**
 * Start footer id #colophon
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_footer_content_start() {
	?>
	<footer id="colophon" class="site-footer" role="contentinfo">
    <?php
}
endif;
add_action( 'catchresponsive_footer', 'catchresponsive_footer_content_start', 30 );


if ( ! function_exists( 'catchresponsive_footer_sidebar' ) ) :
/**
 * Footer Sidebar
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_footer_sidebar() {
	get_sidebar( 'footer' );
}
endif;
add_action( 'catchresponsive_footer', 'catchresponsive_footer_sidebar', 40 );


if ( ! function_exists( 'catchresponsive_footer_content_end' ) ) :
/**
 * End footer id #colophon
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_footer_content_end() {
	?>
	</footer><!-- #colophon -->
	<?php
}
endif;
add_action( 'catchresponsive_footer', 'catchresponsive_footer_content_end', 110 );


if ( ! function_exists( 'catchresponsive_header_right' ) ) :
/**
 * Shows Header Right Social Icon
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_header_right() { ?>
	<aside class="sidebar sidebar-header-right widget-area">
		<section class="widget widget_search" id="header-right-search">
			<div class="widget-wrap">
				<?php echo get_search_form(); ?>
			</div>
		</section>
		<?php if ( '' != ( $catchresponsive_social_icons = catchresponsive_get_social_icons() ) ) { ?>
			<section class="widget widget_catchresponsive_social_icons" id="header-right-social-icons">
				<div class="widget-wrap">
					<?php echo $catchresponsive_social_icons; ?>
				</div><!-- .widget-wrap -->
			</section><!-- #header-right-social-icons -->
		<?php
		} ?>
	</aside><!-- .sidebar .header-sidebar .widget-area -->
<?php
}
endif;
add_action( 'catchresponsive_header', 'catchresponsive_header_right', 60 );

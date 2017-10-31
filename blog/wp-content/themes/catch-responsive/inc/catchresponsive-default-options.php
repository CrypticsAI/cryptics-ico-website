<?php
/**
 * Implement Default Theme/Customizer Options
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
 * Returns the default options for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_get_default_theme_options() {

	$default_theme_options = array(
		//Site Title an Tagline
		'logo'												=> get_template_directory_uri() . '/images/headers/logo.png',
		'logo_alt_text' 									=> '',
		'logo_disable'										=> 1,
		'move_title_tagline'								=> 0,

		//Layout
		'theme_layout' 										=> 'right-sidebar',
		'content_layout'									=> 'excerpt-image-left',
		'single_post_image_layout'							=> 'disabled',

		//Header Image
		'enable_featured_header_image'						=> 'disabled',
		'featured_image_size'								=> 'full',
		'featured_header_image_url'							=> '',
		'featured_header_image_alt'							=> '',
		'featured_header_image_base'						=> 0,


		//Breadcrumb Options
		'breadcumb_option'									=> 0,
		'breadcumb_onhomepage'								=> 0,
		'breadcumb_seperator'								=> '&raquo;',

		//Custom CSS
		'custom_css'										=> '',

		//Scrollup Options
		'disable_scrollup'                                  => 0,

		//Excerpt Options
		'excerpt_length'									=> '55',
		'excerpt_more_text'									=> __( 'Read More ...', 'catch-responsive' ),

		//Homepage / Frontpage Settings
		'front_page_category'								=> '0',

		//Pagination Options
		'pagination_type'									=> 'default',

		//Promotion Headline Options
		'promotion_headline_option'							=> 'disabled',
		'promotion_headline_type'							=> 'promotion-headline-content',
		'promotion_headline'								=> __( 'Catch Responsive is a Premium Responsive WordPress Theme', 'catch-responsive' ),
		'promotion_subheadline'								=> __( 'This is promotion headline. You can edit this from Appearance -> Customize -> Theme Options -> Promotion Headline Options', 'catch-responsive' ),
		'promotion_headline_button'							=> __( 'Reviews', 'catch-responsive' ),
		'promotion_headline_url'							=> esc_url( 'http://wordpress.org/support/view/theme-reviews/catch-responsive' ),
		'promotion_headline_target'							=> 1,

		//Search Options
		'search_text'										=> __( 'Search...', 'catch-responsive' ),

		//Basic Color Options
		'color_scheme' 										=> 'light',
		'background_color'									=> '#f9f9f9',
		'header_textcolor'									=> '#111111',
		'mobile_menu_color_scheme'							=> 'light',

		//Featured Content Options
		'featured_content_option'							=> 'homepage',
		'featured_content_layout'							=> 'layout-three',
		'featured_content_position'							=> 0,
		'featured_content_headline'							=> __( 'Featured Content', 'catch-responsive' ),
		'featured_content_subheadline'						=> __( 'Here you can showcase the x number of Featured Content.', 'catch-responsive' ),
		'featured_content_type'								=> 'demo-featured-content',
		'featured_content_number'							=> '3',
		'featured_content_show'								=> 'excerpt',

		//Featured Slider Options
		'featured_slider_option'							=> 'homepage',
		'featured_slider_image_loader'						=> 'true',
		'featured_slide_transition_effect'					=> 'fadeout',
		'featured_slide_transition_delay'					=> '4',
		'featured_slide_transition_length'					=> '1',
		'featured_slider_type'								=> 'demo-featured-slider',
		'featured_slide_number'								=> '4',

		//Reset all settings
		'reset_all_settings'								=> 0,
	);

	return apply_filters( 'catchresponsive_default_theme_options', $default_theme_options );
}


/**
 * Returns an array of color schemes registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_color_schemes() {
	$color_scheme_options = array(
		'light' => __( 'Light', 'catch-responsive' ),
		'dark'  => __( 'Dark', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_color_schemes', $color_scheme_options );
}


/**
 * Returns an array of layout options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_layouts() {
	$layout_options = array(
		'left-sidebar' 	=> __( 'Primary Sidebar, Content', 'catch-responsive' ),
		'right-sidebar' => __( 'Content, Primary Sidebar', 'catch-responsive' ),
		'no-sidebar'	=> __( 'No Sidebar ( Content Width )', 'catch-responsive' ),
	);
	return apply_filters( 'catchresponsive_layouts', $layout_options );
}


/**
 * Returns an array of content layout options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_get_archive_content_layout() {
	$layout_options = array(
		'excerpt-image-left' => __( 'Show Excerpt', 'catch-responsive' ),
		'full-content'       => __( 'Show Full Content (No Featured Image)', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_get_archive_content_layout', $layout_options );
}


/**
 * Returns an array of feature header enable options
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_enable_featured_header_image_options() {
	$enable_featured_header_image_options = array(
		'homepage'               => __( 'Homepage / Frontpage', 'catch-responsive' ),
		'exclude-home'           => __( 'Excluding Homepage', 'catch-responsive' ),
		'exclude-home-page-post' => __( 'Excluding Homepage, Page/Post Featured Image', 'catch-responsive' ),
		'entire-site'            => __( 'Entire Site', 'catch-responsive' ),
		'entire-site-page-post'  => __( 'Entire Site, Page/Post Featured Image', 'catch-responsive' ),
		'pages-posts'            => __( 'Pages and Posts', 'catch-responsive' ),
		'disabled'               => __( 'Disabled', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_enable_featured_header_image_options', $enable_featured_header_image_options );
}


/**
 * Returns an array of feature image size
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_image_size_options() {
	$featured_image_size_options = array(
		'full'   => __( 'Full Image', 'catch-responsive' ),
		'large'  => __( 'Large Image', 'catch-responsive' ),
		'slider' => __( 'Slider Image', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_image_size_options', $featured_image_size_options );
}


/**
 * Returns an array of content and slider layout options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_slider_content_options() {
	$featured_slider_content_options = array(
		'homepage' 		=> __( 'Homepage / Frontpage', 'catch-responsive' ),
		'entire-site' 	=> __( 'Entire Site', 'catch-responsive' ),
		'disabled'		=> __( 'Disabled', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_slider_content_options', $featured_slider_content_options );
}


/**
 * Returns an array of feature content types registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_content_types() {
	$featured_content_types = array(
		'demo-featured-content' => __( 'Demo Featured Content', 'catch-responsive' ),
		'featured-page-content' => __( 'Featured Page Content', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_content_types', $featured_content_types );
}


/**
 * Returns an array of featured content options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_content_layout_options() {
	$featured_content_layout_option = array(
		'layout-three' => __( '3 columns', 'catch-responsive' ),
		'layout-four'  => __( '4 columns', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_content_layout_options', $featured_content_layout_option );
}


/**
 * Returns an array of featured content show registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_content_show() {
	$featured_content_show_option = array(
		'excerpt' 		=> __( 'Show Excerpt', 'catch-responsive' ),
		'full-content' 	=> __( 'Show Full Content', 'catch-responsive' ),
		'hide-content' 	=> __( 'Hide Content', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_content_show', $featured_content_show_option );
}


/**
 * Returns an array of feature slider types registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_slider_types() {
	$featured_slider_types = array(
		'demo-featured-slider' => __( 'Demo Featured Slider', 'catch-responsive' ),
		'featured-page-slider' => __( 'Featured Page Slider', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_slider_types', $featured_slider_types );
}


/**
 * Returns an array of feature slider transition effects
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_slide_transition_effects() {
	$featured_slide_transition_effects = array(
		'fade' 		=> __( 'Fade', 'catch-responsive' ),
		'fadeout' 	=> __( 'Fade Out', 'catch-responsive' ),
		'none' 		=> __( 'None', 'catch-responsive' ),
		'scrollHorz'=> __( 'Scroll Horizontal', 'catch-responsive' ),
		'scrollVert'=> __( 'Scroll Vertical', 'catch-responsive' ),
		'flipHorz'	=> __( 'Flip Horizontal', 'catch-responsive' ),
		'flipVert'	=> __( 'Flip Vertical', 'catch-responsive' ),
		'tileSlide'	=> __( 'Tile Slide', 'catch-responsive' ),
		'tileBlind'	=> __( 'Tile Blind', 'catch-responsive' ),
		'shuffle'	=> __( 'Shuffle', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_featured_slide_transition_effects', $featured_slide_transition_effects );
}


/**
 * Returns an array of featured slider image loader options
 *
 * @since Catch Responsive 2.1
 */
function catchresponsive_featured_slider_image_loader() {
	$color_scheme_options = array(
		'true'  => __( 'True', 'catch-responsive' ),
		'wait'  => __( 'Wait', 'catch-responsive' ),
		'false' => __( 'False', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_color_schemes', $color_scheme_options );
}


/**
 * Returns an array of color schemes registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_get_pagination_types() {
	$pagination_types = array(
		'default'                => __( 'Default(Older Posts/Newer Posts)', 'catch-responsive' ),
		'numeric'                => __( 'Numeric', 'catch-responsive' ),
		'infinite-scroll-click'  => __( 'Infinite Scroll (Click)', 'catch-responsive' ),
		'infinite-scroll-scroll' => __( 'Infinite Scroll (Scroll)', 'catch-responsive' ),
	);

	return apply_filters( 'catchresponsive_get_pagination_types', $pagination_types );
}


/**
 * Returns an array of content featured image size.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_single_post_image_layout_options() {
	$single_post_image_layout_options = array(
		'featured'  => __( 'Featured', 'catch-responsive' ),
		'full-size' => __( 'Full Size', 'catch-responsive' ),
		'disabled'  => __( 'Disabled', 'catch-responsive' ),
	);
	return apply_filters( 'catchresponsive_single_post_image_layout_options', $single_post_image_layout_options );
}


/**
 * Returns list of social icons currently supported
 *
 * @since Catch Responsive 1.0
*/
/**
 * Returns list of social icons currently supported
 *
 * @since Catch Responsive 1.0
*/
function catchresponsive_get_social_icons_list() {
	$catchresponsive_social_icons_list = array(
		'facebook_link'		=> array(
			'genericon_class' 	=> 'facebook-alt',
			'label' 			=> esc_html__( 'Facebook', 'catch-responsive' )
			),
		'twitter_link'		=> array(
			'genericon_class' 	=> 'twitter',
			'label' 			=> esc_html__( 'Twitter', 'catch-responsive' )
			),
		'googleplus_link'	=> array(
			'genericon_class' 	=> 'googleplus-alt',
			'label' 			=> esc_html__( 'Googleplus', 'catch-responsive' )
			),
		'email_link'		=> array(
			'genericon_class' 	=> 'mail',
			'label' 			=> esc_html__( 'Email', 'catch-responsive' )
			),
		'feed_link'			=> array(
			'genericon_class' 	=> 'feed',
			'label' 			=> esc_html__( 'Feed', 'catch-responsive' )
			),
		'wordpress_link'	=> array(
			'genericon_class' 	=> 'wordpress',
			'label' 			=> esc_html__( 'WordPress', 'catch-responsive' )
			),
		'github_link'		=> array(
			'genericon_class' 	=> 'github',
			'label' 			=> esc_html__( 'GitHub', 'catch-responsive' )
			),
		'linkedin_link'		=> array(
			'genericon_class' 	=> 'linkedin',
			'label' 			=> esc_html__( 'LinkedIn', 'catch-responsive' )
			),
		'pinterest_link'	=> array(
			'genericon_class' 	=> 'pinterest',
			'label' 			=> esc_html__( 'Pinterest', 'catch-responsive' )
			),
		'flickr_link'		=> array(
			'genericon_class' 	=> 'flickr',
			'label' 			=> esc_html__( 'Flickr', 'catch-responsive' )
			),
		'vimeo_link'		=> array(
			'genericon_class' 	=> 'vimeo',
			'label' 			=> esc_html__( 'Vimeo', 'catch-responsive' )
			),
		'youtube_link'		=> array(
			'genericon_class' 	=> 'youtube',
			'label' 			=> esc_html__( 'YouTube', 'catch-responsive' )
			),
		'tumblr_link'		=> array(
			'genericon_class' 	=> 'tumblr',
			'label' 			=> esc_html__( 'Tumblr', 'catch-responsive' )
			),
		'instagram_link'	=> array(
			'genericon_class' 	=> 'instagram',
			'label' 			=> esc_html__( 'Instagram', 'catch-responsive' )
			),
		'polldaddy_link'	=> array(
			'genericon_class' 	=> 'polldaddy',
			'label' 			=> esc_html__( 'PollDaddy', 'catch-responsive' )
			),
		'codepen_link'		=> array(
			'genericon_class' 	=> 'codepen',
			'label' 			=> esc_html__( 'CodePen', 'catch-responsive' )
			),
		'path_link'			=> array(
			'genericon_class' 	=> 'path',
			'label' 			=> esc_html__( 'Path', 'catch-responsive' )
			),
		'dribbble_link'		=> array(
			'genericon_class' 	=> 'dribbble',
			'label' 			=> esc_html__( 'Dribbble', 'catch-responsive' )
			),
		'skype_link'		=> array(
			'genericon_class' 	=> 'skype',
			'label' 			=> esc_html__( 'Skype', 'catch-responsive' )
			),
		'digg_link'			=> array(
			'genericon_class' 	=> 'digg',
			'label' 			=> esc_html__( 'Digg', 'catch-responsive' )
			),
		'reddit_link'		=> array(
			'genericon_class' 	=> 'reddit',
			'label' 			=> esc_html__( 'Reddit', 'catch-responsive' )
			),
		'stumbleupon_link'	=> array(
			'genericon_class' 	=> 'stumbleupon',
			'label' 			=> esc_html__( 'Stumbleupon', 'catch-responsive' )
			),
		'pocket_link'		=> array(
			'genericon_class' 	=> 'pocket',
			'label' 			=> esc_html__( 'Pocket', 'catch-responsive' ),
			),
		'dropbox_link'		=> array(
			'genericon_class' 	=> 'dropbox',
			'label' 			=> esc_html__( 'DropBox', 'catch-responsive' ),
			),
		'spotify_link'		=> array(
			'genericon_class' 	=> 'spotify',
			'label' 			=> esc_html__( 'Spotify', 'catch-responsive' ),
			),
		'foursquare_link'	=> array(
			'genericon_class' 	=> 'foursquare',
			'label' 			=> esc_html__( 'Foursquare', 'catch-responsive' ),
			),
		'twitch_link'		=> array(
			'genericon_class' 	=> 'twitch',
			'label' 			=> esc_html__( 'Twitch', 'catch-responsive' ),
			),
		'website_link'		=> array(
			'genericon_class' 	=> 'website',
			'label' 			=> esc_html__( 'Website', 'catch-responsive' ),
			),
		'phone_link'		=> array(
			'genericon_class' 	=> 'phone',
			'label' 			=> esc_html__( 'Phone', 'catch-responsive' ),
			),
		'handset_link'		=> array(
			'genericon_class' 	=> 'handset',
			'label' 			=> esc_html__( 'Handset', 'catch-responsive' ),
			),
		'cart_link'			=> array(
			'genericon_class' 	=> 'cart',
			'label' 			=> esc_html__( 'Cart', 'catch-responsive' ),
			),
		'cloud_link'		=> array(
			'genericon_class' 	=> 'cloud',
			'label' 			=> esc_html__( 'Cloud', 'catch-responsive' ),
			),
		'link_link'		=> array(
			'genericon_class' 	=> 'link',
			'label' 			=> esc_html__( 'Link', 'catch-responsive' ),
			),
	);

	return apply_filters( 'catchresponsive_social_icons_list', $catchresponsive_social_icons_list );
}


/**
 * Returns an array of metabox layout options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_metabox_layouts() {
	$layout_options = array(
		'default' 	=> array(
			'id' 	=> 'catchresponsive-layout-option',
			'value' => 'default',
			'label' => __( 'Default', 'catch-responsive' ),
		),
		'left-sidebar' 	=> array(
			'id' 	=> 'catchresponsive-layout-option',
			'value' => 'left-sidebar',
			'label' => __( 'Primary Sidebar, Content', 'catch-responsive' ),
		),
		'right-sidebar' => array(
			'id' 	=> 'catchresponsive-layout-option',
			'value' => 'right-sidebar',
			'label' => __( 'Content, Primary Sidebar', 'catch-responsive' ),
		),
		'no-sidebar'	=> array(
			'id' 	=> 'catchresponsive-layout-option',
			'value' => 'no-sidebar',
			'label' => __( 'No Sidebar ( Content Width )', 'catch-responsive' ),
		),
	);
	return apply_filters( 'catchresponsive_layouts', $layout_options );
}

/**
 * Returns an array of metabox header featured image options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_metabox_header_featured_image_options() {
	$header_featured_image_options = array(
		'default' => array(
			'id'		=> 'catchresponsive-header-image',
			'value' 	=> 'default',
			'label' 	=> __( 'Default', 'catch-responsive' ),
		),
		'enable' => array(
			'id'		=> 'catchresponsive-header-image',
			'value' 	=> 'enable',
			'label' 	=> __( 'Enable', 'catch-responsive' ),
		),
		'disable' => array(
			'id'		=> 'catchresponsive-header-image',
			'value' 	=> 'disable',
			'label' 	=> __( 'Disable', 'catch-responsive' )
		)
	);
	return apply_filters( 'header_featured_image_options', $header_featured_image_options );
}


/**
 * Returns an array of metabox featured image options registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_metabox_featured_image_options() {
	$featured_image_options = array(
		'default' => array(
			'id'		=> 'catchresponsive-featured-image',
			'value' 	=> 'default',
			'label' 	=> __( 'Default', 'catch-responsive' ),
		),
		'featured' => array(
			'id'		=> 'catchresponsive-featured-image',
			'value' 	=> 'featured',
			'label' 	=> __( 'Featured Image', 'catch-responsive' )
		),
		'full-size' => array(
			'id' => 'catchresponsive-featured-image',
			'value' => 'full-size',
			'label' => __( 'Full Image', 'catch-responsive' )
		),
		'disable' => array(
			'id' => 'catchresponsive-featured-image',
			'value' => 'disable',
			'label' => __( 'Disable Image', 'catch-responsive' )
		)
	);
	return apply_filters( 'featured_image_options', $featured_image_options );
}


/**
 * Returns catchresponsive_contents registered for catchresponsive.
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_get_content() {
	$theme_data = wp_get_theme();

	$catchresponsive_content['left'] 	= sprintf( _x( 'Copyright &copy; %1$s %2$s. All Rights Reserved.', '1: Year, 2: Site Title with home URL', 'catch-responsive' ), esc_attr( date_i18n( __( 'Y', 'catch-responsive' ) ) ), '<a href="'. esc_url( home_url( '/' ) ) .'">'. esc_attr( get_bloginfo( 'name', 'display' ) ) . '</a>' );

	$catchresponsive_content['right']	= esc_attr( $theme_data->get( 'Name') ) . '&nbsp;' . __( 'by', 'catch-responsive' ). '&nbsp;<a target="_blank" href="'. esc_url( $theme_data->get( 'AuthorURI' ) ) .'">'. esc_attr( $theme_data->get( 'Author' ) ) .'</a>';

	return apply_filters( 'catchresponsive_get_content', $catchresponsive_content );
}


/**
 * Returns the default options for Catch Responsive dark theme.
 *
 * @since Catch Responsive 1.8
 */
function catchresponsive_default_dark_color_options() {
	$default_dark_color_options = array(
		//Basic Color Options
		'background_color'	=> '#333333',
		'header_textcolor'	=> '#dddddd',
	);

	return apply_filters( 'catchresponsive_default_dark_color_options', $default_dark_color_options );
}
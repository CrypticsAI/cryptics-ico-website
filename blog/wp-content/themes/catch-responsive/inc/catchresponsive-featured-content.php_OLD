<?php
/**
 * The template for displaying the Featured Content
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


if ( !function_exists( 'catchresponsive_featured_content_display' ) ) :
/**
* Add Featured content.
*
* @uses action hook catchresponsive_before_content.
*
* @since Catch Responsive 1.0
*/
function catchresponsive_featured_content_display() {
	//catchresponsive_flush_transients();

	global $post, $wp_query;

	// get data value from options
	$options 		= catchresponsive_get_theme_options();
	$enablecontent 	= $options['featured_content_option'];
	$contentselect 	= $options['featured_content_type'];

	// Front page displays in Reading Settings
	$page_on_front 	= get_option('page_on_front') ;
	$page_for_posts = get_option('page_for_posts');


	// Get Page ID outside Loop
	$page_id = $wp_query->get_queried_object_id();
	if ( 'entire-site' == $enablecontent || ( ( is_front_page() || ( is_home() && $page_for_posts != $page_id ) ) && 'homepage' == $enablecontent ) ) {
		if ( ( !$catchresponsive_featured_content = get_transient( 'catchresponsive_featured_content_display' ) ) ) {
			$layouts 	 = $options['featured_content_layout'];
			$headline 	 = $options['featured_content_headline'];
			$subheadline = $options['featured_content_subheadline'];

			echo '<!-- refreshing cache -->';

			if ( !empty( $layouts ) ) {
				$classes = $layouts ;
			}

			if ( 'demo-featured-content' == $contentselect ) {
				$classes 		.= ' demo-featured-content' ;
				$headline 		= __( 'Featured Content', 'catch-responsive' );
				$subheadline 	= __( 'Here you can showcase the x number of Featured Content. You can edit this Headline, Subheadline and Feaured Content from "Appearance -> Customize -> Featured Content Options".', 'catch-responsive' );
			}
			elseif ( 'featured-page-content' == $contentselect ) {
				$classes .= ' featured-page-content' ;
			}

			//Check Featured Content Position
			$featured_content_position = $options['featured_content_position'];

			if ( '1' == $featured_content_position ) {
				$classes .= ' border-top' ;
			}

			$catchresponsive_featured_content ='
				<section id="featured-content" class="' . $classes . '">
					<div class="wrapper">';
						if ( !empty( $headline ) || !empty( $subheadline ) ) {
							$catchresponsive_featured_content .='<div class="featured-heading-wrap">';
								if ( !empty( $headline ) ) {
									$catchresponsive_featured_content .='<h2 id="featured-heading" class="entry-title">' . wp_kses_post( $headline ) . '</h2>';
								}
								if ( !empty( $subheadline ) ) {
									$catchresponsive_featured_content .='<p>' . wp_kses_post( $subheadline ) . '</p>';
								}
							$catchresponsive_featured_content .='</div><!-- .featured-heading-wrap -->';
						}
						$catchresponsive_featured_content .='
						<div class="featured-content-wrap">';

							// Select content
							if ( 'demo-featured-content' == $contentselect  && function_exists( 'catchresponsive_demo_content' ) ) {
								$catchresponsive_featured_content .= catchresponsive_demo_content( $options );
							}
							elseif ( 'featured-page-content' == $contentselect && function_exists( 'catchresponsive_page_content' ) ) {
								$catchresponsive_featured_content .= catchresponsive_page_content( $options );
							}

			$catchresponsive_featured_content .='
						</div><!-- .featured-content-wrap -->
					</div><!-- .wrapper -->
				</section><!-- #featured-content -->';
		set_transient( 'catchresponsive_featured_content', $catchresponsive_featured_content, 86940 );
		}
	echo $catchresponsive_featured_content;
	}
}
endif;


if ( ! function_exists( 'catchresponsive_featured_content_display_position' ) ) :
/**
 * Homepage Featured Content Position
 *
 * @action catchresponsive_content, catchresponsive_after_secondary
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_featured_content_display_position() {
	// Getting data from Theme Options
	$options 		= catchresponsive_get_theme_options();

	$featured_content_position = $options['featured_content_position'];

	if ( '1' != $featured_content_position ) {
		add_action( 'catchresponsive_before_content', 'catchresponsive_featured_content_display', 40 );
	} else {
		add_action( 'catchresponsive_after_content', 'catchresponsive_featured_content_display', 40 );
	}

}
endif; // catchresponsive_featured_content_display_position
add_action( 'catchresponsive_before', 'catchresponsive_featured_content_display_position' );


if ( ! function_exists( 'catchresponsive_demo_content' ) ) :
/**
 * This function to display featured posts content
 *
 * @get the data value from customizer options
 *
 * @since Catch Responsive 1.0
 *
 */
function catchresponsive_demo_content( $options ) {
	$catchresponsive_demo_content = '
		<article id="featured-post-1" class="post hentry post-demo">
			<figure class="featured-content-image">
				<img alt="Central Park" class="wp-post-image" src="'.get_template_directory_uri() . '/images/gallery/featured1-350x197.jpg" />
			</figure>
			<div class="entry-container">
				<header class="entry-header">
					<h2 class="entry-title">
						Central Park
					</h2>
				</header>
				<div class="entry-content">
					Central Park is is the most visited urban park in the United States as well as one of the most filmed locations in the world. It was opened in 1857 and is expanded in 843 acres of city-owned land.
				</div>
			</div><!-- .entry-container -->
		</article>

		<article id="featured-post-2" class="post hentry post-demo">
			<figure class="featured-content-image">
				<img alt="Home Office" class="wp-post-image" src="'.get_template_directory_uri() . '/images/gallery/featured2-350x197.jpg" />
			</figure>
			<div class="entry-container">
				<header class="entry-header">
					<h2 class="entry-title">
						Home Office
					</h2>
				</header>
				<div class="entry-content">
					It might be work, but it doesn\'t have to feel like it. All you need is a comfortable desk, nice laptop, home office furniture that keeps things organized, and the right lighting for the job.
				</div>
			</div><!-- .entry-container -->
		</article>

		<article id="featured-post-3" class="post hentry post-demo">
			<figure class="featured-content-image">
				<img alt="Vespa Scooter" class="wp-post-image" src="'.get_template_directory_uri() . '/images/gallery/featured3-350x197.jpg" />
			</figure>
			<div class="entry-container">
				<header class="entry-header">
					<h2 class="entry-title">
						Vespa Scooter
					</h2>
				</header>
				<div class="entry-content">
					The Vespa Scooter has evolved from a single model motor scooter manufactured in the year 1946 by Piaggio & Co. S.p.A. of Pontedera, Italy-to a full line of scooters, today owned by Piaggio.
				</div>
			</div><!-- .entry-container -->
		</article>';

	if ( 'layout-four' == $options['featured_content_layout']) {
		$catchresponsive_demo_content .= '
		<article id="featured-post-4" class="post hentry post-demo">
			<figure class="featured-content-image">
				<img alt="Antique Clock" class="wp-post-image" src="'.get_template_directory_uri() . '/images/gallery/featured4-350x197.jpg" />
			</figure>
			<div class="entry-container">
				<header class="entry-header">
					<h2 class="entry-title">
						Antique Clock
					</h2>
				</header>
				<div class="entry-content">
					Antique clocks increase in value with the rarity of the design, their condition, and appeal in the market place. Many different materials were used in antique clocks.
				</div>
			</div><!-- .entry-container -->
		</article>';
	}

	return $catchresponsive_demo_content;
}
endif; // catchresponsive_demo_content


if ( ! function_exists( 'catchresponsive_page_content' ) ) :
/**
 * This function to display featured page content
 *
 * @param $options: catchresponsive_theme_options from customizer
 *
 * @since Catch Responsive 1.0
 */
function catchresponsive_page_content( $options ) {
	global $post;

	$quantity 					= $options['featured_content_number'];

	$more_link_text				= $options['excerpt_more_text'];

	$show_content	= $options['featured_content_show'];

	$catchresponsive_page_content 	= '';

   	$number_of_page 			= 0; 		// for number of pages

	$page_list					= array();	// list of valid pages ids

	//Get valid pages
	for( $i = 1; $i <= $quantity; $i++ ){
		if ( isset ( $options['featured_content_page_' . $i] ) && $options['featured_content_page_' . $i] > 0 ){
			$number_of_page++;

			$page_list	=	array_merge( $page_list, array( $options['featured_content_page_' . $i] ) );
		}

	}
	if ( !empty( $page_list ) && $number_of_page > 0 ) {
		$loop = new WP_Query( array(
                    'posts_per_page' 		=> $number_of_page,
                    'post__in'       		=> $page_list,
                    'orderby'        		=> 'post__in',
                    'post_type'				=> 'page',
                ));

		$i=0;
		while ( $loop->have_posts()) : $loop->the_post(); $i++;
			$title_attribute = the_title_attribute( array( 'before' => __( 'Permalink to: ', 'catch-responsive' ), 'echo' => false ) );

			$excerpt = get_the_excerpt();

			$catchresponsive_page_content .= '
				<article id="featured-post-' . $i . '" class="post hentry featured-page-content">';
				if ( has_post_thumbnail() ) {
					$catchresponsive_page_content .= '
					<figure class="featured-homepage-image">
						<a href="' . esc_url( get_permalink() ) . '" title="'.the_title( '', '', false ).'">
						'. get_the_post_thumbnail( $post->ID, 'catchresponsive-featured-content', array( 'title' => $title_attribute, 'alt' => $title_attribute, 'class' => 'pngfix' ) ) .'
						</a>
					</figure>';
				}
				else {
					$catchresponsive_first_image = catchresponsive_get_first_image( $post->ID, 'catchresponsive-featured-content', array( 'title' => $title_attribute, 'alt' => $title_attribute, 'class' => 'pngfix' ) );

					if ( '' != $catchresponsive_first_image ) {
						$catchresponsive_page_content .= '
						<figure class="featured-homepage-image">
							<a href="' . esc_url( get_permalink() ) . '" title="'.the_title( '', '', false ).'">
								'. $catchresponsive_first_image .'
							</a>
						</figure>';
					}
				}

				$catchresponsive_page_content .= '
					<div class="entry-container">
						<header class="entry-header">
							<h2 class="entry-title">
								<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . the_title( '','', false ) . '</a>
							</h2>
						</header>';
						if ( 'excerpt' == $show_content ) {
							$catchresponsive_page_content .= '<div class="entry-excerpt"><p>' . $excerpt . '</p></div><!-- .entry-excerpt -->';
						}
						elseif ( 'full-content' == $show_content ) {
							$content = apply_filters( 'the_content', get_the_content() );
							$content = str_replace( ']]>', ']]&gt;', $content );
							$catchresponsive_page_content .= '<div class="entry-content">' . $content . '</div><!-- .entry-content -->';
						}
					$catchresponsive_page_content .= '
					</div><!-- .entry-container -->
				</article><!-- .featured-post-'. $i .' -->';
		endwhile;

		wp_reset_postdata();
	}

	return $catchresponsive_page_content;
}
endif; // catchresponsive_page_content

<?php
/**
 * The Template for displaying all single posts
 *
 * @package Catch Themes
 * @subpackage Catch Responsive
 * @since Catch Responsive 1.0 
 */

get_header(); ?>

	<main id="main" class="site-main" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content', 'single' ); ?>

		<?php 
			/** 
			 * catchresponsive_after_post hook
			 *
			 * @hooked catchresponsive_post_navigation - 10
			 */
			do_action( 'catchresponsive_after_post' ); 
			
			/** 
			 * catchresponsive_comment_section hook
			 *
			 * @hooked catchresponsive_get_comment_section - 10
			 */
			do_action( 'catchresponsive_comment_section' ); 
		?>
	<?php endwhile; // end of the loop. ?>

	</main><!-- #main -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
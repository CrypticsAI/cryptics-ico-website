<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Catch Themes
 * @subpackage Catch Responsive
 * @since Catch Responsive 1.0 
 */

get_header(); ?>

	<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'page' ); ?>

			<?php 
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
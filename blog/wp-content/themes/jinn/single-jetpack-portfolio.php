<?php
/**
 * The Template for displaying all single projects.
 *
 * @package Jinn
 */

get_header(); ?>

<?php if ( get_theme_mod( 'layout_setting' ) === 'sidebar-right' && is_active_sidebar( 'sidebar-custom' ) ) { ?>
    
    <div id="primary" class="content-area small-12 medium-8 columns sidebar-right">
        
<?php } else if ( get_theme_mod( 'layout_setting' ) === 'sidebar-left' && is_active_sidebar( 'sidebar-custom' ) ) { ?>
        
    <div id="primary" class="content-area small-12 medium-8 medium-push-4 columns sidebar-left">
        
<?php } else { ?>   
        
    <div id="primary" class="content-area <?php echo esc_attr( get_theme_mod( 'layout_setting', 'no-sidebar' ) ); ?> small-12 medium-10 medium-push-1 large-8 large-push-2 columns">
        
<?php } ?>

		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'components/features/portfolio/content', 'portfolio-single' ); ?>
                    
                        <?php jinn_post_navigation(); ?>
                        <?php jinn_author_box(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</main>
	</div>
<?php 
// Only get the sidebar if there is a custom one set
if ( is_active_sidebar( 'sidebar-custom' ) ) {
    get_sidebar(); 
}
?>
<?php get_footer(); ?>
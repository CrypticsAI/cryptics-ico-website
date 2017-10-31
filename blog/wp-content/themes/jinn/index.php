<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jinn
 */

get_header(); ?>

<?php if ( is_page_template( 'page-templates/page-sidebar-right.php' ) || get_theme_mod( 'layout_setting' ) === 'sidebar-right' ) { ?>
    
    <div id="primary" class="content-area small-12 medium-8 columns sidebar-right">
        
<?php } else if ( is_page_template( 'page-templates/page-sidebar-left.php' ) || get_theme_mod( 'layout_setting' ) === 'sidebar-left' ) { ?>
        
    <div id="primary" class="content-area small-12 medium-8 medium-push-4 columns sidebar-left">
        
<?php } else if ( is_page_template( 'page-templates/page-no-sidebar.php' ) || get_theme_mod( 'layout_setting' ) === 'no-sidebar' ) { ?>
        
    <div id="primary" class="content-area small-12 medium-10 medium-push-1 large-8 large-push-2 columns no-sidebar">
        
<?php } else if ( is_page_template( 'page-templates/page-full-width.php' ) ) { ?>
        
    <div id="primary" class="content-area medium-12 columns no-sidebar page-full-width">
        
<?php } else { ?>   
        
    <div id="primary" class="content-area <?php echo esc_attr( get_theme_mod( 'layout_setting', 'no-sidebar' ) ); ?> small-12 medium-10 medium-push-1 large-8 large-push-2 columns">
        
<?php } ?>
        
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
			<?php
			endif;

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'components/post/content', get_post_format() );

			endwhile;

                        jinn_paging_nav();

		else :

			get_template_part( 'components/post/content', 'none' );

		endif; ?>

		</main>
	</div><!-- #primary Foundation .columns end -->
<?php
get_sidebar();
get_footer();

<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jinn
 */

?>
<?php if ( ! is_page_template( 'page-templates/frontpage-portfolio.php' ) && ! is_page_template( 'page-templates/page-child-pages.php' ) ) : ?>
<?php if ( '' != get_the_post_thumbnail() ) : ?>
    <div class="index-post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'jinn-featured-image' ); ?>
            </a>
    </div>
<?php endif; ?>
<?php endif; ?>

<?php if( ! is_page_template( 'page-templates/page-child-pages.php' ) ) { ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php } ?>
    
	<header class="entry-header">
            
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            
                <?php if ( ! is_page_template( 'page-templates/frontpage-portfolio.php' ) ) : ?>
                    <?php jinn_breadcrumbs(); ?>
                <?php endif; ?>
            
	</header>
	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jinn' ),
				'after'  => '</div>',
			) );
                        
                        jinn_jetpack_sharing();
		?>
	</div>
    
    <?php 
    // Don't end the article if we want to display child pages too
    // "entry-footer" and </article> are present on that template
    if ( ! is_page_template( 'page-templates/page-child-pages.php' ) && is_user_logged_in() ) : 
    ?>
	<footer class="entry-footer">
		<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					esc_html__( 'Edit %s', 'jinn' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				),
				'<span class="edit-link">',
				'</span>'
			);
		?>
	</footer>
    
    <?php endif; ?>
        
<?php if( ! is_page_template( 'page-templates/page-child-pages.php' ) ) { ?>
    </article><!-- #post-## -->
<?php } ?>
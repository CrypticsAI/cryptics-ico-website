<?php
/**
 * @package Jinn
 */

// Access global variable directly to adjust the content width for portfolio single page
if ( isset( $GLOBALS['content_width'] ) ) {
	$GLOBALS['content_width'] = 1100;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
            <?php if ( '' != get_the_post_thumbnail() ) : ?>
    <div class="author-avatar" style="background: url( <?php echo esc_url( get_the_post_thumbnail_url( $post, 'thumbnail' ) ); ?> )">
                    </div>
            <?php endif; ?>
	
	<div class="entry-content">
            
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before'   => '<div class="page-links clear">',
				'after'    => '</div>',
				'pagelink' => '<span class="page-link">%</span>',
			) );
		?>
                <?php jinn_jetpack_sharing(); ?>
            
        </div>
    
        <footer class="testimonial-footer">
            <?php the_title( '<h3 class="author-title"><span>', '</span></h3>' ); ?>
            <?php edit_post_link( esc_html__( 'Edit', 'jinn' ), '<p class="show-hide-author label">', '</p>' ); ?>
        </footer>
	
</article><!-- #post-## -->

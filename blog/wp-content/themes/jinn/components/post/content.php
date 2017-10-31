<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jinn
 */

?>
<?php if ( ! is_archive() && ! is_page_template( 'page-templates/frontpage-portfolio.php' ) ) : // Display the Featured Image ABOVE the Posts on Index Pages ?> 
    <?php if ( '' != get_the_post_thumbnail() ) : ?>
        <div class="index-post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'jinn-featured-image' ); ?>
                </a>
        </div>
    <?php endif; ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php else : // Set the Featured Image as the Background Image on Archive Pages ?>
        
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php
            if ( '' != get_the_post_thumbnail() ) { ?> style="background: white url(<?php echo esc_url( the_post_thumbnail_url( 'jinn-featured-image' ) ); ?>);" <?php } ?>>

<?php endif; ?>
        
    <div class="post-content <?php echo has_post_thumbnail() ? 'post-thumbnail' : ''; ?>">
	<header class="entry-header">
		<?php
			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}

		if ( 'post' === get_post_type() && ! is_archive() && ! is_page_template( 'page-templates/frontpage-portfolio.php' ) ) : ?>
		<?php get_template_part( 'components/post/content', 'meta' ); ?>
		<?php
		endif; ?>
	</header>
	<div class="entry-content">
		<?php
                
                if ( is_archive() || is_home() || is_404() || is_front_page() || is_page_template( 'page-templates/frontpage-portfolio.php' ) ) { // Makes EVERY Post on an index page an excerpt
                    jinn_fancy_excerpt();
                } else {
                    
                        // Add the Excerpt as a "Lead-in" on Posts/Pages that contain it
                        if( has_excerpt( $post->ID ) ) {
                            echo '<div class="lead-in">';
                            echo '<p>' . wp_kses_post( get_the_excerpt() ) . '</p>';
                            echo '</div><!-- .lead-in -->';
                        }
                        
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'jinn' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jinn' ),
				'after'  => '</div>',
			) );
                        
                        jinn_jetpack_sharing();
                }
		?>
	</div>
	<?php 
        if ( 'post' === get_post_type() && ! is_archive() && ! is_page_template( 'page-templates/frontpage-portfolio.php' ) ) :
            get_template_part( 'components/post/content', 'footer' ); 
        endif; 
        ?>
                        
    </div>
</article><!-- #post-## -->
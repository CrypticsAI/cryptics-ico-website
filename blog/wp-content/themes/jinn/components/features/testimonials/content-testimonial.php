<?php
/**
 * The template used for displaying testimonials on index view
 *
 * @package Jinn
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'group' ); ?>>
	<?php if ( '' != get_the_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>" class="<?php echo is_page_template( 'page-templates/frontpage-portfolio.php' ) ? 'medium-12 columns' : 'medium-3 columns'; ?>">
                    <div class="testimonial-thumbnail" style="background: url( <?php echo esc_url( get_the_post_thumbnail_url( $post, 'thumbnail' ) ); ?> )">
                            <?php echo the_title( '<span class="screen-reader-text">', '</span>', false ); ?>
                    </div>
                </a>
                <div class="testimonial-entry <?php echo is_page_template( 'page-templates/frontpage-portfolio.php' ) ? 'medium-12 large-10 large-push-1 columns' : 'medium-9 columns'; ?>">
	<?php else : ?>
                <div class="testimonial-entry">
        <?php endif; ?>
                    
                    <?php if ( is_single() ) { ?>
                    <header class="testimonial-entry-header">
                            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header>
                    <?php } ?>

                    <div class="entry-content">

                        <?php 
                        
                        if ( is_page_template( 'page-templates/frontpage-portfolio.php' ) ) {
                            jinn_fancy_excerpt();
                        } else {
                        
                        the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'jinn' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
                        
                        }
                        
                        ?>

                    </div>
                    
                    <?php if ( ! is_single() ) { ?>
                    <footer class="testimonial-footer">
                            <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
                    </footer>
                    <?php } ?>
                    
                </div><!-- .testimonial-entry -->
</article>

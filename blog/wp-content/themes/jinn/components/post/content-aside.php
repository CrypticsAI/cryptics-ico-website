<?php
/**
 * Template part for displaying Aside posts.
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

	<div class="entry-content">
		<?php
                
                    if ( is_archive() || is_home() || is_front_page() || is_page_template( 'page-templates/frontpage-portfolio.php' ) ) { // Makes EVERY Post on an index page an excerpt
                        jinn_fancy_excerpt();
                    } else {
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'jinn' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
                    }
                    jinn_jetpack_sharing();
                    
		?>
	</div><!-- .entry-content -->
        
        <?php
            if ( !is_archive() && !is_home() && !is_front_page() && !is_page_template( 'page-templates/frontpage-portfolio.php' ) ) : // Makes EVERY Post on an index page an excerpt
        ?>          
            <header class="entry-header">
                    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                    <?php if ( 'post' === get_post_type() ) : ?>
                    <div class="entry-meta">
                            <?php jinn_posted_on(); ?>
                    </div><!-- .entry-meta -->
                    <?php endif; ?>
            </header><!-- .entry-header -->

            <footer class="entry-footer group">
                    <?php jinn_entry_footer(); ?>
            </footer><!-- .entry-footer -->
        <?php 
            endif;
        ?>
    </div><!-- end entry classes -->
</article><!-- #post-## -->

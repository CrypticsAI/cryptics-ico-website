<?php
/**
 * Template Name: Show Child Pages
 *
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

	<!--<div id="primary" class="content-area<?php // if (!(have_comments() || comments_open())) : ?> no-comments-area<?php // endif; ?>">-->
		<main id="main" class="site-main" role="main">
                    
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php if ( '' != get_the_post_thumbnail() ) : ?>
                            <div class="index-post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'jinn-featured-image' ); ?>
                                    </a>
                            </div>
                        <?php endif; ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part( 'components/page/content', 'page' ); ?>                
                                                    
                       <?php
                       /*
                        * Loop from Front Page to get individual Child Pages of this one
                        */

                       // Get the children of THIS Page
                       $args = array(
                           'post_type'     => 'page',
                           'post_parent'   => $post->ID,
                           'posts_per_page'=> -1,
                           'orderby'       => 'title',
                           'order'         => 'ASC'
                       );
                       $query = new WP_Query( $args );

                       // The Loop
                       if ( $query->have_posts() ) {
                           echo '<ul class="entry-list-main entry-content">';
                           while ( $query->have_posts() ) {
                               $query->the_post();
                               
                               get_template_part( 'components/page/content', 'child-page' ); 
                               
                           }
                           echo '</ul>';

                       } else {

                           get_template_part('content', 'none');
                           
                       }
                       // Restore original Post Data
                       wp_reset_postdata();
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

                        </article><!-- #post-## -->
                        
                        <?php
                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) :
                                comments_template();
                        endif;
                        ?>
                        
			<?php endwhile; ?>
                                                      
		</main><!-- #content -->
	</div><!-- #primary -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>
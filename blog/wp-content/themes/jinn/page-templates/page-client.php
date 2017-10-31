<?php
/**
 * Template Name: Client Page
 *
 */

get_header(); ?>

<div id="primary" class="content-area archive">
        <main id="main" class="site-main" role="main">

            <header class="page-header page-header-client">
                <?php echo '<h1 class="page-title"><span class="page-title-pre">Client Page:</span>' . get_the_title() . '</h1>'; ?>
            </header>
            

	<div id="primary-right" class="large-3 large-push-9 medium-4 medium-push-8 small-12 columns <?php if (!(have_comments() || comments_open())) : ?> no-comments-area<?php endif; ?>">

			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div>
						<header class="entry-header">
							<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
							<div class="entry-thumbnail">
								<?php the_post_thumbnail(); ?>
							</div>
							<?php endif; ?>
	
						</header><!-- .entry-header -->
	
						<div class="entry-content">
							<?php the_content(); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'jinn' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
                                                        <?php jinn_jetpack_sharing(); ?>
                                                </div><!-- .entry-content -->
	
						<footer class="entry-footer">
							<?php edit_post_link( __( 'Edit', 'jinn' ), '<span class="edit-link">', '</span>' ); ?>
						</footer><!-- .entry-meta -->
					</div>
				</article><!-- #post -->
			<?php endwhile; ?>

	</div><!-- #primary-right -->
        
        
        <div id="primary-left" class="archive large-9 large-pull-3 medium-8 medium-pull-4 small-12 columns">
                    
                        <?php
                        echo '<div id="client-projects-container" class="clear">';
                        
                        $client = get_post_meta( $post->ID, 'client', true );
                        
                        $args = array (
                            'order_by'  => 'desc',
                            'tax_query' => array(
                                array(
                                    'taxonomy'  => 'jetpack-portfolio-tag',
                                    'field'     => 'slug',
                                    'terms'     => $client,
                                    )
                                ),
                            'posts_per_page'    => -1,
                        );

                        $query = new WP_Query( $args );

                        // The Loop (load Projects tagged with the specific Jetpack-Portfolio-Tag specified in the Custom Fields for this Page)
                        if ( $query->have_posts() ) {
                            while ( $query->have_posts() ) {
                                $query->the_post();
                                
                                echo '<div class="archive-item small-12 medium-6 large-4 columns end">';
                                    get_template_part( 'components/features/portfolio/content', 'portfolio' );
                                echo '</div>';
                                
                            }
                        } else {
                            get_template_part( 'content', 'none' );
                        }
                        // Restore original Post Data
                        wp_reset_postdata();
                        
                        echo '</div>';
                        
                        //get_template_part( 'jetpack', 'testimonial' );
                        // @TODO Write this as a function that will run anywhere - currently only works on singular() pages
                        
                        // Figure out how to get JUST the Testimonial(s) related to THIS particular Client
                        $testimonial = get_post_meta( get_the_ID(), 'client_testimonial', true );

                        // Check to be sure we actually HAVE testimonials set for this Page, otherwise, we get ALL testimonials from everywhere
                        if( has_category( $testimonial ) ) {
                            $args = array (
                                'post_type'     => 'jetpack-testimonial',
                                'category_name' => $testimonial,
                            );

                            $query = new WP_Query( $args );

                            // The Loop (load the Testimonial with a Custom Field tag for this particular Jetpack-Portfolio-Tag)
                            if ( $query->have_posts() ) {
                                echo '<section id="client-testimonial-container" class="archive-testimonial small-12 columns">';
                                echo '<div class="archive-testimonials">';
                                    while ( $query->have_posts() ) {
                                        $query->the_post();
                                        echo '<div class="archive-item-testimonial index-post group">';
                                            /*
                                             * Include the Post-Format-specific template for the content.
                                             * If you want to override this in a child theme, then include a file
                                             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                             */
                                            get_template_part( 'components/features/testimonials/content', 'testimonial' );
                                        echo '</div>';
                                    }
                                echo '</div>';
                                echo '</section>';
                            }
                            // Restore original Post Data
                            wp_reset_postdata();
                        } // END $testimonial check
                        ?>
			
		
		<?php jinn_paging_nav(); ?>
	</div><!-- #primary-left -->
        
        </main>
</div>

<?php get_footer(); ?>
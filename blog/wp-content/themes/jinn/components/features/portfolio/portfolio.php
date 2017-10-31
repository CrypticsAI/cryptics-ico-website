<?php
/* The template for displaying portfolio items
 *
 * @package Jinn
 */

				if ( get_query_var( 'paged' ) ) :
					$paged = get_query_var( 'paged' );
				elseif ( get_query_var( 'page' ) ) :
					$paged = get_query_var( 'page' );
				else :
					$paged = 1;
				endif;
                                
                                echo esc_html__( 'Paged: ', 'jinn' ) . esc_html( $paged );

				$posts_per_page = get_option( 'jetpack_portfolio_posts_per_page', '12' );
				$args = array(
					'post_type'      => 'jetpack-portfolio',
					'posts_per_page' => $posts_per_page,
					'paged'          => $paged,
				);
				$project_query = new WP_Query ( $args );
				if ( post_type_exists( 'jetpack-portfolio' ) && $project_query -> have_posts() ) :
			?>

				<div class="portfolio-wrapper content-area archive large-12 columns">
                                    
                                    <header class="page-header">
                                        <h1 class="page-title"><?php esc_html_e( 'Projects', 'jinn' ); ?></h1>
                                    </header>
                                    
					<?php /* Start the Loop */ ?>
					<?php while ( $project_query -> have_posts() ) : $project_query -> the_post(); ?>
                                            <div class="archive-item small-12 medium-6 large-3 columns">
                                                    
						<?php get_template_part( 'components/features/portfolio/content', 'portfolio' ); ?>

                                            </div>
					<?php endwhile; ?>

				</div>

				<?php

                                        jinn_paging_nav();
					wp_reset_postdata();
				?>

			<?php else : ?>

				<section class="no-results not-found">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'jinn' ); ?></h1>
					</header>
					<div class="page-content">
						<?php if ( current_user_can( 'publish_posts' ) ) : ?>

							<p><?php 
                                                            /* translators: %1$s: link to create a new Jetpack Portfolio post */
                                                            printf( wp_kses( __( 'Ready to publish your first project? <a href="%1$s">Get started here</a>.', 'jinn' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php?post_type=jetpack-portfolio' ) ) ); 
                                                        ?></p>

						<?php else : ?>

							<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'jinn' ); ?></p>
							<?php get_search_form(); ?>

						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>
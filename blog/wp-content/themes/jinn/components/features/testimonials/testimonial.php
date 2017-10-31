<?php
/* The template for displaying testimonial items
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

				$posts_per_page = get_option( 'jetpack_testimonial_posts_per_page', '10' );
				$args = array(
					'post_type'      => 'jetpack-testimonial',
					'posts_per_page' => $posts_per_page,
					'paged'          => $paged,
				);
				$project_query = new WP_Query ( $args );
				if ( post_type_exists( 'jetpack-testimonial' ) && $project_query -> have_posts() ) :
			?>

				<div class="testimonial-wrapper">

					<?php /* Start the Loop */ ?>
					<?php while ( $project_query -> have_posts() ) : $project_query -> the_post(); ?>

						<?php get_template_part( 'components/features/testimonials/content', 'testimonial' ); ?>

					<?php endwhile; ?>

				</div>
				<?php
                                        jinn_post_navigation();
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
                                                            /* translators: %1$s: link to create a new Jetpack Testimonial post */
                                                            printf( wp_kses( __( 'Ready to add your first testimonial? <a href="%1$s">Get started here</a>.', 'jinn' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php?post_type=jetpack-testimonial' ) ) ); 
                                                        ?></p>

						<?php else : ?>

							<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'jinn' ); ?></p>
							<?php get_search_form(); ?>

						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>
<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jinn
 */

?>

<section class="<?php if ( is_404() ) { echo 'error-404'; } else { echo 'no-results'; } ?> not-found hentry">
	<header class="entry-header">
		<h1 class="entry-title">
                    <?php 
                    if ( is_404() ) { esc_html_e( 'Nothing Found', 'jinn' ); }
                    else if ( is_search() ) { printf( esc_html_e( 'Nothing found for ', 'jinn' ) . '<ins>' . get_search_query() . '</ins>' ); } 
                    else { esc_html_e( 'Nothing found', 'jinn' ); }
                    ?>
                </h1>
	</header><!-- .page-header -->

	<div class="entry-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php 
                            /* translators: %1$s: link to create a new post */
                            printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'jinn' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); 
                        ?></p>

		<?php elseif ( is_404() ) : ?>
                        
                        <p><?php esc_html_e( 'Are you lost? Try another search below or click one of the latest posts.', 'jinn' ); ?></p>
                        <?php get_search_form(); ?>
                             
                <?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'jinn' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'jinn' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .page-content -->
    </section><!-- .no-results -->

    <?php if ( is_404() || is_search() ) { ?>

            <header class="page-header not-found">
                <h1 class="page-title"><?php esc_html_e( 'Recent Posts:' , 'jinn' ); ?></h1>
            </header>
    

    
            <?php 
                // Get the 5 latest posts
            $args = array(
                'posts_per_page' => 5
            );

            $latest_posts_query = new WP_Query( $args );

            // The Loop
            if ( $latest_posts_query->have_posts() ) {
                while ( $latest_posts_query->have_posts() ) {

                        $latest_posts_query->the_post();
                        
                        // Get standard index page content
                        get_template_part( 'components/post/content', get_post_format() );
                }
            }
            /* Restore original Post Data */
            wp_reset_postdata();
    }
    ?>

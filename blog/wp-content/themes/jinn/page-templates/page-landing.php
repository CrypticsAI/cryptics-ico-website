<?php
/**
 * Template Name: Landing Page
 */

get_header(); ?>

        <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'components/page/content', 'landing' ); ?>

                <?php
                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) :
                                comments_template();
                        endif;
                ?>

        <?php endwhile; // End of the loop. ?>

<?php get_footer(); ?>

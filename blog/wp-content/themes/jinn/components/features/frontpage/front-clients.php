<?php    
/**
 * The template for displaying Clients on the Front Page.
 *
 * This is the template that displays a list of clients on the Front Page.
 *
 * @package Jinn
 */
?>
        <li class="clear small-4 medium-3 large-2 columns">
            <a class="clients-link" href="<?php echo esc_url( get_permalink() ); ?>" title="See all Projects for <?php echo get_the_title(); ?>">
                <figure class="client-figure">
                    <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium', array( 'class' => 'desaturate' ) ); ?>
                </figure>
                <h3 class="entry-title"><?php get_the_title(); ?></h3>
            </a>
        </li>
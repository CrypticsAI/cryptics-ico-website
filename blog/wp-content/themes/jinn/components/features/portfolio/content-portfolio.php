<?php
/**
 * The template used for displaying projects on index view
 *
 * @package Jinn
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php
            if ( '' != get_the_post_thumbnail() ) { ?> style="background: white url(<?php echo esc_url( the_post_thumbnail_url( 'jinn-featured-image' ) ); ?>);" <?php } ?>>
    
    <div class="post-content <?php echo has_post_thumbnail() ? 'post-thumbnail' : ''; ?>">
        
	<header class="portfolio-entry-header">
		<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
	</header>
        
        <div class="entry-content">
            
            <?php jinn_fancy_excerpt(); ?>
            
        </div>
        
    </div>
    
</article>

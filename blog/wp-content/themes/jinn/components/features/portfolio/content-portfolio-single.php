<?php
/**
 * @package Jinn
 */

// Access global variable directly to adjust the content width for portfolio single page
if ( isset( $GLOBALS['content_width'] ) ) {
	$GLOBALS['content_width'] = 1100;
}
?>

<?php if ( '' != get_the_post_thumbnail() ) : ?>
        <div class="index-post-thumbnail">
                <?php the_post_thumbnail( 'full' ); ?>
        </div>
<?php endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
           
                <div class="entry-meta">
                    <?php jinn_posted_on(); ?>
                </div>
        </header>
	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before'   => '<div class="page-links clear">',
				'after'    => '</div>',
				'pagelink' => '<span class="page-link">%</span>',
			) );
		?>
                <?php jinn_jetpack_sharing(); ?>
	</div>
	<footer class="entry-footer">
		<?php jinn_entry_footer(); ?>
	</footer>
</article><!-- #post-## -->
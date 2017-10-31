<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Jinn
 */

?>

	</div><!-- #content -->
        
        <a href="#" class="topbutton"></a><!-- Back to top button -->
        
	<footer id="colophon" class="site-footer" role="contentinfo">
            
            <div class="row"><!-- Start Foundation row -->
                
                <?php get_sidebar( 'footer' ); ?>
                
            </div><!-- End Foundation row -->
            
		<?php get_template_part( 'components/footer/site', 'info' ); ?>
            
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

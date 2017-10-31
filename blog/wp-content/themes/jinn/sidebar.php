<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Jinn
 */

if ( is_active_sidebar( 'sidebar-1' ) || is_active_sidebar( 'sidebar-custom' ) || is_active_sidebar( 'sidebar-page' ) ) :
	
?>
<?php if ( !is_archive() && !is_page_template( 'page-templates/page-client.php' ) && ( is_page_template( 'page-templates/page-sidebar-right.php' ) || get_theme_mod( 'layout_setting' ) === 'sidebar-right' ) ) { ?>
    
    <aside id="secondary" class="widget-area small-12 medium-4 columns sidebar-right" role="complementary" data-equalizer-watch> <!-- Foundation .columns start -->
        
<?php } else if ( !is_archive() && !is_page_template( 'page-templates/page-client.php' ) && ( is_page_template( 'page-templates/page-sidebar-left.php' ) || get_theme_mod( 'layout_setting' ) === 'sidebar-left' ) ) { ?>
        
    <aside id="secondary" class="widget-area small-12 medium-4 medium-pull-8 columns sidebar-left" role="complementary" data-equalizer-watch> <!-- Foundation .columns start -->
        
<?php } else if ( is_archive() || ( is_page_template( 'page-templates/page-no-sidebar.php' ) || get_theme_mod( 'layout_setting' ) === 'no-sidebar' ) ) { ?>
        
    <aside id="secondary" class="widget-area medium-12 columns no-sidebar" role="complementary" data-equalizer-watch> <!-- Foundation .columns start -->
        
<?php } else if ( !is_archive() && is_page_template( 'page-templates/page-full-width.php' ) ) { ?>
        
    <aside id="secondary" class="widget-area medium-12 columns no-sidebar full-width" role="complementary" data-equalizer-watch> <!-- Foundation .columns start -->
        
<?php } else { ?>   
        
    <aside id="secondary" class="widget-area <?php echo esc_attr( get_theme_mod( 'layout_setting', 'no-sidebar' ) ); ?> medium-12 columns">
        
<?php } 

if ( is_active_sidebar( 'sidebar-custom' ) && ( get_post_type( get_the_ID() ) === 'jetpack-testimonial' || get_post_type( get_the_ID() ) === 'jetpack-portfolio' ) ) {
        dynamic_sidebar( 'sidebar-custom' );
} elseif ( is_active_sidebar( 'sidebar-page' ) && is_page() ) {
        dynamic_sidebar( 'sidebar-page' );
} else {
	dynamic_sidebar( 'sidebar-1' ); 
} ?>
</aside><!-- #secondary Foundation .columns end -->

<?php else : 
    return;
endif;

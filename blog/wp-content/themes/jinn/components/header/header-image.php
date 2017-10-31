<?php
// You can upload a custom header and it'll output in a smaller logo size.
global $post;

$header_image = get_header_image();
$use_gradient = get_theme_mod( 'use_gradient' );

if ( ! empty( $header_image ) || $use_gradient !== 0 ) { ?>

        <div id="header-image" class="custom-header <?php 
            if ( is_page_template( 'page-templates/frontpage-portfolio.php' ) ) {
                if ( has_nav_menu( 'front' ) ) {
                    echo 'frontpage-portfolio';
                }
                if ( has_post_thumbnail() ) {
                    echo ' de-desaturate" style="background: url( ' . esc_url( get_the_post_thumbnail_url( $post, 'full' ) ) . '); background-size: cover;"'; 
                }
            }
                else if ( has_header_image() ) {
                    echo '" style="background: url(' . esc_url( get_header_image() ) . '); background-size: cover;"';
                }
                else echo '"';
        ?>>
                <div class="header-wrapper">
                        <div class="site-branding-header">

                                <?php if ( is_front_page() || is_home() || is_page_template( 'page-templates/frontpage-portfolio.php' ) ) : ?>
                                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                                <?php else : ?>
                                    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                                <?php endif; ?>
                                <p class="site-description"><?php bloginfo( 'description' ); ?></p>

                        </div><!-- .site-branding -->
                </div><!-- .header-wrapper -->
                    
                <?php if ( is_page_template( 'page-templates/frontpage-portfolio.php' ) ) { ?>
                    <div class="front-menu-box group unsaturate">
            
                        <?php wp_nav_menu( array( 
                            'theme_location'    => 'front', 
                            'menu'              => 'front',
                            'menu_id'           => 'front-menu',
                            'menu_class'        => 'small-block-grid-2 medium-block-grid-3 flip-cards',
                            'fallback_cb'       => false,
                            'walker'            => new jinn_front_page_walker(),
                            'depth' => 1 
                            ) ); 
                        ?>

                    </div>
                <?php } ?>
                    
        </div><!-- #header-image .custom-header -->
        
<?php 
} else { 
        
        // No header? We need nothing. 
        
} ?>
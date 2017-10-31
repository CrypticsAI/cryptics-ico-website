/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-branding-header .site-title a, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
                                $( '.site-branding-header, h1.site-title::after' ).css( {
                                        'display': 'none'
                                } );
			} else {
				$( '.site-branding-header .site-title a, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
                                $( '.site-branding-header, h1.site-title::after' ).css( {
                                        'display': 'block'
                                } );
				$( '.site-branding-header .site-title a, .site-description' ).css( {
					'color': to
				} );
			}
		} );
	} );
        
        // Custom Customizer Functions
        // Header Gradient color
        // Set default colors
        grad1_color = '#085078';
        grad2_color = '#85d8ce';
        grad3_color = '#f8fff3';
        
        wp.customize( 'grad1_color', function( value ) {
		value.bind( function( to ) {
			grad1_color = to;
                        $( '.custom-header' ).css( {
                            'background': 'radial-gradient( ellipse farthest-side at 100% 100%, '
                                    .concat( grad3_color ).concat( ' 10%, ' )
                                    .concat( grad2_color ).concat( ' 50%, ' )
                                    .concat( grad1_color ).concat( ' 120% )' )
                        } );
		} );
	} );
        wp.customize( 'grad2_color', function( value ) {
		value.bind( function( to ) {
			grad2_color = to;
                        $( '.custom-header' ).css( {
                            'background': 'radial-gradient( ellipse farthest-side at 100% 100%, '
                                    .concat( grad3_color ).concat( ' 10%, ' )
                                    .concat( grad2_color ).concat( ' 50%, ' )
                                    .concat( grad1_color ).concat( ' 120% )' )
                        } );
		} );
	} );
        wp.customize( 'grad3_color', function( value ) {
		value.bind( function( to ) {
			grad3_color = to;
                        $( '.custom-header' ).css( {
                            'background': 'radial-gradient( ellipse farthest-side at 100% 100%, '
                                    .concat( grad3_color ).concat( ' 10%, ' )
                                    .concat( grad2_color ).concat( ' 50%, ' )
                                    .concat( grad1_color ).concat( ' 120% )' )
                        } );
		} );
	} );
        
        // Highlight colors
        wp.customize( 'highlight_color', function( value ) {
		value.bind( function( to ) {
			$( 'a:visited, a:hover, a:focus, a:active, .entry-content a, .entry-summary a' ).css( {
                            'color': to 
                        } );
                        $( '.search-toggle, .search-box-wrapper' ).css( {
                            'background-color': to
                        } );
		} );
	} );
        
        
        // Custome Layout (Sidebar) Options
        wp.customize( 'layout_setting', function( value ) {
		value.bind( function( to ) {
			$( '#page' ).removeClass( 'no-sidebar sidebar-right sidebar-left' ); 
                        $( '#page' ).addClass( to );
                        $( '#primary' ).removeClass( 'medium-8 medium-push-4 medium-10 medium-push-1 large-8 large-push-2 no-sidebar sidebar-right sidebar-left' );
                        $( '#secondary' ).removeClass( 'medium-4 medium-pull-8 medium-12 no-sidebar sidebar-right sidebar-left' );
                        $( '#secondary .widget' ).removeClass( 'small-6 medium-4' );
                        if( to === 'sidebar-right' ) {
                            $( '#primary' ).addClass( 'medium-8 sidebar-right' );
                            $( '#secondary' ).addClass( 'medium-4 sidebar-right' );
                        } else if ( to === 'sidebar-left' ) {
                            $( '#primary' ).addClass( 'medium-8 medium-push-4 sidebar-left' );
                            $( '#secondary' ).addClass( 'medium-4 medium-pull-8 sidebar-left' );
                        } else {
                            $( '#primary' ).addClass( 'medium-10 medium-push-1 large-8 large-push-2 no-sidebar' );
                            $( '#secondary' ).addClass( 'medium-12 no-sidebar' );
                            $( '#secondary .widget' ).addClass( 'small-6 medium-4 columns' );
                        }
		} );
	} );
           
} )( jQuery );
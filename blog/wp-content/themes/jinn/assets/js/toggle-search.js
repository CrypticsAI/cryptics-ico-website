/* 
 * Toggles header search on and off
 */
jQuery( document ).ready( function( $ ) {
    $( ".search-toggle" ).click( function() {
        $( "#search-container" ).animate( { width: 'toggle', left: 0 }, function() {
            $( ".search-toggle" ).toggleClass( 'active' );
        });
    });
    
    /* Add Foundation classes to Comment Awaiting Moderation box */
    $("*").find("p.comment-awaiting-moderation").each(function() {
        $(this).addClass("alert-box success radius");
    });
    
    
    /*
     * For Category List dropdown in Post meta
     */
    $( '.entry-meta .cat-links .jinn_cat_switch' ).click( function( e ) {
          e.preventDefault();
          if( $( this ).next( 'ul' ).hasClass( 'childopen' ) ) {
              $( this ).next( 'ul' ).removeClass( 'childopen' );
          } else {
              $( this ).next( 'ul' ).addClass( 'childopen' );
          }
    });
    $( '.entry-meta .cat-links .first-cat-link' ).hover( function ( e ) {
          e.preventDefault();
          if( $( '.entry-meta .cat-links ul' ).hasClass( 'childopen' ) ) {
              $( '.entry-meta .cat-links ul' ).removeClass( 'childopen' );
          } else {
              $( '.entry-meta .cat-links ul' ).addClass( 'childopen' );
          }
    });
    
    /*
     * Toggle Author box
     */
    $( ".show-hide-author" ).click( function() {
        $( ".author-box" ).toggle( 600, 'swing' );
        if ( $( this ).html() == "Hide" ) {
            $( this ).html( "Show" );
        } else {
            $( this ).html( "Hide" );
        }
    });
    
    /*
     * Add classes to centered images
     */
    // Wrap centered images in a new figure element
   $( 'img.aligncenter, figure.aligncenter' ).wrap( '<figure class="centered-image"></figure>' );
   
   /*
    * Add Foundation classes to widgets on pages without a sidebar
    */
   $( '.no-sidebar #secondary .widget, .page-template-page-client #secondary .widget' ).addClass( 'small-6 medium-4 columns' );
});



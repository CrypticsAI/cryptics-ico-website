/* 
 * Masonry settings to organize footer widgets
 */

jQuery( document ).ready( function($) {
    $('#footer-widgets').masonry({
        itemSelector: '.widget',
        isAnimated: true
    });
});

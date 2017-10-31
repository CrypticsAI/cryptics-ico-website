<?php

/* 
 * Footer widgets
 */

if ( ! is_active_sidebar( 'sidebar-footer' ) ) {
    return;
}
?>

<div id="supplementary">
        <div id="footer-widgets" class="footer-widgets widget-area clear" role="complementary">
            <?php dynamic_sidebar( 'sidebar-footer' ); ?>
        </div> <!-- #footer-widgets -->
</div> <!-- #supplementary -->
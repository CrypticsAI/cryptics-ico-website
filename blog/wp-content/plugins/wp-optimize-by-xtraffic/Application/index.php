<?php 
ob_start();
header('Status: 403 Forbidden',true,403);
header('HTTP/1.1 403 Forbidden',true,403);
echo '
<h1>This WordPress website has been optimized by plugin "<a href="https://wordpress.org/plugins/wp-optimize-by-xtraffic/" target="_blank"><em><strong>WP Optimize By xTraffic</strong></em></a>".</h1>
';
exit();
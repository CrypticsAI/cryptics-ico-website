<?php 
return array(
	'application' => array(
        'security' => array(
			'crypt' => array(
				'cipher' => 'rijndael-256'
				,'mode' => 'cbc'
				,'key' => substr(WP_PEPVN_SITE_SALT,0,16)
			)
		)
    )
);
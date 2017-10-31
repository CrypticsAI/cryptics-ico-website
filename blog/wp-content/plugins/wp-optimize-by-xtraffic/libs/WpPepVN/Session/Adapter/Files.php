<?php 
namespace WpPepVN\Session\Adapter;

use WpPepVN\Session\AdapterInterface
	,WpPepVN\Session\Adapter
;

/**
 * WpPepVN\Session\Adapter\Files
 *
 * This adapter store sessions in plain files
 *
 *<code>
 * $session = new \WpPepVN\Session\Adapter\Files(array(
 *    'uniqueId' => 'my-private-app'
 * ));
 *
 * $session->start();
 *
 * $session->set('var', 'some-value');
 *
 * echo $session->get('var');
 *</code>
 */
class Files extends Adapter implements AdapterInterface
{

}

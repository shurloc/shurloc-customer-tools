<?php
/**
 * Plugin bootstrap.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap the plugin.
 */
function shurloc_product_tools_bootstrap(): void {
}

add_action(
	'plugins_loaded',
	'shurloc_customer_tools_bootstrap'
);

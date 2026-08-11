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
function shurloc_customer_tools_bootstrap(): void {
	/*
	 * Autoloader.
	 */

	require_once SHURLOC_CUSTOMER_TOOLS_PATH . 'includes/class-shurloc-autoloader.php';

	$autoloader = new Shurloc_Autoloader(
		__DIR__
	);

	$autoloader->register();
}

add_action(
	'plugins_loaded',
	'shurloc_customer_tools_bootstrap'
);

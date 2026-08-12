<?php
/**
 * Plugin bootstrap.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

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

	$customer_page = new Shurloc_Admin_Page_Controller();

	$admin_page = new Shurloc_Admin_Menu(
		customer_page: $customer_page
	);

	$admin_page->register();
}

add_action(
	'plugins_loaded',
	__NAMESPACE__ . '\\shurloc_customer_tools_bootstrap'
);

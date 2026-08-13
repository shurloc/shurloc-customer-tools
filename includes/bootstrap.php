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
	/**
	 * Autoloader.
	 */

	require_once SHURLOC_CUSTOMER_TOOLS_PATH . 'includes/class-shurloc-autoloader.php';

	$autoloader = new Shurloc_Autoloader(
		__DIR__
	);

	$autoloader->register();

	/**
	 * Admin page.
	 */

	$customer_page = new Shurloc_Admin_Page_Controller();

	$admin_page = new Shurloc_Admin_Menu(
		customer_page: $customer_page
	);

	$admin_page->register();

	/**
	 * User ativity tracking.
	 */

	$user_activity_service = new Shurloc_User_Activity_Service();

	$activity_time_formatter = new Shurloc_Activity_Time_Formatter();

	$user_activity_columns = new Shurloc_User_Activity_Columns(
		time_formatter: $activity_time_formatter,
	);

	$user_filters = new Shurloc_User_Filters();

	$user_activity_filters = new Shurloc_User_Activity_Filters();

	$user_activity_service->register();
	$user_activity_columns->register();
	$user_filters->register();
	$user_activity_filters->register();
}

add_action(
	'plugins_loaded',
	__NAMESPACE__ . '\\shurloc_customer_tools_bootstrap',
	20
);

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

use Shurloc\Tools\Shurloc_Admin_Page_Interface;

/**
 * Bootstrap the plugin.
 */
function shurloc_customer_tools_bootstrap(): void {
	/**
	 * Autoloader.
	 */

	require_once SHURLOC_CUSTOMER_TOOLS_PATH . 'includes/class-shurloc-autoloader.php';

	$autoloader = new Shurloc_Autoloader(
		base_directory: __DIR__,
	);

	$autoloader->register();

	/**
	 * Helpers.
	 */

	$relative_time_formatter = new Shurloc_Relative_Time_Formatter();

	/**
	 * User activity, purchase, and cart services.
	 */

	$user_activity_service = new Shurloc_User_Activity_Service();
	$user_activity_service->register();

	$user_purchase_service = new Shurloc_User_Purchase_Service();
	$user_purchase_service->register();

	$user_cart_service = new Shurloc_User_Cart_Service();
	$user_cart_service->register();

	/**
	 * Customer data migrations.
	 */

	$user_purchase_migration =
		new Shurloc_User_Purchase_Migration(
			purchase_service: $user_purchase_service,
		);

	$migrations_controller =
		new Shurloc_Customer_Migrations_Controller(
			purchase_migration: $user_purchase_migration,
		);
	$migrations_controller->register();

	/**
	 * Admin page.
	 */

	/* @disregard P1009 Undefined type 'Shurloc\Tools\Shurloc_Admin_Page_Interface'. */
	if ( interface_exists( Shurloc_Admin_Page_Interface::class ) ) {
		$customer_page = new Shurloc_Admin_Page_Controller(
			migrations_controller: $migrations_controller,
		);

		$admin_page = new Shurloc_Admin_Menu(
			customer_page: $customer_page,
		);
		$admin_page->register();
	}

	$user_activity_columns = new Shurloc_User_Activity_Columns(
		time_formatter: $relative_time_formatter,
	);
	$user_activity_columns->register();

	$user_purchase_columns = new Shurloc_User_Purchase_Columns(
		time_formatter: $relative_time_formatter,
	);
	$user_purchase_columns->register();

	$user_cart_column = new Shurloc_User_Cart_Column();
	$user_cart_column->register();

	$user_filters = new Shurloc_User_Filters();
	$user_filters->register();

	$user_activity_filters = new Shurloc_User_Activity_Filters();
	$user_activity_filters->register();

	$user_purchase_filters = new Shurloc_User_Purchase_Filters();
	$user_purchase_filters->register();

	/**
	 * Manage other columns in the user table.
	 */

	$user_columns = new Shurloc_User_Columns();
	$user_columns->register();

	$user_phone_column = new Shurloc_User_Phone_Column();
	$user_phone_column->register();
}

add_action(
	'plugins_loaded',
	__NAMESPACE__ . '\\shurloc_customer_tools_bootstrap',
	20
);

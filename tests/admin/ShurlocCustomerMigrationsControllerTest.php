<?php
/**
 * Tests for the customer migrations admin controller.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WC_Order;

/**
 * Tests the customer migrations admin controller.
 */
final class ShurlocCustomerMigrationsControllerTest extends TestCase {

	/**
	 * Migration.
	 *
	 * @var Shurloc_User_Purchase_Migration
	 */
	private Shurloc_User_Purchase_Migration $migration;

	/**
	 * Controller under test.
	 *
	 * @var Shurloc_Customer_Migrations_Controller
	 */
	private Shurloc_Customer_Migrations_Controller $controller;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$GLOBALS['shurloc_test_actions']              = array();
		$GLOBALS['shurloc_test_action_metadata']      = array();
		$GLOBALS['shurloc_test_enqueued_scripts']     = array();
		$GLOBALS['shurloc_test_options']              = array();
		$GLOBALS['shurloc_test_nonce_fields']         = array();
		$GLOBALS['shurloc_test_users']                = array();
		$GLOBALS['shurloc_test_orders']               = array();
		$GLOBALS['shurloc_test_user_meta']            = array();
		$GLOBALS['shurloc_test_options']              = array();
		$GLOBALS['shurloc_test_user_capabilities']    = array();
		$GLOBALS['shurloc_test_admin_referer_checks'] = array();
		$GLOBALS['shurloc_test_nonce_valid']          = true;
		$GLOBALS['shurloc_test_redirects']            = array();
		$GLOBALS['shurloc_test_wp_die_messages']      = array();
		$GLOBALS['shurloc_test_wc_get_orders_args']   = array();

		$_GET = array();

		$service = new Shurloc_User_Purchase_Service();

		$this->migration = new Shurloc_User_Purchase_Migration(
			$service
		);

		$this->controller =
			new Shurloc_Customer_Migrations_Controller(
				$this->migration
			);
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$GLOBALS['shurloc_test_actions']              = array();
		$GLOBALS['shurloc_test_action_metadata']      = array();
		$GLOBALS['shurloc_test_enqueued_scripts']     = array();
		$GLOBALS['shurloc_test_options']              = array();
		$GLOBALS['shurloc_test_nonce_fields']         = array();
		$GLOBALS['shurloc_test_users']                = array();
		$GLOBALS['shurloc_test_orders']               = array();
		$GLOBALS['shurloc_test_user_meta']            = array();
		$GLOBALS['shurloc_test_options']              = array();
		$GLOBALS['shurloc_test_user_capabilities']    = array();
		$GLOBALS['shurloc_test_admin_referer_checks'] = array();
		$GLOBALS['shurloc_test_nonce_valid']          = true;
		$GLOBALS['shurloc_test_redirects']            = array();
		$GLOBALS['shurloc_test_wp_die_messages']      = array();
		$GLOBALS['shurloc_test_wc_get_orders_args']   = array();

		$_GET = array();

		parent::tearDown();
	}

	/**
	 * Verify controller hooks are registered.
	 *
	 * @return void
	 */
	public function test_register_adds_admin_hooks(): void {

		$this->controller->register();

		self::assertContains(
			array(
				$this->controller,
				'handle_purchase_migration',
			),
			$GLOBALS['shurloc_test_actions']
				['admin_post_shurloc_run_purchase_migration']
		);

		self::assertContains(
			array(
				$this->controller,
				'enqueue_assets',
			),
			$GLOBALS['shurloc_test_actions']
				['admin_enqueue_scripts']
		);
	}

	/**
	 * Verify migration script is enqueued on the migrations tab.
	 *
	 * @return void
	 */
	public function test_enqueue_assets_on_migrations_page(): void {

		$_GET['page'] = 'shurloc-customer-tools';
		$_GET['tab']  = 'migrations';

		$this->controller->enqueue_assets();

		$handles = array_column(
			$GLOBALS['shurloc_test_enqueued_scripts'],
			'handle'
		);

		self::assertContains(
			'shurloc-customer-migrations',
			$handles
		);
	}

	/**
	 * Verify migration script is not enqueued elsewhere.
	 *
	 * @return void
	 */
	public function test_enqueue_assets_is_ignored_outside_migrations_page(): void {

		$_GET['page'] = 'shurloc-customer-tools';
		$_GET['tab']  = 'overview';

		$this->controller->enqueue_assets();

		$handles = array_column(
			$GLOBALS['shurloc_test_enqueued_scripts'],
			'handle'
		);

		self::assertNotContains(
			'shurloc-customer-migrations',
			$handles
		);
	}

	/**
	 * Verify the migrations page renders the purchase migration controls.
	 *
	 * @return void
	 */
	public function test_render_shows_purchase_migration_controls(): void {

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Purchase Tracking Seeding',
			$output
		);

		self::assertStringContainsString(
			'Enable this migration',
			$output
		);

		self::assertStringContainsString(
			'Run Purchase Migration',
			$output
		);

		self::assertStringContainsString(
			'disabled',
			$output
		);

		self::assertCount(
			1,
			$GLOBALS['shurloc_test_nonce_fields']
		);

		self::assertSame(
			'shurloc_run_purchase_migration',
			$GLOBALS['shurloc_test_nonce_fields'][0]['action']
		);
	}

	/**
	 * Verify the migrations page displays the last-run migration version.
	 *
	 * @return void
	 */
	public function test_render_shows_last_run_version(): void {

		$GLOBALS['shurloc_test_options']
		[ Shurloc_User_Purchase_Migration::LAST_RUN_VERSION_OPTION ] = 1;

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Last-run migration version',
			$output
		);

		self::assertMatchesRegularExpression(
			'/Last-run migration version<\/th>\s*<td>\s*1\s*<\/td>/',
			$output
		);
	}

	/**
	 * Verify running the purchase migration seeds customer data.
	 *
	 * @return void
	 */
	public function test_run_purchase_migration_seeds_purchase_data(): void {

		$GLOBALS['shurloc_test_users'] = array(
			101,
		);

		$GLOBALS['shurloc_test_orders'][101] = array(
			$this->create_order(
				order_id: 200,
				user_id: 101,
				status: 'completed',
				timestamp: 1_000_000,
				total: 125.50,
			),
		);

		$this->controller->run_purchase_migration();

		self::assertSame(
			200,
			$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Purchase_Service::LAST_PURCHASE_ORDER_META_KEY ]
		);

		self::assertSame(
			'completed',
			$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Purchase_Service::LAST_PURCHASE_STATUS_META_KEY ]
		);
	}

	/**
	 * Verify running the purchase migration returns the expected result URL.
	 *
	 * @return void
	 */
	public function test_run_purchase_migration_returns_result_url(): void {

		$GLOBALS['shurloc_test_users'] = array(
			101,
			102,
		);

		$GLOBALS['shurloc_test_orders'][101] = array(
			$this->create_order(
				order_id: 200,
				user_id: 101,
				status: 'completed',
				timestamp: 1_000_000,
				total: 125.50,
			),
		);

		$GLOBALS['shurloc_test_orders'][102] = array();

		$redirect_url =
		$this->controller->run_purchase_migration();

		self::assertStringContainsString(
			'page=shurloc-customer-tools',
			$redirect_url
		);

		self::assertStringContainsString(
			'tab=migrations',
			$redirect_url
		);

		self::assertStringContainsString(
			'migration=purchase',
			$redirect_url
		);

		self::assertStringContainsString(
			'examined=2',
			$redirect_url
		);

		self::assertStringContainsString(
			'updated=1',
			$redirect_url
		);

		self::assertStringContainsString(
			'skipped=1',
			$redirect_url
		);

		self::assertStringContainsString(
			'errors=0',
			$redirect_url
		);

		self::assertStringContainsString(
			'_wpnonce=test-nonce-shurloc_purchase_migration_result',
			$redirect_url
		);
	}

	/**
	 * Verify running the purchase migration records migration metadata.
	 *
	 * @return void
	 */
	public function test_run_purchase_migration_records_run_metadata(): void {

		$GLOBALS['shurloc_test_users'] = array();

		$this->controller->run_purchase_migration();

		self::assertArrayHasKey(
			Shurloc_User_Purchase_Migration::LAST_RUN_OPTION,
			$GLOBALS['shurloc_test_options']
		);

		self::assertSame(
			Shurloc_User_Purchase_Migration::VERSION,
			$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Purchase_Migration::LAST_RUN_VERSION_OPTION ]
		);
	}

	/**
	 * Verify an unauthorized user cannot run the purchase migration.
	 *
	 * @return void
	 */
	public function test_handle_purchase_migration_rejects_unauthorized_user(): void {

		$GLOBALS['shurloc_test_user_capabilities']
		['manage_options'] = false;

		try {
			$this->controller->handle_purchase_migration();

			self::fail(
				'Expected wp_die() to terminate the request.'
			);
		} catch ( RuntimeException $exception ) {
			self::assertSame(
				'You are not allowed to run this migration.',
				$exception->getMessage()
			);
		}

		self::assertSame(
			array(
				'You are not allowed to run this migration.',
			),
			$GLOBALS['shurloc_test_wp_die_messages']
		);
	}

	/**
	 * Verify a valid migration result nonce displays the result notice.
	 *
	 * @return void
	 */
	public function test_render_displays_valid_purchase_migration_result(): void {

		$_GET['migration'] = 'purchase';
		$_GET['examined']  = '10';
		$_GET['updated']   = '7';
		$_GET['skipped']   = '3';
		$_GET['errors']    = '0';
		$_GET['_wpnonce']  =
		'test-nonce-shurloc_purchase_migration_result';

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Purchase migration complete.',
			$output
		);

		self::assertStringContainsString(
			'Examined: 10',
			$output
		);

		self::assertStringContainsString(
			'Updated: 7',
			$output
		);

		self::assertStringContainsString(
			'Skipped: 3',
			$output
		);

		self::assertStringContainsString(
			'Errors: 0',
			$output
		);
	}

	/**
	 * Verify an invalid migration result nonce does not display a result notice.
	 *
	 * @return void
	 */
	public function test_render_does_not_display_result_with_invalid_nonce(): void {

		$GLOBALS['shurloc_test_nonce_valid'] = false;

		$_GET['migration'] = 'purchase';
		$_GET['examined']  = '10';
		$_GET['updated']   = '7';
		$_GET['skipped']   = '3';
		$_GET['errors']    = '0';
		$_GET['_wpnonce']  = 'invalid-nonce';

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringNotContainsString(
			'Purchase migration complete.',
			$output
		);
	}

	/**
	 * Verify migration result data without a nonce is ignored.
	 *
	 * @return void
	 */
	public function test_render_does_not_display_result_without_nonce(): void {

		$_GET['migration'] = 'purchase';
		$_GET['examined']  = '10';
		$_GET['updated']   = '7';
		$_GET['skipped']   = '3';
		$_GET['errors']    = '0';

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringNotContainsString(
			'Purchase migration complete.',
			$output
		);
	}

	/**
	 * Verify a locked migration is not executed a second time.
	 *
	 * @return void
	 */
	public function test_run_purchase_migration_does_not_run_when_locked(): void {

		$GLOBALS['shurloc_test_users'] = array(
			101,
		);

		$GLOBALS['shurloc_test_orders'][101] = array(
			$this->create_order(
				order_id: 200,
				user_id: 101,
				status: 'completed',
				timestamp: 1_000_000,
				total: 125.50,
			),
		);

		$GLOBALS['shurloc_test_options']
		[ Shurloc_User_Purchase_Migration::LOCK_OPTION ] = time();

		$redirect_url =
		$this->controller->run_purchase_migration();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta']
		);

		self::assertStringContainsString(
			'migration=purchase-locked',
			$redirect_url
		);
	}

	/**
	 * Verify a completed purchase migration releases its lock.
	 *
	 * @return void
	 */
	public function test_run_purchase_migration_releases_lock_after_completion(): void {

		$GLOBALS['shurloc_test_users'] = array();

		$this->controller->run_purchase_migration();

		self::assertArrayNotHasKey(
			Shurloc_User_Purchase_Migration::LOCK_OPTION,
			$GLOBALS['shurloc_test_options']
		);
	}

	/**
	 * Verify a locked purchase migration displays a warning notice.
	 *
	 * @return void
	 */
	public function test_render_displays_purchase_migration_locked_notice(): void {

		$_GET['migration'] = 'purchase-locked';
		$_GET['_wpnonce']  =
		'test-nonce-shurloc_purchase_migration_result';

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Purchase migration is already running.',
			$output
		);

		self::assertStringContainsString(
			'No second migration was started.',
			$output
		);
	}

	/**
	 * Verify the migrations page renders the running overlay.
	 *
	 * @return void
	 */
	public function test_render_shows_migration_running_overlay(): void {

		ob_start();

		$this->controller->render();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'shurloc-migration-overlay',
			$output
		);

		self::assertStringContainsString(
			'Migration is running',
			$output
		);

		self::assertStringContainsString(
			'Please keep this page open until the migration completes.',
			$output
		);
	}

	/**
	 * Create a WooCommerce order test double.
	 *
	 * @param int    $order_id  Order ID.
	 * @param int    $user_id   Customer user ID.
	 * @param string $status    Order status.
	 * @param int    $timestamp Creation timestamp.
	 * @param float  $total     Order total.
	 * @return WC_Order
	 */
	private function create_order(
		int $order_id,
		int $user_id,
		string $status,
		int $timestamp,
		float $total
	): WC_Order {

		$order = new WC_Order( $order_id );

		$order->set_customer_id( $user_id );
		$order->set_status( $status );
		$order->set_date_created( $timestamp );
		$order->set_total( (string) $total );

		return $order;
	}
}

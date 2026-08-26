<?php
/**
 * Tests for the customer migrations admin controller.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;

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

		$GLOBALS['shurloc_test_actions']          = array();
		$GLOBALS['shurloc_test_action_metadata']  = array();
		$GLOBALS['shurloc_test_enqueued_scripts'] = array();
		$GLOBALS['shurloc_test_options']          = array();
		$GLOBALS['shurloc_test_nonce_fields']     = array();

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

		$GLOBALS['shurloc_test_actions']          = array();
		$GLOBALS['shurloc_test_action_metadata']  = array();
		$GLOBALS['shurloc_test_enqueued_scripts'] = array();
		$GLOBALS['shurloc_test_options']          = array();
		$GLOBALS['shurloc_test_nonce_fields']     = array();

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
}

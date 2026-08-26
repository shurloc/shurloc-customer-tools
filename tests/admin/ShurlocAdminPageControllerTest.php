<?php
/**
 * Tests for the customer admin page controller.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;

/**
 * Tests the customer admin page controller.
 */
final class ShurlocAdminPageControllerTest extends TestCase {

	/**
	 * Admin page controller under test.
	 *
	 * @var Shurloc_Admin_Page_Controller
	 */
	private Shurloc_Admin_Page_Controller $controller;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$GLOBALS['shurloc_test_options']      = array();
		$GLOBALS['shurloc_test_nonce_fields'] = array();

		$_GET = array();

		$purchase_service =
			new Shurloc_User_Purchase_Service();

		$purchase_migration =
			new Shurloc_User_Purchase_Migration(
				purchase_service: $purchase_service,
			);

		$migrations_controller =
			new Shurloc_Customer_Migrations_Controller(
				purchase_migration: $purchase_migration,
			);

		$this->controller =
			new Shurloc_Admin_Page_Controller(
				migrations_controller: $migrations_controller,
			);
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$GLOBALS['shurloc_test_options']      = array();
		$GLOBALS['shurloc_test_nonce_fields'] = array();

		$_GET = array();

		parent::tearDown();
	}

	/**
	 * Verify the overview tab is displayed by default.
	 *
	 * @return void
	 */
	public function test_render_page_displays_overview_by_default(): void {

		ob_start();

		$this->controller->render_page();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Customer Tools',
			$output
		);

		self::assertStringContainsString(
			'Utilities for customer administration.',
			$output
		);

		self::assertStringNotContainsString(
			'Customer Data Migrations',
			$output
		);
	}

	/**
	 * Verify the migrations tab renders the migrations controller.
	 *
	 * @return void
	 */
	public function test_render_page_displays_migrations_tab(): void {

		$_GET['page'] = 'shurloc-customer-tools';
		$_GET['tab']  = 'migrations';

		ob_start();

		$this->controller->render_page();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Customer Data Migrations',
			$output
		);

		self::assertStringContainsString(
			'Purchase Tracking Seeding',
			$output
		);

		self::assertStringNotContainsString(
			'Utilities for customer administration.',
			$output
		);
	}

	/**
	 * Verify an invalid tab falls back to the overview tab.
	 *
	 * @return void
	 */
	public function test_render_page_invalid_tab_falls_back_to_overview(): void {

		$_GET['page'] = 'shurloc-customer-tools';
		$_GET['tab']  = 'invalid-tab';

		ob_start();

		$this->controller->render_page();

		$output = (string) ob_get_clean();

		self::assertStringContainsString(
			'Utilities for customer administration.',
			$output
		);

		self::assertStringNotContainsString(
			'Customer Data Migrations',
			$output
		);
	}

	/**
	 * Verify the overview tab is active by default.
	 *
	 * @return void
	 */
	public function test_overview_tab_is_active_by_default(): void {

		ob_start();

		$this->controller->render_page();

		$output = (string) ob_get_clean();

		self::assertMatchesRegularExpression(
			'/tab=overview[^"]*"[^>]*class="nav-tab nav-tab-active"/',
			$output
		);
	}

	/**
	 * Verify the migrations tab is active when selected.
	 *
	 * @return void
	 */
	public function test_migrations_tab_is_active_when_selected(): void {

		$_GET['page'] = 'shurloc-customer-tools';
		$_GET['tab']  = 'migrations';

		ob_start();

		$this->controller->render_page();

		$output = (string) ob_get_clean();

		self::assertMatchesRegularExpression(
			'/tab=migrations[^"]*"[^>]*class="nav-tab nav-tab-active"/',
			$output
		);
	}
}

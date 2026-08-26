<?php
/**
 * Tests for the user cart migration.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;
use WC_Product;

/**
 * Tests the user cart migration.
 */
final class ShurlocUserCartMigrationTest extends TestCase {

	/**
	 * Cart service.
	 *
	 * @var Shurloc_User_Cart_Service
	 */
	private Shurloc_User_Cart_Service $cart_service;

	/**
	 * Migration under test.
	 *
	 * @var Shurloc_User_Cart_Migration
	 */
	private Shurloc_User_Cart_Migration $migration;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$GLOBALS['shurloc_test_user_meta'] = array();
		$GLOBALS['shurloc_test_options']   = array();

		$this->cart_service =
			new Shurloc_User_Cart_Service();

		$this->migration =
			new Shurloc_User_Cart_Migration(
				cart_service: $this->cart_service,
			);
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$GLOBALS['shurloc_test_user_meta'] = array();
		$GLOBALS['shurloc_test_options']   = array();

		parent::tearDown();
	}

	/**
	 * Verify an active migration lock is detected.
	 *
	 * @return void
	 */
	public function test_is_locked_returns_true_for_active_lock(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LOCK_OPTION ] = time();

		self::assertTrue(
			$this->migration->is_locked()
		);
	}

	/**
	 * Verify no lock is reported when none exists.
	 *
	 * @return void
	 */
	public function test_is_locked_returns_false_when_no_lock_exists(): void {

		self::assertFalse(
			$this->migration->is_locked()
		);
	}

	/**
	 * Verify a stale migration lock is removed.
	 *
	 * @return void
	 */
	public function test_is_locked_removes_stale_lock(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LOCK_OPTION ] = time() - 901;

		self::assertFalse(
			$this->migration->is_locked()
		);

		self::assertArrayNotHasKey(
			Shurloc_User_Cart_Migration::LOCK_OPTION,
			$GLOBALS['shurloc_test_options']
		);
	}

	/**
	 * Verify the migration lock can be acquired.
	 *
	 * @return void
	 */
	public function test_acquire_lock_creates_lock(): void {

		$result = $this->migration->acquire_lock();

		self::assertTrue(
			$result
		);

		self::assertArrayHasKey(
			Shurloc_User_Cart_Migration::LOCK_OPTION,
			$GLOBALS['shurloc_test_options']
		);
	}

	/**
	 * Verify an existing migration lock cannot be acquired again.
	 *
	 * @return void
	 */
	public function test_acquire_lock_returns_false_when_already_locked(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LOCK_OPTION ] = time();

		self::assertFalse(
			$this->migration->acquire_lock()
		);
	}

	/**
	 * Verify a stale migration lock can be reacquired.
	 *
	 * @return void
	 */
	public function test_acquire_lock_replaces_stale_lock(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LOCK_OPTION ] = time() - 901;

		self::assertTrue(
			$this->migration->acquire_lock()
		);

		self::assertArrayHasKey(
			Shurloc_User_Cart_Migration::LOCK_OPTION,
			$GLOBALS['shurloc_test_options']
		);
	}

	/**
	 * Verify the migration lock can be released.
	 *
	 * @return void
	 */
	public function test_release_lock_removes_lock(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LOCK_OPTION ] = time();

		$this->migration->release_lock();

		self::assertArrayNotHasKey(
			Shurloc_User_Cart_Migration::LOCK_OPTION,
			$GLOBALS['shurloc_test_options']
		);
	}

	/**
	 * Verify the stored last-run timestamp can be read.
	 *
	 * @return void
	 */
	public function test_get_last_run_returns_stored_timestamp(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LAST_RUN_OPTION ] = 1_000_000;

		self::assertSame(
			1_000_000,
			$this->migration->get_last_run()
		);
	}

	/**
	 * Verify the stored last-run migration version can be read.
	 *
	 * @return void
	 */
	public function test_get_last_run_version_returns_stored_version(): void {

		$GLOBALS['shurloc_test_options']
			[ Shurloc_User_Cart_Migration::LAST_RUN_VERSION_OPTION ] = 2;

		self::assertSame(
			2,
			$this->migration->get_last_run_version()
		);
	}
}

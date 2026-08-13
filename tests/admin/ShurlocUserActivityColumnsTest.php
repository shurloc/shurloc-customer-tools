<?php
/**
 * Tests for the user activity admin columns.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;

/**
 * Tests the user activity admin columns.
 */
final class ShurlocUserActivityColumnsTest extends TestCase {

	/**
	 * Columns class under test.
	 *
	 * @var Shurloc_User_Activity_Columns
	 */
	private Shurloc_User_Activity_Columns $columns;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$GLOBALS['shurloc_test_filters']         = array();
		$GLOBALS['shurloc_test_filter_metadata'] = array();
		$GLOBALS['shurloc_test_user_meta']       = array();
		$GLOBALS['shurloc_test_time']            = 1_000_000;

		$time_formatter = new Shurloc_Relative_Time_Formatter();

		$this->columns = new Shurloc_User_Activity_Columns(
			time_formatter: $time_formatter,
		);
	}

	/**
	 * Verify the Users columns filter is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_users_columns_filter(): void {

		$this->columns->register();

		self::assertContains(
			array(
				$this->columns,
				'add_columns',
			),
			$GLOBALS['shurloc_test_filters']['manage_users_columns']
		);
	}

	/**
	 * Verify the custom column rendering filter is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_custom_column_filter(): void {

		$this->columns->register();

		self::assertContains(
			array(
				$this->columns,
				'render_column',
			),
			$GLOBALS['shurloc_test_filters']['manage_users_custom_column']
		);

		self::assertSame(
			10,
			$GLOBALS['shurloc_test_filter_metadata']
				['manage_users_custom_column'][0]['priority']
		);

		self::assertSame(
			3,
			$GLOBALS['shurloc_test_filter_metadata']
				['manage_users_custom_column'][0]['accepted_args']
		);
	}

	/**
	 * Verify the sortable columns filter is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_sortable_columns_filter(): void {

		$this->columns->register();

		self::assertContains(
			array(
				$this->columns,
				'register_sortable_columns',
			),
			$GLOBALS['shurloc_test_filters']
				['manage_users_sortable_columns']
		);
	}

	/**
	 * Verify activity columns are added to existing Users columns.
	 *
	 * @return void
	 */
	public function test_add_columns_adds_activity_columns(): void {

		$columns = array(
			'username' => 'Username',
			'email'    => 'Email',
		);

		$result = $this->columns->add_columns( $columns );

		self::assertSame(
			array(
				'username'              => 'Username',
				'email'                 => 'Email',
				/**
				 *  Remove last login column.
				'shurloc_last_login'    => 'Last Login',
				*/
				'shurloc_last_activity' => 'Last Activity',
			),
			$result
		);
	}

	/**
	 * Verify the Last Login column renders a formatted timestamp.
	 *
	 * @return void
	 */
	/**
	 *  Remove last login column.
	public function test_last_login_column_renders_formatted_timestamp(): void {

		$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY ] =
				1_000_000 - 3600;

		$result = $this->columns->render_column(
			'',
			Shurloc_User_Activity_Columns::LAST_LOGIN_COLUMN,
			101
		);

		self::assertSame(
			'1 hour ago',
			$result
		);
	}
	 */

	/**
	 * Verify the Last Activity column renders a formatted timestamp.
	 *
	 * @return void
	 */
	public function test_last_activity_column_renders_formatted_timestamp(): void {

		$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY ] =
				1_000_000 - ( 5 * 60 );

		$result = $this->columns->render_column(
			'',
			Shurloc_User_Activity_Columns::LAST_ACTIVITY_COLUMN,
			101
		);

		self::assertSame(
			'5 minutes ago',
			$result
		);
	}

	/**
	 * Verify missing Last Login metadata renders Never.
	 *
	 * @return void
	 */
	/**
	 *  Remove last login column.
	public function test_missing_last_login_renders_never(): void {

		$result = $this->columns->render_column(
			'',
			Shurloc_User_Activity_Columns::LAST_LOGIN_COLUMN,
			101
		);

		self::assertSame(
			'Never',
			$result
		);
	}
	 */

	/**
	 * Verify missing Last Activity metadata renders Never Active.
	 *
	 * @return void
	 */
	public function test_missing_last_activity_renders_never_active(): void {

		$result = $this->columns->render_column(
			'',
			Shurloc_User_Activity_Columns::LAST_ACTIVITY_COLUMN,
			101
		);

		self::assertSame(
			'Never Active',
			$result
		);
	}

	/**
	 * Verify zero Last Login metadata renders Never.
	 *
	 * @return void
	 */
	/**
	 * Remove last login column.
	public function test_zero_last_login_renders_never(): void {

		$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY ] = 0;

		$result = $this->columns->render_column(
			'',
			Shurloc_User_Activity_Columns::LAST_LOGIN_COLUMN,
			101
		);

		self::assertSame(
			'Never',
			$result
		);
	}
	 */

	/**
	 * Verify unrelated column output is preserved.
	 *
	 * @return void
	 */
	public function test_unrelated_column_preserves_existing_output(): void {

		$result = $this->columns->render_column(
			'Existing output',
			'email',
			101
		);

		self::assertSame(
			'Existing output',
			$result
		);
	}

	/**
	 * Verify the Last Login column is sortable by its meta key.
	 *
	 * @return void
	 */
	/**
	 * Remove last login column.
	public function test_last_login_column_is_sortable(): void {

		$result = $this->columns->register_sortable_columns(
			array()
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
			$result[ Shurloc_User_Activity_Columns::LAST_LOGIN_COLUMN ]
		);
	}
	 */

	/**
	 * Verify the Last Activity column is sortable by its meta key.
	 *
	 * @return void
	 */
	public function test_last_activity_column_is_sortable(): void {

		$result = $this->columns->register_sortable_columns(
			array()
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$result[ Shurloc_User_Activity_Columns::LAST_ACTIVITY_COLUMN ]
		);
	}

	/**
	 * Verify existing sortable columns are preserved.
	 *
	 * @return void
	 */
	public function test_sortable_columns_preserve_existing_columns(): void {

		$result = $this->columns->register_sortable_columns(
			array(
				'username' => 'login',
			)
		);

		self::assertSame(
			'login',
			$result['username']
		);

		/**
		 * Remove last login column.
		self::assertSame(
			Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
			$result[ Shurloc_User_Activity_Columns::LAST_LOGIN_COLUMN ]
		);
		 */

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$result[ Shurloc_User_Activity_Columns::LAST_ACTIVITY_COLUMN ]
		);
	}
}

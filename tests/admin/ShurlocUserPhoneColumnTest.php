<?php
/**
 * Tests for the user phone admin column.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;

/**
 * Tests the user phone admin column.
 */
final class ShurlocUserPhoneColumnTest extends TestCase {

	/**
	 * Phone column under test.
	 *
	 * @var Shurloc_User_Phone_Column
	 */
	private Shurloc_User_Phone_Column $phone_column;

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

		$this->phone_column = new Shurloc_User_Phone_Column();
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$GLOBALS['shurloc_test_filters']         = array();
		$GLOBALS['shurloc_test_filter_metadata'] = array();
		$GLOBALS['shurloc_test_user_meta']       = array();

		parent::tearDown();
	}

	/**
	 * Verify the Users columns filter is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_users_columns_filter(): void {

		$this->phone_column->register();

		self::assertContains(
			array(
				$this->phone_column,
				'add_column',
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

		$this->phone_column->register();

		self::assertContains(
			array(
				$this->phone_column,
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
	 * Verify the Phone column is inserted after Email.
	 *
	 * @return void
	 */
	public function test_phone_column_is_added_after_email(): void {

		$result = $this->phone_column->add_column(
			array(
				'cb'       => '<input type="checkbox" />',
				'username' => 'Username',
				'name'     => 'Name',
				'email'    => 'Email',
				'role'     => 'Role',
			)
		);

		self::assertSame(
			array(
				'cb',
				'username',
				'name',
				'email',
				Shurloc_User_Phone_Column::PHONE_COLUMN,
				'role',
			),
			array_keys( $result )
		);

		self::assertSame(
			'Phone',
			$result[ Shurloc_User_Phone_Column::PHONE_COLUMN ]
		);
	}

	/**
	 * Verify the Phone column is appended when Email is unavailable.
	 *
	 * @return void
	 */
	public function test_phone_column_is_appended_when_email_column_is_missing(): void {

		$result = $this->phone_column->add_column(
			array(
				'username' => 'Username',
				'role'     => 'Role',
			)
		);

		self::assertSame(
			array(
				'username',
				'role',
				Shurloc_User_Phone_Column::PHONE_COLUMN,
			),
			array_keys( $result )
		);
	}

	/**
	 * Verify unrelated custom column output is preserved.
	 *
	 * @return void
	 */
	public function test_unrelated_column_preserves_existing_output(): void {

		$result = $this->phone_column->render_column(
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
	 * Verify a missing phone number renders an em dash.
	 *
	 * @return void
	 */
	public function test_missing_phone_renders_em_dash(): void {

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertSame(
			'&mdash;',
			$result
		);
	}

	/**
	 * Verify surrounding whitespace is removed from phone metadata.
	 *
	 * @return void
	 */
	public function test_phone_whitespace_is_trimmed(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'  (555) 123-4567  ';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringContainsString(
			'>(555) 123-4567</a>',
			$result
		);
	}

	/**
	 * Verify a United States country code is omitted from display.
	 *
	 * @return void
	 */
	public function test_leading_us_country_code_is_removed_from_display(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'+1 (555) 123-4567';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringContainsString(
			'>(555) 123-4567</a>',
			$result
		);

		self::assertStringNotContainsString(
			'>+1 (555) 123-4567</a>',
			$result
		);
	}

	/**
	 * Verify the full international number is preserved in the tel URI.
	 *
	 * @return void
	 */
	public function test_us_country_code_is_preserved_in_tel_uri(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'+1 (555) 123-4567';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringContainsString(
			'href="tel:+15551234567"',
			$result
		);
	}

	/**
	 * Verify formatting characters are removed from a local tel URI.
	 *
	 * @return void
	 */
	public function test_local_phone_uri_contains_only_digits(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'(555) 123-4567';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringContainsString(
			'href="tel:5551234567"',
			$result
		);
	}

	/**
	 * Verify a non-US international phone number keeps its country code.
	 *
	 * @return void
	 */
	public function test_non_us_country_code_is_preserved_for_display_and_uri(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'+44 20 7123 4567';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringContainsString(
			'>+44 20 7123 4567</a>',
			$result
		);

		self::assertStringContainsString(
			'href="tel:+442071234567"',
			$result
		);
	}

	/**
	 * Verify non-phone characters do not produce a tel link.
	 *
	 * @return void
	 */
	public function test_phone_without_digits_renders_without_link(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] = 'Unknown';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertSame(
			'Unknown',
			$result
		);

		self::assertStringNotContainsString(
			'<a ',
			$result
		);
	}

	/**
	 * Verify the phone display value is escaped.
	 *
	 * @return void
	 */
	public function test_phone_display_is_escaped(): void {

		$GLOBALS['shurloc_test_user_meta'][101]['billing_phone'] =
			'(555) 123-4567<script>alert(1)</script>';

		$result = $this->phone_column->render_column(
			'',
			Shurloc_User_Phone_Column::PHONE_COLUMN,
			101
		);

		self::assertStringNotContainsString(
			'<script>',
			$result
		);

		self::assertStringContainsString(
			'&lt;script&gt;',
			$result
		);
	}
}

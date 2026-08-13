<?php
/**
 * User phone admin column.
 *
 * Adds a Phone column to the WordPress Users table.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a customer phone number column to the WordPress Users table.
 */
final class Shurloc_User_Phone_Column {

	/**
	 * Phone column key.
	 *
	 * @var string
	 */
	public const PHONE_COLUMN = 'shurloc_phone';

	/**
	 * Billing phone user meta key.
	 *
	 * @var string
	 */
	private const BILLING_PHONE_META_KEY = 'billing_phone';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_filter(
			'manage_users_columns',
			array(
				$this,
				'add_column',
			)
		);

		add_filter(
			'manage_users_custom_column',
			array(
				$this,
				'render_column',
			),
			10,
			3
		);
	}

	/**
	 * Add the Phone column after Email.
	 *
	 * @param array<string,string> $columns Existing Users table columns.
	 * @return array<string,string>
	 */
	public function add_column(
		array $columns
	): array {

		$updated_columns = array();

		foreach ( $columns as $column_key => $column_label ) {

			$updated_columns[ $column_key ] = $column_label;

			if ( 'email' !== $column_key ) {
				continue;
			}

			$updated_columns[ self::PHONE_COLUMN ] = __(
				'Phone',
				'shurloc-customer-tools'
			);
		}

		if ( ! isset( $updated_columns[ self::PHONE_COLUMN ] ) ) {
			$updated_columns[ self::PHONE_COLUMN ] = __(
				'Phone',
				'shurloc-customer-tools'
			);
		}

		return $updated_columns;
	}

	/**
	 * Render the Phone column.
	 *
	 * @param string $output      Existing column output.
	 * @param string $column_name Column name.
	 * @param int    $user_id     User ID.
	 * @return string
	 */
	public function render_column(
		string $output,
		string $column_name,
		int $user_id
	): string {

		if ( self::PHONE_COLUMN !== $column_name ) {
			return $output;
		}

		$phone = (string) get_user_meta(
			$user_id,
			self::BILLING_PHONE_META_KEY,
			true
		);

		$phone = trim( $phone );

		if ( '' === $phone ) {
			return '&mdash;';
		}

		$display_phone = $this->format_display_phone( $phone );
		$phone_uri     = $this->format_phone_uri( $phone );

		if ( '' === $phone_uri ) {
			return esc_html( $display_phone );
		}

		return sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( 'tel:' . $phone_uri ),
			esc_html( $display_phone )
		);
	}

	/**
	 * Format a phone number for display.
	 *
	 * Removes a leading United States country code while preserving the
	 * remaining formatting stored in user metadata.
	 *
	 * @param string $phone Phone number.
	 * @return string
	 */
	private function format_display_phone(
		string $phone
	): string {

		$phone = trim( $phone );

		if ( str_starts_with( $phone, '+1' ) ) {
			$phone = ltrim(
				substr( $phone, 2 )
			);
		}

		return $phone;
	}

	/**
	 * Format a phone number for use in a tel URI.
	 *
	 * Preserves a leading plus sign and removes all other characters that
	 * are not digits.
	 *
	 * @param string $phone Phone number.
	 * @return string
	 */
	private function format_phone_uri(
		string $phone
	): string {

		$phone = trim( $phone );

		$has_leading_plus = str_starts_with( $phone, '+' );

		$digits = preg_replace(
			'/\D+/',
			'',
			$phone
		);

		if ( null === $digits || '' === $digits ) {
			return '';
		}

		return $has_leading_plus
			? '+' . $digits
			: $digits;
	}
}

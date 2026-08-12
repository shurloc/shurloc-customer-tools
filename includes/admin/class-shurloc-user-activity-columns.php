<?php
/**
 * User activity admin columns.
 *
 * Adds Last Login and Last Activity columns to the WordPress Users table.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

/**
 * Adds user activity columns to the WordPress Users table.
 */
final class Shurloc_User_Activity_Columns {

	/**
	 * Last Login column key.
	 *
	 * @var string
	 */
	private const LAST_LOGIN_COLUMN = 'shurloc_last_login';

	/**
	 * Last Activity column key.
	 *
	 * @var string
	 */
	private const LAST_ACTIVITY_COLUMN = 'shurloc_last_activity';

	/**
	 * Activity time formatter.
	 *
	 * @var Shurloc_Activity_Time_Formatter
	 */
	private Shurloc_Activity_Time_Formatter $time_formatter;

	/**
	 * Constructor.
	 *
	 * @param Shurloc_Activity_Time_Formatter $time_formatter Activity time formatter.
	 */
	public function __construct(
		Shurloc_Activity_Time_Formatter $time_formatter
	) {

		$this->formatter = $time_formatter;
	}

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
				'add_columns',
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

		add_filter(
			'manage_users_sortable_columns',
			array(
				$this,
				'register_sortable_columns',
			)
		);
	}

	/**
	 * Add activity columns to the Users table.
	 *
	 * @param array<string,string> $columns Existing Users table columns.
	 * @return array<string,string>
	 */
	public function add_columns(
		array $columns
	): array {

		$columns[ self::LAST_LOGIN_COLUMN ] = __(
			'Last Login',
			'shurloc-customer-tools'
		);

		$columns[ self::LAST_ACTIVITY_COLUMN ] = __(
			'Last Activity',
			'shurloc-customer-tools'
		);

		return $columns;
	}

	/**
	 * Render a custom Users table column.
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

		switch ( $column_name ) {

			case self::LAST_LOGIN_COLUMN:
				return $this->render_timestamp_column(
					user_id: $user_id,
					meta_key: Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
					empty_label: __( 'Never', 'shurloc-customer-tools' ),
				);

			case self::LAST_ACTIVITY_COLUMN:
				return $this->render_timestamp_column(
					user_id: $user_id,
					meta_key: Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
					empty_label: __( 'Never Active', 'shurloc-customer-tools' ),
				);

			default:
				return $output;
		}
	}

	/**
	 * Register activity columns as sortable.
	 *
	 * @param array<string,string> $columns Existing sortable columns.
	 * @return array<string,string>
	 */
	public function register_sortable_columns(
		array $columns
	): array {

		$columns[ self::LAST_LOGIN_COLUMN ] =
			Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY;

		$columns[ self::LAST_ACTIVITY_COLUMN ] =
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY;

		return $columns;
	}

	/**
	 * Render a timestamp-backed user column.
	 *
	 * @param int    $user_id     User ID.
	 * @param string $meta_key    User meta key.
	 * @param string $empty_label Label when no timestamp is stored.
	 * @return string
	 */
	private function render_timestamp_column(
		int $user_id,
		string $meta_key,
		string $empty_label
	): string {

		$timestamp = (int) get_user_meta(
			$user_id,
			$meta_key,
			true
		);

		if ( 0 >= $timestamp ) {
			return esc_html( $empty_label );
		}

		return esc_html(
			$this->time_formatter->format( $timestamp )
		);
	}
}

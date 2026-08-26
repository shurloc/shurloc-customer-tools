<?php
/**
 * User purchase migration.
 *
 * Seeds last-purchase metadata for existing registered customers from their
 * most recent qualifying WooCommerce order.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

use Throwable;

/**
 * Seeds purchase tracking data for existing users.
 */
final class Shurloc_User_Purchase_Migration {

	/**
	 * Current migration version.
	 *
	 * Increment when the migration behavior changes in a way that may warrant
	 * rerunning it.
	 *
	 * @var int
	 */
	public const VERSION = 1;

	/**
	 * Option storing the timestamp of the most recent migration run.
	 *
	 * This preserves the option used by the original Code Snippet.
	 *
	 * @var string
	 */
	public const LAST_RUN_OPTION = 'sl_last_purchase_seeded';

	/**
	 * Option storing the migration version used for the most recent run.
	 *
	 * @var string
	 */
	public const LAST_RUN_VERSION_OPTION = 'sl_last_purchase_seeded_version';

	/**
	 * Purchase service.
	 *
	 * @var Shurloc_User_Purchase_Service
	 */
	private Shurloc_User_Purchase_Service $purchase_service;

	/**
	 * Constructor.
	 *
	 * @param Shurloc_User_Purchase_Service $purchase_service Purchase service.
	 */
	public function __construct(
		Shurloc_User_Purchase_Service $purchase_service
	) {

		$this->purchase_service = $purchase_service;
	}

	/**
	 * Run the purchase tracking migration.
	 *
	 * Each registered WordPress user is examined. When the user has at least
	 * one qualifying WooCommerce order, their purchase snapshot is replaced
	 * with data from their most recent qualifying order.
	 *
	 * This migration is intentionally rerunnable.
	 *
	 * @return array{
	 *     examined: int,
	 *     updated: int,
	 *     skipped: int,
	 *     errors: int
	 * }
	 */
	public function run(): array {

		$result = array(
			'examined' => 0,
			'updated'  => 0,
			'skipped'  => 0,
			'errors'   => 0,
		);

		$user_ids = get_users(
			array(
				'fields' => 'ids',
			)
		);

		foreach ( $user_ids as $user_id ) {

			++$result['examined'];

			try {

				$orders = wc_get_orders(
					array(
						'customer_id' => (int) $user_id,
						'status'      =>
							Shurloc_User_Purchase_Service::QUALIFYING_STATUSES,
						'orderby'     => 'date',
						'order'       => 'DESC',
						'limit'       => 1,
						'return'      => 'objects',
					)
				);

				if ( empty( $orders ) ) {
					++$result['skipped'];
					continue;
				}

				$order = reset( $orders );

				if ( false === $order ) {
					++$result['skipped'];
					continue;
				}

				$stored = $this->purchase_service
					->store_purchase_from_order(
						(int) $user_id,
						$order
					);

				if ( ! $stored ) {
					++$result['skipped'];
					continue;
				}

				++$result['updated'];

			} catch ( Throwable $exception ) {

				++$result['errors'];

				continue;
			}
		}

		update_option(
			self::LAST_RUN_OPTION,
			time()
		);

		update_option(
			self::LAST_RUN_VERSION_OPTION,
			self::VERSION
		);

		return $result;
	}

	/**
	 * Get the timestamp of the most recent migration run.
	 *
	 * @return int Last-run timestamp, or 0 if the migration has never run.
	 */
	public function get_last_run(): int {

		return (int) get_option(
			self::LAST_RUN_OPTION,
			0
		);
	}

	/**
	 * Get the migration version used for the most recent run.
	 *
	 * @return int Last-run version, or 0 if no version has been recorded.
	 */
	public function get_last_run_version(): int {

		return (int) get_option(
			self::LAST_RUN_VERSION_OPTION,
			0
		);
	}
}

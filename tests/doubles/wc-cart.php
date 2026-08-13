<?php
/**
 * WooCommerce cart test double.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

/**
 * WooCommerce cart test double.
 */
if ( ! class_exists( 'WC_Cart' ) ) {

	/**
	 * WooCommerce cart test double.
	 */
	class WC_Cart {

		/**
		 * Get the cart items.
		 *
		 * @return array<string,array<string,mixed>>
		 */
		public function get_cart(): array {

			return array();
		}

		/**
		 * Get the number of items in the cart.
		 *
		 * @return int
		 */
		public function get_cart_contents_count(): int {

			return 0;
		}

		/**
		 * Get the calculated cart totals.
		 *
		 * @return array<string,mixed>
		 */
		public function get_totals(): array {

			return array(
				'total' => 0.0,
			);
		}
	}
}

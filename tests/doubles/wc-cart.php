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
		 * Cart items.
		 *
		 * @var array<string,array<string,mixed>>
		 */
		private array $cart = array();

		/**
		 * Cart totals.
		 *
		 * @var array<string,mixed>
		 */
		private array $totals = array(
			'total' => 0.0,
		);

		/**
		 * Set the cart items.
		 *
		 * @param array<string,array<string,mixed>> $cart Cart items.
		 * @return void
		 */
		public function set_cart(
			array $cart
		): void {

			$this->cart = $cart;
		}

		/**
		 * Get the cart items.
		 *
		 * @return array<string,array<string,mixed>>
		 */
		public function get_cart(): array {

			return $this->cart;
		}

		/**
		 * Get the number of items in the cart.
		 *
		 * Counts product quantities rather than the number of cart lines,
		 * matching WooCommerce behavior.
		 *
		 * @return int
		 */
		public function get_cart_contents_count(): int {

			$count = 0;

			foreach ( $this->cart as $cart_item ) {

				if (
					! isset( $cart_item['quantity'] ) ||
					! is_numeric( $cart_item['quantity'] )
				) {
					continue;
				}

				$count += (int) $cart_item['quantity'];
			}

			return $count;
		}

		/**
		 * Set the cart total.
		 *
		 * @param float $total Cart total.
		 * @return void
		 */
		public function set_total(
			float $total
		): void {

			$this->totals['total'] = $total;
		}

		/**
		 * Set the cart totals.
		 *
		 * @param array<string,mixed> $totals Cart totals.
		 * @return void
		 */
		public function set_totals(
			array $totals
		): void {

			$this->totals = $totals;
		}

		/**
		 * Get the cart totals.
		 *
		 * @return array<string,mixed>
		 */
		public function get_totals(): array {

			return $this->totals;
		}
	}
}

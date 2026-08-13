<?php
/**
 * WooCommerce cart test double.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use WC_Cart;

/**
 * Controllable WooCommerce cart test double.
 */
final class Shurloc_WC_Cart_Double extends WC_Cart {

	/**
	 * Test cart items.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	private array $test_cart = array();

	/**
	 * Test cart totals.
	 *
	 * @var array<string,mixed>
	 */
	private array $test_totals = array(
		'total' => 0.0,
	);

	/**
	 * Set test cart items.
	 *
	 * @param array<string,array<string,mixed>> $cart Cart items.
	 * @return void
	 */
	public function set_test_cart(
		array $cart
	): void {

		$this->test_cart = $cart;
	}

	/**
	 * Get cart items.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_cart(): array {

		return $this->test_cart;
	}

	/**
	 * Get the number of items in the cart.
	 *
	 * Counts quantities rather than cart lines to match WooCommerce behavior.
	 *
	 * @return int
	 */
	public function get_cart_contents_count(): int {

		$count = 0;

		foreach ( $this->test_cart as $cart_item ) {

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
	 * Set the test cart total.
	 *
	 * @param float $total Cart total.
	 * @return void
	 */
	public function set_test_total(
		float $total
	): void {

		$this->test_totals['total'] = $total;
	}

	/**
	 * Set test cart totals.
	 *
	 * @param array<string,mixed> $totals Cart totals.
	 * @return void
	 */
	public function set_test_totals(
		array $totals
	): void {

		$this->test_totals = $totals;
	}

	/**
	 * Get cart totals.
	 *
	 * @return array<string,mixed>
	 */
	public function get_totals(): array {

		return $this->test_totals;
	}
}

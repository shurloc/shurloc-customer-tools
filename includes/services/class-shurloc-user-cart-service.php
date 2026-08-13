<?php
/**
 * User cart service.
 *
 * Tracks the most recent WooCommerce cart state for logged-in users.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

use WC_Cart;
use WC_Product;

/**
 * Tracks a user's most recently known WooCommerce cart state.
 */
final class Shurloc_User_Cart_Service {

	/**
	 * Last known cart items meta key.
	 *
	 * @var string
	 */
	public const CART_ITEMS_META_KEY = 'cart_last_known_items';

	/**
	 * Last known cart total meta key.
	 *
	 * @var string
	 */
	public const CART_TOTAL_META_KEY = 'cart_last_known_total';

	/**
	 * Last known cart item count meta key.
	 *
	 * @var string
	 */
	public const CART_COUNT_META_KEY = 'cart_last_known_count';

	/**
	 * Last known cart version meta key.
	 *
	 * @var string
	 */
	public const CART_VERSION_META_KEY = 'cart_last_known_version';

	/**
	 * Last known cart update timestamp meta key.
	 *
	 * @var string
	 */
	public const CART_UPDATED_META_KEY = 'cart_last_known_updated';

	/**
	 * Register WooCommerce hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_action(
			'woocommerce_add_to_cart',
			array(
				$this,
				'capture_cart',
			),
			20
		);

		add_action(
			'woocommerce_cart_item_removed',
			array(
				$this,
				'capture_cart',
			),
			20
		);

		add_action(
			'woocommerce_cart_item_restored',
			array(
				$this,
				'capture_cart',
			),
			20
		);

		add_action(
			'woocommerce_after_cart_item_quantity_update',
			array(
				$this,
				'capture_cart',
			),
			20
		);

		add_action(
			'woocommerce_cart_emptied',
			array(
				$this,
				'capture_cart',
			),
			20
		);
	}

	/**
	 * Capture the current WooCommerce cart state.
	 *
	 * Hook arguments are intentionally ignored because the current complete
	 * cart state is always read directly from WooCommerce.
	 *
	 * @return void
	 */
	public function capture_cart(): void {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( 0 >= $user_id ) {
			return;
		}

		$cart = $this->get_cart();

		if ( null === $cart ) {
			return;
		}

		$items = $this->get_cart_items( $cart );

		$count = $cart->get_cart_contents_count();

		$total = $this->get_cart_total( $cart );

		$version = (int) get_user_meta(
			$user_id,
			self::CART_VERSION_META_KEY,
			true
		);

		++$version;

		update_user_meta(
			$user_id,
			self::CART_ITEMS_META_KEY,
			$items
		);

		update_user_meta(
			$user_id,
			self::CART_TOTAL_META_KEY,
			$total
		);

		update_user_meta(
			$user_id,
			self::CART_COUNT_META_KEY,
			$count
		);

		update_user_meta(
			$user_id,
			self::CART_VERSION_META_KEY,
			$version
		);

		update_user_meta(
			$user_id,
			self::CART_UPDATED_META_KEY,
			time()
		);
	}

	/**
	 * Get the current WooCommerce cart.
	 *
	 * @return WC_Cart|null
	 */
	private function get_cart(): ?WC_Cart {

		if ( ! function_exists( 'WC' ) ) {
			return null;
		}

		return WC()->cart;
	}

	/**
	 * Get normalized cart items.
	 *
	 * @param WC_Cart $cart WooCommerce cart.
	 * @return array<int,array{
	 *     product_id:int,
	 *     variation_id:int,
	 *     qty:int,
	 *     sku:string,
	 *     name:string,
	 *     key:string,
	 *     variation:array<string,string>
	 * }>
	 */
	private function get_cart_items(
		WC_Cart $cart
	): array {

		$items = array();

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

			if (
				! isset( $cart_item['data'] ) ||
				! $cart_item['data'] instanceof WC_Product
			) {
				continue;
			}

			$product = $cart_item['data'];

			$product_id = isset( $cart_item['product_id'] )
				? (int) $cart_item['product_id']
				: 0;

			$variation_id = isset( $cart_item['variation_id'] )
				? (int) $cart_item['variation_id']
				: 0;

			$quantity = isset( $cart_item['quantity'] )
				? (int) $cart_item['quantity']
				: 0;

			if ( 0 >= $quantity ) {
				continue;
			}

			$variation = $this->get_variation_attributes(
				cart_item: $cart_item,
			);

			$items[] = array(
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
				'qty'          => $quantity,
				'sku'          => $product->get_sku(),
				'name'         => $product->get_name(),
				'key'          => (string) $cart_item_key,
				'variation'    => $variation,
			);
		}

		return $items;
	}

	/**
	 * Get normalized variation attributes from a cart item.
	 *
	 * @param array<string,mixed> $cart_item WooCommerce cart item.
	 * @return array<string,string>
	 */
	private function get_variation_attributes(
		array $cart_item
	): array {

		if (
			! isset( $cart_item['variation'] ) ||
			! is_array( $cart_item['variation'] )
		) {
			return array();
		}

		$variation = array();

		foreach ( $cart_item['variation'] as $attribute => $value ) {

			if (
				! is_string( $attribute ) ||
				! is_string( $value )
			) {
				continue;
			}

			$variation[ $attribute ] = $value;
		}

		return $variation;
	}

	/**
	 * Get the current cart total as a numeric value.
	 *
	 * @param WC_Cart $cart WooCommerce cart.
	 * @return float
	 */
	private function get_cart_total(
		WC_Cart $cart
	): float {

		$totals = $cart->get_totals();

		if (
			isset( $totals['total'] ) &&
			is_numeric( $totals['total'] )
		) {
			return (float) $totals['total'];
		}

		return 0.0;
	}
}

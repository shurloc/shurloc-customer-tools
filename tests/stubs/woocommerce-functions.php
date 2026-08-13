<?php
/**
 * WooCommerce function test doubles.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );


if ( ! function_exists( 'wc_get_order_status_name' ) ) {

	/**
	 * Get a display name for an order status.
	 *
	 * @param string $status Order status.
	 * @return string
	 */
	function wc_get_order_status_name(
		string $status
	): string {

		return ucwords(
			str_replace(
				'-',
				' ',
				$status
			)
		);
	}
}


if ( ! function_exists( 'wc_price' ) ) {

	/**
	 * Format a WooCommerce price.
	 *
	 * @param float $price Price.
	 * @return string
	 */
	function wc_price(
		float $price
	): string {

		return '$' . number_format(
			$price,
			2,
			'.',
			','
		);
	}
}

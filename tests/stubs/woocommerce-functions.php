<?php
/**
 * WooCommerce function test doubles.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );


/**
 * WooCommerce instance used by tests.
 */
$GLOBALS['shurloc_test_woocommerce'] = null;


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


if ( ! function_exists( 'wc_get_order_statuses' ) ) {

	/**
	 * Get WooCommerce order statuses.
	 *
	 * Test replacement for wc_get_order_statuses().
	 *
	 * @return array<string,string>
	 */
	function wc_get_order_statuses(): array {

		return array(
			'wc-pending'    => 'Pending payment',
			'wc-on-hold'    => 'On hold',
			'wc-processing' => 'Processing',
			'wc-completed'  => 'Completed',
			'wc-cancelled'  => 'Cancelled',
			'wc-refunded'   => 'Refunded',
			'wc-failed'     => 'Failed',
		);
	}
}


if ( ! function_exists( 'WC' ) ) {

	/**
	 * Get the WooCommerce test instance.
	 *
	 * Test replacement for WC().
	 *
	 * @return WooCommerce
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Squiz.Commenting.FunctionComment.Missing -- Matches WooCommerce's WC() function.
	function WC(): WooCommerce {

		if (
			! $GLOBALS['shurloc_test_woocommerce'] instanceof WooCommerce
		) {
			$GLOBALS['shurloc_test_woocommerce'] = new WooCommerce();
		}

		return $GLOBALS['shurloc_test_woocommerce'];
	}
}

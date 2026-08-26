<?php
/**
 * WooCommerce function test doubles.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );


/**
 * WooCommerce instance used by tests.
 */
$GLOBALS['shurloc_test_woocommerce'] = null;

/**
 * WooCommerce orders indexed by order ID.
 */
$GLOBALS['shurloc_test_orders'] = array();

/**
 * Test WooCommerce orders indexed by customer user ID.
 */
$GLOBALS['shurloc_test_orders'] = array();

/**
 * Recorded WooCommerce order query arguments.
 */
$GLOBALS['shurloc_test_wc_get_orders_args'] = array();


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


/**
 * Get a WooCommerce order.
 *
 * @param int $order_id Order ID.
 * @return WC_Order|false
 */
function wc_get_order(
	int $order_id
): WC_Order|false {

	if (
		! isset( $GLOBALS['shurloc_test_orders'] ) ||
		! is_array( $GLOBALS['shurloc_test_orders'] ) ||
		! isset( $GLOBALS['shurloc_test_orders'][ $order_id ] )
	) {
		return false;
	}

	$order = $GLOBALS['shurloc_test_orders'][ $order_id ];

	if ( ! $order instanceof WC_Order ) {
		return false;
	}

	return $order;
}


if ( ! function_exists( 'wc_attribute_label' ) ) {

	/**
	 * Get a display label for a WooCommerce attribute.
	 *
	 * Test replacement for wc_attribute_label().
	 *
	 * @param string $name Attribute name.
	 * @return string
	 */
	function wc_attribute_label(
		string $name
	): string {

		$name = str_replace(
			array(
				'pa_',
				'_',
				'-',
			),
			array(
				'',
				' ',
				' ',
			),
			$name
		);

		return ucwords( $name );
	}

	if ( ! function_exists( 'wc_get_orders' ) ) {

		/**
		 * Retrieve WooCommerce orders.
		 *
		 * Test replacement for wc_get_orders().
		 *
		 * @param array<string, mixed> $args Order query arguments.
		 * @return WC_Order[]
		 */
		function wc_get_orders(
			array $args = array()
		): array {

			$GLOBALS['shurloc_test_wc_get_orders_args'][] =
			$args;

			$user_id = isset( $args['customer_id'] )
			? (int) $args['customer_id']
			: 0;

			return $GLOBALS['shurloc_test_orders'][ $user_id ]
			?? array();
		}
	}
}

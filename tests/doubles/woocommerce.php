<?php
/**
 * WooCommerce test double.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

/**
 * WooCommerce test double.
 */
if ( ! class_exists( 'WooCommerce' ) ) {

	/**
	 * WooCommerce test double.
	 */
	class WooCommerce {

		/**
		 * WooCommerce cart.
		 *
		 * @var WC_Cart
		 */
		public WC_Cart $cart;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->cart = new WC_Cart();
		}
	}
}

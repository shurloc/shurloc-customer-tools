<?php
/**
 * WooCommerce product test double.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

/**
 * WooCommerce product test double.
 */
if ( ! class_exists( 'WC_Product' ) ) {

	/**
	 * WooCommerce product test double.
	 */
	class WC_Product {

		/**
		 * Product SKU.
		 *
		 * @var string
		 */
		private string $sku = '';

		/**
		 * Product name.
		 *
		 * @var string
		 */
		private string $name = '';

		/**
		 * Set the product SKU.
		 *
		 * @param string $sku Product SKU.
		 * @return void
		 */
		public function set_sku(
			string $sku
		): void {

			$this->sku = $sku;
		}

		/**
		 * Get the product SKU.
		 *
		 * @return string
		 */
		public function get_sku(): string {

			return $this->sku;
		}

		/**
		 * Set the product name.
		 *
		 * @param string $name Product name.
		 * @return void
		 */
		public function set_name(
			string $name
		): void {

			$this->name = $name;
		}

		/**
		 * Get the product name.
		 *
		 * @return string
		 */
		public function get_name(): string {

			return $this->name;
		}
	}
}

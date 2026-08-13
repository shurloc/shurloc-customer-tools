<?php
/**
 * Tests for the user cart service.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;
use WC_Product;
use WooCommerce;

/**
 * Tests the user cart service.
 */
final class ShurlocUserCartServiceTest extends TestCase {

	/**
	 * Service under test.
	 *
	 * @var Shurloc_User_Cart_Service
	 */
	private Shurloc_User_Cart_Service $service;

	/**
	 * WooCommerce cart test double.
	 *
	 * @var Shurloc_WC_Cart_Double
	 */
	private Shurloc_WC_Cart_Double $cart;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$GLOBALS['shurloc_test_actions']           = array();
		$GLOBALS['shurloc_test_action_metadata']   = array();
		$GLOBALS['shurloc_test_user_meta']         = array();
		$GLOBALS['shurloc_test_current_user_id']   = 0;
		$GLOBALS['shurloc_test_is_user_logged_in'] = false;
		$GLOBALS['shurloc_test_time']              = 1_000_000;

		$this->cart = new Shurloc_WC_Cart_Double();

		$woocommerce       = new WooCommerce();
		$woocommerce->cart = $this->cart;

		$GLOBALS['shurloc_test_woocommerce'] = $woocommerce;

		$this->service = new Shurloc_User_Cart_Service();
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$GLOBALS['shurloc_test_actions']           = array();
		$GLOBALS['shurloc_test_action_metadata']   = array();
		$GLOBALS['shurloc_test_user_meta']         = array();
		$GLOBALS['shurloc_test_current_user_id']   = 0;
		$GLOBALS['shurloc_test_is_user_logged_in'] = false;
		$GLOBALS['shurloc_test_woocommerce']       = null;

		parent::tearDown();
	}

	/**
	 * Verify cart hooks are registered.
	 *
	 * @return void
	 */
	public function test_register_adds_cart_hooks(): void {

		$this->service->register();

		foreach (
			array(
				'woocommerce_add_to_cart',
				'woocommerce_cart_item_removed',
				'woocommerce_cart_item_restored',
				'woocommerce_after_cart_item_quantity_update',
				'woocommerce_cart_emptied',
			) as $hook
		) {
			self::assertContains(
				array(
					$this->service,
					'capture_cart',
				),
				$GLOBALS['shurloc_test_actions'][ $hook ]
			);

			self::assertSame(
				20,
				$GLOBALS['shurloc_test_action_metadata'][ $hook ][0]['priority']
			);
		}
	}

	/**
	 * Verify logged-out users are ignored.
	 *
	 * @return void
	 */
	public function test_logged_out_user_is_ignored(): void {

		$GLOBALS['shurloc_test_is_user_logged_in'] = false;
		$GLOBALS['shurloc_test_current_user_id']   = 101;

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta']
		);
	}

	/**
	 * Verify an invalid current user ID is ignored.
	 *
	 * @return void
	 */
	public function test_zero_user_id_is_ignored(): void {

		$GLOBALS['shurloc_test_is_user_logged_in'] = true;
		$GLOBALS['shurloc_test_current_user_id']   = 0;

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta']
		);
	}

	/**
	 * Verify a cart snapshot is stored.
	 *
	 * @return void
	 */
	public function test_cart_snapshot_is_stored(): void {

		$this->log_in_user( 101 );

		$product = $this->create_product(
			sku: 'TEST-123',
			name: 'Test Product',
		);

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'product_id'   => 100,
					'variation_id' => 105,
					'quantity'     => 2,
					'data'         => $product,
				),
			)
		);

		$this->cart->set_test_total( 149.95 );

		$this->service->capture_cart();

		self::assertSame(
			array(
				array(
					'product_id'   => 100,
					'variation_id' => 105,
					'qty'          => 2,
					'sku'          => 'TEST-123',
					'name'         => 'Test Product',
					'key'          => 'abc123',
				),
			),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);

		self::assertSame(
			2,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_COUNT_META_KEY ]
		);

		self::assertSame(
			149.95,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_TOTAL_META_KEY ]
		);

		self::assertSame(
			1,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_VERSION_META_KEY ]
		);

		self::assertSame(
			1_000_000,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_UPDATED_META_KEY ]
		);
	}

	/**
	 * Verify multiple cart lines are normalized.
	 *
	 * @return void
	 */
	public function test_multiple_cart_items_are_stored(): void {

		$this->log_in_user( 101 );

		$product_one = $this->create_product(
			sku: 'ONE',
			name: 'Product One',
		);

		$product_two = $this->create_product(
			sku: 'TWO',
			name: 'Product Two',
		);

		$this->cart->set_test_cart(
			array(
				'first'  => array(
					'product_id'   => 100,
					'variation_id' => 0,
					'quantity'     => 1,
					'data'         => $product_one,
				),
				'second' => array(
					'product_id'   => 200,
					'variation_id' => 205,
					'quantity'     => 3,
					'data'         => $product_two,
				),
			)
		);

		$this->service->capture_cart();

		$items = $GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ];

		self::assertCount(
			2,
			$items
		);

		self::assertSame(
			4,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_COUNT_META_KEY ]
		);
	}

	/**
	 * Verify cart version increments on each capture.
	 *
	 * @return void
	 */
	public function test_cart_version_increments(): void {

		$this->log_in_user( 101 );

		$GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Cart_Service::CART_VERSION_META_KEY ] = 5;

		$this->service->capture_cart();

		self::assertSame(
			6,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_VERSION_META_KEY ]
		);
	}

	/**
	 * Verify the updated timestamp changes when the cart is captured.
	 *
	 * @return void
	 */
	public function test_cart_updated_timestamp_is_stored(): void {

		$this->log_in_user( 101 );

		$GLOBALS['shurloc_test_time'] = 2_000_000;

		$this->service->capture_cart();

		self::assertSame(
			2_000_000,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_UPDATED_META_KEY ]
		);
	}

	/**
	 * Verify an empty cart clears the stored snapshot.
	 *
	 * @return void
	 */
	public function test_empty_cart_replaces_previous_snapshot(): void {

		$this->log_in_user( 101 );

		$GLOBALS['shurloc_test_user_meta'][101] = array(
			Shurloc_User_Cart_Service::CART_ITEMS_META_KEY => array(
				array(
					'product_id'   => 100,
					'variation_id' => 0,
					'qty'          => 2,
					'sku'          => 'OLD',
					'name'         => 'Old Product',
					'key'          => 'old-key',
				),
			),
			Shurloc_User_Cart_Service::CART_COUNT_META_KEY => 2,
			Shurloc_User_Cart_Service::CART_TOTAL_META_KEY => 100.00,
			Shurloc_User_Cart_Service::CART_VERSION_META_KEY => 3,
		);

		$this->cart->set_test_cart( array() );
		$this->cart->set_test_total( 0.0 );

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);

		self::assertSame(
			0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_COUNT_META_KEY ]
		);

		self::assertSame(
			0.0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_TOTAL_META_KEY ]
		);

		self::assertSame(
			4,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_VERSION_META_KEY ]
		);
	}

	/**
	 * Verify the checkout empty-cart path stores an empty cart.
	 *
	 * @return void
	 */
	public function test_cart_emptied_hook_captures_empty_cart(): void {

		$this->log_in_user( 101 );

		$product = $this->create_product(
			sku: 'TEST',
			name: 'Test Product',
		);

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'product_id'   => 100,
					'variation_id' => 0,
					'quantity'     => 1,
					'data'         => $product,
				),
			)
		);

		$this->service->capture_cart();

		$this->cart->set_test_cart( array() );
		$this->cart->set_test_total( 0.0 );

		$this->service->register();

		do_action( 'woocommerce_cart_emptied' );

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);

		self::assertSame(
			0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_COUNT_META_KEY ]
		);

		self::assertSame(
			0.0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_TOTAL_META_KEY ]
		);
	}

	/**
	 * Verify cart items without product data are ignored.
	 *
	 * @return void
	 */
	public function test_cart_item_without_product_data_is_ignored(): void {

		$this->log_in_user( 101 );

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'product_id'   => 100,
					'variation_id' => 0,
					'quantity'     => 1,
				),
			)
		);

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);
	}

	/**
	 * Verify cart items with invalid product data are ignored.
	 *
	 * @return void
	 */
	public function test_cart_item_with_invalid_product_data_is_ignored(): void {

		$this->log_in_user( 101 );

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'product_id'   => 100,
					'variation_id' => 0,
					'quantity'     => 1,
					'data'         => new \stdClass(),
				),
			)
		);

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);
	}

	/**
	 * Verify zero-quantity cart items are ignored.
	 *
	 * @return void
	 */
	public function test_zero_quantity_cart_item_is_ignored(): void {

		$this->log_in_user( 101 );

		$product = $this->create_product(
			sku: 'TEST',
			name: 'Test Product',
		);

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'product_id'   => 100,
					'variation_id' => 0,
					'quantity'     => 0,
					'data'         => $product,
				),
			)
		);

		$this->service->capture_cart();

		self::assertSame(
			array(),
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ]
		);
	}

	/**
	 * Verify a missing product ID is normalized to zero.
	 *
	 * @return void
	 */
	public function test_missing_product_id_is_normalized_to_zero(): void {

		$this->log_in_user( 101 );

		$product = $this->create_product(
			sku: 'TEST',
			name: 'Test Product',
		);

		$this->cart->set_test_cart(
			array(
				'abc123' => array(
					'quantity' => 1,
					'data'     => $product,
				),
			)
		);

		$this->service->capture_cart();

		$items = $GLOBALS['shurloc_test_user_meta'][101]
			[ Shurloc_User_Cart_Service::CART_ITEMS_META_KEY ];

		self::assertSame(
			0,
			$items[0]['product_id']
		);

		self::assertSame(
			0,
			$items[0]['variation_id']
		);
	}

	/**
	 * Verify a missing total is normalized to zero.
	 *
	 * @return void
	 */
	public function test_missing_cart_total_is_normalized_to_zero(): void {

		$this->log_in_user( 101 );

		$this->cart->set_test_totals(
			array(
				'subtotal' => 100.00,
			)
		);

		$this->service->capture_cart();

		self::assertSame(
			0.0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_TOTAL_META_KEY ]
		);
	}

	/**
	 * Verify a nonnumeric total is normalized to zero.
	 *
	 * @return void
	 */
	public function test_invalid_cart_total_is_normalized_to_zero(): void {

		$this->log_in_user( 101 );

		$this->cart->set_test_totals(
			array(
				'total' => 'invalid',
			)
		);

		$this->service->capture_cart();

		self::assertSame(
			0.0,
			$GLOBALS['shurloc_test_user_meta'][101]
				[ Shurloc_User_Cart_Service::CART_TOTAL_META_KEY ]
		);
	}

	/**
	 * Log in a test user.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	private function log_in_user(
		int $user_id
	): void {

		$GLOBALS['shurloc_test_is_user_logged_in'] = true;
		$GLOBALS['shurloc_test_current_user_id']   = $user_id;
	}

	/**
	 * Create a product test double.
	 *
	 * @param string $sku  Product SKU.
	 * @param string $name Product name.
	 * @return WC_Product
	 */
	private function create_product(
		string $sku,
		string $name
	): WC_Product {

		$product = new WC_Product();

		$product->set_sku( $sku );
		$product->set_name( $name );

		return $product;
	}
}

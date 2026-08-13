<?php
/**
 * WooCommerce order test double.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

/**
 * WooCommerce order test double.
 */
if ( ! class_exists( 'WC_Order' ) ) {

	/**
	 * WooCommerce order test double.
	 */
	class WC_Order {

		/**
		 * Order ID.
		 *
		 * @var int
		 */
		private int $id;

		/**
		 * Customer ID.
		 *
		 * @var int
		 */
		private int $customer_id = 0;

		/**
		 * Order status.
		 *
		 * @var string
		 */
		private string $status = 'pending';

		/**
		 * Creation date.
		 *
		 * @var DateTimeImmutable|null
		 */
		private ?DateTimeImmutable $date_created = null;

		/**
		 * Order total.
		 *
		 * @var string
		 */
		private string $total = '0';

		/**
		 * Constructor.
		 *
		 * @param int $id Order ID.
		 */
		public function __construct(
			int $id = 0
		) {

			$this->id = $id;
		}

		/**
		 * Get the order ID.
		 *
		 * @return int
		 */
		public function get_id(): int {

			return $this->id;
		}

		/**
		 * Set the customer ID.
		 *
		 * @param int $customer_id Customer ID.
		 * @return void
		 */
		public function set_customer_id(
			int $customer_id
		): void {

			$this->customer_id = $customer_id;
		}

		/**
		 * Get the customer ID.
		 *
		 * @return int
		 */
		public function get_customer_id(): int {

			return $this->customer_id;
		}

		/**
		 * Set the order status.
		 *
		 * @param string $status Order status.
		 * @return void
		 */
		public function set_status(
			string $status
		): void {

			$this->status = $status;
		}

		/**
		 * Get the order status.
		 *
		 * @return string
		 */
		public function get_status(): string {

			return $this->status;
		}

		/**
		 * Set the order creation date.
		 *
		 * @param int|string|null $date Creation date.
		 * @return void
		 */
		public function set_date_created(
			int|string|null $date
		): void {

			if ( null === $date ) {
				$this->date_created = null;
				return;
			}

			if ( is_int( $date ) ) {
				$this->date_created = new DateTimeImmutable(
					'@' . $date
				);

				return;
			}

			$this->date_created = new DateTimeImmutable( $date );
		}

		/**
		 * Get the order creation date.
		 *
		 * @return DateTimeImmutable|null
		 */
		public function get_date_created(): ?DateTimeImmutable {

			return $this->date_created;
		}

		/**
		 * Set the order total.
		 *
		 * @param string $total Order total.
		 * @return void
		 */
		public function set_total(
			string $total
		): void {

			$this->total = $total;
		}


		/**
		 * Get the order total.
		 *
		 * @return string
		 */
		public function get_total(): string {

			return $this->total;
		}
	}
}

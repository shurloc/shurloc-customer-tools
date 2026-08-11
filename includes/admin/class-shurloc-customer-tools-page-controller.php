<?php
/**
 * Customer admin page controller.
 *
 * Provides admin tools for customer functions.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

/**
 * Customer admin page controller.
 */
final class Shurloc_Customer_Tools_Page_Controller implements Shurloc_Admin_Page_Interface {


	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Render the Customer Tools page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		?>

		<div class="wrap">

			<h1>Shur-Loc Customer Tools</h1>

			<p>
				Utilities for customer administration.
			</p>

		</div>

		<?php
	}
}

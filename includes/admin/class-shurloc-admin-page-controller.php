<?php
/**
 * Customer admin page controller.
 *
 * Provides admin tools for customer functions.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use Shurloc\Tools\Shurloc_Admin_Page_Interface;

/**
 * Customer admin page controller.
 *
 * @disregard P1009 Undefined type 'Shurloc\Tools\Shurloc_Admin_Page_Interface'.
 */
final class Shurloc_Admin_Page_Controller implements Shurloc_Admin_Page_Interface {

	/**
	 * Page slug.
	 */
	private const PAGE_SLUG = 'shurloc-customer-tools';

	/**
	 * Migrations controller.
	 *
	 * @var Shurloc_Customer_Migrations_Controller
	 */
	private Shurloc_Customer_Migrations_Controller $migrations_controller;

	/**
	 * Constructor.
	 *
	 * @param Shurloc_Customer_Migrations_Controller $migrations_controller Migrations controller.
	 */
	public function __construct(
		Shurloc_Customer_Migrations_Controller $migrations_controller
	) {

		$this->migrations_controller = $migrations_controller;
	}

	/**
	 * Render the Customer Tools page.
	 *
	 * @return void
	 */
	public function render_page(): void {

		$current_tab = $this->get_current_tab();

		?>
		<div class="wrap">

			<h1>Customer Tools</h1>

			<nav class="nav-tab-wrapper">
				<a
					href="<?php echo esc_url( $this->get_tab_url( 'overview' ) ); ?>"
					class="nav-tab <?php echo 'overview' === $current_tab ? 'nav-tab-active' : ''; ?>"
				>
					Overview
				</a>

				<a
					href="<?php echo esc_url( $this->get_tab_url( 'migrations' ) ); ?>"
					class="nav-tab <?php echo 'migrations' === $current_tab ? 'nav-tab-active' : ''; ?>"
				>
					Migrations
				</a>
			</nav>

			<?php
			if ( 'migrations' === $current_tab ) {
				$this->migrations_controller->render();
			} else {
				$this->render_overview();
			}
			?>

		</div>
		<?php
	}

	/**
	 * Render the overview tab.
	 *
	 * @return void
	 */
	private function render_overview(): void {
		?>
		<p>
			Utilities for customer administration.
		</p>
		<?php
	}

	/**
	 * Get the active tab.
	 *
	 * @return string
	 */
	private function get_current_tab(): string {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['tab'] ) ) {
			return 'overview';
		}

		$tab = sanitize_key(
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_unslash( $_GET['tab'] )
		);

		if (
			in_array(
				$tab,
				array(
					'overview',
					'migrations',
				),
				true
			)
		) {
			return $tab;
		}

		return 'overview';
	}

	/**
	 * Get an admin tab URL.
	 *
	 * @param string $tab Tab slug.
	 * @return string
	 */
	private function get_tab_url(
		string $tab
	): string {

		return add_query_arg(
			array(
				'page' => self::PAGE_SLUG,
				'tab'  => $tab,
			),
			admin_url( 'admin.php' )
		);
	}
}
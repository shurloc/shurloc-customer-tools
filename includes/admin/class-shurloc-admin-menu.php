<?php
/**
 * Customer Tools admin menu.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

/**
 * Registers Customer Tools admin UI.
 */
final class Shurloc_Admin_Menu {

	/**
	 * Parent ShurLoc Tools menu slug.
	 */
	private const PARENT_MENU_SLUG = 'shurloc-tools';

	/**
	 * Customer Tools menu slug.
	 */
	private const CUSTOMER_MENU_SLUG = 'shurloc-customer-tools';

	/**
	 * Required capability.
	 */
	private const CAPABILITY = 'manage_options';

	/**
	 * Customer menu position.
	 */
	private const CUSTOMER_MENU_POSITION = 20;

	/**
	 * Customer page.
	 *
	 * @var \Shurloc_Admin_Page_Interface
	 */
	private \Shurloc_Admin_Page_Interface $customer_page;

	/**
	 * Constructor.
	 *
	 * @param \Shurloc_Admin_Page_Interface $customer_page Customer page.
	 */
	public function __construct(
		\Shurloc_Admin_Page_Interface $customer_page
	) {
		$this->customer_page = $customer_page;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_action(
			'admin_menu',
			array( $this, 'register_menu' ),
			20
		);

		add_action(
			'shurloc_tools_overview',
			array( $this, 'render_overview_section' ),
			self::CUSTOMER_MENU_POSITION
		);
	}

	/**
	 * Register the Customer Tools submenu.
	 *
	 * @return void
	 */
	public function register_menu(): void {

		add_submenu_page(
			self::PARENT_MENU_SLUG,
			'ShurLoc Customer Tools',
			'Customers',
			self::CAPABILITY,
			self::CUSTOMER_MENU_SLUG,
			array( $this->customer_page, 'render_page' ),
			self::CUSTOMER_MENU_POSITION
		);
	}

	/**
	 * Render the Customer Tools overview section.
	 *
	 * @return void
	 */
	public function render_overview_section(): void {
		?>
		<h2>Customers</h2>

		<p>
			Customer tools.
		</p>

		<p>
			<a
				href="<?php echo esc_url( $this->get_customer_tools_url() ); ?>"
				class="button button-primary"
			>
				Open Customer Tools
			</a>
		</p>
		<?php
	}

	/**
	 * Get the Customer Tools admin URL.
	 *
	 * @return string
	 */
	private function get_customer_tools_url(): string {

		return add_query_arg(
			array(
				'page' => self::CUSTOMER_MENU_SLUG,
			),
			admin_url( 'admin.php' )
		);
	}
}
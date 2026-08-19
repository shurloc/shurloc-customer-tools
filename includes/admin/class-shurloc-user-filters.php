<?php
/**
 * Shared user admin filters.
 *
 * Renders the shared Shur-loc Customer Tools filter bar on the WordPress
 * Users screen.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

/**
 * Coordinates Shur-loc Customer Tools user filters.
 */
final class Shurloc_User_Filters {

	/**
	 * Shared filter controls action.
	 *
	 * @var string
	 */
	public const FILTER_CONTROLS_ACTION = 'shurloc_customer_tools_user_filters';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_action(
			'manage_users_extra_tablenav',
			array(
				$this,
				'render_filters',
			),
			10,
			1
		);
	}

	/**
	 * Render the shared Users screen filter bar.
	 *
	 * WordPress fires manage_users_extra_tablenav for both the top and bottom
	 * table controls. Render the shared filter bar only at the top to avoid
	 * duplicate named form controls.
	 *
	 * @param string $which Controls location. Either top or bottom.
	 * @return void
	 */
	public function render_filters(
		string $which
	): void {

		if ( 'top' !== $which ) {
			return;
		}
		?>

		<div class="alignleft actions">

			<?php
			/**
			 * Render Shur-loc Customer Tools user filter controls.
			 *
			 * Feature-specific filter classes should hook here and render
			 * only their controls. This coordinator owns the surrounding
			 * container and shared Filter button.
			 *
			 * Suggested priorities:
			 *
			 * 10 - User activity filters
			 * 20 - User purchase filters
			 */
			do_action( self::FILTER_CONTROLS_ACTION );

			submit_button(
				__(
					'Filter',
					'shurloc-customer-tools'
				),
				'secondary',
				'filter_action',
				false
			);
			?>

		</div>

		<?php
	}
}

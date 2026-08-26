<?php
/**
 * Customer migrations admin controller.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

defined( 'ABSPATH' ) || exit;

/**
 * Renders and processes customer data migrations.
 */
final class Shurloc_Customer_Migrations_Controller {

	/**
	 * Admin page slug.
	 */
	private const PAGE_SLUG = 'shurloc-customer-tools';

	/**
	 * Admin tab slug.
	 */
	private const TAB_SLUG = 'migrations';

	/**
	 * Required capability.
	 */
	private const CAPABILITY = 'manage_options';

	/**
	 * Purchase migration action.
	 */
	private const PURCHASE_ACTION =
		'shurloc_run_purchase_migration';

	/**
	 * Purchase migration.
	 *
	 * @var Shurloc_User_Purchase_Migration
	 */
	private Shurloc_User_Purchase_Migration $purchase_migration;

	/**
	 * Constructor.
	 *
	 * @param Shurloc_User_Purchase_Migration $purchase_migration Purchase migration.
	 */
	public function __construct(
		Shurloc_User_Purchase_Migration $purchase_migration
	) {

		$this->purchase_migration = $purchase_migration;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_action(
			'admin_post_' . self::PURCHASE_ACTION,
			array(
				$this,
				'handle_purchase_migration',
			)
		);

		add_action(
			'admin_enqueue_scripts',
			array(
				$this,
				'enqueue_assets',
			)
		);
	}

	/**
	 * Enqueue migration admin assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {

		if ( ! $this->is_migrations_page() ) {
			return;
		}

		wp_enqueue_script(
			'shurloc-customer-migrations',
			SHURLOC_CUSTOMER_TOOLS_URL .
				'assets/js/customer-migrations.js',
			array(),
			SHURLOC_CUSTOMER_TOOLS_VERSION,
			true
		);
	}

	/**
	 * Render the migrations tab.
	 *
	 * @return void
	 */
	public function render(): void {

		$this->render_result_notice();

		$last_run = $this->purchase_migration->get_last_run();

		$last_run_display = 'Never';

		if ( 0 < $last_run ) {

			$formatted_last_run = wp_date(
				'F j, Y g:i a',
				$last_run
			);

			if ( false !== $formatted_last_run ) {
				$last_run_display = $formatted_last_run;
			}
		}

		$last_run_version =
			$this->purchase_migration->get_last_run_version();

		?>
		<h2>Customer Data Migrations</h2>

		<p>
			Controlled tools for seeding and rebuilding customer
			tracking data.
		</p>

		<div class="card">
			<h2>Purchase Tracking Seeding</h2>

			<p>
				Seeds each registered user's last-purchase data from
				their most recent qualifying WooCommerce order.
			</p>

			<table class="widefat striped">
				<tbody>
					<tr>
						<th scope="row">Current migration version</th>
						<td>
							<?php
							echo esc_html(
								(string) Shurloc_User_Purchase_Migration::VERSION
							);
							?>
						</td>
					</tr>

					<tr>
						<th scope="row">Last-run migration version</th>
						<td>
							<?php
							echo esc_html(
								0 < $last_run_version
									? (string) $last_run_version
									: 'Not recorded'
							);
							?>
						</td>
					</tr>

					<tr>
						<th scope="row">Last run</th>
						<td>
							<?php echo esc_html( $last_run_display ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<form
				method="post"
				action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				class="shurloc-migration-form"
				data-confirm-message="Run the Purchase Tracking migration? This will rebuild purchase tracking data for existing users."
			>
				<input
					type="hidden"
					name="action"
					value="<?php echo esc_attr( self::PURCHASE_ACTION ); ?>"
				/>

				<?php
				wp_nonce_field(
					self::PURCHASE_ACTION
				);
				?>

				<p>
					<label>
						<input
							type="checkbox"
							class="shurloc-migration-enable"
						/>

						Enable this migration
					</label>
				</p>

				<p>
					<button
						type="submit"
						class="button button-primary shurloc-migration-submit"
						disabled
					>
						Run Purchase Migration
					</button>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Process the purchase migration.
	 *
	 * @return void
	 */
	public function handle_purchase_migration(): void {

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die(
				esc_html__(
					'You are not allowed to run this migration.',
					'shurloc-customer-tools'
				)
			);
		}

		check_admin_referer(
			self::PURCHASE_ACTION
		);

		$result = $this->purchase_migration->run();

		$redirect_url = add_query_arg(
			array(
				'page'      => self::PAGE_SLUG,
				'tab'       => self::TAB_SLUG,
				'migration' => 'purchase',
				'examined'  => $result['examined'],
				'updated'   => $result['updated'],
				'skipped'   => $result['skipped'],
				'errors'    => $result['errors'],
				'_wpnonce'  => wp_create_nonce(
					'shurloc_purchase_migration_result'
				),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect(
			$redirect_url
		);

		exit;
	}

	/**
	 * Render the result from the previous migration run.
	 *
	 * @return void
	 */
	private function render_result_notice(): void {

		if (
		! isset(
			$_GET['_wpnonce'],
			$_GET['migration']
		)
		) {
			return;
		}

		$nonce = sanitize_text_field(
			wp_unslash( $_GET['_wpnonce'] )
		);

		if (
		! wp_verify_nonce(
			$nonce,
			'shurloc_purchase_migration_result'
		)
		) {
			return;
		}

		$migration = sanitize_key(
			wp_unslash( $_GET['migration'] )
		);

		if ( 'purchase' !== $migration ) {
			return;
		}

		$examined = isset( $_GET['examined'] )
		? absint( $_GET['examined'] )
		: 0;

		$updated = isset( $_GET['updated'] )
		? absint( $_GET['updated'] )
		: 0;

		$skipped = isset( $_GET['skipped'] )
		? absint( $_GET['skipped'] )
		: 0;

		$errors = isset( $_GET['errors'] )
		? absint( $_GET['errors'] )
		: 0;

		$notice_class = 0 === $errors
		? 'notice notice-success is-dismissible'
		: 'notice notice-warning is-dismissible';

		?>
	<div class="<?php echo esc_attr( $notice_class ); ?>">
		<p>
			<?php
			echo esc_html(
				sprintf(
					'Purchase migration complete. Examined: %d; Updated: %d; Skipped: %d; Errors: %d.',
					$examined,
					$updated,
					$skipped,
					$errors
				)
			);
			?>
		</p>
	</div>
		<?php
	}

	/**
	 * Determine whether the current request is the migrations page.
	 *
	 * @return bool
	 */
	private function is_migrations_page(): bool {

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset( $_GET['page'] )
			? sanitize_key(
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_unslash( $_GET['page'] )
			)
			: '';

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] )
			? sanitize_key(
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_unslash( $_GET['tab'] )
			)
			: '';

		return (
			self::PAGE_SLUG === $page &&
			self::TAB_SLUG === $tab
		);
	}
}

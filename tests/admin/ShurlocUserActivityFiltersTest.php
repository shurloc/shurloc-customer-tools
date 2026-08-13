<?php
/**
 * Tests for user activity admin filters.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

use PHPUnit\Framework\TestCase;
use WP_User_Query;

/**
 * Tests the user activity admin filters.
 */
final class ShurlocUserActivityFiltersTest extends TestCase {

	/**
	 * Filters class under test.
	 *
	 * @var Shurloc_User_Activity_Filters
	 */
	private Shurloc_User_Activity_Filters $filters;

	/**
	 * Prepare each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {

		parent::setUp();

		$_GET = array();

		$GLOBALS['shurloc_test_actions']         = array();
		$GLOBALS['shurloc_test_action_metadata'] = array();
		$GLOBALS['shurloc_test_time']            = 1_000_000;

		$this->filters = new Shurloc_User_Activity_Filters();
	}

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {

		$_GET = array();

		$GLOBALS['shurloc_test_actions']         = array();
		$GLOBALS['shurloc_test_action_metadata'] = array();

		parent::tearDown();
	}

	/**
	 * Verify the Users filter UI hook is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_restrict_manage_users_action(): void {

		$this->filters->register();

		self::assertContains(
			array(
				$this->filters,
				'render_filters',
			),
			$GLOBALS['shurloc_test_actions']['restrict_manage_users']
		);
	}

	/**
	 * Verify the user query hook is registered.
	 *
	 * @return void
	 */
	public function test_register_adds_pre_get_users_action(): void {

		$this->filters->register();

		self::assertContains(
			array(
				$this->filters,
				'modify_user_query',
			),
			$GLOBALS['shurloc_test_actions']['pre_get_users']
		);
	}

	/**
	 * Verify the Last Login filter is rendered.
	 *
	 * @return void
	 */
	public function test_last_login_filter_is_rendered(): void {

		ob_start();

		$this->filters->render_filters();

		$output = ob_get_clean();

		self::assertIsString( $output );

		self::assertStringContainsString(
			'name="shurloc_last_login"',
			$output
		);

		self::assertStringContainsString(
			'Never Logged In',
			$output
		);
	}

	/**
	 * Verify the Last Activity filter is rendered.
	 *
	 * @return void
	 */
	public function test_last_activity_filter_is_rendered(): void {

		ob_start();

		$this->filters->render_filters();

		$output = ob_get_clean();

		self::assertIsString( $output );

		self::assertStringContainsString(
			'name="shurloc_last_activity"',
			$output
		);

		self::assertStringContainsString(
			'Never Active',
			$output
		);
	}

	/**
	 * Verify selected Last Login filter value is preserved.
	 *
	 * @return void
	 */
	public function test_selected_last_login_filter_is_preserved(): void {

		$_GET['shurloc_last_login'] = '7';

		ob_start();

		$this->filters->render_filters();

		$output = ob_get_clean();

		self::assertIsString( $output );

		self::assertMatchesRegularExpression(
			'/value="7"\s+selected="selected"/',
			$output
		);
	}

	/**
	 * Verify selected Last Activity filter value is preserved.
	 *
	 * @return void
	 */
	public function test_selected_last_activity_filter_is_preserved(): void {

		$_GET['shurloc_last_activity'] = '30';

		ob_start();

		$this->filters->render_filters();

		$output = ob_get_clean();

		self::assertIsString( $output );

		self::assertMatchesRegularExpression(
			'/value="30"\s+selected="selected"/',
			$output
		);
	}

	/**
	 * Verify Last Login sorting uses numeric user meta.
	 *
	 * @return void
	 */
	public function test_last_login_sorting_uses_numeric_meta(): void {

		$query = new WP_User_Query(
			array(
				'orderby' => Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
			)
		);

		$this->filters->modify_user_query( $query );

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
			$query->get( 'meta_key' )
		);

		self::assertSame(
			'meta_value_num',
			$query->get( 'orderby' )
		);
	}

	/**
	 * Verify Last Activity sorting uses numeric user meta.
	 *
	 * @return void
	 */
	public function test_last_activity_sorting_uses_numeric_meta(): void {

		$query = new WP_User_Query(
			array(
				'orderby' => Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			)
		);

		$this->filters->modify_user_query( $query );

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$query->get( 'meta_key' )
		);

		self::assertSame(
			'meta_value_num',
			$query->get( 'orderby' )
		);
	}

	/**
	 * Verify unrelated sorting is unchanged.
	 *
	 * @return void
	 */
	public function test_unrelated_sorting_is_unchanged(): void {

		$query = new WP_User_Query(
			array(
				'orderby' => 'login',
			)
		);

		$this->filters->modify_user_query( $query );

		self::assertSame(
			'login',
			$query->get( 'orderby' )
		);

		self::assertNull(
			$query->get( 'meta_key' )
		);
	}

	/**
	 * Verify one-day Last Login filtering.
	 *
	 * @return void
	 */
	public function test_last_login_one_day_filter_adds_meta_query(): void {

		$_GET['shurloc_last_login'] = '1';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		self::assertSame(
			array(
				array(
					'key'     => Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
					'value'   => 913600,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
			),
			$query->get( 'meta_query' )
		);
	}

	/**
	 * Verify seven-day Last Activity filtering.
	 *
	 * @return void
	 */
	public function test_last_activity_seven_day_filter_adds_meta_query(): void {

		$_GET['shurloc_last_activity'] = '7';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		self::assertSame(
			array(
				array(
					'key'     => Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
					'value'   => 395200,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
			),
			$query->get( 'meta_query' )
		);
	}

	/**
	 * Verify thirty-day activity filtering.
	 *
	 * @return void
	 */
	public function test_last_activity_thirty_day_filter_adds_meta_query(): void {

		$GLOBALS['shurloc_test_time'] = 3_000_000;

		$_GET['shurloc_last_activity'] = '30';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		$meta_query = $query->get( 'meta_query' );

		self::assertIsArray( $meta_query );

		self::assertSame(
			408000,
			$meta_query[0]['value']
		);
	}

	/**
	 * Verify Never Logged In matches missing, zero, and empty metadata.
	 *
	 * @return void
	 */
	public function test_never_logged_in_filter_is_defensive(): void {

		$_GET['shurloc_last_login'] = 'never';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		self::assertSame(
			array(
				array(
					'relation' => 'OR',
					array(
						'key'     => Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
						'value'   => 0,
						'compare' => '=',
						'type'    => 'NUMERIC',
					),
					array(
						'key'     => Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
						'value'   => '',
						'compare' => '=',
					),
				),
			),
			$query->get( 'meta_query' )
		);
	}

	/**
	 * Verify Never Active matches missing, zero, and empty metadata.
	 *
	 * @return void
	 */
	public function test_never_active_filter_is_defensive(): void {

		$_GET['shurloc_last_activity'] = 'never';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		$meta_query = $query->get( 'meta_query' );

		self::assertIsArray( $meta_query );

		self::assertSame(
			'OR',
			$meta_query[0]['relation']
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$meta_query[0][0]['key']
		);
	}

	/**
	 * Verify Last Login and Last Activity filters are combined with AND.
	 *
	 * @return void
	 */
	public function test_login_and_activity_filters_are_combined(): void {

		$_GET['shurloc_last_login']    = '7';
		$_GET['shurloc_last_activity'] = '1';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		$meta_query = $query->get( 'meta_query' );

		self::assertIsArray( $meta_query );

		self::assertSame(
			'AND',
			$meta_query['relation']
		);

		self::assertCount(
			3,
			$meta_query
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_LOGIN_META_KEY,
			$meta_query[0]['key']
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$meta_query[1]['key']
		);
	}

	/**
	 * Verify existing meta query clauses are preserved.
	 *
	 * @return void
	 */
	public function test_existing_meta_query_is_preserved(): void {

		$_GET['shurloc_last_activity'] = '7';

		$query = new WP_User_Query(
			array(
				'meta_query' => array(
					array(
						'key'     => 'existing_key',
						'value'   => 'existing_value',
						'compare' => '=',
					),
				),
			)
		);

		$this->filters->modify_user_query( $query );

		$meta_query = $query->get( 'meta_query' );

		self::assertIsArray( $meta_query );

		self::assertSame(
			'existing_key',
			$meta_query[0]['key']
		);

		self::assertSame(
			Shurloc_User_Activity_Service::LAST_ACTIVITY_META_KEY,
			$meta_query[1]['key']
		);

		self::assertSame(
			'AND',
			$meta_query['relation']
		);
	}

	/**
	 * Verify invalid request values are ignored.
	 *
	 * @return void
	 */
	public function test_invalid_filter_value_is_ignored(): void {

		$_GET['shurloc_last_login'] = 'invalid';

		$query = new WP_User_Query();

		$this->filters->modify_user_query( $query );

		self::assertNull(
			$query->get( 'meta_query' )
		);
	}
}

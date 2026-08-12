<?php
/**
 * WordPress function test doubles.
 *
 * @package ShurLocCustomerTools
 */

declare( strict_types=1 );

/**
 * Registered test actions.
 */
$GLOBALS['shurloc_test_actions'] = array();

/**
 * Registered test action metadata.
 */
$GLOBALS['shurloc_test_action_metadata'] = array();

/**
 * Current test user ID.
 */
$GLOBALS['shurloc_test_current_user_id'] = 0;

/**
 * Whether a test user is logged in.
 */
$GLOBALS['shurloc_test_is_user_logged_in'] = false;

/**
 * Stored test user metadata.
 */
$GLOBALS['shurloc_test_user_meta'] = array();

/**
 * Test timestamp.
 *
 * Used by namespaced time() doubles in service tests.
 *
 * @var int
 */
$GLOBALS['shurloc_test_time'] = 0;


if ( ! function_exists( 'add_action' ) ) {

	/**
	 * Register a test action.
	 *
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback.
	 * @param int      $priority      Priority.
	 * @param int      $accepted_args Accepted arguments.
	 * @return true
	 */
	function add_action(
		string $hook,
		$callback,
		int $priority = 10,
		int $accepted_args = 1
	): bool {

		$GLOBALS['shurloc_test_actions'][ $hook ][] = $callback;

		$GLOBALS['shurloc_test_action_metadata'][ $hook ][] = array(
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}


if ( ! function_exists( 'do_action' ) ) {

	/**
	 * Execute registered test actions.
	 *
	 * @param string $hook    Hook name.
	 * @param mixed  ...$args Action arguments.
	 * @return void
	 */
	function do_action(
		string $hook,
		...$args
	): void {

		if ( empty( $GLOBALS['shurloc_test_actions'][ $hook ] ) ) {
			return;
		}

		foreach (
			$GLOBALS['shurloc_test_actions'][ $hook ]
			as $index => $callback
		) {
			$accepted_args =
				$GLOBALS['shurloc_test_action_metadata'][ $hook ][ $index ]['accepted_args']
				?? 1;

			$callback_args = array_slice(
				$args,
				0,
				$accepted_args
			);

			$callback( ...$callback_args );
		}
	}
}


if ( ! function_exists( 'is_user_logged_in' ) ) {

	/**
	 * Determine whether the current test user is logged in.
	 *
	 * @return bool
	 */
	function is_user_logged_in(): bool {

		return $GLOBALS['shurloc_test_is_user_logged_in'];
	}
}


if ( ! function_exists( 'get_current_user_id' ) ) {

	/**
	 * Get the current test user ID.
	 *
	 * @return int
	 */
	function get_current_user_id(): int {

		return $GLOBALS['shurloc_test_current_user_id'];
	}
}


if ( ! function_exists( 'get_user_meta' ) ) {

	/**
	 * Retrieve test user metadata.
	 *
	 * @param int    $user_id User ID.
	 * @param string $key     Metadata key.
	 * @param bool   $single  Whether to return a single value.
	 * @return mixed
	 */
	function get_user_meta(
		int $user_id,
		string $key = '',
		bool $single = false
	) {

		if ( '' === $key ) {
			return $GLOBALS['shurloc_test_user_meta'][ $user_id ] ?? array();
		}

		if (
			! isset(
				$GLOBALS['shurloc_test_user_meta'][ $user_id ][ $key ]
			)
		) {
			return $single
				? ''
				: array();
		}

		$value = $GLOBALS['shurloc_test_user_meta'][ $user_id ][ $key ];

		return $single
			? $value
			: array( $value );
	}
}


if ( ! function_exists( 'update_user_meta' ) ) {

	/**
	 * Update test user metadata.
	 *
	 * @param int    $user_id User ID.
	 * @param string $key     Metadata key.
	 * @param mixed  $value   Metadata value.
	 * @return int|bool
	 */
	function update_user_meta(
		int $user_id,
		string $key,
		$value
	) {

		$GLOBALS['shurloc_test_user_meta'][ $user_id ][ $key ] = $value;

		return true;
	}
}

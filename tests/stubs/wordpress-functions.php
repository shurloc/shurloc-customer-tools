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
 */
$GLOBALS['shurloc_test_time'] = 0;

/**
 * Registered test filters.
 */
$GLOBALS['shurloc_test_filters'] = array();


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
	 * @return bool
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


if ( ! function_exists( '__' ) ) {

	/**
	 * Return translated text unchanged.
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __(
		string $text,
		string $domain = 'default'
	): string {

		unset( $domain );

		return $text;
	}
}


if ( ! function_exists( '_n' ) ) {

	/**
	 * Return singular or plural test text.
	 *
	 * @param string $single Singular text.
	 * @param string $plural Plural text.
	 * @param int    $number Number.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function _n(
		string $single,
		string $plural,
		int $number,
		string $domain = 'default'
	): string {

		unset( $domain );

		return 1 === $number
			? $single
			: $plural;
	}
}


if ( ! function_exists( 'get_option' ) ) {

	/**
	 * Get a test option.
	 *
	 * @param string $option         Option name.
	 * @param mixed  $default_return Default value.
	 * @return mixed
	 */
	function get_option(
		string $option,
		$default_return = false
	) {

		if ( 'date_format' === $option ) {
			return 'F j, Y';
		}

		return $default_return;
	}
}


if ( ! function_exists( 'wp_date' ) ) {

	/**
	 * Format a test timestamp.
	 *
	 * @param string $format    Date format.
	 * @param int    $timestamp Unix timestamp.
	 * @return string
	 */
	function wp_date(
		string $format,
		int $timestamp
	): string {

		return gmdate(
			$format,
			$timestamp
		);
	}
}


if ( ! function_exists( 'add_filter' ) ) {

	/**
	 * Register a test filter.
	 *
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback.
	 * @param int      $priority      Priority.
	 * @param int      $accepted_args Accepted arguments.
	 * @return true
	 */
	function add_filter(
		string $hook,
		$callback,
		int $priority = 10,
		int $accepted_args = 1
	): bool {

		$GLOBALS['shurloc_test_filters'][ $hook ][] = $callback;

		$GLOBALS['shurloc_test_filter_metadata'][ $hook ][] = array(
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}


if ( ! function_exists( 'esc_html' ) ) {

	/**
	 * Escape HTML text.
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	function esc_html(
		string $text
	): string {

		return htmlspecialchars(
			$text,
			ENT_QUOTES | ENT_SUBSTITUTE,
			'UTF-8'
		);
	}
}


if ( ! function_exists( 'sanitize_key' ) ) {

	/**
	 * Sanitize a test key.
	 *
	 * @param string $key Key.
	 * @return string
	 */
	function sanitize_key(
		string $key
	): string {

		$key = strtolower( $key );

		return preg_replace(
			'/[^a-z0-9_\-]/',
			'',
			$key
		) ?? '';
	}
}


if ( ! function_exists( 'wp_unslash' ) ) {

	/**
	 * Remove slashes from a test value.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	function wp_unslash(
		string $value
	): string {

		return stripslashes( $value );
	}
}


if ( ! function_exists( 'esc_attr' ) ) {

	/**
	 * Escape an HTML attribute.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	function esc_attr(
		string $text
	): string {

		return htmlspecialchars(
			$text,
			ENT_QUOTES | ENT_SUBSTITUTE,
			'UTF-8'
		);
	}
}


if ( ! function_exists( 'esc_html__' ) ) {

	/**
	 * Translate and escape text.
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function esc_html__(
		string $text,
		string $domain = 'default'
	): string {

		unset( $domain );

		return htmlspecialchars(
			$text,
			ENT_QUOTES | ENT_SUBSTITUTE,
			'UTF-8'
		);
	}
}


if ( ! function_exists( 'selected' ) ) {

	/**
	 * Render a selected HTML attribute.
	 *
	 * @param mixed $selected Selected value.
	 * @param mixed $current  Current value.
	 * @param bool  $display  Whether to echo the result.
	 * @return string
	 */
	function selected(
		$selected,
		$current = true,
		bool $display = true
	): string {

		$result = $selected === $current
			? ' selected="selected"'
			: '';

		if ( $display ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed test-only HTML attribute.
			echo $result;
		}

		return $result;
	}

	if ( ! function_exists( 'submit_button' ) ) {

		/**
		 * Render a submit button.
		 *
		 * Test replacement for submit_button().
		 *
		 * @param string              $text             Button text.
		 * @param string              $type             Button type.
		 * @param string              $name             Button name.
		 * @param bool                $wrap             Whether to wrap the button.
		 * @param array<string,mixed> $other_attributes Additional attributes.
		 * @return void
		 */
		function submit_button(
			string $text = 'Save Changes',
			string $type = 'primary',
			string $name = 'submit',
			bool $wrap = true,
			array $other_attributes = array()
		): void {

			unset( $other_attributes );

			$button = sprintf(
				'<input type="submit" name="%s" class="button %s" value="%s" />',
				esc_attr( $name ),
				esc_attr( 'button-' . $type ),
				esc_attr( $text )
			);

			if ( $wrap ) {
				printf(
					'<p class="submit">%s</p>',
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Test-only HTML generated from escaped values.
					$button
				);

				return;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Test-only HTML generated from escaped values.
			echo $button;
		}
	}
}


if ( ! function_exists( 'wp_strip_all_tags' ) ) {

	/**
	 * Strip all HTML tags from a string.
	 *
	 * Test replacement for wp_strip_all_tags().
	 *
	 * @param string $text          Text containing HTML.
	 * @param bool   $remove_breaks Whether to remove line breaks and tabs.
	 * @return string
	 */
	function wp_strip_all_tags(
		string $text,
		bool $remove_breaks = false
	): string {

		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- Test double for wp_strip_all_tags().
		$text = strip_tags( $text );

		if ( $remove_breaks ) {
			$text = preg_replace(
				'/[\r\n\t ]+/',
				' ',
				$text
			);

			if ( null === $text ) {
				return '';
			}
		}

		return trim( $text );
	}
}

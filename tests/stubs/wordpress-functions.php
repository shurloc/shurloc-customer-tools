<?php
/**
 * WordPress function test doubles.
 *
 * @package ShurlocCustomerTools
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

/**
 * Test permalinks indexed by post ID.
 */
$GLOBALS['shurloc_test_permalinks'] = array();

/**
 * Enqueued test styles.
 */
$GLOBALS['shurloc_test_enqueued_styles'] = array();

/**
 * Enqueued test scripts.
 */
$GLOBALS['shurloc_test_enqueued_scripts'] = array();

/**
 * Registered test filter metadata.
 */
$GLOBALS['shurloc_test_filter_metadata'] = array();


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


if ( ! function_exists( 'admin_url' ) ) {

	/**
	 * Get an admin URL.
	 *
	 * Test replacement for admin_url().
	 *
	 * @param string $path Admin path.
	 * @return string
	 */
	function admin_url(
		string $path = ''
	): string {

		return 'https://example.com/wp-admin/' . ltrim( $path, '/' );
	}
}


if ( ! function_exists( 'add_query_arg' ) ) {

	/**
	 * Add query arguments to a URL.
	 *
	 * Test replacement for add_query_arg().
	 *
	 * @param array<string,int|string> $args Query arguments.
	 * @param string                   $url  Base URL.
	 * @return string
	 */
	function add_query_arg(
		array $args,
		string $url
	): string {

		if ( empty( $args ) ) {
			return $url;
		}

		$separator = str_contains( $url, '?' )
			? '&'
			: '?';

		return $url
			. $separator
			. http_build_query( $args );
	}
}


if ( ! function_exists( 'esc_url' ) ) {

	/**
	 * Escape a URL.
	 *
	 * Test replacement for esc_url().
	 *
	 * @param string $url URL.
	 * @return string
	 */
	function esc_url(
		string $url
	): string {

		return $url;
	}
}


if ( ! function_exists( 'esc_html' ) ) {

	/**
	 * Escape HTML text.
	 *
	 * Test replacement for esc_html().
	 *
	 * @param string $text Text.
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


if ( ! function_exists( '__' ) ) {

	/**
	 * Return translated text unchanged.
	 *
	 * Test replacement for __().
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


if ( ! function_exists( 'esc_html__' ) ) {

	/**
	 * Translate and escape HTML text.
	 *
	 * Test replacement for esc_html__().
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

		return esc_html( $text );
	}
}


/**
 * Delete user metadata.
 *
 * Test replacement for delete_user_meta().
 *
 * @param int    $user_id  User ID.
 * @param string $meta_key Metadata key.
 * @return bool
 */
function delete_user_meta(
	int $user_id,
	string $meta_key
): bool {

	if (
		! isset( $GLOBALS['shurloc_test_user_meta'][ $user_id ] ) ||
		! is_array( $GLOBALS['shurloc_test_user_meta'][ $user_id ] )
	) {
		return false;
	}

	if (
		! array_key_exists(
			$meta_key,
			$GLOBALS['shurloc_test_user_meta'][ $user_id ]
		)
	) {
		return false;
	}

	unset(
		$GLOBALS['shurloc_test_user_meta'][ $user_id ][ $meta_key ]
	);

	if (
		empty(
			$GLOBALS['shurloc_test_user_meta'][ $user_id ]
		)
	) {
		unset(
			$GLOBALS['shurloc_test_user_meta'][ $user_id ]
		);
	}

	return true;
}


if ( ! function_exists( 'esc_attr__' ) ) {

	/**
	 * Translate and escape an HTML attribute.
	 *
	 * Test replacement for esc_attr__().
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function esc_attr__(
		string $text,
		string $domain = 'default'
	): string {

		unset( $domain );

		return esc_attr( $text );
	}
}


if ( ! function_exists( 'wp_kses_post' ) ) {

	/**
	 * Sanitize HTML using the allowed post HTML rules.
	 *
	 * Test replacement for wp_kses_post().
	 *
	 * @param string $data HTML.
	 * @return string
	 */
	function wp_kses_post(
		string $data
	): string {

		return $data;
	}
}


if ( ! function_exists( 'get_permalink' ) ) {

	/**
	 * Get a test permalink.
	 *
	 * Test replacement for get_permalink().
	 *
	 * @param int $post_id Post ID.
	 * @return string|false
	 */
	function get_permalink(
		int $post_id
	): string|false {

		if (
			! isset(
				$GLOBALS['shurloc_test_permalinks'][ $post_id ]
			)
		) {
			return false;
		}

		$permalink =
			$GLOBALS['shurloc_test_permalinks'][ $post_id ];

		return is_string( $permalink )
			? $permalink
			: false;
	}
}


if ( ! function_exists( 'wp_enqueue_style' ) ) {

	/**
	 * Enqueue a test stylesheet.
	 *
	 * Test replacement for wp_enqueue_style().
	 *
	 * @param string   $handle Style handle.
	 * @param string   $src    Stylesheet URL.
	 * @param string[] $deps   Dependencies.
	 * @param string   $ver    Version.
	 * @param string   $media  Media type.
	 * @return void
	 */
	function wp_enqueue_style(
		string $handle,
		string $src = '',
		array $deps = array(),
		string $ver = '',
		string $media = 'all'
	): void {

		$GLOBALS['shurloc_test_enqueued_styles'][] = array(
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media,
		);
	}
}


if ( ! function_exists( 'wp_enqueue_script' ) ) {

	/**
	 * Enqueue a test script.
	 *
	 * Test replacement for wp_enqueue_script().
	 *
	 * @param string   $handle    Script handle.
	 * @param string   $src       Script URL.
	 * @param string[] $deps      Dependencies.
	 * @param string   $ver       Version.
	 * @param bool     $in_footer Whether to enqueue in the footer.
	 * @return void
	 */
	function wp_enqueue_script(
		string $handle,
		string $src = '',
		array $deps = array(),
		string $ver = '',
		bool $in_footer = false
	): void {

		$GLOBALS['shurloc_test_enqueued_scripts'][] = array(
			'handle'    => $handle,
			'src'       => $src,
			'deps'      => $deps,
			'ver'       => $ver,
			'in_footer' => $in_footer,
		);
	}
}


if ( ! function_exists( 'plugin_dir_path' ) ) {

	/**
	 * Get the filesystem directory path for a plugin file.
	 *
	 * Test replacement for plugin_dir_path().
	 *
	 * @param string $file Plugin file path.
	 * @return string
	 */
	function plugin_dir_path(
		string $file
	): string {

		return trailingslashit(
			dirname( $file )
		);
	}
}


if ( ! function_exists( 'plugin_dir_url' ) ) {

	/**
	 * Get the URL for a plugin file's directory.
	 *
	 * Test replacement for plugin_dir_url().
	 *
	 * @param string $file Plugin file path.
	 * @return string
	 */
	function plugin_dir_url(
		string $file
	): string {

		unset( $file );

		return 'https://example.com/wp-content/plugins/shurloc-customer-tools/';
	}
}


if ( ! function_exists( 'trailingslashit' ) ) {

	/**
	 * Add a trailing slash to a string.
	 *
	 * Test replacement for trailingslashit().
	 *
	 * @param string $value Value.
	 * @return string
	 */
	function trailingslashit(
		string $value
	): string {

		return rtrim(
			$value,
			'/\\'
		) . '/';
	}
}

if ( ! function_exists( 'add_submenu_page' ) ) {
	/**
	 * Adds a submenu page.
	 *
	 * @param string          $parent_slug Parent menu slug.
	 * @param string          $page_title  Page title.
	 * @param string          $menu_title  Menu title.
	 * @param string          $capability  Required capability.
	 * @param string          $menu_slug   Menu slug.
	 * @param callable|string $callback    Page callback.
	 * @param int|null        $position    Menu position.
	 * @return string Hook suffix.
	 */
	function add_submenu_page(
		string $parent_slug,
		string $page_title,
		string $menu_title,
		string $capability,
		string $menu_slug,
		callable|string $callback = '',
		?int $position = null
	): string {
		$GLOBALS['shurloc_test_submenu_pages'][] = array(
			'parent_slug' => $parent_slug,
			'page_title'  => $page_title,
			'menu_title'  => $menu_title,
			'capability'  => $capability,
			'menu_slug'   => $menu_slug,
			'callback'    => $callback,
			'position'    => $position,
		);

		return $parent_slug . '_page_' . $menu_slug;
	}
}

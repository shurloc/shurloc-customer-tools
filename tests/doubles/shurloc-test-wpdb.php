<?php
/**
 * WordPress database test double.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

/**
 * Test WordPress database implementation.
 */
final class Shurloc_Test_WPDB {

	/**
	 * WordPress database table prefix.
	 *
	 * @var string
	 */
	public string $prefix = 'wp_';

	/**
	 * Rows returned by get_results().
	 *
	 * @var array<int,object>
	 */
	public array $results = array();

	/**
	 * Recorded prepared queries.
	 *
	 * @var array<int,array{
	 *     query:string,
	 *     args:array<int|string,mixed>
	 * }>
	 */
	public array $prepared_queries = array();

	/**
	 * Recorded result queries.
	 *
	 * @var string[]
	 */
	public array $result_queries = array();

	/**
	 * Prepare a test SQL query.
	 *
	 * Test replacement for wpdb::prepare().
	 *
	 * @param string $query   SQL query.
	 * @param mixed  ...$args Query arguments.
	 * @return string
	 */
	public function prepare(
		string $query,
		...$args
	): string {

		$this->prepared_queries[] = array(
			'query' => $query,
			'args'  => $args,
		);

		$prepared = $query;

		foreach ( $args as $arg ) {

			$prepared = preg_replace(
				'/%[sdiF]/',
				(string) $arg,
				$prepared,
				1
			) ?? $prepared;
		}

		return $prepared;
	}

	/**
	 * Retrieve test database rows.
	 *
	 * Test replacement for wpdb::get_results().
	 *
	 * @param string $query SQL query.
	 * @return array<int,object>
	 */
	public function get_results(
		string $query
	): array {

		$this->result_queries[] = $query;

		return $this->results;
	}
}

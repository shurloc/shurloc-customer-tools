<?php
/**
 * Namespaced functions for testing purposes.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

namespace Shurloc\CustomerTools;

/**
 * Test timestamp returned by the namespaced time() double.
 */
$GLOBALS['shurloc_test_time'] = 0;

/**
 * Test replacement for time().
 *
 * The production service calls time() from this namespace, allowing this
 * double to provide deterministic timestamps during tests.
 *
 * @return int
 */
function time(): int {

	return $GLOBALS['shurloc_test_time'];
}

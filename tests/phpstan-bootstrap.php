<?php
/**
 * PHPStan bootstrap.
 *
 * @package ShurlocCustomerTools
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../' );
}

if ( ! defined( 'SHURLOC_CUSTOMER_TOOLS_VERSION' ) ) {
	define(
		'SHURLOC_CUSTOMER_TOOLS_VERSION',
		'0.1.0'
	);
}

if ( ! defined( 'SHURLOC_CUSTOMER_TOOLS_PATH' ) ) {
	define(
		'SHURLOC_CUSTOMER_TOOLS_PATH',
		__DIR__ . '/'
	);
}

if ( ! defined( 'SHURLOC_CUSTOMER_TOOLS_URL' ) ) {
	define(
		'SHURLOC_CUSTOMER_TOOLS_URL',
		'https://example.com/wp-content/plugins/shurloc-customer-tools/'
	);
}

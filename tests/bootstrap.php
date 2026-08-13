<?php
/**
 * PHPUnit bootstrap.
 *
 * @package ShurlocCustomerTools
 */

use Shurloc\CustomerTools\Shurloc_Autoloader;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
}

/**
 * Load Composer's autoloader.
 */
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

/*
 * Load plugin autoloader.
 *
 * The autoloader cannot load itself, so this remains a manual include.
 */
require_once dirname( __DIR__ ) . '/includes/class-shurloc-autoloader.php';

$shurloc_autoloader = new Shurloc_Autoloader(
	dirname( __DIR__ ) . '/includes'
);

$shurloc_autoloader->register();

/**
 * Load dependencies from shurloc-tools.
 */
require_once dirname( __DIR__, 2 ) . '/shurloc-tools/includes/interfaces/interface-shurloc-admin-page.php';

/**
 * Load stubs and test doubles.
 */
require_once __DIR__ . '/stubs/namespaced-functions.php';
require_once __DIR__ . '/stubs/wordpress-functions.php';
require_once __DIR__ . '/stubs/woocommerce-functions.php';

require_once __DIR__ . '/doubles/wp-user.php';
require_once __DIR__ . '/doubles/wp-user-query.php';
require_once __DIR__ . '/doubles/wc-product.php';
require_once __DIR__ . '/doubles/wc-order.php';
require_once __DIR__ . '/doubles/wc-cart.php';
require_once __DIR__ . '/doubles/woocommerce.php';

require_once __DIR__ . '/doubles/shurloc-wc-cart-double.php';

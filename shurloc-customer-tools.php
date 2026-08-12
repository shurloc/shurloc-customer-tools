<?php
/**
 * Plugin Name:       Shur-Loc Customer Tools
 * Plugin URI:        https://shurloc.com/
 * Description:       Customer tools for the Shur-Loc website.
 * Version:           0.1.1
 * Requires at least: 7.0
 * Requires PHP:      8.4
 * Requires Plugins:  woocommerce, shurloc-tools
 * Author:            Shur-Loc
 * Author URI:        https://shurloc.com/
 * Text Domain:       shurloc-customer-tools
 *
 * @package ShurlocCustomerTools
 */

namespace Shurloc\CustomerTools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/constants.php';
require_once __DIR__ . '/includes/bootstrap.php';

<?php
/**
 * Plugin Name:       Shur-loc Customer Tools
 * Plugin URI:        https://github.com/shurloc/shurloc-customer-tools
 * Description:       Customer tools for the Shur-loc website.
 * Version:           0.6.2
 * Requires at least: 7.0
 * Requires PHP:      8.4
 * Requires Plugins:  woocommerce, shurloc-tools
 * Author:            Shur-loc
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

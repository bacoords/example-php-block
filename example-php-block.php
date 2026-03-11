<?php
/**
 * Plugin Name: Example PHP Block
 * Description: Demonstrates PHP-only block registration in WordPress 7.0+
 * Version: 1.0.0
 * Requires at least: 7.0
 * Author: Developer
 *
 * @package example-php-block
 */

defined( 'ABSPATH' ) || exit;

define( 'EPB_VERSION', '1.0.0' );
define( 'EPB_DIR', plugin_dir_path( __FILE__ ) );
define( 'EPB_URL', plugin_dir_url( __FILE__ ) );

// Load block registrations.
require_once EPB_DIR . 'basic-block/register.php';

/**
 * Example of hooking into a plugin-specific action to register blocks conditionally.
 * This ensures that the WooCommerce block is only registered if WooCommerce is active.
 */
function epb_woocommerce_loaded() {
	require_once EPB_DIR . 'woo-block/register.php';
	// Custom code here. WooCommerce is active and all plugins have been loaded...
}
add_action( 'woocommerce_loaded', 'epb_woocommerce_loaded' );

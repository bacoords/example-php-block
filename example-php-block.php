<?php
/**
 * Plugin Name: Example PHP Block
 * Description: Demonstrates PHP-only block registration in WordPress 7.0+
 * Version: 1.0.0
 * Requires at least: 7.0
 * Author: Developer
 */

defined( 'ABSPATH' ) || exit;

define( 'EPB_VERSION', '1.0.0' );
define( 'EPB_DIR', plugin_dir_path( __FILE__ ) );
define( 'EPB_URL', plugin_dir_url( __FILE__ ) );

// Load block registrations.
require_once EPB_DIR . 'basic-block/register.php';
require_once EPB_DIR . 'woo-block/register.php';

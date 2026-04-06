<?php
/**
 * Plugin Name: Hvar Bookings
 * Plugin URI:  https://hvar-excursions.test
 * Description: Internal booking calendar foundation for boats, transfers, excursions, and taxi dispatch.
 * Version:     0.1.0
 * Author:      Codex for Hvar Excursions
 * Author URI:  https://hvar-excursions.test
 * License:     GPL-2.0-or-later
 * Text Domain: hvar-bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEX_BOOKINGS_VERSION', '0.1.0' );
define( 'HEX_BOOKINGS_FILE', __FILE__ );
define( 'HEX_BOOKINGS_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEX_BOOKINGS_URL', plugin_dir_url( __FILE__ ) );

require_once HEX_BOOKINGS_DIR . 'includes/class-hvar-bookings-installer.php';
require_once HEX_BOOKINGS_DIR . 'includes/class-hvar-bookings-documents.php';
require_once HEX_BOOKINGS_DIR . 'includes/class-hvar-bookings-rest-controller.php';
require_once HEX_BOOKINGS_DIR . 'includes/class-hvar-bookings-screen.php';
require_once HEX_BOOKINGS_DIR . 'includes/class-hvar-bookings-plugin.php';

register_activation_hook( HEX_BOOKINGS_FILE, array( 'Hex_Bookings_Installer', 'activate' ) );
register_deactivation_hook( HEX_BOOKINGS_FILE, array( 'Hex_Bookings_Installer', 'deactivate' ) );

Hex_Bookings_Plugin::instance();

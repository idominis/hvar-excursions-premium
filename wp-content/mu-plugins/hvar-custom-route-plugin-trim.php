<?php
/**
 * Plugin Name: Hvar Custom Route Plugin Trim
 * Description: Disables heavy plugins only on the custom Hvar landing routes to reduce WordPress bootstrap overhead.
 */

if ( ! function_exists( 'hvar_custom_route_trim_get_request_path' ) ) {
	function hvar_custom_route_trim_get_request_path() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
		$path        = (string) wp_parse_url( $request_uri, PHP_URL_PATH );

		return trim( $path, '/' );
	}
}

if ( ! function_exists( 'hvar_custom_route_trim_is_target_route' ) ) {
	function hvar_custom_route_trim_is_target_route() {
		return in_array(
			hvar_custom_route_trim_get_request_path(),
			array(
				'',
				'rentals',
				'excursions',
				'service-plus',
				'transfers',
				'online-booking',
				'taxi-and-speedboat-transfers',
				'contacts',
				'contact',
			),
			true
		);
	}
}

if ( ! function_exists( 'hvar_custom_route_trim_filter_plugins' ) ) {
	function hvar_custom_route_trim_filter_plugins( $plugins ) {
		if ( ! hvar_custom_route_trim_is_target_route() ) {
			return $plugins;
		}

		$blocked_plugins = array(
			'advanced-popups/advanced-popups.php',
			'contact-form-7/wp-contact-form-7.php',
			'elementor/elementor.php',
			'instagram-feed/instagram-feed.php',
			'mailchimp-for-wp/mailchimp-for-wp.php',
			'quickcal/quickcal.php',
			'revslider/revslider.php',
			'the-events-calendar/the-events-calendar.php',
			'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
			'trx_addons/trx_addons.php',
			'trx_updater/trx_updater.php',
			'woocommerce/woocommerce.php',
		);

		$filtered_plugins = array_values( array_diff( (array) $plugins, $blocked_plugins ) );

		$GLOBALS['hvar_custom_route_trim_disabled_plugins'] = array_values( array_intersect( (array) $plugins, $blocked_plugins ) );

		return $filtered_plugins;
	}

	add_filter( 'option_active_plugins', 'hvar_custom_route_trim_filter_plugins', 1 );
}

if ( ! function_exists( 'hvar_custom_route_trim_debug_header' ) ) {
	function hvar_custom_route_trim_debug_header() {
		if ( ! hvar_custom_route_trim_is_target_route() || headers_sent() ) {
			return;
		}

		$disabled_plugins = isset( $GLOBALS['hvar_custom_route_trim_disabled_plugins'] )
			? (array) $GLOBALS['hvar_custom_route_trim_disabled_plugins']
			: array();

		header( 'X-Hvar-Trimmed-Plugins: ' . count( $disabled_plugins ) );
	}

	add_action( 'send_headers', 'hvar_custom_route_trim_debug_header' );
}

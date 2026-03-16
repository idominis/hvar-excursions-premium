<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'catamaran_instagram_feed_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'catamaran_instagram_feed_theme_setup9', 9 );
	function catamaran_instagram_feed_theme_setup9() {
		if ( catamaran_exists_instagram_feed() ) {
			add_action( 'wp_enqueue_scripts', 'catamaran_instagram_feed_frontend_scripts_responsive', 2000 );
			add_action( 'trx_addons_action_load_scripts_front_instagram_feed', 'catamaran_instagram_feed_frontend_scripts_responsive', 10, 1 );
			add_filter( 'catamaran_filter_merge_styles_responsive', 'catamaran_instagram_merge_styles_responsive' );
		}
		if ( is_admin() ) {
			add_filter( 'catamaran_filter_tgmpa_required_plugins', 'catamaran_instagram_feed_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'catamaran_instagram_feed_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('catamaran_filter_tgmpa_required_plugins',	'catamaran_instagram_feed_tgmpa_required_plugins');
	function catamaran_instagram_feed_tgmpa_required_plugins( $list = array() ) {
		if ( catamaran_storage_isset( 'required_plugins', 'instagram-feed' ) && catamaran_storage_get_array( 'required_plugins', 'instagram-feed', 'install' ) !== false ) {
			$list[] = array(
				'name'     => catamaran_storage_get_array( 'required_plugins', 'instagram-feed', 'title' ),
				'slug'     => 'instagram-feed',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if Instagram Feed installed and activated
if ( ! function_exists( 'catamaran_exists_instagram_feed' ) ) {
	function catamaran_exists_instagram_feed() {
		return defined( 'SBIVER' );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'catamaran_instagram_feed_frontend_scripts_responsive' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'catamaran_instagram_feed_frontend_scripts_responsive', 2000 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_instagram_feed', 'catamaran_instagram_feed_frontend_scripts_responsive', 10, 1 );
	function catamaran_instagram_feed_frontend_scripts_responsive( $force = false ) {
		catamaran_enqueue_optimized_responsive( 'instagram_feed', $force, array(
			'css' => array(
				'catamaran-instagram-feed-responsive' => array( 'src' => 'plugins/instagram-feed/instagram-feed-responsive.css', 'media' => 'all' ),
			)
		) );
	}
}

// Merge responsive styles
if ( ! function_exists( 'catamaran_instagram_merge_styles_responsive' ) ) {
	//Handler of the add_filter('catamaran_filter_merge_styles_responsive', 'catamaran_instagram_merge_styles_responsive');
	function catamaran_instagram_merge_styles_responsive( $list ) {
		$list[ 'plugins/instagram/instagram-responsive.css' ] = false;
		return $list;
	}
}

<?php
/* QuickCal support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'catamaran_quickcal_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'catamaran_quickcal_theme_setup9', 9 );
	function catamaran_quickcal_theme_setup9() {
		if ( catamaran_exists_quickcal() ) {
			add_action( 'wp_enqueue_scripts', 'catamaran_quickcal_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_quickcal', 'catamaran_quickcal_frontend_scripts', 10, 1 );
			add_action( 'wp_enqueue_scripts', 'catamaran_quickcal_frontend_scripts_responsive', 2000 );
			add_action( 'trx_addons_action_load_scripts_front_quickcal', 'catamaran_quickcal_frontend_scripts_responsive', 10, 1 );
			add_filter( 'catamaran_filter_merge_styles', 'catamaran_quickcal_merge_styles' );
			add_filter( 'catamaran_filter_merge_styles_responsive', 'catamaran_quickcal_merge_styles_responsive' );
		}
		if ( is_admin() ) {
			add_filter( 'catamaran_filter_tgmpa_required_plugins', 'catamaran_quickcal_tgmpa_required_plugins' );
			add_filter( 'catamaran_filter_theme_plugins', 'catamaran_quickcal_theme_plugins' );
		}
	}
}


// Filter to add in the required plugins list
if ( ! function_exists( 'catamaran_quickcal_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('catamaran_filter_tgmpa_required_plugins',	'catamaran_quickcal_tgmpa_required_plugins');
	function catamaran_quickcal_tgmpa_required_plugins( $list = array() ) {
		if ( catamaran_storage_isset( 'required_plugins', 'quickcal' ) && catamaran_storage_get_array( 'required_plugins', 'quickcal', 'install' ) !== false && catamaran_is_theme_activated() ) {
			$path = catamaran_get_plugin_source_path( 'plugins/quickcal/quickcal.zip' );
			if ( ! empty( $path ) || catamaran_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => catamaran_storage_get_array( 'required_plugins', 'quickcal', 'title' ),
					'slug'     => 'quickcal',
					'source'   => ! empty( $path ) ? $path : 'upload://quickcal.zip',
					'version'  => '1.0.6',
					'required' => false,
				);
			}
		}
		return $list;
	}
}


// Filter theme-supported plugins list
if ( ! function_exists( 'catamaran_quickcal_theme_plugins' ) ) {
	//Handler of the add_filter( 'catamaran_filter_theme_plugins', 'catamaran_quickcal_theme_plugins' );
	function catamaran_quickcal_theme_plugins( $list = array() ) {
		return catamaran_add_group_and_logo_to_slave( $list, 'quickcal', 'quickcal-' );
	}
}


// Check if plugin installed and activated
if ( ! function_exists( 'catamaran_exists_quickcal' ) ) {
	function catamaran_exists_quickcal() {
		return class_exists( 'quickcal_plugin' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'catamaran_quickcal_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'catamaran_quickcal_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_quickcal', 'catamaran_quickcal_frontend_scripts', 10, 1 );
	function catamaran_quickcal_frontend_scripts( $force = false ) {
		catamaran_enqueue_optimized( 'quickcal', $force, array(
			'css' => array(
				'catamaran-quickcal' => array( 'src' => 'plugins/quickcal/quickcal.css' ),
			)
		) );
	}
}


// Enqueue responsive styles for frontend
if ( ! function_exists( 'catamaran_quickcal_frontend_scripts_responsive' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'catamaran_quickcal_frontend_scripts_responsive', 2000 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_quickcal', 'catamaran_quickcal_frontend_scripts_responsive', 10, 1 );
	function catamaran_quickcal_frontend_scripts_responsive( $force = false ) {
		catamaran_enqueue_optimized_responsive( 'quickcal', $force, array(
			'css' => array(
				'catamaran-quickcal-responsive' => array( 'src' => 'plugins/quickcal/quickcal-responsive.css', 'media' => 'all' ),
			)
		) );
	}
}


// Merge custom styles
if ( ! function_exists( 'catamaran_quickcal_merge_styles' ) ) {
	//Handler of the add_filter('catamaran_filter_merge_styles', 'catamaran_quickcal_merge_styles');
	function catamaran_quickcal_merge_styles( $list ) {
		$list[ 'plugins/quickcal/quickcal.css' ] = false;
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'catamaran_quickcal_merge_styles_responsive' ) ) {
	//Handler of the add_filter('catamaran_filter_merge_styles_responsive', 'catamaran_quickcal_merge_styles_responsive');
	function catamaran_quickcal_merge_styles_responsive( $list ) {
		$list[ 'plugins/quickcal/quickcal-responsive.css' ] = false;
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( catamaran_exists_quickcal() ) {
	$catamaran_fdir = catamaran_get_file_dir( 'plugins/quickcal/quickcal-style.php' );
	if ( ! empty( $catamaran_fdir ) ) {
		require_once $catamaran_fdir;
	}
}

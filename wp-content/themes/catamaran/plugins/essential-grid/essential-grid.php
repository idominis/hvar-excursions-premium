<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'catamaran_essential_grid_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'catamaran_essential_grid_theme_setup9', 9 );
	function catamaran_essential_grid_theme_setup9() {
		if ( catamaran_exists_essential_grid() ) {
			add_action( 'wp_enqueue_scripts', 'catamaran_essential_grid_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_essential_grid', 'catamaran_essential_grid_frontend_scripts', 10, 1 );
			add_filter( 'catamaran_filter_merge_styles', 'catamaran_essential_grid_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'catamaran_filter_tgmpa_required_plugins', 'catamaran_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'catamaran_essential_grid_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('catamaran_filter_tgmpa_required_plugins',	'catamaran_essential_grid_tgmpa_required_plugins');
	function catamaran_essential_grid_tgmpa_required_plugins( $list = array() ) {
		if ( catamaran_storage_isset( 'required_plugins', 'essential-grid' ) && catamaran_storage_get_array( 'required_plugins', 'essential-grid', 'install' ) !== false && catamaran_is_theme_activated() ) {
			$path = catamaran_get_plugin_source_path( 'plugins/essential-grid/essential-grid.zip' );
			if ( ! empty( $path ) || catamaran_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => catamaran_storage_get_array( 'required_plugins', 'essential-grid', 'title' ),
					'slug'     => 'essential-grid',
					'source'   => ! empty( $path ) ? $path : 'upload://essential-grid.zip',
					'version'  => '2.2.4.2',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'catamaran_exists_essential_grid' ) ) {
	function catamaran_exists_essential_grid() {
		return defined( 'EG_PLUGIN_PATH' ) || defined( 'ESG_PLUGIN_PATH' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'catamaran_essential_grid_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'catamaran_essential_grid_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_essential_grid', 'catamaran_essential_grid_frontend_scripts', 10, 1 );
	function catamaran_essential_grid_frontend_scripts( $force = false ) {
		catamaran_enqueue_optimized( 'essential_grid', $force, array(
			'css' => array(
				'catamaran-essential-grid' => array( 'src' => 'plugins/essential-grid/essential-grid.css' ),
			)
		) );
	}
}

// Merge custom styles
if ( ! function_exists( 'catamaran_essential_grid_merge_styles' ) ) {
	//Handler of the add_filter('catamaran_filter_merge_styles', 'catamaran_essential_grid_merge_styles');
	function catamaran_essential_grid_merge_styles( $list ) {
		$list[ 'plugins/essential-grid/essential-grid.css' ] = false;
		return $list;
	}
}

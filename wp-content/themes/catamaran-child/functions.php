<?php
/**
 * Child-Theme functions and definitions
 */

// Load rtl.css because it is not autoloaded from the child theme
if ( ! function_exists( 'catamaran_child_load_rtl' ) ) {
	add_filter( 'wp_enqueue_scripts', 'catamaran_child_load_rtl', 3000 );
	function catamaran_child_load_rtl() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'catamaran-style-rtl', get_template_directory_uri() . '/rtl.css' );
		}
	}
}

?>
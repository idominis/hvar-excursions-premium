<?php
/**
 * Theme improves - functions and definitions for the theme improvements (compatibility with WordPress and plugins updates)
 *
 * @package CATAMARAN
 * @since CATAMARAN 2.31.3
 */


//----------------------------------------------------------------------
//-- Additional theme options
//----------------------------------------------------------------------

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'catamaran_improves_add_disable_hyphens_option' ) ) {
	add_action( 'after_setup_theme', 'catamaran_improves_add_disable_hyphens_option', 3 );
	/**
	 * Add 'General' section to the Typography options with the 'Disable word hyphenation' option
	 * 
	 * @hooked 'after_setup_theme', 3
	 */
	function catamaran_improves_add_disable_hyphens_option() {

		// Add 'General' section to the Typography
		catamaran_storage_set_array_after( 'options', 'fonts', array(
			// Fonts - General
			'font_general_section' => array(
				'title' => esc_html__( 'General', 'catamaran' ),
				'desc'  => '',
				'demo'  => true,
				'type'  => 'section',
			),
			'font_general_info'    => array(
				'title' => esc_html__( 'General', 'catamaran' ),
				'desc'  => wp_kses_data( __( 'General typography settings.', 'catamaran' ) ),
				'demo'  => true,
				'type'  => 'info',
			),
			'disable_hyphens'      => array(
				'title' => esc_html__( 'Disable word hyphenation', 'catamaran' ),
				'desc'  => wp_kses_data( __( 'Disable word hyphenation for the headings on tablets and mobile devices.', 'catamaran' ) ),
				'std'   => 0,
				'type'  => 'switch',
			),
		) );
	}
}

if ( ! function_exists( 'catamaran_improves_add_disable_hyphens_styles' ) ) {
	add_action( 'wp_head', 'catamaran_improves_add_disable_hyphens_styles' );
	/**
	 * Add styles for the 'Disable word hyphenation' option
	 * 
	 * @hooked 'wp_head'
	 */
	function catamaran_improves_add_disable_hyphens_styles() {
		
		// Get 'Disable word hyphenation' option value
		$disable_hyphens = catamaran_get_theme_option( 'disable_hyphens' );

		// Add styles
		if ( (int)$disable_hyphens > 0 ) {
			catamaran_add_inline_css( '
				@media (max-width: 1279px) {
					h1, h2, h3, h4, h5, h6 {
						hyphens: none;
						word-break: keep-all;
						white-space: normal;
					}
				}
			' );
		}
	}
}

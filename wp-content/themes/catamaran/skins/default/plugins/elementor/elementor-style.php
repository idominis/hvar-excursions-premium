<?php
// Add plugin-specific fonts to the custom CSS
if ( ! function_exists( 'catamaran_elm_get_css' ) ) {
    add_filter( 'catamaran_filter_get_css', 'catamaran_elm_get_css', 10, 2 );
    function catamaran_elm_get_css( $css, $args ) {

        if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
            $fonts         = $args['fonts'];
            $css['fonts'] .= <<<CSS
.elementor-widget-progress .elementor-title,
.elementor-widget-progress .elementor-progress-percentage,
.elementor-widget-toggle .elementor-toggle-title,
.elementor-widget-tabs .elementor-tab-title,
.elementor-widget-counter .elementor-counter-number-wrapper {
	{$fonts['h5_font-family']}
}
.custom_icon_btn.elementor-widget-button .elementor-button .elementor-button-text,
.elementor-widget-counter .elementor-counter-title,
.elementor-widget-icon-box .elementor-widget-container .elementor-icon-box-title small {
    {$fonts['p_font-family']}
}

CSS;
        }

        return $css;
    }
}


// Add theme-specific CSS-animations
if ( ! function_exists( 'catamaran_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'catamaran_elm_add_theme_animations' );
	function catamaran_elm_add_theme_animations( $animations ) {
		/* To add a theme-specific animations to the list:
			1) Merge to the array 'animations': array(
													esc_html__( 'Theme Specific', 'catamaran' ) => array(
														'ta_custom_1' => esc_html__( 'Custom 1', 'catamaran' )
													)
												)
			2) Add a CSS rules for the class '.ta_custom_1' to create a custom entrance animation
		*/
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'catamaran' ) => array(
									'ta_under_strips' => esc_html__( 'Under the strips', 'catamaran' ),
									'catamaran-fadeinup' => esc_html__( 'Catamaran - Fade In Up', 'catamaran' ),
									'catamaran-fadeinright' => esc_html__( 'Catamaran - Fade In Right', 'catamaran' ),
									'catamaran-fadeinleft' => esc_html__( 'Catamaran - Fade In Left', 'catamaran' ),
									'catamaran-fadeindown' => esc_html__( 'Catamaran - Fade In Down', 'catamaran' ),
									'catamaran-fadein' => esc_html__( 'Catamaran - Fade In', 'catamaran' ),
									'catamaran-infinite-rotate' => esc_html__( 'Catamaran - Infinite Rotate', 'catamaran' ),
								)
							)
						);

		return $animations;
	}
}

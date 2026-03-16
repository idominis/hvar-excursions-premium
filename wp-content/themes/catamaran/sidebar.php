<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

if ( catamaran_sidebar_present() ) {
	
	$catamaran_sidebar_type = catamaran_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $catamaran_sidebar_type && ! catamaran_is_layouts_available() ) {
		$catamaran_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $catamaran_sidebar_type ) {
		// Default sidebar with widgets
		$catamaran_sidebar_name = catamaran_get_theme_option( 'sidebar_widgets' );
		catamaran_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $catamaran_sidebar_name ) ) {
			dynamic_sidebar( $catamaran_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$catamaran_sidebar_id = catamaran_get_custom_sidebar_id();
		do_action( 'catamaran_action_show_layout', $catamaran_sidebar_id );
	}
	$catamaran_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $catamaran_out ) ) {
		$catamaran_sidebar_position    = catamaran_get_theme_option( 'sidebar_position' );
		$catamaran_sidebar_position_ss = catamaran_get_theme_option( 'sidebar_position_ss', 'below' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $catamaran_sidebar_position );
			echo ' sidebar_' . esc_attr( $catamaran_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $catamaran_sidebar_type );

			$catamaran_sidebar_scheme = apply_filters( 'catamaran_filter_sidebar_scheme', catamaran_get_theme_option( 'sidebar_scheme', 'inherit' ) );
			if ( ! empty( $catamaran_sidebar_scheme ) && ! catamaran_is_inherit( $catamaran_sidebar_scheme ) && 'custom' != $catamaran_sidebar_type ) {
				echo ' scheme_' . esc_attr( $catamaran_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<span id="sidebar_skip_link_anchor" class="catamaran_skip_link_anchor"></span>
			<?php

			do_action( 'catamaran_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $catamaran_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$catamaran_title = apply_filters( 'catamaran_filter_sidebar_control_title', 'float' == $catamaran_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'catamaran' ) : '' );
				$catamaran_text  = apply_filters( 'catamaran_filter_sidebar_control_text', 'above' == $catamaran_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'catamaran' ) : '' );
				?>
				<a href="#" role="button" class="sidebar_control" title="<?php echo esc_attr( $catamaran_title ); ?>"><?php echo esc_html( $catamaran_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'catamaran_action_before_sidebar', 'sidebar' );
				catamaran_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $catamaran_out ) );
				do_action( 'catamaran_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'catamaran_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}

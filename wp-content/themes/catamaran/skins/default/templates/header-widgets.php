<?php
/**
 * The template to display the widgets area in the header
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

// Header sidebar
$catamaran_header_name    = catamaran_get_theme_option( 'header_widgets' );
$catamaran_header_present = ! catamaran_is_off( $catamaran_header_name ) && is_active_sidebar( $catamaran_header_name );
if ( $catamaran_header_present ) {
	catamaran_storage_set( 'current_sidebar', 'header' );
	$catamaran_header_wide = catamaran_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $catamaran_header_name ) ) {
		dynamic_sidebar( $catamaran_header_name );
	}
	$catamaran_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $catamaran_widgets_output ) ) {
		$catamaran_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $catamaran_widgets_output );
		$catamaran_need_columns   = strpos( $catamaran_widgets_output, 'columns_wrap' ) === false;
		if ( $catamaran_need_columns ) {
			$catamaran_columns = max( 0, (int) catamaran_get_theme_option( 'header_columns' ) );
			if ( 0 == $catamaran_columns ) {
				$catamaran_columns = min( 6, max( 1, catamaran_tags_count( $catamaran_widgets_output, 'aside' ) ) );
			}
			if ( $catamaran_columns > 1 ) {
				$catamaran_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $catamaran_columns ) . ' widget', $catamaran_widgets_output );
			} else {
				$catamaran_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $catamaran_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'catamaran_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $catamaran_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $catamaran_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'catamaran_action_before_sidebar', 'header' );
				catamaran_show_layout( $catamaran_widgets_output );
				do_action( 'catamaran_action_after_sidebar', 'header' );
				if ( $catamaran_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $catamaran_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'catamaran_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}

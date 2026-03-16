<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */

// Footer sidebar
$catamaran_footer_name    = catamaran_get_theme_option( 'footer_widgets' );
$catamaran_footer_present = ! catamaran_is_off( $catamaran_footer_name ) && is_active_sidebar( $catamaran_footer_name );
if ( $catamaran_footer_present ) {
	catamaran_storage_set( 'current_sidebar', 'footer' );
	$catamaran_footer_wide = catamaran_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $catamaran_footer_name ) ) {
		dynamic_sidebar( $catamaran_footer_name );
	}
	$catamaran_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $catamaran_out ) ) {
		$catamaran_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $catamaran_out );
		$catamaran_need_columns = true;   //or check: strpos($catamaran_out, 'columns_wrap')===false;
		if ( $catamaran_need_columns ) {
			$catamaran_columns = max( 0, (int) catamaran_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $catamaran_columns ) {
				$catamaran_columns = min( 4, max( 1, catamaran_tags_count( $catamaran_out, 'aside' ) ) );
			}
			if ( $catamaran_columns > 1 ) {
				$catamaran_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $catamaran_columns ) . ' widget', $catamaran_out );
			} else {
				$catamaran_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $catamaran_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'catamaran_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $catamaran_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $catamaran_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'catamaran_action_before_sidebar', 'footer' );
				catamaran_show_layout( $catamaran_out );
				do_action( 'catamaran_action_after_sidebar', 'footer' );
				if ( $catamaran_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $catamaran_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'catamaran_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}

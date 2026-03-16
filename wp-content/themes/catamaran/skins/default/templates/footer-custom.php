<?php
/**
 * The template to display default site footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */

$catamaran_footer_id = catamaran_get_custom_footer_id();
$catamaran_footer_meta = get_post_meta( $catamaran_footer_id, 'trx_addons_options', true );
if ( ! empty( $catamaran_footer_meta['margin'] ) ) {
	catamaran_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( catamaran_prepare_css_value( $catamaran_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $catamaran_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $catamaran_footer_id ) ) ); ?>
						<?php
						$catamaran_footer_scheme = catamaran_get_theme_option( 'footer_scheme' );
						if ( ! empty( $catamaran_footer_scheme ) && ! catamaran_is_inherit( $catamaran_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $catamaran_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'catamaran_action_show_layout', $catamaran_footer_id );
	?>
</footer><!-- /.footer_wrap -->

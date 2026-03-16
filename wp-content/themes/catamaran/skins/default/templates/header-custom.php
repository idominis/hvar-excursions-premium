<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.06
 */

$catamaran_header_css   = '';
$catamaran_header_image = get_header_image();
$catamaran_header_video = catamaran_get_header_video();
if ( ! empty( $catamaran_header_image ) && catamaran_trx_addons_featured_image_override( is_singular() || catamaran_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$catamaran_header_image = catamaran_get_current_mode_image( $catamaran_header_image );
}

$catamaran_header_id = catamaran_get_custom_header_id();
$catamaran_header_meta = get_post_meta( $catamaran_header_id, 'trx_addons_options', true );
if ( ! empty( $catamaran_header_meta['margin'] ) ) {
	catamaran_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( catamaran_prepare_css_value( $catamaran_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $catamaran_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $catamaran_header_id ) ) ); ?>
				<?php
				echo ! empty( $catamaran_header_image ) || ! empty( $catamaran_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $catamaran_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $catamaran_header_image ) {
					echo ' ' . esc_attr( catamaran_add_inline_css_class( 'background-image: url(' . esc_url( $catamaran_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( catamaran_is_on( catamaran_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight catamaran-full-height';
				}
				$catamaran_header_scheme = catamaran_get_theme_option( 'header_scheme' );
				if ( ! empty( $catamaran_header_scheme ) && ! catamaran_is_inherit( $catamaran_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $catamaran_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $catamaran_header_video ) ) {
		get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'catamaran_action_show_layout', $catamaran_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>

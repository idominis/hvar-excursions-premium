<?php
/**
 * The template to display the site logo in the footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */

// Logo
if ( catamaran_is_on( catamaran_get_theme_option( 'logo_in_footer' ) ) ) {
	$catamaran_logo_image = catamaran_get_logo_image( 'footer' );
	$catamaran_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $catamaran_logo_image['logo'] ) || ! empty( $catamaran_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $catamaran_logo_image['logo'] ) ) {
					$catamaran_attr = catamaran_getimagesize( $catamaran_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $catamaran_logo_image['logo'] ) . '"'
								. ( ! empty( $catamaran_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $catamaran_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'catamaran' ) . '"'
								. ( ! empty( $catamaran_attr[3] ) ? ' ' . wp_kses_data( $catamaran_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $catamaran_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $catamaran_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}

<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

$catamaran_args = get_query_var( 'catamaran_logo_args' );

// Site logo
$catamaran_logo_type   = isset( $catamaran_args['type'] ) ? $catamaran_args['type'] : '';
$catamaran_logo_image  = catamaran_get_logo_image( $catamaran_logo_type );
$catamaran_logo_text   = catamaran_is_on( catamaran_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$catamaran_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $catamaran_logo_image['logo'] ) || ! empty( $catamaran_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $catamaran_logo_image['logo'] ) ) {
			if ( empty( $catamaran_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric($catamaran_logo_image['logo']) && (int) $catamaran_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$catamaran_attr = catamaran_getimagesize( $catamaran_logo_image['logo'] );
				echo '<img src="' . esc_url( $catamaran_logo_image['logo'] ) . '"'
						. ( ! empty( $catamaran_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $catamaran_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $catamaran_logo_text ) . '"'
						. ( ! empty( $catamaran_attr[3] ) ? ' ' . wp_kses_data( $catamaran_attr[3] ) : '' )
						. '>';
			}
		} else {
			catamaran_show_layout( catamaran_prepare_macros( $catamaran_logo_text ), '<span class="logo_text">', '</span>' );
			catamaran_show_layout( catamaran_prepare_macros( $catamaran_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}

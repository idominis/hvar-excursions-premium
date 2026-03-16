<?php
/**
 * The template to display the side menu
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */
?>
<div class="menu_side_wrap
<?php
echo ' menu_side_' . esc_attr( catamaran_get_theme_option( 'menu_side_icons' ) > 0 ? 'icons' : 'dots' );
$catamaran_menu_scheme = catamaran_get_theme_option( 'menu_scheme' );
$catamaran_header_scheme = catamaran_get_theme_option( 'header_scheme' );
if ( ! empty( $catamaran_menu_scheme ) && ! catamaran_is_inherit( $catamaran_menu_scheme  ) ) {
	echo ' scheme_' . esc_attr( $catamaran_menu_scheme );
} elseif ( ! empty( $catamaran_header_scheme ) && ! catamaran_is_inherit( $catamaran_header_scheme ) ) {
	echo ' scheme_' . esc_attr( $catamaran_header_scheme );
}
?>
				">
	<span class="menu_side_button icon-menu-2"></span>

	<div class="menu_side_inner">
		<?php
		// Logo
		set_query_var( 'catamaran_logo_args', array( 'type' => 'side' ) );
		get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'catamaran_logo_args', array() );
		// Main menu button
		?>
		<div class="toc_menu_item"
			<?php
			if ( catamaran_mouse_helper_enabled() ) {
				echo ' data-mouse-helper="click" data-mouse-helper-axis="y" data-mouse-helper-text="' . esc_attr__( 'Open main menu', 'catamaran' ) . '"';
			}
			?>
		>
			<a href="#" role="button" class="toc_menu_description menu_mobile_description"><span class="toc_menu_description_title"><?php esc_html_e( 'Main menu', 'catamaran' ); ?></span></a>
			<a class="menu_mobile_button toc_menu_icon icon-menu-2" href="#" role="button"></a>
		</div>		
	</div>

</div>
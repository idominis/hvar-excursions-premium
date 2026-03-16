<?php
/**
 * The template to display default site footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$catamaran_footer_scheme = catamaran_get_theme_option( 'footer_scheme' );
if ( ! empty( $catamaran_footer_scheme ) && ! catamaran_is_inherit( $catamaran_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $catamaran_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/footer-socials' ) );

	// Copyright area
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->

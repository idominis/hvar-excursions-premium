<?php
/**
 * The template to display the socials in the footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */


// Socials
if ( catamaran_is_on( catamaran_get_theme_option( 'socials_in_footer' ) ) ) {
	$catamaran_output = catamaran_get_socials_links();
	if ( '' != $catamaran_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php catamaran_show_layout( $catamaran_output ); ?>
			</div>
		</div>
		<?php
	}
}

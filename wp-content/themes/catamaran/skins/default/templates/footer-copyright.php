<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$catamaran_copyright_scheme = catamaran_get_theme_option( 'copyright_scheme' );
if ( ! empty( $catamaran_copyright_scheme ) && ! catamaran_is_inherit( $catamaran_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $catamaran_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$catamaran_copyright = catamaran_get_theme_option( 'copyright' );
			if ( ! empty( $catamaran_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$catamaran_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $catamaran_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$catamaran_copyright = catamaran_prepare_macros( $catamaran_copyright );
				// Display copyright
				echo wp_kses( nl2br( $catamaran_copyright ), 'catamaran_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>

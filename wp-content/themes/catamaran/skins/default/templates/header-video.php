<?php
/**
 * The template to display the background video in the header
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.14
 */
$catamaran_header_video = catamaran_get_header_video();
$catamaran_embed_video  = '';
if ( ! empty( $catamaran_header_video ) && ! catamaran_is_from_uploads( $catamaran_header_video ) ) {
	if ( catamaran_is_youtube_url( $catamaran_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $catamaran_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php catamaran_show_layout( catamaran_get_embed_video( $catamaran_header_video ) ); ?></div>
		<?php
	}
}

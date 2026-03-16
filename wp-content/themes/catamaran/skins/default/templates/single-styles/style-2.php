<?php
/**
 * The "Style 2" template to display the post header of the single post or attachment:
 * featured image placed in the post header and title placed inside content
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.75.0
 */

if ( apply_filters( 'catamaran_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
	$catamaran_post_format = str_replace( 'post-format-', '', get_post_format() );
    $post_meta = in_array( $catamaran_post_format, array( 'video' ) ) ? get_post_meta( get_the_ID(), 'trx_addons_options', true ) : false;
    $video_autoplay = ! empty( $post_meta['video_autoplay'] )
        && ! empty( $post_meta['video_list'] )
        && is_array( $post_meta['video_list'] )
        && count( $post_meta['video_list'] ) == 1
        && ( ! empty( $post_meta['video_list'][0]['video_url'] ) || ! empty( $post_meta['video_list'][0]['video_embed'] ) );

    // Featured image
	ob_start();
	catamaran_show_post_featured_image( array(
		'thumb_bg'  => true,
		'popup'     => true,
        'class_avg' => in_array( $catamaran_post_format, array( 'video' ) )
            ? ( ! $video_autoplay
                ? 'content_wrap'
                : 'with_thumb post_featured_bg with_video with_video_autoplay'
            )
            : '',
        'autoplay'  => $video_autoplay,
        'post_meta' => $post_meta
	) );
	$catamaran_post_header = ob_get_contents();
	ob_end_clean();

	$catamaran_with_featured_image = catamaran_is_with_featured_image( $catamaran_post_header );

	if ( strpos( $catamaran_post_header, 'post_featured' ) !== false ) {
		?>
		<div class="post_header_wrap post_header_wrap_in_header post_header_wrap_style_<?php
			echo esc_attr( catamaran_get_theme_option( 'single_style' ) );
			if ( $catamaran_with_featured_image ) {
				echo ' with_featured_image';
			}
		?>">
			<?php
			do_action( 'catamaran_action_before_post_header' );
			catamaran_show_layout( $catamaran_post_header );
			do_action( 'catamaran_action_after_post_header' );
			?>
		</div>
		<?php
	}
}

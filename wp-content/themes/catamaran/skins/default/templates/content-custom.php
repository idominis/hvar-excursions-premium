<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.50
 */

$catamaran_template_args = get_query_var( 'catamaran_template_args' );
if ( is_array( $catamaran_template_args ) ) {
	$catamaran_columns    = empty( $catamaran_template_args['columns'] ) ? 2 : max( 1, $catamaran_template_args['columns'] );
	$catamaran_blog_style = array( $catamaran_template_args['type'], $catamaran_columns );
} else {
	$catamaran_template_args = array();
	$catamaran_blog_style = explode( '_', catamaran_get_theme_option( 'blog_style' ) );
	$catamaran_columns    = empty( $catamaran_blog_style[1] ) ? 2 : max( 1, $catamaran_blog_style[1] );
}
$catamaran_blog_id       = catamaran_get_custom_blog_id( join( '_', $catamaran_blog_style ) );
$catamaran_blog_style[0] = str_replace( 'blog-custom-', '', $catamaran_blog_style[0] );
$catamaran_expanded      = ! catamaran_sidebar_present() && catamaran_get_theme_option( 'expand_content' ) == 'expand';
$catamaran_components    = ! empty( $catamaran_template_args['meta_parts'] )
							? ( is_array( $catamaran_template_args['meta_parts'] )
								? join( ',', $catamaran_template_args['meta_parts'] )
								: $catamaran_template_args['meta_parts']
								)
							: catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'meta_parts' ) );
$catamaran_post_format   = get_post_format();
$catamaran_post_format   = empty( $catamaran_post_format ) ? 'standard' : str_replace( 'post-format-', '', $catamaran_post_format );

$catamaran_blog_meta     = catamaran_get_custom_layout_meta( $catamaran_blog_id );
$catamaran_custom_style  = ! empty( $catamaran_blog_meta['scripts_required'] ) ? $catamaran_blog_meta['scripts_required'] : 'none';

if ( ! empty( $catamaran_template_args['slider'] ) || $catamaran_columns > 1 || ! catamaran_is_off( $catamaran_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $catamaran_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( catamaran_is_off( $catamaran_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $catamaran_custom_style ) ) . "-1_{$catamaran_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $catamaran_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $catamaran_columns )
					. ' post_layout_' . esc_attr( $catamaran_blog_style[0] )
					. ' post_layout_' . esc_attr( $catamaran_blog_style[0] ) . '_' . esc_attr( $catamaran_columns )
					. ( ! catamaran_is_off( $catamaran_custom_style )
						? ' post_layout_' . esc_attr( $catamaran_custom_style )
							. ' post_layout_' . esc_attr( $catamaran_custom_style ) . '_' . esc_attr( $catamaran_columns )
						: ''
						)
		);
	catamaran_add_blog_animation( $catamaran_template_args );
	?>
>
	<?php
	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}
	// Custom layout
	do_action( 'catamaran_action_show_layout', $catamaran_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $catamaran_template_args['slider'] ) || $catamaran_columns > 1 || ! catamaran_is_off( $catamaran_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}

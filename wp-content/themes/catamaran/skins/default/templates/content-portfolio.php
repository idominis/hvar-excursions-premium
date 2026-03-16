<?php
/**
 * The Portfolio template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

$catamaran_template_args = get_query_var( 'catamaran_template_args' );
if ( is_array( $catamaran_template_args ) ) {
	$catamaran_columns    = empty( $catamaran_template_args['columns'] ) ? 2 : max( 1, $catamaran_template_args['columns'] );
	$catamaran_blog_style = array( $catamaran_template_args['type'], $catamaran_columns );
    $catamaran_columns_class = catamaran_get_column_class( 1, $catamaran_columns, ! empty( $catamaran_template_args['columns_tablet']) ? $catamaran_template_args['columns_tablet'] : '', ! empty($catamaran_template_args['columns_mobile']) ? $catamaran_template_args['columns_mobile'] : '' );
} else {
	$catamaran_template_args = array();
	$catamaran_blog_style = explode( '_', catamaran_get_theme_option( 'blog_style' ) );
	$catamaran_columns    = empty( $catamaran_blog_style[1] ) ? 2 : max( 1, $catamaran_blog_style[1] );
    $catamaran_columns_class = catamaran_get_column_class( 1, $catamaran_columns );
}

$catamaran_post_format = get_post_format();
$catamaran_post_format = empty( $catamaran_post_format ) ? 'standard' : str_replace( 'post-format-', '', $catamaran_post_format );

?><div class="
<?php
if ( ! empty( $catamaran_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( catamaran_is_blog_style_use_masonry( $catamaran_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $catamaran_columns ) : esc_attr( $catamaran_columns_class ));
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $catamaran_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $catamaran_columns )
		. ( 'portfolio' != $catamaran_blog_style[0] ? ' ' . esc_attr( $catamaran_blog_style[0] )  . '_' . esc_attr( $catamaran_columns ) : '' )
	);
	catamaran_add_blog_animation( $catamaran_template_args );
	?>
>
<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	$catamaran_hover   = ! empty( $catamaran_template_args['hover'] ) && ! catamaran_is_inherit( $catamaran_template_args['hover'] )
								? $catamaran_template_args['hover']
								: catamaran_get_theme_option( 'image_hover' );

	if ( 'dots' == $catamaran_hover ) {
		$catamaran_post_link = empty( $catamaran_template_args['no_links'] )
								? ( ! empty( $catamaran_template_args['link'] )
									? $catamaran_template_args['link']
									: get_permalink()
									)
								: '';
		$catamaran_target    = ! empty( $catamaran_post_link ) && catamaran_is_external_url( $catamaran_post_link ) && function_exists( 'catamaran_external_links_target' )
								? catamaran_external_links_target()
								: '';
	}
	
	// Meta parts
	$catamaran_components = ! empty( $catamaran_template_args['meta_parts'] )
							? ( is_array( $catamaran_template_args['meta_parts'] )
								? $catamaran_template_args['meta_parts']
								: explode( ',', $catamaran_template_args['meta_parts'] )
								)
							: catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'meta_parts' ) );

	// Featured image
	catamaran_show_post_featured( apply_filters( 'catamaran_filter_args_featured', 
        array(
			'hover'         => $catamaran_hover,
			'no_links'      => ! empty( $catamaran_template_args['no_links'] ),
			'thumb_size'    => ! empty( $catamaran_template_args['thumb_size'] )
								? $catamaran_template_args['thumb_size']
								: catamaran_get_thumb_size(
									catamaran_is_blog_style_use_masonry( $catamaran_blog_style[0] )
										? (	strpos( catamaran_get_theme_option( 'body_style' ), 'full' ) !== false || $catamaran_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( catamaran_get_theme_option( 'body_style' ), 'full' ) !== false || $catamaran_columns < 3
											? 'square'
											: 'square'
											)
								),
			'thumb_bg' => catamaran_is_blog_style_use_masonry( $catamaran_blog_style[0] ) ? false : true,
			'show_no_image' => true,
			'meta_parts'    => $catamaran_components,
			'class'         => 'dots' == $catamaran_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $catamaran_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $catamaran_post_link )
												? '<a href="' . esc_url( $catamaran_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $catamaran_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
            'thumb_ratio'   => 'info' == $catamaran_hover ?  '100:102' : '',
        ),
        'content-portfolio',
        $catamaran_template_args
    ) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!
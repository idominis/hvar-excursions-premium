<?php
/**
 * The Classic template to display the content
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
$catamaran_expanded   = ! catamaran_sidebar_present() && catamaran_get_theme_option( 'expand_content' ) == 'expand';

$catamaran_post_format = get_post_format();
$catamaran_post_format = empty( $catamaran_post_format ) ? 'standard' : str_replace( 'post-format-', '', $catamaran_post_format );

?><div class="<?php
	if ( ! empty( $catamaran_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( catamaran_is_blog_style_use_masonry( $catamaran_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $catamaran_columns ) : esc_attr( $catamaran_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $catamaran_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $catamaran_columns )
				. ' post_layout_' . esc_attr( $catamaran_blog_style[0] )
				. ' post_layout_' . esc_attr( $catamaran_blog_style[0] ) . '_' . esc_attr( $catamaran_columns )
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

	// Featured image
	$catamaran_hover      = ! empty( $catamaran_template_args['hover'] ) && ! catamaran_is_inherit( $catamaran_template_args['hover'] )
							? $catamaran_template_args['hover']
							: catamaran_get_theme_option( 'image_hover' );

	$catamaran_components = ! empty( $catamaran_template_args['meta_parts'] )
							? ( is_array( $catamaran_template_args['meta_parts'] )
								? $catamaran_template_args['meta_parts']
								: explode( ',', $catamaran_template_args['meta_parts'] )
								)
							: catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'meta_parts' ) );

	catamaran_show_post_featured( apply_filters( 'catamaran_filter_args_featured',
		array(
			'thumb_size' => ! empty( $catamaran_template_args['thumb_size'] )
				? $catamaran_template_args['thumb_size']
				: catamaran_get_thumb_size(
					'classic' == $catamaran_blog_style[0]
						? ( strpos( catamaran_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $catamaran_columns > 2 ? 'big' : 'huge' )
								: ( $catamaran_columns > 2
									? ( $catamaran_expanded ? 'square' : 'square' )
									: ($catamaran_columns > 1 ? 'square' : ( $catamaran_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( catamaran_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $catamaran_columns > 2 ? 'masonry-big' : 'full' )
								: ($catamaran_columns === 1 ? ( $catamaran_expanded ? 'huge' : 'big' ) : ( $catamaran_columns <= 2 && $catamaran_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $catamaran_hover,
			'meta_parts' => $catamaran_components,
			'no_links'   => ! empty( $catamaran_template_args['no_links'] ),
        ),
        'content-classic',
        $catamaran_template_args
    ) );

	// Title and post meta
	$catamaran_show_title = get_the_title() != '';
	$catamaran_show_meta  = count( $catamaran_components ) > 0 && ! in_array( $catamaran_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $catamaran_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'catamaran_filter_show_blog_meta', $catamaran_show_meta, $catamaran_components, 'classic' ) ) {
				if ( count( $catamaran_components ) > 0 ) {
					do_action( 'catamaran_action_before_post_meta' );
					catamaran_show_post_meta(
						apply_filters(
							'catamaran_filter_post_meta_args', array(
							'components' => join( ',', $catamaran_components ),
							'seo'        => false,
							'echo'       => true,
						), $catamaran_blog_style[0], $catamaran_columns
						)
					);
					do_action( 'catamaran_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'catamaran_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'catamaran_action_before_post_title' );
				if ( empty( $catamaran_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'catamaran_action_after_post_title' );
			}

			if( !in_array( $catamaran_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'catamaran_filter_show_blog_readmore', ! $catamaran_show_title || ! empty( $catamaran_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $catamaran_template_args['no_links'] ) ) {
						do_action( 'catamaran_action_before_post_readmore' );
						catamaran_show_post_more_link( $catamaran_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'catamaran_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $catamaran_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('catamaran_filter_show_blog_excerpt', empty($catamaran_template_args['hide_excerpt']) && catamaran_get_theme_option('excerpt_length') > 0, 'classic')) {
			catamaran_show_post_content($catamaran_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $catamaran_template_args['more_button'] )) {
			if ( empty( $catamaran_template_args['no_links'] ) ) {
				do_action( 'catamaran_action_before_post_readmore' );
				catamaran_show_post_more_link( $catamaran_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'catamaran_action_after_post_readmore' );
			}
		}
		$catamaran_content = ob_get_contents();
		ob_end_clean();
		catamaran_show_layout($catamaran_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!

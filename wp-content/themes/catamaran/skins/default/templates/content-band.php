<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.71.0
 */

$catamaran_template_args = get_query_var( 'catamaran_template_args' );
if ( ! is_array( $catamaran_template_args ) ) {
	$catamaran_template_args = array(
								'type'    => 'band',
								'columns' => 1
								);
}

$catamaran_columns       = 1;

$catamaran_expanded      = ! catamaran_sidebar_present() && catamaran_get_theme_option( 'expand_content' ) == 'expand';

$catamaran_post_format   = get_post_format();
$catamaran_post_format   = empty( $catamaran_post_format ) ? 'standard' : str_replace( 'post-format-', '', $catamaran_post_format );

if ( is_array( $catamaran_template_args ) ) {
	$catamaran_columns    = empty( $catamaran_template_args['columns'] ) ? 1 : max( 1, $catamaran_template_args['columns'] );
	$catamaran_blog_style = array( $catamaran_template_args['type'], $catamaran_columns );
	if ( ! empty( $catamaran_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $catamaran_columns > 1 ) {
	    $catamaran_columns_class = catamaran_get_column_class( 1, $catamaran_columns, ! empty( $catamaran_template_args['columns_tablet']) ? $catamaran_template_args['columns_tablet'] : '', ! empty($catamaran_template_args['columns_mobile']) ? $catamaran_template_args['columns_mobile'] : '' );
				?><div class="<?php echo esc_attr( $catamaran_columns_class ); ?>"><?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $catamaran_post_format ) );
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
								: array_map( 'trim', explode( ',', $catamaran_template_args['meta_parts'] ) )
								)
							: catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'meta_parts' ) );
	catamaran_show_post_featured( apply_filters( 'catamaran_filter_args_featured',
		array(
			'no_links'   => ! empty( $catamaran_template_args['no_links'] ),
			'hover'      => $catamaran_hover,
			'meta_parts' => $catamaran_components,
			'thumb_bg'   => true,
			'thumb_ratio'   => '1:1',
			'thumb_size' => ! empty( $catamaran_template_args['thumb_size'] )
								? $catamaran_template_args['thumb_size']
								: catamaran_get_thumb_size( 
								in_array( $catamaran_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( catamaran_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $catamaran_expanded 
											? 'big' 
											: 'medium-square'
											)
										)
									: 'masonry-big'
								)
		),
		'content-band',
		$catamaran_template_args
	) );

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$catamaran_show_title = get_the_title() != '';
		$catamaran_show_meta  = count( $catamaran_components ) > 0 && ! in_array( $catamaran_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $catamaran_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'catamaran_filter_show_blog_categories', $catamaran_show_meta && in_array( 'categories', $catamaran_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'catamaran_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						catamaran_show_post_meta( apply_filters(
															'catamaran_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																'cat_sep'    => false,
																),
															'hover_' . $catamaran_hover, 1
															)
											);
						?>
					</div>
					<?php
					$catamaran_components = catamaran_array_delete_by_value( $catamaran_components, 'categories' );
					do_action( 'catamaran_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'catamaran_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'catamaran_action_before_post_title' );
					if ( empty( $catamaran_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'catamaran_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $catamaran_template_args['excerpt_length'] ) && ! in_array( $catamaran_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$catamaran_template_args['excerpt_length'] = 13;
		}
		if ( apply_filters( 'catamaran_filter_show_blog_excerpt', empty( $catamaran_template_args['hide_excerpt'] ) && catamaran_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				catamaran_show_post_content( $catamaran_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'catamaran_filter_show_blog_meta', $catamaran_show_meta, $catamaran_components, 'band' ) ) {
			if ( count( $catamaran_components ) > 0 ) {
				do_action( 'catamaran_action_before_post_meta' );
				catamaran_show_post_meta(
					apply_filters(
						'catamaran_filter_post_meta_args', array(
							'components' => join( ',', $catamaran_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'catamaran_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'catamaran_filter_show_blog_readmore', ! $catamaran_show_title || ! empty( $catamaran_template_args['more_button'] ), 'band' ) ) {
			if ( empty( $catamaran_template_args['no_links'] ) ) {
				do_action( 'catamaran_action_before_post_readmore' );
				catamaran_show_post_more_link( $catamaran_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'catamaran_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $catamaran_template_args ) ) {
	if ( ! empty( $catamaran_template_args['slider'] ) || $catamaran_columns > 1 ) {
		?>
		</div>
		<?php
	}
}

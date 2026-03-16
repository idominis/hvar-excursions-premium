<?php
/**
 * The template to display single post
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

// Full post loading
$full_post_loading          = catamaran_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = catamaran_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = catamaran_get_theme_option( 'posts_navigation_scroll_which_block', 'article' );

// Position of the related posts
$catamaran_related_position   = catamaran_get_theme_option( 'related_position', 'below_content' );

// Type of the prev/next post navigation
$catamaran_posts_navigation   = catamaran_get_theme_option( 'posts_navigation' );
$catamaran_prev_post          = false;
$catamaran_prev_post_same_cat = (int)catamaran_get_theme_option( 'posts_navigation_scroll_same_cat', 1 );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( catamaran_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	catamaran_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'catamaran_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $catamaran_posts_navigation ) {
		$catamaran_prev_post = get_previous_post( $catamaran_prev_post_same_cat );  // Get post from same category
		if ( ! $catamaran_prev_post && $catamaran_prev_post_same_cat ) {
			$catamaran_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $catamaran_prev_post ) {
			$catamaran_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $catamaran_prev_post ) ) {
		catamaran_sc_layouts_showed( 'featured', false );
		catamaran_sc_layouts_showed( 'title', false );
		catamaran_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $catamaran_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/content', 'single-' . catamaran_get_theme_option( 'single_style' ) ), 'single-' . catamaran_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $catamaran_related_position, 'inside' ) === 0 ) {
		$catamaran_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'catamaran_action_related_posts' );
		$catamaran_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $catamaran_related_content ) ) {
			$catamaran_related_position_inside = max( 0, min( 9, catamaran_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $catamaran_related_position_inside ) {
				$catamaran_related_position_inside = mt_rand( 1, 9 );
			}

			$catamaran_p_number         = 0;
			$catamaran_related_inserted = false;
			$catamaran_in_block         = false;
			$catamaran_content_start    = strpos( $catamaran_content, '<div class="post_content' );
			$catamaran_content_end      = strrpos( $catamaran_content, '</div>' );

			for ( $i = max( 0, $catamaran_content_start ); $i < min( strlen( $catamaran_content ) - 3, $catamaran_content_end ); $i++ ) {
				if ( $catamaran_content[ $i ] != '<' ) {
					continue;
				}
				if ( $catamaran_in_block ) {
					if ( strtolower( substr( $catamaran_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$catamaran_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $catamaran_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $catamaran_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$catamaran_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $catamaran_content[ $i + 1 ] && in_array( $catamaran_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$catamaran_p_number++;
					if ( $catamaran_related_position_inside == $catamaran_p_number ) {
						$catamaran_related_inserted = true;
						$catamaran_content = ( $i > 0 ? substr( $catamaran_content, 0, $i ) : '' )
											. $catamaran_related_content
											. substr( $catamaran_content, $i );
					}
				}
			}
			if ( ! $catamaran_related_inserted ) {
				if ( $catamaran_content_end > 0 ) {
					$catamaran_content = substr( $catamaran_content, 0, $catamaran_content_end ) . $catamaran_related_content . substr( $catamaran_content, $catamaran_content_end );
				} else {
					$catamaran_content .= $catamaran_related_content;
				}
			}
		}

		catamaran_show_layout( $catamaran_content );
	}

	// Comments
	do_action( 'catamaran_action_before_comments' );
	comments_template();
	do_action( 'catamaran_action_after_comments' );

	// Related posts
	if ( 'below_content' == $catamaran_related_position
		&& ( 'scroll' != $catamaran_posts_navigation || (int)catamaran_get_theme_option( 'posts_navigation_scroll_hide_related', 0 ) == 0 )
		&& ( ! $full_post_loading || (int)catamaran_get_theme_option( 'open_full_post_hide_related', 1 ) == 0 )
	) {
		do_action( 'catamaran_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $catamaran_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $catamaran_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $catamaran_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $catamaran_prev_post ) ); ?>"
			data-cur-post-link="<?php echo esc_attr( get_permalink() ); ?>"
			data-cur-post-title="<?php the_title_attribute(); ?>"
			<?php do_action( 'catamaran_action_nav_links_single_scroll_data', $catamaran_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();

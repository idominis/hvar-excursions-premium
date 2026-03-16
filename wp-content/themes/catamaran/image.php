<?php
/**
 * The template to display the attachment
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */


get_header();

while ( have_posts() ) {
	the_post();

	// Display post's content
	get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/content', 'single-' . catamaran_get_theme_option( 'single_style' ) ), 'single-' . catamaran_get_theme_option( 'single_style' ) );

	// Parent post navigation.
	$catamaran_posts_navigation = catamaran_get_theme_option( 'posts_navigation' );
	if ( 'links' == $catamaran_posts_navigation ) {
		?>
		<div class="nav-links-single<?php
			if ( ! catamaran_is_off( catamaran_get_theme_option( 'posts_navigation_fixed', 0 ) ) ) {
				echo ' nav-links-fixed fixed';
			}
		?>">
			<?php
			the_post_navigation( apply_filters( 'catamaran_filter_post_navigation_args', array(
					'prev_text' => '<span class="nav-arrow"></span>'
						. '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Published in', 'catamaran' ) . '</span> '
						. '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'catamaran' ) . '</span> '
						. '<h5 class="post-title">%title</h5>'
						. '<span class="post_date">%date</span>',
			), 'image' ) );
			?>
		</div>
		<?php
	}

	// Comments
	do_action( 'catamaran_action_before_comments' );
	comments_template();
	do_action( 'catamaran_action_after_comments' );
}

get_footer();

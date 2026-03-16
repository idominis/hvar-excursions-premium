<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

$catamaran_template = apply_filters( 'catamaran_filter_get_template_part', catamaran_blog_archive_get_template() );

if ( ! empty( $catamaran_template ) && 'index' != $catamaran_template ) {

	get_template_part( $catamaran_template );

} else {

	catamaran_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$catamaran_stickies   = is_home()
								|| ( in_array( catamaran_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) catamaran_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$catamaran_post_type  = catamaran_get_theme_option( 'post_type' );
		$catamaran_args       = array(
								'blog_style'     => catamaran_get_theme_option( 'blog_style' ),
								'post_type'      => $catamaran_post_type,
								'taxonomy'       => catamaran_get_post_type_taxonomy( $catamaran_post_type ),
								'parent_cat'     => catamaran_get_theme_option( 'parent_cat' ),
								'posts_per_page' => catamaran_get_theme_option( 'posts_per_page' ),
								'sticky'         => catamaran_get_theme_option( 'sticky_style', 'inherit' ) == 'columns'
															&& is_array( $catamaran_stickies )
															&& count( $catamaran_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		catamaran_blog_archive_start();

		do_action( 'catamaran_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'catamaran_action_before_page_author' );
			get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'catamaran_action_after_page_author' );
		}

		if ( catamaran_get_theme_option( 'show_filters', 0 ) ) {
			do_action( 'catamaran_action_before_page_filters' );
			catamaran_show_filters( $catamaran_args );
			do_action( 'catamaran_action_after_page_filters' );
		} else {
			do_action( 'catamaran_action_before_page_posts' );
			catamaran_show_posts( array_merge( $catamaran_args, array( 'cat' => $catamaran_args['parent_cat'] ) ) );
			do_action( 'catamaran_action_after_page_posts' );
		}

		do_action( 'catamaran_action_blog_archive_end' );

		catamaran_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}

<?php
/**
 * The Header: Logo and main menu
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( catamaran_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'catamaran_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'catamaran_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('catamaran_action_body_wrap_attributes'); ?>>

		<?php do_action( 'catamaran_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'catamaran_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('catamaran_action_page_wrap_attributes'); ?>>

			<?php do_action( 'catamaran_action_page_wrap_start' ); ?>

			<?php
			$catamaran_full_post_loading = ( catamaran_is_singular( 'post' ) || catamaran_is_singular( 'attachment' ) ) && catamaran_get_value_gp( 'action' ) == 'full_post_loading';
			$catamaran_prev_post_loading = ( catamaran_is_singular( 'post' ) || catamaran_is_singular( 'attachment' ) ) && catamaran_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $catamaran_full_post_loading && ! $catamaran_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="catamaran_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'catamaran_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to content", 'catamaran' ); ?></a>
				<?php if ( catamaran_sidebar_present() ) { ?>
				<a class="catamaran_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'catamaran_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'catamaran' ); ?></a>
				<?php } ?>
				<a class="catamaran_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'catamaran_filter_skip_links_tabindex', 0 ) ); ?>"><?php esc_html_e( "Skip to footer", 'catamaran' ); ?></a>

				<?php
				do_action( 'catamaran_action_before_header' );

				// Header
				$catamaran_header_type = catamaran_get_theme_option( 'header_type' );
				if ( 'custom' == $catamaran_header_type && ! catamaran_is_layouts_available() ) {
					$catamaran_header_type = 'default';
				}
				get_template_part( apply_filters( 'catamaran_filter_get_template_part', "templates/header-" . sanitize_file_name( $catamaran_header_type ) ) );

				// Side menu
				if ( in_array( catamaran_get_theme_option( 'menu_side', 'none' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				if ( apply_filters( 'catamaran_filter_use_navi_mobile', catamaran_sc_layouts_showed( 'menu_button' ) || $catamaran_header_type == 'default' ) ) {
					get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-navi-mobile' ) );
				}

				do_action( 'catamaran_action_after_header' );

			}
			?>

			<?php do_action( 'catamaran_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( catamaran_is_off( catamaran_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $catamaran_header_type ) ) {
						$catamaran_header_type = catamaran_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $catamaran_header_type && catamaran_is_layouts_available() ) {
						$catamaran_header_id = catamaran_get_custom_header_id();
						if ( $catamaran_header_id > 0 ) {
							$catamaran_header_meta = catamaran_get_custom_layout_meta( $catamaran_header_id );
							if ( ! empty( $catamaran_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$catamaran_footer_type = catamaran_get_theme_option( 'footer_type' );
					if ( 'custom' == $catamaran_footer_type && catamaran_is_layouts_available() ) {
						$catamaran_footer_id = catamaran_get_custom_footer_id();
						if ( $catamaran_footer_id ) {
							$catamaran_footer_meta = catamaran_get_custom_layout_meta( $catamaran_footer_id );
							if ( ! empty( $catamaran_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'catamaran_action_page_content_wrap_class', $catamaran_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'catamaran_filter_is_prev_post_loading', $catamaran_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( catamaran_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'catamaran_action_page_content_wrap_data', $catamaran_prev_post_loading );
			?>>
				<?php
				do_action( 'catamaran_action_page_content_wrap', $catamaran_full_post_loading || $catamaran_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'catamaran_filter_single_post_header', catamaran_is_singular( 'post' ) || catamaran_is_singular( 'attachment' ) ) ) {
					if ( $catamaran_prev_post_loading ) {
						if ( catamaran_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) != 'article' ) {
							do_action( 'catamaran_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$catamaran_path = apply_filters( 'catamaran_filter_get_template_part', 'templates/single-styles/' . catamaran_get_theme_option( 'single_style' ) );
					if ( catamaran_get_file_dir( $catamaran_path . '.php' ) != '' ) {
						get_template_part( $catamaran_path );
					}
				}

				// Widgets area above page
				$catamaran_body_style   = catamaran_get_theme_option( 'body_style' );
				$catamaran_widgets_name = catamaran_get_theme_option( 'widgets_above_page', 'hide' );
				$catamaran_show_widgets = ! catamaran_is_off( $catamaran_widgets_name ) && is_active_sidebar( $catamaran_widgets_name );
				if ( $catamaran_show_widgets ) {
					if ( 'fullscreen' != $catamaran_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					catamaran_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $catamaran_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'catamaran_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $catamaran_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'catamaran_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'catamaran_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<span id="content_skip_link_anchor" class="catamaran_skip_link_anchor"></span>
						<?php
						// Single posts banner between prev/next posts
						if ( ( catamaran_is_singular( 'post' ) || catamaran_is_singular( 'attachment' ) )
							&& $catamaran_prev_post_loading 
							&& catamaran_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) == 'article'
						) {
							do_action( 'catamaran_action_between_posts' );
						}

						// Widgets area above content
						catamaran_create_widgets_area( 'widgets_above_content' );

						do_action( 'catamaran_action_page_content_start_text' );

<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

// Page (category, tag, archive, author) title

if ( catamaran_need_page_title() ) {
	catamaran_sc_layouts_showed( 'title', true );
	catamaran_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								catamaran_show_post_meta(
									apply_filters(
										'catamaran_filter_post_meta_args', array(
											'components' => join( ',', catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', catamaran_array_get_keys_by_value( catamaran_get_theme_option( 'counters' ) ) ),
											'seo'        => catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$catamaran_blog_title           = catamaran_get_blog_title();
							$catamaran_blog_title_text      = '';
							$catamaran_blog_title_class     = '';
							$catamaran_blog_title_link      = '';
							$catamaran_blog_title_link_text = '';
							if ( is_array( $catamaran_blog_title ) ) {
								$catamaran_blog_title_text      = $catamaran_blog_title['text'];
								$catamaran_blog_title_class     = ! empty( $catamaran_blog_title['class'] ) ? ' ' . $catamaran_blog_title['class'] : '';
								$catamaran_blog_title_link      = ! empty( $catamaran_blog_title['link'] ) ? $catamaran_blog_title['link'] : '';
								$catamaran_blog_title_link_text = ! empty( $catamaran_blog_title['link_text'] ) ? $catamaran_blog_title['link_text'] : '';
							} else {
								$catamaran_blog_title_text = $catamaran_blog_title;
							}
							?>
							<h1 class="sc_layouts_title_caption<?php echo esc_attr( $catamaran_blog_title_class ); ?>"<?php
								if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
									?> itemprop="headline"<?php
								}
							?>>
								<?php
								$catamaran_top_icon = catamaran_get_term_image_small();
								if ( ! empty( $catamaran_top_icon ) ) {
									$catamaran_attr = catamaran_getimagesize( $catamaran_top_icon );
									?>
									<img src="<?php echo esc_url( $catamaran_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'catamaran' ); ?>"
										<?php
										if ( ! empty( $catamaran_attr[3] ) ) {
											catamaran_show_layout( $catamaran_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $catamaran_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $catamaran_blog_title_link ) && ! empty( $catamaran_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $catamaran_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $catamaran_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'catamaran_action_breadcrumbs' );
						$catamaran_breadcrumbs = ob_get_contents();
						ob_end_clean();
						catamaran_show_layout( $catamaran_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

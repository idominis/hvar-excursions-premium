<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

							do_action( 'catamaran_action_page_content_end_text' );
							
							// Widgets area below the content
							catamaran_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'catamaran_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'catamaran_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'catamaran_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'catamaran_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$catamaran_body_style = catamaran_get_theme_option( 'body_style' );
					$catamaran_widgets_name = catamaran_get_theme_option( 'widgets_below_page', 'hide' );
					$catamaran_show_widgets = ! catamaran_is_off( $catamaran_widgets_name ) && is_active_sidebar( $catamaran_widgets_name );
					$catamaran_show_related = catamaran_is_single() && catamaran_get_theme_option( 'related_position', 'below_content' ) == 'below_page';
					if ( $catamaran_show_widgets || $catamaran_show_related ) {
						if ( 'fullscreen' != $catamaran_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $catamaran_show_related ) {
							do_action( 'catamaran_action_related_posts' );
						}

						// Widgets area below page content
						if ( $catamaran_show_widgets ) {
							catamaran_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $catamaran_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'catamaran_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'catamaran_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! catamaran_is_singular( 'post' ) && ! catamaran_is_singular( 'attachment' ) ) || ! in_array ( catamaran_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<span id="footer_skip_link_anchor" class="catamaran_skip_link_anchor"></span>
				<?php

				do_action( 'catamaran_action_before_footer' );

				// Footer
				$catamaran_footer_type = catamaran_get_theme_option( 'footer_type' );
				if ( 'custom' == $catamaran_footer_type && ! catamaran_is_layouts_available() ) {
					$catamaran_footer_type = 'default';
				}
				get_template_part( apply_filters( 'catamaran_filter_get_template_part', "templates/footer-" . sanitize_file_name( $catamaran_footer_type ) ) );

				do_action( 'catamaran_action_after_footer' );

			}
			?>

			<?php do_action( 'catamaran_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'catamaran_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'catamaran_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>
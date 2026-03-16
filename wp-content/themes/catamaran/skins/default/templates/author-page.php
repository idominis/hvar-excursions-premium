<?php
/**
 * The template to display the user's avatar, bio and socials on the Author page
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.71.0
 */
?>

<div class="author_page author vcard"<?php
	if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
		?> itemprop="author" itemscope="itemscope" itemtype="<?php echo esc_attr( catamaran_get_protocol( true ) ); ?>//schema.org/Person"<?php
	}
?>>

	<div class="author_avatar"<?php
		if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
			?> itemprop="image"<?php
		}
	?>>
		<?php
		$catamaran_mult = catamaran_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 120 * $catamaran_mult );
		?>
	</div>

	<h4 class="author_title"<?php
		if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
			?> itemprop="name"<?php
		}
	?>><span class="fn"><?php the_author(); ?></span></h4>

	<?php
	$catamaran_author_description = get_the_author_meta( 'description' );
	if ( ! empty( $catamaran_author_description ) ) {
		?>
		<div class="author_bio"<?php
			if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
				?> itemprop="description"<?php
			}
		?>><?php echo wp_kses( wpautop( $catamaran_author_description ), 'catamaran_kses_content' ); ?></div>
		<?php
	}
	?>

	<div class="author_details">
		<span class="author_posts_total">
			<?php
			$catamaran_posts_total = count_user_posts( get_the_author_meta('ID'), 'post' );
			if ( $catamaran_posts_total > 0 ) {
				// Translators: Add the author's posts number to the message
				echo wp_kses( sprintf( _n( '%s article published', '%s articles published', $catamaran_posts_total, 'catamaran' ),
										'<span class="author_posts_total_value">' . number_format_i18n( $catamaran_posts_total ) . '</span>'
								 		),
							'catamaran_kses_content'
							);
			} else {
				esc_html_e( 'No posts published.', 'catamaran' );
			}
			?>
		</span><?php
			ob_start();
			do_action( 'catamaran_action_user_meta', 'author-page' );
			$catamaran_socials = ob_get_contents();
			ob_end_clean();
			catamaran_show_layout( $catamaran_socials,
				'<span class="author_socials"><span class="author_socials_caption">' . esc_html__( 'Follow:', 'catamaran' ) . '</span>',
				'</span>'
			);
		?>
	</div>

</div>

<?php
/**
 * The template to display the Author bio
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */
?>

<div class="author_info author vcard"<?php
	if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
		?> itemprop="author" itemscope="itemscope" itemtype="<?php echo esc_attr( catamaran_get_protocol( true ) ); ?>//schema.org/Person"<?php
	}
?>>

	<div class="author_avatar"<?php
		if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
			?> itemprop="image"<?php
	}
	?>>
		<a class="author_avatar_link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
			<?php
			$catamaran_mult = catamaran_get_retina_multiplier();
			echo get_avatar( get_the_author_meta( 'user_email' ), 120 * $catamaran_mult );
			?>
		</a>
	</div>

	<div class="author_description">
		<h5 class="author_title"<?php
			if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
				?> itemprop="name"<?php
			}
		?>><a class="author_link fn" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php
			the_author();
		?></a></h5>
		<div class="author_label"><?php esc_html_e( 'About Author', 'catamaran' ); ?></div>
		<div class="author_bio"<?php
			if ( catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) ) ) {
				?> itemprop="description"<?php
			}
		?>>
			<?php echo wp_kses( wpautop( get_the_author_meta( 'description' ) ), 'catamaran_kses_content' ); ?>
			<div class="author_links">
				<?php do_action( 'catamaran_action_user_meta', 'author-bio' ); ?>
			</div>
		</div>

	</div>

</div>

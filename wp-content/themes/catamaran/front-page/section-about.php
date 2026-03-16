<div class="front_page_section front_page_section_about<?php
	$catamaran_scheme = catamaran_get_theme_option( 'front_page_about_scheme' );
	if ( ! empty( $catamaran_scheme ) && ! catamaran_is_inherit( $catamaran_scheme ) ) {
		echo ' scheme_' . esc_attr( $catamaran_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( catamaran_get_theme_option( 'front_page_about_paddings' ) );
	if ( catamaran_get_theme_option( 'front_page_about_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$catamaran_css      = '';
		$catamaran_bg_image = catamaran_get_theme_option( 'front_page_about_bg_image' );
		if ( ! empty( $catamaran_bg_image ) ) {
			$catamaran_css .= 'background-image: url(' . esc_url( catamaran_get_attachment_url( $catamaran_bg_image ) ) . ');';
		}
		if ( ! empty( $catamaran_css ) ) {
			echo ' style="' . esc_attr( $catamaran_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$catamaran_anchor_icon = catamaran_get_theme_option( 'front_page_about_anchor_icon' );
	$catamaran_anchor_text = catamaran_get_theme_option( 'front_page_about_anchor_text' );
if ( ( ! empty( $catamaran_anchor_icon ) || ! empty( $catamaran_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_about"'
									. ( ! empty( $catamaran_anchor_icon ) ? ' icon="' . esc_attr( $catamaran_anchor_icon ) . '"' : '' )
									. ( ! empty( $catamaran_anchor_text ) ? ' title="' . esc_attr( $catamaran_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_about_inner
	<?php
	if ( catamaran_get_theme_option( 'front_page_about_fullheight' ) ) {
		echo ' catamaran-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$catamaran_css           = '';
			$catamaran_bg_mask       = catamaran_get_theme_option( 'front_page_about_bg_mask' );
			$catamaran_bg_color_type = catamaran_get_theme_option( 'front_page_about_bg_color_type' );
			if ( 'custom' == $catamaran_bg_color_type ) {
				$catamaran_bg_color = catamaran_get_theme_option( 'front_page_about_bg_color' );
			} elseif ( 'scheme_bg_color' == $catamaran_bg_color_type ) {
				$catamaran_bg_color = catamaran_get_scheme_color( 'bg_color', $catamaran_scheme );
			} else {
				$catamaran_bg_color = '';
			}
			if ( ! empty( $catamaran_bg_color ) && $catamaran_bg_mask > 0 ) {
				$catamaran_css .= 'background-color: ' . esc_attr(
					1 == $catamaran_bg_mask ? $catamaran_bg_color : catamaran_hex2rgba( $catamaran_bg_color, $catamaran_bg_mask )
				) . ';';
			}
			if ( ! empty( $catamaran_css ) ) {
				echo ' style="' . esc_attr( $catamaran_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_about_content_wrap content_wrap">
			<?php
			// Caption
			$catamaran_caption = catamaran_get_theme_option( 'front_page_about_caption' );
			if ( ! empty( $catamaran_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_about_caption front_page_block_<?php echo ! empty( $catamaran_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $catamaran_caption, 'catamaran_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$catamaran_description = catamaran_get_theme_option( 'front_page_about_description' );
			if ( ! empty( $catamaran_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_about_description front_page_block_<?php echo ! empty( $catamaran_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $catamaran_description ), 'catamaran_kses_content' ); ?></div>
				<?php
			}

			// Content
			$catamaran_content = catamaran_get_theme_option( 'front_page_about_content' );
			if ( ! empty( $catamaran_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_content front_page_section_about_content front_page_block_<?php echo ! empty( $catamaran_content ) ? 'filled' : 'empty'; ?>">
					<?php
					$catamaran_page_content_mask = '%%CONTENT%%';
					if ( strpos( $catamaran_content, $catamaran_page_content_mask ) !== false ) {
						$catamaran_content = preg_replace(
							'/(\<p\>\s*)?' . $catamaran_page_content_mask . '(\s*\<\/p\>)/i',
							sprintf(
								'<div class="front_page_section_about_source">%s</div>',
								apply_filters( 'the_content', get_the_content() )
							),
							$catamaran_content
						);
					}
					catamaran_show_layout( $catamaran_content );
					?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

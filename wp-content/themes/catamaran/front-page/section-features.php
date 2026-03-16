<div class="front_page_section front_page_section_features<?php
	$catamaran_scheme = catamaran_get_theme_option( 'front_page_features_scheme' );
	if ( ! empty( $catamaran_scheme ) && ! catamaran_is_inherit( $catamaran_scheme ) ) {
		echo ' scheme_' . esc_attr( $catamaran_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( catamaran_get_theme_option( 'front_page_features_paddings' ) );
	if ( catamaran_get_theme_option( 'front_page_features_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$catamaran_css      = '';
		$catamaran_bg_image = catamaran_get_theme_option( 'front_page_features_bg_image' );
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
	$catamaran_anchor_icon = catamaran_get_theme_option( 'front_page_features_anchor_icon' );
	$catamaran_anchor_text = catamaran_get_theme_option( 'front_page_features_anchor_text' );
if ( ( ! empty( $catamaran_anchor_icon ) || ! empty( $catamaran_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_features"'
									. ( ! empty( $catamaran_anchor_icon ) ? ' icon="' . esc_attr( $catamaran_anchor_icon ) . '"' : '' )
									. ( ! empty( $catamaran_anchor_text ) ? ' title="' . esc_attr( $catamaran_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_features_inner
	<?php
	if ( catamaran_get_theme_option( 'front_page_features_fullheight' ) ) {
		echo ' catamaran-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$catamaran_css      = '';
			$catamaran_bg_mask  = catamaran_get_theme_option( 'front_page_features_bg_mask' );
			$catamaran_bg_color_type = catamaran_get_theme_option( 'front_page_features_bg_color_type' );
			if ( 'custom' == $catamaran_bg_color_type ) {
				$catamaran_bg_color = catamaran_get_theme_option( 'front_page_features_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_features_content_wrap content_wrap">
			<?php
			// Caption
			$catamaran_caption = catamaran_get_theme_option( 'front_page_features_caption' );
			if ( ! empty( $catamaran_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_features_caption front_page_block_<?php echo ! empty( $catamaran_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $catamaran_caption, 'catamaran_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$catamaran_description = catamaran_get_theme_option( 'front_page_features_description' );
			if ( ! empty( $catamaran_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_features_description front_page_block_<?php echo ! empty( $catamaran_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $catamaran_description ), 'catamaran_kses_content' ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_features_output">
				<?php
				if ( is_active_sidebar( 'front_page_features_widgets' ) ) {
					dynamic_sidebar( 'front_page_features_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! catamaran_exists_trx_addons() ) {
						catamaran_customizer_need_trx_addons_message();
					} else {
						catamaran_customizer_need_widgets_message( 'front_page_features_caption', 'ThemeREX Addons - Services' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>

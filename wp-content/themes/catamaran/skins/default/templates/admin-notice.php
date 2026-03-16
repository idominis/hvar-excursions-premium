<?php
/**
 * The template to display Admin notices
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.1
 */

$catamaran_theme_slug = get_option( 'template' );
$catamaran_theme_obj  = wp_get_theme( $catamaran_theme_slug );
?>
<div class="catamaran_admin_notice catamaran_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$catamaran_theme_img = catamaran_get_file_url( 'screenshot.jpg' );
	if ( '' != $catamaran_theme_img ) {
		?>
		<div class="catamaran_notice_image"><img src="<?php echo esc_url( $catamaran_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'catamaran' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="catamaran_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'catamaran' ),
				$catamaran_theme_obj->get( 'Name' ) . ( CATAMARAN_THEME_FREE ? ' ' . __( 'Free', 'catamaran' ) : '' ),
				$catamaran_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="catamaran_notice_text">
		<p class="catamaran_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $catamaran_theme_obj->description ) );
			?>
		</p>
		<p class="catamaran_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'catamaran' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="catamaran_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=catamaran_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'catamaran' );
			?>
		</a>
	</div>
</div>

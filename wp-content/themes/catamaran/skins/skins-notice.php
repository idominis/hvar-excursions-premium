<?php
/**
 * The template to display Admin notices
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.64
 */

$catamaran_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$catamaran_skins_args = get_query_var( 'catamaran_skins_notice_args' );
?>
<div class="catamaran_admin_notice catamaran_skins_notice notice notice-info is-dismissible" data-notice="skins">
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
		<?php esc_html_e( 'New skins are available', 'catamaran' ); ?>
	</h3>
	<?php

	// Description
	$catamaran_total      = $catamaran_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$catamaran_skins_msg  = $catamaran_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $catamaran_total, 'catamaran' ), $catamaran_total ) . '</strong>'
							: '';
	$catamaran_total      = $catamaran_skins_args['free'];
	$catamaran_skins_msg .= $catamaran_total > 0
							? ( ! empty( $catamaran_skins_msg ) ? ' ' . esc_html__( 'and', 'catamaran' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $catamaran_total, 'catamaran' ), $catamaran_total ) . '</strong>'
							: '';
	$catamaran_total      = $catamaran_skins_args['pay'];
	$catamaran_skins_msg .= $catamaran_skins_args['pay'] > 0
							? ( ! empty( $catamaran_skins_msg ) ? ' ' . esc_html__( 'and', 'catamaran' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $catamaran_total, 'catamaran' ), $catamaran_total ) . '</strong>'
							: '';
	?>
	<div class="catamaran_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'catamaran' ), $catamaran_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="catamaran_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $catamaran_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			esc_html_e( 'Go to Skins manager', 'catamaran' );
			?>
		</a>
		<?php
		// Dismiss notice for 7 days
		?>
		<a href="#" role="button" class="button button-secondary catamaran_notice_button_dismiss" data-notice="skins"><i class="dashicons dashicons-no-alt"></i> 
			<?php
			esc_html_e( 'Dismiss', 'catamaran' );
			?>
		</a>
		<?php
		// Hide notice forever
		?>
		<a href="#" role="button" class="button button-secondary catamaran_notice_button_hide" data-notice="skins"><i class="dashicons dashicons-no-alt"></i> 
			<?php
			esc_html_e( 'Never show again', 'catamaran' );
			?>
		</a>
	</div>
</div>

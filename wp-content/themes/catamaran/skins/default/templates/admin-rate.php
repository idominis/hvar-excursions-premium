<?php
/**
 * The template to display Admin notices
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0.1
 */

$catamaran_theme_slug = get_template();
$catamaran_theme_obj  = wp_get_theme( $catamaran_theme_slug );

?>
<div class="catamaran_admin_notice catamaran_rate_notice notice notice-info is-dismissible" data-notice="rate">
	<?php
	// Theme image
	$catamaran_theme_img = catamaran_get_file_url( 'screenshot.jpg' );
	if ( '' != $catamaran_theme_img ) {
		?>
		<div class="catamaran_notice_image"><img src="<?php echo esc_url( $catamaran_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'catamaran' ); ?>"></div>
		<?php
	}

	// Title
	$catamaran_theme_name = '"' . $catamaran_theme_obj->get( 'Name' ) . ( CATAMARAN_THEME_FREE ? ' ' . __( 'Free', 'catamaran' ) : '' ) . '"';
	?>
	<h3 class="catamaran_notice_title"><a href="<?php echo esc_url( catamaran_storage_get( 'theme_rate_url' ) ); ?>"<?php if ( function_exists( 'catamaran_external_links_target' ) ) echo catamaran_external_links_target( true ); ?>>
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name to the 'Welcome' message
				__( 'Help Us Grow - Rate %s Today!', 'catamaran' ),
				$catamaran_theme_name
			)
		);
		?>
	</a></h3>
	<?php

	// Description
	?>
	<div class="catamaran_notice_text">
		<p><?php
			// Translators: Add theme name to the 'Welcome' message
			echo wp_kses_data( sprintf( __( "Thank you for choosing the %s theme for your website! We're excited to see how you've customized your site, and we hope you've enjoyed working with our theme.", 'catamaran' ), $catamaran_theme_name ) );
		?></p>
		<p><?php
			// Translators: Add theme name to the 'Welcome' message
			echo wp_kses_data( sprintf( __( "Your feedback really matters to us! If you've had a positive experience, we'd love for you to take a moment to rate %s and share your thoughts on the customer service you received.", 'catamaran' ), $catamaran_theme_name ) );
		?></p>
	</div>
	<?php

	// Buttons
	?>
	<div class="catamaran_notice_buttons">
		<?php
		// Link to the theme download page
		?>
		<a href="<?php echo esc_url( catamaran_storage_get( 'theme_rate_url' ) ); ?>" class="button button-primary"<?php if ( function_exists( 'catamaran_external_links_target' ) ) echo catamaran_external_links_target( true ); ?>><i class="dashicons dashicons-star-filled"></i> 
			<?php
			// Translators: Add the theme name to the button caption
			echo esc_html( sprintf( __( 'Rate %s Now', 'catamaran' ), $catamaran_theme_name ) );
			?>
		</a>
		<?php
		// Link to the theme support
		?>
		<a href="<?php echo esc_url( catamaran_storage_get( 'theme_support_url' ) ); ?>" class="button"<?php if ( function_exists( 'catamaran_external_links_target' ) ) echo catamaran_external_links_target( true ); ?>><i class="dashicons dashicons-sos"></i> 
			<?php
			esc_html_e( 'Support', 'catamaran' );
			?>
		</a>
		<?php
		// Link to the theme documentation
		?>
		<a href="<?php echo esc_url( catamaran_storage_get( 'theme_doc_url' ) ); ?>" class="button"<?php if ( function_exists( 'catamaran_external_links_target' ) ) echo catamaran_external_links_target( true ); ?>><i class="dashicons dashicons-book"></i> 
			<?php
			esc_html_e( 'Documentation', 'catamaran' );
			?>
		</a>
	</div>
</div>

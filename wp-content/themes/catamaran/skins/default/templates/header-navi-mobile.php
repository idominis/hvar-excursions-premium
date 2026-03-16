<?php
/**
 * The template to show mobile menu (used only header_style == 'default')
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

$catamaran_show_widgets = catamaran_get_theme_option( 'widgets_menu_mobile_fullscreen' );
$catamaran_show_socials = catamaran_get_theme_option( 'menu_mobile_socials' );

?>
<div class="menu_mobile_overlay scheme_dark"></div>
<div class="menu_mobile menu_mobile_<?php echo esc_attr( catamaran_get_theme_option( 'menu_mobile_fullscreen' ) > 0 ? 'fullscreen' : 'narrow' ); ?> scheme_dark">
	<div class="menu_mobile_inner<?php echo esc_attr( $catamaran_show_widgets == 1  ? ' with_widgets' : '' ); ?>">
        <div class="menu_mobile_header_wrap">
            <?php
            // Logo
            set_query_var( 'catamaran_logo_args', array( 'type' => 'mobile' ) );
            get_template_part( apply_filters( 'catamaran_filter_get_template_part', 'templates/header-logo' ) );
            set_query_var( 'catamaran_logo_args', array() ); ?>

            <span class="menu_mobile_close menu_button_close" tabindex="0"><span class="menu_button_close_text"><?php esc_html_e('Close', 'catamaran')?></span><span class="menu_button_close_icon"></span></span>
        </div>
        <div class="menu_mobile_content_wrap content_wrap">
            <div class="menu_mobile_content_wrap_inner<?php echo esc_attr($catamaran_show_socials ? '' : ' without_socials'); ?>"><?php
            // Mobile menu
            $catamaran_menu_mobile = catamaran_get_nav_menu( 'menu_mobile' );
            if ( empty( $catamaran_menu_mobile ) ) {
                $catamaran_menu_mobile = apply_filters( 'catamaran_filter_get_mobile_menu', '' );
                if ( empty( $catamaran_menu_mobile ) ) {
                    $catamaran_menu_mobile = catamaran_get_nav_menu( 'menu_main' );
                    if ( empty( $catamaran_menu_mobile ) ) {
                        $catamaran_menu_mobile = catamaran_get_nav_menu();
                    }
                }
            }
            if ( ! empty( $catamaran_menu_mobile ) ) {
                // Change attribute 'id' - add prefix 'mobile-' to prevent duplicate id on the page
                $catamaran_menu_mobile = preg_replace( '/([\s]*id=")/', '${1}mobile-', $catamaran_menu_mobile );
                // Change main menu classes
                $catamaran_menu_mobile = str_replace(
                array( 'menu_main',   'sc_layouts_menu_nav', 'sc_layouts_menu ' ), // , 'sc_layouts_hide_on_mobile', 'hide_on_mobile'
                array( 'menu_mobile', '', ' ' ), // , '', ''
                    $catamaran_menu_mobile
                );
                // Wrap menu to the <nav> if not present
                if ( strpos( $catamaran_menu_mobile, '<nav ' ) !== 0 ) {	// condition !== false is not allowed, because menu can contain inner <nav> elements (in the submenu layouts)
				$catamaran_menu_mobile = catamaran_is_on( catamaran_get_theme_option( 'seo_snippets' ) )
					? sprintf( '<nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="%1$s//schema.org/SiteNavigationElement">%2$s</nav>', esc_attr( catamaran_get_protocol( true ) ), $catamaran_menu_mobile )
					: sprintf( '<nav class="menu_mobile_nav_area">%s</nav>', $catamaran_menu_mobile );
                }
                // Show menu
                catamaran_show_layout( apply_filters( 'catamaran_filter_menu_mobile_layout', $catamaran_menu_mobile ) );
            }
            // Social icons
            if($catamaran_show_socials) {
                catamaran_show_layout( catamaran_get_socials_links(), '<div class="socials_mobile">', '</div>' );
            }            
            ?>
            </div>
		</div><?php

        if ( $catamaran_show_widgets == 1 )  {
            ?><div class="menu_mobile_widgets_area"><?php
            // Create Widgets Area
            catamaran_create_widgets_area( 'widgets_additional_menu_mobile_fullscreen' );
            ?></div><?php
        } ?>

    </div>
</div>

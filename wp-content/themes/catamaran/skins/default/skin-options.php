<?php
/**
 * Skin Options
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.76.0
 */


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc. or overriden values from the post/page meta)

if ( ! function_exists( 'catamaran_create_theme_options' ) ) {

	function catamaran_create_theme_options() {

		// Message about options override.
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = esc_html__( 'Attention! Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages. If you changed such parameter and nothing happened on the page, this option may be overridden in the corresponding section or in the Page Options of this page. These options are marked with an asterisk (*) in the title.', 'catamaran' );

		// Color schemes number: if < 2 - hide fields with selectors
		$hide_schemes = count( catamaran_storage_get( 'schemes' ) ) < 2;

		$trx_addons_present = function_exists( 'catamaran_exists_trx_addons' ) ? catamaran_exists_trx_addons() : defined( 'TRX_ADDONS_VERSION' );
		if ( $trx_addons_present && ! function_exists( 'catamaran_exists_trx_addons' ) ) {
			$trx_addons_plugin_path = catamaran_get_file_dir( 'plugins/trx_addons/trx_addons.php' );
			if ( ! empty( $trx_addons_plugin_path ) ) {
				require_once $trx_addons_plugin_path;
			}
			trx_addons_set_admin_message(
				esc_html__( 'The new skin version may not be fully compatible with your current theme version. Please update the theme or temporarily revert to the previous skin version.', 'catamaran' )
				. '<br><br>'
				. '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '" class="trx_addons_button trx_addons_button_small trx_addons_button_accent">'
					. esc_html__( 'Go to Dashboard - Updates', 'catamaran' )
				. '</a>'
				. '|'
				. esc_html__( 'Theme Update Required', 'catamaran' ),
				'error'
			);
		}

		catamaran_storage_set(

			'options', array(

				// 'Logo & Site Identity'
				//---------------------------------------------
				'title_tagline'                 => array(
					'title'    => esc_html__( 'Logo & Site Identity', 'catamaran' ),
					'desc'     => '',
					'priority' => 10,
					'icon'     => 'icon-home-2',
					'type'     => 'section',
				),
				'logo_info'                     => array(
					'title'    => esc_html__( 'Logo Settings', 'catamaran' ),
					'desc'     => '',
					'priority' => 20,
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'type'     => 'info',
				),
				'logo_text'                     => array(
					'title'    => esc_html__( 'Use Site Name as Logo', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Use the site title and tagline as a text logo if no image is selected', 'catamaran' ) ),
					'priority' => 30,
					'std'      => 1,
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'switch',
				),
				'logo_zoom'                     => array(
					'title'      => esc_html__( 'Logo zoom', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Zoom the logo (set 1 to leave original size). For this parameter to affect images, their max-height should be specified in "em" instead of "px" during header creation. In this case, maximum logo size depends on the actual size of the picture.', 'catamaran' ) ),
					'std'        => 1,
					'min'        => 0.2,
					'max'        => 2,
					'step'       => 0.1,
					'refresh'    => false,
					'show_value' => true,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'slider',
				),
				'logo_retina_enabled'           => array(
					'title'    => esc_html__( 'Allow retina display logo', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Show fields to select logo images for Retina display', 'catamaran' ) ),
					'priority' => 40,
					'refresh'  => false,
					'std'      => 0,
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'switch',
				),
				// Parameter 'logo' was replaced with standard WordPress 'custom_logo'
				'logo_retina'                   => array(
					'title'      => esc_html__( 'Logo for Retina', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'catamaran' ) ),
					'priority'   => 70,
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'image',
				),
				'logo_mobile_header'            => array(
					'title' => esc_html__( 'Logo for the mobile header', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile header (if enabled in the section "Header - Header mobile")', 'catamaran' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_header_retina'     => array(
					'title'      => esc_html__( 'Logo for the mobile header on Retina', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'catamaran' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'image',
				),
				'logo_mobile'                   => array(
					'title' => esc_html__( 'Logo for the mobile menu', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile menu', 'catamaran' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_retina'            => array(
					'title'      => esc_html__( 'Logo mobile on Retina', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'catamaran' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'image',
				),
				'logo_side'                     => array(
					'title' => esc_html__( 'Logo for the side menu', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu', 'catamaran' ) ),
					'std'   => '',
					'type'  => 'hidden',
				),
				'logo_side_retina'              => array(
					'title'      => esc_html__( 'Logo for the side menu on Retina', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'catamaran' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden',
				),



				// 'General settings'
				//---------------------------------------------
				'general'                       => array(
					'title'    => esc_html__( 'General', 'catamaran' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 20,
					'icon'     => 'icon-settings',
                    'demo'     => true,
					'type'     => 'section',
				),

				'general_layout_info'           => array(
					'title'  => esc_html__( 'Layout', 'catamaran' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'catamaran' ),
                    'demo'     => true,
					'type'   => 'info',
				),
				'body_style'                    => array(
					'title'    => esc_html__( 'Body style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'refresh'  => false,
					'std'      => 'wide',
					'options'  => catamaran_get_list_body_styles( false ),
                    'demo'     => true,
					'type'     => 'choice',
				),
				'page_width'                    => array(
					'title'      => esc_html__( 'Page width', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Total width of the site content and sidebar (in pixels). If empty - use default width', 'catamaran' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed', 'wide' ),
					),
					'std'        => catamaran_theme_defaults( 'page_width' ),
					'min'        => 1000,
					'max'        => 1600,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_width',               // SASS variable's name to preview changes 'on fly'
					'pro_only'   => CATAMARAN_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'page_boxed_extra'             => array(
					'title'      => esc_html__( 'Boxed page extra spaces', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Width of the extra side space on boxed pages', 'catamaran' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'std'        => catamaran_theme_defaults( 'page_boxed_extra' ),
					'min'        => 0,
					'max'        => 150,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_boxed_extra',   // SASS variable's name to preview changes 'on fly'
					'pro_only'   => CATAMARAN_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'boxed_bg_image'                => array(
					'title'      => esc_html__( 'Boxed bg image', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload image for the background of the boxed content.', 'catamaran' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'        => '',
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'type'       => 'image',
				),
				'remove_margins'                => array(
					'title'    => esc_html__( 'Page margins', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Add margins above and below the content area', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'refresh'  => false,
					'std'      => 0,
					'options'  => catamaran_get_list_remove_margins(),
					'type'     => 'choice',
				),

				'general_menu_info'             => array(
					'title' => esc_html__( 'Navigation', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				'menu_side'                     => array(
					'title'    => esc_html__( 'Sidemenu position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position of the side menu - panel with icons (ancors) for inner-page navigation. Use this menu if shortcodes "Ancor" are present on the page.', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'      => 'none',
					'options'  => array(
						'hide'  => array(
										'title' => esc_html__( 'No menu', 'catamaran' ),
										'icon'  => 'images/theme-options/menu-side/hide.png',
									),
						'left'  => array(
										'title' => esc_html__( 'Left menu', 'catamaran' ),
										'icon'  => 'images/theme-options/menu-side/left.png',
									),
						'right' => array(
										'title' => esc_html__( 'Right menu', 'catamaran' ),
										'icon'  => 'images/theme-options/menu-side/right.png',
									),
					),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'hidden',
				),
				'menu_side_icons'               => array(
					'title'      => esc_html__( 'Iconed sidemenu', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Get icons from anchors and display them in the sidemenu, or mark sidemenu items with simple dots', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
					),
					'std'        => 1,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden',
				),
				'menu_side_stretch'             => array(
					'title'      => esc_html__( 'Stretch sidemenu', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Stretch sidemenu to window height (if menu items number >= 5)', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
						'menu_side_icons' => array( 1 )
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden',
				),				
				'menu_mobile_fullscreen'        => array(
					'title' => esc_html__( 'Mobile menu fullscreen', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Display mobile and side menus on full screen (if checked) or slide narrow menu from the left or from the right side (if not checked)', 'catamaran' ) ),
					'std'   => 1,
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'hidden',
				),
				'menu_mobile_socials'           =>  array(
                    'title' => esc_html__( 'Mobile menu socials', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Show socials in the Mobile Menu. Socials are available only if plugin ThemeREX Addons is active', 'catamaran' ) ),
					'std'   => 1,
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'switch',
                ),
                'widgets_menu_mobile_fullscreen'                => array(
                    'title'    => esc_html__( 'Mobile menu fullscreen widgets', 'catamaran' ),
                    'desc'     => wp_kses_data( __( 'Show widgets on the Fullscreen Mobile Menu', 'catamaran' ) ),
                    'dependency' => array(
                        'menu_mobile_fullscreen' => array( 1 )
                    ),
                    'refresh'  => false,
                    'std'      => 1,
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type'     => 'switch',
                ),
                'widgets_additional_menu_mobile_fullscreen'            => array(
                    'title'    => esc_html__( 'Select mobile menu widgets', 'catamaran' ),
                    'desc'     => wp_kses_data( __( 'Select widgets to show on the Fullscreen Mobile Menu', 'catamaran' ) ),
                    'dependency' => array(
                        'menu_mobile_fullscreen' => array( 1 ),
                        'widgets_menu_mobile_fullscreen' => array( 1 )
                    ),
                    'std'      => 'hide',
                    'options'  => array(),
                    'pro_only' => CATAMARAN_THEME_FREE,
                    'type'     => 'select',
                ),
                'scroll_to_top_style'           =>  array(
                    'title'    => esc_html__( 'Scroll to top style', 'catamaran' ),
                    'desc'     => wp_kses_data( __( 'Select scroll to top style. Scroll to top style are available only if plugin ThemeREX Addons is active', 'catamaran' ) ),
                    'std'      => 'default',
                    'options' => array(
                        'default'   => esc_html__( 'Default', 'catamaran' ),
                        'modern'    => esc_html__( 'Modern', 'catamaran' ),
                    ),
                    'override'   => array(
                        'mode'    => 'page',
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type' => 'select',
                ),
                'scroll_to_top_scheme_watchers' => array(
                    'title' => esc_html__('Scroll to top color scheme watchers', 'catamaran'),
                    'desc' => wp_kses_data( __('Monitors what color scheme the object is located', 'catamaran') ),
                    'std' => 0,
                    'override'   => array(
                        'mode'    => 'page',
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
                    'dependency' => array(
                        'scroll_to_top_style' => 'modern'
                    ),
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type' => 'switch'
                ),
                'general_elements_info'          => array(
                    'title' => esc_html__( 'Sticky Elements', 'catamaran' ),
                    'desc'  => '',
                    'type'  => 'info',
                ),
                'sticky_socials' => array(
                    'title' => esc_html__('Sticky socials', 'catamaran'),
                    'desc' => wp_kses_data( __('Show sticky socials. Socials are available only if plugin ThemeREX Addons is active', 'catamaran') ),
                    'std' => 0,
                    'override'   => array(
                        'mode'    => 'page',
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type' => 'switch'
                ),
                'sticky_socials_style'           =>  array(
                    'title'    => esc_html__( 'Sticky socials style', 'catamaran' ),
                    'desc'     => wp_kses_data( __( 'Select sticky socials style', 'catamaran' ) ),
                    'std'      => 'default',
                    'options' => array(
                        'default'   => esc_html__( 'Default', 'catamaran' ),
                        'modern'    => esc_html__( 'Modern', 'catamaran' ),
                    ),
                    'override'   => array(
                        'mode'    => 'page',
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
                    'dependency' => array(
                        'sticky_socials' => array( 1 ),
                    ),
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type' => 'select',
                ),
                'sticky_socials_scheme_watchers' => array(
                    'title' => esc_html__('Sticky socials color scheme watchers', 'catamaran'),
                    'desc' => wp_kses_data( __('Monitors what color scheme the object is located', 'catamaran') ),
                    'std' => 0,
                    'override'   => array(
                        'mode'    => 'page',
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
                    'dependency' => array(
                        'sticky_socials' => array( 1 ),
                    ),
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type' => 'switch'
                ),
                'general_sidebar_info'          => array(
                    'title' => esc_html__( 'Sidebar', 'catamaran' ),
                    'desc'  => '',
                    'demo'  => true,
                    'type'  => 'info',
                ),
				'sidebar_position'              => array(
					'title'    => esc_html__( 'Sidebar position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'      => 'right',
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'options'  => array(),
                    'demo'      => true,
					'type'     => 'choice',
				),
				'sidebar_position_ss'       => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'catamaran' ),
					'desc'     => wp_kses_data( __( "Select position to move sidebar (if it's not hidden) on the small screen - above or below the content", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_ss_single'
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					// Commented to enable a default value for other modes where sidebar is visible
					//'dependency' => array(
					//	'sidebar_position' => array( '^hide' ),
					//),
					'std'      => 'below',
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type'              => array(
					'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style'                 => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type' => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets'               => array(
					'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_widgets_single'
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type'     => array( 'default')
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'type'       => 'select',
				),
				'sidebar_width'                 => array(
					'title'      => esc_html__( 'Sidebar width', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width (minimum sidebar width might vary based on its location and type)', 'catamaran' ) ),
					'std'        => catamaran_theme_defaults( 'sidebar_width' ),
					'min'        => 150,
					'max'        => 500,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_width',      // SASS variable's name to preview changes 'on fly'
					'pro_only'   => CATAMARAN_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'sidebar_gap'                   => array(
					'title'      => esc_html__( 'Sidebar gap', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'catamaran' ) ),
					'std'        => catamaran_theme_defaults( 'sidebar_gap' ),
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_gap',  // SASS variable's name to preview changes 'on fly'
					'pro_only'   => CATAMARAN_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'sidebar_proportional'          => array(
					'title'      => esc_html__( 'Sidebar proportional', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Change the width of the sidebar and gap proportionally when the window is resized, or leave the width of the sidebar constant', 'catamaran' ) ),
					'refresh'    => false,
					'customizer' => 'sidebar_proportional',  // SASS variable's name to preview changes 'on fly'
					'std'        => 1,
					'type'       => 'switch',
				),
				'expand_content'                => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'catamaran' ) ),
					'refresh' => false,
					'override'   => array(
                        'mode'    => 'page',		// Override parameters for single posts moved to the 'expand_content_single'
                        'section' => esc_html__( 'Content', 'catamaran' ),
                    ),
					'options' => catamaran_get_list_expand_content(),
					'std'     => 'expand',
					'type'    => 'choice',
				),

				'general_widgets_info'          => array(
					'title' => esc_html__( 'Additional widgets', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				'widgets_above_page'            => array(
					'title'    => esc_html__( 'Widgets at the top of the page', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'catamaran' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'select',
				),
				'widgets_above_content'         => array(
					'title'    => esc_html__( 'Widgets above the content', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'catamaran' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'select',
				),
				'widgets_below_content'         => array(
					'title'    => esc_html__( 'Widgets below the content', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'catamaran' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'select',
				),
				'widgets_below_page'            => array(
					'title'    => esc_html__( 'Widgets at the bottom of the page', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'catamaran' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'select',
				),

				'general_effects_info'          => array(
					'title' => esc_html__( 'Design & Effects', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'border_radius'                 => array(
					'title'      => esc_html__( 'Border radius', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Specify the border radius of the form fields and buttons in pixels', 'catamaran' ) ),
					'std'        => catamaran_theme_defaults( 'rad' ),
					'min'        => 0,
					'max'        => 20,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'rad',      // SASS name to preview changes 'on fly'
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden', // old value "slider"
				),

				'general_misc_info'             => array(
					'title' => esc_html__( 'Miscellaneous', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				'seo_snippets'                  => array(
					'title' => esc_html__( 'SEO snippets', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Add structured data markup to the single posts and pages', 'catamaran' ) ),
					'std'   => 0,
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'switch',
				),
				'privacy_text' => array(
					"title" => esc_html__("Text with Privacy Policy link", 'catamaran'),
					"desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'catamaran') ),
					"std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'catamaran'), 'catamaran_kses_content' ),
					"type"  => "textarea"
				),



				// 'Header'
				//---------------------------------------------
				'header'                        => array(
					'title'    => esc_html__( 'Header', 'catamaran' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 30,
					'icon'     => 'icon-header',
					'type'     => 'section',
				),

				'header_style_info'             => array(
					'title' => esc_html__( 'Header style', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type'                   => array(
					'title'    => esc_html__( 'Header style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'header_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'dependency' => array(
						'header_type' => array( 'custom' ),
					),
					'std'        => 'header-custom-elementor-header-default',
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				'header_position'               => array(
					'title'    => esc_html__( 'Header position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select site header position', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_fullheight'             => array(
					'title'    => esc_html__( 'Header fullheight', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'      => 0,
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'switch',
				),
				'header_wide'                   => array(
					'title'      => esc_html__( 'Header fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'        => 1,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_zoom'                   => array(
					'title'   => esc_html__( 'Header title zoom', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Zoom the header title. 1 - original size', 'catamaran' ) ),
					'std'     => 1,
					'min'     => 0.2,
					'max'     => 2,
					'step'    => 0.1,
					'show_value' => true,
					'refresh' => false,
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'slider',
				),

				'header_widgets_info'           => array(
					'title' => esc_html__( 'Header widgets', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Here you can place a widget slider, advertising banners, etc.', 'catamaran' ) ),
					'type'  => 'info',
				),
				'header_widgets'                => array(
					'title'    => esc_html__( 'Header widgets', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select set of widgets to show in the header on each page', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
						'desc'    => wp_kses_data( __( 'Select set of widgets to show in the header on this page', 'catamaran' ) ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => 'select',
				),
				'header_columns'                => array(
					'title'      => esc_html__( 'Header columns', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'dependency' => array(
						'header_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => catamaran_get_list_range( 0, 6 ),
					'type'       => 'select',
				),


				'header_style_info_404'             => array(
					'title' => esc_html__( 'Header style 404', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_404'                   => array(
					'title'    => esc_html__( 'Header style 404', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! catamaran_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style_404'                  => array(
					'title'      => esc_html__( 'Select custom layout 404', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'header_type_404' => array( 'custom' ),
					),
					'std'        => 'header-custom-elementor-header-default',
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),


				'header_image_info'             => array(
					'title' => esc_html__( 'Header image', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				'header_image_override'         => array(
					'title'    => esc_html__( 'Header image override', 'catamaran' ),
					'desc'     => wp_kses_data( __( "Allow overriding the header image with a featured image of the page, post, product, etc.", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'      => 0,
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'switch',
				),

				'header_mobile_info'            => array(
					'title'      => esc_html__( 'Mobile header', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Configure the mobile version of the header', 'catamaran' ) ),
					'priority'   => 500,
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'info',
				),
				'header_mobile_enabled'         => array(
					'title'      => esc_html__( 'Enable the mobile header', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Use the mobile version of the header (if checked) or relayout the current header on mobile devices', 'catamaran' ) ),
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_mobile_additional_info' => array(
					'title'      => esc_html__( 'Additional info', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Additional info to show at the top of the mobile header', 'catamaran' ) ),
					'std'        => '',
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'refresh'    => false,
					'teeny'      => false,
					'rows'       => 20,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'text_editor',
				),
				'header_mobile_hide_info'       => array(
					'title'      => esc_html__( 'Hide additional info', 'catamaran' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_mobile_hide_logo'       => array(
					'title'      => esc_html__( 'Hide logo', 'catamaran' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_mobile_hide_login'      => array(
					'title'      => esc_html__( 'Hide login/logout', 'catamaran' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_mobile_hide_search'     => array(
					'title'      => esc_html__( 'Hide search', 'catamaran' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'header_mobile_hide_cart'       => array(
					'title'      => esc_html__( 'Hide cart', 'catamaran' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => ! function_exists( 'catamaran_exists_woocommerce' ) || ! catamaran_exists_woocommerce() ? 'hidden' : 'switch',
				),


				// 'Footer'
				//---------------------------------------------
				'footer'                        => array(
					'title'    => esc_html__( 'Footer', 'catamaran' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 50,
					'icon'     => 'icon-footer',
					'type'     => 'section',
				),
				'footer_type'                   => array(
					'title'    => esc_html__( 'Footer style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'footer_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'catamaran' ),
					),
					'dependency' => array(
						'footer_type' => array( 'custom' ),
					),
					'std'        => 'footer-custom-elementor-footer-default',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets'                => array(
					'title'      => esc_html__( 'Footer widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'catamaran' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns'                => array(
					'title'      => esc_html__( 'Footer columns', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'catamaran' ),
					),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'footer_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => catamaran_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				'footer_wide'                   => array(
					'title'      => esc_html__( 'Footer fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'catamaran' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'logo_in_footer'                => array(
					'title'      => esc_html__( 'Show logo', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show logo in the footer', 'catamaran' ) ),
					'refresh'    => false,
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'hidden', // old - 'switch'
				),
				'logo_footer'                   => array(
					'title'      => esc_html__( 'Logo for footer', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo to display it in the footer', 'catamaran' ) ),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'logo_in_footer' => array( 1 ),
					),
					'std'        => '',
					'type'       => 'image',
				),
				'logo_footer_retina'            => array(
					'title'      => esc_html__( 'Logo for footer (Retina)', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'catamaran' ) ),
					'dependency' => array(
						'footer_type'         => array( 'default' ),
						'logo_in_footer'      => array( 1 ),
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'image',
				),
				'socials_in_footer'             => array(
					'title'      => esc_html__( 'Show social icons', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show social icons in the footer (under logo or footer widgets)', 'catamaran' ) ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'hidden' // old - ! catamaran_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'copyright'                     => array(
					'title'      => esc_html__( 'Copyright', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'catamaran' ) ),
					'translate'  => true,
					'std'        => esc_html__( 'Copyright &copy; {Y}. All rights reserved.', 'catamaran' ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'refresh'    => false,
					'type'       => 'textarea',
				),



				// 'Mobile version'
				//---------------------------------------------
				'mobile'                        => array(
					'title'    => esc_html__( 'Mobile', 'catamaran' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 55,
					'icon'     => 'icon-smartphone',
					'type'     => 'section',
				),

				'mobile_header_info'            => array(
					'title' => esc_html__( 'Header on the mobile device', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_mobile'            => array(
					'title'    => esc_html__( 'Header style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose the header to be used on mobile devices: the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'header_style_mobile'           => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'header_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_mobile'        => array(
					'title'    => esc_html__( 'Header position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),

				'mobile_sidebar_info'           => array(
					'title' => esc_html__( 'Sidebar on the mobile device', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_mobile'       => array(
					'title'    => esc_html__( 'Sidebar position on mobile', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select sidebar position on mobile devices', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => 'choice',
				),
				'sidebar_type_mobile'           => array(
					'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'sidebar_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_mobile'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on mobile devices', 'catamaran' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'default' )
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_mobile'         => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden on mobile devices', 'catamaran' ) ),
					'refresh' => false,
					'dependency' => array(
						'sidebar_position_mobile' => array( 'hide', 'inherit' ),
					),
					'std'     => 'inherit',
					'options' => catamaran_get_list_expand_content( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'choice',
				),

				'mobile_footer_info'           => array(
					'title' => esc_html__( 'Footer on the mobile device', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'footer_type_mobile'           => array(
					'title'    => esc_html__( 'Footer style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use on mobile devices: the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'footer_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'footer_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets_mobile'        => array(
					'title'      => esc_html__( 'Footer widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'catamaran' ) ),
					'dependency' => array(
						'footer_type_mobile' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns_mobile'        => array(
					'title'      => esc_html__( 'Footer columns', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'catamaran' ) ),
					'dependency' => array(
						'footer_type_mobile'    => array( 'default' ),
						'footer_widgets_mobile' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => catamaran_get_list_range( 0, 6 ),
					'type'       => 'select',
				),



				// 'Blog'
				//---------------------------------------------
				'blog'                          => array(
					'title'    => esc_html__( 'Blog', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Options of the the blog archive', 'catamaran' ) ),
					'priority' => 70,
					'icon'     => 'icon-page',
					'type'     => 'panel',
				),


				// Blog - Posts page
				//---------------------------------------------
				'blog_general'                  => array(
					'title' => esc_html__( 'Posts page', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Style and components of the blog archive', 'catamaran' ) ),
					'icon'  => 'icon-posts-page',
					'type'  => 'section',
				),
				'blog_general_info'             => array(
					'title'  => esc_html__( 'Posts page settings', 'catamaran' ),
					'desc'   => wp_kses_data( __( 'Customize the blog archive: post layout, header and footer style, sidebar position, etc.', 'catamaran' ) ),
					'qsetup' => esc_html__( 'General', 'catamaran' ),
					'type'   => 'info',
				),
				'blog_style'                    => array(
					'title'      => esc_html__( 'Blog style', 'catamaran' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						// New format: '@editor/property-name'
						'@editor/template' => array( 'blog.php' ),
						// Old format: CSS selector for any field on the page (also supported)
						//'compare' => 'or',
						//'#page_template' => array( 'blog.php' ),
						//'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'std'        => 'excerpt',
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'first_post_large'              => array(
					'title'      => esc_html__( 'Large first post', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
						'blog_style' => array( 'classic', 'masonry' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'blog_content'                  => array(
					'title'      => esc_html__( 'Posts content', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'catamaran' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						'blog_style' => array( 'excerpt' ),
					),
					'options'    => catamaran_get_list_blog_contents(),
					'type'       => 'radio',
				),
				'excerpt_length'                => array(
					'title'      => esc_html__( 'Excerpt length', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'catamaran' ) ),
					'dependency' => array(
						'blog_style'   => array( 'excerpt' ),
						'blog_content' => array( 'excerpt' ),
					),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'        => 30,
					'type'       => 'text',
				),
				'blog_columns'                  => array(
					'title'   => esc_html__( 'Blog columns', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'How many columns should be used in the blog archive (from 2 to 4)?', 'catamaran' ) ),
					'std'     => 2,
					'options' => catamaran_get_list_range( 2, 4 ),
					'type'    => 'hidden',      // This options is available and must be overriden only for some modes (for example, 'shop')
				),
				'post_type'                     => array(
					'title'      => esc_html__( 'Post type', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select post type to show in the blog archive', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'linked'     => 'parent_cat',
					'refresh'    => false,
					'hidden'     => true,
					'std'        => 'post',
					'options'    => array(),
					'type'       => 'select',
				),
				'parent_cat'                    => array(
					'title'      => esc_html__( 'Category to show', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select category to show in the blog archive', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'refresh'    => false,
					'hidden'     => true,
					'std'        => '0',
					'options'    => array(),
					'type'       => 'select',
				),
				'posts_per_page'                => array(
					'title'      => esc_html__( 'Posts per page', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'How many posts will be displayed on this page', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => '',
					'type'       => 'text',
				),
				'blog_pagination'               => array(
					'title'      => esc_html__( 'Pagination style', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'        => 'pages',
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'options'    => catamaran_get_list_blog_paginations(),
					'type'       => 'choice',
				),
				'blog_animation'                => array(
					'title'      => esc_html__( 'Post animation', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				'disable_animation_on_mobile'   => array(
					'title'      => esc_html__( 'Disable animation on mobile', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Disable any posts animation on mobile devices', 'catamaran' ) ),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'show_filters'                  => array(
					'title'      => esc_html__( 'Show filters', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show categories as tabs to filter posts', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'video_in_popup'                => array(
					'title'      => esc_html__( 'Open video in the popup on a blog archive', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Open the video from posts in the popup (if plugin "ThemeREX Addons" is installed) or play the video instead the cover image', 'catamaran' ) ),
					'std'        => 0,
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'type'       => 'switch',
				),
				'open_full_post_in_blog'        => array(
					'title'      => esc_html__( 'Open full post in blog', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the blog feed. Attention! Applies only to 1 column layouts!', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'open_full_post_hide_author'    => array(
					'title'      => esc_html__( 'Hide author bio', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when open the full version of the post directly in the blog feed.", 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'open_full_post_hide_related'   => array(
					'title'      => esc_html__( 'Hide related posts', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when open the full version of the post directly in the blog feed.", 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),

				'blog_header_info'              => array(
					'title' => esc_html__( 'Header', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_blog'              => array(
					'title'    => esc_html__( 'Header style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_blog'             => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'header_type_blog' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_blog'          => array(
					'title'    => esc_html__( 'Header position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_fullheight_blog'        => array(
					'title'    => esc_html__( 'Header fullheight', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_wide_blog'              => array(
					'title'      => esc_html__( 'Header fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'catamaran' ) ),
					'dependency' => array(
						'header_type_blog' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),

				'blog_sidebar_info'             => array(
					'title' => esc_html__( 'Sidebar', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_blog'         => array(
					'title'   => esc_html__( 'Sidebar position', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'catamaran' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'type'    => 'choice',
				),
				'sidebar_position_ss_blog'  => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'catamaran' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type_blog'           => array(
					'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style_blog'            => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_blog'          => array(
					'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'catamaran' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'type'       => 'select',
				),
				'expand_content_blog'           => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'catamaran' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => catamaran_get_list_expand_content( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'choice',
				),

				'blog_widgets_info'             => array(
					'title' => esc_html__( 'Additional widgets', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				'widgets_above_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				'widgets_above_content_blog'    => array(
					'title'   => esc_html__( 'Widgets above the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				'widgets_below_content_blog'    => array(
					'title'   => esc_html__( 'Widgets below the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				'widgets_below_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),

				'blog_advanced_info'            => array(
					'title' => esc_html__( 'Advanced settings', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'no_image'                      => array(
					'title' => esc_html__( 'Image placeholder', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Select or upload a placeholder image for posts without a featured image. Placeholder is used exclusively on the blog stream page (and not on single post pages), and only in those styles, where omitting a featured image would be inappropriate.", 'catamaran' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'sticky_style'                  => array(
					'title'   => esc_html__( 'Sticky posts style', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select style of the sticky posts output', 'catamaran' ) ),
					'std'     => 'inherit',
					'options' => array(
						'inherit' => esc_html__( 'Decorated posts', 'catamaran' ),
						'columns' => esc_html__( 'Mini-cards', 'catamaran' ),
					),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'hidden', // old -> select
				),
				'meta_parts'                    => array(
					'title'      => esc_html__( 'Post meta', 'catamaran' ),
					'desc'       => wp_kses_data( __( "If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'catamaran' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'@editor/template' => array( 'blog.php' ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=0|date=1|modified=0|views=0|likes=0|comments=1|author=0|share=0|edit=0',
					'options'    => catamaran_get_list_meta_parts(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'checklist',
				),
				'time_diff_before'              => array(
					'title' => esc_html__( 'Easy readable date format', 'catamaran' ),
					'desc'  => wp_kses_data( __( "For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'catamaran' ) ),
					'std'   => 5,
					'type'  => 'text',
				),
				'use_blog_archive_pages'        => array(
					'title'      => esc_html__( 'Use "Blog Archive" page settings on the post list', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Apply options and content of pages created with the template "Blog Archive" for some type of posts and / or taxonomy when viewing feeds of posts of this type and taxonomy.', 'catamaran' ) ),
					'std'        => 0,
					'type'       => 'switch',
				),


				// Blog - Single posts
				//---------------------------------------------
				'blog_single'                   => array(
					'title' => esc_html__( 'Single posts', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Settings of the single post', 'catamaran' ) ),
					'icon'  => 'icon-single-post',
					'type'  => 'section',
				),

				'blog_single_info'       => array(
					'title' => esc_html__( 'Single posts', 'catamaran' ),
					'desc'   => wp_kses_data( __( 'Customize the single post: content  layout, header and footer styles, sidebar position, meta elements, etc.', 'catamaran' ) ),
					'type'  => 'info',
				),
				'blog_single_header_info'       => array(
					'title' => esc_html__( 'Header', 'catamaran' ),
					'desc'   => '',
					'type'  => 'info',
				),
				'header_type_single'            => array(
					'title'    => esc_html__( 'Header style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_single'           => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'header_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_single'        => array(
					'title'    => esc_html__( 'Header position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_fullheight_single'      => array(
					'title'    => esc_html__( 'Header fullheight', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				'header_wide_single'            => array(
					'title'      => esc_html__( 'Header fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'catamaran' ) ),
					'dependency' => array(
						'header_type_single' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),						
				'blog_single_sidebar_info'      => array(
					'title' => esc_html__( 'Sidebar', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_single'       => array(
					'title'   => esc_html__( 'Sidebar position', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar on the single posts', 'catamaran' ) ),
					'std'     => 'hide',
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'options' => array(),
					'type'    => 'choice',
				),
				'sidebar_position_ss_single'    => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the single posts on the small screen - above or below the content', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'below',
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type_single'           => array(
					'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'sidebar_style_single'            => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_single'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on the single posts', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_single'         => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width on the single posts if the sidebar is hidden. Attention! "Narrow" width is only available for posts. For all other post types (Team, Services, etc.), it is equivalent to "Normal"', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => catamaran_get_list_expand_content( true, true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'choice',
				),
				'blog_single_title_info'        => array(
					'title' => esc_html__( 'Featured image and title', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'single_style'                  => array(
					'title'      => esc_html__( 'Single style', 'catamaran' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'        => 'style-1',
					'qsetup'     => esc_html__( 'General', 'catamaran' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'single_parallax'               => array(
					'title'      => esc_html__( 'Parallax speed', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Speed for shifting the image while scrolling the page. If 0, the effect is not applied.', 'catamaran' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'        => 0,
					'min'        => -50,
					'max'        => 50,
					'step'       => 1,
					'refresh'    => false,
					'show_value' => true,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'slider',
				),
				'post_subtitle'                 => array(
					'title' => esc_html__( 'Post subtitle', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Specify post subtitle to display it under the post title.", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'   => '',
					'hidden' => true,
					'type'  => 'text',
				),
				'show_post_meta'                => array(
					'title' => esc_html__( 'Show post meta', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Display block with post's meta: date, categories, counters, etc.", 'catamaran' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'meta_parts_single'             => array(
					'title'      => esc_html__( 'Post meta', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Meta parts for single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'catamaran' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'catamaran' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|author=1|date=1|modified=0|views=0|likes=1|share=1|comments=1|edit=0',
					'options'    => catamaran_get_list_meta_parts(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'checklist',
				),
				'share_position'                 => array(
					'title'      => esc_html__( 'Share links position', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select one or more positions to show Share links on single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'catamaran' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
						'meta_parts_single[share]' => array( 1 ),
					),
					'dir'        => 'vertical',
					'std'        => 'top=0|left=0|bottom=1',
					'options'    => catamaran_get_list_share_links_positions(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'checklist',
				),
				'share_fixed'                   => array(
					'title' => esc_html__( 'Share links fixed', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Fix share links when a document scrolled down", 'catamaran' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
						'meta_parts_single[share]' => array( 1 ),
						'share_position[left]' => array( 1 ),
					),
					'std'   => 1,
					'type'  => 'switch',
				),
				'show_author_info'              => array(
					'title' => esc_html__( 'Show author info', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Display block with information about post's author", 'catamaran' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'show_comments_button'          => array(
					'title' => esc_html__( 'Show comments button', 'catamaran' ),
					'desc'  => wp_kses_data( __( "Display button to show/hide comments block", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'   => 0,
					'type'  => 'switch',
				),
				'show_comments'                 => array(
					'title'   => esc_html__( 'Comments block', 'catamaran' ),
					'desc'    => wp_kses_data( __( "Select initial state of the comments block", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'options' => catamaran_get_list_visiblehidden(),
					'dependency' => array(
						'show_comments_button' => array( 1 ),
					),
					'std'     => 'hidden',
					'type'    => 'radio',
				),

				'blog_single_related_info'      => array(
					'title' => esc_html__( 'Related posts', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'show_related_posts'            => array(
					'title'    => esc_html__( 'Show related posts', 'catamaran' ),
					'desc'     => wp_kses_data( __( "Show 'Related posts' section on single post pages", 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'std'      => 1,
					'type'     => 'switch',
				),
				'related_style'                 => array(
					'title'      => esc_html__( 'Related posts style', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select the style of the related posts output', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'classic',
					'options'    => array(
						// old -> 'modern'  => esc_html__( 'Modern', 'catamaran' ),
						'classic' => esc_html__( 'Classic', 'catamaran' ),
						// old -> 'wide'    => esc_html__( 'Wide', 'catamaran' ),
						'list'    => esc_html__( 'List', 'catamaran' ),
						'short'   => esc_html__( 'Short', 'catamaran' ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'radio',
				),
				'related_position'              => array(
					'title'      => esc_html__( 'Related posts position', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select position to display the related posts', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'below_content',
					'options'    => array (
						'inside'        => esc_html__( 'Inside the content (fullwidth)', 'catamaran' ),
						'inside_left'   => esc_html__( 'At left side of the content', 'catamaran' ),
						'inside_right'  => esc_html__( 'At right side of the content', 'catamaran' ),
						'below_content' => esc_html__( 'After the content', 'catamaran' ),
						'below_page'    => esc_html__( 'After the content & sidebar', 'catamaran' ),
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				'related_position_inside'       => array(
					'title'      => esc_html__( 'Before # paragraph', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Before what paragraph should related posts appear? If 0 - randomly.', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'inside_left', 'inside_right' ),
					),
					'std'        => 2,
					'options'    => catamaran_get_list_range( 0, 9 ),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				'related_posts'                 => array(
					'title'      => esc_html__( 'Related posts', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'How many related posts should be displayed in the single post?', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post,cpt_portfolio',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 9,
					'show_value' => true,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'slider',
				),
				'related_columns'               => array(
					'title'      => esc_html__( 'Related columns', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'How many columns should be used to output related posts on the single post page?', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'below_content', 'below_page' ),
					),
					'std'        => 2,
					'options'    => catamaran_get_list_range( 1, 6 ),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'radio',
				),
				'related_slider'                => array(
					'title'      => esc_html__( 'Use slider layout', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Use slider layout in case related posts count is more than columns count', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden', // old - switch
				),
				'related_slider_controls'       => array(
					'title'      => esc_html__( 'Slider controls', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show arrows in the slider', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'none',
					'options'    => array(
						'none'    => esc_html__('None', 'catamaran'),
						'side'    => esc_html__('Side', 'catamaran'),
						'outside' => esc_html__('Outside', 'catamaran'),
						'top'     => esc_html__('Top', 'catamaran'),
						'bottom'  => esc_html__('Bottom', 'catamaran')
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden', // old -> select
				),
				'related_slider_pagination'       => array(
					'title'      => esc_html__( 'Slider pagination', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show bullets after the slider', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'bottom',
					'options'    => array(
						'none'    => esc_html__('None', 'catamaran'),
						'bottom'  => esc_html__('Bottom', 'catamaran')
					),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden', // old -> radio
				),
				'related_slider_space'          => array(
					'title'      => esc_html__( 'Space between slides', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Space between slides in the related posts slider (in pixels)', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'catamaran' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 30,
					'min'        => 0,
					'max'        => 100,
					'show_value' => true,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'hidden', // old -> slider
				),
				'posts_navigation_info'      => array(
					'title' => esc_html__( 'Post navigation', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'posts_navigation'           => array(
					'title'   => esc_html__( 'Show post navigation', 'catamaran' ),
					'desc'    => wp_kses_data( __( "Display post navigation on single post pages or load the next post automatically after the content of the current article.", 'catamaran' ) ),
					'std'     => 'links',
					'options' => array(
						'none'   => esc_html__('None', 'catamaran'),
						'links'  => esc_html__('Prev/Next links', 'catamaran'),
						'scroll' => esc_html__('Autoload next post', 'catamaran')
					),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'radio',
				),
				'posts_navigation_fixed'     => array(
					'title'      => esc_html__( 'Fixed post navigation', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Fix the position of post navigation buttons on desktop. Display them on either side of post content.", 'catamaran' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'links' ),
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
                'posts_navigation_scroll_same_cat'     => array(
                    'title'      => esc_html__( 'Next post from same category', 'catamaran' ),
                    'desc'       => wp_kses_data( __( "Load next post from the same category or from any category.", 'catamaran' ) ),
                    'dependency' => array(
                        'posts_navigation' => array( 'scroll' ),
                    ),
                    'std'        => 1,
                    'pro_only'   => CATAMARAN_THEME_FREE,
                    'type'       => 'switch',
                ),
				'posts_navigation_scroll_which_block'  => array(
					'title'   => esc_html__( 'Which block to load?', 'catamaran' ),
					'desc'    => wp_kses_data( __( "Load only the content of the next article or the article and sidebar together.", 'catamaran' ) )
								. '<br>'
								. wp_kses_data( __( "Attention! If you override sidebar position or content width on single posts (e.g. the sidebar is displayed on some posts and hidden on others), please don’t use the 'Full post' option to prevent improper content positioning.", 'catamaran' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'     => 'article',
					'options' => array(
						'article' => array(
										'title' => esc_html__( 'Only content', 'catamaran' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/article.png',
									),
						'wrapper' => array(
										'title' => esc_html__( 'Full post', 'catamaran' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/wrapper.png',
									),
					),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'choice',
				),
				'posts_navigation_scroll_hide_author'  => array(
					'title'      => esc_html__( 'Hide author bio', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when infinite scroll is used.", 'catamaran' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'posts_navigation_scroll_hide_related'  => array(
					'title'      => esc_html__( 'Hide related posts', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when infinite scroll is used.", 'catamaran' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'posts_navigation_scroll_hide_comments' => array(
					'title'      => esc_html__( 'Hide comments', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Hide comments after post content when infinite scroll is used.", 'catamaran' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 1,
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'switch',
				),
				'blog_single_footer_info'                        => array(
					'title'    => esc_html__( 'Footer', 'catamaran' ),					
					'desc'   => '',
					'type'  => 'info',
				),
				'footer_type_single'                   => array(
					'title'    => esc_html__( 'Footer style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! $trx_addons_present ? 'hidden' : 'radio',
				),
				'footer_style_single'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						'footer_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'blog_end'                      => array(
					'type' => 'panel_end',
				),



				// 'Colors'
				//---------------------------------------------
				'panel_colors'                  => array(
					'title'    => esc_html__( 'Colors', 'catamaran' ),
					'desc'     => '',
					'priority' => 300,
					'icon'     => 'icon-customizer',
                    'demo'     => true,
					'type'     => 'section',
				),

				'color_schemes_info'            => array(
					'title'  => esc_html__( 'Color scheme assignment', 'catamaran' ),
					'desc'   => wp_kses_data( __( 'Color schemes for various parts of the site. "Inherit" means that this block uses the main color scheme from the first parameter - Site Color Scheme.', 'catamaran' ) ),
					'hidden' => $hide_schemes,
                    'demo'   => true,
					'type'   => 'info',
				),
				'color_scheme'                  => array(
					'title'    => esc_html__( 'Site Color Scheme', 'catamaran' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,post,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
                    'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'header_scheme'                 => array(
					'title'    => esc_html__( 'Header Color Scheme', 'catamaran' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,post,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'catamaran' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
                    'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'menu_scheme'                   => array(
					'title'    => esc_html__( 'Sidemenu Color Scheme', 'catamaran' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'catamaran' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'pro_only' => CATAMARAN_THEME_FREE,
                    'demo'     => true,
					'type'     => 'hidden',
				),
				'sidebar_scheme'                => array(
					'title'    => esc_html__( 'Sidebar Color Scheme', 'catamaran' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,post,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
                    'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'footer_scheme'                 => array(
					'title'    => esc_html__( 'Footer Color Scheme', 'catamaran' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,post,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'catamaran' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
                    'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),

				'color_scheme_editor_info'      => array(
					'title' => esc_html__( 'Color scheme editor', 'catamaran' ),
					'desc'  => wp_kses_data( __( 'Select a color scheme to modify. Attention! Only sections of the site with the selected color scheme will be affected by the changes', 'catamaran' ) ),
                    'demo'  => true,
                    'type'  => 'info',
				),
				'scheme_storage'                => array(
					'title'       => esc_html__( 'Color scheme editor', 'catamaran' ),
					'desc'        => '',
					'std'         => '$catamaran_get_scheme_storage',
					'refresh'     => false,
					'colorpicker' => 'spectrum', //'tiny',
                    'demo'  => true,
					'type'        => 'scheme_editor',
				),

				// Internal options.
				// Attention! Don't change any options in the section below!
				// Huge priority is used to call render this elements after all options!
				'reset_options'                 => array(
					'title'    => '',
					'desc'     => '',
					'std'      => '0',
					'priority' => 10000,
					'type'     => 'hidden',
				),

				'last_option'                   => array(     // Need to manually call action to include Tiny MCE scripts
					'title' => '',
					'desc'  => '',
					'std'   => 1,
					'demo'  => true,
					'type'  => 'hidden',
				),

			)
		);


		// Add parameters for "Category", "Tag", "Author", "Search" to Theme Options
		catamaran_storage_set_array_before( 'options', 'blog_single', catamaran_options_get_list_blog_options( 'category', esc_html__( 'Category', 'catamaran' ), 'icon-category' ) );
		catamaran_storage_set_array_before( 'options', 'blog_single', catamaran_options_get_list_blog_options( 'tag', esc_html__( 'Tag', 'catamaran' ), 'icon-tag-1' ) );
		catamaran_storage_set_array_before( 'options', 'blog_single', catamaran_options_get_list_blog_options( 'author', esc_html__( 'Author', 'catamaran' ), 'icon-resume' ) );
		catamaran_storage_set_array_before( 'options', 'blog_single', catamaran_options_get_list_blog_options( 'search', esc_html__( 'Search', 'catamaran' ), 'icon-search-1' ) );


		// Prepare panel 'Fonts'
		// -------------------------------------------------------------
		$fonts = array(

			// 'Fonts'
			//---------------------------------------------
			'fonts'             => array(
				'title'    => esc_html__( 'Typography', 'catamaran' ),
				'desc'     => '',
				'priority' => 200,
				'icon'     => 'icon-text',
                'demo'     => true,
				'type'     => 'panel',
			),

			// Fonts - Load_fonts
			'load_fonts'        => array(
				'title' => esc_html__( 'Load fonts', 'catamaran' ),
				'desc'  => wp_kses_data( __( 'Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'catamaran' ) ),
                'demo'   => true,
				'type'  => 'section',
			),
			'load_fonts_info'   => array(
				'title' => esc_html__( 'Load fonts', 'catamaran' ),
				'desc'  => is_customize_preview() ? wp_kses_data( __( 'Press "Reload preview area" button at the top of this panel after the all font parameters are changed.', 'catamaran' ) ) : '',
                'demo'   => true,
				'type'  => 'info',
			),
			'load_fonts_subset' => array(
				'title'   => esc_html__( 'Google fonts subsets', 'catamaran' ),
				'desc'    => wp_kses_data( __( 'Specify a comma separated list of subsets to be loaded from Google fonts.', 'catamaran' ) )
						. wp_kses_data( __( 'Permitted subsets include: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'catamaran' ) ),
				'class'   => 'catamaran_column-1_4 catamaran_new_row',
				'refresh' => false,
                'demo'   => true,
				'std'     => '$catamaran_get_load_fonts_subset',
				'type'    => 'text',
			),
		);

		for ( $i = 1; $i <= catamaran_get_theme_setting( 'max_load_fonts' ); $i++ ) {
			if ( catamaran_get_value_gp( 'page' ) != 'theme_options' ) {
				$fonts[ "load_fonts-{$i}-info" ] = array(
					// Translators: Add font's number - 'Font 1', 'Font 2', etc
					'title' => esc_html( sprintf( __( 'Font %s', 'catamaran' ), $i ) ),
					'desc'  => '',
					'demo'  => true,
					'type'  => 'info',
				);
			}
			$fonts[ "load_fonts-{$i}-name" ]   = array(
				'title'   => esc_html__( 'Font name', 'catamaran' ),
				'desc'    => '',
				'class'   => 'catamaran_column-1_4 catamaran_new_row',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$catamaran_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-family" ] = array(
				'title'   => esc_html__( 'Font family', 'catamaran' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Select a font family to be used if the preferred font is not available', 'catamaran' ) )
							: '',
				'class'   => 'catamaran_column-1_4',
				'refresh' => false,
				'std'     => '$catamaran_get_load_fonts_option',
                'demo'   => true,
				'options' => array(
					'inherit'    => esc_html__( 'Inherit', 'catamaran' ),
					'serif'      => esc_html__( 'serif', 'catamaran' ),
					'sans-serif' => esc_html__( 'sans-serif', 'catamaran' ),
					'monospace'  => esc_html__( 'monospace', 'catamaran' ),
					'cursive'    => esc_html__( 'cursive', 'catamaran' ),
					'fantasy'    => esc_html__( 'fantasy', 'catamaran' ),
				),
				'type'    => 'select',
			);
			$fonts[ "load_fonts-{$i}-link" ] = array(
				'title'   => esc_html__( 'Font URL', 'catamaran' ),
				'desc'    => 1 == $i
					? wp_kses_data( __( 'Font URL used only for Adobe fonts. This is URL of the stylesheet for the project with a fonts collection from the site adobe.com', 'catamaran' ) )
					: '',
				'class'   => 'catamaran_column-1_4',
				'refresh' => false,
                'demo'   => true,
				'std'     => '$catamaran_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-styles" ] = array(
				'title'   => esc_html__( 'Font styles', 'catamaran' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font styles used only for Google fonts. This is a comma separated list of the font weight and style options. For example: 400,400italic,700', 'catamaran' ) )
								. '<br>'
								. wp_kses_data( __( 'Attention! Each weight and style option increases download size! Specify only those weight and style options that you plan on using.', 'catamaran' ) )
							: '',
				'class'   => 'catamaran_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$catamaran_get_load_fonts_option',
				'type'    => 'text',
			);
		}
		$fonts['load_fonts_end'] = array(
			'demo' => true,
			'type' => 'section_end',
		);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = catamaran_get_theme_fonts();
		foreach ( $theme_fonts as $tag => $v ) {
			$fonts[ "{$tag}_font_section" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'catamaran' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								// Translators: Add tag's name to make description
								: wp_kses_data( sprintf( __( 'Font settings for the "%s" tag.', 'catamaran' ), $tag ) ),
                'demo'   => true,
				'type'  => 'section',
			);
			$fonts[ "{$tag}_font_info" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'catamaran' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								: '',
				'demo'  => true,
				'type'  => 'info',
			);
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				// Skip property 'text-decoration' for the main text
				if ( 'text-decoration' == $css_prop && 'p' == $tag ) {
					continue;
				}

				$options    = '';
				$type       = 'text';
				$load_order = 1;
				$title      = ucfirst( str_replace( '-', ' ', $css_prop ) );
				if ( 'font-family' == $css_prop ) {
					$type       = 'select';
					$options    = array();
					$load_order = 2;        // Load this option's value after all options are loaded (use option 'load_fonts' to build fonts list)
				} elseif ( 'font-weight' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'catamaran' ),
						'100'     => esc_html__( '100 (Thin)', 'catamaran' ),
						'200'     => esc_html__( '200 (Extra-Light)', 'catamaran' ),
						'300'     => esc_html__( '300 (Light)', 'catamaran' ),
						'400'     => esc_html__( '400 (Regular)', 'catamaran' ),
						'500'     => esc_html__( '500 (Medium)', 'catamaran' ),
						'600'     => esc_html__( '600 (Semi-bold)', 'catamaran' ),
						'700'     => esc_html__( '700 (Bold)', 'catamaran' ),
						'800'     => esc_html__( '800 (Extra-bold)', 'catamaran' ),
						'900'     => esc_html__( '900 (Black)', 'catamaran' ),
					);
				} elseif ( 'font-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'catamaran' ),
						'normal'  => esc_html__( 'Normal', 'catamaran' ),
						'italic'  => esc_html__( 'Italic', 'catamaran' ),
					);
				} elseif ( 'text-decoration' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'      => esc_html__( 'Inherit', 'catamaran' ),
						'none'         => esc_html__( 'None', 'catamaran' ),
						'underline'    => esc_html__( 'Underline', 'catamaran' ),
						'overline'     => esc_html__( 'Overline', 'catamaran' ),
						'line-through' => esc_html__( 'Line-through', 'catamaran' ),
					);
				} elseif ( 'text-transform' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'    => esc_html__( 'Inherit', 'catamaran' ),
						'none'       => esc_html__( 'None', 'catamaran' ),
						'uppercase'  => esc_html__( 'Uppercase', 'catamaran' ),
						'lowercase'  => esc_html__( 'Lowercase', 'catamaran' ),
						'capitalize' => esc_html__( 'Capitalize', 'catamaran' ),
					);
				}
				$fonts[ "{$tag}_{$css_prop}" ] = array(
					'title'      => $title,
					'desc'       => '',
					'refresh'    => false,
					'demo'       => true,
					'load_order' => $load_order,
					'std'        => '$catamaran_get_theme_fonts_option',
					'options'    => $options,
					'type'       => $type,
				);
			}

			$fonts[ "{$tag}_section_end" ] = array(
				'demo' => true,
				'type' => 'section_end',
			);
		}

		$fonts['fonts_end'] = array(
			'demo' => true,
			'type' => 'panel_end',
		);

		// Add fonts parameters to Theme Options
		catamaran_storage_set_array_before( 'options', 'panel_colors', $fonts );

		// Add Header Video if WP version < 4.7
		// -----------------------------------------------------
		if ( ! function_exists( 'get_header_video_url' ) ) {
			catamaran_storage_set_array_after(
				'options', 'header_image_override', 'header_video', array(
					'title'    => esc_html__( 'Header video', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select video to use it as background for the header', 'catamaran' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'catamaran' ),
					),
					'std'      => '',
					'type'     => 'video',
				)
			);
		}

		// Add option 'logo' if WP version < 4.5
		// or 'custom_logo' if current page is not 'Customize'
		// ------------------------------------------------------
		if ( ! function_exists( 'the_custom_logo' ) || ! catamaran_check_url( 'customize.php' ) ) {
			catamaran_storage_set_array_before(
				'options', 'logo_retina', function_exists( 'the_custom_logo' ) ? 'custom_logo' : 'logo', array(
					'title'    => esc_html__( 'Logo', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select or upload the site logo', 'catamaran' ) ),
					'priority' => 60,
					'std'      => '',
					'qsetup'   => esc_html__( 'General', 'catamaran' ),
					'type'     => 'image',
				)
			);
		}

	}
}


// Common parameters for some blog modes: categories, tags, archives, author posts, search, etc.
//------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'catamaran_options_get_list_blog_options' ) ) {
	function catamaran_options_get_list_blog_options( $mode, $title = '', $icon = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $mode );
		}
		return apply_filters( 'catamaran_filter_get_list_blog_options', array(
				"blog_general_{$mode}"           => array(
					'title' => $title,
					// Translators: Add mode name to the description
					'desc'  => wp_kses_data( sprintf( __( "Style and components of the %s posts page", 'catamaran' ), $title ) ),
					'icon'  => $icon,
					'type'  => 'section',
				),
				"blog_general_info_{$mode}"      => array(
					// Translators: Add mode name to the title
					'title'  => wp_kses_data( sprintf( __( "%s posts page", 'catamaran' ), $title ) ),
					// Translators: Add mode name to the description
					'desc'   => wp_kses_data( sprintf( __( 'Customize %s page: post layout, header and footer styles, sidebar position and widgets, etc.', 'catamaran' ), $title ) ),
					'type'   => 'info',
				),
				"blog_style_{$mode}"             => array(
					'title'      => esc_html__( 'Blog style', 'catamaran' ),
					'desc'       => '',
					'std'        => 'excerpt',
					'options'    => array(),
					'type'       => 'choice',
				),
				"first_post_large_{$mode}"       => array(
					'title'      => esc_html__( 'Large first post', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'catamaran' ) ),
					'std'        => 0,
					'options'    => catamaran_get_list_yesno( true ),
					'dependency' => array(
						'blog_style_{$mode}' => array( 'classic', 'masonry' ),
					),
					'type'       => 'radio',
				),
				"blog_content_{$mode}"           => array(
					'title'      => esc_html__( 'Posts content', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'catamaran' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						"blog_style_{$mode}" => array( 'excerpt' ),
					),
					'options'    => catamaran_get_list_blog_contents( true ),
					'type'       => 'radio',
				),
				"excerpt_length_{$mode}"         => array(
					'title'      => esc_html__( 'Excerpt length', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'catamaran' ) ),
					'dependency' => array(
						"blog_style_{$mode}"   => array( 'excerpt' ),
						"blog_content_{$mode}" => array( 'excerpt' ),
					),
					'std'        => 55,
					'type'       => 'text',
				),
				"meta_parts_{$mode}"             => array(
					'title'      => esc_html__( 'Post meta', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Set up post meta parts to show in the blog archive. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'catamaran' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'catamaran' ) ),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=1|likes=1|comments=1|author=0|share=0|edit=0',
					'options'    => catamaran_get_list_meta_parts(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'checklist',
				),
				"blog_pagination_{$mode}"        => array(
					'title'      => esc_html__( 'Pagination style', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'catamaran' ) ),
					'std'        => 'pages',
					'options'    => catamaran_get_list_blog_paginations( true ),
					'type'       => 'choice',
				),
				"blog_animation_{$mode}"         => array(
					'title'      => esc_html__( 'Post animation', 'catamaran' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'catamaran' ) ),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				"open_full_post_in_blog_{$mode}" => array(
					'title'      => esc_html__( 'Open full post in blog', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the posts feed. Attention! Applies only to 1 column layouts!', 'catamaran' ) ),
					'std'        => 0,
					'options'    => catamaran_get_list_checkbox_values( true ),
					'type'       => 'radio',
				),

				"blog_header_info_{$mode}"       => array(
					'title' => esc_html__( 'Header', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"header_type_{$mode}"            => array(
					'title'    => esc_html__( 'Header style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_header_footer_types( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				"header_style_{$mode}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				"header_position_{$mode}"        => array(
					'title'    => esc_html__( 'Header position', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				"header_fullheight_{$mode}"      => array(
					'title'    => esc_html__( 'Header fullheight', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),
				"header_wide_{$mode}"            => array(
					'title'      => esc_html__( 'Header fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'catamaran' ) ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_checkbox_values( true ),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => 'radio',
				),

				"blog_sidebar_info_{$mode}"      => array(
					'title' => esc_html__( 'Sidebar', 'catamaran' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"sidebar_position_{$mode}"       => array(
					'title'   => esc_html__( 'Sidebar position', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'catamaran' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_type_{$mode}"           => array(
					'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => catamaran_get_list_header_footer_types(),
					'pro_only' => CATAMARAN_THEME_FREE,
					'type'     => ! catamaran_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_{$mode}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_{$mode}"        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'catamaran' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_{$mode}"         => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'catamaran' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => catamaran_get_list_expand_content( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'choice',
				),

				"blog_widgets_info_{$mode}"      => array(
					'title' => esc_html__( 'Additional widgets', 'catamaran' ),
					'desc'  => '',
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				"widgets_above_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_above_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_below_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_below_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
			), $mode, $title
		);
	}
}


// Common parameters for CPT
//------------------------------------------------------------------------------------------------------------

// Returns a list of options that can be overridden for CPT
if ( ! function_exists( 'catamaran_options_get_list_cpt_options' ) ) {
	function catamaran_options_get_list_cpt_options( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'catamaran_filter_get_list_cpt_options',
								array_merge(
                                    catamaran_options_get_list_cpt_options_body( $cpt, $title ),
                                    ('portfolio' == $cpt ? catamaran_options_get_list_cpt_options_color( $cpt, $title ) : array()),
                                    ('portfolio' == $cpt ? catamaran_options_get_list_cpt_navigation( $cpt, $title ) : array()),
                                    ('events' == $cpt ? catamaran_options_get_list_cpt_options_color( $cpt, $title ) : array()),
									catamaran_options_get_list_cpt_options_header( $cpt, $title, 'list' ),
									catamaran_options_get_list_cpt_options_header( $cpt, $title, 'single' ),
									catamaran_options_get_list_cpt_options_sidebar( $cpt, $title, 'list' ),
									catamaran_options_get_list_cpt_options_sidebar( $cpt, $title, 'single' ),
									catamaran_options_get_list_cpt_options_footer( $cpt, $title ),
									catamaran_options_get_list_cpt_options_widgets( $cpt, $title )
								),
								$cpt,
								$title
							);
	}
}


// Returns a text description suffix for CPT
if ( ! function_exists( 'catamaran_options_get_cpt_description_suffix' ) ) {
	function catamaran_options_get_cpt_description_suffix( $title, $mode ) {
		return $mode == 'both'
					// Translators: Add CPT name to the description
					? sprintf( __( 'the %s list and single posts', 'catamaran' ), $title )
					: ( $mode == 'list'
						// Translators: Add CPT name to the description
						? sprintf( __( 'the %s list', 'catamaran' ), $title )
						// Translators: Add CPT name to the description
						: sprintf( __( 'Single %s posts', 'catamaran' ), $title )
						);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Content' - Color
if ( ! function_exists( 'catamaran_options_get_list_cpt_navigation' ) ) {
    function catamaran_options_get_list_cpt_navigation( $cpt, $title = '' ) {
        if ( empty( $title ) ) {
            $title = ucfirst( $cpt );
        }
        return apply_filters( 'catamaran_filter_get_list_cpt_navigation', array(
            "content_info_{$cpt}"           => array(
                'title' => esc_html__( 'Body style', 'catamaran' ),
                // Translators: Add CPT name to the description
                'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s list and single posts', 'catamaran' ), $title ) ),
                'type'  => 'info',
            ),
            "cpt_navigation_{$cpt}"           => array(
                'title'   => esc_html__( 'Show post navigation', 'catamaran' ),
                'desc'    => wp_kses_data( __( "Display post navigation on single post pages.", 'catamaran' ) ),
                'override'   => array(
                    'mode'    => 'cpt_portfolio',
                    'section' => esc_html__( 'Content', 'catamaran' ),
                ),
                'std'     => 0,
                'pro_only'=> CATAMARAN_THEME_FREE,
                'type'    => 'switch',
            ),
        ), $cpt, $title
        );
    }
}


// Returns a list of options that can be overridden for CPT. Section 'Content' - Color
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_color' ) ) {
    function catamaran_options_get_list_cpt_options_color( $cpt, $title = '' ) {
        if ( empty( $title ) ) {
            $title = ucfirst( $cpt );
        }
        return apply_filters( 'catamaran_filter_get_list_cpt_options_color', array(
            "content_info_{$cpt}"           => array(
                'title' => esc_html__( 'Body style', 'catamaran' ),
                // Translators: Add CPT name to the description
                'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s list and single posts', 'catamaran' ), $title ) ),
                'type'  => 'info',
            ),
            "color_scheme_{$cpt}"                => array(
                'title'    => wp_kses_data( sprintf( __( 'Color Scheme', 'catamaran' ), $title)),
                'desc'     => '',
                'std'      => 'inherit',
                'options'  => array(),
                'refresh'  => false,
                'type'     => 'radio',
            ),
            "sidebar_scheme_{$cpt}"                => array(
                'title'    => wp_kses_data( sprintf( __( 'Sidebar Color Scheme', 'catamaran' ), $title)),
                'desc'     => '',
                'std'      => 'inherit',
                'options'  => array(),
                'refresh'  => false,
                'type'     => 'radio',
            ),
            "color_scheme_single_{$cpt}"                => array(
                'title'    => wp_kses_data( sprintf( __( 'Single %s Color Scheme', 'catamaran' ), $title)),
                'desc'     => '',
                'std'      => 'inherit',
                'options'  => array(),
                'refresh'  => false,
                'type'     => 'radio',
            ),
        ), $cpt, $title
        );
    }
}


// Returns a list of options that can be overridden for CPT. Section 'Content'
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_body' ) ) {
	function catamaran_options_get_list_cpt_options_body( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = catamaran_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "catamaran_filter_get_list_cpt_options_body{$suffix}", array(
				"content_info{$suffix}_{$cpt}"           => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Body style on %s', 'catamaran' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s', 'catamaran' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"body_style{$suffix}_{$cpt}"             => array(
					'title'    => esc_html__( 'Body style', 'catamaran' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'catamaran' ) ),
					'std'      => 'inherit',
					'options'  => catamaran_get_list_body_styles( true ),
					'type'     => 'choice',
				),
				"boxed_bg_image{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Boxed bg image', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select or upload image, used as background in the boxed body', 'catamaran' ) ),
					'dependency' => array(
						"body_style{$suffix}_{$cpt}" => array( 'boxed' ),
					),
					'std'        => 'inherit',
					'type'       => 'image',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Header'
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_header' ) ) {
	function catamaran_options_get_list_cpt_options_header( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = catamaran_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "catamaran_filter_get_list_cpt_options_header{$suffix}", array(
				"header_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Header on %s', 'catamaran' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up header parameters to display %s', 'catamaran' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"header_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Header style', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'     => 'inherit',
					'options' => catamaran_get_list_header_footer_types( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'radio',
				),
				"header_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select custom layout to display the site header on the %s pages', 'catamaran' ), $title ) ),
					'dependency' => array(
						"header_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				"header_position{$suffix}_{$cpt}"        => array(
					'title'   => esc_html__( 'Header position', 'catamaran' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select position to display the site header on the %s pages', 'catamaran' ), $title ) ),
					'std'     => 'inherit',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'radio',
				),
				"header_image_override{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Header image override', 'catamaran' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( "Allow overriding the header image with a featured image of %s.", 'catamaran' ), $title ) ),
					'std'     => 'inherit',
					'options' => catamaran_get_list_yesno( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'radio',
				),
				"header_widgets{$suffix}_{$cpt}"         => array(
					'title'   => esc_html__( 'Header widgets', 'catamaran' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select set of widgets to show in the header on the %s pages', 'catamaran' ), $title ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => 'select',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Sidebar'
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_sidebar' ) ) {
	function catamaran_options_get_list_cpt_options_sidebar( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = catamaran_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "catamaran_filter_get_list_cpt_options_sidebar{$suffix}", array_merge(
				array(
					"sidebar_info{$suffix}_{$cpt}"           => array(
						// Translators: Add CPT name to the description
						'title' => wp_kses_data( sprintf( __( 'Sidebar on %s', 'catamaran' ), $suffix2 ) ),
						// Translators: Add CPT name to the description
						'desc'  => wp_kses_data( sprintf( __( 'Set up sidebar parameters to display %s', 'catamaran' ), $suffix2 ) ),
						'type'  => 'info',
					),
					"sidebar_position{$suffix}_{$cpt}"       => array(
						'title'   => esc_html__( 'Sidebar position', 'catamaran' ),
						'desc'    => wp_kses_data( __( 'Select sidebar position', 'catamaran' ) ),
						'std'     => 'right',
						'options' => array(),
						'type'    => 'choice',
					),
					"sidebar_position_ss{$suffix}_{$cpt}"    => array(
						'title'    => esc_html__( 'Sidebar position on the small screen', 'catamaran' ),
						'desc'     => wp_kses_data( __( 'Select a position for the sidebar on the small screen: above the content, below or on a sliding side-panel.', 'catamaran' ) ),
						'std'      => 'below',
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'options'  => array(),
						'type'     => 'radio',
					),
					"sidebar_type{$suffix}_{$cpt}"           => array(
						'title'    => esc_html__( 'Sidebar style', 'catamaran' ),
						'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'std'      => 'default',
						'options'  => catamaran_get_list_header_footer_types( true ),
						'pro_only' => CATAMARAN_THEME_FREE,
						'type'     => ! catamaran_exists_trx_addons() ? 'hidden' : 'radio',
					),
					"sidebar_style{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
						'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'catamaran' ), 'catamaran_kses_content' ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'custom' ),
						),
						'std'        => '',
						'options'    => array(),
						'type'       => 'select',
					),
					"sidebar_widgets{$suffix}_{$cpt}"        => array(
						'title'      => esc_html__( 'Sidebar widgets', 'catamaran' ),
						'desc'       => wp_kses_data( __( 'Select set of widgets to display in the sidebar', 'catamaran' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'default' ),
						),
						'std'        => 'hide',
						'options'    => array(),
						'type'       => 'select',
					),
				),
				$mode == 'single' ? array() : array(
					"sidebar_width{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Sidebar width', 'catamaran' ),
						'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width (minimum sidebar width might vary based on its location and type)', 'catamaran' ) ),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 500,
						'step'       => 10,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => CATAMARAN_THEME_FREE,
						'type'       => 'slider',
					),
					"sidebar_gap{$suffix}_{$cpt}"            => array(
						'title'      => esc_html__( 'Sidebar gap', 'catamaran' ),
						'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'catamaran' ) ),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => CATAMARAN_THEME_FREE,
						'type'       => 'slider',
					),
					"sidebar_proportional{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Sidebar proportional', 'catamaran' ),
						'desc'       => wp_kses_data( __( 'Change the width of the sidebar and gap proportionally when the window is resized, or leave the width of the sidebar constant', 'catamaran' ) ),
						'refresh'    => false,
						'std'        => 1,
						'type'       => 'switch',
					),
				),
				array(
					"expand_content{$suffix}_{$cpt}"         => array(
					'title'   => esc_html__( 'Content width', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'catamaran' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => catamaran_get_list_expand_content( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'choice',
					),
				)
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Footer'
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_footer' ) ) {
	function catamaran_options_get_list_cpt_options_footer( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = catamaran_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "catamaran_filter_get_list_cpt_options_footer{$suffix}", array(
				"footer_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Footer on %s', 'catamaran' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up footer parameters to display %s', 'catamaran' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"footer_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Footer style', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'catamaran' ) ),
					'std'     => 'inherit',
					'options' => catamaran_get_list_header_footer_types( true ),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'radio',
				),
				"footer_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select custom layout to display the site footer', 'catamaran' ) ),
					'std'        => 'inherit',
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'options'    => array(),
					'pro_only'   => CATAMARAN_THEME_FREE,
					'type'       => 'select',
				),
				"footer_widgets{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer widgets', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'catamaran' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"footer_columns{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer columns', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'catamaran' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}"    => array( 'default' ),
						"footer_widgets{$suffix}_{$cpt}" => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => catamaran_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				"footer_wide{$suffix}_{$cpt}"            => array(
					'title'      => esc_html__( 'Footer fullwidth', 'catamaran' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'catamaran' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Additional Widget Areas'
if ( ! function_exists( 'catamaran_options_get_list_cpt_options_widgets' ) ) {
	function catamaran_options_get_list_cpt_options_widgets( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = catamaran_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "catamaran_filter_get_list_cpt_options_widgets{$suffix}", array(
				"widgets_info{$suffix}_{$cpt}"           => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Additional panels on %s', 'catamaran' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up additional panels to display %s', 'catamaran' ), $suffix2 ) ),
					'pro_only'  => CATAMARAN_THEME_FREE,
					'type'  => 'info',
				),
				"widgets_above_page{$suffix}_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_above_content{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_below_content{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
				"widgets_below_page{$suffix}_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'catamaran' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'catamaran' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> CATAMARAN_THEME_FREE,
					'type'    => 'select',
				),
			), $cpt, $title
		);
	}
}


// Return lists with choises when its need in the admin mode
if ( ! function_exists( 'catamaran_options_get_list_choises' ) ) {
	add_filter( 'catamaran_filter_options_get_list_choises', 'catamaran_options_get_list_choises', 10, 2 );
	function catamaran_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ( strpos( $id, 'header_style' ) === 0 ) {
				$list = catamaran_get_list_header_styles( strpos( $id, 'header_style_' ) === 0 );
			} elseif ( strpos( $id, 'header_position' ) === 0 ) {
				$list = catamaran_get_list_header_positions( strpos( $id, 'header_position_' ) === 0 );
			} elseif ( strpos( $id, 'header_widgets' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'header_widgets_' ) === 0, true );
			} elseif ( strpos( $id, '_scheme' ) > 0 ) {
				$list = catamaran_get_list_schemes( 'color_scheme' != $id );
			} else if ( strpos( $id, 'sidebar_style' ) === 0 ) {
				$list = catamaran_get_list_sidebar_styles( strpos( $id, 'sidebar_style_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_widgets' ) === 0 ) {
				$list = catamaran_get_list_sidebars( 'sidebar_widgets_single' != $id && ( strpos( $id, 'sidebar_widgets_' ) === 0 || strpos( $id, 'sidebar_widgets_single_' ) === 0 ), true );
			} elseif ( strpos( $id, 'sidebar_position_ss' ) === 0 ) {
				$list = catamaran_get_list_sidebars_positions_ss( strpos( $id, 'sidebar_position_ss_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_position' ) === 0 ) {
				$list = catamaran_get_list_sidebars_positions( strpos( $id, 'sidebar_position_' ) === 0 );
			} elseif ( strpos( $id, 'widgets_above_page' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'widgets_above_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_above_content' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'widgets_above_content_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_page' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'widgets_below_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_content' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'widgets_below_content_' ) === 0, true );
            } elseif ( strpos( $id, 'widgets_additional_menu_mobile_fullscreen' ) === 0 ) {
                $list = catamaran_get_list_sidebars( strpos( $id, 'widgets_additional_menu_mobile_fullscreen_' ) === 0, true );
			} elseif ( strpos( $id, 'footer_style' ) === 0 ) {
				$list = catamaran_get_list_footer_styles( strpos( $id, 'footer_style_' ) === 0 );
			} elseif ( strpos( $id, 'footer_widgets' ) === 0 ) {
				$list = catamaran_get_list_sidebars( strpos( $id, 'footer_widgets_' ) === 0, true );
			} elseif ( strpos( $id, 'blog_style' ) === 0 ) {
				$list = catamaran_get_list_blog_styles( strpos( $id, 'blog_style_' ) === 0 );
			} elseif ( strpos( $id, 'single_style' ) === 0 ) {
				$list = catamaran_get_list_single_styles( strpos( $id, 'single_style_' ) === 0 );
			} elseif ( strpos( $id, 'post_type' ) === 0 ) {
				$list = catamaran_get_list_posts_types();
			} elseif ( strpos( $id, 'parent_cat' ) === 0 ) {
				$list = catamaran_array_merge( array( 0 => esc_html__( '- Select category -', 'catamaran' ) ), catamaran_get_list_categories() );
			} elseif ( strpos( $id, 'blog_animation' ) === 0 ) {
				$list = catamaran_get_list_animations_in( strpos( $id, 'blog_animation_' ) === 0 );
			} elseif ( 'color_scheme_editor' == $id ) {
				$list = catamaran_get_list_schemes();
			} elseif ( 'color_preset' == $id ) {
				$list = catamaran_get_list_color_presets();
			} elseif ( strpos( $id, '_font-family' ) > 0 ) {
				$list = catamaran_get_list_load_fonts( true );
			} elseif ( 'font_preset' == $id ) {
				$list = catamaran_get_list_font_presets();
			}
		}
		return $list;
	}
}




//--------------------------------------------
// THUMBS
//--------------------------------------------
if ( ! function_exists( 'catamaran_skin_setup_thumbs' ) ) {
	add_action( 'after_setup_theme', 'catamaran_skin_setup_thumbs', 1 );
	function catamaran_skin_setup_thumbs() {
		catamaran_storage_set(
			'theme_thumbs', apply_filters(
				'catamaran_filter_add_thumb_sizes', array(
					// Width of the image is equal to the content area width (without sidebar)
					// Height is fixed
					'catamaran-thumb-huge'        => array(
						'size'  => array( 1290, 725, true ),
						'title' => esc_html__( 'Huge image', 'catamaran' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Width of the image is equal to the content area width (with sidebar)
					// Height is fixed
					'catamaran-thumb-big'         => array(
						'size'  => array( 840, 473, true ),
						'title' => esc_html__( 'Large image', 'catamaran' ),
						'subst' => 'trx_addons-thumb-big',
					),

					// Width of the image is equal to the 1/3 of the content area width (without sidebar)
					// Height is fixed
					'catamaran-thumb-med'         => array(
						'size'  => array( 410, 230, true ),
						'title' => esc_html__( 'Medium image', 'catamaran' ),
						'subst' => 'trx_addons-thumb-medium',
					),

					// Small square image (for avatars in comments, etc.)
					'catamaran-thumb-tiny'        => array(
						'size'  => array( 120, 120, true ),
						'title' => esc_html__( 'Small square avatar', 'catamaran' ),
						'subst' => 'trx_addons-thumb-tiny',
					),

					// Width of the image is equal to the content area width (with sidebar)
					// Height is proportional (only downscale, not crop)
					'catamaran-thumb-masonry-big' => array(
						'size'  => array( 840, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'catamaran' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),

					// Width of the image is equal to the 1/3 of the full content area width (without sidebar)
					// Height is proportional (only downscale, not crop)
					'catamaran-thumb-masonry'     => array(
						'size'  => array( 410, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'catamaran' ),
						'subst' => 'trx_addons-thumb-masonry',
					),

					'catamaran-thumb-rectangle' => array(
						'size'  => array( 570, 696, true ), // old - 480x586
						'title' => esc_html__( 'Rectangle', 'catamaran' ),
						'subst' => 'trx_addons-thumb-rectangle',
					),

                    'catamaran-thumb-medium-square' => array(
                        'size'  => array( 650, 572, true ),
                        'title' => esc_html__( 'Square medium', 'catamaran' ),
                        'subst' => 'trx_addons-thumb-medium-square',
                    ),

					'catamaran-thumb-square' => array(
						'size'  => array( 890, 664, true ),
						'title' => esc_html__( 'Square', 'catamaran' ),
						'subst' => 'trx_addons-thumb-square',
					),

				)
			)
		);
	}
}


//--------------------------------------------
// BLOG STYLES
//--------------------------------------------
if ( ! function_exists( 'catamaran_skin_setup_blog_styles' ) ) {
	add_action( 'after_setup_theme', 'catamaran_skin_setup_blog_styles', 1 );
	function catamaran_skin_setup_blog_styles() {

		$blog_styles = array(
			'excerpt' => array(
				'title'   => esc_html__( 'Standard', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-excerpt',
				'styles'  => 'excerpt',
				'icon'    => "images/theme-options/blog-style/excerpt.png",
			),
			'band'    => array(
				'title'   => esc_html__( 'Band', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-band',
				'styles'  => 'band',
				'icon'    => "images/theme-options/blog-style/band.png",
			),
			'classic' => array(
				'title'   => esc_html__( 'Classic', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3, 4 ),
				'styles'  => 'classic',
				'icon'    => "images/theme-options/blog-style/classic-%d.png",
				'new_row' => true,
			),
		);
		if ( ! CATAMARAN_THEME_FREE ) {
			$blog_styles['classic-masonry']   = array(
				'title'   => esc_html__( 'Classic Masonry', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'classic', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/classic-masonry-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio'] = array(
				'title'   => esc_html__( 'Portfolio', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => 'portfolio',
				'icon'    => "images/theme-options/blog-style/portfolio-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio-masonry'] = array(
				'title'   => esc_html__( 'Portfolio Masonry', 'catamaran' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'portfolio', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/portfolio-masonry-%d.png",
				'new_row' => true,
			);
		}
		catamaran_storage_set( 'blog_styles', apply_filters( 'catamaran_filter_add_blog_styles', $blog_styles ) );
	}
}


//--------------------------------------------
// SINGLE STYLES
//--------------------------------------------
if ( ! function_exists( 'catamaran_skin_setup_single_styles' ) ) {
	add_action( 'after_setup_theme', 'catamaran_skin_setup_single_styles', 1 );
	function catamaran_skin_setup_single_styles() {

		catamaran_storage_set( 'single_styles', apply_filters( 'catamaran_filter_add_single_styles', array(
			'style-1'   => array(
				'title'       => esc_html__( 'Style 1', 'catamaran' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are over the image', 'catamaran' ),
				'styles'      => 'style-1',
				'icon'        => "images/theme-options/single-style/style-1.png",
			),
			'style-2'   => array(
				'title'       => esc_html__( 'Style 2', 'catamaran' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are inside the content area', 'catamaran' ),
				'styles'      => 'style-2',
				'icon'        => "images/theme-options/single-style/style-2.png",
			),
			'style-3'   => array(
				'title'       => esc_html__( 'Style 3', 'catamaran' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are below the image', 'catamaran' ),
				'styles'      => 'style-3',
				'icon'        => "images/theme-options/single-style/style-3.png",
			),
			'style-4'   => array(
				'title'       => esc_html__( 'Style 4', 'catamaran' ),
				'description' => esc_html__( 'Boxed image is above the content area, the title and meta are above the image', 'catamaran' ),
				'styles'      => 'style-4',
				'icon'        => "images/theme-options/single-style/style-4.png",
			),
			'style-5'   => array(
				'title'       => esc_html__( 'Style 5', 'catamaran' ),
				'description' => esc_html__( 'Boxed image is inside the content area, the title and meta are above the content area', 'catamaran' ),
				'styles'      => 'style-5',
				'icon'        => "images/theme-options/single-style/style-5.png",
			),
			'style-6'   => array(
				'title'       => esc_html__( 'Style 6', 'catamaran' ),
				'description' => esc_html__( 'Boxed image, the title and meta are inside the content area, the title and meta are above the image', 'catamaran' ),
				'styles'      => 'style-6',
				'icon'        => "images/theme-options/single-style/style-6.png",
			),
			'style-7'   => array(
				'title'       => esc_html__( 'Style 7', 'catamaran' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are below the image', 'catamaran' ),
				'styles'      => 'style-7',
				'icon'        => "images/theme-options/single-style/style-7.png",
			),
		) ) );
	}
}
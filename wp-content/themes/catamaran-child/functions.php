<?php
/**
 * Child-Theme functions and definitions
 */

// Load rtl.css because it is not autoloaded from the child theme
if ( ! function_exists( 'catamaran_child_load_rtl' ) ) {
	add_filter( 'wp_enqueue_scripts', 'catamaran_child_load_rtl', 3000 );
	function catamaran_child_load_rtl() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'catamaran-style-rtl', get_template_directory_uri() . '/rtl.css' );
		}
	}
}

if ( ! function_exists( 'catamaran_child_enqueue_home_assets' ) ) {
	add_action( 'wp_enqueue_scripts', 'catamaran_child_enqueue_home_assets', 3010 );
	function catamaran_child_enqueue_home_assets() {
		$is_rentals_route = catamaran_child_is_rentals_route();
		$is_excursions_route = catamaran_child_is_excursions_route();
		$is_transfers_route = catamaran_child_is_transfers_route();
		$is_contacts_route = catamaran_child_is_contacts_route();

		if ( ! is_front_page() && ! $is_rentals_route && ! $is_excursions_route && ! $is_transfers_route && ! $is_contacts_route ) {
			return;
		}

		wp_enqueue_style(
			'catamaran-child-hvar-home',
			get_stylesheet_directory_uri() . '/assets/css/hvar-home.css',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/css/hvar-home.css' )
		);

		wp_enqueue_script(
			'catamaran-child-hvar-home',
			get_stylesheet_directory_uri() . '/assets/js/hvar-home.js',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/js/hvar-home.js' ),
			true
		);

		wp_add_inline_style( 'catamaran-child-hvar-home', catamaran_child_get_local_fonts_css() );

		if ( $is_rentals_route ) {
			wp_enqueue_style(
				'catamaran-child-hvar-rentals',
				get_stylesheet_directory_uri() . '/assets/css/hvar-rentals.css',
				array( 'catamaran-child-hvar-home' ),
				filemtime( get_stylesheet_directory() . '/assets/css/hvar-rentals.css' )
			);
		}

		if ( $is_excursions_route ) {
			wp_enqueue_style(
				'catamaran-child-hvar-excursions',
				get_stylesheet_directory_uri() . '/assets/css/hvar-excursions.css',
				array( 'catamaran-child-hvar-home' ),
				filemtime( get_stylesheet_directory() . '/assets/css/hvar-excursions.css' )
			);
		}

		if ( $is_transfers_route ) {
			wp_enqueue_style(
				'catamaran-child-hvar-transfers',
				get_stylesheet_directory_uri() . '/assets/css/hvar-transfers.css',
				array( 'catamaran-child-hvar-home' ),
				filemtime( get_stylesheet_directory() . '/assets/css/hvar-transfers.css' )
			);
		}

		if ( $is_contacts_route ) {
			wp_enqueue_style(
				'catamaran-child-hvar-contacts',
				get_stylesheet_directory_uri() . '/assets/css/hvar-contacts.css',
				array( 'catamaran-child-hvar-home' ),
				filemtime( get_stylesheet_directory() . '/assets/css/hvar-contacts.css' )
			);
		}
	}
}

if ( ! function_exists( 'catamaran_child_get_local_fonts_css' ) ) {
	function catamaran_child_get_local_fonts_css() {
		$font_base = trailingslashit( get_template_directory_uri() ) . 'skins/default/css/font-face/Montserrat/';

		return "
@font-face {
	font-family: 'HexMontserrat';
	src: url('{$font_base}montserrat-regular.woff2') format('woff2'),
		 url('{$font_base}montserrat-regular.woff') format('woff');
	font-weight: 400;
	font-style: normal;
	font-display: swap;
}
@font-face {
	font-family: 'HexMontserrat';
	src: url('{$font_base}montserrat-semibold.woff2') format('woff2'),
		 url('{$font_base}montserrat-semibold.woff') format('woff');
	font-weight: 600;
	font-style: normal;
	font-display: swap;
}
@font-face {
	font-family: 'HexMontserrat';
	src: url('{$font_base}montserrat-bold.woff2') format('woff2'),
		 url('{$font_base}montserrat-bold.woff') format('woff');
	font-weight: 700;
	font-style: normal;
	font-display: swap;
}
:root {
	--theme-font-p_font-family: 'HexMontserrat', 'Segoe UI', Arial, sans-serif;
	--theme-font-h1_font-family: 'HexMontserrat', 'Segoe UI', Arial, sans-serif;
}
.hex-homepage,
.hex-homepage button,
.hex-homepage input,
.hex-homepage select,
.hex-homepage textarea {
	font-family: var(--theme-font-p_font-family);
}
";
	}
}

if ( ! function_exists( 'catamaran_child_get_request_path' ) ) {
	function catamaran_child_get_request_path() {
		$request_path = wp_parse_url( home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ), PHP_URL_PATH );

		if ( empty( $request_path ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$request_path = wp_parse_url( home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
		}

		return trim( (string) $request_path, '/' );
	}
}

if ( ! function_exists( 'catamaran_child_is_custom_shell_route' ) ) {
	function catamaran_child_is_custom_shell_route() {
		return '' === catamaran_child_get_request_path()
			|| catamaran_child_is_rentals_route()
			|| catamaran_child_is_excursions_route()
			|| catamaran_child_is_transfers_route()
			|| catamaran_child_is_contacts_route();
	}
}

if ( ! function_exists( 'catamaran_child_reduce_parent_bootstrap' ) ) {
	add_action( 'after_setup_theme', 'catamaran_child_reduce_parent_bootstrap', 20 );
	function catamaran_child_reduce_parent_bootstrap() {
		if ( is_admin() || ! catamaran_child_is_custom_shell_route() ) {
			return;
		}

		$actions_to_remove = array(
			array( 'wp_head', 'catamaran_wp_head', 0 ),
			array( 'wp_footer', 'catamaran_wp_footer', 10 ),
			array( 'wp_footer', 'catamaran_localize_scripts', 10 ),
			array( 'widgets_init', 'catamaran_register_sidebars', 10 ),
			array( 'wp_enqueue_scripts', 'catamaran_load_theme_fonts', 0 ),
			array( 'wp_enqueue_scripts', 'catamaran_load_theme_icons', 0 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles', 1000 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_single', 1020 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_plugins', 1100 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_custom', 1200 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_child', 1500 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_responsive', 2000 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_single_responsive', 2020 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_styles_responsive_child', 2500 ),
			array( 'wp_enqueue_scripts', 'catamaran_wp_scripts', 1000 ),
		);

		foreach ( $actions_to_remove as $hook_config ) {
			remove_action( $hook_config[0], $hook_config[1], $hook_config[2] );
		}

		remove_filter( 'body_class', 'catamaran_add_body_classes' );
	}
}

if ( ! function_exists( 'catamaran_child_asset_image_url' ) ) {
	function catamaran_child_asset_image_url( $relative_path, $fallback_url = '' ) {
		$relative_path = ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );

		if ( '' === $relative_path ) {
			return $fallback_url;
		}

		$file_path = trailingslashit( get_stylesheet_directory() ) . 'assets/images/' . $relative_path;
		if ( ! file_exists( $file_path ) ) {
			return $fallback_url;
		}

		$segments = array_map( 'rawurlencode', explode( '/', $relative_path ) );
		return trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/' . implode( '/', $segments );
	}
}

if ( ! function_exists( 'catamaran_child_localize_image_url' ) ) {
	function catamaran_child_localize_image_url( $image_url ) {
		if ( empty( $image_url ) ) {
			return $image_url;
		}

		$path = wp_parse_url( $image_url, PHP_URL_PATH );
		if ( empty( $path ) ) {
			return $image_url;
		}

		$decoded_path = rawurldecode( ltrim( (string) $path, '/' ) );
		$decoded_path = str_replace( 'img/', '', $decoded_path );

		$exact_map = array(
			'logo/logo-bumbar-rent-hvar-excursions.png'                                           => 'logo/logo-bumbar-rent-hvar-excursions.png',
			'service-rentals.jpg'                                                              => 'home/hero1_boats_speedboats_rentals.jpg',
			'service-transfers.jpg'                                                            => 'home/hero2_taxi_transfers.jpg',
			'service-excursions.jpg'                                                           => 'home/hero3_excursions.jpg',
			'videos/speedboat_hvar_poster_image.jpg'                                           => 'transfers/transfers_1.jpg',
			'excursions/tour-hvar/thumb/tour_hvar_red_rocks_02.jpg'                            => 'excursions/red_rocks_hvar_1.jpg',
			'excursions/tour-vis/thumb/tour_hvar_blue_cave_01.jpg'                             => 'excursions/blue_cave_1.jpg',
			'excursions/tour-vis/thumb/tour_hvar_stiniva_01.jpg'                               => 'excursions/stiniva_vis_1.jpg',
			'excursions/tour-vis/thumb/tour_hvar_green_cave_01.jpg'                            => 'excursions/green_cave_1.jpg',
			'rentals/photos/raptor-alesta/thumb/raptor-alesta-hvar-excursions-rentals.jpg'     => 'rentals/Raptor/Raptor.jpg',
			'rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals (1-1).jpg'     => 'rentals/Raptor/Raptor_6.jpg',
			'rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals (1-3).jpg'     => 'rentals/Raptor/Raptor_7.jpg',
			'rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals (1-5).jpg'     => 'rentals/Raptor/Raptor_10.jpg',
			'rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals (2-1).jpg'     => 'rentals/Raptor/Raptor_11.jpg',
			'rentals/photos/quicksilver100hp/Quicksilver_100HP_1-2.jpg'                        => 'rentals/Quicksilver/Quicksilver_100HP_1-2.webp',
			'rentals/photos/quicksilver100hp/Quicksilver_100HP_1-4.jpg'                        => 'rentals/Quicksilver/Quicksilver_100HP_1-4.webp',
			'rentals/photos/quicksilver100hp/Quicksilver_100HP_1-5.jpg'                        => 'rentals/Quicksilver/Quicksilver_100HP_1-5.webp',
		);

		if ( isset( $exact_map[ $decoded_path ] ) ) {
			return catamaran_child_asset_image_url( $exact_map[ $decoded_path ], $image_url );
		}

		if ( 0 === strpos( $decoded_path, 'rentals/photos/' ) ) {
			$relative_path = str_replace( 'rentals/photos/', 'rentals/', $decoded_path );
			$relative_path = str_replace(
				array( 'raptor-alesta/', 'quicksilver100hp/' ),
				array( 'Raptor/', 'Quicksilver/' ),
				$relative_path
			);

			return catamaran_child_asset_image_url( $relative_path, $image_url );
		}

		return $image_url;
	}
}

if ( ! function_exists( 'catamaran_child_is_rentals_route' ) ) {
	function catamaran_child_is_rentals_route() {
		return 'rentals' === catamaran_child_get_request_path();
	}
}

if ( ! function_exists( 'catamaran_child_is_excursions_route' ) ) {
	function catamaran_child_is_excursions_route() {
		return in_array( catamaran_child_get_request_path(), array( 'excursions', 'service-plus' ), true );
	}
}

if ( ! function_exists( 'catamaran_child_is_transfers_route' ) ) {
	function catamaran_child_is_transfers_route() {
		return in_array( catamaran_child_get_request_path(), array( 'transfers', 'online-booking', 'taxi-and-speedboat-transfers' ), true );
	}
}

if ( ! function_exists( 'catamaran_child_is_contacts_route' ) ) {
	function catamaran_child_is_contacts_route() {
		return in_array( catamaran_child_get_request_path(), array( 'contacts', 'contact' ), true );
	}
}

if ( ! function_exists( 'catamaran_child_render_shell_start' ) ) {
	function catamaran_child_render_shell_start( $extra_body_classes = array() ) {
		?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( $extra_body_classes ); ?>>
<?php
		wp_body_open();
	}
}

if ( ! function_exists( 'catamaran_child_render_shell_end' ) ) {
	function catamaran_child_render_shell_end() {
		wp_footer();
		?>
</body>
</html>
<?php
	}
}

if ( ! function_exists( 'catamaran_child_should_strip_asset' ) ) {
	function catamaran_child_should_strip_asset( $src ) {
		if ( empty( $src ) ) {
			return false;
		}

		$src = (string) $src;

		if ( 0 === strpos( $src, '//' ) ) {
			$src = ( is_ssl() ? 'https:' : 'http:' ) . $src;
		}

		$keep_patterns = array(
			get_stylesheet_directory_uri(),
			'/swiper/',
		);

		foreach ( $keep_patterns as $pattern ) {
			if ( false !== strpos( $src, $pattern ) ) {
				return false;
			}
		}

		$strip_patterns = array(
			'/wp-content/plugins/advanced-popups/',
			'/wp-content/plugins/contact-form-7/',
			'/wp-content/plugins/custom-facebook-feed/',
			'/wp-content/plugins/custom-twitter-feeds/',
			'/wp-content/plugins/elementor/',
			'/wp-content/plugins/feeds-for-tiktok/',
			'/wp-content/plugins/feeds-for-youtube/',
			'/wp-content/plugins/instagram-feed/',
			'/wp-content/plugins/mailchimp-for-wp/',
			'/wp-content/plugins/quickcal/',
			'/wp-content/plugins/reviews-feed/',
			'/wp-content/plugins/revslider/',
			'/wp-content/plugins/the-events-calendar/',
			'/wp-content/plugins/ti-woocommerce-wishlist/',
			'/wp-content/plugins/trx_addons/',
			'/wp-content/plugins/woocommerce-payments/',
			'/wp-content/plugins/woocommerce-paypal-payments/',
			'/wp-content/plugins/woocommerce/',
			'/wp-content/themes/catamaran/',
			'/wp-includes/js/jquery/',
			'/wp-includes/js/jquery/ui/',
			'/wp-includes/js/mediaelement/',
			'use.fontawesome.com/releases/',
			'fonts.googleapis.com',
			'fonts.gstatic.com',
			'use.typekit.net',
		);

		foreach ( $strip_patterns as $pattern ) {
			if ( false !== strpos( $src, $pattern ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'catamaran_child_strip_custom_route_assets' ) ) {
	add_action( 'wp_enqueue_scripts', 'catamaran_child_strip_custom_route_assets', 99999 );
	function catamaran_child_strip_custom_route_assets() {
		if ( ! catamaran_child_is_custom_shell_route() ) {
			return;
		}

		global $wp_styles, $wp_scripts;

		if ( $wp_styles instanceof WP_Styles ) {
			foreach ( $wp_styles->registered as $handle => $style ) {
				if ( catamaran_child_should_strip_asset( $style->src ) ) {
					wp_dequeue_style( $handle );
					wp_deregister_style( $handle );
				}
			}
		}

		if ( $wp_scripts instanceof WP_Scripts ) {
			foreach ( $wp_scripts->registered as $handle => $script ) {
				if ( catamaran_child_should_strip_asset( $script->src ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
				}
			}
		}

		wp_dequeue_script( 'wp-embed' );
		wp_deregister_script( 'wp-embed' );
	}
}

if ( ! function_exists( 'catamaran_child_cleanup_shell_html' ) ) {
	function catamaran_child_cleanup_shell_html( $html ) {
		$patterns = array(
			'#<link[^>]+href=["\'](?:https?:)?//fonts\.googleapis\.com[^>]*>\s*#i',
			'#<link[^>]+href=["\'](?:https?:)?//fonts\.gstatic\.com[^>]*>\s*#i',
			'#<link[^>]+href=["\'](?:https?:)?//use\.fontawesome\.com[^>]*>\s*#i',
		);

		return preg_replace( $patterns, '', $html );
	}
}

if ( ! function_exists( 'catamaran_child_start_shell_output_buffer' ) ) {
	add_action( 'template_redirect', 'catamaran_child_start_shell_output_buffer', 1 );
	function catamaran_child_start_shell_output_buffer() {
		if ( ! catamaran_child_is_custom_shell_route() ) {
			return;
		}

		if ( ! headers_sent() ) {
			ob_start( 'catamaran_child_cleanup_shell_html' );
		}
	}
}

if ( ! function_exists( 'catamaran_child_filter_style_loader_tag' ) ) {
	add_filter( 'style_loader_tag', 'catamaran_child_filter_style_loader_tag', 10, 4 );
	function catamaran_child_filter_style_loader_tag( $html, $handle, $href, $media ) {
		if ( ! catamaran_child_is_custom_shell_route() ) {
			return $html;
		}

		$href = (string) $href;
		if ( catamaran_child_should_strip_asset( $href ) ) {
			return '';
		}

		return $html;
	}
}

if ( ! function_exists( 'catamaran_child_filter_script_loader_tag' ) ) {
	add_filter( 'script_loader_tag', 'catamaran_child_filter_script_loader_tag', 10, 3 );
	function catamaran_child_filter_script_loader_tag( $tag, $handle, $src ) {
		if ( ! catamaran_child_is_custom_shell_route() ) {
			return $tag;
		}

		if ( catamaran_child_should_strip_asset( (string) $src ) ) {
			return '';
		}

		return $tag;
	}
}

if ( ! function_exists( 'catamaran_child_filter_resource_hints' ) ) {
	add_filter( 'wp_resource_hints', 'catamaran_child_filter_resource_hints', 10, 2 );
	function catamaran_child_filter_resource_hints( $urls, $relation_type ) {
		if ( ! catamaran_child_is_custom_shell_route() ) {
			return $urls;
		}

		$blocked_hosts = array(
			'fonts.googleapis.com',
			'fonts.gstatic.com',
			'use.fontawesome.com',
			'use.typekit.net',
		);

		return array_values(
			array_filter(
				$urls,
				static function ( $url ) use ( $blocked_hosts ) {
					$href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;
					$href = (string) $href;

					foreach ( $blocked_hosts as $blocked_host ) {
						if ( false !== strpos( $href, $blocked_host ) ) {
							return false;
						}
					}

					return true;
				}
			)
		);
	}
}

if ( ! function_exists( 'catamaran_child_add_rentals_body_class' ) ) {
	add_filter( 'body_class', 'catamaran_child_add_rentals_body_class' );
	function catamaran_child_add_rentals_body_class( $classes ) {
		if ( catamaran_child_is_rentals_route() ) {
			$classes[] = 'hex-rentals-page';
		}

		if ( catamaran_child_is_excursions_route() ) {
			$classes[] = 'hex-excursions-page';
		}

		if ( catamaran_child_is_transfers_route() ) {
			$classes[] = 'hex-transfers-page';
		}

		if ( catamaran_child_is_contacts_route() ) {
			$classes[] = 'hex-contacts-page';
		}

		return $classes;
	}
}

if ( ! function_exists( 'catamaran_child_rentals_document_title' ) ) {
	add_filter( 'pre_get_document_title', 'catamaran_child_rentals_document_title' );
	function catamaran_child_rentals_document_title( $title ) {
		if ( catamaran_child_is_rentals_route() ) {
			return 'Rentals | Hvar Excursions';
		}

		if ( catamaran_child_is_excursions_route() ) {
			return 'Excursions | Hvar Excursions';
		}

		if ( catamaran_child_is_transfers_route() ) {
			return 'Transfers | Hvar Excursions';
		}

		if ( catamaran_child_is_contacts_route() ) {
			return 'Contact | Hvar Excursions';
		}

		return $title;
	}
}

if ( ! function_exists( 'catamaran_child_render_virtual_rentals_page' ) ) {
	add_action( 'template_redirect', 'catamaran_child_render_virtual_rentals_page', 0 );
	function catamaran_child_render_virtual_rentals_page() {
		if ( ! catamaran_child_is_rentals_route() && ! catamaran_child_is_excursions_route() && ! catamaran_child_is_transfers_route() && ! catamaran_child_is_contacts_route() ) {
			return;
		}

		global $wp_query;

		if ( $wp_query instanceof WP_Query ) {
			$wp_query->is_404      = false;
			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
		}

		status_header( 200 );
		nocache_headers();

		if ( catamaran_child_is_rentals_route() ) {
			include get_stylesheet_directory() . '/rentals-page.php';
			exit;
		}

		if ( catamaran_child_is_transfers_route() ) {
			include get_stylesheet_directory() . '/transfers-page.php';
			exit;
		}

		if ( catamaran_child_is_contacts_route() ) {
			include get_stylesheet_directory() . '/contacts-page.php';
			exit;
		}

		include get_stylesheet_directory() . '/excursions-page.php';
		exit;
	}
}

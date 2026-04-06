<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_Plugin {

	private static $instance = null;
	const PROTECTED_ADMIN_LOGIN = 'admin_domo';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'maybe_upgrade' ), 1 );
		add_action( 'init', array( $this, 'enforce_protected_admin_account' ), 2 );
		add_action( 'init', array( 'Hex_Bookings_Screen', 'register_rewrite' ) );
		add_action( 'set_user_role', array( $this, 'prevent_protected_admin_role_change' ), 10, 3 );
		add_action( 'user_profile_update_errors', array( $this, 'protect_admin_profile_update' ), 10, 3 );
		add_action( 'delete_user', array( $this, 'prevent_protected_admin_deletion' ), 10, 1 );
		add_filter( 'editable_roles', array( $this, 'filter_editable_roles_for_protected_admin' ) );
		add_filter( 'query_vars', array( 'Hex_Bookings_Screen', 'add_query_var' ) );
		add_action( 'template_redirect', array( 'Hex_Bookings_Screen', 'maybe_render' ), 1 );
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	public function maybe_upgrade() {
		if ( get_option( 'hex_bookings_schema_version' ) !== Hex_Bookings_Installer::SCHEMA_VERSION ) {
			Hex_Bookings_Installer::maybe_upgrade();
		}

		$this->maybe_flush_internal_routes();
	}

	public function enforce_protected_admin_account() {
		$user = get_user_by( 'login', self::PROTECTED_ADMIN_LOGIN );

		if ( ! $user instanceof WP_User ) {
			return;
		}

		if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
			$user->set_role( 'administrator' );
		}
	}

	protected function maybe_flush_internal_routes() {
		$rules = get_option( 'rewrite_rules' );
		$required_rules = array(
			'^internal-bookings/?$',
			'^internal-dispatch/?$',
			'^internal-bookers/?$',
			'^internal-booking-settings/?$',
		);

		foreach ( $required_rules as $required_rule ) {
			if ( empty( $rules[ $required_rule ] ) ) {
				Hex_Bookings_Screen::register_rewrite();
				flush_rewrite_rules( false );
				break;
			}
		}
	}

	public function prevent_protected_admin_role_change( $user_id, $new_role, $old_roles ) {
		if ( ! self::is_protected_admin_user( $user_id ) ) {
			return;
		}

		if ( 'administrator' === $new_role ) {
			return;
		}

		$user = get_userdata( $user_id );
		if ( $user instanceof WP_User ) {
			$user->set_role( 'administrator' );
		}
	}

	public function protect_admin_profile_update( $errors, $update, $user ) {
		if ( ! $user instanceof WP_User || ! self::is_protected_admin_user( $user->ID ) ) {
			return;
		}

		$posted_role = isset( $_POST['role'] ) ? sanitize_key( wp_unslash( $_POST['role'] ) ) : 'administrator';
		if ( '' === $posted_role ) {
			$posted_role = 'administrator';
		}

		if ( 'administrator' !== $posted_role ) {
			$errors->add(
				'hex_protected_admin_role',
				__( 'The protected admin_domo account must remain an Administrator and cannot be downgraded.', 'hvar-bookings' )
			);
		}
	}

	public function prevent_protected_admin_deletion( $user_id ) {
		if ( self::is_protected_admin_user( $user_id ) ) {
			wp_die(
				esc_html__( 'The protected admin_domo account cannot be deleted.', 'hvar-bookings' ),
				403
			);
		}
	}

	public function filter_editable_roles_for_protected_admin( $roles ) {
		if ( ! is_admin() ) {
			return $roles;
		}

		$target_user_id = 0;

		if ( isset( $_GET['user_id'] ) ) {
			$target_user_id = (int) $_GET['user_id'];
		} elseif ( isset( $_POST['user_id'] ) ) {
			$target_user_id = (int) $_POST['user_id'];
		}

		if ( self::is_protected_admin_user( $target_user_id ) ) {
			return isset( $roles['administrator'] ) ? array( 'administrator' => $roles['administrator'] ) : $roles;
		}

		return $roles;
	}

	public function register_rest() {
		$controller = new Hex_Bookings_REST_Controller();
		$controller->register_routes();
	}

	public function register_admin_menu() {
		add_menu_page(
			__( 'Boat Bookings', 'hvar-bookings' ),
			__( 'Boat Bookings', 'hvar-bookings' ),
			'access_hex_bookings',
			'hex-bookings',
			array( $this, 'render_admin_redirect' ),
			'dashicons-calendar-alt',
			58
		);

		add_submenu_page(
			'hex-bookings',
			__( 'Dispatch', 'hvar-bookings' ),
			__( 'Dispatch', 'hvar-bookings' ),
			'access_hex_bookings',
			'hex-dispatch',
			array( $this, 'render_dispatch_redirect' )
		);

		add_submenu_page(
			'hex-bookings',
			__( 'Bookers', 'hvar-bookings' ),
			__( 'Bookers', 'hvar-bookings' ),
			'manage_hex_bookings',
			'hex-bookers',
			array( $this, 'render_bookers_redirect' )
		);

		add_submenu_page(
			'hex-bookings',
			__( 'Settings', 'hvar-bookings' ),
			__( 'Settings', 'hvar-bookings' ),
			'manage_hex_bookings',
			'hex-booking-settings',
			array( $this, 'render_settings_redirect' )
		);
	}

	public function render_admin_redirect() {
		wp_safe_redirect( home_url( '/internal-bookings/' ) );
		exit;
	}

	public function render_bookers_redirect() {
		wp_safe_redirect( home_url( '/internal-bookers/' ) );
		exit;
	}

	public function render_dispatch_redirect() {
		wp_safe_redirect( home_url( '/internal-dispatch/' ) );
		exit;
	}

	public function render_settings_redirect() {
		wp_safe_redirect( home_url( '/internal-booking-settings/' ) );
		exit;
	}

	public static function enqueue_screen_assets() {
		wp_enqueue_script(
			'fullcalendar-scheduler',
			'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.19/index.global.min.js',
			array(),
			'6.1.19',
			true
		);

		wp_enqueue_style(
			'hex-bookings-screen',
			HEX_BOOKINGS_URL . 'assets/css/screen.css',
			array(),
			filemtime( HEX_BOOKINGS_DIR . 'assets/css/screen.css' )
		);

		wp_enqueue_script(
			'hex-bookings-screen',
			HEX_BOOKINGS_URL . 'assets/js/screen.js',
			array( 'fullcalendar-scheduler' ),
			filemtime( HEX_BOOKINGS_DIR . 'assets/js/screen.js' ),
			true
		);

		wp_localize_script(
			'hex-bookings-screen',
			'hexBookingsConfig',
			self::get_frontend_config()
		);
	}

	public static function enqueue_dispatch_assets() {
		wp_enqueue_style(
			'hex-bookings-screen',
			HEX_BOOKINGS_URL . 'assets/css/screen.css',
			array(),
			filemtime( HEX_BOOKINGS_DIR . 'assets/css/screen.css' )
		);

		wp_enqueue_style(
			'hex-bookings-dispatch',
			HEX_BOOKINGS_URL . 'assets/css/dispatch.css',
			array( 'hex-bookings-screen' ),
			filemtime( HEX_BOOKINGS_DIR . 'assets/css/dispatch.css' )
		);

		wp_enqueue_script(
			'hex-bookings-dispatch',
			HEX_BOOKINGS_URL . 'assets/js/dispatch.js',
			array(),
			filemtime( HEX_BOOKINGS_DIR . 'assets/js/dispatch.js' ),
			true
		);

		wp_localize_script(
			'hex-bookings-dispatch',
			'hexBookingsConfig',
			self::get_frontend_config()
		);
	}

	public static function enqueue_settings_assets() {
		wp_enqueue_style(
			'hex-bookings-screen',
			HEX_BOOKINGS_URL . 'assets/css/screen.css',
			array(),
			filemtime( HEX_BOOKINGS_DIR . 'assets/css/screen.css' )
		);

		wp_enqueue_style(
			'hex-bookings-settings',
			HEX_BOOKINGS_URL . 'assets/css/settings.css',
			array( 'hex-bookings-screen' ),
			filemtime( HEX_BOOKINGS_DIR . 'assets/css/settings.css' )
		);

		wp_enqueue_script(
			'hex-bookings-settings',
			HEX_BOOKINGS_URL . 'assets/js/settings.js',
			array(),
			filemtime( HEX_BOOKINGS_DIR . 'assets/js/settings.js' ),
			true
		);
	}

	public static function get_frontend_config() {
		return array(
			'restUrl'             => esc_url_raw( rest_url( 'hex-bookings/v1/' ) ),
			'nonce'               => wp_create_nonce( 'wp_rest' ),
			'internalUrl'         => home_url( '/internal-bookings/' ),
			'internalDispatchUrl' => home_url( '/internal-dispatch/' ),
			'internalBookersUrl'  => home_url( '/internal-bookers/' ),
			'internalSettingsUrl' => home_url( '/internal-booking-settings/' ),
			'legend'              => self::get_color_legend(),
			'calendarMode'        => 'fullcalendar-premium-trial',
			'licenseKey'          => 'CC-Attribution-NonCommercial-NoDerivatives',
			'timezone'            => wp_timezone_string() ?: 'Europe/Zagreb',
		);
	}

	public static function get_sales_channels() {
		$settings = self::get_dispatch_settings();
		return apply_filters( 'hex_bookings_sales_channels', $settings['sales_channels'] );
	}

	public static function get_extra_equipment_options() {
		$settings = self::get_dispatch_settings();
		return apply_filters( 'hex_bookings_extra_equipment_options', $settings['equipment_options'] );
	}

	public static function get_transfer_locations() {
		$settings = self::get_dispatch_settings();
		return apply_filters( 'hex_bookings_transfer_locations', $settings['transfer_locations'] );
	}

	public static function get_manager_whatsapp_number() {
		$settings = self::get_dispatch_settings();
		return (string) ( $settings['manager_whatsapp_number'] ?? '' );
	}

	public static function get_manager_notification_time() {
		$settings = self::get_dispatch_settings();
		return (string) ( $settings['manager_notification_time'] ?? '17:00' );
	}

	public static function get_dispatch_settings() {
		$defaults = array(
			'sales_channels'    => array(
				array( 'value' => 'cash', 'label' => __( 'Cash', 'hvar-bookings' ) ),
				array( 'value' => 'click_boat', 'label' => __( 'Click&Boat', 'hvar-bookings' ) ),
				array( 'value' => 'sam_boat', 'label' => __( 'SamBoat', 'hvar-bookings' ) ),
				array( 'value' => 'get_my_boat', 'label' => __( 'GetMyBoat', 'hvar-bookings' ) ),
			),
			'equipment_options' => array(
				array( 'value' => 'skiis', 'label' => __( 'Skiis', 'hvar-bookings' ) ),
				array( 'value' => 'wakeboard', 'label' => __( 'Wakeboard', 'hvar-bookings' ) ),
				array( 'value' => 'inflatable_tube', 'label' => __( 'Inflatable Tube', 'hvar-bookings' ) ),
			),
			'transfer_locations' => array(
				array( 'value' => 'split_airport', 'label' => __( 'Split Airport', 'hvar-bookings' ), 'coordinates' => '43.5389,16.2980' ),
				array( 'value' => 'split_harbour', 'label' => __( 'Split Harbour', 'hvar-bookings' ), 'coordinates' => '43.5074,16.4402' ),
				array( 'value' => 'hvar_harbour', 'label' => __( 'Hvar Harbour', 'hvar-bookings' ), 'coordinates' => '43.1729,16.4426' ),
			),
			'manager_whatsapp_number' => '',
			'manager_notification_time' => '17:00',
		);

		$stored = get_option( 'hex_bookings_dispatch_settings', array() );
		$stored = is_array( $stored ) ? $stored : array();

		return array(
			'sales_channels'     => self::sanitize_settings_list( $stored['sales_channels'] ?? $defaults['sales_channels'] ),
			'equipment_options'  => self::sanitize_settings_list( $stored['equipment_options'] ?? $defaults['equipment_options'] ),
			'transfer_locations' => self::sanitize_transfer_locations( $stored['transfer_locations'] ?? $defaults['transfer_locations'] ),
			'manager_whatsapp_number' => self::sanitize_phone_value( $stored['manager_whatsapp_number'] ?? $defaults['manager_whatsapp_number'] ),
			'manager_notification_time' => self::sanitize_time_value( $stored['manager_notification_time'] ?? $defaults['manager_notification_time'] ),
		);
	}

	public static function update_dispatch_settings( $settings ) {
		$sanitized = array(
			'sales_channels'     => self::sanitize_settings_list( $settings['sales_channels'] ?? array() ),
			'equipment_options'  => self::sanitize_settings_list( $settings['equipment_options'] ?? array() ),
			'transfer_locations' => self::sanitize_transfer_locations( $settings['transfer_locations'] ?? array() ),
			'manager_whatsapp_number' => self::sanitize_phone_value( $settings['manager_whatsapp_number'] ?? '' ),
			'manager_notification_time' => self::sanitize_time_value( $settings['manager_notification_time'] ?? '17:00' ),
		);

		update_option( 'hex_bookings_dispatch_settings', $sanitized, false );

		return $sanitized;
	}

	protected static function sanitize_settings_list( $items ) {
		$items = is_array( $items ) ? $items : array();
		$clean = array();

		foreach ( $items as $item ) {
			$label = sanitize_text_field( (string) ( $item['label'] ?? '' ) );
			if ( '' === $label ) {
				continue;
			}

			$value = sanitize_key( (string) ( $item['value'] ?? '' ) );
			if ( '' === $value ) {
				$value = sanitize_title( $label );
			}

			$clean[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $clean;
	}

	protected static function sanitize_transfer_locations( $items ) {
		$items = is_array( $items ) ? $items : array();
		$clean = array();

		foreach ( $items as $item ) {
			$label = sanitize_text_field( (string) ( $item['label'] ?? '' ) );
			if ( '' === $label ) {
				continue;
			}

			$value = sanitize_key( (string) ( $item['value'] ?? '' ) );
			if ( '' === $value ) {
				$value = sanitize_title( $label );
			}

			if ( 'custom' === $value ) {
				$value = sanitize_title( $label . '-location' );
			}

			$coordinates = preg_replace( '/[^0-9,\.\-\s]/', '', (string) ( $item['coordinates'] ?? '' ) );

			$clean[] = array(
				'value'       => $value,
				'label'       => $label,
				'coordinates' => trim( $coordinates ),
			);
		}

		return $clean;
	}

	protected static function sanitize_phone_value( $value ) {
		return trim( preg_replace( '/[^0-9+\s]/', '', (string) $value ) );
	}

	protected static function sanitize_time_value( $value ) {
		$value = trim( (string) $value );
		return 1 === preg_match( '/^\d{2}:\d{2}$/', $value ) ? $value : '17:00';
	}

	public static function get_color_legend() {
		return array(
			array(
				'key'         => 'with_skipper',
				'label'       => __( 'Blue: With skipper', 'hvar-bookings' ),
				'description' => __( 'Confirmed bookings with skipper, including transfers and most excursions.', 'hvar-bookings' ),
				'swatch'      => '#1f6bff',
			),
			array(
				'key'         => 'without_skipper',
				'label'       => __( 'Red: Without skipper', 'hvar-bookings' ),
				'description' => __( 'Confirmed bareboat rentals.', 'hvar-bookings' ),
				'swatch'      => '#d63131',
			),
			array(
				'key'         => 'taxi',
				'label'       => __( 'Pink: Taxi in Hvar area', 'hvar-bookings' ),
				'description' => __( 'Confirmed Hvar taxi bookings.', 'hvar-bookings' ),
				'swatch'      => '#ec5ca6',
			),
			array(
				'key'         => 'draft',
				'label'       => __( 'Gray: Draft', 'hvar-bookings' ),
				'description' => __( 'Tentative or unfinished bookings.', 'hvar-bookings' ),
				'swatch'      => '#8d98a8',
			),
			array(
				'key'         => 'cancelled',
				'label'       => __( 'Faded: Cancelled', 'hvar-bookings' ),
				'description' => __( 'Cancelled bookings remain visible for context.', 'hvar-bookings' ),
				'swatch'      => '#d8dde6',
			),
			array(
				'key'         => 'blocked',
				'label'       => __( 'Orange: Blocked', 'hvar-bookings' ),
				'description' => __( 'Reserved, maintenance, or manually blocked time.', 'hvar-bookings' ),
				'swatch'      => '#f28b26',
			),
		);
	}

	public static function get_protected_admin_user() {
		$user = get_user_by( 'login', self::PROTECTED_ADMIN_LOGIN );
		return $user instanceof WP_User ? $user : null;
	}

	public static function is_protected_admin_user( $user_id ) {
		$protected_user = self::get_protected_admin_user();
		return $protected_user && (int) $protected_user->ID === (int) $user_id;
	}

	public static function resolve_event_badge( $service_type, $skipper_mode ) {
		if ( 'taxi' === $service_type ) {
			return 'TX';
		}

		if ( 'without_skipper' === $skipper_mode ) {
			return 'NS';
		}

		return 'SK';
	}

	public static function resolve_event_style( $status, $service_type, $skipper_mode ) {
		if ( 'cancelled' === $status ) {
			return array(
				'background' => '#eef2f7',
				'border'     => '#c5ccd8',
				'text'       => '#6a7482',
				'classes'    => array( 'is-cancelled' ),
			);
		}

		if ( 'draft' === $status ) {
			return array(
				'background' => '#8d98a8',
				'border'     => '#7a8595',
				'text'       => '#ffffff',
				'classes'    => array( 'is-draft' ),
			);
		}

		if ( 'blocked' === $status ) {
			return array(
				'background' => '#f28b26',
				'border'     => '#d97516',
				'text'       => '#ffffff',
				'classes'    => array( 'is-blocked' ),
			);
		}

		if ( 'taxi' === $service_type ) {
			return array(
				'background' => '#ec5ca6',
				'border'     => '#d83b8d',
				'text'       => '#ffffff',
				'classes'    => array( 'is-taxi' ),
			);
		}

		if ( 'without_skipper' === $skipper_mode ) {
			return array(
				'background' => '#d63131',
				'border'     => '#b51f1f',
				'text'       => '#ffffff',
				'classes'    => array( 'is-without-skipper' ),
			);
		}

		return array(
			'background' => '#1f6bff',
			'border'     => '#1555d1',
			'text'       => '#ffffff',
			'classes'    => array( 'is-with-skipper' ),
		);
	}
}

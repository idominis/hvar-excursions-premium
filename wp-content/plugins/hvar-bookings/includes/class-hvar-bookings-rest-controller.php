<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_REST_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'hex-bookings/v1';
		$this->rest_base = 'bookings';
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/system',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_system' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/resources',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_resources' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/preferences',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_preferences' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_preferences' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/bookings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_bookings' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_booking' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/bookings/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_booking' ),
					'permission_callback' => array( $this, 'can_access' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_booking' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_booking' ),
					'permission_callback' => array( $this, 'can_delete' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/bookings/(?P<id>\d+)/send-confirmation',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'send_confirmation' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
			)
		);
	}

	public function can_access() {
		return current_user_can( 'access_hex_bookings' ) || current_user_can( 'manage_hex_bookings' );
	}

	public function can_edit() {
		return current_user_can( 'create_hex_bookings' ) || current_user_can( 'edit_hex_bookings' ) || current_user_can( 'manage_hex_bookings' );
	}

	public function can_delete() {
		return current_user_can( 'delete_hex_bookings' ) || current_user_can( 'manage_hex_bookings' );
	}

	public function get_system( WP_REST_Request $request ) {
		$current_user = wp_get_current_user();
		$can_manage   = current_user_can( 'manage_hex_bookings' );

		return rest_ensure_response(
			array(
				'plugin_version' => HEX_BOOKINGS_VERSION,
				'schema_version' => get_option( 'hex_bookings_schema_version' ),
				'current_user'   => array(
					'id'           => get_current_user_id(),
					'initials'     => self::get_user_initials( get_current_user_id() ),
					'display_name' => (string) $current_user->display_name,
					'email'        => (string) $current_user->user_email,
				),
				'permissions'   => array(
					'can_manage' => $can_manage,
				),
				'bookers'       => $can_manage ? self::get_booker_options() : array(),
				'saved_filters' => self::get_saved_filters(),
				'sales_channels' => Hex_Bookings_Plugin::get_sales_channels(),
				'equipment_options' => Hex_Bookings_Plugin::get_extra_equipment_options(),
				'transfer_locations' => Hex_Bookings_Plugin::get_transfer_locations(),
				'notification_settings' => array(
					'manager_whatsapp_number' => Hex_Bookings_Plugin::get_manager_whatsapp_number(),
					'manager_notification_time' => Hex_Bookings_Plugin::get_manager_notification_time(),
				),
				'calendar_mode'  => 'fullcalendar-premium-trial',
				'color_legend'   => Hex_Bookings_Plugin::get_color_legend(),
			)
		);
	}

	public function get_preferences( WP_REST_Request $request ) {
		return rest_ensure_response(
			array(
				'filters' => self::get_saved_filters(),
			)
		);
	}

	public function update_preferences( WP_REST_Request $request ) {
		$filters = self::sanitize_saved_filters( $request->get_param( 'filters' ) );
		update_user_meta( get_current_user_id(), 'hex_bookings_filters', $filters );

		return rest_ensure_response(
			array(
				'filters' => $filters,
				'saved'   => true,
			)
		);
	}

	public function get_resources( WP_REST_Request $request ) {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_resources';
		$rows  = $wpdb->get_results(
			"SELECT id, resource_type, category, name, slug, capacity, sort_order, color, supports_skipper, supports_bareboat, default_service_type
			 FROM {$table}
			 WHERE is_active = 1
			 ORDER BY category ASC, sort_order ASC, name ASC",
			ARRAY_A
		);

		return rest_ensure_response( array( 'resources' => $rows ) );
	}

	public function get_bookings( WP_REST_Request $request ) {
		global $wpdb;

		$table       = $wpdb->prefix . 'hex_bookings';
		$date_from   = sanitize_text_field( (string) $request->get_param( 'date_from' ) );
		$date_to     = sanitize_text_field( (string) $request->get_param( 'date_to' ) );
		$resource_id = (int) $request->get_param( 'resource_id' );
		$booker_id   = (int) $request->get_param( 'booker_user_id' );
		$status      = sanitize_key( (string) $request->get_param( 'status' ) );
		$service     = sanitize_key( (string) $request->get_param( 'service_type' ) );
		$only_mine   = rest_sanitize_boolean( $request->get_param( 'only_mine' ) );
		$all_dates   = rest_sanitize_boolean( $request->get_param( 'all_dates' ) );

		if ( ! current_user_can( 'manage_hex_bookings' ) ) {
			$only_mine = true;
		}

		if ( ! $all_dates && empty( $date_from ) ) {
			$date_from = gmdate( 'Y-m-01' );
		}

		if ( ! $all_dates && empty( $date_to ) ) {
			$date_to = gmdate( 'Y-m-t', strtotime( $date_from ) );
		}

		$sql    = "SELECT * FROM {$table} WHERE deleted_at IS NULL";
		$params = array();

		if ( ! $all_dates ) {
			$sql      .= ' AND booking_date BETWEEN %s AND %s';
			$params[] = $date_from;
			$params[] = $date_to;
		}

		if ( $resource_id > 0 ) {
			$sql      .= ' AND resource_id = %d';
			$params[] = $resource_id;
		}

		if ( in_array( $status, array( 'draft', 'confirmed', 'blocked', 'cancelled' ), true ) ) {
			$sql      .= ' AND status = %s';
			$params[] = $status;
		}

		if ( in_array( $service, array( 'rental', 'transfer', 'excursion', 'taxi' ), true ) ) {
			$sql      .= ' AND service_type = %s';
			$params[] = $service;
		}

		if ( $only_mine ) {
			$sql      .= ' AND booker_user_id = %d';
			$params[] = get_current_user_id();
		} elseif ( $booker_id > 0 && current_user_can( 'manage_hex_bookings' ) ) {
			$sql      .= ' AND booker_user_id = %d';
			$params[] = $booker_id;
		}

		$sql   .= ' ORDER BY booking_date ASC, start_time ASC, id ASC';
		$query  = ! empty( $params ) ? $wpdb->prepare( $sql, $params ) : $sql;

		$rows   = array_map( array( __CLASS__, 'prepare_booking_for_response' ), $wpdb->get_results( $query, ARRAY_A ) );
		$events = array_map( array( __CLASS__, 'map_booking_to_event' ), $rows );

		return rest_ensure_response(
			array(
				'events'   => $events,
				'filters'  => array(
					'resource_id'  => $resource_id,
					'booker_user_id' => $booker_id,
					'status'       => $status,
					'service_type' => $service,
					'only_mine'    => $only_mine,
					'all_dates'    => $all_dates,
				),
			)
		);
	}

	public function get_booking( WP_REST_Request $request ) {
		$booking = self::prepare_booking_for_response( self::get_booking_row( (int) $request['id'] ) );

		if ( ! $booking ) {
			return new WP_Error( 'hex_booking_not_found', __( 'Booking not found.', 'hvar-bookings' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response(
			array(
				'booking' => $booking,
				'event'   => self::map_booking_to_event( $booking ),
				'confirmation' => Hex_Bookings_Documents::build_confirmation_preview( $booking ),
				'manager_notification' => Hex_Bookings_Documents::build_manager_notification( $booking ),
			)
		);
	}

	public function create_booking( WP_REST_Request $request ) {
		global $wpdb;

		$normalized = $this->normalize_payload( $request );
		if ( is_wp_error( $normalized ) ) {
			return $normalized;
		}

		$table  = $wpdb->prefix . 'hex_bookings';
		$result = $wpdb->insert( $table, $normalized );

		if ( false === $result ) {
			return new WP_Error( 'hex_booking_insert_failed', __( 'Failed to create booking.', 'hvar-bookings' ), array( 'status' => 500 ) );
		}

		$booking_id = (int) $wpdb->insert_id;
		self::insert_audit_entry( $booking_id, 'created', null, self::get_booking_row( $booking_id ) );
		$response_booking = self::prepare_booking_for_response( self::get_booking_row( $booking_id ) );
		$confirmation     = Hex_Bookings_Documents::maybe_send_booking_confirmation( $response_booking );
		$manager_note     = Hex_Bookings_Documents::build_manager_notification( $response_booking );
		$google_sheet     = Hex_Bookings_Google_Sheets::sync_booking_change( null, $response_booking );

		return rest_ensure_response(
			array(
				'message' => __( 'Booking created.', 'hvar-bookings' ),
				'booking' => $response_booking,
				'event'   => self::map_booking_to_event( $response_booking ),
				'confirmation' => $confirmation,
				'manager_notification' => $manager_note,
				'google_sheet' => $google_sheet,
			)
		);
	}

	public function update_booking( WP_REST_Request $request ) {
		global $wpdb;

		$booking_id = (int) $request['id'];
		$existing   = self::get_booking_row( $booking_id );

		if ( ! $existing ) {
			return new WP_Error( 'hex_booking_not_found', __( 'Booking not found.', 'hvar-bookings' ), array( 'status' => 404 ) );
		}

		$normalized = $this->normalize_payload( $request, $booking_id, $existing );
		if ( is_wp_error( $normalized ) ) {
			return $normalized;
		}

		$table  = $wpdb->prefix . 'hex_bookings';
		$result = $wpdb->update( $table, $normalized, array( 'id' => $booking_id ) );

		if ( false === $result ) {
			return new WP_Error( 'hex_booking_update_failed', __( 'Failed to update booking.', 'hvar-bookings' ), array( 'status' => 500 ) );
		}

		$updated = self::get_booking_row( $booking_id );
		self::insert_audit_entry( $booking_id, 'updated', $existing, $updated );
		$response_booking = self::prepare_booking_for_response( $updated );
		$confirmation     = Hex_Bookings_Documents::maybe_send_booking_confirmation( $response_booking );
		$manager_note     = Hex_Bookings_Documents::build_manager_notification( $response_booking );
		$google_sheet     = Hex_Bookings_Google_Sheets::sync_booking_change( $existing, $response_booking );

		return rest_ensure_response(
			array(
				'message' => __( 'Booking updated.', 'hvar-bookings' ),
				'booking' => $response_booking,
				'event'   => self::map_booking_to_event( $response_booking ),
				'confirmation' => $confirmation,
				'manager_notification' => $manager_note,
				'google_sheet' => $google_sheet,
			)
		);
	}

	public function delete_booking( WP_REST_Request $request ) {
		global $wpdb;

		$booking_id = (int) $request['id'];
		$existing   = self::get_booking_row( $booking_id );

		if ( ! $existing ) {
			return new WP_Error( 'hex_booking_not_found', __( 'Booking not found.', 'hvar-bookings' ), array( 'status' => 404 ) );
		}

		$table  = $wpdb->prefix . 'hex_bookings';
		$result = $wpdb->update(
			$table,
			array(
				'status'     => 'cancelled',
				'deleted_at' => null,
			),
			array( 'id' => $booking_id )
		);

		if ( false === $result ) {
			return new WP_Error( 'hex_booking_delete_failed', __( 'Failed to delete booking.', 'hvar-bookings' ), array( 'status' => 500 ) );
		}

		$updated = self::get_booking_row( $booking_id );
		self::insert_audit_entry( $booking_id, 'cancelled', $existing, $updated );
		$response_booking = self::prepare_booking_for_response( $updated );
		$google_sheet     = Hex_Bookings_Google_Sheets::sync_booking_change( $existing, $response_booking );

		return rest_ensure_response(
			array(
				'message' => __( 'Booking cancelled.', 'hvar-bookings' ),
				'booking' => $response_booking,
				'google_sheet' => $google_sheet,
			)
		);
	}

	public function send_confirmation( WP_REST_Request $request ) {
		$booking = self::prepare_booking_for_response( self::get_booking_row( (int) $request['id'] ) );

		if ( ! $booking ) {
			return new WP_Error( 'hex_booking_not_found', __( 'Booking not found.', 'hvar-bookings' ), array( 'status' => 404 ) );
		}

		if ( empty( $booking['generate_confirmation'] ) ) {
			return new WP_Error( 'hex_confirmation_disabled', __( 'Generate Booking Confirmation must be checked before sending.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$result = Hex_Bookings_Documents::maybe_send_booking_confirmation( $booking );

		return rest_ensure_response(
			array(
				'confirmation' => $result,
				'booking'      => $booking,
			)
		);
	}

	protected function normalize_payload( WP_REST_Request $request, $exclude_id = 0, $existing = null ) {
		$resource_id = (int) $request->get_param( 'resource_id' );
		if ( $resource_id <= 0 && $existing ) {
			$resource_id = (int) $existing['resource_id'];
		}

		$resource = self::get_resource_row( $resource_id );
		if ( ! $resource ) {
			return new WP_Error( 'hex_invalid_resource', __( 'Valid resource is required.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$booking_date = sanitize_text_field( (string) $request->get_param( 'booking_date' ) );
		if ( empty( $booking_date ) && $existing ) {
			$booking_date = $existing['booking_date'];
		}

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $booking_date ) ) {
			return new WP_Error( 'hex_invalid_booking_date', __( 'Booking date must be in YYYY-MM-DD format.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$is_all_day = $request->has_param( 'is_all_day' ) ? rest_sanitize_boolean( $request->get_param( 'is_all_day' ) ) : ( $existing ? (bool) $existing['is_all_day'] : false );

		$service_type = sanitize_key( (string) $request->get_param( 'service_type' ) );
		if ( empty( $service_type ) ) {
			$service_type = $existing ? $existing['service_type'] : ( $resource['default_service_type'] ?: 'rental' );
		}

		$allowed_service_types = array( 'rental', 'transfer', 'excursion', 'taxi' );
		if ( ! in_array( $service_type, $allowed_service_types, true ) ) {
			return new WP_Error( 'hex_invalid_service_type', __( 'Service type is invalid.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$skipper_mode = sanitize_key( (string) $request->get_param( 'skipper_mode' ) );
		if ( empty( $skipper_mode ) ) {
			$skipper_mode = $existing ? $existing['skipper_mode'] : 'with_skipper';
		}

		if ( in_array( $service_type, array( 'transfer', 'taxi' ), true ) ) {
			$skipper_mode = 'with_skipper';
		}

		$allowed_skipper_modes = array( 'with_skipper', 'without_skipper' );
		if ( ! in_array( $skipper_mode, $allowed_skipper_modes, true ) ) {
			return new WP_Error( 'hex_invalid_skipper_mode', __( 'Skipper mode is invalid.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		if ( 'without_skipper' === $skipper_mode && empty( $resource['supports_bareboat'] ) ) {
			return new WP_Error( 'hex_bareboat_not_allowed', __( 'This boat cannot be booked without skipper.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$status = sanitize_key( (string) $request->get_param( 'status' ) );
		if ( empty( $status ) ) {
			$status = $existing ? $existing['status'] : 'draft';
		}

		$allowed_statuses = array( 'draft', 'confirmed', 'blocked', 'cancelled' );
		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			return new WP_Error( 'hex_invalid_status', __( 'Status is invalid.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$start_time = sanitize_text_field( (string) $request->get_param( 'start_time' ) );
		$end_time   = sanitize_text_field( (string) $request->get_param( 'end_time' ) );

		if ( empty( $start_time ) && $existing ) {
			$start_time = (string) $existing['start_time'];
		}

		if ( empty( $end_time ) && $existing ) {
			$end_time = (string) $existing['end_time'];
		}

		if ( $is_all_day ) {
			$start_time = null;
			$end_time   = null;
		} else {
			if ( ! self::is_valid_time_value( $start_time ) || ! self::is_valid_time_value( $end_time ) ) {
				return new WP_Error( 'hex_invalid_time', __( 'Timed bookings require valid start_time and end_time in HH:MM or HH:MM:SS format.', 'hvar-bookings' ), array( 'status' => 400 ) );
			}

			$start_time = self::normalize_time_value( $start_time );
			$end_time   = self::normalize_time_value( $end_time );

			if ( strtotime( $end_time ) <= strtotime( $start_time ) ) {
				return new WP_Error( 'hex_invalid_time_range', __( 'End time must be later than start time.', 'hvar-bookings' ), array( 'status' => 400 ) );
			}
		}

		$assigned_booker_id = get_current_user_id();
		if ( current_user_can( 'manage_hex_bookings' ) && $request->has_param( 'book_as_user_id' ) ) {
			$requested_booker_id = max( 0, (int) $request->get_param( 'book_as_user_id' ) );
			if ( $requested_booker_id > 0 ) {
				$assigned_booker = get_userdata( $requested_booker_id );
				if ( ! $assigned_booker ) {
					return new WP_Error( 'hex_invalid_booker', __( 'Selected booker does not exist.', 'hvar-bookings' ), array( 'status' => 400 ) );
				}
				$assigned_booker_id = $requested_booker_id;
			}
		} elseif ( $existing && ! empty( $existing['booker_user_id'] ) ) {
			$assigned_booker_id = (int) $existing['booker_user_id'];
		}

		$payload = array(
			'resource_id'       => $resource_id,
			'booking_date'      => $booking_date,
			'start_time'        => $start_time,
			'end_time'          => $end_time,
			'is_all_day'        => $is_all_day ? 1 : 0,
			'status'            => $status,
			'service_type'      => $service_type,
			'skipper_mode'      => $skipper_mode,
			'customer_name'     => self::request_value( $request, 'customer_name', $existing, 'sanitize_text_field' ),
			'customer_phone'    => self::request_value( $request, 'customer_phone', $existing, 'sanitize_text_field' ),
			'customer_email'    => self::request_value( $request, 'customer_email', $existing, 'sanitize_email' ),
			'booking_price'     => self::request_decimal_value( $request, 'booking_price', $existing ),
			'advance_amount'    => self::request_decimal_value( $request, 'advance_amount', $existing ),
			'sales_channel'     => self::request_sales_channel_value( $request, $existing ),
			'extra_equipment'   => wp_json_encode( self::request_list_value( $request, 'extra_equipment', $existing ) ),
			'fuel_included'     => self::request_bool_value( $request, 'fuel_included', $existing ),
			'generate_confirmation' => self::request_bool_value( $request, 'generate_confirmation', $existing ),
			'generate_manager_notification' => self::request_bool_value( $request, 'generate_manager_notification', $existing ),
			'luggage_details'   => self::request_value( $request, 'luggage_details', $existing, 'sanitize_text_field' ),
			'pickup_coordinates'=> self::request_value( $request, 'pickup_coordinates', $existing, array( __CLASS__, 'sanitize_coordinates' ) ),
			'dropoff_coordinates'=> self::request_value( $request, 'dropoff_coordinates', $existing, array( __CLASS__, 'sanitize_coordinates' ) ),
			'passengers'        => self::request_int_value( $request, 'passengers', $existing ),
			'pickup_location'   => self::request_value( $request, 'pickup_location', $existing, 'sanitize_text_field' ),
			'dropoff_location'  => self::request_value( $request, 'dropoff_location', $existing, 'sanitize_text_field' ),
			'route_summary'     => self::request_value( $request, 'route_summary', $existing, 'sanitize_text_field' ),
			'notes'             => self::request_value( $request, 'notes', $existing, 'wp_kses_post' ),
			'internal_notes'    => self::request_value( $request, 'internal_notes', $existing, 'wp_kses_post' ),
			'booker_user_id'    => $assigned_booker_id,
			'booker_initials'   => self::get_user_initials( $assigned_booker_id ),
			'source'            => self::request_source_value( $request, $existing ),
			'external_ref'      => self::request_value( $request, 'external_ref', $existing, 'sanitize_text_field' ),
			'updated_at'        => current_time( 'mysql', true ),
		);

		if ( ! $existing ) {
			$payload['created_at'] = current_time( 'mysql', true );
		}

		if ( null === $payload['booking_price'] ) {
			return new WP_Error( 'hex_booking_price_required', __( 'Booked Price is required.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		if ( null === $payload['advance_amount'] ) {
			return new WP_Error( 'hex_advance_amount_required', __( 'Advance Charged is required.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		if ( (float) $payload['booking_price'] < 0 || (float) $payload['advance_amount'] < 0 ) {
			return new WP_Error( 'hex_booking_money_invalid', __( 'Booked Price and Advance Charged cannot be negative.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		if ( ! empty( $payload['generate_confirmation'] ) && empty( $payload['customer_email'] ) ) {
			return new WP_Error( 'hex_confirmation_email_missing', __( 'Customer E-mail is required when Generate Booking Confirmation is checked.', 'hvar-bookings' ), array( 'status' => 400 ) );
		}

		$conflict = self::find_conflict( $payload, $exclude_id );
		if ( $conflict ) {
			return new WP_Error(
				'hex_booking_conflict',
				sprintf(
					/* translators: %d: conflicting booking ID. */
					__( 'This booking conflicts with booking #%d on the same boat and time range.', 'hvar-bookings' ),
					(int) $conflict['id']
				),
				array(
					'status'   => 409,
					'conflict' => self::map_booking_to_event( $conflict ),
				)
			);
		}

		return $payload;
	}

	protected static function get_resource_row( $resource_id ) {
		global $wpdb;

		if ( $resource_id <= 0 ) {
			return null;
		}

		$table = $wpdb->prefix . 'hex_resources';
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", $resource_id ),
			ARRAY_A
		);
	}

	protected static function get_booking_row( $booking_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_bookings';
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", $booking_id ),
			ARRAY_A
		);
	}

	protected static function insert_audit_entry( $booking_id, $action, $before, $after ) {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_booking_audit';

		$wpdb->insert(
			$table,
			array(
				'booking_id'         => $booking_id,
				'action'             => $action,
				'changed_by_user_id' => get_current_user_id(),
				'change_summary'     => sprintf( 'Booking %s by user %d', $action, get_current_user_id() ),
				'before_json'        => null === $before ? null : wp_json_encode( $before ),
				'after_json'         => null === $after ? null : wp_json_encode( $after ),
				'created_at'         => current_time( 'mysql', true ),
			)
		);
	}

	protected static function find_conflict( $payload, $exclude_id = 0 ) {
		global $wpdb;

		if ( 'cancelled' === $payload['status'] ) {
			return null;
		}

		$table          = $wpdb->prefix . 'hex_bookings';
		$active_status  = array( 'draft', 'confirmed', 'blocked' );
		$start_compare  = ! empty( $payload['start_time'] ) ? $payload['start_time'] : '00:00:00';
		$end_compare    = ! empty( $payload['end_time'] ) ? $payload['end_time'] : '23:59:59';
		$status_placeholders = "'" . implode( "','", array_map( 'esc_sql', $active_status ) ) . "'";

		$sql = $wpdb->prepare(
			"SELECT *
			 FROM {$table}
			 WHERE deleted_at IS NULL
			   AND resource_id = %d
			   AND booking_date = %s
			   AND status IN ({$status_placeholders})
			   AND id <> %d
			   AND (
				 (COALESCE(start_time, '00:00:00') < %s)
				 AND
				 (COALESCE(end_time, '23:59:59') > %s)
			   )
			 ORDER BY id ASC
			 LIMIT 1",
			(int) $payload['resource_id'],
			$payload['booking_date'],
			(int) $exclude_id,
			$end_compare,
			$start_compare
		);

		return $wpdb->get_row( $sql, ARRAY_A );
	}

	protected static function get_user_initials( $user_id ) {
		$user_id  = (int) $user_id;
		$initials = (string) get_user_meta( $user_id, 'hex_booker_initials', true );

		if ( ! empty( $initials ) ) {
			return strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $initials ), 0, 10 ) );
		}

		$user = get_userdata( $user_id );
		$name = trim( (string) $user->display_name );
		if ( '' === $name ) {
			return 'BK';
		}

		$parts = preg_split( '/\s+/', $name );
		$abbr  = '';
		foreach ( $parts as $part ) {
			$abbr .= strtoupper( substr( $part, 0, 1 ) );
			if ( strlen( $abbr ) >= 3 ) {
				break;
			}
		}

		return $abbr ?: 'BK';
	}

	protected static function get_current_user_initials() {
		return self::get_user_initials( get_current_user_id() );
	}

	protected static function get_booker_options() {
		$users = get_users(
			array(
				'role__in' => array( 'administrator', 'booking_manager', 'booker' ),
				'orderby'  => 'display_name',
				'order'    => 'ASC',
				'fields'   => array( 'ID', 'display_name', 'user_email' ),
			)
		);

		$options = array();

		foreach ( $users as $user ) {
			$options[] = array(
				'id'           => (int) $user->ID,
				'display_name' => (string) $user->display_name,
				'email'        => (string) $user->user_email,
				'initials'     => (string) get_user_meta( $user->ID, 'hex_booker_initials', true ),
				'phone'        => (string) get_user_meta( $user->ID, 'hex_booker_phone', true ),
			);
		}

		return $options;
	}

	protected static function get_saved_filters() {
		$filters = get_user_meta( get_current_user_id(), 'hex_bookings_filters', true );
		return self::sanitize_saved_filters( $filters );
	}

	protected static function sanitize_saved_filters( $filters ) {
		$filters = is_array( $filters ) ? $filters : array();

		return array(
			'category'       => sanitize_key( (string) ( $filters['category'] ?? '' ) ),
			'resource_id'    => max( 0, (int) ( $filters['resource_id'] ?? 0 ) ),
			'booker_user_id' => max( 0, (int) ( $filters['booker_user_id'] ?? 0 ) ),
			'service_type'   => sanitize_key( (string) ( $filters['service_type'] ?? '' ) ),
			'status'         => sanitize_key( (string) ( $filters['status'] ?? '' ) ),
			'only_mine'      => ! empty( $filters['only_mine'] ),
		);
	}

	protected static function request_value( WP_REST_Request $request, $key, $existing, $sanitizer ) {
		if ( $request->has_param( $key ) ) {
			return call_user_func( $sanitizer, (string) $request->get_param( $key ) );
		}

		return $existing[ $key ] ?? '';
	}

	protected static function request_int_value( WP_REST_Request $request, $key, $existing ) {
		if ( $request->has_param( $key ) ) {
			return max( 0, (int) $request->get_param( $key ) );
		}

		return isset( $existing[ $key ] ) ? (int) $existing[ $key ] : 0;
	}

	protected static function request_source_value( WP_REST_Request $request, $existing ) {
		if ( $request->has_param( 'source' ) ) {
			return sanitize_key( (string) $request->get_param( 'source' ) );
		}

		return $existing['source'] ?? 'wp_internal';
	}

	protected static function request_bool_value( WP_REST_Request $request, $key, $existing ) {
		if ( $request->has_param( $key ) ) {
			return rest_sanitize_boolean( $request->get_param( $key ) ) ? 1 : 0;
		}

		return ! empty( $existing[ $key ] ) ? 1 : 0;
	}

	protected static function request_decimal_value( WP_REST_Request $request, $key, $existing ) {
		if ( $request->has_param( $key ) ) {
			$raw = str_replace( ',', '.', trim( (string) $request->get_param( $key ) ) );
			if ( '' === $raw ) {
				return null;
			}

			return number_format( (float) $raw, 2, '.', '' );
		}

		if ( isset( $existing[ $key ] ) && '' !== (string) $existing[ $key ] ) {
			return number_format( (float) $existing[ $key ], 2, '.', '' );
		}

		return null;
	}

	protected static function request_sales_channel_value( WP_REST_Request $request, $existing ) {
		$allowed = wp_list_pluck( Hex_Bookings_Plugin::get_sales_channels(), 'value' );
		$value   = $request->has_param( 'sales_channel' )
			? sanitize_key( (string) $request->get_param( 'sales_channel' ) )
			: ( $existing['sales_channel'] ?? 'cash' );

		if ( ! in_array( $value, $allowed, true ) ) {
			$value = 'cash';
		}

		return $value;
	}

	protected static function request_list_value( WP_REST_Request $request, $key, $existing ) {
		if ( $request->has_param( $key ) ) {
			$raw = $request->get_param( $key );
			$items = is_array( $raw ) ? $raw : array( $raw );

			return array_values(
				array_filter(
					array_map(
						static function ( $item ) {
							return sanitize_key( (string) $item );
						},
						$items
					)
				)
			);
		}

		return self::decode_json_list( $existing[ $key ] ?? '' );
	}

	protected static function is_valid_time_value( $value ) {
		return 1 === preg_match( '/^\d{2}:\d{2}(:\d{2})?$/', (string) $value );
	}

	protected static function normalize_time_value( $value ) {
		$value = (string) $value;
		return 5 === strlen( $value ) ? "{$value}:00" : $value;
	}

	protected static function decode_json_list( $value ) {
		if ( is_array( $value ) ) {
			return array_values( array_filter( array_map( 'sanitize_key', $value ) ) );
		}

		$decoded = json_decode( (string) $value, true );
		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'sanitize_key', $decoded ) ) );
	}

	protected static function sanitize_coordinates( $value ) {
		return trim( preg_replace( '/[^0-9,\.\-\s]/', '', (string) $value ) );
	}

	protected static function prepare_booking_for_response( $booking ) {
		if ( empty( $booking ) || ! is_array( $booking ) ) {
			return $booking;
		}

		$booking['extra_equipment'] = self::decode_json_list( $booking['extra_equipment'] ?? '' );
		$booking['booking_price']   = isset( $booking['booking_price'] ) && '' !== (string) $booking['booking_price'] ? (float) $booking['booking_price'] : null;
		$booking['advance_amount']  = isset( $booking['advance_amount'] ) && '' !== (string) $booking['advance_amount'] ? (float) $booking['advance_amount'] : null;
		$booking['sales_channel']   = (string) ( $booking['sales_channel'] ?? 'cash' );
		$booking['luggage_details'] = (string) ( $booking['luggage_details'] ?? '' );
		$booking['pickup_coordinates'] = (string) ( $booking['pickup_coordinates'] ?? '' );
		$booking['dropoff_coordinates'] = (string) ( $booking['dropoff_coordinates'] ?? '' );
		$booking['fuel_included'] = ! empty( $booking['fuel_included'] );
		$booking['generate_confirmation'] = ! empty( $booking['generate_confirmation'] );
		$booking['generate_manager_notification'] = ! empty( $booking['generate_manager_notification'] );

		return $booking;
	}

	public static function map_booking_to_event( $booking ) {
		$booking_date = $booking['booking_date'];
		$start        = ! empty( $booking['start_time'] ) ? "{$booking_date}T{$booking['start_time']}" : "{$booking_date}T00:00:00";
		$end          = ! empty( $booking['end_time'] ) ? "{$booking_date}T{$booking['end_time']}" : "{$booking_date}T23:59:59";
		$color        = Hex_Bookings_Plugin::resolve_event_style( $booking['status'], $booking['service_type'], $booking['skipper_mode'] );
		$badge        = Hex_Bookings_Plugin::resolve_event_badge( $booking['service_type'], $booking['skipper_mode'] );
		$title_parts  = array_filter(
			array(
				$booking['customer_name'],
				$booking['route_summary'],
			)
		);
		$title        = implode( ' | ', array_slice( $title_parts, 0, 2 ) );

		if ( '' === $title ) {
			$title = sprintf(
				/* translators: %d: booking ID. */
				__( 'Booking #%d', 'hvar-bookings' ),
				(int) $booking['id']
			);
		}

		return array(
			'id'              => (int) $booking['id'],
			'resourceId'      => (int) $booking['resource_id'],
			'title'           => $title,
			'start'           => $start,
			'end'             => $end,
			'allDay'          => (bool) $booking['is_all_day'],
			'backgroundColor' => $color['background'],
			'borderColor'     => $color['border'],
			'textColor'       => $color['text'],
			'classNames'      => $color['classes'],
			'extendedProps'   => array(
				'status'          => $booking['status'],
				'service_type'    => $booking['service_type'],
				'skipper_mode'    => $booking['skipper_mode'],
				'booker_initials' => $booking['booker_initials'],
				'booker_user_id'  => (int) $booking['booker_user_id'],
				'customer_name'   => $booking['customer_name'],
				'customer_phone'  => $booking['customer_phone'],
				'customer_email'  => $booking['customer_email'],
				'route_summary'   => $booking['route_summary'],
				'booking_price'   => $booking['booking_price'],
				'advance_amount'  => $booking['advance_amount'],
				'sales_channel'   => $booking['sales_channel'],
				'extra_equipment' => $booking['extra_equipment'],
				'fuel_included'   => $booking['fuel_included'],
				'generate_confirmation' => $booking['generate_confirmation'],
				'generate_manager_notification' => $booking['generate_manager_notification'],
				'luggage_details' => $booking['luggage_details'],
				'pickup_location' => $booking['pickup_location'],
				'pickup_coordinates' => $booking['pickup_coordinates'],
				'dropoff_location'=> $booking['dropoff_location'],
				'dropoff_coordinates' => $booking['dropoff_coordinates'],
				'badge'           => $badge,
			),
		);
	}
}

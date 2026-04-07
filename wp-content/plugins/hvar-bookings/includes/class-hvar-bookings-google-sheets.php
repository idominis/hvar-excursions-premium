<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_Google_Sheets {

	const DEFAULT_SPREADSHEET_ID = '1jTg2_rezN_KBKzUCssU04-1M3zkoiSGslGRJGFHNKNA';
	const DEFAULT_SHEET_ID       = 1920341166;
	const FIRST_RESOURCE_COLUMN = 2; // C.
	const LAST_RESOURCE_COLUMN  = 20; // U.
	const FIRST_DATE_ROW        = 1; // Row 2.
	const LAST_DATE_ROW         = 189; // Row 190.

	public static function sync_booking_change( $before, $after ) {
		if ( ! self::is_configured() ) {
			self::record_export_status( self::booking_id( $after ?: $before ), 'disabled', '', 'Google Sheets service account is not configured.' );
			return array(
				'status'  => 'disabled',
				'message' => 'Google Sheets service account is not configured.',
			);
		}

		$meta = self::get_grid_meta();
		if ( is_wp_error( $meta ) ) {
			self::record_export_status( self::booking_id( $after ?: $before ), 'failed', '', $meta->get_error_message() );
			return array(
				'status'  => 'failed',
				'message' => $meta->get_error_message(),
			);
		}

		$targets = array();
		$before_target = self::booking_target( $before, $meta );
		$after_target  = self::booking_target( $after, $meta );

		if ( $before_target ) {
			$targets[ $before_target['key'] ] = $before_target;
		}

		if ( $after_target ) {
			$targets[ $after_target['key'] ] = $after_target;
		}

		if ( empty( $targets ) ) {
			self::record_export_status( self::booking_id( $after ?: $before ), 'skipped', '', 'Booking does not map to a configured sheet cell.' );
			return array(
				'status'  => 'skipped',
				'message' => 'Booking does not map to a configured sheet cell.',
			);
		}

		$requests = array();
		foreach ( $targets as $target ) {
			$cell_state = self::cell_state_from_bookings( (int) $target['resource_id'], (string) $target['booking_date'] );
			$requests[] = self::cell_update_request( (int) $target['row_index'], (int) $target['column_index'], $cell_state );
		}

		$result = self::send_batch_update( $requests );
		if ( is_wp_error( $result ) ) {
			self::record_export_status( self::booking_id( $after ?: $before ), 'failed', implode( ',', array_keys( $targets ) ), $result->get_error_message() );
			return array(
				'status'  => 'failed',
				'message' => $result->get_error_message(),
			);
		}

		self::record_export_status( self::booking_id( $after ?: $before ), 'synced', implode( ',', array_keys( $targets ) ) );
		return array(
			'status'  => 'synced',
			'message' => 'Google Sheet updated.',
		);
	}

	protected static function is_configured() {
		$credentials_path = self::credentials_path();
		return '' !== $credentials_path && file_exists( $credentials_path );
	}

	protected static function credentials_path() {
		if ( defined( 'HEX_BOOKINGS_GOOGLE_SERVICE_ACCOUNT_JSON' ) ) {
			return (string) HEX_BOOKINGS_GOOGLE_SERVICE_ACCOUNT_JSON;
		}

		return 'C:/laragon/usr/hvar-bookings-google-service-account.json';
	}

	protected static function spreadsheet_id() {
		return defined( 'HEX_BOOKINGS_GOOGLE_SHEETS_SPREADSHEET_ID' )
			? (string) HEX_BOOKINGS_GOOGLE_SHEETS_SPREADSHEET_ID
			: self::DEFAULT_SPREADSHEET_ID;
	}

	protected static function sheet_id() {
		return defined( 'HEX_BOOKINGS_GOOGLE_SHEETS_SHEET_ID' )
			? (int) HEX_BOOKINGS_GOOGLE_SHEETS_SHEET_ID
			: self::DEFAULT_SHEET_ID;
	}

	protected static function get_grid_meta() {
		$cache_key = 'hex_google_sheets_grid_meta_' . md5( self::spreadsheet_id() . ':' . self::sheet_id() );
		$cached    = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$token = self::get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$url = add_query_arg(
			array(
				'includeGridData' => 'true',
				'fields'          => 'sheets(properties(sheetId,title),data(startRow,startColumn,rowData(values(formattedValue,effectiveValue))))',
			),
			'https://sheets.googleapis.com/v4/spreadsheets/' . rawurlencode( self::spreadsheet_id() )
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 20,
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'hex_google_sheets_grid_failed', self::api_error_message( $body, 'Could not read Google Sheet structure.' ) );
		}

		$meta = self::parse_grid_meta( $body );
		if ( is_wp_error( $meta ) ) {
			return $meta;
		}

		set_transient( $cache_key, $meta, MINUTE_IN_SECONDS );
		return $meta;
	}

	protected static function parse_grid_meta( $body ) {
		if ( empty( $body['sheets'] ) || ! is_array( $body['sheets'] ) ) {
			return new WP_Error( 'hex_google_sheets_missing_sheets', 'Google Sheet metadata did not include sheets.' );
		}

		$target_sheet = null;
		foreach ( $body['sheets'] as $sheet ) {
			if ( (int) ( $sheet['properties']['sheetId'] ?? 0 ) === self::sheet_id() ) {
				$target_sheet = $sheet;
				break;
			}
		}

		if ( ! $target_sheet ) {
			return new WP_Error( 'hex_google_sheets_missing_sheet', 'Configured sheet tab was not found.' );
		}

		$resource_columns = array();
		$date_rows        = array();
		$date_rows_by_md  = array();
		$row_data         = $target_sheet['data'][0]['rowData'] ?? array();
		$start_row        = (int) ( $target_sheet['data'][0]['startRow'] ?? 0 );

		foreach ( $row_data as $row_offset => $row ) {
			$row_index = $start_row + (int) $row_offset;
			$values    = $row['values'] ?? array();

			if ( 0 === $row_index ) {
				foreach ( $values as $column_index => $cell ) {
					if ( $column_index < self::FIRST_RESOURCE_COLUMN || $column_index > self::LAST_RESOURCE_COLUMN ) {
						continue;
					}

					$name = self::cell_string_value( $cell );
					if ( '' !== $name ) {
						$resource_columns[ self::normalize_key( $name ) ] = array(
							'name'         => $name,
							'column_index' => (int) $column_index,
						);
					}
				}
				continue;
			}

			if ( $row_index < self::FIRST_DATE_ROW || $row_index > self::LAST_DATE_ROW ) {
				continue;
			}

			$date = self::cell_date_value( $values[0] ?? array() );
			if ( '' !== $date ) {
				$date_rows[ $date ] = (int) $row_index;
				$date_rows_by_md[ substr( $date, 5 ) ] = (int) $row_index;
			}
		}

		return array(
			'sheet_title'       => (string) ( $target_sheet['properties']['title'] ?? '' ),
			'resource_columns'  => $resource_columns,
			'date_rows'         => $date_rows,
			'date_rows_by_md'   => $date_rows_by_md,
		);
	}

	protected static function booking_target( $booking, $meta ) {
		if ( empty( $booking ) || ! is_array( $booking ) ) {
			return null;
		}

		$resource_id  = (int) ( $booking['resource_id'] ?? 0 );
		$booking_date = (string) ( $booking['booking_date'] ?? '' );

		if ( $resource_id <= 0 || '' === $booking_date ) {
			return null;
		}

		$resource_name = self::resource_name( $resource_id );
		if ( '' === $resource_name ) {
			return null;
		}

		$resource_key = self::normalize_key( $resource_name );
		$row_index = (int) ( $meta['date_rows'][ $booking_date ] ?? 0 );
		if ( $row_index <= 0 && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $booking_date ) ) {
			$row_index = (int) ( $meta['date_rows_by_md'][ substr( $booking_date, 5 ) ] ?? 0 );
		}

		if ( empty( $meta['resource_columns'][ $resource_key ] ) || $row_index <= 0 ) {
			return null;
		}

		return array(
			'key'          => $booking_date . '|' . $resource_id,
			'resource_id'  => $resource_id,
			'booking_date' => $booking_date,
			'row_index'    => $row_index,
			'column_index' => (int) $meta['resource_columns'][ $resource_key ]['column_index'],
		);
	}

	protected static function cell_state_from_bookings( $resource_id, $booking_date ) {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_bookings';
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT booker_initials, skipper_mode
				 FROM {$table}
				 WHERE deleted_at IS NULL
				   AND status = %s
				   AND resource_id = %d
				   AND booking_date = %s
				 ORDER BY COALESCE(start_time, '00:00:00') ASC, id ASC",
				'confirmed',
				$resource_id,
				$booking_date
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return array(
				'value' => '',
				'color' => 'clear',
			);
		}

		$initials = array();
		$has_bareboat = false;
		foreach ( $rows as $row ) {
			$initial = strtoupper( trim( (string) ( $row['booker_initials'] ?? '' ) ) );
			if ( '' !== $initial && ! in_array( $initial, $initials, true ) ) {
				$initials[] = $initial;
			}

			if ( 'without_skipper' === (string) ( $row['skipper_mode'] ?? '' ) ) {
				$has_bareboat = true;
			}
		}

		return array(
			'value' => implode( '/', $initials ),
			'color' => $has_bareboat ? 'red' : 'blue',
		);
	}

	protected static function cell_update_request( $row_index, $column_index, $cell_state ) {
		$value = (string) ( $cell_state['value'] ?? '' );
		$color = self::color_for_cell_state( (string) ( $cell_state['color'] ?? 'clear' ) );

		return array(
			'updateCells' => array(
				'rows'   => array(
					array(
						'values' => array(
							array(
								'userEnteredValue'  => array(
									'stringValue' => $value,
								),
								'userEnteredFormat' => array(
									'backgroundColor'     => $color['background'],
									'textFormat'          => array(
										'foregroundColor' => $color['text'],
										'bold'            => '' !== $value,
									),
									'horizontalAlignment' => 'CENTER',
									'verticalAlignment'   => 'MIDDLE',
								),
							),
						),
					),
				),
				'fields' => 'userEnteredValue,userEnteredFormat(backgroundColor,textFormat,horizontalAlignment,verticalAlignment)',
				'start'  => array(
					'sheetId'     => self::sheet_id(),
					'rowIndex'    => $row_index,
					'columnIndex' => $column_index,
				),
			),
		);
	}

	protected static function color_for_cell_state( $state ) {
		if ( 'red' === $state ) {
			return array(
				'background' => array( 'red' => 0.839, 'green' => 0.188, 'blue' => 0.188 ),
				'text'       => array( 'red' => 1, 'green' => 1, 'blue' => 1 ),
			);
		}

		if ( 'blue' === $state ) {
			return array(
				'background' => array( 'red' => 0.122, 'green' => 0.388, 'blue' => 0.922 ),
				'text'       => array( 'red' => 1, 'green' => 1, 'blue' => 1 ),
			);
		}

		return array(
			'background' => array( 'green' => 1 ),
			'text'       => array( 'red' => 1, 'green' => 1, 'blue' => 1 ),
		);
	}

	protected static function send_batch_update( $requests ) {
		if ( empty( $requests ) ) {
			return true;
		}

		$token = self::get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_post(
			'https://sheets.googleapis.com/v4/spreadsheets/' . rawurlencode( self::spreadsheet_id() ) . ':batchUpdate',
			array(
				'timeout' => 20,
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json; charset=utf-8',
				),
				'body'    => wp_json_encode(
					array(
						'requests' => array_values( $requests ),
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'hex_google_sheets_update_failed', self::api_error_message( $body, 'Could not update Google Sheet.' ) );
		}

		return true;
	}

	protected static function get_access_token() {
		$cached = get_transient( 'hex_google_sheets_access_token' );
		if ( is_string( $cached ) && '' !== $cached ) {
			return $cached;
		}

		$credentials_path = self::credentials_path();
		$credentials      = json_decode( (string) file_get_contents( $credentials_path ), true );

		if ( empty( $credentials['client_email'] ) || empty( $credentials['private_key'] ) ) {
			return new WP_Error( 'hex_google_sheets_bad_credentials', 'Google Sheets service account JSON is missing client_email or private_key.' );
		}

		if ( ! function_exists( 'openssl_sign' ) ) {
			return new WP_Error( 'hex_google_sheets_openssl_missing', 'PHP OpenSSL is required for Google Sheets service account authentication.' );
		}

		$now    = time();
		$header = array(
			'alg' => 'RS256',
			'typ' => 'JWT',
		);
		$claims = array(
			'iss'   => $credentials['client_email'],
			'scope' => 'https://www.googleapis.com/auth/spreadsheets',
			'aud'   => 'https://oauth2.googleapis.com/token',
			'iat'   => $now,
			'exp'   => $now + 3600,
		);

		$signing_input = self::base64_url_encode( wp_json_encode( $header ) ) . '.' . self::base64_url_encode( wp_json_encode( $claims ) );
		$signature     = '';
		$signed        = openssl_sign( $signing_input, $signature, $credentials['private_key'], 'sha256WithRSAEncryption' );

		if ( ! $signed ) {
			return new WP_Error( 'hex_google_sheets_jwt_failed', 'Could not sign Google Sheets service account JWT.' );
		}

		$jwt = $signing_input . '.' . self::base64_url_encode( $signature );

		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 20,
				'body'    => array(
					'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
					'assertion'  => $jwt,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code < 200 || $code >= 300 || empty( $body['access_token'] ) ) {
			return new WP_Error( 'hex_google_sheets_token_failed', self::api_error_message( $body, 'Could not get Google Sheets access token.' ) );
		}

		$ttl = max( 60, min( 3300, (int) ( $body['expires_in'] ?? 3600 ) - 120 ) );
		set_transient( 'hex_google_sheets_access_token', (string) $body['access_token'], $ttl );

		return (string) $body['access_token'];
	}

	protected static function record_export_status( $booking_id, $status, $row_key = '', $error = '' ) {
		global $wpdb;

		$booking_id = (int) $booking_id;
		if ( $booking_id <= 0 || empty( $wpdb ) ) {
			return;
		}

		$table = $wpdb->prefix . 'hex_google_exports';
		$now   = current_time( 'mysql', true );
		$data  = array(
			'sheet_name'    => 'gid:' . self::sheet_id(),
			'sheet_row_key' => $row_key,
			'export_status' => $status,
			'last_error'    => $error,
			'updated_at'    => $now,
		);

		if ( 'synced' === $status ) {
			$data['last_exported_at'] = $now;
		}

		$existing_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE booking_id = %d LIMIT 1",
				$booking_id
			)
		);

		if ( $existing_id > 0 ) {
			$wpdb->update( $table, $data, array( 'id' => $existing_id ) );
			return;
		}

		$data['booking_id']  = $booking_id;
		$data['created_at']  = $now;
		$wpdb->insert( $table, $data );
	}

	protected static function resource_name( $resource_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_resources';
		return (string) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT name FROM {$table} WHERE id = %d LIMIT 1",
				(int) $resource_id
			)
		);
	}

	protected static function booking_id( $booking ) {
		return is_array( $booking ) ? (int) ( $booking['id'] ?? 0 ) : 0;
	}

	protected static function cell_string_value( $cell ) {
		if ( ! empty( $cell['formattedValue'] ) ) {
			return trim( (string) $cell['formattedValue'] );
		}

		if ( ! empty( $cell['effectiveValue']['stringValue'] ) ) {
			return trim( (string) $cell['effectiveValue']['stringValue'] );
		}

		return '';
	}

	protected static function cell_date_value( $cell ) {
		if ( isset( $cell['effectiveValue']['numberValue'] ) ) {
			$serial = (float) $cell['effectiveValue']['numberValue'];
			if ( $serial > 30000 ) {
				return gmdate( 'Y-m-d', (int) round( ( $serial - 25569 ) * DAY_IN_SECONDS ) );
			}
		}

		$value = self::cell_string_value( $cell );
		if ( preg_match( '/(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $matches ) ) {
			return sprintf( '%04d-%02d-%02d', (int) $matches[1], (int) $matches[2], (int) $matches[3] );
		}

		if ( preg_match( '/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $value, $matches ) ) {
			return sprintf( '%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1] );
		}

		if ( preg_match( '/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $value, $matches ) ) {
			return sprintf( '%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1] );
		}

		return '';
	}

	protected static function normalize_key( $value ) {
		$value = strtolower( remove_accents( (string) $value ) );
		return preg_replace( '/[^a-z0-9]+/', '', $value );
	}

	protected static function base64_url_encode( $value ) {
		return rtrim( strtr( base64_encode( (string) $value ), '+/', '-_' ), '=' );
	}

	protected static function api_error_message( $body, $fallback ) {
		if ( is_array( $body ) ) {
			if ( ! empty( $body['error']['message'] ) ) {
				return (string) $body['error']['message'];
			}

			if ( ! empty( $body['error_description'] ) ) {
				return (string) $body['error_description'];
			}
		}

		return $fallback;
	}
}

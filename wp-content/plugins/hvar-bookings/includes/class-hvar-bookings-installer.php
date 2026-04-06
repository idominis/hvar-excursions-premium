<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_Installer {

	const SCHEMA_VERSION = '0.6.1';

	public static function activate() {
		self::maybe_upgrade();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}

	public static function maybe_upgrade() {
		self::create_tables();
		self::register_roles();
		self::seed_default_resources();
		update_option( 'hex_bookings_schema_version', self::SCHEMA_VERSION );
	}

	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$resources_table = $wpdb->prefix . 'hex_resources';
		$bookings_table  = $wpdb->prefix . 'hex_bookings';
		$audit_table     = $wpdb->prefix . 'hex_booking_audit';
		$exports_table   = $wpdb->prefix . 'hex_google_exports';

		$sql = array();

		$sql[] = "CREATE TABLE {$resources_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			resource_type VARCHAR(30) NOT NULL DEFAULT 'boat',
			category VARCHAR(50) NOT NULL DEFAULT 'speedboat',
			name VARCHAR(150) NOT NULL,
			slug VARCHAR(150) NOT NULL,
			capacity SMALLINT UNSIGNED NULL,
			supports_skipper TINYINT(1) NOT NULL DEFAULT 1,
			supports_bareboat TINYINT(1) NOT NULL DEFAULT 1,
			default_service_type VARCHAR(30) NULL,
			sort_order INT NOT NULL DEFAULT 0,
			color VARCHAR(20) NULL,
			is_active TINYINT(1) NOT NULL DEFAULT 1,
			notes TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY slug (slug),
			KEY type_category_active (resource_type, category, is_active, sort_order)
		) {$charset_collate};";

		$sql[] = "CREATE TABLE {$bookings_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			resource_id BIGINT UNSIGNED NOT NULL,
			booking_date DATE NOT NULL,
			start_time TIME NULL,
			end_time TIME NULL,
			is_all_day TINYINT(1) NOT NULL DEFAULT 0,
			status VARCHAR(30) NOT NULL DEFAULT 'draft',
			service_type VARCHAR(30) NOT NULL DEFAULT 'rental',
			skipper_mode VARCHAR(30) NOT NULL DEFAULT 'with_skipper',
			customer_name VARCHAR(190) NULL,
			customer_phone VARCHAR(50) NULL,
			customer_email VARCHAR(190) NULL,
			booking_price DECIMAL(10,2) NULL,
			advance_amount DECIMAL(10,2) NULL,
			sales_channel VARCHAR(50) NOT NULL DEFAULT 'cash',
			extra_equipment TEXT NULL,
			fuel_included TINYINT(1) NOT NULL DEFAULT 0,
			generate_confirmation TINYINT(1) NOT NULL DEFAULT 0,
			generate_manager_notification TINYINT(1) NOT NULL DEFAULT 0,
			luggage_details VARCHAR(190) NULL,
			pickup_coordinates VARCHAR(100) NULL,
			dropoff_coordinates VARCHAR(100) NULL,
			passengers SMALLINT UNSIGNED NULL,
			pickup_location VARCHAR(190) NULL,
			dropoff_location VARCHAR(190) NULL,
			route_summary VARCHAR(255) NULL,
			notes TEXT NULL,
			internal_notes TEXT NULL,
			booker_user_id BIGINT UNSIGNED NOT NULL,
			booker_initials VARCHAR(10) NOT NULL,
			source VARCHAR(30) NOT NULL DEFAULT 'wp_internal',
			external_ref VARCHAR(100) NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			deleted_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY resource_date_time (resource_id, booking_date, start_time, end_time),
			KEY status_date (status, booking_date),
			KEY service_date (service_type, booking_date),
			KEY booker_date (booker_user_id, booking_date)
		) {$charset_collate};";

		$sql[] = "CREATE TABLE {$audit_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_id BIGINT UNSIGNED NOT NULL,
			action VARCHAR(30) NOT NULL,
			changed_by_user_id BIGINT UNSIGNED NOT NULL,
			change_summary TEXT NULL,
			before_json LONGTEXT NULL,
			after_json LONGTEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY booking_created (booking_id, created_at)
		) {$charset_collate};";

		$sql[] = "CREATE TABLE {$exports_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_id BIGINT UNSIGNED NOT NULL,
			sheet_name VARCHAR(100) NOT NULL,
			sheet_row_key VARCHAR(100) NULL,
			export_status VARCHAR(30) NOT NULL DEFAULT 'pending',
			last_exported_at DATETIME NULL,
			last_error TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY export_status_updated (export_status, updated_at),
			KEY booking_id (booking_id)
		) {$charset_collate};";

		foreach ( $sql as $statement ) {
			dbDelta( $statement );
		}
	}

	public static function register_roles() {
		$access_caps = array(
			'read'                => true,
			'access_hex_bookings' => true,
			'create_hex_bookings' => true,
			'edit_hex_bookings'   => true,
		);

		$manager_caps = array_merge(
			$access_caps,
			array(
				'delete_hex_bookings' => true,
				'manage_hex_bookings' => true,
				'manage_hex_resources' => true,
				'export_hex_bookings' => true,
			)
		);

		add_role( 'booker', __( 'Booker', 'hvar-bookings' ), $access_caps );
		add_role( 'booking_manager', __( 'Booking Manager', 'hvar-bookings' ), $manager_caps );

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			foreach ( $manager_caps as $cap => $grant ) {
				$admin->add_cap( $cap, $grant );
			}
		}
	}

	public static function seed_default_resources() {
		global $wpdb;

		$table = $wpdb->prefix . 'hex_resources';

		foreach ( self::get_default_resources() as $resource ) {
			$existing_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM {$table} WHERE slug = %s LIMIT 1",
					$resource['slug']
				)
			);

			$data = array(
				'resource_type'         => $resource['resource_type'],
				'category'              => $resource['category'],
				'name'                  => $resource['name'],
				'slug'                  => $resource['slug'],
				'capacity'              => $resource['capacity'],
				'supports_skipper'      => $resource['supports_skipper'],
				'supports_bareboat'     => $resource['supports_bareboat'],
				'default_service_type'  => $resource['default_service_type'],
				'sort_order'            => $resource['sort_order'],
				'color'                 => $resource['color'],
				'is_active'             => 1,
				'notes'                 => $resource['notes'],
			);

			if ( $existing_id > 0 ) {
				$wpdb->update( $table, $data, array( 'id' => $existing_id ) );
			} else {
				$wpdb->insert( $table, $data );
			}
		}
	}

	public static function get_default_resources() {
		return array(
			array(
				'resource_type'        => 'boat',
				'category'             => 'luxury-speedboat',
				'name'                 => 'Raptor',
				'slug'                 => 'luxury-speedboat-alesta-raptor',
				'capacity'             => 12,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 0,
				'default_service_type' => 'transfer',
				'sort_order'           => 190,
				'color'                => '#1f6bff',
				'notes'                => 'Flagship luxury speedboat. Default to skipper-based work.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Clubman 300hp',
				'slug'                 => 'joker-clubman-26-300hp',
				'capacity'             => 14,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 180,
				'color'                => '#1f6bff',
				'notes'                => 'High-end Clubman speedboat suitable for rentals, excursions, and transfers.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Zar 1 Sivi 300hp',
				'slug'                 => 'zar-75-300hp',
				'capacity'             => 14,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 160,
				'color'                => '#1f6bff',
				'notes'                => 'First Zar 300hp boat from the current operational fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Baracuda 4 200hp',
				'slug'                 => 'solemar-200hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 140,
				'color'                => '#1f6bff',
				'notes'                => 'Current 200hp Baracuda resource used in the 2026 fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'BSC 175 hp',
				'slug'                 => 'bsc-175hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 100,
				'color'                => '#1f6bff',
				'notes'                => 'Newer RIB, broad lounging deck, fun all-rounder.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Baracuda Black 175hp',
				'slug'                 => 'solemar-175hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 110,
				'color'                => '#1f6bff',
				'notes'                => 'Current 175hp Baracuda Black resource used in the 2026 fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Scar 1 Sivi 150hp',
				'slug'                 => 'scar-next-150hp',
				'capacity'             => 8,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 80,
				'color'                => '#1f6bff',
				'notes'                => 'First SCAR 150hp resource from the current operational fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Quicksilver 100hp',
				'slug'                 => 'quicksilver-100hp',
				'capacity'             => 7,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 70,
				'color'                => '#1f6bff',
				'notes'                => 'Practical speedboat for couples and small groups.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'boat',
				'name'                 => 'Betina 30hp',
				'slug'                 => 'boat-nautica-500-30hp',
				'capacity'             => 5,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 30,
				'color'                => '#d63131',
				'notes'                => 'Current 30hp self-drive boat in the 2026 fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'boat',
				'name'                 => 'Pirka 20hp',
				'slug'                 => 'boat-20hp',
				'capacity'             => 5,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 20,
				'color'                => '#d63131',
				'notes'                => 'Current 20hp self-drive boat in the 2026 fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'boat',
				'name'                 => 'Adria 8hp',
				'slug'                 => 'adria-8hp',
				'capacity'             => 4,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 10,
				'color'                => '#d63131',
				'notes'                => 'Small easy-drive boat for short local routes around Hvar.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Zodiac 60hp',
				'slug'                 => 'zodiac-60hp',
				'capacity'             => 6,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 40,
				'color'                => '#1f6bff',
				'notes'                => 'Compact Zodiac for quick island-hopping and shorter transfers.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Marinello 1 Bordo 60hp',
				'slug'                 => 'marinello-1-bordo-60hp',
				'capacity'             => 7,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 50,
				'color'                => '#1f6bff',
				'notes'                => 'First Marinello speedboat in the current fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Marinello 2 Crveni 70hp',
				'slug'                 => 'marinello-2-crveni-70hp',
				'capacity'             => 7,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 60,
				'color'                => '#1f6bff',
				'notes'                => 'Second Marinello speedboat in the current fleet sheet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Scar 2 Bijeli 150hp',
				'slug'                 => 'scar-2-bijeli-150hp',
				'capacity'             => 8,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 90,
				'color'                => '#1f6bff',
				'notes'                => 'Second SCAR 150hp resource from the current operational fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Baracuda 2 175hp',
				'slug'                 => 'baracuda-2-175hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 120,
				'color'                => '#1f6bff',
				'notes'                => 'Second 175hp Baracuda resource from the current fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Baracuda 3 175hp',
				'slug'                 => 'baracuda-3-175hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 130,
				'color'                => '#1f6bff',
				'notes'                => 'Third 175hp Baracuda resource from the current fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Baracuda Black 200hp',
				'slug'                 => 'baracuda-black-200hp',
				'capacity'             => 10,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 150,
				'color'                => '#1f6bff',
				'notes'                => 'Black 200hp Baracuda resource from the current fleet.',
			),
			array(
				'resource_type'        => 'boat',
				'category'             => 'speedboat',
				'name'                 => 'Zar 2 Crni 300hp',
				'slug'                 => 'zar-2-crni-300hp',
				'capacity'             => 14,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 1,
				'default_service_type' => 'rental',
				'sort_order'           => 170,
				'color'                => '#1f6bff',
				'notes'                => 'Second Zar 300hp resource from the current fleet.',
			),
			array(
				'resource_type'        => 'transfer',
				'category'             => 'transfer',
				'name'                 => 'TRANSFERI 1',
				'slug'                 => 'transferi-1',
				'capacity'             => 12,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 0,
				'default_service_type' => 'transfer',
				'sort_order'           => 200,
				'color'                => '#e559b5',
				'notes'                => 'Dedicated internal transfer resource.',
			),
			array(
				'resource_type'        => 'transfer',
				'category'             => 'transfer',
				'name'                 => 'TRANSFERI 2',
				'slug'                 => 'transferi-2',
				'capacity'             => 12,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 0,
				'default_service_type' => 'transfer',
				'sort_order'           => 210,
				'color'                => '#e559b5',
				'notes'                => 'Dedicated internal transfer resource.',
			),
			array(
				'resource_type'        => 'transfer',
				'category'             => 'transfer',
				'name'                 => 'TRANSFERI 3',
				'slug'                 => 'transferi-3',
				'capacity'             => 12,
				'supports_skipper'     => 1,
				'supports_bareboat'    => 0,
				'default_service_type' => 'transfer',
				'sort_order'           => 220,
				'color'                => '#e559b5',
				'notes'                => 'Dedicated internal transfer resource.',
			),
		);
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_Documents {

	public static function maybe_send_booking_confirmation( $booking ) {
		if ( empty( $booking['generate_confirmation'] ) ) {
			return array(
				'enabled' => false,
				'sent'    => false,
				'message' => __( 'Booking confirmation is turned off for this booking.', 'hvar-bookings' ),
			);
		}

		$email = sanitize_email( (string) ( $booking['customer_email'] ?? '' ) );
		if ( '' === $email ) {
			return array(
				'enabled' => true,
				'sent'    => false,
				'message' => __( 'Booking saved, but confirmation was not sent because Customer E-mail is empty.', 'hvar-bookings' ),
			);
		}

		$resource   = self::get_resource( (int) $booking['resource_id'] );
		$subject    = self::confirmation_subject( $booking, $resource );
		$attachment = self::create_confirmation_document( $booking, $resource );
		$body       = self::confirmation_mail_body( $booking, $resource );
		$headers    = array( 'Content-Type: text/plain; charset=UTF-8' );

		$sent = wp_mail( $email, $subject, $body, $headers, array( $attachment ) );

		if ( $attachment && file_exists( $attachment ) ) {
			wp_delete_file( $attachment );
		}

		return array(
			'enabled'   => true,
			'sent'      => (bool) $sent,
			'recipient' => $email,
			'subject'   => $subject,
			'message'   => $sent
				? __( 'Booking confirmation document was sent to the guest.', 'hvar-bookings' )
				: __( 'Booking was saved, but the confirmation email could not be sent from this environment.', 'hvar-bookings' ),
		);
	}

	public static function build_confirmation_preview( $booking ) {
		$resource = self::get_resource( (int) $booking['resource_id'] );

		return array(
			'enabled'   => ! empty( $booking['generate_confirmation'] ),
			'recipient' => sanitize_email( (string) ( $booking['customer_email'] ?? '' ) ),
			'subject'   => self::confirmation_subject( $booking, $resource ),
			'text'      => self::confirmation_text( $booking, $resource ),
		);
	}

	public static function build_manager_notification( $booking ) {
		if ( empty( $booking['generate_manager_notification'] ) ) {
			return array(
				'enabled' => false,
			);
		}

		$resource = self::get_resource( (int) $booking['resource_id'] );
		$text     = self::manager_notification_text( $booking, $resource );
		$number   = Hex_Bookings_Plugin::get_manager_whatsapp_number();

		return array(
			'enabled'        => true,
			'text'           => $text,
			'manager_number' => $number,
			'scheduled_time' => Hex_Bookings_Plugin::get_manager_notification_time(),
			'whatsapp_url'   => self::whatsapp_url( $number, $text ),
		);
	}

	protected static function confirmation_subject( $booking, $resource ) {
		$resource_name = $resource ? $resource['name'] : __( 'Booking', 'hvar-bookings' );

		return sprintf(
			/* translators: 1: resource name, 2: booking date */
			__( 'Booking Confirmation - %1$s - %2$s', 'hvar-bookings' ),
			$resource_name,
			self::format_date( $booking['booking_date'] ?? '' )
		);
	}

	protected static function confirmation_mail_body( $booking, $resource ) {
		$customer = trim( (string) ( $booking['customer_name'] ?? '' ) );
		if ( '' === $customer ) {
			$customer = __( 'guest', 'hvar-bookings' );
		}

		return sprintf(
			/* translators: %s: customer name */
			__( "Hello %s,\n\nYour booking confirmation document is attached to this email.\n\nIf you have any questions, reply to this message or contact us on WhatsApp/Viber +385958700479.\n\nKind regards,\nHvar Excursions", 'hvar-bookings' ),
			$customer
		);
	}

	protected static function create_confirmation_document( $booking, $resource ) {
		$upload_dir = wp_upload_dir();
		$dir        = trailingslashit( $upload_dir['basedir'] ) . 'hex-booking-confirmations';

		if ( ! wp_mkdir_p( $dir ) ) {
			$dir = trailingslashit( get_temp_dir() ) . 'hex-booking-confirmations';
			wp_mkdir_p( $dir );
		}

		$filename = sprintf(
			'booking-confirmation-%d-%s.doc',
			(int) $booking['id'],
			sanitize_title( $resource ? $resource['name'] : 'booking' )
		);
		$path = trailingslashit( $dir ) . $filename;

		$html = "<html><head><meta charset=\"utf-8\"></head><body style=\"font-family: Arial, sans-serif; color:#0f2742;\"><pre style=\"font-family: Arial, sans-serif; white-space: pre-wrap; line-height: 1.55;\">" .
			esc_html( self::confirmation_text( $booking, $resource ) ) .
			'</pre></body></html>';

		file_put_contents( $path, $html );

		return $path;
	}

	protected static function confirmation_text( $booking, $resource ) {
		if ( in_array( (string) ( $booking['service_type'] ?? '' ), array( 'transfer', 'taxi' ), true ) ) {
			return self::transfer_confirmation_text( $booking, $resource );
		}

		return self::rental_confirmation_text( $booking, $resource );
	}

	protected static function rental_confirmation_text( $booking, $resource ) {
		$replacements = self::common_replacements( $booking, $resource );

		$template = <<<TEXT
Thank you for booking with us {ContactName}!

Here are the details for your booking {Date}:
Contact name/phone: {ContactName} {ContactPhone}

Check-in/boat pick up location:
- The meeting point will be where we agreed.
- Please check the exact pick-up location on the map:
  {PickUpMapLink}
- If there is several minutes of delay, which is possible due to a busy harbor, we apologize in advance.

Contact:
- For booking related questions, and check-in arrangement please contact us on our email address or my (Ivan) WhatsApp / Viber: +385958700479

Please take a minute and double-check the following details:

================
BOOKING CONFIRMATION
================
Boat: {BoatName}
Date: {Date}
Check in Time: {Time}
Check out Time: {ReturnTime}
Passengers: {PassengerCount}
Extras: {Extras}
Skipper: {SkipperStatus}
Fuel Policy: {FuelStatus}
-----------------------------
Total Price: {TotalPrice}

Deposit paid: {Deposit}
-----------------------------
Remaining amount to pay on pickup:
= {RemainingAmount}

Notes
- Please note that we only accept cash at check-in.
- If you are renting a speedboat without a skipper, you are required to have a valid boat license with you.
- Remember to bring a valid form of identification, such as an ID card, driving license, or passport.
- In addition to the above, we recommend bringing:
  - Fully charged mobile phone
  - Sunscreen
  - Towels
  - Light jacket if the weather forecast predicts windy conditions.

Insurance and Damage Responsibility

Please note that the boat is covered by third-party liability insurance, meaning that any damage caused by another party is covered by the insurance.
However, guests are responsible for any damage they personally cause to the boat, including (but not limited to):
- Loss of anchor or other equipment
- Damage to the propeller or engine
- Breakage of any parts onboard

In case of any such incident, guests are required to report it immediately at the time of return, and compensation will be arranged accordingly.

Upon check-in, we kindly ask you to inspect the boat's condition, equipment, and propeller together with the skipper to confirm everything is in proper order before departure.

Renting 300hp+ Speedboats
For rentals of our 300hp and more powerful speedboats, for the safety of our guests and to avoid unpleasant or dangerous situations, we require that the renter has significant prior boating experience, as these boats are demanding to operate. In addition to the mandatory boating license, the owner may request a short trial run to verify the renter's skill and experience before approving the rental.

Cancellation Policy

We offer refunds for booking deposits in the following cases:
- A FULL REFUND (minus a 20 EUR service fee) is available up to 15 days before the pick-up date.
- A PARTIAL REFUND of up to 50% of the deposit amount is possible between 14 to 2 full days before the pick-up date.
- NO REFUND will be provided for cancellations made within one full day prior to the pick-up date or in the event of a no-show at the designated pick-up time (unless arranged otherwise).

We understand that some circumstances may be beyond your control, so we offer penalty-free cancellations at any time before the pick-up date if we are unable to provide the service due to unfavorable weather conditions or technical/mechanical issues.

Once again thank you for your trust and decision to book with us!
TEXT;

		return strtr( $template, $replacements );
	}

	protected static function transfer_confirmation_text( $booking, $resource ) {
		$replacements = self::common_replacements( $booking, $resource );

		$template = <<<TEXT
Thank you for booking with us {ContactName}!

Here are the details for your booking {Date}:
Contact name/phone: {ContactName} {ContactPhone}

Pick up location ({PickUpLocation})
Pick up is at {PickUpLocation}.
- Please check the exact pick-up location on Google map: {PickUpMapLink}
- The boat and skipper will be waiting for you there.

Drop off location ({DropOffLocation})
- Drop off location {DropOffLocation}.
- Please check the exact drop off location on Google map: {DropOffMapLink}

Contact:
- For booking related questions, and check-in arrangement please contact us on our email address or my (Ivan) WhatsApp / Viber: +385958700479

Please take a minute and double-check the following details:

================
BOOKING CONFIRMATION
================
Transfer: {PickUpLocation} - {DropOffLocation}
Boat: {BoatName}
Date: {Date}
Time: {Time}
Passengers: {PassengerCount}
Skipper: {SkipperStatus}
Luggage: {LuggageStatus}
Fuel Policy: {FuelStatus}
-----------------------------
Total Price: {TotalPrice}
Deposit(s) paid: {Deposit}
-----------------------------
Remaining amount to pay on pickup (in cash):
= {RemainingAmount}

Notes
- Please note that we only accept cash at check-in.
- Remember to bring a valid form of identification, such as an ID card, driving license, or passport.
- In addition to the above, we recommend bringing:
  - Fully charged mobile phone
  - Sunscreen
  - Towels
  - Light jacket if the weather forecast predicts windy conditions.

Cancellation Policy

We offer refunds for booking deposits in the following cases:
- A FULL REFUND (minus a 20 EUR service fee) is available up to 15 days before the pick-up date.
- A PARTIAL REFUND of up to 50% of the deposit amount is possible between 14 to 2 full days before the pick-up date.
- NO REFUND will be provided for cancellations made within one full day prior to the pick-up date or in the event of a no-show at the designated pick-up time (unless arranged otherwise).

We understand that some circumstances may be beyond your control, so we offer penalty-free cancellations at any time before the pick-up date if we are unable to provide the service due to unfavorable weather conditions or technical/mechanical issues.

Thank you for your trust, and we look forward to seeing you soon.

Kind Regards,
Ivan

www.hvarexcursions.com
email: info@hvarexcursions.com
Contact (WhatsApp, Viber, phone):
+385 95 87 00 479
TEXT;

		return strtr( $template, $replacements );
	}

	protected static function common_replacements( $booking, $resource ) {
		$total     = self::to_float( $booking['booking_price'] ?? null );
		$deposit   = self::to_float( $booking['advance_amount'] ?? null );
		$remaining = max( 0, $total - $deposit );
		$extras    = self::extras_label( $booking['extra_equipment'] ?? array() );
		$pickup    = (string) ( $booking['pickup_location'] ?? '' );
		$dropoff   = (string) ( $booking['dropoff_location'] ?? '' );
		$pickup_map = self::map_link( $pickup, (string) ( $booking['pickup_coordinates'] ?? '' ) );
		$dropoff_map = self::map_link( $dropoff, (string) ( $booking['dropoff_coordinates'] ?? '' ) );
		$passengers = (int) ( $booking['passengers'] ?? 0 );
		$boat_name  = $resource ? (string) $resource['name'] : __( 'Booking', 'hvar-bookings' );

		return array(
			'{ContactName}'   => self::fallback( $booking['customer_name'] ?? '', __( 'Guest', 'hvar-bookings' ) ),
			'{ContactPhone}'  => self::fallback( $booking['customer_phone'] ?? '', '-' ),
			'{BoatName}'      => $boat_name,
			'{Date}'          => self::format_date( $booking['booking_date'] ?? '' ),
			'{Time}'          => self::format_time( $booking['start_time'] ?? '' ),
			'{ReturnTime}'    => self::format_time( $booking['end_time'] ?? '' ),
			'{PassengerCount}'=> $passengers > 0 ? (string) $passengers : '0',
			'{Extras}'        => $extras,
			'{SkipperStatus}' => self::skipper_label( $booking ),
			'{FuelStatus}'    => self::fuel_label( $booking ),
			'{TotalPrice}'    => self::money_label( $total ),
			'{Deposit}'       => self::money_label( $deposit ),
			'{RemainingAmount}' => self::money_label( $remaining ),
			'{PickUpMapLink}' => $pickup_map,
			'{DropOffMapLink}' => $dropoff_map,
			'{PickUpLocation}' => self::fallback( $pickup, __( 'Hvar', 'hvar-bookings' ) ),
			'{DropOffLocation}' => self::fallback( $dropoff, '-' ),
			'{LuggageStatus}' => self::fallback( $booking['luggage_details'] ?? '', __( 'None', 'hvar-bookings' ) ),
		);
	}

	protected static function skipper_label( $booking ) {
		$service = (string) ( $booking['service_type'] ?? '' );
		if ( in_array( $service, array( 'transfer', 'taxi' ), true ) ) {
			return __( 'Included', 'hvar-bookings' );
		}

		return 'without_skipper' === (string) ( $booking['skipper_mode'] ?? '' )
			? __( 'Not included', 'hvar-bookings' )
			: __( 'Included', 'hvar-bookings' );
	}

	protected static function fuel_label( $booking ) {
		$service = (string) ( $booking['service_type'] ?? '' );
		if ( in_array( $service, array( 'transfer', 'taxi' ), true ) ) {
			return __( 'Included', 'hvar-bookings' );
		}

		return ! empty( $booking['fuel_included'] )
			? __( 'Included in price', 'hvar-bookings' )
			: __( 'Not included', 'hvar-bookings' );
	}

	protected static function extras_label( $values ) {
		$values = is_array( $values ) ? $values : array();
		if ( empty( $values ) ) {
			return __( 'None', 'hvar-bookings' );
		}

		$labels = array();
		$options = Hex_Bookings_Plugin::get_extra_equipment_options();
		$lookup = array();
		foreach ( $options as $option ) {
			$lookup[ $option['value'] ] = $option['label'];
		}

		foreach ( $values as $value ) {
			$value = sanitize_key( (string) $value );
			if ( isset( $lookup[ $value ] ) ) {
				$labels[] = $lookup[ $value ];
			}
		}

		return empty( $labels ) ? __( 'None', 'hvar-bookings' ) : implode( ', ', $labels );
	}

	public static function manager_notification_text( $booking, $resource ) {
		$weekday = self::format_weekday( $booking['booking_date'] ?? '' );
		$date    = self::format_date( $booking['booking_date'] ?? '' );
		$time    = self::format_time( $booking['start_time'] ?? '' );
		$return  = self::format_time( $booking['end_time'] ?? '' );
		$total   = self::to_float( $booking['booking_price'] ?? null );
		$deposit = self::to_float( $booking['advance_amount'] ?? null );
		$remaining = max( 0, $total - $deposit );
		$pickup_map = self::map_link( (string) ( $booking['pickup_location'] ?? '' ), (string) ( $booking['pickup_coordinates'] ?? '' ) );
		$dropoff_map = self::map_link( (string) ( $booking['dropoff_location'] ?? '' ), (string) ( $booking['dropoff_coordinates'] ?? '' ) );
		$notes   = self::fallback( $booking['notes'] ?? '', '-' );
		$internal = trim( (string) ( $booking['internal_notes'] ?? '' ) );

		if ( in_array( (string) ( $booking['service_type'] ?? '' ), array( 'transfer', 'taxi' ), true ) ) {
			$template = <<<TEXT
Notes for the transfer:
Departure date/time: {Date} ({Weekday}) at {Time}
Pick-up location: {PickUpLocation}
{PickUpMapLink}
Drop-off location: {DropOffLocation}
{DropOffMapLink}
Number of passengers: {PassengerCount}{LuggageSuffix}
Contact person / phone: {ContactName} {ContactPhone}
Additional notes: {AdditionalNotes}
-----------------------------
Total price: {TotalPrice}
Deposit paid: {Deposit}
-----------------------------
To be collected (cash):
= {RemainingAmount}

Notes for Filip:
{InternalNotes}
TEXT;
		} else {
			$template = <<<TEXT
Notes for the speedboat:
Speedboat: {BoatName}
Departure date/time: {Date} ({Weekday}) at {Time}
Return time: {ReturnTime}
Pick-up location: {PickUpLocation}
{PickUpMapLink}
Number of guests: {PassengerCount}
Skipper: {SkipperStatus}
Fuel: {FuelStatus}
Extras: {Extras}
Contact person / phone: {ContactName} {ContactPhone}
Additional notes: {AdditionalNotes}
-----------------------------
Total price: {TotalPrice}
Deposit paid: {Deposit}
-----------------------------
To be collected (cash):
= {RemainingAmount}

Notes for Filip:
{InternalNotes}
TEXT;
		}

		return strtr(
			$template,
			array(
				'{BoatName}'       => $resource ? $resource['name'] : __( 'Boat', 'hvar-bookings' ),
				'{Date}'           => $date,
				'{Weekday}'        => $weekday,
				'{Time}'           => $time,
				'{ReturnTime}'     => $return,
				'{PickUpLocation}' => self::fallback( $booking['pickup_location'] ?? '', __( 'Hvar', 'hvar-bookings' ) ),
				'{PickUpMapLink}'  => $pickup_map,
				'{DropOffLocation}' => self::fallback( $booking['dropoff_location'] ?? '', '-' ),
				'{DropOffMapLink}' => $dropoff_map,
				'{PassengerCount}' => (string) ( (int) ( $booking['passengers'] ?? 0 ) ),
				'{LuggageSuffix}'  => '' !== (string) ( $booking['luggage_details'] ?? '' ) ? ' + ' . $booking['luggage_details'] : '',
				'{SkipperStatus}'  => self::skipper_label( $booking ),
				'{FuelStatus}'     => self::fuel_label( $booking ),
				'{Extras}'         => self::extras_label( $booking['extra_equipment'] ?? array() ),
				'{ContactName}'    => self::fallback( $booking['customer_name'] ?? '', __( 'Guest', 'hvar-bookings' ) ),
				'{ContactPhone}'   => self::fallback( $booking['customer_phone'] ?? '', '-' ),
				'{AdditionalNotes}' => $notes,
				'{TotalPrice}'     => self::money_label( $total ),
				'{Deposit}'        => self::money_label( $deposit ),
				'{RemainingAmount}' => self::money_label( $remaining ),
				'{InternalNotes}'  => '' !== $internal ? $internal : '-',
			)
		);
	}

	protected static function whatsapp_url( $number, $text ) {
		$digits = preg_replace( '/\D+/', '', (string) $number );
		if ( '' === $digits || '' === trim( (string) $text ) ) {
			return '';
		}

		return 'https://wa.me/' . $digits . '?text=' . rawurlencode( $text );
	}

	protected static function map_link( $label, $coordinates ) {
		$query = trim( (string) $coordinates );
		if ( '' === $query ) {
			$query = trim( (string) $label );
		}

		if ( '' === $query ) {
			return '-';
		}

		return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query );
	}

	protected static function get_resource( $resource_id ) {
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

	protected static function format_date( $date_value ) {
		if ( '' === (string) $date_value ) {
			return '-';
		}

		$timestamp = strtotime( (string) $date_value );
		return $timestamp ? wp_date( 'd.m.Y', $timestamp ) : (string) $date_value;
	}

	protected static function format_weekday( $date_value ) {
		if ( '' === (string) $date_value ) {
			return '-';
		}

		$timestamp = strtotime( (string) $date_value );
		return $timestamp ? wp_date( 'l', $timestamp ) : '-';
	}

	protected static function format_time( $time_value ) {
		$time_value = (string) $time_value;
		if ( '' === $time_value ) {
			return '--:--';
		}

		return substr( $time_value, 0, 5 );
	}

	protected static function money_label( $amount ) {
		return 'EUR ' . number_format( self::to_float( $amount ), 2, '.', '' );
	}

	protected static function to_float( $value ) {
		return (float) ( $value ?: 0 );
	}

	protected static function fallback( $value, $fallback ) {
		$value = trim( (string) $value );
		return '' !== $value ? $value : $fallback;
	}
}

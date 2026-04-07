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
			__( "Hello %s,\n\nYour booking confirmation document is attached to this email.\n\nIf you have any questions, reply to this message or contact us on WhatsApp/Viber +385958700479 (Ivan)\n\nKind regards,\nIvan\n\nBumbar Rent / HvarExcursions.com", 'hvar-bookings' ),
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
			'booking-confirmation-%d-%s.docx',
			(int) $booking['id'],
			sanitize_title( $resource ? $resource['name'] : 'booking' )
		);
		$path = trailingslashit( $dir ) . $filename;

		self::write_docx_package( $path, self::confirmation_text( $booking, $resource ) );

		return $path;
	}

	protected static function write_docx_package( $path, $text ) {
		$links                = array();
		$logo_relationship_id = '';
		$logo_path            = self::confirmation_logo_path();

		if ( '' !== $logo_path && file_exists( $logo_path ) ) {
			$logo_relationship_id = 'rIdLogo';
		}

		$files = array(
			'[Content_Types].xml' => self::docx_content_types_xml(),
			'_rels/.rels' => self::docx_root_relationships_xml(),
			'docProps/app.xml' => self::docx_app_xml(),
			'docProps/core.xml' => self::docx_core_xml(),
			'word/document.xml' => self::docx_document_xml( (string) $text, $links, $logo_relationship_id ),
			'word/styles.xml' => self::docx_styles_xml(),
			'word/settings.xml' => self::docx_settings_xml(),
		);

		if ( '' !== $logo_relationship_id ) {
			$files['word/media/bumbar-rent-logo.png'] = file_get_contents( $logo_path );
		}

		$files['word/_rels/document.xml.rels'] = self::docx_document_relationships_xml( $links, $logo_relationship_id );

		self::write_zip_store_package( $path, $files );
	}

	protected static function docx_document_xml( $text, &$links, $logo_relationship_id = '' ) {
		$paragraphs = array();
		$lines      = preg_split( "/\r\n|\n|\r/", (string) $text );

		foreach ( $lines as $line ) {
			$paragraphs[] = self::docx_paragraph_xml( $line, $links );
			if ( '' !== $logo_relationship_id && 'Bumbar Rent / HvarExcursions.com' === trim( (string) $line ) ) {
				$paragraphs[] = self::docx_logo_paragraph_xml( $logo_relationship_id );
			}
		}

		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
			'<w:body>' .
			implode( '', $paragraphs ) .
			'<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="360" w:footer="360" w:gutter="0"/></w:sectPr>' .
			'</w:body></w:document>';
	}

	protected static function docx_paragraph_xml( $line, &$links ) {
		$line      = (string) $line;
		$trimmed   = trim( $line );
		$is_title  = self::docx_is_heading_line( $trimmed );
		$is_center = false;
		$is_bullet = self::docx_is_bullet_line( $line );
		$is_nested_bullet = self::docx_is_nested_bullet_line( $line );

		$paragraph_properties = '<w:pPr><w:spacing w:after="' . ( '' === $trimmed ? '0' : '45' ) . '" w:line="218" w:lineRule="auto"/>';
		if ( $is_center ) {
			$paragraph_properties .= '<w:jc w:val="center"/>';
		}
		if ( $is_bullet ) {
			$paragraph_properties .= $is_nested_bullet ? '<w:ind w:left="720" w:hanging="300"/>' : '<w:ind w:left="360" w:hanging="300"/>';
		}
		$paragraph_properties .= '</w:pPr>';

		if ( $is_bullet ) {
			$line = preg_replace( '/^(\s*)•\s*/u', '$1•	', $line );
		}

		return '<w:p>' . $paragraph_properties . self::docx_runs_with_links_xml( $line, $links, $is_title ) . '</w:p>';
	}

	protected static function docx_runs_with_links_xml( $line, &$links, $force_bold = false ) {
		if ( '' === (string) $line ) {
			return '<w:r><w:t></w:t></w:r>';
		}

		$parts = preg_split( '~(https?://\S+)~', (string) $line, -1, PREG_SPLIT_DELIM_CAPTURE );
		$xml   = '';

		foreach ( $parts as $part ) {
			if ( '' === $part ) {
				continue;
			}

			if ( preg_match( '~^https?://\S+$~', $part ) ) {
				$relationship_id = 'rId' . ( count( $links ) + 10 );
				$links[ $relationship_id ] = $part;
				$xml .= '<w:hyperlink r:id="' . self::xml_attr( $relationship_id ) . '" w:history="1">' .
					self::docx_run_xml( $part, false, true ) .
					'</w:hyperlink>';
				continue;
			}

			$xml .= self::docx_runs_for_text_xml( $part, $force_bold );
		}

		return $xml;
	}

	protected static function docx_logo_paragraph_xml( $relationship_id ) {
		return '<w:p><w:pPr><w:spacing w:before="120" w:after="45" w:line="218" w:lineRule="auto"/></w:pPr><w:r><w:drawing>' .
			'<wp:inline distT="0" distB="0" distL="0" distR="0">' .
			'<wp:extent cx="2100000" cy="650000"/><wp:effectExtent l="0" t="0" r="0" b="0"/><wp:docPr id="1" name="Bumbar Rent Logo"/><wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>' .
			'<a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
			'<pic:pic><pic:nvPicPr><pic:cNvPr id="0" name="Bumbar Rent Logo"/><pic:cNvPicPr/></pic:nvPicPr>' .
			'<pic:blipFill><a:blip r:embed="' . self::xml_attr( $relationship_id ) . '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>' .
			'<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="2100000" cy="650000"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>' .
			'</pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>';
	}

	protected static function docx_runs_for_text_xml( $text, $force_bold = false ) {
		$text = (string) $text;
		if ( '' === $text ) {
			return '';
		}

		if ( $force_bold ) {
			return self::docx_run_xml( $text, true );
		}

		$label_patterns = array(
			'Contact name/phone:',
			'Boat:',
			'Transfer:',
			'Date:',
			'Check in Time:',
			'Check out Time:',
			'Time:',
			'Passengers:',
			'Extras:',
			'Skipper:',
			'Luggage:',
			'Fuel Policy:',
			'Total Price:',
			'Deposit paid:',
			'Deposit(s) paid:',
			'Remaining amount to pay on pickup:',
			'Remaining amount to pay on pickup (in cash):',
		);

		foreach ( $label_patterns as $label ) {
			if ( 0 === strpos( ltrim( $text ), $label ) ) {
				$leading = substr( $text, 0, strlen( $text ) - strlen( ltrim( $text ) ) );
				$value   = substr( ltrim( $text ), strlen( $label ) );
				return self::docx_run_xml( $leading, false ) . self::docx_run_xml( $label, true ) . self::docx_run_xml( $value, false );
			}
		}

		$phrases = array(
			'only accept cash',
			'~50m from the hotel Delfin and Sports bar',
			'guests are responsible',
			'damage they personally cause',
			'required to report it immediately',
			'A FULL REFUND',
			'A PARTIAL REFUND',
			'NO REFUND',
			'20 EUR service fee',
		);

		return self::docx_highlight_phrases_xml( $text, $phrases );
	}

	protected static function docx_highlight_phrases_xml( $text, $phrases ) {
		$segments = array( array( 'text' => (string) $text, 'bold' => false ) );

		foreach ( $phrases as $phrase ) {
			$next = array();
			foreach ( $segments as $segment ) {
				if ( $segment['bold'] || '' === $segment['text'] ) {
					$next[] = $segment;
					continue;
				}

				$parts = explode( $phrase, $segment['text'] );
				if ( 1 === count( $parts ) ) {
					$next[] = $segment;
					continue;
				}

				$last_index = count( $parts ) - 1;
				foreach ( $parts as $index => $part ) {
					if ( '' !== $part ) {
						$next[] = array( 'text' => $part, 'bold' => false );
					}
					if ( $index < $last_index ) {
						$next[] = array( 'text' => $phrase, 'bold' => true );
					}
				}
			}
			$segments = $next;
		}

		$xml = '';
		foreach ( $segments as $segment ) {
			$xml .= self::docx_run_xml( $segment['text'], $segment['bold'] );
		}

		return $xml;
	}

	protected static function docx_run_xml( $text, $bold = false, $hyperlink = false ) {
		if ( '' === (string) $text ) {
			return '';
		}

		$properties = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="' . ( $bold ? '20' : '18' ) . '"/>';
		if ( $bold ) {
			$properties .= '<w:b/>';
		}
		if ( $hyperlink ) {
			$properties .= '<w:color w:val="0563C1"/><w:u w:val="single"/>';
		}
		$properties .= '</w:rPr>';

		$parts = preg_split( "/(\t)/", (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		$xml   = '<w:r>' . $properties;

		foreach ( $parts as $part ) {
			if ( '' === $part ) {
				continue;
			}

			if ( "\t" === $part ) {
				$xml .= '<w:tab/>';
				continue;
			}

			$xml .= '<w:t xml:space="preserve">' . self::xml_text( $part ) . '</w:t>';
		}

		return $xml . '</w:r>';
	}

	protected static function docx_is_bullet_line( $line ) {
		return (bool) preg_match( '/^\s*•/u', (string) $line );
	}

	protected static function docx_is_nested_bullet_line( $line ) {
		return (bool) preg_match( '/^\s{2,}•/u', (string) $line );
	}

	protected static function docx_is_heading_line( $line ) {
		return in_array(
			(string) $line,
			array(
				'BOOKING CONFIRMATION',
				'Notes',
				'Insurance and Damage Responsibility',
				'Renting 300hp+ Speedboats',
				'Cancellation Policy',
				'Contact:',
				'Check-in/boat pick up location:',
			),
			true
		);
	}

	protected static function docx_content_types_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
			'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
			'<Default Extension="xml" ContentType="application/xml"/>' .
			'<Default Extension="png" ContentType="image/png"/>' .
			'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>' .
			'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>' .
			'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
			'<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>' .
			'<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>' .
			'</Types>';
	}

	protected static function docx_root_relationships_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
			'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
			'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>' .
			'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>' .
			'</Relationships>';
	}

	protected static function docx_document_relationships_xml( $links, $logo_relationship_id = '' ) {
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
			'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
			'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>';

		if ( '' !== $logo_relationship_id ) {
			$xml .= '<Relationship Id="' . self::xml_attr( $logo_relationship_id ) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/bumbar-rent-logo.png"/>';
		}

		foreach ( $links as $id => $url ) {
			$xml .= '<Relationship Id="' . self::xml_attr( $id ) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="' . self::xml_attr( $url ) . '" TargetMode="External"/>';
		}

		return $xml . '</Relationships>';
	}

	protected static function docx_styles_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
			'<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:after="45" w:line="218" w:lineRule="auto"/></w:pPr><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="18"/></w:rPr></w:style>' .
			'</w:styles>';
	}

	protected static function docx_settings_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:zoom w:percent="100"/></w:settings>';
	}

	protected static function docx_app_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Hvar Bookings</Application></Properties>';
	}

	protected static function docx_core_xml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
			'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' .
			'<dc:title>Booking Confirmation</dc:title><dc:creator>Hvar Excursions</dc:creator><cp:lastModifiedBy>Hvar Excursions</cp:lastModifiedBy>' .
			'</cp:coreProperties>';
	}

	protected static function write_zip_store_package( $path, $files ) {
		$local   = '';
		$central = '';
		$offset  = 0;

		foreach ( $files as $name => $data ) {
			$name = str_replace( '\\', '/', (string) $name );
			$data = (string) $data;
			$crc  = hexdec( hash( 'crc32b', $data ) );
			$size = strlen( $data );
			$name_length = strlen( $name );

			$local_header = pack( 'VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $size, $size, $name_length, 0 ) . $name;
			$central_header = pack( 'VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 0, $crc, $size, $size, $name_length, 0, 0, 0, 0, 0, $offset ) . $name;

			$local  .= $local_header . $data;
			$central .= $central_header;
			$offset += strlen( $local_header ) + $size;
		}

		$end = pack( 'VvvvvVVv', 0x06054b50, 0, 0, count( $files ), count( $files ), strlen( $central ), strlen( $local ), 0 );
		file_put_contents( $path, $local . $central . $end );
	}

	protected static function xml_text( $value ) {
		return htmlspecialchars( (string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8' );
	}

	protected static function xml_attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
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
• The meeting point will be at the barrel (RED PIN on the map below), ~50m from the hotel Delfin and Sports bar in Hvar Port.
• Check the exact location on the map: {PickUpMapLink}
• If there’s several minutes of delay, which is possible due to busy harbor, we apologize in advance.

Contact:
• For booking related questions, and check-in arrangement please contact us on our email address or my (Ivan) WhatsApp / Viber: +385958700479

Please take a minute and double-check the following details:

========================
BOOKING CONFIRMATION
========================
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
= {RemainingAmount}{FuelAfterTripSuffix}

Notes
• Please note that we only accept cash at check-in.
• If you are renting a speedboat without a skipper, you are required to have a valid boat license with you.
• Remember to bring a valid form of identification, such as an ID card, driving license, or passport.
• In addition to the above, we recommend bringing:
  • Fully charged mobile phone
  • Sunscreen
  • Towels
  • Light jacket if the weather forecast predicts windy conditions.

Insurance and Damage Responsibility

Please note that the boat is covered by third-party liability insurance, meaning that any damage caused by another party is covered by the insurance.
However, guests are responsible for any damage they personally cause to the boat, including (but not limited to):
• Loss of anchor or other equipment
• Damage to the propeller or engine
• Breakage of any parts onboard

In case of any such incident, guests are required to report it immediately at the time of return, and compensation will be arranged accordingly.

Upon check-in, we kindly ask you to inspect the boat's condition, equipment, and propeller together with the skipper to confirm everything is in proper order before departure.

Renting 300hp+ Speedboats
For rentals of our 300hp and more powerful speedboats, for the safety of our guests and to avoid unpleasant or dangerous situations, we require that the renter has significant prior boating experience, as these boats are demanding to operate. In addition to the mandatory boating license, the owner may request a short trial run to verify the renter's skill and experience before approving the rental.

Cancellation Policy

We offer refunds for booking deposits in the following cases:
• A FULL REFUND (minus a 20 EUR service fee) is available up to 15 days before the pick-up date.
• A PARTIAL REFUND of up to 50% of the deposit amount is possible between 14 to 2 full days before the pick-up date.
• NO REFUND will be provided for cancellations made within one full day prior to the pick-up date or in the event of a no-show at the designated pick-up time (unless arranged otherwise).

We understand that some circumstances may be beyond your control, so we offer penalty-free cancellations at any time before the pick-up date if we are unable to provide the service due to unfavorable weather conditions or technical/mechanical issues.

Once again thank you for your trust and decision to book with us!

Bumbar Rent / HvarExcursions.com
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
• Please check the exact pick-up location on Google map: {PickUpMapLink}
• The boat and skipper will be waiting for you there.

Drop off location ({DropOffLocation})
• Drop off location {DropOffLocation}.
• Please check the exact drop off location on Google map: {DropOffMapLink}

Contact:
• For booking related questions, and check-in arrangement please contact us on our email address or my (Ivan) WhatsApp / Viber: +385958700479

Please take a minute and double-check the following details:

========================
BOOKING CONFIRMATION
========================
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
• Please note that we only accept cash at check-in.
• Remember to bring a valid form of identification, such as an ID card, driving license, or passport.
• In addition to the above, we recommend bringing:
  • Fully charged mobile phone
  • Sunscreen
  • Towels
  • Light jacket if the weather forecast predicts windy conditions.

Cancellation Policy

We offer refunds for booking deposits in the following cases:
• A FULL REFUND (minus a 20 EUR service fee) is available up to 15 days before the pick-up date.
• A PARTIAL REFUND of up to 50% of the deposit amount is possible between 14 to 2 full days before the pick-up date.
• NO REFUND will be provided for cancellations made within one full day prior to the pick-up date or in the event of a no-show at the designated pick-up time (unless arranged otherwise).

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
		if ( ! in_array( (string) ( $booking['service_type'] ?? '' ), array( 'transfer', 'taxi' ), true ) && ( '-' === $pickup_map || self::is_placeholder_location( $pickup ) ) ) {
			$pickup_map = self::default_hvar_pickup_map_link();
		}
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
			'{FuelAfterTripSuffix}' => self::fuel_after_trip_suffix( $booking ),
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

	protected static function fuel_after_trip_suffix( $booking ) {
		$service = (string) ( $booking['service_type'] ?? '' );
		if ( in_array( $service, array( 'transfer', 'taxi' ), true ) || ! empty( $booking['fuel_included'] ) ) {
			return '';
		}

		return ' (+fuel after the trip)';
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

		if ( '' === $query || self::is_placeholder_location( $query ) ) {
			return '-';
		}

		return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query );
	}

	protected static function default_hvar_pickup_map_link() {
		return 'https://maps.app.goo.gl/ABsMfYo6Z5fwbupD6';
	}

	protected static function is_placeholder_location( $value ) {
		$value = strtolower( trim( (string) $value ) );
		return in_array( $value, array( 'choose location', 'select location', 'choose', 'custom' ), true );
	}

	protected static function confirmation_logo_path() {
		$logo_files = array(
			'logo-bumbar-rent-hvar-excursions_black.png',
			'logo-bumbar-rent-hvar-excursions-black.png',
			'logo-bumbar-rent-hvar-excursions.png',
		);

		$base_dirs = array();
		if ( function_exists( 'get_stylesheet_directory' ) ) {
			$base_dirs[] = trailingslashit( get_stylesheet_directory() ) . 'assets/images/logo/';
		}

		$base_dirs[] = ABSPATH . 'wp-content/themes/catamaran-child/assets/images/logo/';

		foreach ( $base_dirs as $base_dir ) {
			foreach ( $logo_files as $logo_file ) {
				$path = $base_dir . $logo_file;
				if ( file_exists( $path ) ) {
					return $path;
				}
			}
		}

		return '';
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

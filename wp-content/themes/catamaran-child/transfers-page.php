<?php
/**
 * Virtual transfers page for Hvar Excursions.
 */

$hex_transfer_status = isset( $_GET['transfer_status'] ) ? sanitize_key( wp_unslash( $_GET['transfer_status'] ) ) : '';
$hex_transfer_notice = '';
$hex_transfer_notice_class = '';
$hex_transfer_notice_title = '';
$hex_transfer_defaults = array(
	'from'       => '',
	'to'         => '',
	'date'       => '',
	'time'       => '10:00',
	'passengers' => '',
	'luggage'    => 'Standard luggage',
	'notes'      => '',
);

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['hex_transfer_request'] ) ) {
	$hex_transfer_defaults['from']       = isset( $_POST['hex_transfer_from'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_from'] ) ) : '';
	$hex_transfer_defaults['to']         = isset( $_POST['hex_transfer_to'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_to'] ) ) : '';
	$hex_transfer_defaults['date']       = isset( $_POST['hex_transfer_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_date'] ) ) : '';
	$hex_transfer_defaults['time']       = isset( $_POST['hex_transfer_time'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_time'] ) ) : '10:00';
	$hex_transfer_defaults['passengers'] = isset( $_POST['hex_transfer_passengers'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_passengers'] ) ) : '';
	$hex_transfer_defaults['luggage']    = isset( $_POST['hex_transfer_luggage'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_transfer_luggage'] ) ) : 'Standard luggage';
	$hex_transfer_defaults['notes']      = isset( $_POST['hex_transfer_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['hex_transfer_notes'] ) ) : '';

	$hex_transfers_url = home_url( '/transfers/' );

	if ( ! isset( $_POST['hex_transfer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hex_transfer_nonce'] ) ), 'hex_transfer_request' ) ) {
		wp_safe_redirect( add_query_arg( 'transfer_status', 'failed', $hex_transfers_url ) );
		exit;
	}

	$hex_required_fields = array(
		$hex_transfer_defaults['from'],
		$hex_transfer_defaults['to'],
		$hex_transfer_defaults['date'],
		$hex_transfer_defaults['time'],
		$hex_transfer_defaults['passengers'],
	);

	if ( in_array( '', $hex_required_fields, true ) || $hex_transfer_defaults['from'] === $hex_transfer_defaults['to'] ) {
		wp_safe_redirect( add_query_arg( 'transfer_status', 'invalid', $hex_transfers_url ) );
		exit;
	}

	$hex_mail_to = 'info@hvarexcursions.com';
	if ( ! is_email( $hex_mail_to ) ) {
		$hex_mail_to = get_option( 'admin_email' );
	}

	$hex_mail_subject = sprintf(
		'Transfer Request: %s to %s',
		$hex_transfer_defaults['from'],
		$hex_transfer_defaults['to']
	);

	$hex_mail_lines = array(
		'New transfer request from the Transfers page.',
		'',
		'Transfer from: ' . $hex_transfer_defaults['from'],
		'Transfer to: ' . $hex_transfer_defaults['to'],
		'Date: ' . $hex_transfer_defaults['date'],
		'Pick-up time: ' . $hex_transfer_defaults['time'],
		'Passengers: ' . $hex_transfer_defaults['passengers'],
		'Luggage: ' . $hex_transfer_defaults['luggage'],
	);

	if ( '' !== $hex_transfer_defaults['notes'] ) {
		$hex_mail_lines[] = 'Notes: ' . $hex_transfer_defaults['notes'];
	}

	$hex_mail_sent = wp_mail( $hex_mail_to, $hex_mail_subject, implode( "\n", $hex_mail_lines ) );

	wp_safe_redirect(
		add_query_arg(
			'transfer_status',
			$hex_mail_sent ? 'sent' : 'failed',
			$hex_transfers_url
		)
	);
	exit;
}

if ( 'sent' === $hex_transfer_status ) {
	$hex_transfer_notice_title = 'Request Sent';
	$hex_transfer_notice = 'Your transfer request has been sent. We will contact you shortly.';
	$hex_transfer_notice_class = 'is-success';
} elseif ( 'invalid' === $hex_transfer_status ) {
	$hex_transfer_notice_title = 'Missing Information';
	$hex_transfer_notice = 'Please complete route, date, time, and passengers before sending your request.';
	$hex_transfer_notice_class = 'is-error';
} elseif ( 'failed' === $hex_transfer_status ) {
	$hex_transfer_notice_title = 'Request Not Sent';
	$hex_transfer_notice = 'We could not send the request right now. Please try again or contact us by WhatsApp.';
	$hex_transfer_notice_class = 'is-error';
}

catamaran_child_render_shell_start();

$hex_links = array(
	'home'       => home_url( '/' ),
	'rentals'    => home_url( '/rentals/' ),
	'excursions' => home_url( '/excursions/' ),
	'transfers'  => home_url( '/transfers/' ),
	'contact'    => home_url( '/contacts/' ),
	'whatsapp'   => 'https://wa.me/385958700479',
);

$destinations = array(
	'Split Airport',
	'Split Harbour',
	'Hvar Town Harbour',
	'Pakleni Islands',
	'Trogir',
	'Vis Town',
	'Korcula',
	'Dubrovnik',
	'Zadar',
	'Milna (Brac)',
);

$popular_routes = array(
	array(
		'title'       => 'Split Airport to Hvar',
		'from'        => 'Split Airport',
		'to'          => 'Hvar Town Harbour',
		'duration'    => 'Approx. 60 min',
		'copy'        => 'The fastest airport arrival route, ideal when you want to skip ferry transfers and reach Hvar directly.',
		'image'       => 'https://www.hvarexcursions.com/img/service-transfers.jpg',
		'eyebrow'     => 'Airport Priority',
	),
	array(
		'title'       => 'Split Harbour to Hvar',
		'from'        => 'Split Harbour',
		'to'          => 'Hvar Town Harbour',
		'duration'    => 'Approx. 55 min',
		'copy'        => 'A direct harbour-to-harbour transfer for guests arriving by cruise, hotel, or city transfer in Split.',
		'image'       => 'https://www.hvarexcursions.com/img/videos/speedboat_hvar_poster_image.jpg',
		'eyebrow'     => 'Fastest Link',
	),
	array(
		'title'       => 'Hvar to Vis',
		'from'        => 'Hvar Town Harbour',
		'to'          => 'Vis Town',
		'duration'    => 'Approx. 55 min',
		'copy'        => 'Private island-to-island transfer with more flexibility than public catamarans and easier luggage handling.',
		'image'       => 'https://www.hvarexcursions.com/img/excursions/tour-vis/thumb/tour_hvar_blue_cave_01.jpg',
		'eyebrow'     => 'Island Hop',
	),
	array(
		'title'       => 'Hvar to Korcula',
		'from'        => 'Hvar Town Harbour',
		'to'          => 'Korcula',
		'duration'    => 'Approx. 90 min',
		'copy'        => 'A longer private crossing for guests who want to move between islands without schedules and waiting around.',
		'image'       => 'https://www.hvarexcursions.com/img/excursions/tour-hvar/thumb/tour_hvar_pakleni_otoci_02.jpg',
		'eyebrow'     => 'Longer Range',
	),
);

$route_meta = array(
	'Split Airport|Hvar Town Harbour' => array(
		'duration' => 'Approx. 60 min',
		'tag'      => 'Direct airport transfer',
	),
	'Split Harbour|Hvar Town Harbour' => array(
		'duration' => 'Approx. 55 min',
		'tag'      => 'Direct harbour transfer',
	),
	'Hvar Town Harbour|Pakleni Islands' => array(
		'duration' => 'Approx. 15 min',
		'tag'      => 'Short local transfer',
	),
	'Hvar Town Harbour|Vis Town' => array(
		'duration' => 'Approx. 55 min',
		'tag'      => 'Open-sea island transfer',
	),
	'Hvar Town Harbour|Korcula' => array(
		'duration' => 'Approx. 90 min',
		'tag'      => 'Longer island crossing',
	),
	'Hvar Town Harbour|Dubrovnik' => array(
		'duration' => 'Approx. 3 hrs',
		'tag'      => 'Long-range private transfer',
	),
	'Hvar Town Harbour|Trogir' => array(
		'duration' => 'Approx. 70 min',
		'tag'      => 'Private coast transfer',
	),
	'Hvar Town Harbour|Zadar' => array(
		'duration' => 'Approx. 3 hrs',
		'tag'      => 'Custom long-range route',
	),
	'Hvar Town Harbour|Milna (Brac)' => array(
		'duration' => 'Approx. 40 min',
		'tag'      => 'Short island link',
	),
);

$capacity_cards = array(
	array(
		'title' => 'Small private groups',
		'copy'  => 'Ideal for airport pickups, hotel transfers, couples, and smaller groups who want a quick and elegant route.',
		'meta'  => '2-6 guests',
		'image' => 'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
	),
	array(
		'title' => 'Comfort for families',
		'copy'  => 'Better for luggage, more space, and routes where comfort matters just as much as speed.',
		'meta'  => '6-12 guests',
		'image' => 'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/thumb/bsc_175hp_hvar_excursions_rentals.jpg',
	),
	array(
		'title' => 'Larger custom groups',
		'copy'  => 'For events, beach clubs, villas, and multi-guest coordination where private timing is the priority.',
		'meta'  => '12+ guests',
		'image' => 'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/thumb/raptor-alesta-hvar-excursions-rentals.jpg',
	),
);

$faq_items = array(
	array(
		'question' => 'Why choose a sea transfer instead of public transport?',
		'answer'   => 'Because it is direct, private, and flexible. You avoid ferry schedules, long waits, luggage changes, and fixed departure times.',
	),
	array(
		'question' => 'Can you pick us up at a hotel, marina, or beach club?',
		'answer'   => 'Yes. The transfer concept is designed around direct pick-up and drop-off wherever conditions allow: harbours, hotels, marinas, fuel stations, and similar access points.',
	),
	array(
		'question' => 'Do off-peak hours help with pricing?',
		'answer'   => 'Yes. Your old page already highlights 06:00-08:59 and 18:00-20:59 as better-value hours, so the new page keeps that idea in a cleaner visual way.',
	),
	array(
		'question' => 'Can we request a destination not listed in the quick form?',
		'answer'   => 'Absolutely. The form is there to make common routes faster, but custom destinations and one-off routes are still available on request.',
	),
);

foreach ( $popular_routes as &$route ) {
	$route['image'] = catamaran_child_localize_image_url( $route['image'] );
}
unset( $route );

foreach ( $capacity_cards as &$capacity_card ) {
	$capacity_card['image'] = catamaran_child_localize_image_url( $capacity_card['image'] );
}
unset( $capacity_card );
?>
<div class="hex-homepage hex-transfers-shell" data-hex-transfer-root data-hex-transfer-routes="<?php echo esc_attr( wp_json_encode( $route_meta ) ); ?>" data-hex-transfer-contact-base="<?php echo esc_url( $hex_links['contact'] ); ?>" data-hex-transfer-whatsapp-base="<?php echo esc_url( $hex_links['whatsapp'] ); ?>">
	<header class="hex-transfers-hero" id="top">
		<div class="hex-transfers-hero__media" aria-hidden="true" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/videos/speedboat_hvar_poster_image.jpg' ) ); ?>');"></div>
		<div class="hex-transfers-hero__overlay" aria-hidden="true"></div>

		<nav class="hex-nav" aria-label="Main">
			<a class="hex-nav__brand" href="<?php echo esc_url( $hex_links['home'] ); ?>">
				<img src="<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/logo/logo-bumbar-rent-hvar-excursions.png' ) ); ?>" alt="Hvar Excursions logo" loading="eager" decoding="async">
			</a>

			<div class="hex-nav__center">
				<ul class="hex-nav__menu" id="hex-nav-menu">
					<li><a href="<?php echo esc_url( $hex_links['home'] ); ?>">Home</a></li>
					<li><a href="<?php echo esc_url( $hex_links['rentals'] ); ?>">Rentals</a></li>
					<li><a href="<?php echo esc_url( $hex_links['excursions'] ); ?>">Excursions</a></li>
					<li><a href="<?php echo esc_url( $hex_links['transfers'] ); ?>">Transfers</a></li>
					<li><a href="<?php echo esc_url( $hex_links['contact'] ); ?>">Contact</a></li>
				</ul>
			</div>

			<div class="hex-nav__actions">
				<button class="hex-icon-btn hex-search-toggle" type="button" aria-expanded="false" aria-controls="hex-search-drawer" title="Search">
					<span class="hex-icon hex-icon--search" aria-hidden="true"></span>
					<span class="screen-reader-text">Open search</span>
				</button>
				<button class="hex-icon-btn hex-panel-toggle" type="button" aria-expanded="false" aria-controls="hex-sidepanel" title="Panel bar">
					<span class="hex-icon hex-icon--panel" aria-hidden="true"></span>
					<span class="screen-reader-text">Open panel</span>
				</button>
				<a class="hex-icon-btn hex-whatsapp-btn" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener" title="Chat on WhatsApp">
					<span class="hex-icon hex-icon--whatsapp" aria-hidden="true">
						<svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
							<path d="M20.52 3.48A11.86 11.86 0 0 0 12.07 0C5.51 0 .17 5.34.17 11.9c0 2.1.55 4.16 1.59 5.97L0 24l6.31-1.66a11.86 11.86 0 0 0 5.76 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.46-8.43ZM12.08 21.8h-.01a9.83 9.83 0 0 1-5.01-1.37l-.36-.21-3.75.99 1-3.65-.23-.37a9.8 9.8 0 0 1-1.52-5.28c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.02 6.96 2.88a9.79 9.79 0 0 1 2.87 6.95c0 5.42-4.41 9.82-9.8 9.82Zm5.39-7.35c-.29-.15-1.71-.84-1.98-.94-.27-.1-.47-.15-.67.15-.2.3-.77.94-.95 1.13-.17.2-.35.22-.64.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.45-1.72-1.62-2.01-.17-.3-.02-.46.13-.61.13-.13.29-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.08-.8.37-.27.3-1.04 1.02-1.04 2.49s1.06 2.88 1.21 3.08c.15.2 2.09 3.2 5.07 4.49.71.31 1.26.49 1.69.62.71.22 1.36.19 1.87.12.57-.08 1.71-.7 1.95-1.38.24-.67.24-1.25.17-1.37-.07-.12-.27-.2-.57-.35Z"></path>
						</svg>
					</span>
					<span class="screen-reader-text">Send a WhatsApp message to +385 95 87 00 479</span>
				</a>
				<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Book now</a>
				<button class="hex-nav__toggle" type="button" aria-expanded="false" aria-controls="hex-nav-menu">Menu</button>
			</div>
		</nav>

		<div class="hex-search-drawer" id="hex-search-drawer" hidden>
			<form role="search" method="get" class="hex-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="hex-search-input" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'catamaran' ); ?></label>
				<input id="hex-search-input" type="search" name="s" placeholder="Search routes, destinations, transfers..." value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit">Search</button>
			</form>
		</div>

		<aside class="hex-sidepanel" id="hex-sidepanel" hidden aria-label="Quick panel">
			<div class="hex-sidepanel__inner">
				<button class="hex-sidepanel__close" type="button" aria-label="Close panel">&times;</button>
				<h3>Need a custom transfer?</h3>
				<p>Tell us where you are landing, how many guests you have, and how direct you want the route to be. We will confirm the best setup.</p>
				<ul>
					<li><a href="tel:+385958700479">+385 95 87 00 479</a></li>
					<li><a href="mailto:info@hvarexcursions.com">info@hvarexcursions.com</a></li>
					<li><a href="<?php echo esc_url( $hex_links['contact'] ); ?>">Contact page</a></li>
				</ul>
				<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request a quote</a>
			</div>
		</aside>
		<div class="hex-sidepanel-overlay" hidden></div>

		<div class="hex-transfers-hero__content">
			<div class="hex-transfers-hero__copy">
				<span class="hex-transfers-hero__eyebrow">Private Sea Transfers</span>
				<h1>Hvar transfers made fast, simple, and visually cleaner.</h1>
				<p>The old page had the right idea. This new version keeps the useful transfer logic but presents it like a premium concierge page instead of a long utility form.</p>
				<div class="hex-transfers-hero__cta">
					<a class="hex-btn hex-btn--primary" href="#transfer-quote">Get a quote</a>
					<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
				</div>
				<div class="hex-transfers-hero__chips">
					<?php foreach ( $popular_routes as $route ) : ?>
						<button class="hex-transfers-chip" type="button" data-hex-transfer-route data-from="<?php echo esc_attr( $route['from'] ); ?>" data-to="<?php echo esc_attr( $route['to'] ); ?>">
							<?php echo esc_html( $route['title'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="hex-transfers-quote-card" id="transfer-quote">
				<div class="hex-transfers-quote-card__stepper">
					<span>Step 1. Route</span>
					<span>Step 2. Time</span>
					<span>Step 3. Confirm</span>
				</div>
				<?php if ( $hex_transfer_notice ) : ?>
					<div class="hex-transfers-form__notice <?php echo esc_attr( $hex_transfer_notice_class ); ?>">
						<strong><?php echo esc_html( $hex_transfer_notice_title ); ?></strong>
						<p><?php echo esc_html( $hex_transfer_notice ); ?></p>
					</div>
				<?php endif; ?>
				<form class="hex-transfers-form" method="post" action="<?php echo esc_url( $hex_links['transfers'] ); ?>" data-hex-transfer-form>
					<?php wp_nonce_field( 'hex_transfer_request', 'hex_transfer_nonce' ); ?>
					<input type="hidden" name="hex_transfer_request" value="1">
					<div class="hex-transfers-form__row">
						<label class="hex-transfers-field hex-transfers-field--select" data-hex-transfer-picker-wrap>
							<span>Transfer from</span>
							<select name="hex_transfer_from" data-hex-transfer-from>
								<option value="">Choose origin</option>
								<?php foreach ( $destinations as $destination ) : ?>
									<option value="<?php echo esc_attr( $destination ); ?>"<?php selected( $hex_transfer_defaults['from'], $destination ); ?>><?php echo esc_html( $destination ); ?></option>
								<?php endforeach; ?>
								<option value="Custom location"<?php selected( $hex_transfer_defaults['from'], 'Custom location' ); ?>>Custom</option>
							</select>
						</label>
						<button class="hex-transfers-form__swap" type="button" data-hex-transfer-swap aria-label="Swap origin and destination">&#8645;</button>
						<label class="hex-transfers-field hex-transfers-field--select" data-hex-transfer-picker-wrap>
							<span>Transfer to</span>
							<select name="hex_transfer_to" data-hex-transfer-to>
								<option value="">Choose destination</option>
								<?php foreach ( $destinations as $destination ) : ?>
									<option value="<?php echo esc_attr( $destination ); ?>"<?php selected( $hex_transfer_defaults['to'], $destination ); ?>><?php echo esc_html( $destination ); ?></option>
								<?php endforeach; ?>
								<option value="Custom location"<?php selected( $hex_transfer_defaults['to'], 'Custom location' ); ?>>Custom</option>
							</select>
						</label>
					</div>
					<div class="hex-transfers-form__row hex-transfers-form__row--compact">
						<label class="hex-transfers-field hex-transfers-field--date" data-hex-transfer-picker-wrap>
							<span>Date</span>
							<input type="date" name="hex_transfer_date" value="<?php echo esc_attr( $hex_transfer_defaults['date'] ); ?>" data-hex-transfer-date>
						</label>
						<label class="hex-transfers-field hex-transfers-field--select" data-hex-transfer-picker-wrap>
							<span>Pick-up time</span>
							<select name="hex_transfer_time" data-hex-transfer-time>
								<?php
								for ( $hour = 0; $hour < 24; $hour++ ) :
									foreach ( array( '00', '30' ) as $minute ) :
										$time_value = sprintf( '%02d:%s', $hour, $minute );
										?>
										<option value="<?php echo esc_attr( $time_value ); ?>"<?php selected( $hex_transfer_defaults['time'], $time_value ); ?>><?php echo esc_html( $time_value ); ?></option>
										<?php
									endforeach;
								endfor;
								?>
							</select>
						</label>
						<label class="hex-transfers-field hex-transfers-field--select" data-hex-transfer-picker-wrap>
							<span>Passengers</span>
							<select name="hex_transfer_passengers" data-hex-transfer-passengers>
								<option value="">Choose size</option>
								<option value="2"<?php selected( $hex_transfer_defaults['passengers'], '2' ); ?>>Up to 2 people</option>
								<option value="4"<?php selected( $hex_transfer_defaults['passengers'], '4' ); ?>>Up to 4 people</option>
								<option value="6"<?php selected( $hex_transfer_defaults['passengers'], '6' ); ?>>Up to 6 people</option>
								<option value="8"<?php selected( $hex_transfer_defaults['passengers'], '8' ); ?>>Up to 8 people</option>
								<option value="12"<?php selected( $hex_transfer_defaults['passengers'], '12' ); ?>>Up to 12 people</option>
								<option value="16"<?php selected( $hex_transfer_defaults['passengers'], '16' ); ?>>Up to 16 people</option>
								<option value="24"<?php selected( $hex_transfer_defaults['passengers'], '24' ); ?>>Up to 24 people</option>
								<option value="32"<?php selected( $hex_transfer_defaults['passengers'], '32' ); ?>>Up to 32 people</option>
								<option value="40"<?php selected( $hex_transfer_defaults['passengers'], '40' ); ?>>Up to 40 people</option>
								<option value="56"<?php selected( $hex_transfer_defaults['passengers'], '56' ); ?>>Up to 56 people</option>
							</select>
						</label>
					</div>
					<div class="hex-transfers-form__row hex-transfers-form__row--compact">
						<div class="hex-transfers-form__stack">
							<label class="hex-transfers-field hex-transfers-field--select" data-hex-transfer-picker-wrap>
								<span>Luggage</span>
								<select name="hex_transfer_luggage" data-hex-transfer-luggage>
									<option value="Standard luggage"<?php selected( $hex_transfer_defaults['luggage'], 'Standard luggage' ); ?>>Standard luggage</option>
									<option value="Extra luggage"<?php selected( $hex_transfer_defaults['luggage'], 'Extra luggage' ); ?>>Extra luggage</option>
									<option value="Sports gear or bulky luggage"<?php selected( $hex_transfer_defaults['luggage'], 'Sports gear or bulky luggage' ); ?>>Sports gear or bulky luggage</option>
								</select>
							</label>
							<button class="hex-btn hex-btn--primary hex-transfers-form__submit" type="submit">Send Request</button>
						</div>
						<label class="hex-transfers-form__notes">
							<span>Notes</span>
							<textarea rows="3" name="hex_transfer_notes" data-hex-transfer-notes placeholder="Flight number, hotel, marina, child seats, beach club, custom route..."><?php echo esc_textarea( $hex_transfer_defaults['notes'] ); ?></textarea>
						</label>
					</div>
				</form>
			</div>
		</div>
	</header>

	<main class="hex-main hex-transfers-main">
		<section class="hex-transfers-summary">
			<div class="hex-container">
				<div class="hex-transfers-summary__grid">
					<div class="hex-transfers-summary__panel">
						<span class="hex-rentals-chip">Live Request Summary</span>
						<h2 data-hex-transfer-summary="route">Choose an origin and destination</h2>
						<div class="hex-transfers-summary__meta">
							<span data-hex-transfer-summary="duration">Estimated duration appears here</span>
							<span data-hex-transfer-summary="group">Passenger count appears here</span>
							<span data-hex-transfer-summary="timing">Pick-up timing appears here</span>
						</div>
						<p data-hex-transfer-summary="note">Confirm price and availability via Contact or WhatsApp, just like on your old transfer page, but in a cleaner layout.</p>
						<div class="hex-transfers-summary__actions">
							<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>" data-hex-transfer-contact>Send request</a>
							<a class="hex-btn hex-btn--ghost-dark" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener" data-hex-transfer-whatsapp>WhatsApp quote</a>
						</div>
					</div>
					<div class="hex-transfers-summary__aside">
						<div class="hex-transfers-summary__badge" data-hex-transfer-summary="status">Private speedboat transfer</div>
						<h3>Better-value timing</h3>
						<p>Off-peak hours from <strong>06:00-08:59</strong> and <strong>18:00-20:59</strong> are highlighted here because they were already part of your old booking logic.</p>
						<div class="hex-transfers-summary__offpeak" data-hex-transfer-summary="offpeak">Select a time to see if your request falls into the better-value window.</div>
					</div>
				</div>
			</div>
		</section>

		<section class="hex-transfers-steps">
			<div class="hex-container">
				<div class="hex-transfers-section__heading">
					<span class="hex-rentals-chip">How It Works</span>
					<h2>The same transfer logic as before, just easier to read and faster to act on.</h2>
				</div>
				<div class="hex-transfers-steps__grid">
					<article>
						<strong>1. Choose your route</strong>
						<p>Pick an origin and destination or tap one of the popular route chips to prefill the request instantly.</p>
					</article>
					<article>
						<strong>2. Set your timing</strong>
						<p>Add the date, time, and number of passengers so the request already feels organized before we confirm it.</p>
					</article>
					<article>
						<strong>3. Confirm by contact</strong>
						<p>Just like the old page, final price and availability are confirmed directly with you, now with a cleaner request summary.</p>
					</article>
				</div>
			</div>
		</section>

		<section class="hex-transfers-routes">
			<div class="hex-container">
				<div class="hex-transfers-section__heading">
					<span class="hex-rentals-chip">Popular Routes</span>
					<h2>Start with the transfers guests ask for most often.</h2>
					<p>These cards are not meant to replace custom routing. They just make the most common requests feel immediate and premium.</p>
				</div>
				<div class="hex-transfers-routes__grid">
					<?php foreach ( $popular_routes as $route ) : ?>
						<article class="hex-transfers-route-card">
							<div class="hex-transfers-route-card__media" style="background-image:url('<?php echo esc_url( $route['image'] ); ?>');"></div>
							<div class="hex-transfers-route-card__body">
								<span class="hex-rentals-chip"><?php echo esc_html( $route['eyebrow'] ); ?></span>
								<h3><?php echo esc_html( $route['title'] ); ?></h3>
								<div class="hex-transfers-route-card__duration"><?php echo esc_html( $route['duration'] ); ?></div>
								<p><?php echo esc_html( $route['copy'] ); ?></p>
								<button class="hex-btn hex-btn--small" type="button" data-hex-transfer-route data-from="<?php echo esc_attr( $route['from'] ); ?>" data-to="<?php echo esc_attr( $route['to'] ); ?>">Use this route</button>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hex-transfers-capacity">
			<div class="hex-container">
				<div class="hex-transfers-section__heading hex-transfers-section__heading--centered">
					<span class="hex-rentals-chip">Capacity & Comfort</span>
					<h2>Different transfers need different boat setups.</h2>
				</div>
				<div class="hex-transfers-capacity__grid">
					<?php foreach ( $capacity_cards as $card ) : ?>
						<article class="hex-transfers-capacity-card">
							<div class="hex-transfers-capacity-card__media" style="background-image:url('<?php echo esc_url( $card['image'] ); ?>');"></div>
							<div class="hex-transfers-capacity-card__body">
								<h3><?php echo esc_html( $card['title'] ); ?></h3>
								<div class="hex-transfers-capacity-card__meta"><?php echo esc_html( $card['meta'] ); ?></div>
								<p><?php echo esc_html( $card['copy'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hex-transfers-faq">
			<div class="hex-container">
				<div class="hex-transfers-section__heading">
					<span class="hex-rentals-chip">FAQ</span>
					<h2>Keep the useful questions from the old page, but in a calmer format.</h2>
				</div>
				<div class="hex-transfers-faq__list">
					<?php foreach ( $faq_items as $faq ) : ?>
						<details>
							<summary><?php echo esc_html( $faq['question'] ); ?></summary>
							<p><?php echo esc_html( $faq['answer'] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hex-transfers-cta">
			<div class="hex-container">
				<div class="hex-transfers-cta__inner">
					<span class="hex-rentals-chip">Custom Route</span>
					<h2>Hotels, airports, marinas, islands, beach clubs. If it makes sense on the map, ask us and we will shape the transfer around it.</h2>
					<div class="hex-transfers-cta__actions">
						<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request transfer</a>
						<a class="hex-btn hex-btn--ghost" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
					</div>
				</div>
			</div>
		</section>
	</main>

	<footer class="hex-footer">
		<div class="hex-container">
			<div class="hex-footer__cards">
				<div>
					<h3>Phone</h3>
					<p><a href="tel:+385958700479">+385 95 87 00 479</a></p>
					<p><a href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">Whatsapp and Viber</a></p>
				</div>
				<div>
					<h3>Email</h3>
					<p><a href="mailto:info@hvarexcursions.com">info@hvarexcursions.com</a></p>
				</div>
				<div>
					<h3>Address</h3>
					<p>Biskupa Dubokovica 22, 21450 Hvar</p>
				</div>
			</div>
			<div class="hex-footer__imprint">
				<p>
					U.O. BUMBAR, Vl. Filip Barisic<br>
					MB: 92448291, OIB: 34454779189<br>
					All rights reserved, 2006-2026 U.O. Bumbar
				</p>
			</div>
		</div>
	</footer>

	<button class="hex-back-to-top" type="button" aria-label="Back to top">Top</button>
</div>
<?php
catamaran_child_render_shell_end();

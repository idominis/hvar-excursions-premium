<?php
/**
 * Virtual contacts page for Hvar Excursions.
 */

$hex_topics = array( 'Boat Rental', 'Excursion', 'Transfer', 'Custom Request' );

$hex_contact_status       = isset( $_GET['contact_status'] ) ? sanitize_key( wp_unslash( $_GET['contact_status'] ) ) : '';
$hex_contact_notice       = '';
$hex_contact_notice_class = '';
$hex_contact_notice_title = '';
$hex_contact_defaults     = array(
	'name'    => isset( $_GET['name'] ) ? sanitize_text_field( wp_unslash( $_GET['name'] ) ) : '',
	'email'   => isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '',
	'phone'   => isset( $_GET['phone'] ) ? sanitize_text_field( wp_unslash( $_GET['phone'] ) ) : '',
	'topic'   => 'Transfer',
	'message' => '',
);

$hex_topic_map = array(
	'Boat Rental'      => 'Boat Rental',
	'Rental'           => 'Boat Rental',
	'Excursion'        => 'Excursion',
	'Transfer'         => 'Transfer',
	'Transfer Inquiry' => 'Transfer',
	'Custom Request'   => 'Custom Request',
);

if ( isset( $_GET['subject'] ) ) {
	$hex_subject = sanitize_text_field( wp_unslash( $_GET['subject'] ) );
	if ( isset( $hex_topic_map[ $hex_subject ] ) ) {
		$hex_contact_defaults['topic'] = $hex_topic_map[ $hex_subject ];
	}
}

$hex_prefill_lines = array();

if ( isset( $_GET['from'] ) && '' !== trim( (string) $_GET['from'] ) ) {
	$hex_prefill_lines[] = 'Transfer from: ' . sanitize_text_field( wp_unslash( $_GET['from'] ) );
}

if ( isset( $_GET['to'] ) && '' !== trim( (string) $_GET['to'] ) ) {
	$hex_prefill_lines[] = 'Transfer to: ' . sanitize_text_field( wp_unslash( $_GET['to'] ) );
}

if ( isset( $_GET['date'] ) && '' !== trim( (string) $_GET['date'] ) ) {
	$hex_prefill_lines[] = 'Date: ' . sanitize_text_field( wp_unslash( $_GET['date'] ) );
}

if ( isset( $_GET['time'] ) && '' !== trim( (string) $_GET['time'] ) ) {
	$hex_prefill_lines[] = 'Pick-up time: ' . sanitize_text_field( wp_unslash( $_GET['time'] ) );
}

if ( isset( $_GET['passengers'] ) && '' !== trim( (string) $_GET['passengers'] ) ) {
	$hex_prefill_lines[] = 'Passengers: ' . sanitize_text_field( wp_unslash( $_GET['passengers'] ) );
}

if ( isset( $_GET['luggage'] ) && '' !== trim( (string) $_GET['luggage'] ) ) {
	$hex_prefill_lines[] = 'Luggage: ' . sanitize_text_field( wp_unslash( $_GET['luggage'] ) );
}

if ( isset( $_GET['notes'] ) && '' !== trim( (string) $_GET['notes'] ) ) {
	$hex_prefill_lines[] = 'Notes: ' . sanitize_textarea_field( wp_unslash( $_GET['notes'] ) );
}

if ( isset( $_GET['message'] ) && '' !== trim( (string) $_GET['message'] ) ) {
	$hex_prefill_lines[] = sanitize_textarea_field( wp_unslash( $_GET['message'] ) );
}

if ( $hex_prefill_lines ) {
	$hex_contact_defaults['message'] = implode( "\n", $hex_prefill_lines );
}

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['hex_contact_request'] ) ) {
	$hex_contact_defaults['name']    = isset( $_POST['hex_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_contact_name'] ) ) : '';
	$hex_contact_defaults['email']   = isset( $_POST['hex_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['hex_contact_email'] ) ) : '';
	$hex_contact_defaults['phone']   = isset( $_POST['hex_contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_contact_phone'] ) ) : '';
	$hex_contact_defaults['topic']   = isset( $_POST['hex_contact_topic'] ) ? sanitize_text_field( wp_unslash( $_POST['hex_contact_topic'] ) ) : 'Transfer';
	$hex_contact_defaults['message'] = isset( $_POST['hex_contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['hex_contact_message'] ) ) : '';

	$hex_contacts_url = home_url( '/contacts/' );

	if ( ! isset( $_POST['hex_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hex_contact_nonce'] ) ), 'hex_contact_request' ) ) {
		$hex_contact_notice_title = 'Request Not Sent';
		$hex_contact_notice       = 'Security check failed. Please refresh the page and try again.';
		$hex_contact_notice_class = 'is-error';
	} elseif ( '' === $hex_contact_defaults['name'] || '' === $hex_contact_defaults['email'] || '' === $hex_contact_defaults['message'] || ! is_email( $hex_contact_defaults['email'] ) ) {
		$hex_contact_notice_title = 'Missing Information';
		$hex_contact_notice       = 'Please enter your name, a valid email address, and a short message before sending.';
		$hex_contact_notice_class = 'is-error';
	} else {
		$hex_mail_to = 'info@hvarexcursions.com';
		if ( ! is_email( $hex_mail_to ) ) {
			$hex_mail_to = get_option( 'admin_email' );
		}

		$hex_mail_subject = sprintf( 'Contact Request: %s', $hex_contact_defaults['topic'] );
		$hex_mail_lines   = array(
			'New contact request from the Contacts page.',
			'',
			'Name: ' . $hex_contact_defaults['name'],
			'Email: ' . $hex_contact_defaults['email'],
			'Phone: ' . ( $hex_contact_defaults['phone'] ? $hex_contact_defaults['phone'] : 'Not provided' ),
			'Topic: ' . $hex_contact_defaults['topic'],
			'',
			'Message:',
			$hex_contact_defaults['message'],
		);

		$hex_headers = array( 'Reply-To: ' . $hex_contact_defaults['name'] . ' <' . $hex_contact_defaults['email'] . '>' );
		$hex_sent    = wp_mail( $hex_mail_to, $hex_mail_subject, implode( "\n", $hex_mail_lines ), $hex_headers );

		if ( $hex_sent ) {
			wp_safe_redirect( add_query_arg( 'contact_status', 'sent', $hex_contacts_url ) );
			exit;
		}

		$hex_contact_notice_title = 'Request Not Sent';
		$hex_contact_notice       = 'We could not send your message right now. Please try again later or contact us directly on WhatsApp.';
		$hex_contact_notice_class = 'is-error';
	}
}

if ( 'sent' === $hex_contact_status ) {
	$hex_contact_notice_title = 'Message Sent';
	$hex_contact_notice       = 'Your message has been sent. We will get back to you shortly.';
	$hex_contact_notice_class = 'is-success';
}

catamaran_child_render_shell_start();

$hex_links = array(
	'home'      => home_url( '/' ),
	'rentals'   => home_url( '/rentals/' ),
	'excursions'=> home_url( '/excursions/' ),
	'transfers' => home_url( '/transfers/' ),
	'contact'   => home_url( '/contacts/' ),
	'whatsapp'  => 'https://wa.me/385958700479',
	'phone'     => 'tel:+385958700479',
	'email'     => 'mailto:info@hvarexcursions.com',
	'map'       => 'https://maps.app.goo.gl/s7JPCa7Ptd1R5xKWA',
);

$hex_method_cards = array(
	array(
		'eyebrow' => 'Call',
		'title'   => '+385 95 87 00 479',
		'copy'    => 'For direct planning, urgent arrivals, and same-day coordination around Hvar.',
		'link'    => $hex_links['phone'],
		'label'   => 'Call now',
	),
	array(
		'eyebrow' => 'WhatsApp',
		'title'   => 'Fastest Reply',
		'copy'    => 'The quickest way to arrange rentals, transfers, and custom island plans.',
		'link'    => $hex_links['whatsapp'],
		'label'   => 'WhatsApp us',
	),
	array(
		'eyebrow' => 'Email',
		'title'   => 'info@hvarexcursions.com',
		'copy'    => 'Best for longer requests, villa coordination, and multi-stop itinerary planning.',
		'link'    => $hex_links['email'],
		'label'   => 'Send email',
	),
);
?>
<div class="hex-homepage hex-contacts-shell" data-hex-contact-root>
	<header class="hex-contacts-hero" id="top">
		<div class="hex-contacts-hero__media" aria-hidden="true" style="background-image:url('<?php echo esc_url( catamaran_child_asset_image_url( 'contacts/hero_contacts.jpg', 'https://www.hvarexcursions.com/img/service-excursions.jpg' ) ); ?>');"></div>
		<div class="hex-contacts-hero__overlay" aria-hidden="true"></div>

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
				<input id="hex-search-input" type="search" name="s" placeholder="Search rentals, excursions, transfers..." value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit">Search</button>
			</form>
		</div>

		<aside class="hex-sidepanel" id="hex-sidepanel" hidden aria-label="Quick panel">
			<div class="hex-sidepanel__inner">
				<button class="hex-sidepanel__close" type="button" aria-label="Close panel">&times;</button>
				<h3>Need a quick answer?</h3>
				<p>Message us directly for rentals, excursions, transfers, or a custom plan around Hvar and the surrounding islands.</p>
				<ul>
					<li><a href="<?php echo esc_url( $hex_links['phone'] ); ?>">+385 95 87 00 479</a></li>
					<li><a href="<?php echo esc_url( $hex_links['email'] ); ?>">info@hvarexcursions.com</a></li>
					<li><a href="<?php echo esc_url( $hex_links['map'] ); ?>" target="_blank" rel="noopener">Open our location</a></li>
				</ul>
				<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
			</div>
		</aside>
		<div class="hex-sidepanel-overlay" hidden></div>

		<div class="hex-contacts-hero__content">
			<div class="hex-contacts-hero__copy">
				<span class="hex-contacts-hero__eyebrow">Contact Concierge</span>
				<h1>Let&rsquo;s plan your next day on the Adriatic.</h1>
				<p>Whether you need a private transfer, the right boat, or a full day around Hvar and Vis, this page should feel like the easiest final step in the whole site.</p>
				<div class="hex-contacts-hero__cta">
					<a class="hex-btn hex-btn--primary" href="#contact-form">Send a message</a>
					<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
				</div>
			</div>

			<div class="hex-contacts-hero-card">
				<span class="hex-rentals-chip">Based in Hvar</span>
				<h2>Direct contact, fast coordination, local support.</h2>
				<ul>
					<li><strong>Phone</strong><span><a href="<?php echo esc_url( $hex_links['phone'] ); ?>">+385 95 87 00 479</a></span></li>
					<li><strong>Email</strong><span><a href="<?php echo esc_url( $hex_links['email'] ); ?>">info@hvarexcursions.com</a></span></li>
					<li><strong>Address</strong><span>Biskupa Dubokovica 22, 21450 Hvar</span></li>
				</ul>
				<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['map'] ); ?>" target="_blank" rel="noopener">Open in Google Maps</a>
			</div>
		</div>
	</header>

	<main class="hex-main hex-contacts-main">
		<section class="hex-contacts-methods">
			<div class="hex-container">
				<div class="hex-contacts-methods__grid">
					<?php foreach ( $hex_method_cards as $card ) : ?>
						<article class="hex-contacts-method-card">
							<span class="hex-rentals-chip"><?php echo esc_html( $card['eyebrow'] ); ?></span>
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<p><?php echo esc_html( $card['copy'] ); ?></p>
							<a class="hex-btn hex-btn--small" href="<?php echo esc_url( $card['link'] ); ?>"<?php echo 'mailto:' === substr( $card['link'], 0, 7 ) || 'tel:' === substr( $card['link'], 0, 4 ) ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo esc_html( $card['label'] ); ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hex-contacts-form-section" id="contact-form">
			<div class="hex-container">
				<div class="hex-contacts-form-grid">
					<div class="hex-contacts-form-panel">
						<div class="hex-contacts-section__heading">
							<span class="hex-rentals-chip">Write To Us</span>
							<h2>Tell us what you need and we&rsquo;ll point you in the right direction.</h2>
						</div>

						<?php if ( $hex_contact_notice ) : ?>
							<div class="hex-contacts-notice <?php echo esc_attr( $hex_contact_notice_class ); ?>">
								<strong><?php echo esc_html( $hex_contact_notice_title ); ?></strong>
								<p><?php echo esc_html( $hex_contact_notice ); ?></p>
							</div>
						<?php endif; ?>

						<form class="hex-contacts-form" method="post" action="<?php echo esc_url( $hex_links['contact'] ); ?>">
							<?php wp_nonce_field( 'hex_contact_request', 'hex_contact_nonce' ); ?>
							<input type="hidden" name="hex_contact_request" value="1">

							<div class="hex-contacts-form__topics">
								<?php foreach ( $hex_topics as $topic ) : ?>
									<button class="hex-contacts-topic<?php echo $hex_contact_defaults['topic'] === $topic ? ' is-active' : ''; ?>" type="button" data-hex-contact-topic="<?php echo esc_attr( $topic ); ?>"><?php echo esc_html( $topic ); ?></button>
								<?php endforeach; ?>
							</div>

							<div class="hex-contacts-form__row">
								<label>
									<span>Name</span>
									<input type="text" name="hex_contact_name" value="<?php echo esc_attr( $hex_contact_defaults['name'] ); ?>" placeholder="Your name">
								</label>
								<label>
									<span>Email</span>
									<input type="email" name="hex_contact_email" value="<?php echo esc_attr( $hex_contact_defaults['email'] ); ?>" placeholder="name@example.com">
								</label>
							</div>

							<div class="hex-contacts-form__row">
								<label>
									<span>Phone</span>
									<input type="text" name="hex_contact_phone" value="<?php echo esc_attr( $hex_contact_defaults['phone'] ); ?>" placeholder="+385 ...">
								</label>
								<label>
									<span>Topic</span>
									<select name="hex_contact_topic" data-hex-contact-topic-select>
										<?php foreach ( $hex_topics as $topic ) : ?>
											<option value="<?php echo esc_attr( $topic ); ?>"<?php selected( $hex_contact_defaults['topic'], $topic ); ?>><?php echo esc_html( $topic ); ?></option>
										<?php endforeach; ?>
									</select>
								</label>
							</div>

							<label class="hex-contacts-form__message">
								<span>Message</span>
								<textarea rows="5" name="hex_contact_message" placeholder="Tell us your dates, how many guests you have, and what kind of day or transfer you want."><?php echo esc_textarea( $hex_contact_defaults['message'] ); ?></textarea>
							</label>

							<div class="hex-contacts-form__actions">
								<button class="hex-btn hex-btn--primary" type="submit">Send message</button>
								<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp instead</a>
							</div>
						</form>
					</div>

					<aside class="hex-contacts-info-panel">
						<span class="hex-rentals-chip">Quick Info</span>
						<h3>Contact details that feel clear, not cluttered.</h3>
						<ul class="hex-contacts-info-list">
							<li><strong>Phone</strong><a href="<?php echo esc_url( $hex_links['phone'] ); ?>">+385 95 87 00 479</a></li>
							<li><strong>WhatsApp</strong><a href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">Open chat</a></li>
							<li><strong>Email</strong><a href="<?php echo esc_url( $hex_links['email'] ); ?>">info@hvarexcursions.com</a></li>
							<li><strong>Address</strong><span>Biskupa Dubokovica 22, 21450 Hvar</span></li>
						</ul>
						<p>From this page we can direct you to the right boat, transfer, or excursion plan without making you search around the site again.</p>
					</aside>
				</div>
			</div>
		</section>

		<section class="hex-contacts-map">
			<div class="hex-container">
				<div class="hex-contacts-map__grid">
					<div class="hex-contacts-map__copy">
						<span class="hex-rentals-chip">Location</span>
						<h2>Find us in Hvar and use the page as your calm final touchpoint.</h2>
						<p>We wanted the Contacts page to feel less like a default template and more like a polished concierge desk. The map anchors that with a real local reference point.</p>
						<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['map'] ); ?>" target="_blank" rel="noopener">Open location</a>
					</div>
					<div class="hex-contacts-map__frame">
						<iframe title="Hvar Excursions location" src="https://www.google.com/maps?q=Biskupa%20Dubokovica%2022%2C%2021450%20Hvar&z=16&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
				</div>
			</div>
		</section>

		<section class="hex-contacts-cta">
			<div class="hex-container">
				<div class="hex-contacts-cta__inner">
					<span class="hex-rentals-chip">Fastest Channel</span>
					<h2>Need a quick answer? WhatsApp is still the fastest way to coordinate the details.</h2>
					<div class="hex-contacts-cta__actions">
						<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
						<a class="hex-btn hex-btn--ghost" href="<?php echo esc_url( $hex_links['phone'] ); ?>">Call now</a>
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
					<p><a href="<?php echo esc_url( $hex_links['phone'] ); ?>">+385 95 87 00 479</a></p>
					<p><a href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">Whatsapp and Viber</a></p>
				</div>
				<div>
					<h3>Email</h3>
					<p><a href="<?php echo esc_url( $hex_links['email'] ); ?>">info@hvarexcursions.com</a></p>
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

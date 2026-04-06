<?php
/**
 * Custom front page for Hvar Excursions remake in the Catamaran child theme.
 */

catamaran_child_render_shell_start();

$hex_links = array(
	'home'        => home_url( '/' ),
	'services'    => home_url( '/our-services/' ),
	'rentals'     => home_url( '/rentals/' ),
	'excursions'  => home_url( '/excursions/' ),
	'transfers'   => home_url( '/transfers/' ),
	'contact'     => home_url( '/contacts/' ),
	'video'       => 'https://www.youtube.com/watch?v=Xveg6s-j5c0',
);

$hex_destination_rows = array(
	array(
		home_url( '/wp-content/uploads/2021/10/post38-copyright.jpg' ),
		home_url( '/wp-content/uploads/2021/10/post39-copyright.jpg' ),
		home_url( '/wp-content/uploads/2021/10/post40-copyright.jpg' ),
	),
	array(
		home_url( '/wp-content/uploads/2021/10/post41-copyright.jpg' ),
		home_url( '/wp-content/uploads/2021/10/post42-copyright.jpg' ),
		home_url( '/wp-content/uploads/2021/10/post43-copyright.jpg' ),
	),
);
?>
<div class="hex-homepage">
	<header class="hex-hero" id="top">
		<div class="hex-hero__media" aria-hidden="true">
			<div class="hex-hero-slider" data-hex-hero-slider>
				<div class="hex-hero-slide is-active" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-rentals.jpg' ) ); ?>');"></div>
				<div class="hex-hero-slide" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-transfers.jpg' ) ); ?>');"></div>
				<div class="hex-hero-slide" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-excursions.jpg' ) ); ?>');"></div>
			</div>
			<div class="hex-hero__overlay"></div>
		</div>

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
				<a class="hex-icon-btn hex-whatsapp-btn" href="https://wa.me/385958700479" target="_blank" rel="noopener" title="Chat on WhatsApp">
					<span class="hex-icon hex-icon--whatsapp" aria-hidden="true">
						<svg viewBox="0 0 24 24" role="presentation" focusable="false" aria-hidden="true">
							<path d="M20.52 3.48A11.86 11.86 0 0 0 12.07 0C5.51 0 .17 5.34.17 11.9c0 2.1.55 4.16 1.59 5.97L0 24l6.31-1.66a11.86 11.86 0 0 0 5.76 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.46-8.43ZM12.08 21.8h-.01a9.83 9.83 0 0 1-5.01-1.37l-.36-.21-3.75.99 1-3.65-.23-.37a9.8 9.8 0 0 1-1.52-5.28c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.02 6.96 2.88a9.79 9.79 0 0 1 2.87 6.95c0 5.42-4.41 9.82-9.8 9.82Zm5.39-7.35c-.29-.15-1.71-.84-1.98-.94-.27-.1-.47-.15-.67.15-.2.3-.77.94-.95 1.13-.17.2-.35.22-.64.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.45-1.72-1.62-2.01-.17-.3-.02-.46.13-.61.13-.13.29-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.08-.8.37-.27.3-1.04 1.02-1.04 2.49s1.06 2.88 1.21 3.08c.15.2 2.09 3.2 5.07 4.49.71.31 1.26.49 1.69.62.71.22 1.36.19 1.87.12.57-.08 1.71-.7 1.95-1.38.24-.67.24-1.25.17-1.37-.07-.12-.27-.2-.57-.35Z"></path>
						</svg>
					</span>
					<span class="screen-reader-text">Send a WhatsApp message to +385 95 87 00 479</span>
				</a>
				<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['transfers'] ); ?>">Book now</a>
				<button class="hex-nav__toggle" type="button" aria-expanded="false" aria-controls="hex-nav-menu">Menu</button>
			</div>
		</nav>

		<div class="hex-search-drawer" id="hex-search-drawer" hidden>
			<form role="search" method="get" class="hex-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="hex-search-input" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'catamaran' ); ?></label>
				<input id="hex-search-input" type="search" name="s" placeholder="Search tours, rentals, destinations..." value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit">Search</button>
			</form>
		</div>

		<aside class="hex-sidepanel" id="hex-sidepanel" hidden aria-label="Quick panel">
			<div class="hex-sidepanel__inner">
				<button class="hex-sidepanel__close" type="button" aria-label="Close panel">&times;</button>
				<h3>Need help planning?</h3>
				<p>Call, message, or book directly and we will build the perfect Hvar route for you.</p>
				<ul>
					<li><a href="tel:+385958700479">+385 95 87 00 479</a></li>
					<li><a href="mailto:info@hvarexcursions.com">info@hvarexcursions.com</a></li>
					<li><a href="<?php echo esc_url( $hex_links['contact'] ); ?>">Contact page</a></li>
				</ul>
				<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['transfers'] ); ?>">Book now</a>
			</div>
		</aside>
		<div class="hex-sidepanel-overlay" hidden></div>

		<div class="hex-hero__content" data-hex-hero-content>
			<article class="hex-hero-copy is-active" data-hex-hero-copy>
				<h1>Boats &amp; Speedbosts Rentals</h1>
				<p>Rent Our Boats with or without Skipper</p>
				<div class="hex-hero__cta-wrap">
					<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['rentals'] ); ?>">Discover More</a>
				</div>
			</article>

			<article class="hex-hero-copy" data-hex-hero-copy>
				<h1>Boat Taxi &amp; Transfers</h1>
				<p>Get from Split Airport directly to Hvar Harbour</p>
				<div class="hex-hero__cta-wrap">
					<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['transfers'] ); ?>">Discover More</a>
				</div>
			</article>

			<article class="hex-hero-copy" data-hex-hero-copy>
				<h1>Excursions - Private &amp; Group Tours</h1>
				<p>Discover Gems around Hvar and Caves around Vis</p>
				<div class="hex-hero__cta-wrap">
					<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['excursions'] ); ?>">Discover More</a>
				</div>
			</article>

			<div class="hex-hero-pagination" aria-label="Hero slider navigation">
				<button type="button" class="hex-hero-dot is-active" data-hex-hero-dot="0" aria-label="Slide 1"></button>
				<button type="button" class="hex-hero-dot" data-hex-hero-dot="1" aria-label="Slide 2"></button>
				<button type="button" class="hex-hero-dot" data-hex-hero-dot="2" aria-label="Slide 3"></button>
			</div>
		</div>

		<div class="hex-hero-arrows" aria-label="Hero slide controls">
			<button type="button" class="hex-hero-arrow hex-hero-arrow--prev" data-hex-hero-arrow="prev" aria-label="Previous slide">
				<span class="screen-reader-text"><?php esc_html_e( 'Previous slide', 'catamaran' ); ?></span>
			</button>
			<button type="button" class="hex-hero-arrow hex-hero-arrow--next" data-hex-hero-arrow="next" aria-label="Next slide">
				<span class="screen-reader-text"><?php esc_html_e( 'Next slide', 'catamaran' ); ?></span>
			</button>
		</div>
	</header>

	<main class="hex-main">
		<section class="hex-intro">
			<div class="hex-container">
				<p>
					Rent a boat in Hvar with Hvar Excursions starting at EUR160/day. Explore the Adriatic with our top-quality boats!<br>
					Or book a private speedboat transfer from Split to Hvar in just 60 minutes. Contact us for prices!
				</p>
			</div>
		</section>

		<section class="hex-services">
			<div class="hex-container">
				<h2>Our Services</h2>
				<div class="hex-services__grid">
					<article class="hex-card">
					<a href="<?php echo esc_url( $hex_links['rentals'] ); ?>" class="hex-card__media" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-rentals.jpg' ) ); ?>');">
							<div class="hex-card__shade"></div>
							<div class="hex-card__text">
								<h3>Rentals</h3>
								<ul>
									<li>Speedboats and Boats</li>
									<li>Cars and Scooters</li>
									<li>Wakeboard and Water Ski</li>
								</ul>
								<span class="hex-btn hex-btn--small">View Details</span>
							</div>
						</a>
						<p>
							Hire boats, speedboats, RIB boats, cars and scooters. Our boat rentals can come with a local professional skipper or as self-drive options.
						</p>
					</article>

					<article class="hex-card">
					<a href="<?php echo esc_url( $hex_links['excursions'] ); ?>" class="hex-card__media" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-excursions.jpg' ) ); ?>');">
							<div class="hex-card__shade"></div>
							<div class="hex-card__text">
								<h3>Excursions</h3>
								<ul>
									<li>Island Hopping</li>
									<li>Blue and Green Cave</li>
									<li>Golden Horn Beach</li>
								</ul>
								<span class="hex-btn hex-btn--small">View Details</span>
							</div>
						</a>
						<p>
							Flexible daily excursions by comfortable boats with cave visits, private coves, and custom island routes around Hvar, Brac, Korcula, and Vis.
						</p>
					</article>

					<article class="hex-card">
					<a href="<?php echo esc_url( $hex_links['transfers'] ); ?>" class="hex-card__media" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-transfers.jpg' ) ); ?>');">
							<div class="hex-card__shade"></div>
							<div class="hex-card__text">
								<h3>Transfers</h3>
								<ul>
									<li>Split Airport via Speedboat</li>
									<li>Korcula, Zadar, Dubrovnik</li>
									<li>Various Destinations</li>
								</ul>
								<span class="hex-btn hex-btn--small">View Details</span>
							</div>
						</a>
						<p>
							Private speedboat and taxi transfers to airports, ports, and islands with fast custom routing across Dalmatia.
						</p>
					</article>
				</div>
			</div>
		</section>

		<section class="hex-destinations">
			<div class="hex-container">
				<h2 class="hex-destinations__headline">
					In our arrangement or if you like, in your own...<br>
					Explore beautiful destinations like
					<span class="hex-destinations__typed-word-wrap">
						<span class="hex-destinations__typed-word" data-hex-destination-typed data-words='["Hvar","Pakleni Islands","Korcula","Blue Cave","Bol","Dubrovnik"]'>Hvar</span>
					</span>.
				</h2>
				<div class="hex-destinations__marquee" aria-label="Featured destinations">
					<?php foreach ( $hex_destination_rows as $row_index => $row_images ) : ?>
						<div class="hex-destinations__lane <?php echo 0 === $row_index ? 'hex-destinations__lane--left' : 'hex-destinations__lane--right'; ?>">
							<div class="hex-destinations__track">
								<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
									<div class="hex-destinations__track-group" <?php echo 1 === $copy ? 'aria-hidden="true"' : ''; ?>>
										<?php foreach ( $row_images as $image_url ) : ?>
											<a href="<?php echo esc_url( $hex_links['contact'] ); ?>" class="hex-destination-link">
												<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async">
											</a>
										<?php endforeach; ?>
									</div>
								<?php endfor; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hex-experience">
			<div class="hex-container">
				<div class="hex-experience__copy">
					<h2>Experience</h2>
					<p>
						Meet our crew and enjoy authentic footage from Hvar and our rental experience.
						Come and experience Hvar summer with us.
					</p>
				</div>
				<a class="hex-experience__video" href="<?php echo esc_url( $hex_links['video'] ); ?>" target="_blank" rel="noopener" aria-label="Play Hvar Excursions video">
					<img src="<?php echo esc_url( catamaran_child_asset_image_url( 'transfers/transfers_1.jpg', catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/service-transfers.jpg' ) ) ); ?>" alt="Watch the Hvar experience video" loading="lazy" decoding="async">
					<span class="hex-experience__play" aria-hidden="true">
						<span class="hex-experience__play-inner">
							<svg viewBox="0 0 12 16" role="presentation" focusable="false" aria-hidden="true">
								<path d="M1 0.7L11 8L1 15.3V0.7Z"></path>
							</svg>
							<span>PLAY</span>
						</span>
					</span>
				</a>
			</div>
		</section>

		<div class="hex-video-modal" data-hex-video-modal hidden aria-hidden="true">
			<div class="hex-video-modal__backdrop" data-hex-video-close></div>
			<div class="hex-video-modal__dialog" role="dialog" aria-modal="true" aria-label="Experience video">
				<button class="hex-video-modal__close" type="button" data-hex-video-close aria-label="<?php esc_attr_e( 'Close video', 'catamaran' ); ?>">&times;</button>
				<div class="hex-video-modal__frame-wrap">
					<iframe class="hex-video-modal__frame" data-hex-video-frame src="" title="Hvar Excursions video" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; accelerometer; gyroscope" allowfullscreen loading="lazy"></iframe>
				</div>
			</div>
		</div>
	</main>

	<footer class="hex-footer">
		<div class="hex-container">
			<div class="hex-footer__cards">
				<div>
					<h3>Phone</h3>
					<p><a href="tel:+385958700479">+385 95 87 00 479</a></p>
					<p><a href="https://api.whatsapp.com/send?phone=385958700479" target="_blank" rel="noopener">Whatsapp and Viber</a></p>
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

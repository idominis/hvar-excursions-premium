<?php
/**
 * Virtual excursions page for Hvar Excursions.
 */

catamaran_child_render_shell_start();

$hex_links = array(
    'home'       => home_url( '/' ),
    'rentals'    => home_url( '/rentals/' ),
    'excursions' => home_url( '/excursions/' ),
    'transfers'  => home_url( '/transfers/' ),
    'contact'    => home_url( '/contacts/' ),
    'whatsapp'   => 'https://wa.me/385958700479',
);

$excursions = array(
    array(
        'id'          => 'south-hvar',
        'chip'        => 'Signature Coast Day',
        'title'       => 'Southern Hvar Coast & Pakleni Islands',
        'price'       => 'From 280 EUR',
        'duration'    => 'Half day or full day',
        'best_for'    => 'First-time visitors, couples, easy island hopping',
        'description' => 'A flexible private route that keeps the focus on swimming, scenic bays, and the iconic south side of Hvar. It is the relaxed, photogenic excursion that fits families, couples, and guests who want the best of Hvar without a very long offshore crossing.',
        'stops'       => array( 'Red Rocks', 'Dubovica', 'Mekicevica', 'Mlini', 'Stipanska', 'Palmizana' ),
        'highlights'  => array( 'Cliff-jump and swim stops', 'Private skipper or self-drive setups', 'Lunch stop can be tailored to your group', 'Easy to adapt to wind and weather on the day' ),
        'gallery'     => array(
            'https://www.hvarexcursions.com/img/excursions/tour-hvar/thumb/tour_hvar_red_rocks_02.jpg',
            'https://www.hvarexcursions.com/img/excursions/tour-hvar/thumb/tour_hvar_mekicevica_02.jpg',
            'https://www.hvarexcursions.com/img/excursions/tour-hvar/thumb/tour_hvar_pakleni_otoci_02.jpg',
        ),
        'note'        => 'Private route with flexible timing and custom lunch stops.',
    ),
    array(
        'id'          => 'vis-blue-cave',
        'chip'        => 'Full-Day Premium Adventure',
        'title'       => 'Vis, Blue Cave & Stiniva',
        'price'       => 'From 550 EUR',
        'duration'    => 'Full day excursion',
        'best_for'    => 'Adventure seekers, full-day private groups, iconic cave route',
        'description' => 'This is the high-impact route for guests who want the big Adriatic day: Blue Cave, Green Cave, Stiniva, Budikovac, and a long-range speedboat run that feels like a proper island expedition. It is more premium, more cinematic, and worth building around the right boat.',
        'stops'       => array( 'Blue Cave', 'Green Cave', 'Stiniva', 'Budikovac', 'Stoncica', 'Palmizana optional' ),
        'highlights'  => array( 'Long-range private tour to Vis', 'Snorkeling masks and ice box included', 'Route can adapt to sea conditions', 'Group-tour alternative available on request' ),
        'gallery'     => array(
            'https://www.hvarexcursions.com/img/excursions/tour-vis/thumb/tour_hvar_blue_cave_01.jpg',
            'https://www.hvarexcursions.com/img/excursions/tour-vis/thumb/tour_hvar_stiniva_01.jpg',
            'https://www.hvarexcursions.com/img/excursions/tour-vis/thumb/tour_hvar_green_cave_01.jpg',
        ),
        'note'        => 'Best reserved with a stronger speedboat for comfort on the longer crossing.',
    ),
);

$boat_recommendations = array(
    array(
        'chip'        => 'Easy Day Setup',
        'title'       => 'Betina 30hp',
        'copy'        => 'Best for simple south-side cruising, beach stops, and easy Pakleni days close to Hvar.',
        'ideal'       => 'Pakleni and nearby coves',
        'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg',
        'link'        => add_query_arg( array( 'subject' => 'Excursion Inquiry', 'boat' => 'Betina 30hp' ), $hex_links['contact'] ),
    ),
    array(
        'chip'        => 'Balanced Private Tour',
        'title'       => 'Scar 1 Sivi 150hp',
        'copy'        => 'A strong all-round private excursion boat for Southern Hvar, Red Rocks, Dubovica, and fast scenic hopping.',
        'ideal'       => 'South Hvar coast',
        'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
        'link'        => add_query_arg( array( 'subject' => 'Excursion Inquiry', 'boat' => 'Scar 1 Sivi 150hp' ), $hex_links['contact'] ),
    ),
    array(
        'chip'        => 'Flagship Experience',
        'title'       => 'Raptor',
        'copy'        => 'The premium match for Blue Cave and longer full-day routes when comfort, power, and arrival style all matter.',
        'ideal'       => 'Vis and Blue Cave',
        'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/thumb/raptor-alesta-hvar-excursions-rentals.jpg',
        'link'        => add_query_arg( array( 'subject' => 'Excursion Inquiry', 'boat' => 'Raptor' ), $hex_links['contact'] ),
    ),
);

foreach ( $excursions as &$excursion ) {
    $excursion['gallery'] = array_map( 'catamaran_child_localize_image_url', $excursion['gallery'] );
}
unset( $excursion );

foreach ( $boat_recommendations as &$boat_recommendation ) {
    $boat_recommendation['image'] = catamaran_child_localize_image_url( $boat_recommendation['image'] );
}
unset( $boat_recommendation );

$excursions[0]['gallery'] = array(
    catamaran_child_asset_image_url( 'excursions/red_rocks_hvar_1.jpg', $excursions[0]['gallery'][0] ),
    catamaran_child_asset_image_url( 'excursions/green_cave_2.jpg', $excursions[0]['gallery'][1] ),
    catamaran_child_asset_image_url( 'excursions/golden_horn_bol_1.jpg', $excursions[0]['gallery'][2] ),
);

$excursions[1]['gallery'] = array(
    catamaran_child_asset_image_url( 'excursions/blue_cave_1.jpg', $excursions[1]['gallery'][0] ),
    catamaran_child_asset_image_url( 'excursions/stiniva_vis_1.jpg', $excursions[1]['gallery'][1] ),
    catamaran_child_asset_image_url( 'excursions/green_cave_1.jpg', $excursions[1]['gallery'][2] ),
);

$faq_items = array(
    array(
        'question' => 'Can we customize the route?',
        'answer'   => 'Yes. The new page is designed around curated routes, but the final plan can still be adjusted to your group, weather, and preferred swim or lunch stops.',
    ),
    array(
        'question' => 'Do excursions include skipper and equipment?',
        'answer'   => 'Most private excursion setups are arranged with skipper, and the Vis route also highlights snorkeling masks and an ice box as part of the experience.',
    ),
    array(
        'question' => 'Which tour is better for families?',
        'answer'   => 'Southern Hvar and Pakleni is the easier, calmer choice for families or guests who want a gentler day with shorter crossings and more flexible stops.',
    ),
    array(
        'question' => 'Do you also offer a group Blue Cave option?',
        'answer'   => 'Yes. The Vis section keeps the private premium route as the main focus, but we can also direct guests toward a more affordable group-tour option if that suits the trip better.',
    ),
);

$hex_render_gallery = static function( $gallery, $title, $class_name = '' ) {
    if ( empty( $gallery ) ) {
        return;
    }

    $gallery_json = wp_json_encode( array_values( $gallery ) );
    ?>
    <div class="hex-rental-gallery <?php echo esc_attr( $class_name ); ?>" data-hex-gallery data-hex-gallery-images="<?php echo esc_attr( $gallery_json ); ?>" data-hex-gallery-title="<?php echo esc_attr( $title ); ?>">
        <button class="hex-rental-gallery__main" type="button" data-hex-gallery-open aria-label="<?php echo esc_attr( 'Open gallery for ' . $title ); ?>">
            <img src="<?php echo esc_url( $gallery[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async" data-hex-gallery-main>
            <span class="hex-rental-gallery__badge"><?php echo esc_html( count( $gallery ) ); ?> photos</span>
        </button>
        <div class="hex-rental-gallery__thumbs" role="tablist" aria-label="<?php echo esc_attr( $title . ' gallery thumbnails' ); ?>">
            <?php foreach ( $gallery as $index => $image_url ) : ?>
                <button class="hex-rental-gallery__thumb<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" data-hex-gallery-thumb="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( $title . ' photo ' . ( $index + 1 ) ); ?>">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async">
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
};
?>
<div class="hex-homepage hex-excursions-shell">
    <header class="hex-excursions-hero" id="top">
        <div class="hex-excursions-hero__media" aria-hidden="true" style="background-image:url('<?php echo esc_url( catamaran_child_localize_image_url( 'https://www.hvarexcursions.com/img/excursions/tour-vis/thumb/tour_hvar_blue_cave_01.jpg' ) ); ?>');"></div>
        <div class="hex-excursions-hero__overlay" aria-hidden="true"></div>

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
                <input id="hex-search-input" type="search" name="s" placeholder="Search tours, boats, caves, destinations..." value="<?php echo esc_attr( get_search_query() ); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <aside class="hex-sidepanel" id="hex-sidepanel" hidden aria-label="Quick panel">
            <div class="hex-sidepanel__inner">
                <button class="hex-sidepanel__close" type="button" aria-label="Close panel">&times;</button>
                <h3>Plan your excursion day</h3>
                <p>Tell us the route, the vibe, and how private or adventurous you want the day to feel. We will help you match the right tour and boat.</p>
                <ul>
                    <li><a href="tel:+385958700479">+385 95 87 00 479</a></li>
                    <li><a href="mailto:info@hvarexcursions.com">info@hvarexcursions.com</a></li>
                    <li><a href="<?php echo esc_url( $hex_links['contact'] ); ?>">Contact page</a></li>
                </ul>
                <a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request a quote</a>
            </div>
        </aside>
        <div class="hex-sidepanel-overlay" hidden></div>

        <div class="hex-excursions-hero__content">
            <span class="hex-excursions-hero__eyebrow">Private Boat Tours & Day Trips</span>
            <h1>Excursions that feel curated, cinematic, and easy to book.</h1>
            <p>From Southern Hvar coast days to Blue Cave crossings, this page is built around the routes people actually want, with clearer tour storytelling and better boat matching.</p>
            <div class="hex-excursions-hero__cta">
                <a class="hex-btn hex-btn--primary" href="#signature-tours">Explore tours</a>
                <a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request custom tour</a>
            </div>
            <div class="hex-excursions-hero__chips" aria-label="Excursion quick facts">
                <span>Private tours</span>
                <span>Blue Cave</span>
                <span>Pakleni</span>
                <span>Skipper options</span>
                <span>Custom lunch stops</span>
            </div>
        </div>
    </header>

    <main class="hex-main hex-excursions-main">
        <section class="hex-excursions-jump">
            <div class="hex-container">
                <div class="hex-excursions-jump__wrap">
                    <a href="#signature-tours">Signature Tours</a>
                    <a href="#boat-match">Boat Match</a>
                    <a href="#custom-day">Custom Day</a>
                    <a href="#excursions-faq">FAQ</a>
                </div>
            </div>
        </section>

        <section class="hex-excursions-intro">
            <div class="hex-container">
                <div class="hex-excursions-intro__grid">
                    <article>
                        <strong>2 signature routes</strong>
                        <p>One easier Hvar coastline day and one full-day Vis adventure.</p>
                    </article>
                    <article>
                        <strong>Flexible by weather</strong>
                        <p>Routes, stops, and swim timing can shift based on sea conditions.</p>
                    </article>
                    <article>
                        <strong>Boat matched to route</strong>
                        <p>We keep the experience first, then recommend the right boat for it.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="hex-excursions-featured" id="signature-tours">
            <div class="hex-container">
                <div class="hex-excursions-section__heading">
                    <span class="hex-rentals-chip">Signature Tours</span>
                    <h2>A cleaner way to explore the experiences you actually sell.</h2>
                    <p>Instead of a long repeating catalog, the new page leads with your best routes first, then lets guests move naturally toward the right boat and booking option.</p>
                </div>

                <?php foreach ( $excursions as $index => $excursion ) : ?>
                    <article class="hex-excursion-tour" id="<?php echo esc_attr( $excursion['id'] ); ?>">
                        <div class="hex-excursion-tour__media<?php echo 1 === $index % 2 ? ' is-shifted' : ''; ?>">
                            <?php $hex_render_gallery( $excursion['gallery'], $excursion['title'], 'hex-rental-gallery--featured' ); ?>
                        </div>
                        <div class="hex-excursion-tour__copy">
                            <span class="hex-rentals-chip"><?php echo esc_html( $excursion['chip'] ); ?></span>
                            <h2><?php echo esc_html( $excursion['title'] ); ?></h2>
                            <div class="hex-excursion-tour__meta">
                                <span><?php echo esc_html( $excursion['price'] ); ?></span>
                                <span><?php echo esc_html( $excursion['duration'] ); ?></span>
                                <span><?php echo esc_html( $excursion['best_for'] ); ?></span>
                            </div>
                            <p><?php echo esc_html( $excursion['description'] ); ?></p>
                            <div class="hex-excursion-tour__stops">
                                <?php foreach ( $excursion['stops'] as $stop ) : ?>
                                    <span><?php echo esc_html( $stop ); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <ul class="hex-excursion-tour__highlights">
                                <?php foreach ( $excursion['highlights'] as $highlight ) : ?>
                                    <li><?php echo esc_html( $highlight ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="hex-excursion-tour__note"><?php echo esc_html( $excursion['note'] ); ?></p>
                            <div class="hex-excursion-tour__actions">
                                <a class="hex-btn hex-btn--primary" href="<?php echo esc_url( add_query_arg( array( 'subject' => 'Excursion Inquiry', 'tour' => $excursion['title'] ), $hex_links['contact'] ) ); ?>">Request this tour</a>
                                <a class="hex-btn hex-btn--ghost-dark" href="#boat-match">See recommended boats</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="hex-excursions-moods" id="custom-day">
            <div class="hex-container">
                <div class="hex-excursions-section__heading hex-excursions-section__heading--centered">
                    <span class="hex-rentals-chip">Choose By Mood</span>
                    <h2>Not every guest starts with a destination. Some start with the kind of day they want.</h2>
                </div>
                <div class="hex-excursions-moods__grid">
                    <article>
                        <strong>Relax & Swim</strong>
                        <p>Shorter crossings, quieter coves, lunch stop flexibility, and the best of Hvar without the longest runs.</p>
                    </article>
                    <article>
                        <strong>Adventure & Caves</strong>
                        <p>Blue Cave, cliff-framed beaches, snorkeling, and a more dynamic full-day rhythm with bigger landmarks.</p>
                    </article>
                    <article>
                        <strong>Luxury Private Day</strong>
                        <p>Premium speedboat, custom itinerary, comfortable pacing, and more attention to arrival style and service.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="hex-excursions-boats" id="boat-match">
            <div class="hex-container">
                <div class="hex-excursions-section__heading">
                    <span class="hex-rentals-chip">Boat Match</span>
                    <h2>Recommended setups instead of repeating a huge boat list under every route.</h2>
                    <p>This keeps the page lighter while still guiding guests toward the right type of excursion boat for the day they want.</p>
                </div>
                <div class="hex-excursions-boats__grid">
                    <?php foreach ( $boat_recommendations as $boat ) : ?>
                        <article class="hex-excursions-boat-card">
                            <div class="hex-excursions-boat-card__media" style="background-image:url('<?php echo esc_url( $boat['image'] ); ?>');"></div>
                            <div class="hex-excursions-boat-card__body">
                                <span class="hex-rentals-chip"><?php echo esc_html( $boat['chip'] ); ?></span>
                                <h3><?php echo esc_html( $boat['title'] ); ?></h3>
                                <p><?php echo esc_html( $boat['copy'] ); ?></p>
                                <div class="hex-excursions-boat-card__ideal"><?php echo esc_html( $boat['ideal'] ); ?></div>
                                <div class="hex-excursions-boat-card__actions">
                                    <a class="hex-btn hex-btn--small" href="<?php echo esc_url( $boat['link'] ); ?>">Ask for this setup</a>
                                    <a class="hex-excursions-boat-card__link" href="<?php echo esc_url( $hex_links['rentals'] ); ?>">See rentals</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="hex-excursions-faq" id="excursions-faq">
            <div class="hex-container">
                <div class="hex-excursions-section__heading">
                    <span class="hex-rentals-chip">FAQ</span>
                    <h2>Short answers for the questions that usually slow down booking.</h2>
                </div>
                <div class="hex-excursions-faq__list">
                    <?php foreach ( $faq_items as $faq ) : ?>
                        <details>
                            <summary><?php echo esc_html( $faq['question'] ); ?></summary>
                            <p><?php echo esc_html( $faq['answer'] ); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="hex-excursions-cta">
            <div class="hex-container">
                <div class="hex-excursions-cta__inner">
                    <span class="hex-rentals-chip">Ready To Plan</span>
                    <h2>Tell us the route, the group size, and the mood of the day. We will shape the excursion around it.</h2>
                    <div class="hex-excursions-cta__actions">
                        <a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request excursion</a>
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

    <div class="hex-gallery-modal" data-hex-gallery-modal hidden aria-hidden="true">
        <div class="hex-gallery-modal__backdrop" data-hex-gallery-close></div>
        <div class="hex-gallery-modal__dialog" role="dialog" aria-modal="true" aria-label="Excursion gallery">
            <button class="hex-gallery-modal__close" type="button" data-hex-gallery-close aria-label="<?php esc_attr_e( 'Close gallery', 'catamaran' ); ?>">&times;</button>
            <button class="hex-gallery-modal__nav hex-gallery-modal__nav--prev" type="button" data-hex-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'catamaran' ); ?>">&lsaquo;</button>
            <div class="hex-gallery-modal__figure">
                <img src="" alt="" data-hex-gallery-modal-image>
            </div>
            <button class="hex-gallery-modal__nav hex-gallery-modal__nav--next" type="button" data-hex-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'catamaran' ); ?>">&rsaquo;</button>
            <div class="hex-gallery-modal__caption">
                <strong data-hex-gallery-modal-title></strong>
                <span data-hex-gallery-modal-counter></span>
            </div>
        </div>
    </div>
</div>
<?php
catamaran_child_render_shell_end();

<?php
/**
 * Virtual rentals page for Hvar Excursions.
 */

catamaran_child_render_shell_start();

$hex_links = array(
	'home'      => home_url( '/' ),
	'services'  => home_url( '/our-services/' ),
	'rentals'   => home_url( '/rentals/' ),
	'excursions'=> home_url( '/excursions/' ),
	'transfers' => home_url( '/transfers/' ),
	'contact'   => home_url( '/contacts/' ),
	'whatsapp'  => 'https://wa.me/385958700479',
);

$featured_rental = array(
	'title'       => 'Raptor',
	'short_title' => 'Raptor 480HP',
	'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/thumb/raptor-alesta-hvar-excursions-rentals.jpg',
	'gallery'     => array(
		'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals%20(1-1).jpg',
		'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals%20(1-3).jpg',
		'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals%20(1-5).jpg',
		'https://www.hvarexcursions.com/img/rentals/photos/raptor-alesta/raptor-alesta-hvar-excursions-rentals%20(2-1).jpg',
	),
	'description' => 'Meet the Raptor Alesta 480HP, a bold flagship made for guests who want maximum speed, comfort, and presence on the water. It is ideal for luxury day charters, longer island-hopping routes, and unforgettable arrivals around Hvar.',
	'specs'       => array(
		'Length'    => '12.00 m / 37 ft',
		'Engine'    => '1x480HP',
		'Capacity'  => '12+2 guests',
		'Best for'  => 'Luxury full-day charters',
	),
);

$speedboats = array(
	array(
		'title'       => 'Clubman 300hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/joker300hp/joker_jc_clubman_hvar_excursions_rentals(10-1).jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/joker300hp/joker_jc_clubman_hvar_excursions_rentals(10-1).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/joker300hp/joker_jc_clubman_hvar_excursions_rentals(10-4).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/joker300hp/joker_jc_clubman_hvar_excursions_rentals(10-8).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/joker300hp/joker_jc_clubman_hvar_excursions_rentals(20-1).jpg',
		),
		'description' => 'A spacious high-end RIB with serious power, a large sun deck, and skipper-only comfort for premium routes around Hvar, Vis, Korcula, Split, and beyond.',
		'meta'        => array( '300HP', '8.50 m / 28 ft', '12-14 guests' ),
	),
	array(
		'title'       => 'Zar 1 Sivi 300hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/zar300hp/thumb/zar_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/zar300hp/thumb/zar_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zar300hp/Zar75_dry_dock.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zar300hp/zar_hvar_excursions_rentals_13.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zar300hp/zar_hvar_excursions_rentals_17.jpg',
		),
		'description' => 'Top-tier Zar performance with generous deck space, refined ride quality, and the kind of comfort that works beautifully for longer Adriatic exploration.',
		'meta'        => array( '300HP', '8.50 m / 28 ft', '12-14 guests' ),
	),
	array(
		'title'       => 'Baracuda 4 200hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/solemar200hp/thumb/solemar_200hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/solemar200hp/thumb/solemar_200hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar200hp/solemar_150hp_hvar_excursions_rentals%20(2).jpeg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar200hp/solemar_150hp_hvar_excursions_rentals%20(4).jpeg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar200hp/solemar_150hp_hvar_excursions_rentals%20(7).jpeg',
		),
		'description' => 'A comfortable small RIB with a stronger engine, front and rear sunbathing areas, and enough power for relaxed but capable island days.',
		'meta'        => array( '200HP', '7.00 m / 23 ft', '10 guests' ),
	),
	array(
		'title'       => 'BSC 175 hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/thumb/bsc_175hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/thumb/bsc_175hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/bsc_175hp_hvar_excursions_rentals(1_2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/bsc_175hp_hvar_excursions_rentals(2_3).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/bsc175hp/bsc_175hp_hvar_excursions_rentals(3_2).jpg',
		),
		'description' => 'A newer RIB with a lively feel, good sound setup, and a broad lounging deck that makes it a fun all-rounder for Pakleni and Vis routes.',
		'meta'        => array( '175HP', '7.00 m / 23 ft', '10 guests' ),
	),
	array(
		'title'       => 'Baracuda Black 175hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/solemar175hp/thumb/solemar_150hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/solemar175hp/thumb/solemar_150hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar175hp/solemar_150hp_hvar_excursions_rentals%20(1_2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar175hp/solemar_150hp_hvar_excursions_rentals%20(1_4).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/solemar175hp/solemar_150hp_hvar_excursions_rentals%20(1_7).jpg',
		),
		'description' => 'An easy, comfortable RIB for guests who want a spacious small speedboat with reliable performance and a smooth self-drive day around Hvar.',
		'meta'        => array( '175HP', '7.00 m / 23 ft', '10 guests' ),
	),
	array(
		'title'       => 'Scar 1 Sivi 150hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(5).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(8).jpg',
		),
		'description' => 'A stylish open boat from SCAR Marine that balances sporty handling with elegant looks, perfect for couples or smaller groups who still want a dynamic ride.',
		'meta'        => array( '150HP', '6.50 m / 21 ft', '8 guests' ),
	),
	array(
		'title'       => 'Scar 2 Bijeli 150hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(5).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/scar150hp/scar_150hp_hvar_excursions_rentals%20(8).jpg',
		),
		'description' => 'The second Scar 150hp option in the fleet, ideal for guests who want a clean sporty profile and a compact private-day setup around Hvar and the Pakleni Islands.',
		'meta'        => array( '150HP', '6.50 m / 21 ft', '8 guests' ),
	),
	array(
		'title'       => 'Quicksilver 100hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/quicksilver100hp/thumb/Quicksilver_100hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/quicksilver100hp/thumb/Quicksilver_100hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/quicksilver100hp/Quicksilver_100HP_1-2.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/quicksilver100hp/Quicksilver_100HP_1-4.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/quicksilver100hp/Quicksilver_100HP_1-5.jpg',
		),
		'description' => 'A practical speedboat for couples and small groups, with enough speed for island hopping and a simple setup that keeps the day easy and enjoyable.',
		'meta'        => array( '100HP', '5.50 m / 18 ft', '7 guests' ),
	),
	array(
		'title'       => 'Zodiac 60hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/zodiac60hp/thumb/boat_zodiac_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/zodiac60hp/thumb/boat_zodiac_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zodiac60hp/boat_zodiac_hvar_excursions_rentals_2.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zodiac60hp/boat_zodiac_hvar_excursions_rentals_3.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/zodiac60hp/boat_zodiac_hvar_excursions_rentals_4.jpg',
		),
		'description' => 'A nimble 60hp Zodiac that works beautifully for short self-drive adventures, beach hopping, and easy family cruising around the closer Hvar coastline.',
		'meta'        => array( '60HP', 'Compact RIB', 'Easy coastal exploring' ),
	),
	array(
		'title'       => 'Marinello 1 Bordo 60hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_2.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_3.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_4.jpg',
		),
		'description' => 'The first Marinello in the lineup, a clean and approachable 60hp setup for guests who want something simple, polished, and fun for a shorter day at sea.',
		'meta'        => array( '60HP', 'Compact open boat', 'Great for couples and small groups' ),
	),
	array(
		'title'       => 'Marinello 2 Crveni 70hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_2.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_3.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/marinelo60hp/boat_marinello_hvar_excursions_rentals_4.jpg',
		),
		'description' => 'The red Marinello brings a little more punch and presence, giving guests an easy 70hp option for smooth Pakleni runs and relaxed day trips from Hvar.',
		'meta'        => array( '70HP', 'Compact open boat', 'Ideal for easy island hopping' ),
	),
);

$boats = array(
	array(
		'title'       => 'Betina 30hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(4).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(7).jpg',
		),
		'description' => 'A smart step up from a basic boat, with steering wheel control, a 30HP engine, and fuel included. It is ideal for comfortable self-drive cruising around the Pakleni Islands and nearby Hvar beaches.',
		'meta'        => array( '30HP', '4.80 m / 16 ft', '5 guests', 'Fuel included' ),
	),
	array(
		'title'       => 'Pirka 20hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(2).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(4).jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/nautica-500/boat_nautica-2020_hvar_excursions_rentals(7).jpg',
		),
		'description' => 'A lighter self-drive option for relaxed coastal days, hidden coves, and simple Pakleni routes. We are currently using our closest small-boat photo set here until the dedicated Pirka gallery is ready.',
		'meta'        => array( '20HP', 'Easy self-drive', 'Ideal for short day cruising' ),
	),
	array(
		'title'       => 'Adria 8hp',
		'image'       => 'https://www.hvarexcursions.com/img/rentals/photos/boat20hp/thumb/boat_20hp_hvar_excursions_rentals.jpg',
		'gallery'     => array(
			'https://www.hvarexcursions.com/img/rentals/photos/boat20hp/thumb/boat_20hp_hvar_excursions_rentals.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/boat20hp/boat_20hp_hvar_excursions_rentals_2.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/boat20hp/boat_20hp_hvar_excursions_rentals_3.jpg',
			'https://www.hvarexcursions.com/img/rentals/photos/boat20hp/boat_20hp_hvar_excursions_rentals_4.jpg',
		),
		'description' => 'Our most straightforward little boat for calm-weather coastal exploring, best for very easy near-shore cruising and guests who want the simplest possible self-drive option.',
		'meta'        => array( '8HP', 'Small easy-drive boat', 'Short coastal routes' ),
	),
);

$featured_rental['image']   = catamaran_child_localize_image_url( $featured_rental['image'] );
$featured_rental['gallery'] = array_map( 'catamaran_child_localize_image_url', $featured_rental['gallery'] );

foreach ( $speedboats as &$speedboat ) {
	$speedboat['image']   = catamaran_child_localize_image_url( $speedboat['image'] );
	$speedboat['gallery'] = array_map( 'catamaran_child_localize_image_url', $speedboat['gallery'] );
}
unset( $speedboat );

foreach ( $boats as &$boat ) {
	$boat['image']   = catamaran_child_localize_image_url( $boat['image'] );
	$boat['gallery'] = array_map( 'catamaran_child_localize_image_url', $boat['gallery'] );
}
unset( $boat );

$boats[2]['image'] = catamaran_child_asset_image_url(
	'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.00.41_b1305334.jpg',
	$boats[2]['image']
);
$boats[2]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.00.41_b1305334.jpg', $boats[2]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.01.02_42b37261.jpg', $boats[2]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.01.21_01446e8d.jpg', $boats[2]['gallery'][2] ),
);

$featured_rental['image'] = catamaran_child_asset_image_url( 'rentals/Raptor/Raptor_10.jpg', $featured_rental['image'] );
$featured_rental['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/Raptor/Raptor_10.jpg', $featured_rental['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/Raptor/Raptor_11.jpg', $featured_rental['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/Raptor/Raptor_12.jpg', $featured_rental['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/Raptor/Raptor_13.jpg', $featured_rental['gallery'][3] ),
);

$speedboats[0]['image'] = catamaran_child_asset_image_url( 'rentals/joker300hp/joker_jc_clubman_hvar_excursions_rentals (1-1).jpg', $speedboats[0]['image'] );
$speedboats[0]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/joker300hp/joker_jc_clubman_hvar_excursions_rentals (1-1).jpg', $speedboats[0]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/joker300hp/joker_jc_clubman_hvar_excursions_rentals (1-2).jpg', $speedboats[0]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/joker300hp/joker_jc_clubman_hvar_excursions_rentals (1-3).jpg', $speedboats[0]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/joker300hp/joker_jc_clubman_hvar_excursions_rentals (2-1).jpg', $speedboats[0]['gallery'][3] ),
);

$speedboats[1]['image'] = catamaran_child_asset_image_url( 'rentals/Quicksilver/zar_hvar_excursions_rentals_1.jpg', $speedboats[1]['image'] );
$speedboats[1]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/Quicksilver/zar_hvar_excursions_rentals_1.jpg', $speedboats[1]['gallery'][0] ),
);

$speedboats[2]['image'] = catamaran_child_asset_image_url( 'rentals/solemar200hp/thumb/solemar_200hp_hvar_excursions_rentals.jpg', $speedboats[2]['image'] );
$speedboats[2]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/solemar200hp/thumb/solemar_200hp_hvar_excursions_rentals.jpg', $speedboats[2]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/solemar200hp/solemar_150hp_hvar_excursions_rentals (2).jpeg', $speedboats[2]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/solemar200hp/solemar_150hp_hvar_excursions_rentals (4).jpeg', $speedboats[2]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/solemar200hp/solemar_150hp_hvar_excursions_rentals (7).jpeg', $speedboats[2]['gallery'][3] ),
);

$speedboats[3]['image'] = catamaran_child_asset_image_url( 'rentals/bsc175hp/thumb/bsc_175hp_hvar_excursions_rentals.jpg', $speedboats[3]['image'] );
$speedboats[3]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/bsc175hp/thumb/bsc_175hp_hvar_excursions_rentals.jpg', $speedboats[3]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/bsc175hp/bsc_175hp_hvar_excursions_rentals(1_2).jpg', $speedboats[3]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/bsc175hp/bsc_175hp_hvar_excursions_rentals(2_3).jpg', $speedboats[3]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/bsc175hp/bsc_175hp_hvar_excursions_rentals(3_2).jpg', $speedboats[3]['gallery'][3] ),
);

$speedboats[4]['image'] = catamaran_child_asset_image_url( 'rentals/solemar175hp/thumb/solemar_150hp_hvar_excursions_rentals.jpg', $speedboats[4]['image'] );
$speedboats[4]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/solemar175hp/thumb/solemar_150hp_hvar_excursions_rentals.jpg', $speedboats[4]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/solemar175hp/solemar_150hp_hvar_excursions_rentals (1_2).jpg', $speedboats[4]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/solemar175hp/solemar_150hp_hvar_excursions_rentals (1_4).jpg', $speedboats[4]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/solemar175hp/solemar_150hp_hvar_excursions_rentals (1_7).jpg', $speedboats[4]['gallery'][3] ),
);

$speedboats[5]['image'] = catamaran_child_asset_image_url( 'rentals/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg', $speedboats[5]['image'] );
$speedboats[5]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg', $speedboats[5]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (2).jpg', $speedboats[5]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (5).jpg', $speedboats[5]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (8).jpg', $speedboats[5]['gallery'][3] ),
);

$speedboats[6]['image'] = catamaran_child_asset_image_url( 'rentals/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg', $speedboats[6]['image'] );
$speedboats[6]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/scar150hp/thumb/scar_150hp_hvar_excursions_rentals.jpg', $speedboats[6]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (2).jpg', $speedboats[6]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (5).jpg', $speedboats[6]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/scar150hp/scar_150hp_hvar_excursions_rentals (8).jpg', $speedboats[6]['gallery'][3] ),
);

$speedboats[7]['image'] = catamaran_child_asset_image_url( 'rentals/Quicksilver/thumb/Quicksilver_100hp_hvar_excursions_rentals.webp', $speedboats[7]['image'] );
$speedboats[7]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/Quicksilver/thumb/Quicksilver_100hp_hvar_excursions_rentals.webp', $speedboats[7]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/Quicksilver/Quicksilver_100HP_1-2.webp', $speedboats[7]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/Quicksilver/Quicksilver_100HP_1-4.webp', $speedboats[7]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/Quicksilver/Quicksilver_100HP_1-5.webp', $speedboats[7]['gallery'][3] ),
);

$speedboats[8]['image'] = catamaran_child_asset_image_url( 'rentals/zodiac60hp/thumb/boat_zodiac_hvar_excursions_rentals.jpg', $speedboats[8]['image'] );
$speedboats[8]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/zodiac60hp/thumb/boat_zodiac_hvar_excursions_rentals.jpg', $speedboats[8]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/zodiac60hp/boat_zodiac_hvar_excursions_rentals_2.jpg', $speedboats[8]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/zodiac60hp/boat_zodiac_hvar_excursions_rentals_3.jpg', $speedboats[8]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/zodiac60hp/boat_zodiac_hvar_excursions_rentals_4.jpg', $speedboats[8]['gallery'][3] ),
);

$speedboats[9]['image'] = catamaran_child_asset_image_url( 'rentals/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg', $speedboats[9]['image'] );
$speedboats[9]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg', $speedboats[9]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_2.jpg', $speedboats[9]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_3.jpg', $speedboats[9]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_4.jpg', $speedboats[9]['gallery'][3] ),
);

$speedboats[10]['image'] = catamaran_child_asset_image_url( 'rentals/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg', $speedboats[10]['image'] );
$speedboats[10]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/thumb/boat_marinello_hvar_excursions_rentals.jpg', $speedboats[10]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_2.jpg', $speedboats[10]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_3.jpg', $speedboats[10]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/marinelo60hp/boat_marinello_hvar_excursions_rentals_4.jpg', $speedboats[10]['gallery'][3] ),
);

$boats[1]['image'] = catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.00.41_b1305334.jpg', $boats[1]['image'] );
$boats[1]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.00.41_b1305334.jpg', $boats[1]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.01.02_42b37261.jpg', $boats[1]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/Adria_20hp/WhatsApp Slika 2025-09-15 u 21.01.21_01446e8d.jpg', $boats[1]['gallery'][2] ),
);

$boats[0]['image'] = catamaran_child_asset_image_url( 'rentals/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg', $boats[0]['image'] );
$boats[0]['gallery'] = array(
	catamaran_child_asset_image_url( 'rentals/nautica-500/thumb/boat_nautica-500_hvar_excursions_rentals.jpg', $boats[0]['gallery'][0] ),
	catamaran_child_asset_image_url( 'rentals/nautica-500/boat_nautica-2020_hvar_excursions_rentals(2).jpg', $boats[0]['gallery'][1] ),
	catamaran_child_asset_image_url( 'rentals/nautica-500/boat_nautica-2020_hvar_excursions_rentals(4).jpg', $boats[0]['gallery'][2] ),
	catamaran_child_asset_image_url( 'rentals/nautica-500/boat_nautica-2020_hvar_excursions_rentals(7).jpg', $boats[0]['gallery'][3] ),
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
<div class="hex-homepage hex-rentals-shell">
	<header class="hex-rentals-hero" id="top">
		<div class="hex-rentals-hero__media" aria-hidden="true" style="background-image:url('<?php echo esc_url( $featured_rental['image'] ); ?>');"></div>
		<div class="hex-rentals-hero__overlay" aria-hidden="true"></div>

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
				<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['transfers'] ); ?>">Book now</a>
				<button class="hex-nav__toggle" type="button" aria-expanded="false" aria-controls="hex-nav-menu">Menu</button>
			</div>
		</nav>

		<div class="hex-search-drawer" id="hex-search-drawer" hidden>
			<form role="search" method="get" class="hex-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="hex-search-input" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'catamaran' ); ?></label>
				<input id="hex-search-input" type="search" name="s" placeholder="Search boats, transfers, destinations..." value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit">Search</button>
			</form>
		</div>

		<aside class="hex-sidepanel" id="hex-sidepanel" hidden aria-label="Quick panel">
			<div class="hex-sidepanel__inner">
				<button class="hex-sidepanel__close" type="button" aria-label="Close panel">&times;</button>
				<h3>Plan your Hvar route</h3>
				<p>Tell us the boat, the route, and the mood of the day. We will help you choose the right rental setup.</p>
				<ul>
					<li><a href="tel:+385958700479">+385 95 87 00 479</a></li>
					<li><a href="mailto:info@hvarexcursions.com">info@hvarexcursions.com</a></li>
					<li><a href="<?php echo esc_url( $hex_links['contact'] ); ?>">Contact page</a></li>
				</ul>
				<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Request a quote</a>
			</div>
		</aside>
		<div class="hex-sidepanel-overlay" hidden></div>

		<div class="hex-rentals-hero__content">
			<span class="hex-rentals-hero__eyebrow">Boat Rentals in Hvar</span>
			<h1>Luxury, speed, and self-drive options arranged in one clear fleet.</h1>
			<p>Start with the Alesta Raptor, move through our speedboats, and finish with easy self-drive boats for relaxed island days around Hvar.</p>
			<div class="hex-rentals-hero__cta">
				<a class="hex-btn hex-btn--primary" href="#rentals-luxury">Start with Alesta</a>
				<a class="hex-btn hex-btn--book" href="<?php echo esc_url( $hex_links['contact'] ); ?>">Ask for availability</a>
			</div>
		</div>
	</header>

	<main class="hex-main hex-rentals-main">
		<section class="hex-rentals-jump">
			<div class="hex-container">
				<div class="hex-rentals-jump__wrap">
					<a href="#rentals-luxury">Luxury Speedboat</a>
					<a href="#rentals-speedboats">Speedboats</a>
					<a href="#rentals-boats">Boats</a>
				</div>
			</div>
		</section>

		<section class="hex-rentals-featured" id="rentals-luxury">
			<div class="hex-container">
				<div class="hex-rentals-featured__grid">
					<div class="hex-rentals-featured__media">
						<?php $hex_render_gallery( $featured_rental['gallery'], $featured_rental['title'], 'hex-rental-gallery--featured' ); ?>
					</div>
					<div class="hex-rentals-featured__copy">
						<span class="hex-rentals-chip">Luxury Speedboat</span>
						<h2><?php echo esc_html( $featured_rental['short_title'] ); ?></h2>
						<p><?php echo esc_html( $featured_rental['description'] ); ?></p>
						<ul class="hex-rentals-specs">
							<?php foreach ( $featured_rental['specs'] as $label => $value ) : ?>
								<li>
									<span><?php echo esc_html( $label ); ?></span>
									<strong><?php echo esc_html( $value ); ?></strong>
								</li>
							<?php endforeach; ?>
						</ul>
						<div class="hex-rentals-featured__actions">
							<a class="hex-btn hex-btn--primary" href="<?php echo esc_url( add_query_arg( array( 'subject' => 'Rental Inquiry', 'boat' => $featured_rental['title'] ), $hex_links['contact'] ) ); ?>">Request offer</a>
							<a class="hex-btn hex-btn--ghost-dark" href="<?php echo esc_url( $hex_links['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="hex-rentals-section" id="rentals-speedboats">
			<div class="hex-container">
				<div class="hex-rentals-section__heading">
					<span class="hex-rentals-chip">Speedboats</span>
					<h2>Fast boats for island hopping, longer routes, and full Adriatic days.</h2>
					<p>These are the more dynamic options in the fleet, from high-powered premium RIBs to flexible self-drive speedboats for exploring Hvar, Pakleni Islands, Vis, and beyond.</p>
				</div>

				<div class="hex-rentals-grid-wrap">
					<div class="hex-rentals-grid" id="rentals-speedboats-grid">
						<?php foreach ( $speedboats as $index => $boat ) : ?>
							<article class="hex-rental-card<?php echo $index >= 3 ? ' is-collapsed' : ''; ?>">
								<div class="hex-rental-card__media">
									<?php $hex_render_gallery( $boat['gallery'], $boat['title'] ); ?>
								</div>
								<div class="hex-rental-card__body">
									<div class="hex-rental-card__meta">
										<?php foreach ( $boat['meta'] as $meta_item ) : ?>
											<span><?php echo esc_html( $meta_item ); ?></span>
										<?php endforeach; ?>
									</div>
									<h3><?php echo esc_html( $boat['title'] ); ?></h3>
									<p><?php echo esc_html( $boat['description'] ); ?></p>
									<a class="hex-btn hex-btn--small" href="<?php echo esc_url( add_query_arg( array( 'subject' => 'Rental Inquiry', 'boat' => $boat['title'] ), $hex_links['contact'] ) ); ?>">Request offer</a>
								</div>
							</article>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $speedboats ) > 3 ) : ?>
						<div class="hex-rentals-reveal">
							<button class="hex-rentals-reveal__toggle" type="button" data-hex-reveal-toggle aria-expanded="false" aria-controls="rentals-speedboats-grid">
								<span class="hex-rentals-reveal__label" data-hex-reveal-label>View More</span>
								<span class="hex-rentals-reveal__arrow" aria-hidden="true"></span>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="hex-rentals-section hex-rentals-section--boats" id="rentals-boats">
			<div class="hex-container">
				<div class="hex-rentals-section__heading">
					<span class="hex-rentals-chip">Boats</span>
					<h2>Simple, self-drive boats for slower island days and easy local cruising.</h2>
					<p>These are the most accessible options in the rental lineup, perfect for relaxed routes close to Hvar and the Pakleni Islands.</p>
				</div>

				<div class="hex-rentals-grid-wrap">
					<div class="hex-rentals-grid hex-rentals-grid--boats" id="rentals-boats-grid">
						<?php foreach ( $boats as $index => $boat ) : ?>
							<article class="hex-rental-card hex-rental-card--boat<?php echo $index >= 3 ? ' is-collapsed' : ''; ?>">
								<div class="hex-rental-card__media">
									<?php $hex_render_gallery( $boat['gallery'], $boat['title'] ); ?>
								</div>
								<div class="hex-rental-card__body">
									<div class="hex-rental-card__meta">
										<?php foreach ( $boat['meta'] as $meta_item ) : ?>
											<span><?php echo esc_html( $meta_item ); ?></span>
										<?php endforeach; ?>
									</div>
									<h3><?php echo esc_html( $boat['title'] ); ?></h3>
									<p><?php echo esc_html( $boat['description'] ); ?></p>
									<a class="hex-btn hex-btn--small" href="<?php echo esc_url( add_query_arg( array( 'subject' => 'Rental Inquiry', 'boat' => $boat['title'] ), $hex_links['contact'] ) ); ?>">Request offer</a>
								</div>
							</article>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $boats ) > 3 ) : ?>
						<div class="hex-rentals-reveal">
							<button class="hex-rentals-reveal__toggle" type="button" data-hex-reveal-toggle aria-expanded="false" aria-controls="rentals-boats-grid">
								<span class="hex-rentals-reveal__label" data-hex-reveal-label>View More</span>
								<span class="hex-rentals-reveal__arrow" aria-hidden="true"></span>
							</button>
						</div>
					<?php endif; ?>
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
		<div class="hex-gallery-modal__dialog" role="dialog" aria-modal="true" aria-label="Boat gallery">
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

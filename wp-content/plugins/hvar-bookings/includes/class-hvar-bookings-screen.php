<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hex_Bookings_Screen {

	public static function register_rewrite() {
		add_rewrite_rule( '^internal-bookings/?$', 'index.php?hex_internal_bookings=1', 'top' );
		add_rewrite_rule( '^internal-dispatch/?$', 'index.php?hex_internal_dispatch=1', 'top' );
		add_rewrite_rule( '^internal-bookers/?$', 'index.php?hex_internal_bookers=1', 'top' );
		add_rewrite_rule( '^internal-booking-settings/?$', 'index.php?hex_internal_booking_settings=1', 'top' );
	}

	public static function add_query_var( $vars ) {
		$vars[] = 'hex_internal_bookings';
		$vars[] = 'hex_internal_dispatch';
		$vars[] = 'hex_internal_bookers';
		$vars[] = 'hex_internal_booking_settings';
		return $vars;
	}

	public static function maybe_render() {
		if ( self::is_settings_route() ) {
			self::render_settings();
			return;
		}

		if ( self::is_bookers_route() ) {
			self::render_bookers();
			return;
		}

		if ( self::is_dispatch_route() ) {
			self::render_dispatch();
			return;
		}

		if ( self::is_bookings_route() ) {
			self::render_bookings();
		}
	}

	protected static function require_login() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
	}

	protected static function render_page_shell( $body_html, $body_class = 'hex-bookings-screen', $asset_mode = 'screen' ) {
		status_header( 200 );
		nocache_headers();

		if ( 'dispatch' === $asset_mode ) {
			Hex_Bookings_Plugin::enqueue_dispatch_assets();
		} elseif ( 'settings' === $asset_mode ) {
			Hex_Bookings_Plugin::enqueue_settings_assets();
		} else {
			Hex_Bookings_Plugin::enqueue_screen_assets();
		}

		?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( array( $body_class ) ); ?>>
<?php wp_body_open(); ?>
<?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php wp_footer(); ?>
</body>
</html>
<?php
		exit;
	}

	protected static function internal_nav( $active ) {
		ob_start();
		?>
		<nav class="hex-bookings-app__subnav" aria-label="<?php esc_attr_e( 'Internal navigation', 'hvar-bookings' ); ?>">
			<a class="hex-bookings-app__subnav-link <?php echo 'bookings' === $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/internal-bookings/' ) ); ?>"><?php esc_html_e( 'Timeline', 'hvar-bookings' ); ?></a>
			<a class="hex-bookings-app__subnav-link <?php echo 'dispatch' === $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/internal-dispatch/' ) ); ?>"><?php esc_html_e( 'Dispatch', 'hvar-bookings' ); ?></a>
			<?php if ( current_user_can( 'manage_hex_bookings' ) ) : ?>
				<a class="hex-bookings-app__subnav-link <?php echo 'bookers' === $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/internal-bookers/' ) ); ?>"><?php esc_html_e( 'Bookers', 'hvar-bookings' ); ?></a>
				<a class="hex-bookings-app__subnav-link <?php echo 'settings' === $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/internal-booking-settings/' ) ); ?>"><?php esc_html_e( 'Settings', 'hvar-bookings' ); ?></a>
			<?php endif; ?>
		</nav>
		<?php
		return ob_get_clean();
	}

	protected static function render_dispatch() {
		self::require_login();

		if ( ! current_user_can( 'access_hex_bookings' ) && ! current_user_can( 'manage_hex_bookings' ) ) {
			wp_die( esc_html__( 'You do not have permission to access the internal dispatch app.', 'hvar-bookings' ), 403 );
		}

		ob_start();
		?>
<div class="hex-dispatch-app">
	<header class="hex-dispatch-app__header">
		<div class="hex-dispatch-app__intro">
			<p class="hex-bookings-app__eyebrow"><?php esc_html_e( 'Mobile Dispatch', 'hvar-bookings' ); ?></p>
			<h1><?php esc_html_e( 'Internal Dispatch', 'hvar-bookings' ); ?></h1>
			<p><?php esc_html_e( 'Fast booking workflow for phones and browsers. Create, review, and edit reservations without the desktop timeline overhead.', 'hvar-bookings' ); ?></p>
		</div>
		<div class="hex-dispatch-app__toolbar">
			<?php echo self::internal_nav( 'dispatch' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="hex-dispatch-usercard" id="hex-dispatch-usercard">
				<div class="hex-dispatch-usercard__badge">--</div>
				<div class="hex-dispatch-usercard__body">
					<span class="hex-dispatch-usercard__label"><?php esc_html_e( 'Signed in', 'hvar-bookings' ); ?></span>
					<strong class="hex-dispatch-usercard__name">-</strong>
					<span class="hex-dispatch-usercard__email">-</span>
				</div>
			</div>
		</div>
	</header>

	<div class="hex-dispatch-shell">
		<section class="hex-dispatch-top">
			<div class="hex-dispatch-kpis">
				<div class="hex-dispatch-kpi">
					<span class="hex-dispatch-kpi__label"><?php esc_html_e( 'Today', 'hvar-bookings' ); ?></span>
					<strong id="hex-dispatch-today-count">0</strong>
				</div>
				<div class="hex-dispatch-kpi">
					<span class="hex-dispatch-kpi__label"><?php esc_html_e( 'My Bookings', 'hvar-bookings' ); ?></span>
					<strong id="hex-dispatch-mine-count">0</strong>
				</div>
			</div>
		</section>

		<div class="hex-dispatch-toast" id="hex-dispatch-toast" hidden></div>

		<main class="hex-dispatch-main">
			<section class="hex-dispatch-view is-active" data-screen="today">
				<div class="hex-dispatch-sectionhead">
					<div>
						<h2><?php esc_html_e( 'Today', 'hvar-bookings' ); ?></h2>
						<p id="hex-dispatch-today-label"><?php esc_html_e( 'Today’s dispatch board', 'hvar-bookings' ); ?></p>
					</div>
					<button type="button" class="hex-bookings-app__button hex-bookings-app__button--primary hex-dispatch-sectionhead__cta" data-dispatch-open-form="new"><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></button>
				</div>
				<div class="hex-dispatch-chips" id="hex-dispatch-today-filters">
					<button type="button" class="hex-dispatch-chip is-active" data-filter="scope" data-value="all"><?php esc_html_e( 'All', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-dispatch-chip" data-filter="scope" data-value="mine"><?php esc_html_e( 'Mine', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-dispatch-chip" data-filter="status" data-value="confirmed"><?php esc_html_e( 'Confirmed', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-dispatch-chip" data-filter="status" data-value="draft"><?php esc_html_e( 'Draft', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-dispatch-chip" data-filter="service_type" data-value="transfer"><?php esc_html_e( 'Transfers', 'hvar-bookings' ); ?></button>
				</div>
				<div class="hex-dispatch-list" id="hex-dispatch-today-list"></div>
			</section>

			<section class="hex-dispatch-view" data-screen="mine">
				<div class="hex-dispatch-sectionhead">
					<div>
						<h2><?php esc_html_e( 'My Bookings', 'hvar-bookings' ); ?></h2>
						<p><?php esc_html_e( 'All your bookings, with today or the next upcoming booking highlighted.', 'hvar-bookings' ); ?></p>
					</div>
				</div>
				<div class="hex-dispatch-list-meta" id="hex-dispatch-my-meta"></div>
				<div class="hex-dispatch-list" id="hex-dispatch-my-list"></div>
				<div class="hex-dispatch-pagination" id="hex-dispatch-my-pagination"></div>
			</section>

			<section class="hex-dispatch-view" data-screen="new">
				<div class="hex-dispatch-sectionhead">
					<div>
						<h2 id="hex-dispatch-form-title"><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></h2>
						<p><?php esc_html_e( 'Quick booking form for mobile dispatch work.', 'hvar-bookings' ); ?></p>
					</div>
				</div>
				<form id="hex-dispatch-form" class="hex-dispatch-form">
					<input type="hidden" name="booking_id" value="">
					<div class="hex-dispatch-form__grid">
						<label class="hex-dispatch-form__full">
							<span><?php esc_html_e( 'Boat', 'hvar-bookings' ); ?></span>
							<select name="resource_id" required></select>
						</label>
						<label class="hex-dispatch-form__full">
							<span><?php esc_html_e( 'Service', 'hvar-bookings' ); ?></span>
							<select name="service_type">
								<option value="rental"><?php esc_html_e( 'Rental', 'hvar-bookings' ); ?></option>
								<option value="transfer"><?php esc_html_e( 'Transfer', 'hvar-bookings' ); ?></option>
								<option value="excursion"><?php esc_html_e( 'Excursion', 'hvar-bookings' ); ?></option>
								<option value="taxi"><?php esc_html_e( 'Taxi', 'hvar-bookings' ); ?></option>
							</select>
						</label>
						<label class="hex-dispatch-form__full">
							<span><?php esc_html_e( 'Status', 'hvar-bookings' ); ?></span>
							<select name="status">
								<option value="draft"><?php esc_html_e( 'Draft', 'hvar-bookings' ); ?></option>
								<option value="confirmed" selected><?php esc_html_e( 'Confirmed', 'hvar-bookings' ); ?></option>
								<option value="blocked"><?php esc_html_e( 'Blocked', 'hvar-bookings' ); ?></option>
								<option value="cancelled"><?php esc_html_e( 'Cancelled', 'hvar-bookings' ); ?></option>
							</select>
						</label>
						<label class="hex-dispatch-form__third">
							<span><?php esc_html_e( 'Date', 'hvar-bookings' ); ?></span>
							<input type="date" name="booking_date" required>
						</label>
						<label class="hex-dispatch-form__third">
							<span><?php esc_html_e( 'Start', 'hvar-bookings' ); ?></span>
							<input type="time" name="start_time">
						</label>
						<label class="hex-dispatch-form__third">
							<span><?php esc_html_e( 'End', 'hvar-bookings' ); ?></span>
							<input type="time" name="end_time">
						</label>
						<label class="hex-dispatch-form__toggle">
							<input type="checkbox" name="is_all_day" value="1">
							<span><?php esc_html_e( 'All day booking', 'hvar-bookings' ); ?></span>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--rental">
							<span><?php esc_html_e( 'Skipper', 'hvar-bookings' ); ?></span>
							<select name="skipper_mode">
								<option value="with_skipper"><?php esc_html_e( 'With skipper', 'hvar-bookings' ); ?></option>
								<option value="without_skipper"><?php esc_html_e( 'Without skipper', 'hvar-bookings' ); ?></option>
							</select>
						</label>
						<label class="hex-dispatch-form__book-as" hidden>
							<span><?php esc_html_e( 'Book As', 'hvar-bookings' ); ?></span>
							<select name="book_as_user_id"></select>
						</label>
						<label>
							<span><?php esc_html_e( 'Customer Name', 'hvar-bookings' ); ?></span>
							<input type="text" name="customer_name" placeholder="<?php esc_attr_e( 'Guest name or booking reference', 'hvar-bookings' ); ?>">
						</label>
						<label>
							<span><?php esc_html_e( 'Customer Phone', 'hvar-bookings' ); ?></span>
							<input type="text" name="customer_phone" placeholder="<?php esc_attr_e( 'Contact phone', 'hvar-bookings' ); ?>">
						</label>
						<label>
							<span><?php esc_html_e( 'Customer E-mail', 'hvar-bookings' ); ?></span>
							<input type="email" name="customer_email" placeholder="<?php esc_attr_e( 'guest@example.com', 'hvar-bookings' ); ?>">
						</label>
						<label>
							<span><?php esc_html_e( 'Passengers', 'hvar-bookings' ); ?></span>
							<div class="hex-dispatch-passengers">
								<button type="button" class="hex-dispatch-passengers__button" data-passenger-step="-1" aria-label="<?php esc_attr_e( 'Decrease passengers', 'hvar-bookings' ); ?>">-</button>
								<input type="number" name="passengers" min="0" step="1" inputmode="numeric">
								<button type="button" class="hex-dispatch-passengers__button" data-passenger-step="1" aria-label="<?php esc_attr_e( 'Increase passengers', 'hvar-bookings' ); ?>">+</button>
							</div>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--rental hex-dispatch-form__full">
							<span><?php esc_html_e( 'Route / Area', 'hvar-bookings' ); ?></span>
							<input type="text" name="route_summary" placeholder="<?php esc_attr_e( 'Pakleni Islands, south shore, Vis route...', 'hvar-bookings' ); ?>">
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--transfer">
							<span><?php esc_html_e( 'Transfer From', 'hvar-bookings' ); ?></span>
							<select name="pickup_location"></select>
							<div class="hex-dispatch-map-actions">
								<button type="button" class="hex-bookings-app__button" data-hex-map-check="pickup"><?php esc_html_e( 'Check on Map', 'hvar-bookings' ); ?></button>
							</div>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--transfer">
							<span><?php esc_html_e( 'Transfer To', 'hvar-bookings' ); ?></span>
							<select name="dropoff_location"></select>
							<div class="hex-dispatch-map-actions">
								<button type="button" class="hex-bookings-app__button" data-hex-map-check="dropoff"><?php esc_html_e( 'Check on Map', 'hvar-bookings' ); ?></button>
							</div>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--transfer hex-dispatch-form__custom" data-hex-custom-coords="pickup" hidden>
							<span><?php esc_html_e( 'Custom From Coordinates', 'hvar-bookings' ); ?></span>
							<input type="text" name="pickup_coordinates" placeholder="<?php esc_attr_e( '43.1729,16.4426', 'hvar-bookings' ); ?>">
							<div class="hex-dispatch-map-actions">
								<button type="button" class="hex-bookings-app__button" data-hex-map-check="pickup-custom"><?php esc_html_e( 'Check on Map', 'hvar-bookings' ); ?></button>
							</div>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--transfer hex-dispatch-form__custom" data-hex-custom-coords="dropoff" hidden>
							<span><?php esc_html_e( 'Custom To Coordinates', 'hvar-bookings' ); ?></span>
							<input type="text" name="dropoff_coordinates" placeholder="<?php esc_attr_e( '43.1729,16.4426', 'hvar-bookings' ); ?>">
							<div class="hex-dispatch-map-actions">
								<button type="button" class="hex-bookings-app__button" data-hex-map-check="dropoff-custom"><?php esc_html_e( 'Check on Map', 'hvar-bookings' ); ?></button>
							</div>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--transfer hex-dispatch-form__full">
							<span><?php esc_html_e( 'Luggage', 'hvar-bookings' ); ?></span>
							<input type="text" name="luggage_details" placeholder="<?php esc_attr_e( '0, 2 cabin bags, 4 suitcases...', 'hvar-bookings' ); ?>">
						</label>
						<label class="hex-dispatch-form__money">
							<span><?php esc_html_e( 'Booked Price (EUR)', 'hvar-bookings' ); ?></span>
							<input type="number" name="booking_price" min="0" step="0.01" inputmode="decimal" required>
						</label>
						<label class="hex-dispatch-form__money">
							<span><?php esc_html_e( 'Advance Charged (EUR)', 'hvar-bookings' ); ?></span>
							<input type="number" name="advance_amount" min="0" step="0.01" inputmode="decimal" required>
						</label>
						<label class="hex-dispatch-form__toggle hex-dispatch-form__mode hex-dispatch-form__mode--rental">
							<input type="checkbox" name="fuel_included" value="1">
							<span><?php esc_html_e( 'Fuel included in price', 'hvar-bookings' ); ?></span>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--rental">
							<span><?php esc_html_e( 'Sales Channel', 'hvar-bookings' ); ?></span>
							<select name="sales_channel"></select>
						</label>
						<label class="hex-dispatch-form__mode hex-dispatch-form__mode--rental">
							<span><?php esc_html_e( 'Additional Equipment', 'hvar-bookings' ); ?></span>
							<select name="extra_equipment"></select>
						</label>
						<label class="hex-dispatch-form__full hex-dispatch-form__notes">
							<span data-rental-label="<?php esc_attr_e( 'Booking Notes', 'hvar-bookings' ); ?>" data-transfer-label="<?php esc_attr_e( 'Transfer Notes', 'hvar-bookings' ); ?>"><?php esc_html_e( 'Booking Notes', 'hvar-bookings' ); ?></span>
							<textarea
								name="notes"
								rows="3"
								placeholder="<?php esc_attr_e( 'Guest requests, fuel note, special arrangements...', 'hvar-bookings' ); ?>"
								data-rental-placeholder="<?php esc_attr_e( 'Guest requests, fuel note, special arrangements...', 'hvar-bookings' ); ?>"
								data-transfer-placeholder="<?php esc_attr_e( 'Flight number, arrival details, meeting point...', 'hvar-bookings' ); ?>"
							></textarea>
						</label>
						<label class="hex-dispatch-form__full">
							<span><?php esc_html_e( 'Internal Notes', 'hvar-bookings' ); ?></span>
							<textarea name="internal_notes" rows="4"></textarea>
						</label>
						<label class="hex-dispatch-form__toggle hex-dispatch-form__full">
							<input type="checkbox" name="generate_confirmation" value="1">
							<span><?php esc_html_e( 'Generate Booking Confirmation', 'hvar-bookings' ); ?></span>
						</label>
						<label class="hex-dispatch-form__toggle hex-dispatch-form__full">
							<input type="checkbox" name="generate_manager_notification" value="1">
							<span><?php esc_html_e( 'Generate Manager Notification', 'hvar-bookings' ); ?></span>
						</label>
					</div>
					<div class="hex-dispatch-form__message" id="hex-dispatch-form-message" aria-live="polite"></div>
					<div class="hex-dispatch-form__status" id="hex-dispatch-form-status" hidden>
						<div class="hex-dispatch-form__status-item" data-status-kind="confirmation" hidden>
							<div class="hex-dispatch-form__status-head">
								<strong><?php esc_html_e( 'Confirmation', 'hvar-bookings' ); ?></strong>
								<button type="button" class="hex-bookings-app__button" data-status-send-confirmation hidden><?php esc_html_e( 'Send Now', 'hvar-bookings' ); ?></button>
							</div>
							<span data-status-text="confirmation">-</span>
						</div>
						<div class="hex-dispatch-form__status-item" data-status-kind="manager" hidden>
							<strong><?php esc_html_e( 'Manager Notification', 'hvar-bookings' ); ?></strong>
							<span data-status-text="manager">-</span>
						</div>
					</div>
					<div class="hex-dispatch-form__actions">
						<button type="submit" class="hex-bookings-app__button hex-bookings-app__button--primary"><?php esc_html_e( 'Save Booking', 'hvar-bookings' ); ?></button>
						<button type="button" class="hex-bookings-app__button hex-bookings-app__button--new" data-dispatch-reset-form><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></button>
						<button type="button" class="hex-bookings-app__button hex-bookings-app__button--danger" data-dispatch-cancel-booking hidden><?php esc_html_e( 'Cancel Booking', 'hvar-bookings' ); ?></button>
					</div>
				</form>
			</section>

			<section class="hex-dispatch-view" data-screen="boats">
				<div class="hex-dispatch-sectionhead">
					<div>
						<h2><?php esc_html_e( 'Boats', 'hvar-bookings' ); ?></h2>
						<p><?php esc_html_e( 'Quick view of today’s boat usage grouped by category.', 'hvar-bookings' ); ?></p>
					</div>
				</div>
				<div class="hex-dispatch-boatgroups" id="hex-dispatch-boatgroups"></div>
			</section>

			<section class="hex-dispatch-view" data-screen="more">
				<div class="hex-dispatch-sectionhead">
					<div>
						<h2><?php esc_html_e( 'More', 'hvar-bookings' ); ?></h2>
						<p><?php esc_html_e( 'Profile, shortcuts, and manager actions.', 'hvar-bookings' ); ?></p>
					</div>
				</div>
				<div class="hex-dispatch-more" id="hex-dispatch-more">
					<div class="hex-dispatch-more__card">
						<h3><?php esc_html_e( 'Your profile', 'hvar-bookings' ); ?></h3>
						<p id="hex-dispatch-profile-name">-</p>
						<p id="hex-dispatch-profile-email">-</p>
					</div>
					<div class="hex-dispatch-more__links">
						<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookings/' ) ); ?>"><?php esc_html_e( 'Open Desktop Timeline', 'hvar-bookings' ); ?></a>
						<?php if ( current_user_can( 'manage_hex_bookings' ) ) : ?>
							<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookers/' ) ); ?>"><?php esc_html_e( 'Manage Bookers', 'hvar-bookings' ); ?></a>
							<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-booking-settings/' ) ); ?>"><?php esc_html_e( 'Booking Settings', 'hvar-bookings' ); ?></a>
						<?php endif; ?>
						<a class="hex-bookings-app__button" href="<?php echo esc_url( wp_logout_url( home_url( '/internal-dispatch/' ) ) ); ?>"><?php esc_html_e( 'Log Out', 'hvar-bookings' ); ?></a>
					</div>
				</div>
			</section>
		</main>

		<nav class="hex-dispatch-nav" aria-label="<?php esc_attr_e( 'Dispatch navigation', 'hvar-bookings' ); ?>">
			<button type="button" class="hex-dispatch-nav__item is-active" data-screen-target="today"><?php esc_html_e( 'Today', 'hvar-bookings' ); ?></button>
			<button type="button" class="hex-dispatch-nav__item" data-screen-target="mine"><?php esc_html_e( 'My Bookings', 'hvar-bookings' ); ?></button>
			<button type="button" class="hex-dispatch-nav__item hex-dispatch-nav__item--new" data-screen-target="new"><span aria-hidden="true">+</span><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></button>
			<button type="button" class="hex-dispatch-nav__item" data-screen-target="boats"><?php esc_html_e( 'Boats', 'hvar-bookings' ); ?></button>
			<button type="button" class="hex-dispatch-nav__item" data-screen-target="more"><?php esc_html_e( 'More', 'hvar-bookings' ); ?></button>
		</nav>
	</div>

	<aside class="hex-dispatch-drawer" id="hex-dispatch-drawer" hidden>
		<div class="hex-dispatch-drawer__backdrop" data-drawer-close></div>
		<div class="hex-dispatch-drawer__panel">
			<button type="button" class="hex-dispatch-drawer__close" data-drawer-close aria-label="<?php esc_attr_e( 'Close booking details', 'hvar-bookings' ); ?>">&times;</button>
			<div class="hex-dispatch-drawer__eyebrow"><?php esc_html_e( 'Booking Details', 'hvar-bookings' ); ?></div>
			<h3 class="hex-dispatch-drawer__title">-</h3>
			<div class="hex-dispatch-drawer__badges"></div>
			<dl class="hex-dispatch-drawer__details"></dl>
			<div class="hex-dispatch-drawer__actions">
				<button type="button" class="hex-bookings-app__button hex-bookings-app__button--primary" data-drawer-edit><?php esc_html_e( 'Edit Booking', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-drawer-preview-confirmation hidden><?php esc_html_e( 'Preview Confirmation', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-drawer-preview-manager-note hidden><?php esc_html_e( 'Preview Manager Note', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-drawer-copy-manager-note hidden><?php esc_html_e( 'Copy Manager Note', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-drawer-send-manager-note hidden><?php esc_html_e( 'Send by WhatsApp', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-drawer-close><?php esc_html_e( 'Close', 'hvar-bookings' ); ?></button>
			</div>
		</div>
	</aside>

	<aside class="hex-dispatch-preview" id="hex-dispatch-preview" hidden>
		<div class="hex-dispatch-preview__backdrop" data-preview-close></div>
		<div class="hex-dispatch-preview__panel">
			<button type="button" class="hex-dispatch-preview__close" data-preview-close aria-label="<?php esc_attr_e( 'Close preview', 'hvar-bookings' ); ?>">&times;</button>
			<div class="hex-dispatch-preview__eyebrow" id="hex-dispatch-preview-eyebrow">-</div>
			<h3 class="hex-dispatch-preview__title" id="hex-dispatch-preview-title">-</h3>
			<pre class="hex-dispatch-preview__text" id="hex-dispatch-preview-text"></pre>
			<div class="hex-dispatch-preview__actions">
				<button type="button" class="hex-bookings-app__button" data-preview-copy><?php esc_html_e( 'Copy Text', 'hvar-bookings' ); ?></button>
				<button type="button" class="hex-bookings-app__button" data-preview-close><?php esc_html_e( 'Close', 'hvar-bookings' ); ?></button>
			</div>
		</div>
	</aside>
</div>
		<?php
		$html = ob_get_clean();
		self::render_page_shell( $html, 'hex-dispatch-screen', 'dispatch' );
	}

	protected static function render_bookings() {
		self::require_login();

		if ( ! current_user_can( 'access_hex_bookings' ) && ! current_user_can( 'manage_hex_bookings' ) ) {
			wp_die( esc_html__( 'You do not have permission to access the internal bookings board.', 'hvar-bookings' ), 403 );
		}

		ob_start();
		?>
<div class="hex-bookings-app">
	<header class="hex-bookings-app__header">
		<div>
			<p class="hex-bookings-app__eyebrow"><?php esc_html_e( 'Internal Dispatch', 'hvar-bookings' ); ?></p>
			<h1><?php esc_html_e( 'Hvar Bookings Timeline', 'hvar-bookings' ); ?></h1>
			<p><?php esc_html_e( 'FullCalendar Premium trial is powering the resource timeline, booking colors, manager filters, and assignment flow.', 'hvar-bookings' ); ?></p>
			<?php echo self::internal_nav( 'bookings' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="hex-bookings-app__actions">
			<div class="hex-bookings-app__user" id="hex-bookings-current-user">
				<span class="hex-bookings-app__user-initials">--</span>
				<div class="hex-bookings-app__user-details">
					<span class="hex-bookings-app__user-label"><?php esc_html_e( 'Logged in as', 'hvar-bookings' ); ?></span>
					<strong class="hex-bookings-app__user-name">-</strong>
					<span class="hex-bookings-app__user-email">-</span>
				</div>
			</div>
			<a class="hex-bookings-app__button" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'hvar-bookings' ); ?></a>
			<?php if ( current_user_can( 'manage_hex_bookings' ) ) : ?>
				<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookers/' ) ); ?>"><?php esc_html_e( 'Manage Bookers', 'hvar-bookings' ); ?></a>
			<?php endif; ?>
			<a class="hex-bookings-app__button hex-bookings-app__button--primary" href="<?php echo esc_url( rest_url( 'hex-bookings/v1/system' ) ); ?>"><?php esc_html_e( 'REST Status', 'hvar-bookings' ); ?></a>
		</div>
	</header>

	<section class="hex-bookings-app__legend">
		<?php foreach ( Hex_Bookings_Plugin::get_color_legend() as $legend_item ) : ?>
			<div class="hex-bookings-legend__item">
				<span class="hex-bookings-legend__swatch" style="--hex-legend-color: <?php echo esc_attr( $legend_item['swatch'] ); ?>"></span>
				<div>
					<strong><?php echo esc_html( $legend_item['label'] ); ?></strong>
					<p><?php echo esc_html( $legend_item['description'] ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</section>

	<section class="hex-bookings-app__grid">
		<div class="hex-bookings-app__panel">
			<h2><?php esc_html_e( 'Timeline Board', 'hvar-bookings' ); ?></h2>
			<p><?php esc_html_e( 'Filter the fleet, focus on your own work, and manage bookings directly on the resource timeline.', 'hvar-bookings' ); ?></p>
			<form id="hex-bookings-filters" class="hex-bookings-filters">
				<label class="hex-bookings-filters__field hex-bookings-filters__field--category">
					<span><?php esc_html_e( 'Category', 'hvar-bookings' ); ?></span>
					<select name="category">
						<option value=""><?php esc_html_e( 'All categories', 'hvar-bookings' ); ?></option>
					</select>
				</label>
				<label class="hex-bookings-filters__field hex-bookings-filters__field--resource">
					<span><?php esc_html_e( 'Resource', 'hvar-bookings' ); ?></span>
					<select name="resource_id">
						<option value=""><?php esc_html_e( 'All boats', 'hvar-bookings' ); ?></option>
					</select>
				</label>
				<label class="hex-bookings-filters__field hex-bookings-filters__field--service">
					<span><?php esc_html_e( 'Service', 'hvar-bookings' ); ?></span>
					<select name="service_type">
						<option value=""><?php esc_html_e( 'All services', 'hvar-bookings' ); ?></option>
						<option value="rental"><?php esc_html_e( 'Rental', 'hvar-bookings' ); ?></option>
						<option value="transfer"><?php esc_html_e( 'Transfer', 'hvar-bookings' ); ?></option>
						<option value="excursion"><?php esc_html_e( 'Excursion', 'hvar-bookings' ); ?></option>
						<option value="taxi"><?php esc_html_e( 'Taxi', 'hvar-bookings' ); ?></option>
					</select>
				</label>
				<label class="hex-bookings-filters__field hex-bookings-filters__field--booker">
					<span><?php esc_html_e( 'Booker', 'hvar-bookings' ); ?></span>
					<select name="booker_user_id" disabled>
						<option value=""><?php esc_html_e( 'All bookers', 'hvar-bookings' ); ?></option>
					</select>
				</label>
				<label class="hex-bookings-filters__field hex-bookings-filters__field--status">
					<span><?php esc_html_e( 'Status', 'hvar-bookings' ); ?></span>
					<select name="status">
						<option value=""><?php esc_html_e( 'All statuses', 'hvar-bookings' ); ?></option>
						<option value="draft"><?php esc_html_e( 'Draft', 'hvar-bookings' ); ?></option>
						<option value="confirmed"><?php esc_html_e( 'Confirmed', 'hvar-bookings' ); ?></option>
						<option value="blocked"><?php esc_html_e( 'Blocked', 'hvar-bookings' ); ?></option>
						<option value="cancelled"><?php esc_html_e( 'Cancelled', 'hvar-bookings' ); ?></option>
					</select>
				</label>
				<label class="hex-bookings-filters__toggle hex-bookings-filters__field hex-bookings-filters__field--mine">
					<input type="checkbox" name="only_mine" value="1">
					<span><?php esc_html_e( 'Only my bookings', 'hvar-bookings' ); ?></span>
				</label>
				<div class="hex-bookings-filters__actions">
					<button type="button" class="hex-bookings-app__button" data-hex-clear-filters><?php esc_html_e( 'Clear filters', 'hvar-bookings' ); ?></button>
				</div>
			</form>
			<div class="hex-bookings-calendar-toolbar-extra">
				<button type="button" class="hex-bookings-app__button hex-bookings-app__button--primary" data-hex-calendar-new-booking><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></button>
			</div>
			<div id="hex-bookings-calendar-root" class="hex-bookings-calendar-root"></div>
			<div id="hex-bookings-event-popover" class="hex-bookings-popover" hidden>
				<button type="button" class="hex-bookings-popover__close" data-hex-popover-close aria-label="<?php esc_attr_e( 'Close booking preview', 'hvar-bookings' ); ?>">&times;</button>
				<div class="hex-bookings-popover__eyebrow"><?php esc_html_e( 'Booking Preview', 'hvar-bookings' ); ?></div>
				<h3 class="hex-bookings-popover__title">-</h3>
				<div class="hex-bookings-popover__meta"></div>
				<dl class="hex-bookings-popover__details"></dl>
				<div class="hex-bookings-popover__actions">
					<button type="button" class="hex-bookings-app__button hex-bookings-app__button--primary" data-hex-popover-edit><?php esc_html_e( 'Edit booking', 'hvar-bookings' ); ?></button>
				</div>
			</div>
		</div>
		<div class="hex-bookings-app__panel">
			<h2><?php esc_html_e( 'Booking Editor', 'hvar-bookings' ); ?></h2>
			<p><?php esc_html_e( 'Select a timeline slot for a new booking, or preview an event first and jump into editing only when you need to.', 'hvar-bookings' ); ?></p>
			<form id="hex-bookings-form" class="hex-bookings-form">
				<input type="hidden" name="booking_id" value="">

				<label>
					<span><?php esc_html_e( 'Boat / Resource', 'hvar-bookings' ); ?></span>
					<select name="resource_id" required></select>
				</label>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Date', 'hvar-bookings' ); ?></span>
						<input type="date" name="booking_date" required>
					</label>
					<label>
						<span><?php esc_html_e( 'Status', 'hvar-bookings' ); ?></span>
						<select name="status">
							<option value="draft"><?php esc_html_e( 'Draft', 'hvar-bookings' ); ?></option>
							<option value="confirmed"><?php esc_html_e( 'Confirmed', 'hvar-bookings' ); ?></option>
							<option value="blocked"><?php esc_html_e( 'Blocked', 'hvar-bookings' ); ?></option>
							<option value="cancelled"><?php esc_html_e( 'Cancelled', 'hvar-bookings' ); ?></option>
						</select>
					</label>
				</div>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Start', 'hvar-bookings' ); ?></span>
						<input type="time" name="start_time">
					</label>
					<label>
						<span><?php esc_html_e( 'End', 'hvar-bookings' ); ?></span>
						<input type="time" name="end_time">
					</label>
				</div>

				<label class="hex-bookings-form__toggle">
					<input type="checkbox" name="is_all_day" value="1">
					<span><?php esc_html_e( 'All day booking', 'hvar-bookings' ); ?></span>
				</label>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Service Type', 'hvar-bookings' ); ?></span>
						<select name="service_type">
							<option value="rental"><?php esc_html_e( 'Rental', 'hvar-bookings' ); ?></option>
							<option value="transfer"><?php esc_html_e( 'Transfer', 'hvar-bookings' ); ?></option>
							<option value="excursion"><?php esc_html_e( 'Excursion', 'hvar-bookings' ); ?></option>
							<option value="taxi"><?php esc_html_e( 'Taxi', 'hvar-bookings' ); ?></option>
						</select>
					</label>
					<label>
						<span><?php esc_html_e( 'Skipper Mode', 'hvar-bookings' ); ?></span>
						<select name="skipper_mode">
							<option value="with_skipper"><?php esc_html_e( 'With skipper', 'hvar-bookings' ); ?></option>
							<option value="without_skipper"><?php esc_html_e( 'Without skipper', 'hvar-bookings' ); ?></option>
						</select>
					</label>
				</div>

				<label class="hex-bookings-form__book-as" hidden>
					<span><?php esc_html_e( 'Book As', 'hvar-bookings' ); ?></span>
					<select name="book_as_user_id">
						<option value=""><?php esc_html_e( 'Use my own booker profile', 'hvar-bookings' ); ?></option>
					</select>
				</label>

				<label>
					<span><?php esc_html_e( 'Customer Name', 'hvar-bookings' ); ?></span>
					<input type="text" name="customer_name" placeholder="<?php esc_attr_e( 'Guest or booking reference', 'hvar-bookings' ); ?>">
				</label>

				<label>
					<span><?php esc_html_e( 'Route Summary', 'hvar-bookings' ); ?></span>
					<input type="text" name="route_summary" placeholder="<?php esc_attr_e( 'Split to Hvar, Pakleni route, Hvar taxi...', 'hvar-bookings' ); ?>">
				</label>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Booked Price (EUR)', 'hvar-bookings' ); ?></span>
						<input type="number" name="booking_price" min="0" step="0.01" inputmode="decimal" required>
					</label>
					<label>
						<span><?php esc_html_e( 'Advance Charged (EUR)', 'hvar-bookings' ); ?></span>
						<input type="number" name="advance_amount" min="0" step="0.01" inputmode="decimal" required>
					</label>
				</div>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Passengers', 'hvar-bookings' ); ?></span>
						<input type="number" name="passengers" min="0" step="1">
					</label>
					<label>
						<span><?php esc_html_e( 'Customer Phone', 'hvar-bookings' ); ?></span>
						<input type="text" name="customer_phone">
					</label>
				</div>

				<label>
					<span><?php esc_html_e( 'Internal Notes', 'hvar-bookings' ); ?></span>
					<textarea name="internal_notes" rows="4"></textarea>
				</label>

				<div class="hex-bookings-form__message" id="hex-bookings-form-message" aria-live="polite"></div>

				<div class="hex-bookings-form__actions">
					<button type="submit" class="hex-bookings-app__button hex-bookings-app__button--primary hex-bookings-form__button hex-bookings-form__button--save"><?php esc_html_e( 'Save Booking', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-bookings-app__button hex-bookings-app__button--new hex-bookings-form__button hex-bookings-form__button--new" data-hex-reset-form><?php esc_html_e( 'New Booking', 'hvar-bookings' ); ?></button>
					<button type="button" class="hex-bookings-app__button hex-bookings-app__button--danger hex-bookings-form__button hex-bookings-form__button--cancel" data-hex-delete-booking hidden><?php esc_html_e( 'Cancel Booking', 'hvar-bookings' ); ?></button>
				</div>
			</form>
		</div>
	</section>
</div>
		<?php
		$html = ob_get_clean();
		self::render_page_shell( $html, 'hex-bookings-screen' );
	}

	protected static function render_settings() {
		self::require_login();

		if ( ! current_user_can( 'manage_hex_bookings' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage booking settings.', 'hvar-bookings' ), 403 );
		}

		$message  = '';
		$error    = '';
		$settings = Hex_Bookings_Plugin::get_dispatch_settings();

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'hex_booking_settings_save', 'hex_booking_settings_nonce' );
			$posted = wp_unslash( $_POST['hex_booking_settings'] ?? array() );
			$settings = Hex_Bookings_Plugin::update_dispatch_settings( is_array( $posted ) ? $posted : array() );
			$message = __( 'Booking settings saved successfully.', 'hvar-bookings' );
		}

		ob_start();
		?>
<div class="hex-bookings-app hex-bookings-settings">
	<header class="hex-bookings-app__header">
		<div>
			<p class="hex-bookings-app__eyebrow"><?php esc_html_e( 'Internal Settings', 'hvar-bookings' ); ?></p>
			<h1><?php esc_html_e( 'Booking Settings', 'hvar-bookings' ); ?></h1>
			<p><?php esc_html_e( 'Manage the dropdown values used in Dispatch for sales channels, additional equipment, and transfer destinations with coordinates.', 'hvar-bookings' ); ?></p>
			<?php echo self::internal_nav( 'settings' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="hex-bookings-app__actions">
			<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-dispatch/' ) ); ?>"><?php esc_html_e( 'Back to Dispatch', 'hvar-bookings' ); ?></a>
			<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookings/' ) ); ?>"><?php esc_html_e( 'Back to Timeline', 'hvar-bookings' ); ?></a>
		</div>
	</header>

	<?php if ( '' !== $message ) : ?>
		<div class="hex-bookings-app__notice is-success"><?php echo esc_html( $message ); ?></div>
	<?php endif; ?>

	<?php if ( '' !== $error ) : ?>
		<div class="hex-bookings-app__notice is-error"><?php echo esc_html( $error ); ?></div>
	<?php endif; ?>

	<form class="hex-settings-form" method="post">
		<?php wp_nonce_field( 'hex_booking_settings_save', 'hex_booking_settings_nonce' ); ?>

		<section class="hex-settings-section">
			<div class="hex-settings-section__head">
				<div>
					<h2><?php esc_html_e( 'Sales Channels', 'hvar-bookings' ); ?></h2>
					<p><?php esc_html_e( 'Used in the rental/speedboat booking form.', 'hvar-bookings' ); ?></p>
				</div>
				<button type="button" class="hex-bookings-app__button" data-hex-settings-add="sales-channels"><?php esc_html_e( 'Add Channel', 'hvar-bookings' ); ?></button>
			</div>
			<div class="hex-settings-list" data-hex-settings-list="sales-channels">
				<?php foreach ( $settings['sales_channels'] as $index => $item ) : ?>
					<?php self::render_settings_row( 'sales_channels', $index, $item, false ); ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="hex-settings-section">
			<div class="hex-settings-section__head">
				<div>
					<h2><?php esc_html_e( 'Additional Equipment', 'hvar-bookings' ); ?></h2>
					<p><?php esc_html_e( 'Used in the Additional Equipment dropdown on rental bookings.', 'hvar-bookings' ); ?></p>
				</div>
				<button type="button" class="hex-bookings-app__button" data-hex-settings-add="equipment-options"><?php esc_html_e( 'Add Equipment', 'hvar-bookings' ); ?></button>
			</div>
			<div class="hex-settings-list" data-hex-settings-list="equipment-options">
				<?php foreach ( $settings['equipment_options'] as $index => $item ) : ?>
					<?php self::render_settings_row( 'equipment_options', $index, $item, false ); ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="hex-settings-section">
			<div class="hex-settings-section__head">
				<div>
					<h2><?php esc_html_e( 'Transfer Locations', 'hvar-bookings' ); ?></h2>
					<p><?php esc_html_e( 'Used in the Transfer From and Transfer To dropdowns. Add coordinates to support Check on Map. Custom is always available in Dispatch for one-off coordinates.', 'hvar-bookings' ); ?></p>
				</div>
				<button type="button" class="hex-bookings-app__button" data-hex-settings-add="transfer-locations"><?php esc_html_e( 'Add Location', 'hvar-bookings' ); ?></button>
			</div>
			<div class="hex-settings-list" data-hex-settings-list="transfer-locations">
				<?php foreach ( $settings['transfer_locations'] as $index => $item ) : ?>
					<?php self::render_settings_row( 'transfer_locations', $index, $item, true ); ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="hex-settings-section">
			<div class="hex-settings-section__head">
				<div>
					<h2><?php esc_html_e( 'Manager Notifications', 'hvar-bookings' ); ?></h2>
					<p><?php esc_html_e( 'Used for one-tap WhatsApp manager notes now, and as the base for a future scheduled daily notification flow.', 'hvar-bookings' ); ?></p>
				</div>
			</div>
			<div class="hex-settings-fields">
				<label>
					<span><?php esc_html_e( 'Manager WhatsApp Number', 'hvar-bookings' ); ?></span>
					<input type="text" name="hex_booking_settings[manager_whatsapp_number]" value="<?php echo esc_attr( $settings['manager_whatsapp_number'] ?? '' ); ?>" placeholder="<?php esc_attr_e( '+385...', 'hvar-bookings' ); ?>">
				</label>
				<label>
					<span><?php esc_html_e( 'Daily Notification Time', 'hvar-bookings' ); ?></span>
					<input type="time" name="hex_booking_settings[manager_notification_time]" value="<?php echo esc_attr( $settings['manager_notification_time'] ?? '17:00' ); ?>">
				</label>
			</div>
		</section>

		<div class="hex-settings-actions">
			<button type="submit" class="hex-bookings-app__button hex-bookings-app__button--primary"><?php esc_html_e( 'Save Settings', 'hvar-bookings' ); ?></button>
		</div>
	</form>

	<template id="hex-settings-row-template-basic">
		<?php self::render_settings_row( '__GROUP__', '__INDEX__', array( 'label' => '', 'value' => '' ), false ); ?>
	</template>

	<template id="hex-settings-row-template-location">
		<?php self::render_settings_row( '__GROUP__', '__INDEX__', array( 'label' => '', 'value' => '', 'coordinates' => '' ), true ); ?>
	</template>
</div>
		<?php
		$html = ob_get_clean();
		self::render_page_shell( $html, 'hex-bookings-settings-screen', 'settings' );
	}

	protected static function render_bookers() {
		self::require_login();

		if ( ! current_user_can( 'manage_hex_bookings' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage bookers.', 'hvar-bookings' ), 403 );
		}

		$message = '';
		$error   = '';

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'hex_booker_save', 'hex_booker_nonce' );
			$result = self::handle_booker_save();

			if ( is_wp_error( $result ) ) {
				$error = $result->get_error_message();
			} else {
				$redirect_url = add_query_arg(
					array(
						'hex_booker_notice' => rawurlencode( $result ),
					),
					home_url( '/internal-bookers/' )
				);
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		if ( isset( $_GET['hex_booker_notice'] ) ) {
			$message = sanitize_text_field( wp_unslash( $_GET['hex_booker_notice'] ) );
		}

		$editing_user = self::get_editing_booker();
		$bookers      = self::get_bookers();

		ob_start();
		?>
<div class="hex-bookings-app hex-bookers-app">
	<header class="hex-bookings-app__header">
		<div>
			<p class="hex-bookings-app__eyebrow"><?php esc_html_e( 'Internal Admin', 'hvar-bookings' ); ?></p>
			<h1><?php esc_html_e( 'Booker Directory', 'hvar-bookings' ); ?></h1>
			<p><?php esc_html_e( 'Manage the people who can be assigned to bookings. This powers the manager-only Book As dropdown on the timeline.', 'hvar-bookings' ); ?></p>
			<?php echo self::internal_nav( 'bookers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="hex-bookings-app__actions">
			<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookings/' ) ); ?>"><?php esc_html_e( 'Back to Timeline', 'hvar-bookings' ); ?></a>
			<a class="hex-bookings-app__button hex-bookings-app__button--primary" href="<?php echo esc_url( home_url( '/internal-bookers/' ) ); ?>"><?php esc_html_e( 'New Booker', 'hvar-bookings' ); ?></a>
		</div>
	</header>

	<?php if ( $message ) : ?>
		<div class="hex-bookers-notice hex-bookers-notice--success"><?php echo esc_html( $message ); ?></div>
	<?php endif; ?>
	<?php if ( $error ) : ?>
		<div class="hex-bookers-notice hex-bookers-notice--error"><?php echo esc_html( $error ); ?></div>
	<?php endif; ?>

	<section class="hex-bookers-layout">
		<div class="hex-bookings-app__panel">
			<h2><?php esc_html_e( 'Current Bookers', 'hvar-bookings' ); ?></h2>
			<p><?php esc_html_e( 'Use this list to review who is active in the booking workflow and jump into editing their profile.', 'hvar-bookings' ); ?></p>
			<div class="hex-bookers-table-wrap">
				<table class="hex-bookers-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Initials', 'hvar-bookings' ); ?></th>
							<th><?php esc_html_e( 'Name', 'hvar-bookings' ); ?></th>
							<th><?php esc_html_e( 'Email', 'hvar-bookings' ); ?></th>
							<th><?php esc_html_e( 'Phone', 'hvar-bookings' ); ?></th>
							<th><?php esc_html_e( 'Role', 'hvar-bookings' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'hvar-bookings' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $bookers as $booker ) : ?>
							<tr>
								<td data-label="<?php esc_attr_e( 'Initials', 'hvar-bookings' ); ?>"><span class="hex-bookers-table__initials"><?php echo esc_html( $booker['initials'] ?: '--' ); ?></span></td>
								<td data-label="<?php esc_attr_e( 'Name', 'hvar-bookings' ); ?>"><?php echo esc_html( $booker['display_name'] ); ?></td>
								<td data-label="<?php esc_attr_e( 'Email', 'hvar-bookings' ); ?>"><?php echo esc_html( $booker['email'] ); ?></td>
								<td><?php echo esc_html( $booker['phone'] ?: '—' ); ?></td>
								<td data-label="<?php esc_attr_e( 'Role', 'hvar-bookings' ); ?>"><?php echo esc_html( $booker['role_label'] ); ?></td>
								<td class="hex-bookers-table__actions" data-label="<?php esc_attr_e( 'Actions', 'hvar-bookings' ); ?>">
									<a class="hex-bookings-app__button" href="<?php echo esc_url( add_query_arg( array( 'edit_user' => $booker['id'] ), home_url( '/internal-bookers/' ) ) ); ?>"><?php esc_html_e( 'Edit', 'hvar-bookings' ); ?></a>
									<?php if ( ! empty( $booker['is_protected'] ) ) : ?>
										<span class="hex-bookers-table__protected"><?php esc_html_e( 'Protected', 'hvar-bookings' ); ?></span>
									<?php elseif ( ! empty( $booker['can_delete'] ) ) : ?>
										<form class="hex-bookers-table__delete" method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Delete this booker? Existing bookings will remain visible, but this user will no longer be able to log in.', 'hvar-bookings' ) ); ?>');">
											<?php wp_nonce_field( 'hex_booker_save', 'hex_booker_nonce' ); ?>
											<input type="hidden" name="hex_booker_action" value="delete">
											<input type="hidden" name="user_id" value="<?php echo esc_attr( $booker['id'] ); ?>">
											<button type="submit" class="hex-bookings-app__button hex-bookings-app__button--danger"><span aria-hidden="true">&times;</span><?php esc_html_e( 'Delete', 'hvar-bookings' ); ?></button>
										</form>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="hex-bookings-app__panel">
			<h2><?php echo esc_html( $editing_user ? __( 'Edit Booker', 'hvar-bookings' ) : __( 'Add Booker', 'hvar-bookings' ) ); ?></h2>
			<p><?php esc_html_e( 'Booker data lives on WordPress user accounts. This screen manages the booking-specific fields like initials and phone too.', 'hvar-bookings' ); ?></p>
			<form class="hex-bookers-form" method="post">
				<?php wp_nonce_field( 'hex_booker_save', 'hex_booker_nonce' ); ?>
				<input type="hidden" name="hex_booker_action" value="<?php echo esc_attr( $editing_user ? 'update' : 'create' ); ?>">
				<input type="hidden" name="user_id" value="<?php echo esc_attr( $editing_user['id'] ?? 0 ); ?>">

				<label>
					<span><?php esc_html_e( 'Full Name', 'hvar-bookings' ); ?></span>
					<input type="text" name="display_name" required value="<?php echo esc_attr( $editing_user['display_name'] ?? '' ); ?>">
				</label>

				<label>
					<span><?php esc_html_e( 'Email', 'hvar-bookings' ); ?></span>
					<input type="email" name="user_email" required value="<?php echo esc_attr( $editing_user['email'] ?? '' ); ?>" <?php disabled( ! empty( $editing_user['is_protected'] ) ); ?>>
				</label>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Phone', 'hvar-bookings' ); ?></span>
						<input type="text" name="hex_booker_phone" value="<?php echo esc_attr( $editing_user['phone'] ?? '' ); ?>">
					</label>
					<label>
						<span><?php esc_html_e( 'Initials', 'hvar-bookings' ); ?></span>
						<input type="text" name="hex_booker_initials" maxlength="10" value="<?php echo esc_attr( $editing_user['initials'] ?? '' ); ?>">
					</label>
				</div>

				<div class="hex-bookings-form__row">
					<label>
						<span><?php esc_html_e( 'Role', 'hvar-bookings' ); ?></span>
						<?php if ( ! empty( $editing_user['is_protected'] ) ) : ?>
							<input type="text" value="<?php esc_attr_e( 'Protected Administrator', 'hvar-bookings' ); ?>" readonly>
							<input type="hidden" name="booker_role" value="administrator">
						<?php else : ?>
							<select name="booker_role">
								<option value="booker" <?php selected( $editing_user['role'] ?? 'booker', 'booker' ); ?>><?php esc_html_e( 'Booker', 'hvar-bookings' ); ?></option>
								<option value="booking_manager" <?php selected( $editing_user['role'] ?? '', 'booking_manager' ); ?>><?php esc_html_e( 'Booking Manager', 'hvar-bookings' ); ?></option>
							</select>
						<?php endif; ?>
					</label>
					<?php if ( ! $editing_user ) : ?>
						<label>
							<span><?php esc_html_e( 'Temporary Password', 'hvar-bookings' ); ?></span>
							<input type="text" name="user_pass" value="<?php echo esc_attr( wp_generate_password( 12, false ) ); ?>">
						</label>
					<?php endif; ?>
				</div>

				<div class="hex-bookings-form__actions">
					<button type="submit" class="hex-bookings-app__button hex-bookings-app__button--primary"><?php echo esc_html( $editing_user ? __( 'Save Booker', 'hvar-bookings' ) : __( 'Create Booker', 'hvar-bookings' ) ); ?></button>
					<a class="hex-bookings-app__button" href="<?php echo esc_url( home_url( '/internal-bookers/' ) ); ?>"><?php esc_html_e( 'Reset Form', 'hvar-bookings' ); ?></a>
				</div>
				<?php if ( ! empty( $editing_user['is_protected'] ) ) : ?>
					<p class="hex-bookers-form__hint"><?php esc_html_e( 'This protected administrator account cannot be downgraded from the internal booker screen.', 'hvar-bookings' ); ?></p>
				<?php endif; ?>
			</form>
		</div>
	</section>
</div>
		<?php
		$html = ob_get_clean();
		self::render_page_shell( $html, 'hex-bookers-screen' );
	}

	protected static function handle_booker_save() {
		$action       = sanitize_key( wp_unslash( $_POST['hex_booker_action'] ?? '' ) );
		$user_id      = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;

		if ( 'delete' === $action ) {
			return self::handle_booker_delete( $user_id );
		}

		$display_name = sanitize_text_field( wp_unslash( $_POST['display_name'] ?? '' ) );
		$user_email   = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
		$user_pass    = (string) wp_unslash( $_POST['user_pass'] ?? '' );
		$phone        = sanitize_text_field( wp_unslash( $_POST['hex_booker_phone'] ?? '' ) );
		$initials     = strtoupper( preg_replace( '/[^A-Za-z]/', '', (string) wp_unslash( $_POST['hex_booker_initials'] ?? '' ) ) );
		$role         = sanitize_key( wp_unslash( $_POST['booker_role'] ?? 'booker' ) );
		$is_protected = Hex_Bookings_Plugin::is_protected_admin_user( $user_id );
		$previous_role = '';

		if ( 'update' === $action && $user_id > 0 ) {
			$previous_user = get_userdata( $user_id );
			$previous_role = $previous_user && ! empty( $previous_user->roles ) ? (string) $previous_user->roles[0] : '';
		}

		if ( ! $is_protected && ! in_array( $role, array( 'booker', 'booking_manager' ), true ) ) {
			return new WP_Error( 'hex_invalid_role', __( 'Please choose a valid role.', 'hvar-bookings' ) );
		}

		if ( '' === $display_name || '' === $user_email ) {
			return new WP_Error( 'hex_missing_fields', __( 'Name and email are required.', 'hvar-bookings' ) );
		}

		if ( 'create' === $action ) {
			if ( '' === $user_pass ) {
				return new WP_Error( 'hex_missing_password', __( 'Temporary password is required for a new booker.', 'hvar-bookings' ) );
			}

			$user_id = wp_insert_user(
				array(
					'user_login'   => $user_email,
					'user_pass'    => $user_pass,
					'user_email'   => $user_email,
					'display_name' => $display_name,
					'first_name'   => $display_name,
					'role'         => $role,
				)
			);

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
		} elseif ( 'update' === $action ) {
			if ( $user_id <= 0 ) {
				return new WP_Error( 'hex_missing_user', __( 'Booker not found.', 'hvar-bookings' ) );
			}

			if ( $is_protected ) {
				$protected_user = get_userdata( $user_id );
				$user_email     = $protected_user ? $protected_user->user_email : $user_email;
				$role           = 'administrator';
			}

			$result = wp_update_user(
				array(
					'ID'           => $user_id,
					'user_email'   => $user_email,
					'display_name' => $display_name,
					'first_name'   => $display_name,
					'role'         => $role,
				)
			);

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		} else {
			return new WP_Error( 'hex_invalid_action', __( 'Unknown booker action.', 'hvar-bookings' ) );
		}

		update_user_meta( $user_id, 'hex_booker_phone', $phone );
		update_user_meta( $user_id, 'hex_booker_initials', substr( $initials, 0, 10 ) );

		if ( defined( 'HEX_BOOKER_ACCESS_EMAILS_ENABLED' ) && HEX_BOOKER_ACCESS_EMAILS_ENABLED && ! $is_protected && ( 'create' === $action || ( '' !== $previous_role && $previous_role !== $role ) ) ) {
			self::send_booker_access_email( $user_id, $role, 'create' === $action ? $user_pass : '' );
		}

		return 'create' === $action ? __( 'Booker created successfully.', 'hvar-bookings' ) : __( 'Booker updated successfully.', 'hvar-bookings' );
	}

	protected static function handle_booker_delete( $user_id ) {
		if ( $user_id <= 0 ) {
			return new WP_Error( 'hex_missing_user', __( 'Booker not found.', 'hvar-bookings' ) );
		}

		if ( get_current_user_id() === $user_id ) {
			return new WP_Error( 'hex_delete_self', __( 'You cannot delete your own booker account while you are logged in.', 'hvar-bookings' ) );
		}

		if ( Hex_Bookings_Plugin::is_protected_admin_user( $user_id ) ) {
			return new WP_Error( 'hex_delete_protected', __( 'The protected administrator account cannot be deleted.', 'hvar-bookings' ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user instanceof WP_User ) {
			return new WP_Error( 'hex_missing_user', __( 'Booker not found.', 'hvar-bookings' ) );
		}

		if ( ! array_intersect( array( 'booker', 'booking_manager' ), (array) $user->roles ) ) {
			return new WP_Error( 'hex_delete_invalid_role', __( 'Only Booker and Booking Manager accounts can be deleted from this screen.', 'hvar-bookings' ) );
		}

		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$deleted = wp_delete_user( $user_id, get_current_user_id() );

		if ( ! $deleted ) {
			return new WP_Error( 'hex_delete_failed', __( 'Booker could not be deleted.', 'hvar-bookings' ) );
		}

		return __( 'Booker deleted successfully.', 'hvar-bookings' );
	}

	protected static function send_booker_access_email( $user_id, $role, $temporary_password = '' ) {
		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) ) {
			return false;
		}

		$role_label = self::role_label( $role );
		$login_url  = home_url( '/internal-dispatch/' );
		$site_name  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$subject    = sprintf(
			/* translators: %s: user role label. */
			__( 'Your Hvar Excursions %s access is ready', 'hvar-bookings' ),
			$role_label
		);

		$lines = array(
			sprintf(
				/* translators: %s: user display name. */
				__( 'Hi %s,', 'hvar-bookings' ),
				$user->display_name ?: $user->user_login
			),
			'',
			sprintf(
				/* translators: %s: user role label. */
				__( 'You have been assigned the %s role for the Hvar Excursions booking app.', 'hvar-bookings' ),
				$role_label
			),
			sprintf(
				/* translators: %s: login URL. */
				__( 'You can log in here: %s', 'hvar-bookings' ),
				$login_url
			),
			sprintf(
				/* translators: %s: user login. */
				__( 'Username: %s', 'hvar-bookings' ),
				$user->user_login
			),
		);

		if ( '' !== $temporary_password ) {
			$lines[] = sprintf(
				/* translators: %s: temporary password. */
				__( 'Temporary password: %s', 'hvar-bookings' ),
				$temporary_password
			);
		}

		$lines[] = '';
		$lines[] = __( 'If you already changed your password, keep using your current password.', 'hvar-bookings' );
		$lines[] = '';
		$lines[] = $site_name;

		return wp_mail(
			$user->user_email,
			$subject,
			implode( "\n", $lines ),
			array( 'Content-Type: text/plain; charset=UTF-8' )
		);
	}

	protected static function get_bookers() {
		$users  = get_users(
			array(
				'role__in' => array( 'administrator', 'booking_manager', 'booker' ),
				'orderby'  => 'display_name',
				'order'    => 'ASC',
			)
		);
		$items = array();

		foreach ( $users as $user ) {
			$role         = ! empty( $user->roles ) ? $user->roles[0] : 'booker';
			$is_protected = Hex_Bookings_Plugin::is_protected_admin_user( $user->ID );
			$items[] = array(
				'id'           => (int) $user->ID,
				'display_name' => (string) $user->display_name,
				'email'        => (string) $user->user_email,
				'phone'        => (string) get_user_meta( $user->ID, 'hex_booker_phone', true ),
				'initials'     => (string) get_user_meta( $user->ID, 'hex_booker_initials', true ),
				'role'         => $role,
				'role_label'   => self::role_label( $role ),
				'is_protected' => $is_protected,
				'can_delete'   => ! $is_protected && get_current_user_id() !== (int) $user->ID && in_array( $role, array( 'booker', 'booking_manager' ), true ),
			);
		}

		return $items;
	}

	protected static function get_editing_booker() {
		$user_id = isset( $_GET['edit_user'] ) ? (int) $_GET['edit_user'] : 0;
		if ( $user_id <= 0 ) {
			return null;
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return null;
		}

		$role = ! empty( $user->roles ) ? $user->roles[0] : 'booker';

		return array(
			'id'           => (int) $user->ID,
			'display_name' => (string) $user->display_name,
			'email'        => (string) $user->user_email,
			'phone'        => (string) get_user_meta( $user->ID, 'hex_booker_phone', true ),
			'initials'     => (string) get_user_meta( $user->ID, 'hex_booker_initials', true ),
			'role'         => Hex_Bookings_Plugin::is_protected_admin_user( $user->ID ) ? 'administrator' : ( in_array( $role, array( 'booker', 'booking_manager' ), true ) ? $role : 'booker' ),
			'is_protected' => Hex_Bookings_Plugin::is_protected_admin_user( $user->ID ),
		);
	}

	protected static function role_label( $role ) {
		if ( 'booking_manager' === $role ) {
			return __( 'Booking Manager', 'hvar-bookings' );
		}

		if ( 'administrator' === $role ) {
			return __( 'Administrator', 'hvar-bookings' );
		}

		return __( 'Booker', 'hvar-bookings' );
	}

	protected static function render_settings_row( $group, $index, $item, $include_coordinates ) {
		$label       = (string) ( $item['label'] ?? '' );
		$value       = (string) ( $item['value'] ?? '' );
		$coordinates = (string) ( $item['coordinates'] ?? '' );
		?>
		<div class="hex-settings-row<?php echo $include_coordinates ? ' has-coordinates' : ''; ?>" data-hex-settings-row>
			<label>
				<span><?php esc_html_e( 'Label', 'hvar-bookings' ); ?></span>
				<input type="text" name="hex_booking_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $label ); ?>">
			</label>
			<label>
				<span><?php esc_html_e( 'Value Key', 'hvar-bookings' ); ?></span>
				<input type="text" name="hex_booking_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'leave empty to auto-generate', 'hvar-bookings' ); ?>">
			</label>
			<?php if ( $include_coordinates ) : ?>
				<label>
					<span><?php esc_html_e( 'Coordinates', 'hvar-bookings' ); ?></span>
					<input type="text" name="hex_booking_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][coordinates]" value="<?php echo esc_attr( $coordinates ); ?>" placeholder="<?php esc_attr_e( '43.1729,16.4426', 'hvar-bookings' ); ?>">
				</label>
			<?php endif; ?>
			<button type="button" class="hex-bookings-app__button hex-settings-row__remove" data-hex-settings-remove><?php esc_html_e( 'Remove', 'hvar-bookings' ); ?></button>
		</div>
		<?php
	}

	public static function is_bookings_route() {
		if ( (bool) get_query_var( 'hex_internal_bookings' ) ) {
			return true;
		}

		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

		return 'internal-bookings' === $request_path;
	}

	public static function is_dispatch_route() {
		if ( (bool) get_query_var( 'hex_internal_dispatch' ) ) {
			return true;
		}

		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

		return 'internal-dispatch' === $request_path;
	}

	public static function is_bookers_route() {
		if ( (bool) get_query_var( 'hex_internal_bookers' ) ) {
			return true;
		}

		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

		return 'internal-bookers' === $request_path;
	}

	public static function is_settings_route() {
		if ( (bool) get_query_var( 'hex_internal_booking_settings' ) ) {
			return true;
		}

		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

		return 'internal-booking-settings' === $request_path;
	}
}

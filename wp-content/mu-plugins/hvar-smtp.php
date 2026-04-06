<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'phpmailer_init',
	static function ( $phpmailer ) {
		if ( ! defined( 'HEX_SMTP_HOST' ) || '' === HEX_SMTP_HOST ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host       = HEX_SMTP_HOST;
		$phpmailer->Port       = defined( 'HEX_SMTP_PORT' ) ? (int) HEX_SMTP_PORT : 465;
		$phpmailer->SMTPAuth   = true;
		$phpmailer->Username   = defined( 'HEX_SMTP_USERNAME' ) ? HEX_SMTP_USERNAME : '';
		$phpmailer->Password   = defined( 'HEX_SMTP_PASSWORD' ) ? HEX_SMTP_PASSWORD : '';
		$phpmailer->SMTPSecure = defined( 'HEX_SMTP_SECURE' ) ? HEX_SMTP_SECURE : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		$phpmailer->CharSet    = 'UTF-8';

		if ( defined( 'HEX_SMTP_FROM_EMAIL' ) && '' !== HEX_SMTP_FROM_EMAIL ) {
			$phpmailer->setFrom(
				HEX_SMTP_FROM_EMAIL,
				defined( 'HEX_SMTP_FROM_NAME' ) ? HEX_SMTP_FROM_NAME : 'Hvar Excursions',
				false
			);
		}
	},
	20
);

add_filter(
	'wp_mail_from',
	static function ( $from_email ) {
		return defined( 'HEX_SMTP_FROM_EMAIL' ) && '' !== HEX_SMTP_FROM_EMAIL ? HEX_SMTP_FROM_EMAIL : $from_email;
	}
);

add_filter(
	'wp_mail_from_name',
	static function ( $from_name ) {
		return defined( 'HEX_SMTP_FROM_NAME' ) && '' !== HEX_SMTP_FROM_NAME ? HEX_SMTP_FROM_NAME : $from_name;
	}
);

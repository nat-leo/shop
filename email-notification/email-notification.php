<?php
/**
 * Plugin Name: Email Notification
 * Description: Emails the configured administrator when a WooCommerce product is added to a cart.
 * Version: 1.1.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the Gmail app password supplied by the WordPress runtime.
 *
 * @return string Gmail app password, or an empty string when unavailable.
 */
function email_notification_get_gmail_app_password() {
	$password = getenv( 'WP_GMAIL_APP_PASSWORD' );

	if ( false === $password ) {
		$env_file = ABSPATH . '.env';

		if ( is_readable( $env_file ) ) {
			$settings = parse_ini_file( $env_file, false, INI_SCANNER_RAW );
			$password = is_array( $settings ) && isset( $settings['WP_GMAIL_APP_PASSWORD'] )
				? $settings['WP_GMAIL_APP_PASSWORD']
				: false;
		}
	}

	if ( false === $password && defined( 'WP_GMAIL_APP_PASSWORD' ) ) {
		$password = WP_GMAIL_APP_PASSWORD;
	}

	if ( ! is_string( $password ) ) {
		return '';
	}

	// Google displays app passwords in groups; SMTP expects the raw value.
	return preg_replace( '/\s+/', '', trim( $password ) );
}

/**
 * Send WordPress mail through the authenticated Gmail account.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $mailer WordPress PHPMailer instance.
 * @return void
 */
function email_notification_configure_gmail_smtp( $mailer ) {
	$password = email_notification_get_gmail_app_password();

	if ( '' === $password ) {
		return;
	}

	$mailer->isSMTP();
	$mailer->Host       = 'smtp.gmail.com';
	$mailer->SMTPAuth   = true;
	$mailer->Port       = 587;
	$mailer->SMTPSecure = 'tls';
	$mailer->Username   = 'n8wyliu@gmail.com';
	$mailer->Password   = $password;
	$mailer->setFrom( 'n8wyliu@gmail.com', 'Shop Notifications', false );
}

/**
 * Return the email address for the intended administrator account.
 *
 * Both the login and email address are checked so that notifications are not
 * sent to an unexpected recipient if either value is changed independently.
 *
 * @return string Administrator email address, or an empty string when the
 *                expected administrator account cannot be found.
 */
function email_notification_get_recipient() {
	$user = get_user_by( 'login', 'dev' );

	if (
		! $user instanceof WP_User ||
		! in_array( 'administrator', (array) $user->roles, true ) ||
		'n8wyliu@gmail.com' !== strtolower( $user->user_email )
	) {
		return '';
	}

	return 'n8wyliu@gmail.com';
}

/**
 * Notify the intended administrator after a product is added to the cart.
 *
 * @param string $cart_item_key WooCommerce cart item key.
 * @param int    $product_id    Product ID.
 * @param int    $quantity      Quantity added.
 * @return void
 */
function email_notification_product_added_to_cart( $cart_item_key, $product_id, $quantity ) {
	$recipient = email_notification_get_recipient();

	if ( '' === $recipient ) {
		return;
	}

	$product = wc_get_product( $product_id );
	$name    = $product ? $product->get_name() : "Product #{$product_id}";

	wp_mail(
		$recipient,
		'Product added to cart',
		sprintf(
			'A customer added %s × %s to their cart.',
			absint( $quantity ),
			$name
		),
		array( 'From: Shop Notifications <n8wyliu@gmail.com>' )
	);
}

/**
 * Log immediate mail transport failures when WordPress debug logging is on.
 *
 * @param WP_Error $error Mail error raised by WordPress.
 * @return void
 */
function email_notification_log_mail_failure( $error ) {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'Email Notification: ' . $error->get_error_message() );
	}
}

// After a product is successfully added to the cart.
add_action( 'phpmailer_init', 'email_notification_configure_gmail_smtp' );
add_action( 'woocommerce_add_to_cart', 'email_notification_product_added_to_cart', 10, 3 );
add_action( 'wp_mail_failed', 'email_notification_log_mail_failure' );

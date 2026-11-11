# Email Notification

A small WordPress plugin that emails a specific administrator whenever a
customer successfully adds a WooCommerce product to their cart.

## Recipient

Notifications are sent to `n8wyliu@gmail.com` only when WordPress contains a
user that matches all three of these requirements:

- Username: `dev`
- Email address: `n8wyliu@gmail.com`
- Role: Administrator

Checking all three values prevents mail from being sent to an unintended user
if the `dev` username or its email address is changed. The plugin does not use
the global `admin_email` option.

## Requirements

- WordPress
- WooCommerce
- A Gmail app password exposed to PHP as `WP_GMAIL_APP_PASSWORD`
- The administrator account described above

## Installation

1. Copy the `email-notification` directory into `wp-content/plugins/`.
2. In WordPress, open **Plugins > Installed Plugins**.
3. Activate **Email Notification**.
4. Confirm that the `dev` user is an Administrator and has the email address
   `n8wyliu@gmail.com`.
5. Make `WP_GMAIL_APP_PASSWORD` available to the PHP process. The plugin reads
   it with `getenv()` and also supports a same-named `wp-config.php` constant.
6. Add a product to a cart and verify that the message arrives.

Activation does not create or modify a WordPress user.

## What the email looks like

For a product named **Canvas Backpack** added with a quantity of two, the
message is:

```text
To: n8wyliu@gmail.com
Subject: Product added to cart

A customer added 2 × Canvas Backpack to their cart.
```

If WooCommerce cannot load the product object, the message falls back to its
numeric ID:

```text
A customer added 1 × Product #123 to their cart.
```

## Behavior examples

| User state | Result |
| --- | --- |
| `dev` is an Administrator with `n8wyliu@gmail.com` | Email is sent |
| `dev` exists but has a different email | No email is sent |
| `dev` has the Subscriber or Customer role | No email is sent |
| No user named `dev` exists | No email is sent |
| The global site administrator email is different | It is ignored |
| A product is added with quantity `3` | The email reports quantity `3` |

## Illustrative examples

### Create the administrator with WP-CLI

If the account does not exist yet:

```bash
wp user create dev n8wyliu@gmail.com --role=administrator
```

If it already exists, update it:

```bash
wp user update dev --user_email=n8wyliu@gmail.com --role=administrator
```

### Verify the administrator with WP-CLI

```bash
wp user get dev --fields=ID,user_login,user_email,roles
```

Example output:

```text
Field       Value
ID          7
user_login  dev
user_email  n8wyliu@gmail.com
roles       administrator
```

### Verify the recipient in PHP

Run this only after WordPress and the plugin have loaded:

```php
$recipient = email_notification_get_recipient();

if ( '' === $recipient ) {
    error_log( 'The expected administrator account is unavailable.' );
} else {
    error_log( 'Cart notifications will be sent to ' . $recipient );
}
```

### Trigger the notification action in PHP

This example is useful in a local development environment. Replace `123` with
a real product ID:

```php
do_action(
    'woocommerce_add_to_cart',
    'example-cart-item-key',
    123,
    2
);
```

### Capture outgoing mail during development

The `wp_mail` filter can inspect a message without changing this plugin. This
example writes the mail arguments to the WordPress debug log:

```php
add_filter( 'wp_mail', function ( $mail ) {
    error_log( print_r( $mail, true ) );
    return $mail;
} );
```

Use this only in development and enable logging in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

### Add an HTML content type

WordPress sends plain text by default. A site can opt into HTML mail with a
filter:

```php
add_filter( 'wp_mail_content_type', function () {
    return 'text/html';
} );
```

The plugin's current message is plain text, so HTML is not required.

### Gmail SMTP configuration

The plugin configures WordPress's PHPMailer instance to use authenticated Gmail
SMTP at `smtp.gmail.com` with STARTTLS on port `587`. It authenticates as and
sends from `n8wyliu@gmail.com`.

The repository's `.env` file is ignored by Git, but PHP does not read `.env`
files automatically. The process that starts WordPress must load that file or
pass the variable into the WordPress container. On a host where environment
variables are unavailable, define the value outside this repository in
`wp-config.php`:

```php
define( 'WP_GMAIL_APP_PASSWORD', 'your-app-password' );
```

Never use the normal Google account password or commit the app password. The
Gmail account must have 2-Step Verification enabled and the value must be a
Google app password.

Without a reachable transport, a typical local error is:

```text
Could not instantiate mail function.
sendmail: can't connect to remote host (127.0.0.1): Connection refused
```

### Change the sender name

The recipient is fixed by this plugin, but WordPress filters can customize the
sender:

```php
add_filter( 'wp_mail_from_name', function () {
    return get_bloginfo( 'name' ) . ' Store';
} );
```

### Change the sender address

```php
add_filter( 'wp_mail_from', function () {
    return 'store@example.com';
} );
```

Use an address that your mail provider authorizes for the site's domain.

### Observe delivery failures

`wp_mail()` returning successfully means WordPress handed the message to its
mail transport; it does not guarantee inbox delivery. This hook logs immediate
transport errors:

```php
add_action( 'wp_mail_failed', function ( $error ) {
    error_log( $error->get_error_message() );
} );
```

## Execution flow

1. WooCommerce fires `woocommerce_add_to_cart`.
2. The plugin looks up the WordPress user whose login is `dev`.
3. It verifies the Administrator role and exact expected email address.
4. It loads the added product to obtain its display name.
5. It sends the quantity and product name to `n8wyliu@gmail.com` with
   `wp_mail()`.

## Troubleshooting

### No message is sent

Check the user first:

```bash
wp user get dev --fields=user_login,user_email,roles
```

Then confirm that WooCommerce is active and that the product was actually
added. Finally, confirm that `WP_GMAIL_APP_PASSWORD` is visible to the
WordPress PHP process and review the WordPress debug log for SMTP errors.

### The hook runs more often than expected

The event occurs each time WooCommerce successfully adds an item. Multiple
adds, separate browser requests, or custom code that calls the action can each
produce a separate notification.

### The email is accepted but never arrives

Confirm the Gmail app password is available to PHP, inspect spam folders, and
review the WordPress debug log. A successful `wp_mail()` return value confirms
submission, not inbox delivery.

### `wordpress@localhost` is reported as invalid

Local WordPress sites derive their default sender from the site hostname, and
`localhost` is not a valid email domain. This plugin supplies
`n8wyliu@gmail.com` as its From address. The SMTP provider must authorize that
sender address before it can deliver mail.

## Security and privacy notes

- The email does not include customer personal data.
- The recipient is not derived from request data.
- The username, role, and email must all match before mail is sent.
- Avoid logging complete email payloads in production because future message
  changes could add private information.

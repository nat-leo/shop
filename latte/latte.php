<?php
/**
 * Plugin Name: Latte
 * Description: A React latte configurator. Add it to a page with [latte].
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */

defined('ABSPATH') || exit;

add_shortcode('latte', function () {
    // Enqueue only when the shortcode actually renders. Use WordPress's React.
    wp_enqueue_script('latte-configurator', plugins_url('assets/latte.js', __FILE__), array('wp-element'), '1.0.0', true);
    // A stylesheet link in the shortcode also works when rendering after wp_head.
    $stylesheet = sprintf('<link rel="stylesheet" href="%s" media="all">', esc_url(plugins_url('assets/latte.css', __FILE__) . '?ver=1.0.0'));
    return $stylesheet . '<div class="latte-root"><p>Loading latte configurator…</p><noscript>Enable JavaScript to configure your latte.</noscript></div>';
});

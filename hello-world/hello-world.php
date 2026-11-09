<?php
/**
 * Plugin Name: Hello World
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

add_shortcode('hello_world', function () {
    return '<p>Hello, world!</p>';
});
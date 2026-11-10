# Let's Shop
Wordpress site connected to Square 

# Quickstart

[Wordpress.org](https://wordpress.org/) is an open source way to build websites.

[Square API](https://developer.squareup.com/us/en) is a way to connect Wordpress sites to Square to take advantage of the Square Services

# Wordpress

[Learn Wordpress](https://learn.wordpress.org/)

There's a local way to get Wordpress running on yo' computer. Using the `npm` package `@wordpress/env`.

## Install:

Install Docker and Node.js, and then installing wordpress is just one line.
```
npm install -g @wordpress/env
```

## Quickstart:

Ensure that Docker is running. This line of code sets up Wordpress for Docker. It may take a second initially.
```
npx wp-env start
```

```
npx wp-env stop
```

Then open [http://localhost:8889]().

Admin: [http://localhost:8888/wp-admin]().
 - username: `admin`
 - password: `password`

Change the passwords from their defaults before publishing.

## Plugins

**Create a plugin folder on your computer**, then use `wp-env` to load it into the Docker container.

**1. In your project folder, create these files:**

```text
my-project/
├── .wp-env.json
└── hello-world/
    └── hello-world.php
```

**2. Put this in `.wp-env.json`:**

```json
{
  "plugins": ["./hello-world"]
}
```

**3. Put this in `hello-world/hello-world.php`:**

```php
<?php
/**
 * Plugin Name: Hello World
 * Description: My first WordPress plugin.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

add_shortcode('hello_world', function () {
    return '<p>Hello, world!</p>';
});
```

**4. Run from `my-project/`:**

```bash
npx wp-env start
npx wp-env run cli wp plugin activate hello-world
```

**5. Open [localhost:8888/wp-admin](http://localhost:8888/wp-admin)** and sign in:

- Username: `admin`
- Password: `password`

Edit a page, add a **Shortcode** block, enter `[hello_world]`, and preview it.

Your local PHP file is mounted into WordPress, so saved code changes appear when you refresh.

# WooCommerce
shopifying Wordpress may also require [WooCommerce](). This is an open source with plenty of [extensions](https://woocommerce.com/product-category/woocommerce-extensions/?categoryIds=1021&collections=product&page=1&min=0&max=0) and [documentation](https://woocommerce.com/documentation/woocommerce/)

Woocommerce has a Square Plugin.

Here's how to add WooCommerce to Wordpress:
```
npx wp-env start
npx wp-env run cli wp plugin install woocommerce --activate
```
Super easy.

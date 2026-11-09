# Make a Worpress Plugin

**Create a plugin folder on your computer**, then tell `wp-env` to load it.

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

`activate <FOLDER>` must match the plugin's folder. `hello-world` is the name of the folder where the plugin lives. 

**5. Open [localhost:8888/wp-admin](http://localhost:8888/wp-admin)** and sign in:

- Username: `admin`
- Password: `password`

Edit a page, add a **Shortcode** block, enter `[hello_world]`, and preview it.

Your local PHP file is mounted into WordPress, so saved code changes appear when you refresh.

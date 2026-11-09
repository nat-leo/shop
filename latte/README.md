# Latte

A React-powered WordPress shortcode plugin. No npm build or external CDN required;
it uses the React implementation provided by WordPress (`wp-element`).

## Install with wp-env

Place the `latte` folder alongside your existing `hello-world` folder. Merge this
entry into `.wp-env.json` (preserve your other settings):

```json
{
  "plugins": ["./hello-world", "./latte"]
}
```

Run from the directory containing `.wp-env.json`:

```sh
npx wp-env start
npx wp-env run cli wp plugin activate latte
```

Add a **Shortcode** block to a page, enter `[latte]`, and view the page.
Alternatively, upload `latte.zip` through Plugins → Add New → Upload Plugin,
then activate Latte.

## Options

- Size: 12oz or 16oz.
- Espresso: one of five sample origins (Ethiopia, Colombia, Guatemala, Kenya, Brazil).
- Syrups: any combination of Simple, Honey, Strawberry, Dulce de leche, Basil.
- Temperature: Hot or Iced.
- Service: Here or To go.

Replace the `origins` array in `assets/latte.js` with your actual coffee names.
Each shortcode instance has independent state and accessible native controls.
JavaScript loads only when the shortcode renders. CSS is scoped to the configurator.
Selections reset on refresh. This is a configurator preview; prices, cart, Square,
inventory and payments are not connected. No made-up prices are shown.

Requires WordPress 6.2+, PHP 7.4+, and a theme that calls `wp_footer()`.

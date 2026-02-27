=== WP Barcode API ===
Tags: barcode, qrcode, ean, elementor, api, generator
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful plugin for generating barcodes and QR codes via shortcodes or Elementor using the BarcodeAPI.org interface.

== Description ==

WP Barcode API allows for easy integration of barcodes into your WordPress website. The plugin uses the API from [barcodeapi.org](https://barcodeapi.org) to dynamically generate a variety of barcode formats.

By default, generation is done client-side (via URL), which reduces the load on your server. For static use cases, the plugin also offers a backend generator that saves barcodes as image files directly to your media library.

**Key Features:**

*   **Shortcode Support:** Easily insert barcodes into posts or pages with `[barcode]`.
*   **Elementor Widget:** A native widget for Elementor with styling options (width, height, rotation) and support for dynamic tags (e.g., using Post ID or Custom Fields as barcode content).
*   **Static Generation:** Manually create barcodes in the admin area and save them permanently to the WordPress Media Library.
*   **API Key Support:** Optionally provide an API key to benefit from higher rate limits.
*   **Supported Formats:** QR Code, EAN-13, EAN-8, Code 128, Code 39, UPC, ITF, MSI, Pharmacode, and many more.

== Installation ==

1. Upload the `wp-barcode-api` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. (Optional) Go to *Settings > Barcode API* and enter your API key if you have one.

== Frequently Asked Questions ==

= Do I need an API Key? =

For low traffic, the barcodeapi.org API is often free to use (please check their website for current rate limits). For professional use or high traffic, we recommend purchasing a key.

= What data is transmitted? =

The plugin generates image URLs that point to `barcodeapi.org`. The visitor's IP address and the barcode content are transmitted to the API to generate the image.

= How do I use the shortcode? =

Use the shortcode as follows:
`[barcode content="123456" type="ean13" width="200" height="100" show_text="true"]`

Available parameters:
*   `content`: The content of the barcode (text or URL).
*   `type`: The type (e.g., `qrcode`, `ean13`, `code128`, `auto`).
*   `width`: Width in pixels.
*   `height`: Height in pixels.
*   `show_text`: `true` or `false` (shows the text below the barcode).
*   `rotation`: `N` (Normal), `R` (Right), `L` (Left), `I` (Inverted).

== Changelog ==

= 1.0.4 =
* Enhancement: Full internationalization (i18n) of the plugin. All strings are now translatable.

= 1.0.3 =
* Enhancement: Improved admin area code quality by externalizing CSS and JavaScript.

= 1.0.2 =
* Fix: Adjusted update checker for public repository.

= 1.0.1 =
* Fix: Corrected release workflow for GitHub Actions.

= 1.0.0 =
*   Initial release.
*   Added: Shortcode `[barcode]`.
*   Added: Elementor Widget.
*   Added: Admin page with API Key setting and generator.
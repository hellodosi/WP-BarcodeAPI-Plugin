# WP Barcode API

A powerful WordPress plugin for generating barcodes and QR codes via shortcodes or Elementor using the BarcodeAPI.org interface.

## Features

*   **Shortcode Support:** Easily insert barcodes into posts or pages with `[barcode]`.
*   **Elementor Widget:** Native widget with extensive styling options and support for dynamic tags.
*   **Static Generation:** Manually create barcodes in the admin area and save them permanently to the WordPress Media Library.
*   **API Key Support:** Optionally provide an API key to benefit from higher rate limits.
*   **Client-Side Rendering:** By default, barcodes are generated directly in the visitor's browser to reduce server load.
*   **Status Check:** Check your API limits (server & client) directly in the backend.

## Supported Formats

The plugin supports all common formats of the API, including:
*   QR Code
*   EAN-13, EAN-8
*   Code 128, Code 39, Code 93
*   UPC-A, UPC-E
*   ITF, MSI, Pharmacode
*   Data Matrix, PDF417, Aztec
*   and many more.

## Installation

1.  Upload the plugin directory to the `/wp-content/plugins/` folder or install the ZIP archive via the WordPress backend.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  (Optional) Go to *Settings > Barcode API* and enter your API key.

## Usage

### Shortcode

Use the shortcode anywhere in your content:

```shortcode
[barcode content="123456" type="ean13" width="200" height="100" show_text="true"]
```

**Attribute:**
*   `content`: The Content (Text or URL).
*   `type`: The Barcode-Type (e.g. `qrcode`, `ean13`, `auto`).
*   `width`, `height`: Dimensions in pixels.
*   `fg`, `bg`: Foreground- and HBackgroundcolor (Hex, z.B. `ff0000`).
*   `show_text`: `true` or `false`.

### Elementor

Search for the widget in the Elementor editor. **Barcode API**.
*   **Content:** Select the type and enter the content (supports dynamic tags).
*   **API Settings:** Configure API-specific parameters such as colors and rotation.
*   **Style:** Use Elementor's standard image options for borders, shadows, size, and alignment.

### Static generator

Under *Settings > Barcode API*, you will find a generator that allows you to create barcodes once and import them into your media library as image files. This is ideal for static content that does not need to be regenerated every time the page is viewed.

## API Limits

The plugin uses the API from barcodeapi.org. Please note the API's rate limits. You can view your current usage status (both for the server and for your client) in the plugin's admin area.

## License

GPL v2 or later
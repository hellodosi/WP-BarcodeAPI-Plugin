<?php
/**
 * Plugin Name:       WP Barcode API
 * Plugin URI:        https://github.com/hellodosi/WP-BarcodeAPI-Plugin
 * Description:       A powerful plugin for generating barcodes and QR codes via shortcodes or Elementor using the BarcodeAPI.org interface.
 * Version:           1.0.4
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Dominik Scharrer
 * Author URI:        https://github.com/hellodosi
 * License:           GPL v2 or later
 * Text Domain:       wp-barcode-api
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Barcode_API' ) ) {

	/**
	 * Main plugin class.
	 */
	class WP_Barcode_API {

		/**
		 * The single instance of the class (singleton).
		 */
		private static $instance = null;

		/**
		 * Returns the instance of the class.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {
			$this->init_hooks();
			$this->init_updater();
		}

		/**
		 * Registers all hooks (actions & filters).
		 */
		private function init_hooks() {
			// Admin Menu
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			
			// Register settings
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Shortcode
			add_shortcode( 'barcode', array( $this, 'render_shortcode' ) );

			// Load Elementor widget
			add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widget' ) );

			// Load admin assets
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		/**
		 * Registers the Elementor widget.
		 */
		public function register_elementor_widget( $widgets_manager ) {
			require_once( __DIR__ . '/elementor-widget.php' );
			$widgets_manager->register( new \Elementor_Barcode_Widget() );
		}

		/**
		 * Enqueues CSS and JS for the admin page.
		 */
		public function enqueue_admin_assets( $hook_suffix ) {
			// Only load on our settings page
			if ( 'settings_page_wp-barcode-api' !== $hook_suffix ) {
				return;
			}

			$plugin_dir_url = plugin_dir_url( __FILE__ );

			wp_enqueue_style( 'wp-barcode-api-admin', $plugin_dir_url . 'admin/style.css', array(), $this->get_plugin_version() );

			wp_enqueue_script( 'wp-barcode-api-admin', $plugin_dir_url . 'admin/script.js', array(), $this->get_plugin_version(), true );

			// Pass translated strings to JavaScript
			wp_localize_script( 'wp-barcode-api-admin', 'wpBarcodeApiL10n', array(
				'networkError'  => __( 'Network response was not OK:', 'wp-barcode-api' ),
				'ip'            => __( 'IP:', 'wp-barcode-api' ),
				'consumed'      => __( 'Consumed:', 'wp-barcode-api' ),
				'status'        => __( 'Status:', 'wp-barcode-api' ),
				'blocked'       => __( 'Blocked', 'wp-barcode-api' ),
				'active'        => __( 'Active', 'wp-barcode-api' ),
				'unknown'       => __( 'Unknown', 'wp-barcode-api' ),
				'loadingError'  => __( 'Error loading data:', 'wp-barcode-api' ),
				'loading'       => __( 'Loading data...', 'wp-barcode-api' ),
			) );

		}

		/**
		 * Registers the admin page under Settings.
		 */
		public function register_admin_menu() {
			add_options_page(
				__( 'Barcode API Settings', 'wp-barcode-api' ),
				__( 'Barcode API', 'wp-barcode-api' ),
				'manage_options',
				'wp-barcode-api',
				array( $this, 'render_admin_page' )
			);
		}

		/**
		 * Registers the plugin settings.
		 */
		public function register_settings() {
			register_setting( 'wp_barcode_api_options', 'wp_barcode_api_key' );
		}

		/**
		 * Returns a list of all supported barcode types.
		 * Source: https://barcodeapi.org/types.html
		 */
		public static function get_supported_types() {
			return array(
				'auto'            => __( 'Auto Detect', 'wp-barcode-api' ),
				'qrcode'          => __( 'QR Code', 'wp-barcode-api' ),
				'code128'         => __( 'Code 128', 'wp-barcode-api' ),
				'code39'          => __( 'Code 39', 'wp-barcode-api' ),
				'code93'          => __( 'Code 93', 'wp-barcode-api' ),
				'codabar'         => __( 'Codabar', 'wp-barcode-api' ),
				'ean13'           => __( 'EAN-13', 'wp-barcode-api' ),
				'ean8'            => __( 'EAN-8', 'wp-barcode-api' ),
				'upc'             => __( 'UPC-A', 'wp-barcode-api' ),
				'upce'            => __( 'UPC-E', 'wp-barcode-api' ),
				'itf'             => __( 'ITF (Interleaved 2 of 5)', 'wp-barcode-api' ),
				'itf14'           => __( 'ITF-14', 'wp-barcode-api' ),
				'msi'             => __( 'MSI', 'wp-barcode-api' ),
				'msi10'           => __( 'MSI 10', 'wp-barcode-api' ),
				'msi11'           => __( 'MSI 11', 'wp-barcode-api' ),
				'msi1010'         => __( 'MSI 1010', 'wp-barcode-api' ),
				'msi1110'         => __( 'MSI 1110', 'wp-barcode-api' ),
				'pharmacode'      => __( 'Pharmacode', 'wp-barcode-api' ),
				'datamatrix'      => __( 'Data Matrix', 'wp-barcode-api' ),
				'pdf417'          => __( 'PDF417', 'wp-barcode-api' ),
				'aztec'           => __( 'Aztec Code', 'wp-barcode-api' ),
				'telepen'         => __( 'Telepen', 'wp-barcode-api' ),
				'kix'             => __( 'KIX (Klant index)', 'wp-barcode-api' ),
				'rm4scc'          => __( 'RM4SCC (Royal Mail)', 'wp-barcode-api' ),
				'onecode'         => __( 'USPS Intelligent Mail', 'wp-barcode-api' ),
				'gs1-128'         => __( 'GS1-128', 'wp-barcode-api' ),
				'isbn'            => __( 'ISBN', 'wp-barcode-api' ),
				'ismn'            => __( 'ISMN', 'wp-barcode-api' ),
				'issn'            => __( 'ISSN', 'wp-barcode-api' ),
				'postnet'         => __( 'Postnet', 'wp-barcode-api' ),
				'planet'          => __( 'Planet', 'wp-barcode-api' ),
				'channelcode'     => __( 'Channel Code', 'wp-barcode-api' ),
				'code11'          => __( 'Code 11', 'wp-barcode-api' ),
				'code2of5'        => __( 'Code 2 of 5', 'wp-barcode-api' ),
				'coop2of5'        => __( 'COOP 2 of 5', 'wp-barcode-api' ),
				'matrix2of5'      => __( 'Matrix 2 of 5', 'wp-barcode-api' ),
				'industrial2of5'  => __( 'Industrial 2 of 5', 'wp-barcode-api' ),
			);
		}

		/**
		 * Generates the API URL based on parameters.
		 */
		public static function get_api_url( $content, $type = 'auto', $args = array(), $use_api_key = false ) {
			$base_url = 'https://barcodeapi.org/api';
			$type     = sanitize_text_field( $type );
			$content  = rawurlencode( $content ); // rawurlencode (%20 instead of +) is safer for path segments

			// API Key hinzufügen, falls vorhanden
			if ( $use_api_key ) {
				$api_key = get_option( 'wp_barcode_api_key' );
				if ( ! empty( $api_key ) ) {
					$args['token'] = $api_key;
				}
			}

			// Sanitize colors (remove hash for API)
			if ( ! empty( $args['fg'] ) ) {
				$args['fg'] = str_replace( '#', '', $args['fg'] );
			}
			if ( ! empty( $args['bg'] ) ) {
				$args['bg'] = str_replace( '#', '', $args['bg'] );
			}

			// Build query parameters
			$query_args = array_filter( $args, function( $value ) {
				return $value !== '' && $value !== null;
			});

			$query_string = http_build_query( $query_args );
			
			return "{$base_url}/{$type}/{$content}" . ( $query_string ? "?{$query_string}" : '' );
		}

		/**
		 * Shortcode Callback: [barcode content="123" type="qrcode"]
		 */
		public function render_shortcode( $atts ) {
			$atts = shortcode_atts( array(
				'content'     => '12345678',
				'type'        => 'auto',
				'width'       => '',
				'height'      => '',
				'show_text'   => '', // true/false
				'text_size'   => '',
				'text_margin' => '',
				'rotation'    => '',
				'fg'          => '',
				'bg'          => '',
			), $atts, 'barcode' );

			$args = array(
				'width'      => $atts['width'],
				'height'     => $atts['height'],
				'text'       => $atts['show_text'],
				'textsize'   => $atts['text_size'],
				'textmargin' => $atts['text_margin'],
				'rotation'   => $atts['rotation'],
				'fg'         => $atts['fg'],
				'bg'         => $atts['bg'],
			);

			$image_url = self::get_api_url( $atts['content'], $atts['type'], $args, false ); // false = Do not use key for client-side requests

			return sprintf( '<img src="%s" alt="%s" class="wp-barcode-api-img" />', esc_url( $image_url ), sprintf( esc_attr__( 'Barcode for %s', 'wp-barcode-api' ), $atts['content'] ) );
		}

		/**
		 * Renders the admin page.
		 */
		public function render_admin_page() {
			// Statischen Barcode speichern Logik
			$message = '';
			if ( isset( $_POST['generate_static_barcode'] ) && check_admin_referer( 'wp_barcode_generate_static' ) ) {
				$message = $this->handle_static_generation();
			}
			
			// Server-seitigen Status abrufen
			$api_key = get_option( 'wp_barcode_api_key' );
			$server_url = 'https://barcodeapi.org/limiter/';
			if ( ! empty( $api_key ) ) {
				$server_url = add_query_arg( 'token', $api_key, $server_url );
			}
			$server_data = null;
			$response = wp_remote_get( $server_url );
			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$server_data = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'WP Barcode API Settings', 'wp-barcode-api' ); ?></h1>
				
				<?php if ( $message ) : ?>
					<div class="updated notice is-dismissible"><p><?php echo $message; ?></p></div>
				<?php endif; ?>

				<div class="wp-barcode-api-status-wrapper">
					<h2><?php esc_html_e( 'API Usage & Rate Limits', 'wp-barcode-api' ); ?></h2>
					<p><?php esc_html_e( 'The API limits requests based on the IP address. Since this plugin uses both client-side (browser) and server-side (WordPress) requests, both limits are displayed separately here.', 'wp-barcode-api' ); ?></p>
					
					<div class="wp-barcode-api-status-flex">
						<!-- Server Side -->
						<div class="wp-barcode-api-status-box">
							<h3><?php esc_html_e( 'Server-side (WordPress)', 'wp-barcode-api' ); ?></h3>
							<p class="description"><?php esc_html_e( 'Used for: Static generation, caching.', 'wp-barcode-api' ); ?></p>
							<?php if ( $server_data ) : ?>
								<ul>
									<li><strong><?php esc_html_e( 'IP:', 'wp-barcode-api' ); ?></strong> <?php echo esc_html( $server_data['caller'] ); ?></li>
									<li><strong><?php esc_html_e( 'Consumed:', 'wp-barcode-api' ); ?></strong> <?php echo esc_html( $server_data['tokenSpend'] ); ?> / <?php echo esc_html( $server_data['tokenLimit'] ); ?></li>
									<li><strong><?php esc_html_e( 'Status:', 'wp-barcode-api' ); ?></strong> <?php echo $server_data['enforce'] ? '<span class="status-blocked">' . esc_html__( 'Blocked', 'wp-barcode-api' ) . '</span>' : '<span class="status-active">' . esc_html__( 'Active', 'wp-barcode-api' ) . '</span>'; ?></li>
								</ul>
							<?php else : ?>
								<p><?php esc_html_e( 'Status could not be retrieved.', 'wp-barcode-api' ); ?></p>
							<?php endif; ?>
						</div>

						<!-- Client Side -->
						<div class="wp-barcode-api-status-box">
							<h3><?php esc_html_e( 'Client-side (Your Browser)', 'wp-barcode-api' ); ?></h3>
							<p class="description"><?php esc_html_e( 'Used for: Shortcodes, Elementor Widget.', 'wp-barcode-api' ); ?></p>
							<div id="bc-client-status"><?php esc_html_e( 'Loading data...', 'wp-barcode-api' ); ?></div>
						</div>
					</div>

					<p style="margin-top: 15px;">
						<a href="https://barcodeapi.org/session.html" target="_blank" class="button button-secondary"><?php esc_html_e( 'View details on barcodeapi.org', 'wp-barcode-api' ); ?></a>
						<a href="https://barcodeapi.org/#pricing" target="_blank" class="button button-primary"><?php esc_html_e( 'Buy API Key', 'wp-barcode-api' ); ?></a>
					</p>

				</div>

				<form method="post" action="options.php">
					<?php
					settings_fields( 'wp_barcode_api_options' );
					do_settings_sections( 'wp_barcode_api_options' );
					?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'API Key (Optional)', 'wp-barcode-api' ); ?></th>
							<td>
								<input type="text" name="wp_barcode_api_key" value="<?php echo esc_attr( get_option( 'wp_barcode_api_key' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Enter your key here to use higher limits.', 'wp-barcode-api' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>

				<hr>

				<h2><?php esc_html_e( 'Generate Static Barcode', 'wp-barcode-api' ); ?></h2>
				<p><?php esc_html_e( 'Use this form to generate a barcode once and save it permanently in your media library.', 'wp-barcode-api' ); ?></p>
				
				<form method="post" action="">
					<?php wp_nonce_field( 'wp_barcode_generate_static' ); ?>
					<table class="form-table">
						<tr>
							<th><?php esc_html_e( 'Content', 'wp-barcode-api' ); ?></th>
							<td><input type="text" name="bc_content" required class="regular-text" placeholder="<?php esc_attr_e( 'e.g. 12345678 or https://...', 'wp-barcode-api' ); ?>"></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Type', 'wp-barcode-api' ); ?></th>
							<td>
								<select name="bc_type">
									<?php foreach ( self::get_supported_types() as $value => $label ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Options', 'wp-barcode-api' ); ?></th>
							<td>
								<label><?php esc_html_e( 'Width:', 'wp-barcode-api' ); ?> <input type="number" name="bc_width" placeholder="<?php esc_attr_e( 'e.g. 200', 'wp-barcode-api' ); ?>" class="small-text"></label>
								<label><?php esc_html_e( 'Height:', 'wp-barcode-api' ); ?> <input type="number" name="bc_height" placeholder="<?php esc_attr_e( 'e.g. 80', 'wp-barcode-api' ); ?>" class="small-text"></label>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Colors (Hex)', 'wp-barcode-api' ); ?></th>
							<td>
								<label><?php esc_html_e( 'Foreground:', 'wp-barcode-api' ); ?> <input type="text" name="bc_fg" placeholder="<?php esc_attr_e( '000000', 'wp-barcode-api' ); ?>" class="small-text"></label>
								<label><?php esc_html_e( 'Background:', 'wp-barcode-api' ); ?> <input type="text" name="bc_bg" placeholder="<?php esc_attr_e( 'ffffff', 'wp-barcode-api' ); ?>" class="small-text"></label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p class="description"><strong><?php esc_html_e( 'Note:', 'wp-barcode-api' ); ?></strong> <?php esc_html_e( 'Not all settings (e.g., colors, text display, dimensions) are supported by every barcode type. For example, QR codes often ignore specific dimension settings in favor of readability.', 'wp-barcode-api' ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" name="generate_static_barcode" id="submit-gen" class="button button-secondary" value="<?php esc_attr_e( 'Save to Media Library', 'wp-barcode-api' ); ?>">
					</p>
				</form>

				<hr>

				<h2><?php esc_html_e( 'Shortcode Guide', 'wp-barcode-api' ); ?></h2>
				<p><?php esc_html_e( 'Use the shortcode anywhere in your content:', 'wp-barcode-api' ); ?></p>
				<code>[barcode content="YourText" type="qrcode" width="150"]</code>
				<p><?php esc_html_e( 'Available attributes:', 'wp-barcode-api' ); ?> <code>content</code>, <code>type</code>, <code>width</code>, <code>height</code>, <code>fg</code> (color), <code>bg</code> (background), <code>show_text</code>.<br><em><?php esc_html_e( 'Note: Not all attributes work with every barcode type.', 'wp-barcode-api' ); ?></em></p>
			</div>
			<?php
		}

		/**
		 * Handles saving the image to the media library.
		 */
		private function handle_static_generation() {
			if ( ! current_user_can( 'upload_files' ) ) {
				return __( 'You do not have permission to upload files.', 'wp-barcode-api' );
			}

			$content = sanitize_text_field( $_POST['bc_content'] );
			$type    = sanitize_text_field( $_POST['bc_type'] );
			$width   = sanitize_text_field( $_POST['bc_width'] );
			$height  = sanitize_text_field( $_POST['bc_height'] );
			$fg      = sanitize_text_field( $_POST['bc_fg'] );
			$bg      = sanitize_text_field( $_POST['bc_bg'] );

			$args = array(
				'width'  => $width,
				'height' => $height,
				'fg'     => $fg,
				'bg'     => $bg,
			);

			$url = self::get_api_url( $content, $type, $args, true ); // true = Use key for server-side request

			// Bild herunterladen
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return __( 'Error retrieving the barcode from the API.', 'wp-barcode-api' );
			}

			$image_data = wp_remote_retrieve_body( $response );
			$filename   = 'barcode-' . sanitize_title( $content ) . '.png';
			$upload     = wp_upload_bits( $filename, null, $image_data );

			if ( ! empty( $upload['error'] ) ) {
				return sprintf( __( 'Error saving file: %s', 'wp-barcode-api' ), $upload['error'] );
			}

			// In Mediathek einfügen
			$file_path = $upload['file'];
			$file_type = wp_check_filetype( $filename, null );
			
			$attachment = array(
				'post_mime_type' => $file_type['type'],
				'post_title'     => sprintf( __( 'Barcode: %s', 'wp-barcode-api' ), $content ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $file_path );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return sprintf(
				/* translators: %s is a link to view the image. */
				__( 'Barcode saved successfully! %s', 'wp-barcode-api' ),
				'<a href="' . esc_url( get_edit_post_link( $attach_id ) ) . '" target="_blank">' . esc_html__( 'View image', 'wp-barcode-api' ) . '</a>'
			);
		}

		/**
		 * Returns the plugin version.
		 *
		 * @return string
		 */
		private function get_plugin_version() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugin_data = get_plugin_data( __FILE__ );
			return $plugin_data['Version'];
		}

		/**
		 * Initializes the update checker.
		 */
		private function init_updater() {
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php' ) ) {
				require 'plugin-update-checker/plugin-update-checker.php';
				$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
					'https://github.com/hellodosi/WP-BarcodeAPI-Plugin/',
					__FILE__,
					'wp-barcode-api'
				);
			}
		}
	}

	// Start the plugin
	WP_Barcode_API::get_instance();
}
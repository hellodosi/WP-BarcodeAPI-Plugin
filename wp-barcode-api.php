<?php
/**
 * Plugin Name:       WP Barcode API
 * Plugin URI:        https://github.com/hellodosi/WP-BarcodeAPI-Plugin
 * Description:       Ein WordPress Plugin zur Bereitstellung einer Barcode API.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Dominik Scharrer
 * Author URI:        https://github.com/hellodosi
 * License:           GPL v2 or later
 * Text Domain:       wp-barcode-api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Barcode_API' ) ) {

	/**
	 * Hauptklasse des Plugins.
	 */
	class WP_Barcode_API {

		/**
		 * Die einzige Instanz der Klasse (Singleton).
		 */
		private static $instance = null;

		/**
		 * Gibt die Instanz der Klasse zurück.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Konstruktor.
		 */
		private function __construct() {
			$this->init_hooks();
			$this->init_updater();
		}

		/**
		 * Registriert alle Hooks (Actions & Filters).
		 */
		private function init_hooks() {
			// Admin Menü
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			
			// Settings registrieren
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Shortcode
			add_shortcode( 'barcode', array( $this, 'render_shortcode' ) );

			// Elementor Widget laden
			add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widget' ) );
		}

		/**
		 * Registriert das Elementor Widget.
		 */
		public function register_elementor_widget( $widgets_manager ) {
			require_once( __DIR__ . '/elementor-widget.php' );
			$widgets_manager->register( new \Elementor_Barcode_Widget() );
		}

		/**
		 * Registriert die Admin-Seite unter Einstellungen.
		 */
		public function register_admin_menu() {
			add_options_page(
				'Barcode API Einstellungen',
				'Barcode API',
				'manage_options',
				'wp-barcode-api',
				array( $this, 'render_admin_page' )
			);
		}

		/**
		 * Registriert die Plugin-Einstellungen.
		 */
		public function register_settings() {
			register_setting( 'wp_barcode_api_options', 'wp_barcode_api_key' );
		}

		/**
		 * Gibt eine Liste aller unterstützten Barcode-Typen zurück.
		 * Quelle: https://barcodeapi.org/types.html
		 */
		public static function get_supported_types() {
			return array(
				'auto'            => 'Auto Detect',
				'qrcode'          => 'QR Code',
				'code128'         => 'Code 128',
				'code39'          => 'Code 39',
				'code93'          => 'Code 93',
				'codabar'         => 'Codabar',
				'ean13'           => 'EAN-13',
				'ean8'            => 'EAN-8',
				'upc'             => 'UPC-A',
				'upce'            => 'UPC-E',
				'itf'             => 'ITF (Interleaved 2 of 5)',
				'itf14'           => 'ITF-14',
				'msi'             => 'MSI',
				'msi10'           => 'MSI 10',
				'msi11'           => 'MSI 11',
				'msi1010'         => 'MSI 1010',
				'msi1110'         => 'MSI 1110',
				'pharmacode'      => 'Pharmacode',
				'datamatrix'      => 'Data Matrix',
				'pdf417'          => 'PDF417',
				'aztec'           => 'Aztec Code',
				'telepen'         => 'Telepen',
				'kix'             => 'KIX (Klant index)',
				'rm4scc'          => 'RM4SCC (Royal Mail)',
				'onecode'         => 'USPS Intelligent Mail',
				'gs1-128'         => 'GS1-128',
				'isbn'            => 'ISBN',
				'ismn'            => 'ISMN',
				'issn'            => 'ISSN',
				'postnet'         => 'Postnet',
				'planet'          => 'Planet',
				'channelcode'     => 'Channel Code',
				'code11'          => 'Code 11',
				'code2of5'        => 'Code 2 of 5',
				'coop2of5'        => 'COOP 2 of 5',
				'matrix2of5'      => 'Matrix 2 of 5',
				'industrial2of5'  => 'Industrial 2 of 5',
			);
		}

		/**
		 * Generiert die API URL basierend auf Parametern.
		 */
		public static function get_api_url( $content, $type = 'auto', $args = array() ) {
			$base_url = 'https://barcodeapi.org/api';
			$type     = sanitize_text_field( $type );
			$content  = rawurlencode( $content ); // rawurlencode (%20 statt +) ist sicherer für Pfad-Segmente

			// API Key hinzufügen, falls vorhanden
			$api_key = get_option( 'wp_barcode_api_key' );
			if ( ! empty( $api_key ) ) {
				$args['token'] = $api_key;
			}

			// Farben bereinigen (Hash entfernen für API)
			if ( ! empty( $args['fg'] ) ) {
				$args['fg'] = str_replace( '#', '', $args['fg'] );
			}
			if ( ! empty( $args['bg'] ) ) {
				$args['bg'] = str_replace( '#', '', $args['bg'] );
			}

			// Query Parameter bauen
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

			$image_url = self::get_api_url( $atts['content'], $atts['type'], $args );

			return sprintf( '<img src="%s" alt="Barcode %s" class="wp-barcode-api-img" />', esc_url( $image_url ), esc_attr( $atts['content'] ) );
		}

		/**
		 * Rendert die Admin-Seite.
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
				<h1>WP Barcode API Einstellungen</h1>
				
				<?php if ( $message ) : ?>
					<div class="updated notice is-dismissible"><p><?php echo $message; ?></p></div>
				<?php endif; ?>

				<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-bottom: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
					<h2>API Nutzung & Rate Limits</h2>
					<p>Die API limitiert Anfragen basierend auf der IP-Adresse. Da dieses Plugin sowohl client-seitige (Browser) als auch server-seitige (WordPress) Anfragen nutzt, werden hier beide Limits getrennt angezeigt.</p>
					
					<div style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
						<!-- Server Side -->
						<div style="flex: 1; min-width: 250px; padding: 15px; background: #f0f0f1; border: 1px solid #c3c4c7;">
							<h3 style="margin-top: 0;">Server-seitig (WordPress)</h3>
							<p class="description">Genutzt für: Statische Generierung, Caching.</p>
							<?php if ( $server_data ) : ?>
								<ul style="margin-bottom: 0;">
									<li><strong>IP:</strong> <?php echo esc_html( $server_data['caller'] ); ?></li>
									<li><strong>Verbraucht:</strong> <?php echo esc_html( $server_data['tokenSpend'] ); ?> / <?php echo esc_html( $server_data['tokenLimit'] ); ?></li>
									<li><strong>Status:</strong> <?php echo $server_data['enforce'] ? '<span style="color:#d63638">Gesperrt</span>' : '<span style="color:#00a32a">Aktiv</span>'; ?></li>
								</ul>
							<?php else : ?>
								<p>Status konnte nicht abgerufen werden.</p>
							<?php endif; ?>
						</div>

						<!-- Client Side -->
						<div style="flex: 1; min-width: 250px; padding: 15px; background: #f0f0f1; border: 1px solid #c3c4c7;">
							<h3 style="margin-top: 0;">Client-seitig (Ihr Browser)</h3>
							<p class="description">Genutzt für: Shortcodes, Elementor Widget.</p>
							<div id="bc-client-status">Lade Daten...</div>
						</div>
					</div>

					<p style="margin-top: 15px;">
						<a href="https://barcodeapi.org/session.html" target="_blank" class="button button-secondary">Details auf barcodeapi.org ansehen</a>
						<a href="https://barcodeapi.org/#pricing" target="_blank" class="button button-primary">API Key kaufen</a>
					</p>

					<script>
					(function() {
						var apiKey = '<?php echo esc_js( $api_key ); ?>';
						var url = 'https://barcodeapi.org/limiter/';
						if ( apiKey ) {
							url += '?token=' + encodeURIComponent(apiKey);
						}

						fetch(url)
							.then(function(response) { return response.json(); })
							.then(function(data) {
								var html = '<ul style="margin-bottom: 0;">';
								html += '<li><strong>IP:</strong> ' + (data.caller || 'Unbekannt') + '</li>';
								html += '<li><strong>Verbraucht:</strong> ' + (data.tokenSpend || 0) + ' / ' + (data.tokenLimit || 0) + '</li>';
								html += '<li><strong>Status:</strong> ' + (data.enforce ? '<span style="color:#d63638">Gesperrt</span>' : '<span style="color:#00a32a">Aktiv</span>') + '</li>';
								html += '</ul>';
								document.getElementById('bc-client-status').innerHTML = html;
							})
							.catch(function(err) {
								document.getElementById('bc-client-status').innerText = 'Fehler: ' + err.message;
							});
					})();
					</script>
				</div>

				<form method="post" action="options.php">
					<?php
					settings_fields( 'wp_barcode_api_options' );
					do_settings_sections( 'wp_barcode_api_options' );
					?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">API Key (Optional)</th>
							<td>
								<input type="text" name="wp_barcode_api_key" value="<?php echo esc_attr( get_option( 'wp_barcode_api_key' ) ); ?>" class="regular-text" />
								<p class="description">Hinterlegen Sie hier Ihren Key, um höhere Limits zu nutzen.</p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>

				<hr>

				<h2>Statischen Barcode generieren</h2>
				<p>Nutzen Sie dieses Formular, um einen Barcode einmalig zu generieren und dauerhaft in Ihrer Mediathek zu speichern.</p>
				
				<form method="post" action="">
					<?php wp_nonce_field( 'wp_barcode_generate_static' ); ?>
					<table class="form-table">
						<tr>
							<th>Inhalt</th>
							<td><input type="text" name="bc_content" required class="regular-text" placeholder="z.B. 12345678 oder https://..."></td>
						</tr>
						<tr>
							<th>Typ</th>
							<td>
								<select name="bc_type">
									<?php foreach ( self::get_supported_types() as $value => $label ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Optionen</th>
							<td>
								<label>Breite: <input type="number" name="bc_width" placeholder="z.B. 200" class="small-text"></label>
								<label>Höhe: <input type="number" name="bc_height" placeholder="z.B. 80" class="small-text"></label>
							</td>
						</tr>
						<tr>
							<th>Farben (Hex)</th>
							<td>
								<label>Vordergrund: <input type="text" name="bc_fg" placeholder="000000" class="small-text"></label>
								<label>Hintergrund: <input type="text" name="bc_bg" placeholder="ffffff" class="small-text"></label>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" name="generate_static_barcode" id="submit-gen" class="button button-secondary" value="In Mediathek speichern">
					</p>
				</form>

				<hr>

				<h2>Shortcode Anleitung</h2>
				<p>Verwenden Sie den Shortcode an beliebiger Stelle:</p>
				<code>[barcode content="IhrText" type="qrcode" width="150"]</code>
				<p>Verfügbare Attribute: <code>content</code>, <code>type</code>, <code>width</code>, <code>height</code>, <code>fg</code> (Farbe), <code>bg</code> (Hintergrund), <code>show_text</code>.</p>
			</div>
			<?php
		}

		/**
		 * Logik zum Speichern des Bildes in der Mediathek.
		 */
		private function handle_static_generation() {
			if ( ! current_user_can( 'upload_files' ) ) {
				return 'Keine Berechtigung zum Hochladen von Dateien.';
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

			$url = self::get_api_url( $content, $type, $args );

			// Bild herunterladen
			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return 'Fehler beim Abrufen des Barcodes von der API.';
			}

			$image_data = wp_remote_retrieve_body( $response );
			$filename   = 'barcode-' . sanitize_title( $content ) . '.png';
			$upload     = wp_upload_bits( $filename, null, $image_data );

			if ( ! empty( $upload['error'] ) ) {
				return 'Fehler beim Speichern: ' . $upload['error'];
			}

			// In Mediathek einfügen
			$file_path = $upload['file'];
			$file_type = wp_check_filetype( $filename, null );
			
			$attachment = array(
				'post_mime_type' => $file_type['type'],
				'post_title'     => 'Barcode: ' . $content,
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $file_path );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return 'Barcode erfolgreich gespeichert! <a href="' . esc_url( get_edit_post_link( $attach_id ) ) . '" target="_blank">Bild ansehen</a>';
		}

		/**
		 * Initialisiert den Update-Checker.
		 */
		private function init_updater() {
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php' ) ) {
				require 'plugin-update-checker/plugin-update-checker.php';
				$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
					'https://github.com/hellodosi/WP-BarcodeAPI-Plugin/',
					__FILE__,
					'wp-barcode-api'
				);
				
				// Authentifizierung für privates Repo
				$myUpdateChecker->setAuthentication('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');
			}
		}
	}

	// Plugin starten
	WP_Barcode_API::get_instance();
}
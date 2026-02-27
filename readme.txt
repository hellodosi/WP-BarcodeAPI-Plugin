=== WP Barcode API ===
Tags: barcode, qrcode, ean, elementor, api, generator
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ein leistungsstarkes Plugin zur Generierung von Barcodes und QR-Codes über Shortcodes oder Elementor unter Verwendung der BarcodeAPI.org Schnittstelle.

== Description ==

WP Barcode API ermöglicht die einfache Einbindung von Barcodes in Ihre WordPress-Website. Das Plugin nutzt die API von [barcodeapi.org](https://barcodeapi.org), um eine Vielzahl von Barcode-Formaten dynamisch zu generieren.

Die Generierung erfolgt standardmäßig client-seitig (via URL), was Ihren Server entlastet. Für statische Anwendungsfälle bietet das Plugin zudem einen Generator im Backend, der Barcodes als Bilddatei direkt in Ihre Mediathek speichert.

**Hauptfunktionen:**

*   **Shortcode Support:** Fügen Sie Barcodes einfach mit `[barcode]` in Beiträge oder Seiten ein.
*   **Elementor Widget:** Ein natives Widget für Elementor mit Styling-Optionen (Breite, Höhe, Rotation) und Unterstützung für dynamische Tags (z.B. Post-ID oder Custom Fields als Barcode-Inhalt).
*   **Statische Generierung:** Erstellen Sie Barcodes manuell im Admin-Bereich und speichern Sie diese dauerhaft in der WordPress Mediathek.
*   **API Key Support:** Hinterlegen Sie optional einen API Key, um höhere Rate-Limits zu nutzen.
*   **Unterstützte Formate:** QR Code, EAN-13, EAN-8, Code 128, Code 39, UPC, ITF, MSI, Pharmacode.

== Installation ==

1. Laden Sie den Ordner `wp-barcode-api` in das Verzeichnis `/wp-content/plugins/` hoch.
2. Aktivieren Sie das Plugin über das Menü 'Plugins' in WordPress.
3. (Optional) Gehen Sie zu *Einstellungen > Barcode API* und hinterlegen Sie Ihren API Key, falls vorhanden.

== Frequently Asked Questions ==

= Benötige ich einen API Key? =

Für geringes Aufkommen ist die API von barcodeapi.org oft kostenlos nutzbar (beachten Sie die aktuellen Rate Limits auf deren Website). Für professionellen Einsatz oder hohen Traffic empfehlen wir den Kauf eines Keys.

= Welche Daten werden übertragen? =

Das Plugin generiert Bild-URLs, die auf `barcodeapi.org` zeigen. Die IP-Adresse des Besuchers und der Inhalt des Barcodes werden an die API übermittelt, um das Bild zu generieren.

= Wie nutze ich den Shortcode? =

Verwenden Sie den Shortcode wie folgt:
`[barcode content="123456" type="ean13" width="200" height="100" show_text="true"]`

Verfügbare Parameter:
*   `content`: Der Inhalt des Barcodes (Text oder URL).
*   `type`: Der Typ (z.B. `qrcode`, `ean13`, `code128`, `auto`).
*   `width`: Breite in Pixeln.
*   `height`: Höhe in Pixeln.
*   `show_text`: `true` oder `false` (zeigt den Text unter dem Barcode an).
*   `rotation`: `N` (Normal), `R` (Rechts), `L` (Links), `I` (Invertiert).

== Screenshots ==

1. Die Einstellungsseite mit dem Generator für statische Barcodes.
2. Das Elementor Widget in Aktion.

== Changelog ==

= 1.0.0 =
*   Initialer Release.
*   Hinzugefügt: Shortcode `[barcode]`.
*   Hinzugefügt: Elementor Widget.
*   Hinzugefügt: Admin-Seite mit API Key Einstellung und Generator.
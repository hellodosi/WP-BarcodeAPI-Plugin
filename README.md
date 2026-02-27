# WP Barcode API

Ein leistungsstarkes WordPress-Plugin zur Generierung von Barcodes und QR-Codes über Shortcodes oder Elementor unter Verwendung der [BarcodeAPI.org](https://barcodeapi.org) Schnittstelle.

## Funktionen

*   **Shortcode Support:** Einfaches Einfügen von Barcodes in Beiträge oder Seiten mit `[barcode]`.
*   **Elementor Widget:** Natives Widget mit umfangreichen Styling-Optionen und Unterstützung für dynamische Tags.
*   **Statische Generierung:** Erstellen Sie Barcodes manuell im Admin-Bereich und speichern Sie diese dauerhaft in der WordPress Mediathek.
*   **API Key Support:** Hinterlegen Sie optional einen API Key, um höhere Rate-Limits zu nutzen.
*   **Client-Side Rendering:** Standardmäßig werden Barcodes direkt im Browser des Besuchers generiert, um Ihren Server zu entlasten.
*   **Status-Check:** Überprüfen Sie Ihre API-Limits (Server & Client) direkt im Backend.

## Unterstützte Formate

Das Plugin unterstützt alle gängigen Formate der API, darunter:
*   QR Code
*   EAN-13, EAN-8
*   Code 128, Code 39, Code 93
*   UPC-A, UPC-E
*   ITF, MSI, Pharmacode
*   Data Matrix, PDF417, Aztec
*   und viele mehr.

## Installation

1.  Laden Sie das Plugin-Verzeichnis in den Ordner `/wp-content/plugins/` hoch oder installieren Sie das ZIP-Archiv über das WordPress-Backend.
2.  Aktivieren Sie das Plugin über das Menü 'Plugins' in WordPress.
3.  (Optional) Gehen Sie zu *Einstellungen > Barcode API* und hinterlegen Sie Ihren API Key.

## Nutzung

### Shortcode

Verwenden Sie den Shortcode an beliebiger Stelle in Ihren Inhalten:

```shortcode
[barcode content="123456" type="ean13" width="200" height="100" show_text="true"]
```

**Attribute:**
*   `content`: Der Inhalt (Text oder URL).
*   `type`: Der Barcode-Typ (z.B. `qrcode`, `ean13`, `auto`).
*   `width`, `height`: Dimensionen in Pixeln.
*   `fg`, `bg`: Vorder- und Hintergrundfarbe (Hex, z.B. `ff0000`).
*   `show_text`: `true` oder `false`.

### Elementor

Suchen Sie im Elementor-Editor nach dem Widget **Barcode API**.
*   **Inhalt:** Wählen Sie den Typ und geben Sie den Inhalt ein (unterstützt dynamische Tags).
*   **API Einstellungen:** Konfigurieren Sie API-spezifische Parameter wie Farben und Rotation.
*   **Stil:** Nutzen Sie die Standard-Bild-Optionen von Elementor für Rahmen, Schatten, Größe und Ausrichtung.

### Statischer Generator

Unter *Einstellungen > Barcode API* finden Sie einen Generator, mit dem Sie Barcodes einmalig erstellen und als Bilddatei in Ihre Mediathek importieren können. Dies ist ideal für statische Inhalte, die nicht bei jedem Seitenaufruf neu generiert werden müssen.

## API Limits

Das Plugin nutzt die API von barcodeapi.org. Bitte beachten Sie die Rate Limits der API. Im Admin-Bereich des Plugins können Sie Ihren aktuellen Verbrauchsstatus (sowohl für den Server als auch für Ihren Client) einsehen.

## Lizenz

GPL v2 or later
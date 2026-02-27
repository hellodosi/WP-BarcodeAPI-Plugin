<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Barcode_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wp_barcode_api_widget';
	}

	public function get_title() {
		return esc_html__( 'Barcode API', 'wp-barcode-api' );
	}

	public function get_icon() {
		return 'eicon-barcode';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Inhalt', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'barcode_content',
			[
				'label' => esc_html__( 'Barcode Inhalt', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '12345678',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Text oder URL eingeben', 'wp-barcode-api' ),
			]
		);

		$options = class_exists( 'WP_Barcode_API' ) ? WP_Barcode_API::get_supported_types() : [ 'auto' => 'Auto' ];

		$this->add_control(
			'barcode_type',
			[
				'label' => esc_html__( 'Barcode Typ', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => $options,
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Einstellungen', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'barcode_width',
			[
				'label' => esc_html__( 'Breite (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'barcode_height',
			[
				'label' => esc_html__( 'Höhe (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => esc_html__( 'Text anzeigen', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Ja',
				'label_off' => 'Nein',
				'return_value' => 'true',
				'default' => '',
			]
		);

		$this->add_control(
			'rotation',
			[
				'label' => esc_html__( 'Rotation', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'N',
				'options' => [
					'N' => 'Normal',
					'R' => '90° Rechts',
					'L' => '90° Links',
					'I' => '180° Invertiert',
				],
			]
		);

		$this->add_control(
			'barcode_fg',
			[
				'label' => esc_html__( 'Vordergrundfarbe', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
			]
		);

		$this->add_control(
			'barcode_bg',
			[
				'label' => esc_html__( 'Hintergrundfarbe', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		if ( empty( $settings['barcode_content'] ) ) {
			return;
		}

		// Zugriff auf die Helper-Methode der Hauptklasse
		if ( class_exists( 'WP_Barcode_API' ) ) {
			$args = [
				'width'    => $settings['barcode_width'],
				'height'   => $settings['barcode_height'],
				'text'     => $settings['show_text'],
				'rotation' => $settings['rotation'],
				'fg'       => $settings['barcode_fg'],
				'bg'       => $settings['barcode_bg'],
			];

			$url = WP_Barcode_API::get_api_url( $settings['barcode_content'], $settings['barcode_type'], $args );

			echo sprintf( '<img src="%s" alt="Barcode" style="max-width:100%%; height:auto;">', esc_url( $url ) );
		}
	}
}
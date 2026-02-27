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

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Ausrichtung', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Links', 'wp-barcode-api' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Zentriert', 'wp-barcode-api' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Rechts', 'wp-barcode-api' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// API Settings Section (Moved from Style)
		$this->start_controls_section(
			'api_settings_section',
			[
				'label' => esc_html__( 'API Einstellungen', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style_limitations_notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<div style="font-size: 11px; line-height: 1.4; color: #777; margin-bottom: 15px;"><i>Hinweis: Manche Einstellungen (z.B. Farben, Größe, Text) werden nicht von allen Barcode-Typen unterstützt.</i></div>',
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'barcode_width',
			[
				'label' => esc_html__( 'API Breite (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'barcode_height',
			[
				'label' => esc_html__( 'API Höhe (px)', 'wp-barcode-api' ),
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

		// Style Section (Standard Image Styling)
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Bild', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Breite', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label' => esc_html__( 'Max. Breite', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => esc_html__( 'Deckkraft', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Eckenradius', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'selector' => '{{WRAPPER}} img',
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

			echo sprintf( '<img src="%s" alt="Barcode">', esc_url( $url ) );
		}
	}
}
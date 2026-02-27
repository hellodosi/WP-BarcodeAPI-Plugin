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
				'label' => esc_html__( 'Content', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'barcode_content',
			[
				'label' => esc_html__( 'Barcode Content', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '12345678',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter text or URL', 'wp-barcode-api' ),
			]
		);

		$options = class_exists( 'WP_Barcode_API' ) ? WP_Barcode_API::get_supported_types() : [ 'auto' => 'Auto' ];

		$this->add_control(
			'barcode_type',
			[
				'label' => esc_html__( 'Barcode Type', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => $options,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wp-barcode-api' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wp-barcode-api' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wp-barcode-api' ),
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
				'label' => esc_html__( 'API Settings', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style_limitations_notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					'<div style="font-size: 11px; line-height: 1.4; color: #777; margin-bottom: 15px;"><i>%s</i></div>',
					esc_html__( 'Note: Some settings (e.g., colors, size, text) are not supported by all barcode types.', 'wp-barcode-api' )
				),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'barcode_width',
			[
				'label' => esc_html__( 'API Width (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'barcode_height',
			[
				'label' => esc_html__( 'API Height (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => esc_html__( 'Show Text', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wp-barcode-api' ),
				'label_off' => esc_html__( 'No', 'wp-barcode-api' ),
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
					'N' => esc_html__( 'Normal', 'wp-barcode-api' ),
					'R' => esc_html__( '90° Right', 'wp-barcode-api' ),
					'L' => esc_html__( '90° Left', 'wp-barcode-api' ),
					'I' => esc_html__( '180° Inverted', 'wp-barcode-api' ),
				],
			]
		);

		$this->add_control(
			'text_size',
			[
				'label' => esc_html__( 'Text Size (pt)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 72,
				'step' => 1,
				'condition' => [ 'show_text' => 'true' ],
			]
		);

		$this->add_control(
			'text_margin',
			[
				'label' => esc_html__( 'Text Margin (px)', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'condition' => [ 'show_text' => 'true' ],
			]
		);

		$this->add_control(
			'barcode_fg',
			[
				'label' => esc_html__( 'Foreground Color', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
			]
		);

		$this->add_control(
			'barcode_bg',
			[
				'label' => esc_html__( 'Background Color', 'wp-barcode-api' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
			]
		);

		$this->end_controls_section();

		// Style Section (Standard Image Styling)
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'wp-barcode-api' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'wp-barcode-api' ),
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
				'label' => esc_html__( 'Max Width', 'wp-barcode-api' ),
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
				'label' => esc_html__( 'Opacity', 'wp-barcode-api' ),
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
				'label' => esc_html__( 'Border Radius', 'wp-barcode-api' ),
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
				'textsize'   => $settings['text_size'],
				'textmargin' => $settings['text_margin'],
				'rotation' => $settings['rotation'],
				'fg'       => $settings['barcode_fg'],
				'bg'       => $settings['barcode_bg'],
			];

			$url = WP_Barcode_API::get_api_url( $settings['barcode_content'], $settings['barcode_type'], $args, false ); // false = Key nicht für Client-Anfragen nutzen

			echo sprintf( '<img src="%s" alt="%s">', esc_url( $url ), esc_attr__( 'Barcode', 'wp-barcode-api' ) );
		}
	}
}
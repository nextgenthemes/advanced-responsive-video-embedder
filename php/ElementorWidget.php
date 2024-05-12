<?php
namespace Nextgenthemes\ARVE;

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 10.0.0
 * @codeCoverageIgnore
 */
class ElementorWidget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 10.0.0
	 * @return string Widget name.
	 */
	public function get_name(): string {
		return 'arve';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 10.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title(): string {
		return esc_html__( 'ARVE Video', 'advanced-responsive-video-embedder' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 10.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon(): string {
		return 'eicon-youtube';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 10.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories(): array {
		return [ 'basic', 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 10.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return [ 'oembed', 'url', 'link', 'video' ];
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 10.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url(): string {
		return 'https://nextgenthemes.com/plugins/arve/documentation/';
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 10.0.0
	 * @access protected
	 */
	protected function register_controls(): void {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-oembed-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		foreach ( settings( 'gutenberg_block' ) as $key => $s ) {

			$control_args = array(
				'label' => $s['label'],
			);

			if ( ! empty( $s['placeholder'] ) ) {
				$control_args['placeholder'] = $s['placeholder'];
			}

			$control_args['placeholder'] = $s['placeholder'] ?? null;

			switch ( $s['type'] ) {
				case 'string':
					$control_args['type']       = \Elementor\Controls_Manager::TEXT;
					$control_args['input_type'] = 'text';
					break;
				case 'boolean':
					$control_args['type'] = \Elementor\Controls_Manager::SWITCHER;
					break;
				case 'select':
					$control_args['type']    = \Elementor\Controls_Manager::SELECT;
					$control_args['options'] = $s['options'];
					break;
				case 'integer':
					$control_args['type'] = \Elementor\Controls_Manager::NUMBER;
					break;
			}

			switch ( $key ) {
				case 'url':
					$control_args['label_block'] = true;
					$control_args['default']     = 'https://www.youtube.com/watch?v=XHOmBV4js_E';
					$control_args['input_type']  = 'url';
					break;

				case 'thumbnail':
					$control_args['type'] = \Elementor\Controls_Manager::MEDIA;
					break;

				case 'random_video_url':
				case 'random_video_urls':
				case 'description':
				case 'title':
				case 'parameters':
					$control_args['label_block'] = true;
					break;
			}

			switch ( $key ) {
				case 'url':
				case 'title':
				case 'description':
				case 'thumbnail':
					$control_args['dynamic']['active'] = true;
					break;
			}

			$this->add_control( $key, $control_args );
		}

		$this->end_controls_section();
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 10.0.0
	 * @access protected
	 */
	protected function render(): void {

		$settings = $this->get_settings_for_display();

		foreach ( $settings as $key => $value ) {

			if ( ! array_key_exists( $key, settings( 'gutenberg_block' ) ) ) {
				unset( $settings[ $key ] );
			}
		}

		$settings['thumbnail'] = $settings['thumbnail']['id'];

		echo '<div class="arve-elementor-widget">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo shortcode( $settings );
		echo '</div>';
	}
}

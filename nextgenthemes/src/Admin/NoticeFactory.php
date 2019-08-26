<?php
namespace Nextgenthemes\Admin;

class NoticeFactory {

	public $slug;
	public $notice;
	public $dismiss_forever;

	public function __construct( $slug, $notice, $dismiss_forever = true, $capabilities = 'activate_plugins' ) {

		if ( ! current_user_can( $capabilities ) ) {
			return;
		}

		$this->notice_id       = "admin-notice-factory-$slug";
		$this->notice          = (string) $notice;
		$this->dismiss_forever = (bool) $dismiss_forever;

		if ( 'admin-notice-factory-arve_dismiss_pro_notice' === $this->notice_id ) {
			$this->notice_id = 'arve_dismiss_pro_notice';
		}

		add_action( 'admin_notices', [ $this, 'action_admin_notices' ] );
		add_action( "wp_ajax_{$this->notice_id}", [ $this, 'ajax_call' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
	}

	public function action_admin_notices() {

		if ( apply_filters( 'nj_debug_admin_message', false ) ) {
			delete_user_meta( get_current_user_id(), $this->notice_id );
			delete_transient( $this->notice_id );
		}

		$user_id   = get_current_user_id();
		$user_meta = get_user_meta( $user_id, $this->notice_id );

		if ( $this->dismiss_forever && ! empty( $user_meta ) ) {
			return;
		} elseif ( get_transient( $this->notice_id ) ) {
			return;
		}

		printf(
			'<div class="notice is-dismissible updated" data-nextgenthemes-notice-id="%s">%s</div>',
			esc_attr( $this->notice_id ),
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			$this->notice
			// phpcs:enable
		);
	}

	public function assets() {

		\Nextgenthemes\Asset\enqueue(
			[
				'handle' => 'nextgenthemes-notice-ajax',
				'deps'   => [ 'jquery' ],
				'src'    => \Nextgenthemes\Asset\plugin_or_theme_uri( 'nextgenthemes/dist/js/notice-ajax.js' ),
			]
		);
	}

	public function ajax_call() {

		$user_id = get_current_user_id();

		if ( $this->dismiss_forever ) {
			add_user_meta( $user_id, $this->notice_id, true );
		} else {
			set_transient( $this->notice_id, true, HOUR_IN_SECONDS );
		}

		wp_die();
	}
}

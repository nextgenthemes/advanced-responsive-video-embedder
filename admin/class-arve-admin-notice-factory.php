<?php

class ARVE_Admin_Notice_Factory {

	private $notice_id;
	private $notice;
	private $dismiss_forever;

	public function __construct( $notice_id, $notice, $dismiss_forever = true, $capabilities = 'activate_plugins' ) {

		if ( ! current_user_can( $capabilities ) ) {
			return;
		}

		$this->notice_id       = "admin-notice-factory-$notice_id";
		$this->notice          = $notice;
		$this->dismiss_forever = $dismiss_forever;

		if ( 'admin-notice-factory-arve_dismiss_pro_notice' === $this->notice_id ) {
			$this->notice_id = 'arve_dismiss_pro_notice';
		}

		add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
		add_action( 'wp_ajax_' . $this->notice_id, array( $this, 'ajax_call' ) );
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
			'<div class="notice is-dismissible updated" data-nj-notice-id="%s">%s</div>',
			esc_attr( $this->notice_id ),
			$this->notice // phpcs:ignore
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

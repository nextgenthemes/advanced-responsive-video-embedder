<?php
if ( ! class_exists( 'Nextgenthemes_Admin_Notice_Factory' ) ) {

	final class Nextgenthemes_Admin_Notice_Factory {

		private $user_id;
		private $notice_id;
		private $notice_html;
		private $dismiss_forever;
		private $dismiss_time;
		private $capabilities;

		function __construct( $notice_id, $notice_html, $dismiss_time = false, $capabilities = 'activate_plugins' ) {

			if ( ! current_user_can( $capabilities ) ) {
				return;
			}

			$this->user_id         = get_current_user_id();
			$this->notice_id       = "admin-notice-factory-$notice_id";
			$this->transient_id    = "{$this->notice_id}-{$this->user_id}";
			$this->notice_html     = (string) $notice_html;
			$this->dismiss_forever = ( false === $dismiss_time ) ? true : false;
			$this->dismiss_time    = (int) $dismiss_time;

			if ( 'admin-notice-factory-arve_dismiss_pro_notice' == $this->notice_id ) {
				$this->notice_id = 'arve_dismiss_pro_notice';
			}

			add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
			add_action( 'wp_ajax_' . $this->notice_id, array( $this, 'ajax_call' ) );
		}

		function action_admin_notices() {

			$user_meta = get_user_meta( $this->user_id, $this->notice_id );

			if( $this->dismiss_forever && ! empty( $user_meta ) ) {
				return;
			} elseif( get_transient( $this->notice_id ) ) {
				return;
			} elseif( get_transient( $this->transient_id ) ) {
				return;
			}

			printf(
				'<div class="notice is-dismissible updated" data-nj-notice-id="%s">%s</div>',
				esc_attr( $this->notice_id ),
				$this->notice_html
			);

			wp_enqueue_script(
				'nextgenthemes-admin-notice-factory',
				NEXTGENTHEMES_ADMIN_URL . 'admin-notice-factory.js',
				array( 'jquery' ),
				filemtime( __DIR__ . '/admin-notice-factory.js' )
			);
		}

		function ajax_call() {

			if( $this->dismiss_forever ) {
				add_user_meta( $this->user_id, $this->notice_id, true );
			} else {
				set_transient( $this->transient_id, true, $this->dismiss_time );
			}
			wp_die();
		}
	}
} // if class exists

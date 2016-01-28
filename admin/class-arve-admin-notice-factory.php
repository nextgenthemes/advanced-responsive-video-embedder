<?php

class Advanced_Responsive_Video_Embedder_Admin_Notice_Factory {

  private $notice_id;
  private $notice;

  function __construct( $notice_id, $notice, $dismissable = true ) {

    $this->notice_id = $notice_id;
    $this->notice = $notice;
    $this->dismissable = $dismissable;

		add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );

    if( $dismissable ) {
      add_action( 'admin_head',            array( $this, 'action_admin_head' ) );
      add_action( 'wp_ajax_' . $notice_id, array( $this, 'ajax_call' ) );
    }
  }

  function action_admin_notices() {
		#delete_user_meta( get_current_user_id(), $this->notice_id );

		if( ! empty( get_user_meta( get_current_user_id(), $this->notice_id ) ) ) {
			return;
		}

    printf(
      '<div class="notice updated%s %s"><p style="font-size: 1.15em;">%s</p></div>',
      $this->dismissable ? ' is-dismissible' : '',
      esc_attr( $this->notice_id ),
      $this->notice
    );
	}

	function ajax_call() {

		add_user_meta( get_current_user_id(), $this->notice_id, true );
		wp_die();
	}

  function action_admin_head() {
    ?>
<script>
(function ( $ ) {
	'use strict';

	$(document).on( 'click', '.<?php echo $this->notice_id; ?> .notice-dismiss', function() {
		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: '<?php echo $this->notice_id; ?>'
			}
		});
	});
}(jQuery));
</script>
    <?php
  }
}

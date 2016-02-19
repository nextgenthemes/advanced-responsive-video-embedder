<?php

class Advanced_Responsive_Video_Embedder_Admin_Notice_Factory {

  private $notice_id;
  private $notice;
  private $dismissible;

  function __construct( $notice_id, $notice, $dismissible = true ) {

    $this->notice_id   = "admin-notice-factory-$notice_id";
    $this->notice      = $notice;
    $this->dismissible = $dismissible;

		add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
    add_action( 'admin_head',                  array( $this, 'action_admin_head' ) );
    add_action( 'wp_ajax_' . $this->notice_id, array( $this, 'ajax_call' ) );
  }

  function action_admin_notices() {
		#delete_user_meta( get_current_user_id(), $this->notice_id );

		if( $this->dismissible && ! empty( get_user_meta( get_current_user_id(), $this->notice_id ) ) ) {
			return;
		} elseif( get_transient( $this->notice_id ) ) {
      return;
    }

    printf(
      '<div class="%s"><p style="font-size: 1.15em;">%s</p></div>',
      esc_attr( "notice updated is-dismissible $this->notice_id" ),
      $this->notice
    );
	}

	function ajax_call() {

    if( $this->dismissible ) {
      add_user_meta( get_current_user_id(), $this->notice_id, true );
    } else {
      set_transient( $this->notice_id, true, HOUR_IN_SECONDS );
    }
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

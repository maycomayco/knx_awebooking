<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying notice after check available single room
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 * @deprecated 2.0
 */

?>

<div class="apb-check-notice-js panel panel-warning" style="display:none">
    <div class="panel-heading">
        <h3 class="panel-title" id="panel-title"><?php esc_html_e( 'Check Availability Warning!', 'awebooking' ); ?>
          <a href="#panel-title" class="anchorjs-link"><span class="anchorjs-icon"></span></a>
      </h3>
    </div>
    <?php fill_content_message_single();  ?>
</div>

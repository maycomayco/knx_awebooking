<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying loop list room after check available.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */
?>
<div class="apb-content">
	<div class="apb-room">
		<?php
		/**
		 * @hooked loop_content_check_available
		 */
		do_action( 'apb_loop_content_check_available' );
		?>
	</div>
</div>

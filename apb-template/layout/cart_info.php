<?php
/**
 * The template for displaying layout information cart.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="apb-room-selected">
    <!-- HEADING -->
    <h2 class="apb-heading"><?php esc_html_e( 'All Rooms Select', 'awebooking' ) ?></h2>
    <!-- END / HEADING -->

    <div class="apb-room-selected_content">
        <?php do_action( 'apb_loop_item_cart_info' ); ?>
    </div>
</div>

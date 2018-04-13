<?php
/**
 * The template for displaying layout nav step
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
<div class="apb-notice-js"></div>
<div class="apb-step">
    <ul>
        <li class="apb-step-one <?php echo empty( $active ) ? 'active' : ''; ?>"><span>1</span> <?php esc_html_e( 'Make a reservation', 'awebooking' ); ?></li>
        <li class="apb-step-two"><span>2</span> <?php esc_html_e( 'Review', 'awebooking' ) ?></li>
        <li class="apb-step-three <?php echo ( 3 == $active ) ? 'active' : ''; ?>"><span>3</span> <?php esc_html_e( 'Place booking', 'awebooking' ); ?></li>
    </ul>
</div>

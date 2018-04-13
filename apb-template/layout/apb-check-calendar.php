<?php
/**
 * The template for displaying check calendar.
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

<h2 class="apb-heading"><?php esc_html_e( 'Availability', 'awebooking' ); ?></h2>
<div class="apb-calendar_wrap">
    <div id="apb_calendar" class="apb-calendar"></div>
</div>

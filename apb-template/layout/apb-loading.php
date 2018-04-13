<?php
/**
 * The template for displaying layout loading
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
<div id="preloader" class="apb-loading " style="display: none;">
    <span><?php esc_html_e( 'Loading', 'awebooking' ); ?></span>
</div>

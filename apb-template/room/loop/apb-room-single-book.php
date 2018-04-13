<?php
/**
 * The template for displaying loop button book of single room.
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

<input type="hidden" name="room_id" value="<?php the_ID(); ?>">
<button class="apb-btn apb-single-checkavb-js"><?php esc_html_e( 'Book', 'awebooking' ); ?></button>

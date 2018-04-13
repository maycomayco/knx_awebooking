<?php
/**
 * The template for displaying layout complated select room
 *
 * Override this template by copying it to your theme.
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2 class="apb-heading"><?php esc_html_e( 'All rooms are selected', 'awebooking' ); ?></h2>

<?php
/**
 * @hooked layout_list_review_book() - 10
 */
do_action( 'apb_layout_list_review_book' );
?>
<div class="apb-room-select-footer">
	<a href="#" class="link-other-room change-all-room-btn"><?php esc_html_e( 'Change all rooms', 'awebooking' ); ?></a>
	<a href="<?php echo esc_url( AWE_function::get_checkout_page_url() ); ?>" class="apb-btn"><?php esc_html_e( 'Go to checkout', 'awebooking' ); ?></a>
</div>

<?php
/**
 * The template for displaying cart total
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
<!-- ITEM -->
<div class="apb-room-selected_item">
	<?php if ( get_option( 'apb_tax_amount' ) ) : ?>
		<div class="apb-room-seleted_total-room">
			<?php esc_html_e( 'Tax', 'awebooking' ); ?>
			<span class="apb-amount"><?php echo wp_kses_post( AWE_function::get_displayed_tax( $apb_cart['total'] ) ); ?></span>
		</div>
	<?php endif; ?>

	<div class="apb-room-seleted_total-room">
		<?php esc_html_e( 'Total', 'awebooking' ); ?>
		<span class="apb-amount"><?php echo wp_kses_post( AWE_function::apb_price( AWE_function::calculate_tax( $apb_cart['total'] ) ) ); ?></span>
	</div>
</div>
<!-- END / ITEM -->

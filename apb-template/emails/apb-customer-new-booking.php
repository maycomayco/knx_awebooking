<?php
/**
 * Tempalte mail for new order. send to staff.
 *
 * @author  aweteam
 * @package awebooking/apb-template/Emails
 * @version 1.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$config_email = AWE_function::get_option( 'apb_mail_new_booking' );
if ( ! empty( $config_email['text'] ) ) {
	echo '<p>' . esc_html( $config_email['text'] ) . '</p>';
}

$total_ex_tax = get_post_meta( $order_id, 'total_ex_tax', true );
$order_total_price = get_post_meta( $order_id, '_order_total', true );
$total_ex_tax_wc = 0;

foreach ( $order_data as $item ) {
	$total_ex_tax_wc += $item['total_price'];
	$room_num = $i++;
	$room_id = absint( $item['order_room_id'] );
	$room_type_id = wp_get_post_parent_id( $room_id );
	$room_type    = get_post( $room_type_id );

	$item['package_data'] = maybe_unserialize( $item['package_data'] );
	?>
	<!-- ITEM -->
	<div style="border-bottom: 1px solid #e4e4e4; padding: 20px 20px 15px;" class="apb-room-selected_item">
		<?php APB_Email::email_guest_info( $item, $order_id ); ?>
		
		<?php APB_Email::email_check_in( $item ); ?>

		<?php APB_Email::email_check_out( $item ); ?>

		<?php APB_Email::email_room_name( $item, $room_num, $room_type_id ); ?>

		<?php APB_Email::email_room_type_name( $room_type_id ); ?>

		<div style="border-top: 1px solid #e4e4e4; margin-top: 15px; padding-top: 5px;" class="apb-room-seleted_package">
			<?php APB_Email::email_days_pricing( $item, $room_type_id ); ?>

			<?php APB_Email::email_extra_price( $item, $room_type_id ); ?>

			<?php APB_Email::email_package( $item, $room_type_id ); ?>

			<?php APB_Email::email_extra_sale( $item, $room_type_id ); ?>
		</div>

		<?php APB_Email::email_subtotal( $item['total_price'], $item['id'] ); ?>
	</div>
	<!-- END / ITEM -->
<?php } 

// die();
?>

<div style="border-bottom: 1px solid #e4e4e4; padding: 20px 20px 15px;" class="apb-room-selected_item">
	<?php if ( $total_ex_tax_wc != $order_total_price ) { ?>
		<div class="apb-room-seleted_total-room" style="color:#333333;font-size:14px;font-weight:bold;border-top: 1px solid #e4e4e4;padding-top: 15px;">
			<?php esc_html_e( 'Tax', 'awebooking' ); ?>
			<span class="apb-amount" style="color:#46598b;float:right;font-weight:bold;"><?php echo wp_kses_post( AWE_function::apb_price( $order_total_price - $total_ex_tax_wc ) ); ?></span>
		</div>
	<?php } ?>

	<div class="apb-room-seleted_total-room" style="color:#333333;font-size:14px;font-weight:bold;border-top: 1px solid #e4e4e4;padding-top: 15px;">
		<?php esc_html_e( 'Total', 'awebooking' ); ?>
		<span class="apb-amount" style="color:#46598b;float:right;font-weight:bold;"><?php echo wp_kses_post( AWE_function::apb_price( $order_total_price ) ); ?></span>
	</div>
</div>

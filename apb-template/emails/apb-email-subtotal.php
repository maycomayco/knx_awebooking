 <?php
/**
 * Email subtotal
 *
 * @package Awebooking
 */

?>

<div class="apb-room-seleted_total-room" style="color:#333333;font-size:14px;font-weight:bold;border-top: 1px solid #e4e4e4;padding-top: 15px;">




<?php
$apb_deposit_type = get_post_meta( $id, '_pre_payment_type' );
$apb_deposit_number = get_post_meta( $id, '_deposit_payment' );

$prepay = '';
if ( ! empty( $apb_deposit_type ) && 'none' != $apb_deposit_type[0] ) {
	if ( 'percent' == $apb_deposit_type[0] ) {
		$prepay = $apb_deposit_number[0] . '%';
		$prepay = AWE_function::apb_price( ( $apb_deposit_number[0] / 100 ) * $subtotal );
		$remain = $subtotal - ( ( $apb_deposit_number[0] / 100 ) * $subtotal );
		$remain = AWE_function::apb_price( $remain );
	} else if ( 'money' == $apb_deposit_type[0] ) {
		$prepay = AWE_function::apb_price( $apb_deposit_number[0] );
		$remain = AWE_function::apb_price( $subtotal - $apb_deposit_number[0] );
	}
	?>
	<p>
	<?php esc_html_e( 'Pre-payment', 'awebooking' ); ?>
	<span class="apb-amount" style="color:#46598b;float:right;font-weight:bold;"><?php echo wp_kses_post(  $prepay ); ?></span>
	</p>

	<p>
	<?php esc_html_e( 'Remain', 'awebooking' ); ?>
	<span class="apb-amount" style="color:#46598b;float:right;font-weight:bold;"><?php echo wp_kses_post( $remain ); ?></span>
	</p>
<?php
?>
<?php 
}
?>
	<p>
	<?php esc_html_e( 'Subtotal', 'awebooking' ); ?>
	<span class="apb-amount" style="color:#46598b;float:right;font-weight:bold;"><?php echo wp_kses_post( AWE_function::apb_price( $subtotal ) ); ?></span>
	</p>
</div>

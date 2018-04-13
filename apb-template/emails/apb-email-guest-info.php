<?php
/**
 * Email guest info
 *
 * @package Awebooking
 */

$userID = get_post_meta( $post->ID, 'custommer', true );
$user = get_user_by(' id', $userID );

if( class_exists( 'WC_Order' ) ) {
	$order = new WC_Order( $order_id );
	$phone = $order->billing_phone;
	$email = !empty( $order->billing_email ) ? $order->billing_email :  $user->data->user_email;
	$name =  !empty( $order->billing_last_name ) ?  $order->billing_first_name . ' ' . $order->billing_last_name : $user->data->display_name;
}else{

	$phone = '- None -';
	$email = $user->data->user_email;
	$name = $user->data->display_name;
}



?>
<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;">
	<?php esc_html_e( 'Order ID', 'awebooking' ); ?>
</h6>

<span style="display: inline-block; font-size: 12px; line-height: 1.428em; vertical-align: middle;" class="apb-option">
	#<?php echo esc_html( $order_id ); ?>
</span>

<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;">
	<?php esc_html_e( 'Customer name', 'awebooking' ); ?>
</h6>

<span style="display: inline-block; font-size: 12px; line-height: 1.428em; vertical-align: middle;" class="apb-option">
	<?php echo esc_html( $name ); ?>
</span>

<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;">
	<?php esc_html_e( 'Customer email', 'awebooking' ); ?>
</h6>

<span style="display: inline-block; font-size: 12px; line-height: 1.428em; vertical-align: middle;" class="apb-option">
	<?php echo esc_html( $email ); ?>
</span>

<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;">
	<?php esc_html_e( 'Customer phone', 'awebooking' ); ?>
</h6>

<span style="display: inline-block; font-size: 12px; line-height: 1.428em; vertical-align: middle;" class="apb-option">
	<?php echo esc_html( $phone ); ?>
</span>

<span style="display: inline-block; font-size: 12px; line-height: 1.428em; vertical-align: middle;" class="apb-option">
	<?php printf( esc_html__( '%s Adult', 'awebooking' ), absint( $item['room_adult'] ) ); ?>, <?php printf( esc_html__( '%s Child', 'awebooking' ), absint( $item['room_child'] ) ); ?>
</span>
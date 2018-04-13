<?php
/**
 * The template for displaying loop price of room.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

$currency = AWE_function::get_option( 'woocommerce_currency' );
$apb_currency_pos = AWE_function::get_option( 'woocommerce_currency_pos' ) && in_array( AWE_function::get_option( 'woocommerce_currency_pos' ), array( 'left', 'right', 'left_space', 'right_space' ) ) ? AWE_function::get_option( 'woocommerce_currency_pos' ) : 'left';
$apb_decimals = AWE_function::get_option( 'woocommerce_price_num_decimals' ) ? absint( AWE_function::get_option( 'woocommerce_price_num_decimals' ) ) : 2;
$apb_decimal_sep = AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ? AWE_function::get_option( 'woocommerce_price_decimal_sep' ) : '.';
$apb_thousand_sep = AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ? AWE_function::get_option( 'woocommerce_price_thousand_sep' ) : ',';



?>

<span class="apb-room_price">
	<?php printf( esc_html__( '%s/night', 'awebooking' ), '<span class="apb-room_amount apb-price-' . absint( $room_type_id ) . '">' . AWE_function::apb_price( $price ) . '</span>' ) ?>
	<input class="room-price-base-<?php echo absint( get_the_ID() ) ?>" data-base="<?php echo $price; ?>" value="<?php echo $price ?>" type="hidden">
</span>

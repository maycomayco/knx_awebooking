<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying loop price of room single
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

printf(
	'<p class="price">
		%s
		<input class="room-price-base-%s" data-base="%s" value="%s" type="hidden">
	</p>',
	sprintf( esc_html__( '%s/night', 'awebooking' ), '<span class="amount apb-price-' . absint( get_the_ID() ) . '">' . AWE_function::apb_price( $room_price ) . '</span>' ),
	get_the_ID(),
	$room_price,
	$room_price
);

/**
 * Total price of day
 * Not change.
 */
$apb_currency_pos = AWE_function::get_option( 'woocommerce_currency_pos' ) && in_array( AWE_function::get_option( 'woocommerce_currency_pos' ), array( 'left', 'right', 'left_space', 'right_space' ) ) ? AWE_function::get_option( 'woocommerce_currency_pos' ) : 'left';
$apb_decimals = AWE_function::get_option( 'woocommerce_price_num_decimals' ) ? absint( AWE_function::get_option( 'woocommerce_price_num_decimals' ) ) : 2;
$apb_decimal_sep = AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ? AWE_function::get_option( 'woocommerce_price_decimal_sep' ) : '.';
$apb_thousand_sep = AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ? AWE_function::get_option( 'woocommerce_price_thousand_sep' ) : ',';

printf(
	'<input class="total-price-room-%s" value="%s" data-currency-pos="%s" data-decimals="%s" data-decimal-sep="%s" data-thousand-sep="%s" data-currency="%s" type="hidden">',
	absint( get_the_ID() ),
	( float ) $room_price,
	esc_attr( $apb_currency_pos ),
	absint( $apb_decimals ),
	esc_attr( $apb_decimal_sep ),
	esc_attr( $apb_thousand_sep ),
	esc_attr( AWE_function::get_currency( $currency ) )
);

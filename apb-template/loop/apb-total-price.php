<?php
/**
 * The template for displaying total loop price of room.
 *
 * Override this template by copying it to your theme.
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 * @since 2.2.1
 */

$currency = AWE_function::get_option( 'woocommerce_currency' );
$apb_currency_pos = AWE_function::get_option( 'woocommerce_currency_pos' ) && in_array( AWE_function::get_option( 'woocommerce_currency_pos' ), array( 'left', 'right', 'left_space', 'right_space' ) ) ? AWE_function::get_option( 'woocommerce_currency_pos' ) : 'left';
$apb_decimals = AWE_function::get_option( 'woocommerce_price_num_decimals' ) ? absint( AWE_function::get_option( 'woocommerce_price_num_decimals' ) ) : 2;
$apb_decimal_sep = AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ? AWE_function::get_option( 'woocommerce_price_decimal_sep' ) : '.';
$apb_thousand_sep = AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ? AWE_function::get_option( 'woocommerce_price_thousand_sep' ) : ',';
?>

<span class="apb-room_price apb-total-price">
	<?php
	printf(
		esc_html__( 'Total: %s', 'awebooking' ),
		'<span class="apb-room_amount apb-total-all-price-' . absint( $room_type_id ) . '" data-value="' . $price . '">' . AWE_function::apb_price( $price ) . '</span>'
	);
	?>
</span>

<?php
printf(
	'<input class="total-price-room-%s" value="%s" data-currency-pos="%s" data-decimals="%s" data-decimal-sep="%s" data-thousand-sep="%s" data-currency="%s" type="hidden">',
	absint( $room_type_id ),
	( float ) $price,
	esc_attr( $apb_currency_pos ),
	absint( $apb_decimals ),
	esc_attr( $apb_decimal_sep ),
	esc_attr( $apb_thousand_sep ),
	esc_attr( AWE_function::get_currency( $currency ) )
);

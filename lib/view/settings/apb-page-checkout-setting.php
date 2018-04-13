<?php
/**
 *  Tempalte setting form check available
 *
 * @version   1.0
 * @package   AweBooking/admin/
 * @author    AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$apb_deposit_type = get_option( 'apb_deposit_type' );
?>

<div class="form-elements">
	<p><?php esc_html_e( 'Checkout presentation style', 'awebooking' ); ?></p>
	<div class="form-radios">
		<div class="form-elements">
			<input id="rooms_checkout_style_1" type="radio" <?php checked( 1, get_option( 'rooms_checkout_style' ) ); ?> value="1" name="rooms_checkout_style">
			<label for="rooms_checkout_style_1"><?php esc_html_e( 'WooCommerce checkout', 'awebooking' ) ?> </label><br/>
			<span class="description"><?php esc_html_e( 'Checkout and payment with plugin woocommerce. With checkout woocommerce function, you have to install page checkout of woocommerce', 'awebooking' ); ?></span>
		</div> 
		
		<div class="form-elements">
			<input id="rooms_checkout_style_2" type="radio" <?php checked( 2, get_option( 'rooms_checkout_style' ) ); ?> value="2" name="rooms_checkout_style">
			<label for="rooms_checkout_style_2"><?php esc_html_e( 'Contact form 7 checkout', 'awebooking' ); ?> </label><br/>
			<span class="description"><?php esc_html_e( 'Use CF7 to finish the checkout step and recevie your guest information. NOTE: Required [email* apb-email] and [text* apb-name] in form, then copy CF7 shortcode and insert into Awe Checkout Page.', 'awebooking' ); ?></span>
		</div>

		<div class="apb-js-wc-deposit">
			<div class="form-elements">
				<label for="deposit_type"><?php esc_html_e( 'Prepayment type' ) ?></label><br>
				<select name="apb_deposit_type" id="deposit_type">
					<option <?php selected( 'none', $apb_deposit_type ); ?> value="none"> <?php esc_html_e( 'Disable' ) ?> </option> 
					<option <?php selected( 'percent', $apb_deposit_type ); ?> value="percent"><?php esc_html_e( 'By Percent' ) ?></option> 
					<option <?php selected( 'money', $apb_deposit_type ); ?> value="money"><?php esc_html_e( 'By Fixed Amount' ) ?></option> 
				</select>	
			</div>

			<div class="form-elements" <?php echo ( 'none' == $apb_deposit_type or '' == $apb_deposit_type ) ? 'style="display:none;"' : ''; ?>>
				<label> <?php  esc_html_e( 'Money number', 'awebooking' ); ?> </label> <br>
				<input type="text" max="100" name="apb_deposit_number" value="<?php echo get_option( 'apb_deposit_number' ) ?>"> 
				<span class="apb-js-type-deposit" data-type="<?php echo AWE_function::get_option( 'woocommerce_currency' ) ?>">
					<?php echo ( 'money' == $apb_deposit_type ) ? AWE_function::get_option( 'woocommerce_currency' ) : '%' ?>
				</span> 
			</div>

		</div>

	</div>
</div>

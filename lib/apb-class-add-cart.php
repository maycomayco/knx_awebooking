<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Add To Cart Class
 *
 * @class 		APB_Cart
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

class APB_Cart {

	public function __construct() {
		add_action("wp_ajax_user_check_available", array( $this, "user_check_available"));
		add_action("wp_ajax_nopriv_user_check_available", array( $this, "user_check_available"));

		add_action("wp_ajax_apb_ResultIDRoom", array( $this, "apb_ResultIDRoom"));
		add_action("wp_ajax_nopriv_apb_ResultIDRoom", array( $this, "apb_ResultIDRoom"));
	}


	/**
	 *	ApbCart_GetPrice
	 *  @param string $from
	 *  @param string $to
	 *  @param int $room_id
	 */
	public function ApbCart_GetPrice( $from, $to, $room_id, $byDay = true ) {
		$price = 0;
		if ( $byDay ) {
			$args_price = AWE_function::get_pricing_of_days( $from, $to, $room_id );
			foreach ( $args_price as $itemPrice ) {
				foreach ( $itemPrice as $value ) {
					$price = $value;
				}
			}
		} else {
			$price = count( AWE_function::range_date( $from, $to ) ) - 1;
		}

		return $price;
	}

	public function user_check_available() {
		ob_start();

		if ( $_POST['control'] == "check") {
			if( get_option('rooms_checkout_style') == 1){
				if(class_exists('WooCommerce')){
					WC()->cart->empty_cart();
				}
			}
			if( get_option('rooms_checkout_style') == 2){
				 $_SESSION['apb_cart'] = array();
			}
		}

		if ( $_POST['control'] == "change") {
			if(get_option('rooms_checkout_style') == 1){
				if(class_exists('WooCommerce')){
					global $woocommerce;
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

						if ( $cart_item['product_id'] == $_POST['room_id']) {

							WC()->cart->remove_cart_item( $cart_item_key);
						}
					}
				}
			}
			// if(get_option('rooms_checkout_style') == 2){
			//      unset( $_SESSION['apb_cart'][ $_POST['key']]);
			// }
		}
		do_action( 'loop_data_check_availability' );
		$html = ob_get_clean();
		echo $html;
		die;
		return false;
	}

	public function apb_ResultIDRoom() {
		$args_rooms = explode( ',', $_POST['args_rooms'] );
		$room_id = array();
		for ( $i = 0; $i <= $_POST['booking_num'] - 1; $i++ ) {
			$room_id[] = $args_rooms[ $i ];
		}
		echo json_encode( $room_id );
		die;
		return false;
	}
}
new APB_Cart();

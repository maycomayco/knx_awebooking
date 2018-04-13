<?php
/**
 * AWE Paypal
 *
 * @class 		ApbPaypal
 * @version		1.0
 * @package		AweBooking
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class ApbPaypal {

	public function __construct(){
		//add_filter( 'woocommerce_payment_successful_result', array( $this, 'apb_payment_successful_result' ), 20, 2 );
		add_filter( 'woocommerce_paypal_args', array( $this, 'paypal_args' ), 10, 1 );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'payment_complete_order_status' ), 10, 1 );
	}
	public function paypal_args( $order ){

		$apb_prepayment_type = isset( $_REQUEST[ 'apb_prepayment_type' ] ) ? $_REQUEST[ 'apb_prepayment_type' ] : 'full';

		if( 'full' != $apb_prepayment_type ) {
			$current_price  = $order['amount_1'];
			$ex_prepayment_type = explode( '|', $apb_prepayment_type );

			if( '%' == $ex_prepayment_type[1] ){
				$order['amount_1'] = ( $ex_prepayment_type[0]/100 ) * $current_price;
			}else{
				$order['amount_1'] = $current_price - $ex_prepayment_type[0];
			}
		}

		return $order;
	}	
	public function payment_complete_order_status( $order_status, $order_id  ){
		return 'completed';
	}
	public function apb_payment_successful_result( $result, $order_id ){

		if( class_exists( 'WC_Gateway_Paypal' ) ) {
			$result = new WC_Gateway_Paypal;

			$data = $result->process_payment( $order_id );
			
		}	
	}
}


new ApbPaypal();

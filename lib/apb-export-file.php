<?php
/**
 * AWE Post Type
 *
 * @class       AWE_Post_type
 * @version     1.0
 * @package     AweBooking/Classes/
 * @author      AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AWE_Post_type.
 */
class AWE_Export extends AWE_function {

	/**
	 * [get_user_name get name of customer]
	 * @param  [int] $id [id order]
	 * @return [string]     [display name]
	 */
	public static function get_user_name( $id ){

		if ( get_post_meta($id, '_shipping_last_name', true) ) {
			$username = get_post_meta($id, '_shipping_last_name', true);
		} else {
			$user = get_post_meta( $id, 'apb_customer_name', true );
			if ( ! empty( $user ) ) {
			
				$username = $user;
			} else {

				$cm_id = get_post_meta( $id, 'custommer', true );
				$get_user = get_user_by( 'id',$cm_id );
				$get_user = (array) $get_user;
				if(!empty($get_user['data'])){
					$data_user = (array) $get_user['data'];
					$username = $data_user['display_name'];
				} else {
		
					$username = 'Guest';
				}
			}
		}
		return $username;
	}

	/**
	 * [get_date of arrival date and departure date]
	 * @param  [int] $id [id order]
	 * @return [array] $d    
	 */
	public static function get_date( $id ){
		  $d = array();
		  $d['arrival_date']   = date_i18n( AWE_function::get_current_date_format(), strtotime( get_post_meta( $id, 'from', true ) ) );
		  $d['departure_date'] = date_i18n( AWE_function::get_current_date_format(), strtotime( get_post_meta( $id, 'to', true ) ) );
		  return  $d;
	}

	/**
	 * [get_email get email of customer]
	 * @param  [int] $id [id order]
	 * @return [type]     [description]
	 */
	public static function get_email( $id ){
		$cm_id = get_post_meta( $id, 'custommer', true );
		$get_user = get_user_by( 'id',$cm_id );
		if( NULL != ( $get_user ) ) {
			$email = $get_user->user_email;
		} else {
			$email = '';
		}
		return esc_html( $email );
	}

	public static function get_order_data( $id ){
		$order_data  = get_post_meta( $id, 'apb_data_order', true );
		return $order_data[0];
	}

	public static function get_order_status( $id ){
		$order_status   = get_post_status( $id );

		switch ( $order_status ) {
			case 'wc-pending':
			case 'wc-processing':
			case 'wc-on-hold':
				$booking_status = 'apb-pending';
				break;

			case 'wc-completed':
				$booking_status = 'apb-completed';
				break;

			case 'wc-cancelled':
			case 'wc-refunded':
			case 'wc-failed':
				$booking_status = 'apb-cancelled';
				break;

			default:
				$booking_status = $order_status;
		}
		$status = AWE_function::apb_get_trans_order_statuses( $booking_status );
		return $status;
	}

	/**
	 * get room name
	 * @param  [int] $id [room id]
	 * @return [varchar]     [room name]
	 */
	public static function get_room_current( $id ){
		$get_room_current = get_post( $id );
		return esc_html( $get_room_current->post_title );
	}

}

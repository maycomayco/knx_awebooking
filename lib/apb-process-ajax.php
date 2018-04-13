<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Process Ajax Autoload
 *
 * @class       Process_ajax
 * @package     AweBooking/Classes/
 * @author      AweTeam
 */


class Process_ajax {

	private $order_status = array();


	public function __construct() {

		// Get the order status complete.
		if ( function_exists( 'wc_get_order_statuses' ) ) {
			$this->order_status = wc_get_order_statuses();
		} else {
			$this->order_status = AWE_function::apb_get_order_statuses();
		}

		add_action( 'wp_ajax_genform_option', array( $this, 'genform_option' ) );
		add_action( 'wp_ajax_delete_type_option', array( $this, 'delete_type_option' ) );

		add_action( 'wp_ajax_get_pricing', array( $this, 'get_pricing' ) );
		add_action( 'wp_ajax_add_pricing_for_room', array( $this, 'add_pricing_for_room' ) );

		add_action( 'wp_ajax_add_availability_for_room', array( $this, 'add_availability_for_room' ) );
		add_action( 'wp_ajax_get_availability', array( $this, 'get_availability' ) );
		add_action( 'wp_ajax_get_availability_2_2', array( $this, 'get_availability_2_2' ) );
		add_action( 'wp_ajax_get_info_event', array( $this, 'get_info_event' ) );

		add_action( 'wp_ajax_user_room_select', array( $this, 'user_room_select' ) );

		add_action( 'wp_ajax_full_select_room', array( $this, 'full_select_room' ) );

		add_action( 'wp_ajax_admin_check_availability', array( $this, 'admin_check_availability' ) );
		add_action( 'wp_ajax_admin_get_option_room', array( $this, 'admin_get_option_room' ) );

		add_action( 'wp_ajax_single_check_available', array( $this, 'single_check_available' ) );

		add_action( 'wp_ajax_apb_before_add_cart', array( $this, 'apb_before_add_cart' ) );
		add_action( 'wp_ajax_add_to_order', array( $this, 'add_to_order' ) );
		add_action( 'wp_ajax_info_cart_ckeckout', array( $this, 'info_cart_ckeckout' ) );
		add_action( 'wp_ajax_apb_ckeckout_form', array( $this, 'apb_ckeckout_form' ) );

		add_action( 'wp_ajax_Apb_add_booking_single_available_manage', array( $this, 'Apb_add_booking_single_available_manage' ) );


		/*
		 * front end
		 */
		add_action( 'wp_ajax_apb_frontend_get_availability', array( $this, 'apb_frontend_get_availability' ) );
		add_action( 'wp_ajax_nopriv_apb_frontend_get_availability', array( $this, 'apb_frontend_get_availability' ) );


		add_action( 'wp_ajax_nopriv_get_availability', array( $this, 'get_availability' ) );
		add_action( 'wp_ajax_nopriv_user_room_select', array( $this, 'user_room_select' ) );

		add_action( 'wp_ajax_nopriv_full_select_room', array( $this, 'full_select_room' ) );
		add_action( 'wp_ajax_nopriv_single_check_available', array( $this, 'single_check_available' ) );
		add_action( 'wp_ajax_nopriv_apb_before_add_cart', array( $this, 'apb_before_add_cart' ) );
		add_action( 'wp_ajax_nopriv_add_to_order', array( $this, 'add_to_order' ) );
		add_action( 'wp_ajax_nopriv_info_cart_ckeckout', array( $this, 'info_cart_ckeckout' ) );
		add_action( 'wp_ajax_nopriv_apb_ckeckout_form', array( $this, 'apb_ckeckout_form' ) );


		add_action( 'wp_ajax_apb_check_available', array( $this, 'check_available' ) );
		add_action( 'wp_ajax_nopriv_apb_check_available', array( $this, 'check_available' ) );

		add_action( 'wp_ajax_apb_add_room_to_cart', array( $this, 'add_room_to_cart' ) );
		add_action( 'wp_ajax_nopriv_apb_add_room_to_cart', array( $this, 'add_room_to_cart' ) );

		add_action( 'wp_ajax_apb_update_layout_room_select', array( $this, 'update_layout_room_select' ) );
		add_action( 'wp_ajax_nopriv_apb_update_layout_room_select', array( $this, 'update_layout_room_select' ) );

		add_action( 'wp_ajax_apb_remove_cart', array( $this, 'remove_cart' ) );
		add_action( 'wp_ajax_nopriv_apb_remove_cart', array( $this, 'remove_cart' ) );

		add_action( 'wp_ajax_apb_change_selected_item_layout', array( $this, 'change_selected_item_layout' ) );
		add_action( 'wp_ajax_nopriv_apb_change_selected_item_layout', array( $this, 'change_selected_item_layout' ) );

		add_action( 'wp_ajax_apb_remove_room_from_cart', array( $this, 'remove_room_from_cart' ) );
		add_action( 'wp_ajax_nopriv_apb_remove_room_from_cart', array( $this, 'remove_room_from_cart' ) );

		add_action( 'wp_ajax_apb_get_room_type_availability_color', array( $this, 'get_room_type_availability_color' ) );
		add_action( 'wp_ajax_nopriv_apb_get_room_type_availability_color', array( $this, 'get_room_type_availability_color' ) );

		add_action( 'wp_ajax_apb_remove_old_data', array( $this, 'remove_old_data' ) );

	}

	public function remove_old_data() {
		AWE_function::reset_old_data();
		esc_html_e( 'Remove old version data successfully', 'awebooking' );
		die();
	}


	/**
	 * Render form input option from ajax request.
	 */
	public function genform_option() {
		$html = '<tr class="draggable odd item-option">
					<td>
						<div class="form-item form-type-textfield apb-form-type-icon">
							<div class="form-elements">
								<label>&nbsp; </label>
								<input type="hidden" class="apb-input-icon-new-' .( $_POST['itemnums']+1) . '" name="option_icon[]">
								<button type="button" data-id="new-' .( $_POST['itemnums']+1) . '" class="apb-js-chose-icon button apb-icon-new-' .( $_POST['itemnums']+1) . '">' . esc_html__( 'Icon', 'awebooking' ) . '</button>
							</div>
						</div>
						<div class="form-item form-type-textfield">
							<div class="form-elements">
								<label>' . esc_html__( 'Name', 'awebooking' ) . '</label>
								<input type="text" size="15" value="" name="option_name[]">
							</div>
						</div>
						<div class="form-item form-type-textfield text-desc">
							<div class="form-elements">
								<label>' . esc_html__( 'Description', 'awebooking' ) . ' </label>
								<textarea name="option_desc[]"></textarea>
							</div>
						</div>
						<input type="hidden" name="option_operation[]" value="add">';
		  $html .=      '<div class="form-item form-type-textfield">
							<div class="form-elements">
								<label>' . esc_html__( 'Price', 'awebooking' ) . '</label>
								<input type="hidden" name="option_id[]">
								<input name="option_value[]" type="text" size="10" value="">
								<div class="awe-set-type-package">
									<input class="apb-option-type-js" data-id="new' .( $_POST['itemnums']+1) . '" type="checkbox"  value="1">
									<input type="hidden" id="apb-option-type-new' .( $_POST['itemnums']+1) . '" name="type_package[]" value="0">
									<span>' . esc_html__( 'Daily package', 'awebooking' ) . '</span>
								</div>
							</div>
						</div>
						<div class="form-item form-type-textfield">
							<div class="form-elements">
								<label>&nbsp; </label>
								<button type="button" class="remove-option-js button">Remove</button>
							</div>
						</div>
					</td>
				</tr>';
		echo json_encode( $html);
		die;
		return FALSE;
	}
	/*
	 * Delete option of room or type room
	 */
	public function delete_type_option() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}apb_booking_options WHERE id = %d", $_POST['id'] ) );
		echo json_encode( "yes");
		die;
		return false;
	}

	/*********************** End form option **********************
	 ******************** Admin check Available ******************/
	/**
	 * Check available anh show all room to created book from administrator
	 */
	function admin_check_availability() {
		if ( empty( $_POST['from'] ) || empty( $_POST['to'] ) || empty( $_POST['adult'] ) ) {
			esc_html_e( 'No room availability', 'awebooking' );
			die();
		}

		$from = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['from'] ) ) );
		$to = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['to'] ) ) );

		$adult = absint( $_POST['adult'] );
		$child = isset( $_POST['child'] ) ? absint( $_POST['child'] ) : 0;

		$number_nights = AWE_function::get_number_of_nights( $from, $to );
		if ( $number_nights > AWE_function::get_max_night() ) {
			printf(
				esc_html__( 'Max nights to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_night() )
			);
			die();
		}

		if ( $adult > AWE_function::get_max_adult() ) {
			printf(
				esc_html__( 'Max adult to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_adult() )
			);
			die();
		}

		if ( $child > AWE_function::get_max_child() ) {
			printf(
				esc_html__( 'Max child to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_child() )
			);
			die();
		}

		$room_type = AWE_function::check_available( $from, $to, $adult, $child );
		if ( ! empty( $room_type ) ) {
			$room_type_id = array();
			foreach ( $room_type as $k => $v ) {
				$room_type_id[] = $k;
			}

			$apb_query = new WP_Query( array(
				'post__in'          => $room_type_id,
				'posts_per_page'    => -1,
				'post_type'         => 'apb_room_type',
			) );
			if ( $apb_query->have_posts() ) {
				$html = '';
				while ( $apb_query->have_posts() ) : $apb_query->the_post();
					$room_price     = get_post_meta( get_the_ID(), 'base_price', true );
					$room_desc      = get_post_meta( get_the_ID(), 'room_desc', true );
					$extra_sale     = get_post_meta( get_the_ID(), 'extra_sale', true );
					$info_price_day = AWE_function::get_pricing_of_days( $from, $to, get_the_ID(), 1 );
					$item = get_post( get_the_ID() );

					$html .= '<div class="room-' . absint( get_the_ID() ) . '">';
					$html .= sprintf(
						'<input name="data[room_id]" type="radio" data-name="%1$s" class="rooms" id="%2$s" value="%3$s">',
						esc_attr( $item->post_title ),
						esc_html( $item->post_name ),
						absint( get_the_ID() )
					);
					$html .= '<label for="' . esc_attr( $item->post_name ) . '">';
					$html .= esc_html( get_the_title() ) . ' - ';
					$html .= sprintf( __( 'Price Basic: %s', 'awebooking' ), sprintf( __( '%s/night', 'awebooking' ), AWE_function::apb_price( $room_price ) ) );
					$html .= '</label>';
					$html .= '<span class="spinner is-active apb-load-' . absint( get_the_ID() ) . '" style="display: none;"></span>';
					$html .= '</div>';

					if ( isset( $_POST['order_id'] ) ) {
						$html .= '<input type="hidden" name="order_current" value="' . absint( $_POST['order_id'] ) . '">';
					}

				endwhile;
				wp_reset_postdata();
				echo $html;
				die();
			} else {
				esc_html_e( 'No room availability', 'awebooking' );
				die();
			}
		} else {
			esc_html_e( 'No room availability', 'awebooking' );
			die();
		}

		die();
	}

	public function admin_get_option_room() {
		$from = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['from'] ) ) );
		$to = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['to'] ) ) );
		$room_id = absint( $_POST['room_id'] );
		$room_name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$data_option = AWE_function::get_room_option( $room_id, 'apb_room_type' );
		$total_night = AWE_function::get_number_of_nights( $from, $to );

		$adult = $_REQUEST['room_adult'][0]['adult'];
		$child = $_REQUEST['room_child'][0]['child'];

		$total_price = AWE_function::calculate_total_price( $room_id, $from, $to, $adult, $child );

		$html = '';
		$html .= '<div class="list-option-room">';
			if ( ! empty( $data_option ) ) {
				$html .= '<div class="form-item">';
					$html .= '<div class="form-elements">';
						$html .= '<label>' . sprintf( esc_html__( 'Package of %s', 'awebooking' ), esc_html( $room_name ) ) . '</label>';

						foreach ( $data_option as $item ) {
							$package_name = $item->option_name;
							$price_package = $item->option_value;
							$package_operation = $item->option_operation;
							$daily_package = $item->revision_id;
							$package_icon = $item->option_icon;

							$html .= '<p>';

							$html .= sprintf(
								'<label for="package-%1$s"><input type="checkbox" id="package-%1$s" name="package_data[id][]" class="options_operation_js package-%1$s" data-operation="%2$s" data-value="%3$s" data-nights="%6$s" data-daily="%4$s" value="%1$s"> <i class="%7$s"></i>  %5$s </label> ',
								absint( $item->id ),
								esc_attr( $package_operation ),
								esc_attr( $price_package ),
								absint( $daily_package ),
								esc_html( $package_name ),
								absint( $total_night ),
								esc_html( $package_icon )
							);

							$html .= sprintf(
								'<input data-id="%1$s" class="package_total_js package-num-%1$s" data-value="%2$s" name="package_data[total][]" type="number" min="1" value="1"> %3$s / %4$s',
								absint( $item->id ),
								esc_attr( $price_package ),
								esc_attr( AWE_function::apb_price( $price_package ) ),
								( $daily_package == 1 ) ? __( 'day', 'awebooking' ) :  __( 'package', 'awebooking' )
							);

							$html .= '</p>';
						}

					$html .= '</div>';
				$html .= '</div>';
			}

			$html .= '<div class="form-item book_total_price">';
				$html .= '<label>' . esc_html__( 'Price', 'awebooking' ) . '</label>';
				$html .= '<input type="text" class="form-text total_price_js" readonly size="10" data-default="' . ( float ) $total_price . '" value="' . ( float ) $total_price . '" name="data[price]">';
			$html .= '</div>';
		$html .= '</div>';

		echo $html;
		die;
		return false;
	}

	####################### End Admin check Available ######################
	######################## Pricing Manage #######################

	/*
	 * Ajax Add price by date for room
	 */

	public function add_pricing_for_room() {
		$from = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['rooms_start_date'] ) ) );
		$to = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['rooms_end_date'] ) ) );
		global $wpdb;
		$start_month    = date( 'm', strtotime( $from ) );
		$end_month      = date( 'm', strtotime( $to ) );
		$start_year     = date( 'Y', strtotime( $from ) );
		$end_year       = date( 'Y', strtotime( $to ) );

		$start_date     = date( 'd', strtotime( $from ) );
		$end_date       = date( 'd', strtotime( $to ) );

		$date_start     = date( 'd', strtotime( $from ) );
		$date_end       = date( 'd', strtotime( $to ) );

		$amount         = wp_kses( $_POST['amount'], array() );
		$operation      = isset( $_POST['operation'] ) ? wp_kses( wp_unslash( $_POST['operation'] ), array() ) : 'add';
		$day_option     = isset( $_POST['day_option'] ) ? $_POST['day_option'] : null;

		################## GET ALL LIST DAY OF MONTH AND YEAR ##################
		# Result array all list day for month for year or one month
		########################################################################

		foreach ( $_POST['room_id'] as $room_id ) {
			$list_month_price = $this->get_list_month_pricing( $start_year, $end_year, $start_month, $end_month, $from, $to, $room_id, $amount, $operation );

			$list_update = $this->insert_pricing( $list_month_price, $room_id, $day_option );
			################## UPDATE PRICING ##################

			if ( isset( $list_update ) && ! empty( $list_update ) ) {
				/**
				 *  Update - array > 2.
				 */
				if ( count( $list_update ) > 2 ) {
					/**
					 * Update pring Start date.
					 */
					$this->list_update_pricing( $list_update, $from, $to, $room_id, $day_option, 'start' );
					/*
					 * Update pring center array
					 */
					$this->list_update_pricing( $list_update, $from, $to, $room_id, $day_option, 'center' );
					/*
					 * Update pring end date
					 */
					$this->list_update_pricing( $list_update, $from, $to, $room_id, $day_option, 'end' );
				} else {

					################## Update  - array <= 2 ###################

					/*
					 * Update pricing Start date
					 */

					$this->list_update_pricing( $list_update, $from, $to, $room_id, $day_option, 'start' );
					/*
					 * Update pring end date
					 */
					if (count( $list_update ) > 1) {
						$this->list_update_pricing( $list_update, $from, $to, $room_id, $day_option, 'end' );
					}
				}
			}
		}

		die;
		return false;
	}

	/**
	 * get_list_month_pricing
	 * @param $list_month_price array
	 * @param $room_id int
	 * @param $day_option array
	 */
	public function insert_pricing( $list_month_price, $room_id, $day_option = null ) {
		global $wpdb;

		foreach ( $list_month_price as $key => $val ) {
			$ex_year_month  = explode( '-', $key );
			$esc_string     = array( '%s', '%s', '%s' );
			$pricing_empty  = AWE_function::check_apb_pricing( $ex_year_month[1], $ex_year_month[0], $room_id );

			if ( empty( $pricing_empty ) ) {

				################## INSERT PRICING ##################

				$array_param = array(
					'unit_id'   => $room_id,
					'year'      => $ex_year_month[1],
					'month'     => $ex_year_month[0],
				);

				for ( $i = 1; $i <= 31; $i++) {
					if ( $day_option != Null) {
						if ( in_array( date( 'l', strtotime( date( $ex_year_month[1] . '-' . $ex_year_month[0] . '-' . $i ) ) ), $day_option ) ) {
							$esc_string[] = '%s';
							$array_param[ 'd' . $i ] = $val[ 'd' . $i ];
						} else {
							$esc_string[] = '%s';
							$array_param[ 'd' . $i ] = get_post_meta( $room_id, 'base_price', true );
						}
					} else {
						$esc_string[] = '%s';
						$array_param[ 'd' . $i ] = $val[ 'd' . $i ];
					}
				}
				$wpdb->insert(
					$wpdb->prefix . 'apb_pricing', $array_param, $esc_string
				);
			} else {
				/**
				 * Create array to update pricing.
				 */
				$list_update[][ $ex_year_month[1] . '-' . $ex_year_month[0] ] = $val;
			}
		}
		if (isset( $list_update ) ) {
			return $list_update;
		} else {
			$list_update = array();
		}
	}

	/**
	 * Update pricing for room.
	 * @param $list_update array
	 * @param $start_date date
	 * @param $end_date date
	 * @param $room_id int
	 * @param $day_option array
	 * @param $action string
	 */
	public function list_update_pricing( $list_update, $start_date, $end_date, $room_id, $day_option = null, $action ) {
		/**
		 * Update pricing Start date.
		 */
		if ( 'start' == $action ) {

			$key_list_update = array_keys( $list_update[0] );
			$ex_year_month_start = explode( '-', $key_list_update[0] );

			for ( $d = date( 'd', strtotime( $start_date ) ); $d <= 31; $d++ ) {

				$for_date = $ex_year_month_start[0] . '-' . $ex_year_month_start[1] . '-' . $d;

				if ( strtotime( $for_date ) >= strtotime( $start_date ) && strtotime( $for_date ) <= strtotime( $end_date ) ) {
					if ( null != $day_option ) {
						if ( in_array( date( 'l', strtotime( date( $ex_year_month_start[0] . '-' . $ex_year_month_start[1] . '-' . $d ) ) ), $day_option ) ) {
							$esc_string_start[] = '%s';
							$array_param_start[ 'd' . ( int ) $d ] = $list_update[0][ $key_list_update[0] ][ 'd' . ( int ) $d ];
						}
					} else {
						$esc_string_start[] = '%s';
						$array_param_start[ 'd' . ( int ) $d ] = $list_update[0][ $key_list_update[0] ][ 'd' . ( int ) $d ];
					}
					if ( isset( $array_param_start ) ) {
						$this->update_pricing( $room_id, $ex_year_month_start[0], $ex_year_month_start[1], $array_param_start, $esc_string_start, 'start' );
					}
				}
			}
		} elseif ( 'center' == $action ) {
			/**
			 * Update pring center array.
			 */
			for ( $i = 1; $i <= count( $list_update ) - 2; $i++ ) {
				$key_list_update = array_keys( $list_update[ $i ] );
				$ex_year_month = explode( '-', $key_list_update[0] );
				for ( $d = 1; $d <= 31; $d++ ) {
					if ( null != $day_option ) {
						if ( in_array( date( 'l', strtotime( date( $ex_year_month[0] . '-' . $ex_year_month[1] . '-' . $d ) ) ), $day_option ) ) {
							$esc_string[] = '%s';
							$array_param[ 'd' . ( int ) $d ] = $list_update[ $i ][ $key_list_update[0] ][ 'd' . ( int ) $d ];
						}
					} else {
						$esc_string[] = '%s';
						$array_param[ 'd' . ( int ) $d ] = $list_update[ $i ][ $key_list_update[0] ][ 'd' . ( int ) $d ];
					}
				}
				if ( isset( $array_param ) ) {
					$this->update_pricing( $room_id, $ex_year_month[0], $ex_year_month[1], $array_param, $esc_string, 'default' );
				}
			}
		} elseif ( 'end' == $action ) {
			$key_list_update = array_keys( $list_update[ count( $list_update ) - 1 ] );
			$ex_year_month_end = explode( '-', $key_list_update[0] );
			for ( $d = 1; $d <= date( 'd', strtotime( $end_date ) ); $d++ ) {
				$for_date = $ex_year_month_end[0] . '-' . $ex_year_month_end[1] . '-' . $d;
				if ( strtotime( $for_date ) <= strtotime( $end_date ) ) {
					if ( null != $day_option ) {
						if ( in_array( date( 'l', strtotime( date( $ex_year_month_end[0] . '-' . $ex_year_month_end[1] . '-' . $d ) ) ), $day_option ) ) {
							$esc_string_end[] = '%s';
							$array_param_end[ 'd' . ( int ) $d ] = $list_update[ count( $list_update ) - 1 ][ $key_list_update[0] ][ 'd' . ( int ) $d ];
						}
					} else {
						$esc_string_end[] = '%s';
						$array_param_end[ 'd' . ( int ) $d ] = $list_update[ count( $list_update ) - 1 ][ $key_list_update[0] ][ 'd' . ( int ) $d ];
					}
					if ( isset( $array_param_end ) ) {
						$this->update_pricing( $room_id, $ex_year_month_end[0], $ex_year_month_end[1], $array_param_end, $esc_string_end, 'end' );
					}
				}
			}
		}
	}

	/**
	 * Get_list_month_pricing.
	 * @param $start_year int
	 * @param $end_year int
	 * @param $start_month int
	 * @param $end_month array
	 * @param $start_date array
	 * @param $end_date array
	 * @param $room_id array
	 * @param $amount array
	 * @param $operation string
	 */
	static public function get_list_month_pricing( $start_year, $end_year, $start_month, $end_month, $start_date, $end_date, $room_id, $amount, $operation ) {
		if ( $start_month == $end_month ) {
			################## GET DEFAULT LIST PRICE ##################
			# Codation input start month == input end month
			############################################################
			$list_month_price[ ( int ) $start_month . '-' . $start_year ] = AWE_function::get_day_for_month_start( $start_year, $start_month, $start_date, $end_date, $room_id, $amount, $operation, "start");
		} else {

			$month_start = date( 'm', strtotime( $start_date ) );
			$month_end = date( 'm', strtotime( $end_date ) );

			$month_year_start = 12 - $month_start;

			$days = AWE_function::get_list_day( $start_year, $end_year, $start_month, $end_month );

			################## GET DEFAULT LIST PRICE ##################
			# Codation : number or month or year > 2
			############################################################
			if (count( $days ) > 2) {
				$list_month_price[(int) $days[0]['m'] . '-' . $days[0]["y"]] = AWE_function::get_day_for_month_start( $start_year, $start_month, $start_date, $end_date, $room_id, $amount, $operation, "start");
				for ( $i = 1; $i <= count( $days ) - 2; $i++) {
					$list_month_price[(int) $days[ $i]['m'] . '-' . $days[ $i]["y"]] = AWE_function::get_day_for_month_start( $start_year, $start_month, $start_date, $end_date, $room_id, $amount, $operation, "default");
				}
				$list_month_price[(int) $days[count( $days ) - 1]['m'] . '-' . $days[count( $days ) - 1]["y"]] = AWE_function::get_day_for_month_start( $end_year, $end_month, $start_date, $end_date, $room_id, $amount, $operation, "end");
			} else {
				$list_month_price[(int) $days[0]['m'] . '-' . $days[0]["y"]] = AWE_function::get_day_for_month_start( $start_year, $start_month, $start_date, $end_date, $room_id, $amount, $operation, "start");
				$list_month_price[(int) $days[1]['m'] . '-' . $days[1]["y"]] = AWE_function::get_day_for_month_start( $end_year, $end_month, $start_date, $end_date, $room_id, $amount, $operation, "end");
			}
		}
		return $list_month_price;
	}

	/**
	 * update_pricing : func update pricing
	 * @param $room_id int
	 * @param $year int
	 * @param $month int
	 * @param $array_param array
	 * @param $esc_string array
	 */
	public function update_pricing( $room_id, $year, $month, $array_param, $esc_string) {
		global $wpdb;
		$wpdb->update(
				$wpdb->prefix . 'apb_pricing', $array_param, array(
					'unit_id'   => $room_id,
					'year'      => $year,
					'month'     => $month
				), $esc_string, array( '%s', '%s', '%s' )
		);
	}

	public function get_pricing() {
		$date = explode('-', $_POST['date'] );
		$info_pricing = AWE_function::check_apb_pricing( $date[0], $date[1], wp_kses( $_POST['room_id'], '' ) );
		$total_day    = date( 't', mktime(0, 0, 0, $date[1], 1, $date[0] ) );
		if ( ! empty( $info_pricing) ) {
			for ( $day = 1; $day <= $total_day; $day++) {
				$get_price = "d" . $day;
				$key_pricing[ $day ] = $info_pricing[0]->$get_price;
			}
			foreach ( $this->PricingGetListSameValue( $key_pricing) as $key => $val) {
				foreach ( $val as $item ) {

					$day_start = ( $item[0] > 9) ? $item[0] : "0" . $item[0];
					$day_end = ( $item[count( $item ) - 1] > 9) ? $item[count( $item ) - 1] : "0" . $item[count( $item ) - 1];
					$default_pricing[] = array(
						"start" => $date[0] . '-' . $date[1] . '-' . $day_start . "T13:00:00Z",
						"end"   => $date[0] . '-' . $date[1] . '-' . $day_end . "T13:00:00Z",
						"color" => ( $key > 100) ? "green" : "orange",
						"title" => $key
					);
				}
			}
		} else {
			$default_pricing = array(
				array(
					'start' => $date[0] . '-' . $date[1] . '-01T13:00:00Z',
					'end'   => $date[0] . '-' . $date[1] . '-' . $total_day . 'T13:00:00Z',
					'color' => ( get_post_meta( wp_kses( $_POST['room_id'], '' ), 'base_price', true ) > 100) ? 'green' : 'orange',
					'title' => get_post_meta( wp_kses( $_POST['room_id'], '' ), 'base_price', true ),
				),
			);
		}

		echo json_encode( $default_pricing );
		die;
		return false;
	}

	function PricingGetListSameValue( $list ) {
		$result = array();
		$list_next = array();
		$current_key = null;
		foreach ( $list as $key => $value ) {
			$current_key = empty( $current_key ) ? $value : $current_key;
			if ( ! isset( $list_next[ $value ] ) ) {
				$list_next[ $value ] = 0;
			}
			if ( $current_key != $value ) {
				if ( ! empty( $result[ $value ][ $list_next[ $value ] ] ) ) {
					$list_next[ $value ] ++;
				}
				$list_next[ $current_key ] ++;
				$current_key = $value;
			}

			$result[ $value ][ $list_next[ $value ] ][] = $key;
		}
		return $result;
	}

	####################### End Pricing Manage #######################
	#
	#
	#
	#
	#
	####################### Availability Manage ######################

	public function add_availability_for_room() {
		global $wpdb;
		$from = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['rooms_start_date'] ) ) );
		$to = AWE_function::convert_date_to_mdY( sanitize_text_field( wp_unslash( $_POST['rooms_end_date'] ) ) );
		// $to2 = date( 'm/d/Y', strtotime( $to ) - DAY_IN_SECONDS );
		$to2 = $to;
		$unit_state = absint( $_POST['unit_state'] );

		$start_month    = date( 'm', strtotime( $from ) );
		$end_month      = date( 'm', strtotime( $to2 ) );
		$start_year     = date( 'Y', strtotime( $from ) );
		$end_year       = date( 'Y', strtotime( $to2 ) );
		$start_date     = date( 'd', strtotime( $from ) );
		$end_date       = date( 'd', strtotime( $to2 ) );
		$day_option     = isset( $_POST['day_option'] ) ? $_POST['day_option'] : null;

		foreach ( $_POST['room_id'] as $room_id ) {
			AWE_function::update_available( $from, $to, $room_id , 2 );
			// AWE_Controller::update_day_available( $from, $to, $room_id , 2);
			$list_month_avb = $this->get_list_month_availability( $start_year, $end_year, $start_month, $end_month, $from, $to2, $unit_state );
			$list_update    = $this->insert_availability( $list_month_avb, $room_id, $day_option);

			if ( ! empty( $list_update ) ) {
				/*
				 *  Update  - array > 2
				 */
				if ( count( $list_update ) > 2) {
					$this->list_update_availability( $list_update, $room_id, $from, $to2, $unit_state, $day_option, 'start' );
					$this->list_update_availability( $list_update, $room_id, $from, $to2, $unit_state, $day_option, 'center' );
					$this->list_update_availability( $list_update, $room_id, $from, $to2, $unit_state, $day_option, 'end' );

					/*
					 * Update availability Start date
					 */
				} else {

					/*
					 * Update availability Start date
					 */
					$this->list_update_availability( $list_update, $room_id, $from, $to2, $unit_state, $day_option, 'start' );

					/*
					 * Update pring end date
					 */
					if (count( $list_update ) > 1) {
						$this->list_update_availability( $list_update, $room_id, $from, $to2, $unit_state, $day_option, 'end' );
					}
				}
			}

		}
		die;
		return false;
	}

	public function get_list_month_availability( $start_year, $end_year, $start_month, $end_month, $start_day, $end_day, $unit_state ) {
		################## GET ALL LIST DAY OF MONTH AND YEAR ##################
		# Result array all list day for month for year or one month
		########################################################################

		if ( $start_month == $end_month) {

			################## GET DEFAULT LIST AVAILABILITY ##################
			# Codation input start month == input end month
			############################################################

			$list_month_avb[(int) $start_month . '-' . $start_year] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "start");
		} else {

			$days = AWE_function::get_list_day( $start_year, $end_year, $start_month, $end_month);

			if (count( $days ) > 2) {
				$list_month_avb[(int) $days[0]['m'] . '-' . $days[0]["y"]] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "start");

				for ( $i = 1; $i <= count( $days ) - 2; $i++) {
					$list_month_avb[(int) $days[ $i]['m'] . '-' . $days[ $i]["y"]] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "default");
				}

				$list_month_avb[(int) $days[count( $days ) - 1]['m'] . '-' . $days[count( $days ) - 1]["y"]] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "end");
			} else {
				$list_month_avb[(int) $days[0]['m'] . '-' . $days[0]["y"]] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "start");
				$list_month_avb[(int) $days[1]['m'] . '-' . $days[1]["y"]] = $this->get_day_for_month_start_avb( $start_year, $start_month, $start_day, $end_day, $unit_state, "end");
			}
		}
		return $list_month_avb;
	}

	public function insert_availability( $list_month, $room_id, $day_option = null) {
		global $wpdb;
		foreach ( $list_month as $key => $val) {
			$ex_year_month = explode('-', $key);
			/*
			 * Check room available exists
			 */
			$roomavai_empty = AWE_function::check_rooms_avb( $ex_year_month[1], $ex_year_month[0], $room_id );
			if ( empty( $roomavai_empty) ) {

				################## INSERT AVAILABILITY ##################
				$esc_string  = array( '%d', '%d', '%d' );

				$array_param = array(
					"unit_id" => $room_id,
					"year"    => $ex_year_month[1],
					"month"   => $ex_year_month[0],
				);
				for ( $i = 1; $i <= 31; $i++) {
					$esc_string[] = '%d';
					 if ( $day_option != Null) {
						 if (in_array(date( 'l', strtotime(date( $ex_year_month[1] . '-' . $ex_year_month[0] . '-' . $i) ) ), $day_option) ) {
								$array_param["d" . $i] = $val['d' . $i];
						  }else{
								$array_param["d" . $i] = 2;
						  }
					 }else{

					 }
					 if ( $val['d' . $i] == "A/V") {
						$array_param["d" . $i] = 2;
					} else {
						 if ( $day_option != Null) {
							 if (in_array(date( 'l', strtotime(date( $ex_year_month[1] . '-' . $ex_year_month[0] . '-' . $i) ) ), $day_option) ) {
								$array_param["d" . $i] = $val['d' . $i];
							}
						 }else{
								$array_param["d" . $i] = $val['d' . $i];
						 }
					}

				}
				$wpdb->insert(
					$wpdb->prefix . 'apb_availability', $array_param, $esc_string
				);
			} else {
				/*
				 *  Create array to update pricing
				 */
				$list_update[][ $ex_year_month[1] . '-' . $ex_year_month[0]] = $val;
			}
		}
		if (isset( $list_update ) ) {
			return $list_update;
		} else {
			return array();
		}
	}

	public function list_update_availability( $list_update, $room_id, $start_date, $end_date, $unit_state, $day_option = null, $action, $night = 0) {
		/*
		 * Update availability Start date
		 */

		if ( $action == "start") {
			$key_list_update = array_keys( $list_update[0] );
			$ex_year_month_start = explode('-', $key_list_update[0] );
			$total_day_month_start = date( 't', mktime(0, 0, 0, $ex_year_month_start[1], 1, $ex_year_month_start[0] ) );

			for ( $d = date( "d", strtotime( $start_date ) ); $d <= $total_day_month_start; $d++) {
				$for_date = $ex_year_month_start[0] . '-' . $ex_year_month_start[1] . '-' . $d;

				if (strtotime( $for_date ) >= strtotime( $start_date ) && strtotime( $for_date ) <= strtotime ( '-' .$night. ' day' , strtotime ( $end_date ) ) ) {
					 if ( $day_option != Null) {
						  if (in_array(date( 'l', strtotime(date( $ex_year_month_start[0] . '-' . $ex_year_month_start[1] . '-' . $d ) ) ), $day_option) ) {
							   $esc_string_start[] = '%d';
							   $array_param_start["d" . (int) $d] = $unit_state;
						  }
					 }else{
						 $esc_string_start[] = '%d';
						 $array_param_start["d" . (int) $d] = $unit_state;
					 }

				}
			}
			$this->update_availability(
					wp_kses( $room_id, ""), $ex_year_month_start[0], $ex_year_month_start[1], $array_param_start, $esc_string_start, "start"
			);
		}
		if ( $action == "center") {

			/*
			 * Update available center array
			 */
			for ( $i = 1; $i <= count( $list_update ) - 2; $i++) {
				$key_list_update = array_keys( $list_update[ $i] );
				$ex_year_month = explode('-', $key_list_update[0] );
				for ( $d = 1; $d <= 31; $d++) {
					if ( $day_option != Null) {
						if (in_array(date( 'l', strtotime(date( $ex_year_month[0] . '-' . $ex_year_month[1] . '-' . $d ) ) ), $day_option) ) {
							 $esc_string_center[] = '%d';
							 $array_param_center["d" . (int) $d] = $unit_state;
						}
					} else {
						 $esc_string_start[] = '%d';
						 $array_param_start["d" . (int) $d] = $unit_state;
					}
				}

				$this->update_availability(
					wp_kses( $room_id, ""), $ex_year_month[0], $ex_year_month[1], $array_param_center, $esc_string_center, "center");

			}
		}
		if ( $action == "end") {
			$key_list_update = array_keys( $list_update[count( $list_update ) - 1] );
			$ex_year_month_end = explode('-', $key_list_update[0] );

			for ( $d = 1; $d <= date( "d", strtotime( $end_date ) )-$night; $d++) {
				$for_date = $ex_year_month_end[0] . '-' . $ex_year_month_end[1] . '-' . $d;

				if (strtotime( $for_date ) <= strtotime ( '-' .$night. ' day' , strtotime ( $end_date ) ) ) {
					 if ( $day_option != Null) {
						  if (in_array(date( 'l', strtotime(date( $ex_year_month_end[0] . '-' . $ex_year_month_end[1] . '-' . $d ) ) ), $day_option) ) {
							   $esc_string_end[] = '%d';
							   $array_param_end["d" . (int) $d] = $unit_state;
						  }
					 }else{
						 $esc_string_end[] = '%d';
						 $array_param_end["d" . (int) $d] = $unit_state;
					 }

				}
			}
			$this->update_availability(
					wp_kses( $room_id, ""), $ex_year_month_end[0], $ex_year_month_end[1], $array_param_end, $esc_string_end, "end"
			);
		}
	}

	/**
	 * get_day_for_month_start : Get all day for one month
	 * @param $year int
	 * @param $month int
	 * @param $start_date int
	 * @param $end_date int
	 * @param $room_id int
	 * @param $price int
	 * @param $action status get result
	 * @param $control String
	 */
	public function get_day_for_month_start_avb( $year, $month, $start_date, $end_date, $unit_state = "", $action) {

		if ( $action == 'start' ) {
			for ( $day = 1; $day <= 31; $day++) {
				$for_date = $year . '-' . $month . '-' . $day;
				if (strtotime( $for_date ) >= strtotime( $start_date ) && strtotime( $for_date ) <= strtotime( $end_date ) ) {
					$_days['d' . $day] = $unit_state;
				} else {
					$_days['d' . $day] = "A/V";
				}
			}
		}
		if ( $action == 'default' ) {
			for ( $day = 1; $day <= 31; $day++) {
				$for_date = $year . '-' . $month . '-' . $day;
				$_days['d' . $day] = $unit_state;
			}
		}
		if ( $action == 'end' ) {
			for ( $day = 1; $day <= 31; $day++) {
				$for_date = $year . '-' . $month . '-' . $day;
				if (strtotime( $for_date ) <= strtotime( $end_date ) ) {
					$_days['d' . $day] = $unit_state;
				} else {
					$_days['d' . $day] = "A/V";
				}
			}
		}
		return $_days;
	}

	/**
	 * update_pricing : func update pricing
	 * @param $room_id int
	 * @param $year int
	 * @param $month int
	 * @param $array_param array
	 * @param $esc_string array
	 */
	public function update_availability( $room_id, $year, $month, $array_param, $esc_string) {
		global $wpdb;
		$wpdb->update(
				$wpdb->prefix . 'apb_availability', $array_param, array(
					'unit_id' => $room_id,
					'year'    => $year,
					'month'   => $month
				), $esc_string, array( '%d', '%d', '%d' )
		);
	}

	/**
	 * get_data_avb : Get availability by room
	 * @param $operation string
	 * @param $room_id int
	 * @param $month int
	 * @param $year int
	 * @param $day int
	 * @param $input_price int
	 */
	public function get_data_avb( $operation, $room_id, $month, $year, $day, $input_price ) {
		$room = AWE_function::check_apb_pricing( $year, $month, $room_id );
		if ( ! empty( $room ) ) {
			$price_default = $room[0]->$day;
		} else {
			$price_default = get_post_meta(wp_kses( $room_id, '' ), "base_price", true);
		}
		switch ( $operation) {
			case 'add':
				return $input_price + $price_default;
				break;
			case 'sub':
				return $price_default - $input_price;
				break;
			case 'replace':
				return $input_price;
				break;
			case 'increase':
				return $price_default + $input_price / 100 * $price_default;
				break;
			case 'decrease':
				return $price_default - $input_price / 100 * $price_default;
				break;
			default :
				return $input_price;
		}
	}


	public function get_availability_2_2() {
		$start_month = strtotime( $_POST['date'] . '-01' );
		$end_month = strtotime( '+1 month', $start_month );
		$room_id = absint( $_POST['room_id'] );

		$result = array();

		$query_args = array(
			'post_type'		=> 'apb_order',
			'nopaging'		=> true,
			'post_status'	=> array( 'apb-completed' ),
			'meta_query'	=> array(
				array(
					'key'	=> 'to',
					'value'	=> date( 'm/d/Y', $start_month ),
					'compare'	=> '>=',
				),
				array(
					'key'	=> 'from',
					'value'	=> date( 'm/d/Y', $end_month ),
					'compare'	=> '<',
				),
				array(
					'key'	=> 'order_room_id',
					'value'	=> $room_id,
				),
			),
		);

		$apb_query = new WP_Query( $query_args );

		// if ( ! $apb_query->have_posts() ) {
		// 	wp_send_json( $result );
		// }

		while ( $apb_query->have_posts() ) {
			$apb_query->the_post();

			$start = date( 'Y-m-d', strtotime( get_post_meta( get_the_ID(), 'from', true ) ) );
			$end = date( 'Y-m-d', strtotime( get_post_meta( get_the_ID(), 'to', true ) ) );

			$status = get_post_status();

			switch ( $status ) {
				case 'apb-pending':
					$color = get_option( 'color-pending' );
					$title = get_option( 'label-pending' );
					break;

				case 'apb-completed':
					$color = get_option( 'color-complate' );
					$title = get_option( 'label-complate' );
					break;

				default:
					$color = get_option( 'color-pending' );
					$title = get_option( 'label-pending' );
			}

			$result[] = array(
				'start'	=> $start,
				'end'	=> $end,
				'color'	=> $color,
				'title'	=> $title,
			);
		}
		wp_reset_postdata();

		// Get unavailable events.
		$year_month = explode( '-', $_POST['date'] );
		$data = $this->get_availability_data_in_month( $room_id, $year_month[1], $year_month[0] );
		$end_day = $start_day = null;
		if($data!=NULL){
			for ( $i = 1; $i <= 31; $i++ ) {
				$key = 'd' . $i;
				$prev_key = 'd' . ( $i - 1 );
				
				if ( 1 == $data[ $key ] && ( empty( $data[ $prev_key ] ) || 1 != $data[ $prev_key ] ) ) {
					$start_day = $i;
				}

				if ( 2 == $data[ $key ] && ( empty( $data[ $prev_key ] ) || 2 != $data[ $prev_key ] ) ) {
					$start_day_p = $i;
				}

				if ( 3 == $data[ $key ] && ( empty( $data[ $prev_key ] ) || 3 != $data[ $prev_key ] ) ) {
					$start_day_pending = $i;
				}

				if ( 4 == $data[ $key ] && ( empty( $data[ $prev_key ] ) || 4 != $data[ $prev_key ] ) ) {
					$start_day_c = $i;
				}

				if ( 1 != $data[ $key ] && ! empty( $data[ $prev_key ] ) && 1 == $data[ $prev_key ] || ! checkdate( $year_month[1], $i, $year_month[0] ) || 31 == $i ) {
					$end_day = $i - 1;

					if ( 1 == $data[ $prev_key ] ) {
						$result[] = array(
							'start'		=> $_POST['date'] . '-' . zeroise( $start_day, 2 ),
							'end'		=> date( 'Y-m-d', strtotime( '+1 day', strtotime( $_POST['date'] . '-' . zeroise( $end_day, 2 ) ) ) ),
							'color'		=> get_option( 'color-unavailable' ),
							'title'		=> get_option( 'label-unavailable' ),
						);
					}
				} 

				if ( 2 != $data[ $key ] && ! empty( $data[ $prev_key ] ) && 2 == $data[ $prev_key ]  || 30 == $i ) {
					$end_day = $i - 1;
				
					if ( 2 == $data[ $prev_key ] ) {
						$result[] = array(
							'start'		=> $_POST['date'] . '-' . zeroise( $start_day_p, 2 ),
							'end'		=> date( 'Y-m-d', strtotime( '+1 day', strtotime( $_POST['date'] . '-' . zeroise( $end_day, 2 ) ) ) ),
							'color'		=> get_option( 'color-available' ),
							'title'		=> get_option( 'label-available' ),
						);
					}
				}

				if ( 3 != $data[ $key ] && ! empty( $data[ $prev_key ] ) && 3 == $data[ $prev_key ] || ! checkdate( $year_month[1], $i, $year_month[0] ) || 31 == $i ) {
					$end_day = $i - 1;

					if ( 3 == $data[ $prev_key ] ) {

						$result[] = array(
							'start'		=> $_POST['date'] . '-' . zeroise( $start_day_pending, 2 ),
							'end'		=> date( 'Y-m-d', strtotime( '+1 day', strtotime( $_POST['date'] . '-' . zeroise( $end_day, 2 ) ) ) ),
							'color'		=> get_option( 'color-pending' ),
							'title'		=> get_option( 'label-pending' ),
						);
					}
				}

				if ( 4 != $data[ $key ] && ! empty( $data[ $prev_key ] ) && 4 == $data[ $prev_key ] || ! checkdate( $year_month[1], $i, $year_month[0] ) || 31 == $i ) {
					$end_day = $i - 1;

					if ( 4 == $data[ $prev_key ] ) {

						$result[] = array(
							'start'		=> $_POST['date'] . '-' . zeroise( $start_day_c, 2 ),
							'end'		=> date( 'Y-m-d', strtotime( '+1 day', strtotime( $_POST['date'] . '-' . zeroise( $end_day, 2 ) ) ) ),
							'color'		=> get_option( 'color-complate' ),
							'title'		=> get_option( 'label-complate' ),
						);
					}
				}

			}
		} else {
			$last = strtotime($_POST['date']);
		 	$last_day_this_month  = date('t',$last);

				$result[] = array(
						'start'		=> $_POST['date'] . '-' . zeroise( '01', 2 ),
						'end'		=> date( 'Y-m-d', strtotime( '+1 day', strtotime( $_POST['date'] . '-' . zeroise( $last_day_this_month-1, 2 ) ) ) ),
						'color'		=> get_option( 'color-available' ),
						'title'		=> get_option( 'label-available' ),
					);
		}
		
		wp_send_json( $result );

		die();
	}


	public function get_availability_data_in_month( $room_id, $month, $year ) {
		global $wpdb;

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}apb_availability WHERE unit_id = %d AND year = %d AND month = %d",
				absint( $room_id ),
				absint( $year ),
				absint( $month )
			),
			ARRAY_A
		);

		return $result;
	}


	/**
	 * Get all event by room_id and status key
	 * @param int $room_id - ID Of Room show event
	 * @param char $room_status - Status pending|complete|available
	 * @param int $month - Month current
	 */
	public function Awe_GetAllEvent( $room_id, $room_status, $month) {
		/*----------  Status color and label color  ----------*/
		switch ( $room_status ) {
			/*case 'apb-pending':
				$key_status = 3;
				$name_status = 'apb-pending';
				$avt_stt_color = get_option( 'color-pending' );
				$avt_stt_text = get_option( 'label-pending' );
				break;*/
			case 'apb-completed':
				$key_status = 0;
				$name_status = 'apb-completed';
				$avt_stt_color =  get_option( 'color-complate' );
				$avt_stt_text =  get_option( 'label-complate' );
				break;
			default:
				$key_status = 2;
				$name_status = "apb-available";
				$avt_stt_color = get_option( 'color-available' );
				$avt_stt_text = get_option( 'label-available' );
				break;
		}

		$args = array(
			'post_type' => 'apb_order',
			'post_status' => $name_status,
			'posts_per_page' => -1,
			'meta_query' => array(
				 array(
					'key' => 'order_room_id',
					'value' => $room_id,
				)
			),
		);

		$info_events = get_posts( $args );
		foreach ( $info_events as $item_event) {
			$_shop_order_id = get_post_meta( $item_event->ID, "posttype_shop_order_id", true);

		if (date( 'm', strtotime ( get_post_meta( $item_event->ID,"from",true) ) ) >= $month-1 and date( 'm', strtotime ( get_post_meta( $item_event->ID,"to",true) ) ) <= $month+1) {
			$default_availability[] = array(
				"start"     => date( 'Y-m-d',  strtotime(get_post_meta( $item_event->ID,"from",true) ) ) . "T13:00:00Z",
				"end"       => date( 'Y-m-d', strtotime( '+1 day', strtotime ( get_post_meta( $item_event->ID,"to",true) ) ) ) . "T13:00:00Z",
				"color"     => $avt_stt_color,
				"post_id"   => $item_event->ID,
				"title"     => 'admin - ID #' .$_shop_order_id,
				"key_status" => $key_status,
				"type_show" => $room_status
			);
		 }
		}
		return $default_availability;
	}
	public function get_all_event_by_room_id( $room_id, $date, $key_from, $key_to ) {
		 $_id = array();
		 $_month = date( 'm', strtotime( $date."-1") );
		 $args = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $this->order_status ),
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' => 'order_room_id',
						'value' => wp_kses( $room_id, '' ),
					),
				),
			);

		$get_all_event_item = get_posts( $args );
		foreach ( $get_all_event_item as $item_event) {
			$from = get_post_meta( $item_event->ID, 'from', true );
			$to = get_post_meta( $item_event->ID, 'to', true );
			$month_from = date( 'm', strtotime( $from ) );
			$month_to = date( 'm', strtotime( $to ) );

			$args_from = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $this->order_status ),
				'meta_query' => array(
					array(
						'key' => 'from',
						'value' => date( 'm/d/Y', strtotime( $key_from ) ),
						'compare' => '>='
					),
					array(
						'key' => 'order_room_id',
						'value' => wp_kses( $room_id, '' ),
					)
				),
			);
			$args_to = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $this->order_status ),
				'meta_query' => array(
					array(
						'key' => 'to',
						'value' => date( 'm/d/Y', strtotime( $key_to ) ),
						'compare' => '<='
					),
					array(
						'key' => 'order_room_id',
						'value' => wp_kses( $room_id, '' ),
					)
				),
			);

			 if (strtotime(date( "m/d/Y", strtotime( $key_from ) ) ) == strtotime( $from ) ) {
				if ( $month_from != $month_to ) {
				   foreach (get_posts( $args_from ) as $item ) {
					   if (date( 'm', strtotime(get_post_meta( $item->ID, 'from', true ) ) )  == $_month) {
							 $_id[ $item->ID]  = $item->ID;
						}
				   }
				   $_id[ $item_event->ID]  = $item_event->ID;
				}
			 }
			 if (strtotime(date( "m/d/Y", strtotime( $key_to ) ) ) == strtotime( $to ) ) {
				if ( $month_from != $month_to ) {
					foreach (get_posts( $args_to ) as $item ) {
						if ( date( 'm', strtotime(get_post_meta( $item->ID, 'to', true ) ) )  == $_month) {
							$_id[ $item->ID ]  = $item->ID;
						}
					}
					$_id[ $item_event->ID ]  = $item_event->ID;
				}
			}
		}
		return $_id;
	}

	public function apb_calendar_get_status_by_key( $room_status, $room_id, $date ) {
		$default_availability = array();
		if ( $room_status != 'apb-available'  && $room_status != 'apb-unavailable'  ) {
			switch ( $room_status ) {
				case 'apb-pending':
				   $key_status = 3;
					$name_status = 'apb-pending';
					break;
				case 'apb-completed':
				   $key_status = 0;
					$name_status = 'apb-completed';
					break;
				default:
					 $key_status = 2;
					$name_status = "apb-available";
					 break;
			}


			$args = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $this->order_status ),
				'posts_per_page' => -1,
				'meta_query' => array(
					 array(
						'key' => 'order_room_id',
						'value' => wp_kses( $room_id, '' ),
					)
				),
			);
			$info_events = get_posts( $args );

			foreach ( $info_events as $item_event) {
				$customer_id =  get_post_meta( $item_event->ID,"custommer",true);
				$customer    = get_user_by( 'id', $customer_id );
				$_shop_order_id = get_post_meta( $item_event->ID, "posttype_shop_order_id", true);
				 if ( $name_status == $item_event->post_status ) {
					switch ( $item_event->post_status ) {
						case 'apb-completed';
							$s_color = get_option( 'color-complate' );
							$s_title = get_option( 'label-complate' );
							break;
						case 'apb-pending';
							$s_color = get_option( 'color-pending' );
							$s_title = get_option( 'label-pending' );
							break;
					 }
					 $default_availability[] = array(
						"start"     => date( 'Y-m-d',  strtotime(get_post_meta( $item_event->ID,"from",true) ) ) . "T13:00:00Z",
						"end"       => date( 'Y-m-d', strtotime ( get_post_meta( $item_event->ID,"to",true) ) ) . "T13:00:00Z",
						"color"     => $s_color,
						"post_id"   => $item_event->ID,
						"title"     => ucfirst( $customer->user_login) . ' - ID #' .$_shop_order_id,
						"key_status" =>  $key_status
					);
				 }
			}
		}
		return $default_availability;

	}

	/**
	 * Frontend get calendar.
	 */
	public function apb_frontend_get_availability() {
		$post_date = $_POST['date'] . '-01';
		$month = date( 'm', strtotime( $post_date ) );
		$year = date( 'Y', strtotime( $post_date ) );
		$total_day = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
		$room_type_id = absint( $_POST['room_id'] );
		$info_avb = AWE_function::check_rooms_avb( $year, $month, $room_type_id );

		if ( ! empty( $info_avb ) ) {
			for ( $day = 1; $day <= $total_day + 1; $day++ ) {
				$get_day = 'd' . $day;
				$key_avb[ $day ] = $info_avb[0]->$get_day;
			}

			$pricingGetListSameValue = $this->PricingGetListSameValue( $key_avb );
			foreach ( $pricingGetListSameValue as $key => $val) {
				foreach ( $val as $item ) {
				   /*----------  Get day by status  ----------*/
					$day_start = date( 'Y-m-d', strtotime(date( $_POST['date'].'-'.$item[0] ) ) );
					$day_end =  date( 'Y-m-d', strtotime( '+1 day', strtotime(date( $_POST['date'].'-'.$item[count( $item ) - 1] ) ) ) );

					switch ( $key) {
						 case 0:
							$avt_stt_color =  get_option( 'color-unavailable' );
							$avt_stt_text =  get_option( 'label-unavailable' );
							$key_id = 1;
							break;
						case 1:
							$avt_stt_color =  get_option( 'color-unavailable' );
							$avt_stt_text =  get_option( 'label-unavailable' );
							$key_id = 1;
							break;
						case 2:
							$avt_stt_color = get_option( 'color-available' );
							$avt_stt_text = get_option( 'label-available' );
							$key_id = 2;
							break;
						case 3:
							$avt_stt_color = get_option( 'color-pending' );
							$avt_stt_text = get_option( 'label-pending' );
							$key_id = 2;
							break;
					}

					$default_availability[] = array(
						"start" => $day_start . "T13:00:00Z",
						"end"   => $day_end . "T13:00:00Z",
						"color" => $avt_stt_color,
						"title" => $avt_stt_text,
						"id" => $key_id
					);
				}
			}

		} else {
			$default_availability = array(
				array(
					"start" => $post_date . "T13:00:00Z",
					"end"   => $_POST['date'] . '-' . $total_day . "T13:00:00Z",
					"color" => get_option( 'color-available' ),
					"title" => get_option( 'label-available' ),
					"id" => 2
				)
			);
		}

		echo json_encode( $default_availability);
		die;
		return false;
	}


	public function get_info_event() {

		if ( isset( $_POST['book_id'] ) && ! empty( $_POST['book_id'] ) ) {
			if ( is_array( $_POST['book_id'] ) ) {
				$args_event = array(
					'post_type' => 'apb_order',
					'post__in'  => array_keys( $_POST['book_id'] ),
					'post_status' => array_keys( $this->order_status ),
				);
				$_id = array();
				$all_event_by_month = get_posts( $args_event );

				$_get_from = strtotime( date( 'm/d/Y', $_POST['from'] ) );
				$_get_to = strtotime( date( 'm/d/Y', $_POST['to'] ) );
				foreach ( $all_event_by_month as $item_event) {
					$from = get_post_meta( $item_event->ID, 'from', true );
					$to = get_post_meta( $item_event->ID, 'to', true );

					$_from = strtotime( date( 'm/d/Y', strtotime( $from ) ) ) ."<br/>";
					$_to = strtotime( date( 'm/d/Y', strtotime( $to ) ) );
					if ( $_POST['calendar'] == 1) {
						if ( $_from >= $_get_from ) {
							$_id[ $item_event->ID] = $item_event->ID;
						}
					}
					if ( $_POST['calendar'] == 2) {
						if ( $_to <= $_get_to ) {
							$_id[ $item_event->ID] = $item_event->ID;
						}
					}

				}
				$args = array(
					'post_type' => 'apb_order',
					'post__in'  => array_keys( $_id ),
					'post_status' => array_keys( $this->order_status ),
				);
			} else {
				$args = array(
					'post_type' => 'apb_order',
					'post__in'  => array( $_POST['book_id'] ),
					'post_status' => array_keys( $this->order_status ),
				);
			}
		} else {
			$array_status = $this->order_status;
			unset( $array_status['apb-cancelled'] );
			$start_month = date( 'm/01/Y', $_POST['from'] );
			$end_month = date( 'm/31/Y', $_POST['from'] );
			$args = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $array_status ),
				'meta_query' => array(
					'relation' => 'AND', 
					array(
					'relation' => 'BETWEEN', 
						array(
							'key' => 'from',
							'value' => date( 'm/d/Y', $_POST['from'] ),
							'compare' => '>='
						),

							
						array(
							'key' => 'from',
							'value' => date( 'm/d/Y', $_POST['to'] ),
							'compare' => '<='
						),
					),
					array(
						'key' => 'order_room_id',
						'value' => wp_kses( $_POST['room_id'], '' ),
					),
				),
			);
		}
	
		$info_events = get_posts( $args );
		if( empty($info_events) ) {
			$args = array(
				'post_type' => 'apb_order',
				'post_status' => array_keys( $array_status ),
				'meta_query' => array(
					'relation' => 'AND', 
					array(
					'relation' => 'BETWEEN', 
						array(
							'key' => 'to',
							'value' => date( 'm/d/Y', $_POST['from'] ),
							'compare' => '>='
						),
					
						array(
							'key' => 'to',
							'value' => date( 'm/d/Y', $_POST['to'] ),
							'compare' => '<='
						),
					),
					array(
						'key' => 'order_room_id',
						'value' => wp_kses( $_POST['room_id'], '' ),
					),
				),
			);
			$info_events = get_posts( $args );
		}
		// var_dump( $args );
		if ( ! empty( $info_events ) ) {
			$html = '';
			foreach ( $info_events as $info_event) {
				$from = get_post_meta( $info_event->ID, 'from', true );
				$to = get_post_meta( $info_event->ID, 'to', true );
				$room_id = get_post_meta( $info_event->ID, 'order_room_id', true );
				$room = get_post( $room_id );
				$custommer = get_userdata( get_post_meta( $info_event->ID, 'custommer', true ) );
				$shop_order_id = get_post_meta( $info_event->ID, 'posttype_shop_order_id', true );
				$custom_ = isset( $custommer->user_login ) ? $custommer->user_login : 'Guest';

				$html .= '<div class="item-order-' . esc_attr( $shop_order_id ) . '"><a href="' . esc_url( admin_url( 'post.php?post=' . absint( $shop_order_id ) . '&action=edit' ) ) . '"><h3>Order ID #' . esc_html( $shop_order_id ) . '</h3></a> - <a class="room-delete-order" data-id="' . esc_attr( $shop_order_id ) . '" href="#">' . esc_html__( 'Move to trash', 'awebooking' ) . ' </a>';
				$html .= '<p><strong>' . esc_html__( 'Customer:', 'awebooking' ) . '</strong> ' . esc_html( $custom_ ) . '</p>';
				$html .= '<p><strong>' . esc_html__( 'From:', 'awebooking' ) . '</strong> ' . date( 'm/d/Y', strtotime( $from ) ) . '</p>';
				$html .= '<p><strong>' . esc_html__( 'To:', 'awebooking' ) . '</strong> ' . date( 'm/d/Y', strtotime( $to ) ) . '</p>';
				$html .= '<p><strong>' . esc_html__( 'Room:', 'awebooking' ) . '</strong> <span class="room_translate_name">' . esc_html( $room->post_title ) . '</span></p>';
				$html .= '<p><strong>' . esc_html__( 'Status order:', 'awebooking' ) . '</strong> ' . esc_html( $this->order_status[ get_post_status( $info_event->ID ) ] ) . '</p></div>';

			}
		} else {
			$html = '<h3>' . esc_html( 'Booking not exist!', 'awebooking' ) . '</h3>';
		}

		echo $html;
		die;
		return false;
	}


	public function user_room_select() {
		if ( 'callback' != $_POST['control'] ) {
			AWE_function::delete_cart();
		}

		ob_start();
		/**
		 * @hooked layout_room_select.
		 */
		do_action( 'layout_room_select' );
		$html = ob_get_clean();

		echo $html;

		die();
		return false;
	}

	public function apb_before_add_cart() {
		$room_info      = get_post(wp_kses( $_POST['room_id'], '' ) );
		$max_sleeps     = get_post_meta( $room_info->ID, "max_sleeps", true);
		$min_sleeps     = get_post_meta( $room_info->ID, "min_sleeps", true);
		$max_children   = get_post_meta( $room_info->ID, "max_children", true);
		$min_children   = get_post_meta( $room_info->ID, "min_children", true);

		$key = substr(md5(rand(0, 9) ), 0, 5);
		$num = $_POST['num_people'];
		$html = '';
		if ( $_POST['room_adult'][ $num]['adult'] + $_POST['room_child'][ $num]['child'] <= $max_sleeps and $_POST['room_adult'][ $num]['adult'] + $_POST['room_child'][ $num]['child'] >= $min_sleeps ) {
			if ( '' === $max_children && '' === $min_children || $_POST['room_child'][ $num ]['child'] <= $max_children && $_POST['room_child'][ $num ]['child'] >= $min_children ) {
				$html .= 'yes';
			} else {
				$html .= '<div class="panel panel-warning">
						<div class="panel-heading">
						  <h3 id="panel-title" class="panel-title">' . esc_html__( 'Check Availability Warning', 'awebooking' ) . ' !
							  <a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a>
						  </h3>
						</div>
						<div class="panel-body">
						<p><h2> ' . esc_html__( 'Room', 'awebooking' ) . ' ' . esc_html( $room_info->post_title) . '</h2></p>
						<p>' . esc_html__( 'Person minimum', 'awebooking' ) . ' : ' . esc_html( $min_sleeps ) . '</p>
						<p>' . esc_html__( 'Person maximum', 'awebooking' ) . ' : ' . esc_html( $max_sleeps ) . '</p>
						<p>' . esc_html__( 'Child minimum', 'awebooking' ) . ' : ' . esc_html( $min_children) . '</p>
						<p>' . esc_html__( 'Child maximum', 'awebooking' ) . ' : ' . esc_html( $max_children) . '</p>
						<p>' . esc_html__( 'Please select an Adult and Child', 'awebooking' ) . '</p>
						<p>' . esc_html__( 'The total number of guests (adults and children) allowed in this unit', 'awebooking' ) . '</p>
						</div>
					</div>';
			}
		} else {
			$html .= '<div class="panel panel-warning">
						<div class="panel-heading">
						  <h3 id="panel-title" class="panel-title">' . esc_html__( 'Check Availability Warning', 'awebooking' ) . ' !
							  <a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a>
						  </h3>
						</div>
						<div class="panel-body">
						<p><h2> ' . esc_html__( 'Room', 'awebooking' ) . ' ' . esc_html( $room_info->post_title) . '</h2></p>
						<p>' . esc_html__( 'Person minimum', 'awebooking' ) . ' : ' . esc_html( $min_sleeps ) . '</p>
						<p>' . esc_html__( 'Person maximum', 'awebooking' ) . ' : ' . esc_html( $max_sleeps ) . '</p>
						<p>' . esc_html__( 'Child minimum', 'awebooking' ) . ' : ' . esc_html( $min_children) . '</p>
						<p>' . esc_html__( 'Child maximum', 'awebooking' ) . ' : ' . esc_html( $max_children) . '</p>
						<p>' . esc_html__( 'Please select an Adult and Child', 'awebooking' ) . '</p>
						<p>' . esc_html__( 'The total number of guests (adults and children) allowed in this unit', 'awebooking' ) . '</p>
						</div>
					</div>';
		}
		echo json_encode( $html);
		die;
		return false;
	}

	public function full_select_room() {
		ob_start();

		/**
		 * @hooked layout_review_book.
		 */
		do_action( 'apb_layout_review_book' );
		$html = ob_get_clean();

		echo $html;
		die;
		return false;
	}

	public function add_to_order() {
		if ( ! isset( $_POST['room_id'] ) ) {
			die();
		}

		$room_ids = $_POST['room_id'];
		$room_ids = array_map( 'absint', $room_ids );
		$order_data['post_type']     = 'shop_order';
		$order_data['post_status']   = 'apb-pending';
		$order_data['ping_status']   = 'closed';
		$order_data['post_author']   = 1;
		$order_data['post_password'] = uniqid( 'order_' );
		$order_data['post_title']    = sprintf( __( 'Order &ndash; %s', 'awebooking' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'awebooking' ) ) );
		$order_id = wp_insert_post( $order_data );

		$customer_id = AWE_function::maybe_create_customer( $_POST['info_custom']['apb-name'], $_POST['info_custom']['apb-email'] );

		$total_price = 0;
		for ( $i = 0; $i <= count( $room_ids ) - 1; $i++ ) {
			$from = $_POST['from'][ $i ];
			$to = $_POST['to'][ $i ];
			$room_id = $room_ids[ $i ];

			$room_id = AWE_function::get_master_post_id( $room_id );

			/**
			 * Add new order.
			 */
			$order_data['post_type']     = 'apb_order';
			$order_data['post_status']   = 'apb-pending';
			$order_data['ping_status']   = 'closed';
			$order_data['post_author']   = 1;
			$order_data['post_password'] = uniqid( 'order_' );
			$order_data['post_title']    = '#' . $order_id;
			$id = wp_insert_post( $order_data );
			$apb_order_id[] = $id;

			$package_data = ( string ) $_POST['packages'][ $i ];
			$args_order[] = apply_filters( 'apb_add_to_order_data', array(
				'id'              => $id,
				'custommer'       => get_current_user_id(),
				'room_adult'      => $_POST['room_adult'][ $i ],
				'room_child'      => $_POST['room_child'][ $i ],
				'from'            => $from,
				'to'              => $to,
				'order_room_id'   => $room_id,
				'package_data'    => $_POST['packages'][ $i ],
				'total_price'     => $_POST['price'][ $i ],
				'search_by'       => 'apb_room_type',
			), $_POST );

			$total_price += $_POST['price'][ $i ];

			/**
			 * Add meta box.
			 */
			update_post_meta( $id, 'custommer', $customer_id );
			update_post_meta( $id, 'room_adult', $_POST['room_adult'][ $i ] );
			update_post_meta( $id, 'room_child', $_POST['room_child'][ $i ] );
			update_post_meta( $id, 'from', $from );
			update_post_meta( $id, 'to', $to );
			update_post_meta( $id, 'order_room_id', $room_id, '' );
			update_post_meta( $id, 'info_custom_order', $_POST['info_custom'], '' );
			update_post_meta( $id, 'package_data', $_POST['packages'][ $i ], '' );
			update_post_meta( $id, 'posttype_shop_order_id', $order_id, '' );

			AWE_function::update_available( $from, $to, $room_id, 3 );

			/**
			 * Hook apb_created_order_item
			 */
			do_action( 'apb_created_order_item', $id, $room_id, $_POST, $i );
		}

		update_post_meta( $order_id, 'info_custom_order', wp_kses( $_POST['info_custom'], '' ) );
		update_post_meta( $order_id, 'custommer', $customer_id );
		update_post_meta( $order_id, 'from', $args_order[0]['from'] );
		update_post_meta( $order_id, 'to', $args_order[0]['to'] );
		update_post_meta( $order_id, 'apb_data_order', $args_order );
		update_post_meta( $order_id, 'total_ex_tax', $total_price );
		update_post_meta( $order_id, 'tax_amount', get_option( 'apb_tax_amount' ) );
		update_post_meta( $order_id, 'tax_type', get_option( 'apb_tax_type' ) );
		update_post_meta( $order_id, '_order_total', AWE_function::calculate_tax( $total_price ) );
		update_post_meta( $order_id, 'apb_order_id', $apb_order_id );
		update_post_meta( $order_id, 'order_type', 2 );
		update_post_meta( $order_id, '_pre_payment_type', get_option( 'apb_deposit_type' ) );
		update_post_meta( $order_id, '_deposit_payment', get_option( 'apb_deposit_number' ) );

		/**
		 * Hook apb_created_order
		 */
		do_action( 'apb_created_order', $order_id, $_POST );

		/*----------  Send mail after checkout  ----------*/
		$config_mail = AWE_function::get_option( 'apb_mail_new_booking' );
		$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : '[{site_title}] New customer booking ({order_number}) - {order_date}';
		$subject = AWE_function::email_str_replace( $subject, $order_id );
		$heading = ! empty( $config_mail['header'] ) ? $config_mail['header'] : 'New customer booking';
		$heading = AWE_function::email_str_replace( $heading, $order_id );

		ob_start();
		do_action( 'apb_mail_new_booking', $order_id );
		$message       = ob_get_clean();

		$email         = new APB_Email();

		if ( isset( $config_mail['admin_notice_status'] ) && 1 == $config_mail['admin_notice_status'] ) {
			$mail_admin = ! empty( $config_mail['admin_mail_multi'] ) ? explode( ',', $config_mail['admin_mail_multi'] ) : (array) get_option( 'admin_email' );
			foreach ( $mail_admin as $email_admin ) {
				$message_admin = '<h2>' . __( 'Orders Info', 'awebooking' ) . '</h2>';
				$message_admin .= '<table>';

				foreach ( $_POST['info_custom'] as $key => $value ) {
					if ( 'apb-email' == $key ) {
						$key = __( 'Email', 'awebooking' );
					} elseif ( 'apb-name' == $key ) {
						$key = __( 'Name', 'awebooking' );
					}

					$message_admin .= "<tr>
						<td>" . str_replace( 'apb-', '',  ucwords( $key ) ) ."</td>
						<td>{$value}</td>
					  </tr>";
				}
				$message_admin .= '</table>';
				$message_admin .= $message;
				$message_admin = $email->apb_style_inline( $email->apb_wrap_message( $heading, $message_admin) );
				$email->apb_sendMail( $email_admin, $subject, $message_admin, 1 );
			}
			$email->destroy();

		}
		$email->destroy();

		/* Send email pending */
		$config_mail = AWE_function::get_option( 'apb_mail_pending' );
		$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : 'Your {site_title} booking receipt from {order_date}';
		$subject = AWE_function::email_str_replace( $subject, $order_id );
		$heading = ! empty( $config_mail['header'] ) ? $config_mail['header'] : 'Thank you for your booking';
		$heading = AWE_function::email_str_replace( $heading, $order_id );

		ob_start();
		do_action( 'apb_mail_pending_order', $order_id );
		$message       = ob_get_clean();
		$email         = new APB_Email();

		if ( isset( $config_mail['user_notice_status'] ) && 1 == $config_mail['user_notice_status'] && isset( $_POST['info_custom']['apb-email'] ) ) {
			$info_customer = $_POST['info_custom'];
			$message_user = $email->apb_style_inline( $email->apb_wrap_message( $heading, $message ) );
			$email->apb_sendMail( $info_customer['apb-email'], $subject, $message_user, 0, $info_customer['apb-name'] );
		}
		$email->destroy();

		// Unset session cart.
		AWE_function::delete_cart();

		die;
		return false;
	}

	/**
	 * Add Booking by single available manage of room
	 */
	public function Apb_add_booking_single_available_manage() {
		$from = $_POST['from'];
		$to = $_POST['to'];
		$room_id = absint( $_POST['room_id'] );
		$check_available = AWE_function::check_room_available( $from, $to, $room_id, true );
		if ( ! empty( $check_available ) ) {

			$room_id = absint( $_POST['room_id'] );
			$data_option    = AWE_function::get_room_option( $check_available[0]->unit_id, 'apb_room_type' );
			$total_day      = ( count( AWE_function::range_date( $from, $to ) ) - 1 == 0 ) ? 1 : count( AWE_function::range_date( $from, $to ) );
			$room_price     = get_post_meta( $room_id, 'base_price', true );
			$room = get_post( $room_id );

			$total_price = $total_day * $room_price;
			$extra_sale   = get_post_meta( $room_id, 'extra_sale', true );
			$data_extra_sale = AWE_function::apb_get_extra_sale( $extra_sale, $total_day, $from );
			if ( ! empty( $data_extra_sale ) ) {
				if ( 'sub' == $data_extra_sale['sale_type'] ) {
					$total_price = $total_price - $data_extra_sale['amount'];
				}
				if ( 'decrease' == $data_extra_sale['sale_type'] ) {
					 $total_price = $total_price - $data_extra_sale['amount'] / 100 * $total_price;
				}
				// $sale_info = $data_extra_sale;
			}

			$html = '';
			$html .= '<div class="form-item">
						<div class="form-elements ">
							<label>' . esc_html__( 'Package of', 'awebooking' ) . ' ' . esc_html( $room->post_title ) . '</label>';

			foreach ( $data_option as $item ) {
				$package_name = $item->option_name;
				$price_package = $item->option_value;
				$package_operation = $item->option_operation;
				$html .= "<div>"
						. "<input id='package-".$item->id."' name='package_id[]' class='options_operation_js package-". esc_attr( $item->id ) ."' type='checkbox' data-operation='" . esc_attr( $package_operation) . "' data-value='" . esc_attr( $price_package) . "' value='" . esc_attr( $item->id ) . "' >"
						."<label for='package-". esc_attr( $item->id ) ."'>" . esc_html( $package_name) . "</label>"
						."<input data-id='". esc_attr( $item->id ) ."' class='package_total_js package-num-". esc_attr( $item->id ) ."' data-value='" . esc_attr( $price_package) . "' name='package_total[]' type='number' min='1' value='1'>";

			}
			$html .= '</div></div>
						<div class="form-item">
						   <label>' . esc_html__( 'Price', 'awebooking' ) . '</label>
						   <input type="text" readonly class="form-text total_price_js" data-default="' . ( float ) $total_price . '" size="10" value="' . ( float ) $total_price . '" name="room_price">
						</div>';
			echo json_encode( $html );

		} else {
			$check_available_isset = AWE_function::check_room_available( $from, $to, $room_id, false );
			if ( empty( $check_available_isset ) ) {
				$data_option    = AWE_function::get_room_option( $room_id, 'apb_room_type' );
				$total_day      = ( count( AWE_function::range_date( $from, $to ) ) - 1 == 0 ) ? 1 : count( AWE_function::range_date( $from, $to ) );
				$room_price     = get_post_meta( $room_id, 'base_price', true );
				$room = get_post( $room_id );

				$total_price = $total_day * $room_price;
				$extra_sale   = get_post_meta( $room_id, 'extra_sale', true );
				if ( ! empty( $extra_sale ) ) {
					$data_extra_sale = AWE_function::apb_get_extra_sale( $extra_sale, $total_day, $from );
					if ( ! empty( $data_extra_sale ) ) {
						if ( 'sub' == $data_extra_sale['sale_type'] ) {
							$total_price = $total_price - $data_extra_sale['amount'];
						}
						if ( 'decrease' == $data_extra_sale['sale_type'] ) {
							 $total_price = $total_price - $data_extra_sale['amount'] / 100 * $total_price;
						}
					}
				}

				$html = '';
				$html .= '<div class="form-item"><div class="form-elements ">';
				$html .= sprintf( '<label>' . esc_html__( 'Package of %s', 'awebooking' ) . '</label>', esc_html( $room->post_title ) );

				foreach ( $data_option as $item ) {
					$package_name = $item->option_name;
					$price_package = $item->option_value;
					$package_operation = $item->option_operation;
					$html .= '<div>'
						. "<input id='package-".$item->id."' name='package_id[]' class='options_operation_js package-". esc_attr( $item->id ) ."' type='checkbox' data-operation='" . esc_attr( $package_operation) . "' data-value='" . esc_attr( $price_package) . "' value='" . esc_attr( $item->id ) . "' >"
							."<label for='package-". esc_attr( $item->id ) ."'>" . esc_html( $package_name) . "</label>"
							."<input data-id='". esc_attr( $item->id ) ."' class='package_total_js package-num-". esc_attr( $item->id ) ."' data-value='" . esc_attr( $price_package) . "' name='package_total[]' type='number' min='1' value='1'>";

				}
				$html .= '</div>
							</div>
							<div class="form-item">
							   <label>' . esc_html__( 'Price', 'awebooking' ) . '</label>
							   <input type="text" readonly class="form-text total_price_js" data-default="' . ( float ) $total_price . '"  size="10" value="' . ( float ) $total_price . '" name="room_price">
							</div>';
				echo json_encode( $html );
			} else {
				echo json_encode( 'no' );
			}

		}
		die;
		return false;
	}

	/**
	 * Get total cart info.
	 */
	public function info_cart_ckeckout() {
		ob_start();
		include AWE_function::template_exsits( 'layout/cart_info' );
		$html = ob_get_clean();
		echo json_encode( $html );
		die;
		return false;
	}

	/**
	 * Get Form checkout.
	 */
	public function apb_ckeckout_form() {
		ob_start();
		include AWE_function::template_exsits( 'apb-checkout' );
		$html = ob_get_clean();
		echo json_encode( $html );
		die;
		return false;
	}


	/**
	 * Check available.
	 *
	 * @return void
	 */
	public function check_available() {
		if ( empty( $_POST['from'] ) || empty( $_POST['to'] ) || empty( $_POST['adult'] ) ) {
			die();
		}

		$from = wp_kses_post( wp_unslash( AWE_function::convert_date_to_mdY( $_POST['from'] ) ) );
		$to = wp_kses_post( wp_unslash( AWE_function::convert_date_to_mdY( $_POST['to'] ) ) );

		$number_nights = AWE_function::get_number_of_nights( $from, $to );
		if ( $number_nights > AWE_function::get_max_night() ) {
			printf(
				esc_html__( 'Max nights to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_night() )
			);
			die();
		}

		$adult = absint( $_POST['adult'] );
		if ( $adult > AWE_function::get_max_adult() ) {
			printf(
				esc_html__( 'Max adult to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_adult() )
			);
			die();
		}

		$child = absint( $_POST['child'] );
		if ( $child > AWE_function::get_max_child() ) {
			printf(
				esc_html__( 'Max child to check is %s', 'awebooking' ),
				absint( AWE_function::get_max_child() )
			);
			die();
		}

		$cart_index = isset( $_POST['cart_index'] ) ? intval( $_POST['cart_index'] ) : -1;
		$room_type_id = isset( $_POST['room_type_id'] ) ? absint( $_POST['room_type_id'] ) : null;

		if ( $room_type_id ) {
			$result = AWE_function::single_check_available( $from, $to, $adult, $child, $room_type_id, $_POST );

			if ( 'guest' == $result ) {
				do_action( 'apb_unavailable_message_guest', $room_type_id );

				die();
			}

			if ( 'min-night' == $result ) {
				do_action( 'apb_unavailable_message_guest', $room_type_id );

				die();
			}

			if ( 'unavailable' == $result ) {
				do_action( 'apb_unavailable_message_unavailable', $room_type_id );

				die();
			}

			$apb_query = new WP_Query( 'post_type=apb_room_type&p=' . $room_type_id );
			if ( $apb_query->have_posts() ) {
				while ( $apb_query->have_posts() ) {
					$apb_query->the_post();

					$room_price     = get_post_meta( $room_type_id, 'base_price', true );
					$room_desc      = get_post_meta( $room_type_id, 'room_desc', true );
					$extra_sale     = get_post_meta( $room_type_id, 'extra_sale', true );
					$info_price_day = AWE_function::get_pricing_of_days( $from, $to, $room_type_id, 1 );

					$base_price_for = get_post_meta( $room_type_id, 'base_price_for', true );
					$extra_guess_data = AWE_function::get_extra_guest_data( $base_price_for, $adult, $child );
					$extra_price_data = AWE_function::get_extra_price_data( $room_type_id, $adult, $child );

					$total_price = AWE_function::calculate_total_price( $room_type_id, $from, $to, $adult, $child );

					$avg_price = $total_price / $number_nights;

					$remain = AWE_function::get_remaining_count( $room_type_id, $from, $to );

					include AWE_function::template_exsits( 'loop/apb-content-check-available-all-room' );
				}
			}

			die();
		} else {
			$room_type = AWE_function::check_available( $from, $to, $adult, $child, $room_type_id, $_POST );

			if ( ! empty( $room_type ) ) {
				$room_type_id = array();
				foreach ( $room_type as $k => $v ) {
					$room_type_id[] = $k;
				}

				$apb_query = new WP_Query( apply_filters( 'apb_check_available_results_query_args', array(
					'post__in'          => $room_type_id,
					'nopaging'          => 1,
					'post_type'         => 'apb_room_type',
				) ) );
				if ( $apb_query->have_posts() ) {
					while ( $apb_query->have_posts() ) : $apb_query->the_post();

						$room_price     = get_post_meta( get_the_ID(), 'base_price', true );
						$room_desc      = get_post_meta( get_the_ID(), 'room_desc', true );
						$extra_sale     = get_post_meta( get_the_ID(), 'extra_sale', true );
						$info_price_day = AWE_function::get_pricing_of_days( $from, $to, get_the_ID(), 1 );

						$base_price_for = get_post_meta( get_the_ID(), 'base_price_for', true );
						$extra_guess_data = AWE_function::get_extra_guest_data( $base_price_for, $adult, $child );
						$extra_price_data = AWE_function::get_extra_price_data( get_the_ID(), $adult, $child );

						$total_price = AWE_function::calculate_total_price( get_the_ID(), $from, $to, $adult, $child );

						$avg_price = $total_price / $number_nights;
						
						$remain = isset( $room_type[ get_the_ID() ] ) ? absint( $room_type[ get_the_ID() ] ) : 0;

						include AWE_function::template_exsits( 'loop/apb-content-check-available-all-room' );
					endwhile;
					wp_reset_postdata();
				} else {
					do_action( 'apb_notice_available' );
				}
			} else {
				do_action( 'apb_notice_available' );
			}
		}

		die();
	}


	/**
	 * Add room to cart.
	 *
	 * @since 2.0
	 */
	public function add_room_to_cart() {
		global $wpdb, $sitepress;
		if ( empty( $_POST['room_type_id'] ) || empty( $_POST['price'] ) || empty( $_POST['from'] ) || empty( $_POST['to'] ) || empty( $_POST['adult'] ) ) {
			die();
		}
		
		$room_type_id = absint( $_POST['room_type_id'] );
		if( AWE_function::activated_wpml() ){
			$default_lang = $sitepress->get_default_language();
			$results = $wpdb->get_results( 'SELECT trid FROM '.$wpdb->prefix.'icl_translations WHERE element_id = ' . $room_type_id, OBJECT );
			$trid = $results[0]->trid;

			$results = $wpdb->get_results( 'SELECT element_id FROM '.$wpdb->prefix.'icl_translations WHERE trid = "' .  $trid . '" and language_code="' . $default_lang . '"', OBJECT );

			if( null == $results ) {
				$room_type_id = $room_type_id;
			} else {
				$room_type_id = $results[0]->element_id;
			}
		} else {
				$room_type_id = $room_type_id;
		}

		
		$price = ( float ) $_POST['price'];
		$from = wp_kses_post( wp_unslash( $_POST['from'] ) );
		$to = wp_kses_post( wp_unslash( $_POST['to'] ) );
		$adult = absint( $_POST['adult'] );
		$child = absint( $_POST['child'] );
		$package_data = ! empty( $_POST['package_data'] ) ? $_POST['package_data'] : array();
		$cart_index = isset( $_POST['cart_index'] ) ? intval( $_POST['cart_index'] ) : -1;

		$room_id = AWE_function::get_room_available_from_room_type( $room_type_id, $from, $to );
		
		
		if ( ! $room_id ) {
			wp_send_json_error( esc_html__( 'Not found any room available in this room type', 'awebooking' ) );
		}

		$sale_info = null;
		$extra_sale = get_post_meta( $room_type_id, 'extra_sale', true );
		$data_extra_sale = null;
		if ( ! empty( $extra_sale ) ) {
			$total_night = AWE_function::get_number_of_nights( $from, $to );
			$data_extra_sale = AWE_function::apb_get_extra_sale( $extra_sale, $total_night + 1, $from );
		}

		if ( ! AWE_function::add_room_to_cart( $room_id, $from, $to, $adult, $child, $price, $data_extra_sale, $package_data, $cart_index, $_POST ) ) {
			wp_send_json_error( esc_html__( 'Can not add room to cart', 'awebooking' ) );
		}

		wp_send_json_success( array(
			'message'	=> sprintf( esc_html__( 'Added room %s to cart successfully', 'awebooking' ), get_post( $room_id )->post_title ),
			'room_id'	=> $room_id,
		) );

		die();
	}


	/**
	 * Update room select layout.
	 *
	 * @return void
	 * @since 2.0
	 */
	public function update_layout_room_select() {
		do_action( 'layout_room_select' );
		die();
	}


	/**
	 * Remove cart.
	 * @return void
	 */
	public function remove_cart() {
		AWE_function::delete_cart();
		die();
	}


	/**
	 * Get changed layout in selected room.
	 *
	 * @return void
	 * @since 2.0
	 */
	public function change_selected_item_layout() {
		$room_id = absint( $_POST['room_id'] );
		$adult = absint( $_POST['adult'] );
		$child = absint( $_POST['child'] );
		$index = absint( $_POST['index'] );
		$disable = isset( $_POST['disable'] ) ? intval( $_POST['disable'] ) : 0;

		if ( 0 == $disable ) {
			?>
			<div class="apb-room-seleted_current apb-bg_blue" data-type="1" data-adult="<?php echo absint( $adult ) ?>" data-child="<?php echo absint( $child ) ?>">
				<h6><?php printf( esc_html__( 'You are booking room %s', 'awebooking' ), absint( $index + 1 ) ); ?></h6>
				<span>
					<?php
					printf( esc_html__( '%s Adult', 'awebooking' ), absint( $adult ) );
					echo ', ';
					printf( esc_html__( '%s Child', 'awebooking' ), absint( $child ) );
					?>
				</span>
			</div>
			<?php
		} elseif ( 1 == $disable ) {
			?>
			<div class="apb-room-selected_item apb_disable" data-adult="<?php echo absint( $adult ) ?>" data-child="<?php echo absint( $child ) ?>">
				<h6><?php printf( esc_html__( 'Room %s', 'awebooking' ), absint( $index + 1 ) ); ?></h6>
				<span class="apb-option">
					<?php
					printf( esc_html__( '%s Adult', 'awebooking' ), absint( $adult ) );
					echo ', ';
					printf( esc_html__( '%s Child', 'awebooking' ), absint( $child ) );
					?>
				</span>
			</div>
			<?php
		} elseif ( -1 == $disable ) {
			$cart = AWE_function::get_cart();
			// var_dump($cart);
			$item_cart = $cart[ $room_id ];
			$room = get_post( $room_id );
			$room_type_id = wp_get_post_parent_id( $room_id );
			$room_type = get_post( $room_type_id );
			?>
			<div data-id="<?php echo absint( $room_id ); ?>" data-child="<?php echo absint( $child ) ?>" data-adult="<?php echo absint( $adult ) ?>" class="check-list-cart-js apb-room-selected_item box-cart-item-<?php echo absint( $room_id ); ?>">
			<h6><?php printf( esc_html__( 'Room %s', 'awebooking' ), absint( $index + 1 ) ); ?></h6>
				<div class="apb-room-seleted_name">
					<h2><?php echo esc_html( $room_type->post_title ); ?></h2>
					<span class="apb-amount">
						<?php echo AWE_function::apb_price( $item_cart['price'] ); ?>
					</span>
					<?php
					printf(
						'<input type="hidden" class="price-day-js" data-id="%d" value="%d">',
						$item_cart['room_id'],
						$item_cart['price']
					);
					?>
				</div>

				<span>
					<?php
					printf( esc_html__( '%s Adult', 'awebooking' ), absint( $item_cart['adult'] ) );
					echo ', ';
					printf( esc_html__( '%s Child', 'awebooking' ), absint( $item_cart['child'] ) );
					?>
				</span>

				<?php
				printf(
					'<a class="apb-room-seleted_change %s" data-num="%d" data-key="%s" data-id="%d" href="#">%s</a>',
					'change-item-cart-js',
					absint( $index ),
					absint( $room_id ),
					absint( $room_id ),
					esc_html__( 'Change Room', 'awebooking' )
				);
				?>
			</div>
			<?php
		}

		die();
	}


	/**
	 * Remove room from cart.
	 *
	 * @return void
	 * @since 2.0
	 */
	public function remove_room_from_cart() {
		if ( ! isset( $_POST['room_id'] ) ) {
			die();
		}

		AWE_function::remove_room_from_cart( absint( $_POST['room_id'] ) );
		die();
	}


	/**
	 * Get room type availability color.
	 *
	 * @return void
	 * @since 2.0
	 */
	public function get_room_type_availability_color() {
		if ( empty( $_POST['room_id'] ) || empty( $_POST['date'] ) ) {
			die();
		}

		global $wpdb;
		$room_type_id = absint( $_POST['room_id'] );
		$date = sanitize_text_field( wp_unslash( $_POST['date'] ) );
		$rooms = AWE_function::get_rooms_of_room_type( $room_type_id );
		$where_room = implode( ',', array_keys( $rooms ) );
		$date_data = explode( '-', $date );
		$year = absint( $date_data[0] );
		$month = absint( $date_data[1] );
		$number_rooms = absint( get_post_meta( $room_type_id, 'number_of_rooms', true ) );

		$start_month = date( 'Y-m-d', strtotime( $date . '-01' ) );
		$end_month = date( 'Y-m-d', strtotime( '+1 month', strtotime( $start_month ) ) );

		$start_month_result = $start_month .= 'T13:00:00Z';
		$end_month_result = $end_month .= 'T13:00:00Z';

		$color_unavailable = get_option( 'color-unavailable' );
		$text_unavailable = get_option( 'label-unavailable' );
		$color_available = get_option( 'color-available' );
		$text_available = get_option( 'label-available' );

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}apb_availability WHERE year = %s AND month = %s AND unit_id IN ({$where_room})",
			$year,
			$month
		);

		$result = $wpdb->get_results( $sql );
		if ( empty( $result ) || count( $result ) < $number_rooms ) {
			// Always has a room available in month.
			wp_send_json( array(
				array(
					'start'	=> $start_month_result,
					'end'	=> $end_month_result,
					'color'	=> $color_available,
					'title'	=> $text_available,
					'id'	=> 2,
				),
			) );
		} else {

			$status = array();
			$index = -1;
			$prev = -1;

			for ( $i = 1; $i <= 31; $i++ ) {
				$prop = 'd' . $i;

				$value = 0;
				foreach ( $result as $v ) {
					if ( AWE_function::prevent_book_pending() ) {
						if ( 2 == $v->$prop ) {
							$value = 1;
							break;
						}
					} else {
						if ( 2 == $v->$prop || 3 == $v->$prop ) {
							$value = 1;
							break;
						}
					}
				}

				if ( $value != $prev ) {
					if ( $index >= 0 ) {
						$status[ $index ]['end'] = $year . '-' . zeroise( $month, 2 ) . '-' . zeroise( $i, 2 ) . 'T13:00:00Z';
					}

					$index++;
					$status[ $index ] = array();

					$status[ $index ]['start'] = $year . '-' . zeroise( $month, 2 ) . '-' . zeroise( $i, 2 ) . 'T13:00:00Z';

					if ( $value ) {
						$status[ $index ]['color'] = $color_available;
						$status[ $index ]['title'] = $text_available;
						$status[ $index ]['id'] = 2;
					} else {
						$status[ $index ]['color'] = $color_unavailable;
						$status[ $index ]['title'] = $text_unavailable;
						$status[ $index ]['id'] = 1;
					}

					$prev = $value;
				}

				if ( 31 == $i ) {
					$status[ $index ]['end'] = $end_month;
				}
			}
			wp_send_json( $status );
		}

		die();
	}
}
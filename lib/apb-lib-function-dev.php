<?php
/**
 * AWE Library Function
 *
 * @class       AWE_function
 * @version     1.0
 * @package     AweBooking/Classes/
 * @author      AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper functions.
 */
class AWE_function {

	/**
	 * Create rooms for a room type.
	 *
	 * @param  int $room_type_id    Room type ID.
	 * @param  int $number_of_rooms Number of rooms to create.
	 * @param  int $begin           Begin create from.
	 *
	 * @return void
	 * @since 2.0
	 */
	public static function bulk_create_rooms( $room_type_id, $number_of_rooms, $begin = 1 ) {
		$room_type = get_post( $room_type_id );
		for ( $i = $begin; $i <= $number_of_rooms; $i++ ) {
			wp_insert_post( array(
				'post_type'     => 'apb_room',
				'post_title'    => 1 == $number_of_rooms ? get_the_title( $room_type_id ) : 'no.' . $i,
				'post_name'     => $room_type->post_name . '-' . $i,
				'post_parent'   => $room_type_id,
				'post_status'   => 'publish',
			) );
		}
	}

	/**
	 * Remove rooms for a room type.
	 *
	 * @param  int $room_type_id    Room type ID.
	 * @param  int $number_of_rooms Number of rooms to create.
	 * @param  int $begin           Begin remove from.
	 *
	 * @return void
	 * @since 2.0
	 */
	public static function bulk_remove_rooms( $room_type_id, $number_of_rooms, $begin = 1 ) {
		$room_type = get_post( $room_type_id );
		for ( $i = $begin; $i <= $number_of_rooms; $i++ ) {
			$room_slug = $room_type->post_name . '-' . $i;
			$temp_rooms = get_posts( array(
				'name' => $room_slug,
				'post_type' => 'apb_room',
				'post_status'   => 'any',
			) );
			if ( ! empty( $temp_rooms[0] ) ) {
				wp_delete_post( $temp_rooms[0]->ID, true );
			}
		}
	}


	/**
	 * Get rooms of room type.
	 *
	 * @param  int $room_type_id Room type ID.
	 * @return array             Array of room object: (id => room).
	 * @since 2.0
	 */
	public static function get_rooms_of_room_type( $room_type_id, $post_status = null ) {
		$query_args = array(
			'post_parent' => $room_type_id,
			'post_type'   => 'apb_room',
			'numberposts' => -1,
			'orderby'     => 'ID',
			'order'       => 'asc',
		);

		if ( $post_status ) {
			$query_args['post_status'] = $post_status;
		}

		$room = get_children( $query_args );

		return $room;
	}


	/**
	 * Get a room from room type to book.
	 *
	 * @param  int    $room_type_id Room type ID.
	 * @param  string $from         From date.
	 * @param  string $to           To date.
	 *
	 * @return int
	 * @since 2.0
	 */
	public static function get_room_available_from_room_type( $room_type_id, $from, $to ) {
		$room = AWE_function::get_rooms_of_room_type( $room_type_id );

		if ( empty( $room ) ) {
			return false;
		}

		$unavailable_room = AWE_function::get_room_unavailable( $from, $to, $room_type_id );
		$unavailable_room_id = array();
		foreach ( $unavailable_room as $v ) {
			$unavailable_room_id[] = $v->unit_id;
		}

		$cart = AWE_function::get_cart();

		foreach ( $room as $id => $object ) {
			if ( in_array( $id, $unavailable_room_id ) ) {
				continue;
			}

			if ( isset( $cart[ $id ] ) ) {
				continue;
			}

			return $id;
		}

		return false;
	}


	/**
	 * Get list nights.
	 *
	 * @param  string $start  Arrival date.
	 * @param  string $end    Departure date.
	 * @param  string $format Output date format.
	 * @param  string $step   Date step.
	 * @return array
	 */
	public static function range_night( $start, $end, $format = 'm/d/Y', $step = '+1 day' ) {
		$dates = array();

		$current = strtotime( $start );
		$last = strtotime( $end );

		while ( $current < $last ) {
			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}
		return $dates;
	}


	/**
	 * Get room unavailable.
	 *
	 * @param  string $from         Arrival date. Converted date.
	 * @param  string $to           Departure date. Converted date.
	 * @param  int    $room_type_id Room type ID.
	 * @return array
	 * @since 2.0
	 */
	public static function get_room_unavailable( $from, $to, $room_type_id = null ) {
		global $wpdb;

		$dates = AWE_function::range_night( $from, $to );

		if ( empty( $dates ) ) {
			return array();
		}

		$room_ids = array();
		if ( ! empty( $room_type_id ) ) {
			$rooms = AWE_function::get_rooms_of_room_type( $room_type_id );
			foreach ( $rooms as $k => $v ) {
				$room_ids[] = $k;
			}
		}

		$where = array();
		foreach ( $dates as $date ) {
			$d = explode( '/', $date );
			$field_year = $d[2];
			$field_month = absint( $d[0] );
			$field_day = 'd' . absint( $d[1] );

			if ( AWE_function::prevent_book_pending() ) {
				$where[] = sprintf(
					'year = %d AND month = %d AND %s != 2',
					$field_year,
					$field_month,
					$field_day
				);
			} else {
				$where[] = sprintf(
					'year = %d AND month = %d AND %s < 2',
					$field_year,
					$field_month,
					$field_day
				);
			}
		}

		$where = implode( ' OR ', $where );

		if ( ! empty( $room_ids ) ) {
			$where .= ' AND unit_id IN ( ' . implode( ',', $room_ids ) . ' )';
		}

		$sql = "SELECT DISTINCT unit_id FROM {$wpdb->prefix}apb_availability WHERE {$where}";
		return $wpdb->get_results( $sql );
	}


	/**
	 * Check available.
	 *
	 * @param  string $from         Arrival date with m/d/Y format.
	 * @param  string $to           Departure date with m/d/Y format.
	 * @param  int    $adult        Number of adult.
	 * @param  int    $child        Number of child.
	 * @param  int    $room_type_id Room type ID.
	 * @param  array  $post_data    $_POST data.
	 * @return array                (room_type_id => remain_rooms).
	 * @since 2.0
	 */
	public static function check_available( $from, $to, $adult, $child, $room_type_id = null, $post_data = array() ) {
		$unavailable = AWE_function::get_room_unavailable( $from, $to, $room_type_id );

		$number_nights = AWE_function::get_number_of_nights( $from, $to );

		$unavailable_room_type = array();
		foreach ( $unavailable as $v ) {

			$room_type_id2 = wp_get_post_parent_id( $v->unit_id );
			if ( isset( $unavailable_room_type[ $room_type_id2 ] ) ) {
				$unavailable_room_type[ $room_type_id2 ] += 1;
			} else {
				$unavailable_room_type[ $room_type_id2 ] = 1;
			}

			if ( AWE_function::activated_wpml() ) {
				$lang = ! empty( $post_data['lang'] ) ? $post_data['lang'] : ICL_LANGUAGE_CODE;

				$room_type_id2_trans = icl_object_id( $room_type_id2, 'apb_room_type', true, $lang );
				if ( $room_type_id2 != $room_type_id2_trans ) {
					if ( isset( $unavailable_room_type[ $room_type_id2_trans ] ) ) {
						$unavailable_room_type[ $room_type_id2_trans ] += 1;
					} else {
						$unavailable_room_type[ $room_type_id2_trans ] = 1;
					}
				}
			}
		}

		$cart = AWE_function::get_cart();
		foreach ( $cart as $k => $v ) {
			$room_type_id2 = wp_get_post_parent_id( $k );
			if ( isset( $unavailable_room_type[ $room_type_id2 ] ) ) {
				$unavailable_room_type[ $room_type_id2 ] += 1;
			} else {
				$unavailable_room_type[ $room_type_id2 ] = 1;
			}

			if ( AWE_function::activated_wpml() ) {
				$lang = ! empty( $post_data['lang'] ) ? $post_data['lang'] : ICL_LANGUAGE_CODE;

				$room_type_id2_trans = icl_object_id( $room_type_id2, 'apb_room_type', true, $lang );
				if ( $room_type_id2 != $room_type_id2_trans ) {
					if ( isset( $unavailable_room_type[ $room_type_id2_trans ] ) ) {
						$unavailable_room_type[ $room_type_id2_trans ] += 1;
					} else {
						$unavailable_room_type[ $room_type_id2_trans ] = 1;
					}
				}
			}
		}

		$room_type = array();
		$args = array(
			'post_type'     => 'apb_room_type',
			'posts_per_page'    => -1,
			'meta_query'    => array(
				'relation'      => 'AND',
				array(
					'key'       => 'min_night',
					'value'     => $number_nights,
					'compare'   => '<=',
					'type'      => 'NUMERIC',
				),
				array(
					'key'       => 'min_sleeps',
					'value'     => $adult + $child,
					'compare'   => '<=',
					'type'      => 'NUMERIC',
				),
				array(
					'key'       => 'max_sleeps',
					'value'     => $adult + $child,
					'compare'   => '>=',
					'type'      => 'NUMERIC',
				),
				array(
					'relation'  => 'OR',
					array(
						'key'       => 'min_children',
						'compare'   => 'NOT EXISTS',
					),
					array(
						'key'       => 'min_children',
						'value'     => $child,
						'compare'   => '<=',
						'type'      => 'NUMERIC',
					),
					array(
						'key'       => 'min_children',
						'value'     => '',
						'compare'   => '=',
					),
				),
				array(
					'relation'  => 'OR',
					array(
						'key'       => 'max_children',
						'compare'   => 'NOT EXISTS',
					),
					array(
						'key'       => 'max_children',
						'value'     => $child,
						'compare'   => '>=',
						'type'      => 'NUMERIC',
					),
					array(
						'key'       => 'max_children',
						'value'     => '',
						'compare'   => '=',
					),
				),
			),
		);

		if ( ! is_null( $room_type_id ) ) {
			$args['p'] = $room_type_id;
		}

		$apb_query = new WP_Query( apply_filters( 'apb_check_available_query_args', $args, $post_data ) );

		if ( $apb_query->have_posts() ) {
			while ( $apb_query->have_posts() ) {
				$apb_query->the_post();

				$number_rooms = get_post_meta( get_the_ID(), 'number_of_rooms', true );
				if ( isset( $unavailable_room_type[ get_the_ID() ] ) && $unavailable_room_type[ get_the_ID() ] >= $number_rooms ) {
					continue;
				}

				$count = isset( $unavailable_room_type[ get_the_ID() ] ) ? $unavailable_room_type[ get_the_ID() ] : 0;
				$room_type[ get_the_ID() ] = $number_rooms - $count;
			}
			wp_reset_postdata();

		}
		return $room_type;
	}


	/**
	 * Check available for a specific room type.
	 *
	 * @param  string $from         Arrival date with m/d/Y format.
	 * @param  string $to           Departure date with m/d/Y format.
	 * @param  int    $adult        Number of adult.
	 * @param  int    $child        Number of child.
	 * @param  int    $room_type_id Room type ID.
	 * @return string               Result id: guest, min-night, available, unavailable.
	 * @since 2.5.4
	 */
	public static function single_check_available( $from, $to, $adult, $child, $room_type_id = null, $post_data = array() ) {
		$original_id = AWE_function::wpml_get_default_room_type( $room_type_id );

		$min_guest = get_post_meta( $original_id, 'min_sleeps', true );
		$max_guest = get_post_meta( $original_id, 'max_sleeps', true );
		$min_child = get_post_meta( $original_id, 'min_children', true );
		$max_child = get_post_meta( $original_id, 'max_children', true );
		$min_night = get_post_meta( $original_id, 'min_night', true );


		$guests = $adult + $child;
		$number_nights = AWE_function::get_number_of_nights( $from, $to );

		if ( $guests < $min_guest || $guests > $max_guest || $child < $min_child || $child > $max_child ) {
			return 'guest';
		}

		if ( $number_nights < $min_night ) {
			return 'min-night';
		}


		$unavailable = AWE_function::get_room_unavailable( $from, $to, $original_id );
		$number_rooms = get_post_meta( $original_id, 'number_of_rooms', true );

		if ( count( $unavailable ) < $number_rooms ) {
			return 'available';
		}

		return 'unavailable';
	}


	public static function get_remaining_count( $room_type_id, $from, $to ) {
		echo '123';
		$unavailable = AWE_function::get_room_unavailable( $from, $to, $room_type_id );
		$number_rooms = get_post_meta( $room_type_id, 'number_of_rooms', true );

		return $number_rooms - count( $unavailable );
	}


	/**
		 * Update room available.
	 *
	 * @param  string $from    Arrival date.
	 * @param  string $to      Departure date.
	 * @param  int    $room_id Room ID.
	 * @param  int    $status  Available status
	 *     0: completed
	 *     1: not available
	 *     2: Available
	 *     3: Pending.
	 *
	 * @since 2.0
	 */
	public static function update_available( $from, $to, $room_id, $status ) {
		global $wpdb;
		//$from = AWE_function::convert_date_to_mdY( $from );
		//$to = AWE_function::convert_date_to_mdY( $to );
		$dates = AWE_function::range_night( $from, $to );

		if ( empty( $dates ) ) {
			return;
		}

		foreach ( $dates as $date ) {
			$d = explode( '/', $date );
			$field_year = $d[2];
			$field_month = $d[0];
			$field_day = 'd' . absint( $d[1] );

			$check_exists = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->prefix}apb_availability WHERE unit_id = %d AND year = %d AND month = %d",
					$room_id,
					$field_year,
					$field_month
				)
			);
			$check_exists = absint( $check_exists );

			if ( $check_exists ) {
				$wpdb->update(
					"{$wpdb->prefix}apb_availability",
					array(
						$field_day => $status,
					),
					array(
						'unit_id' => $room_id,
						'year'    => $field_year,
						'month'   => $field_month,
					),
					array( '%s' )
				);
			} else {
				$wpdb->insert(
					"{$wpdb->prefix}apb_availability",
					array(
						'unit_id'  => $room_id,
						'year'     => $field_year,
						'month'    => $field_month,
						$field_day => $status,
					),
					array( '%d', '%d', '%d', '%d' )
				);
			}
		}
	}


	/**
	 * Check if preventing booking in pending day.
	 *
	 * @return bool
	 * @since 2.0
	 */
	public static function prevent_book_pending() {
		return ( bool ) get_option( '_booking_pending' );
	}


	/**
	 * Check if use woo checkout.
	 *
	 * @return bool
	 * @since 2.0
	 */
	public static function use_woo_checkout() {
		return 1 == get_option( 'rooms_checkout_style' );
	}


	/**
	 * Get list days.
	 *
	 * @param  string $start  Arrival date.
	 * @param  string $end    Departure date.
	 * @param  string $format Output date format.
	 * @param  string $step   Date step.
	 * @return array
	 */
	public static function range_date( $start, $end, $format = 'm/d/Y', $step = '+1 day' ) {
		$dates = array();

		$current = strtotime( $start );
		$last = strtotime( $end );

		while ( $current <= $last ) {
			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}
		return $dates;
	}


	/**
	 * Get number of nights.
	 *
	 * @param  string $from Arrival date with format m/d/Y or Y-m-d.
	 * @param  string $to   Departure date with format m/d/Y or Y-m-d.
	 *
	 * @return int          Number of nights.
	 * @since 2.0
	 */
	public static function get_number_of_nights( $from, $to ) {
		$from = strtotime( $from );
		$to = strtotime( $to );
		return ceil( ( $to - $from ) / DAY_IN_SECONDS );
	}

	/**
	 * Register order status.
	 * @return array Order status.
	 * @since 1.0
	 */
	public static function apb_get_order_statuses() {
		$order_statuses = array(
			'apb-pending'    => _x( 'Pending Payment', 'Order status', 'awebooking' ),
			'apb-completed'  => _x( 'Completed', 'Order status', 'awebooking' ),
			'apb-cancelled'  => _x( 'Cancelled', 'Order status', 'awebooking' ),
		);
		return apply_filters( 'apb_order_statuses', $order_statuses );
	}

	public static function apb_get_trans_order_statuses( $status ) {
		$order_statuses = array(
			'apb-pending'    => _x( 'Pending Payment', 'Order status', 'awebooking' ),
			'apb-completed'  => _x( 'Completed', 'Order status', 'awebooking' ),
			'apb-cancelled'  => _x( 'Cancelled', 'Order status', 'awebooking' ),
		);
		
		switch ( $status ) {
			case 'apb-pending':
				return 'Pending Payment';
				break;

			case 'apb-completed':
				return 'Completed Payment';
				break;

			case 'apb-cancelled':
				return 'Cancelled Payment';
				break;
		}
	}




	/**
	 * check_room_available - Show room available.
	 * @param date $from
	 * @param date $to
	 */
	public static function check_room_available( $from, $to, $room_id = '', $check_all_day = false, $status_filter = false, $status = null ) {
		global $wpdb;
		$to = strtotime( $to );
		$to = $to - DAY_IN_SECONDS;
		$to = date( 'm/d/Y', $to );
		// var_dump( $to);

		// Default year.
		$start_year     = date( 'Y', strtotime( $from ) );
		$end_year       = date( 'Y', strtotime( $to ) );

		// Default month.
		$start_month    = date( 'm', strtotime( $from ) );
		$end_month      = date( 'm', strtotime( $to ) );

		// Default day.
		$start_day      = date( 'd', strtotime( $from ) );
		$end_day        = date( 'd', strtotime( $to ) );

		/*if ( $end_day > 1 ) {
			$end_day--;
		} else {
			if ( 1 == $end_month ) {
				$end_month = 12;
				$end_year--;
				$end_day = 31;
			} else {
				$end_month--;
				for ( $i = 31; $i >= 28; $i++ ) {
					if ( checkdate( $end_month, $i, $end_year ) ) {
						$end_day = $i;
						break;
					}
				}
			}
		}*/

		// Get list month of start date and end date.
		$list_month = self::get_list_days( $start_year, $end_year, $start_month, $end_month );

		if ( count( $list_month ) > 2 ) {
			for ( $day = $start_day; $day <= 31; $day++ ) {
				$for_date = $start_year . '-' . $start_month . '-' . $day;
				if ( strtotime( $for_date ) >= strtotime( $from ) && strtotime( $for_date ) <= strtotime( $to ) ) {
					$_days[ $start_year . '-' . $start_month ][ 'd' . ( int ) $day ] = ( int ) $day;
				}
			}
			for ( $d_center = 1; $d_center <= count( $list_month ) - 2; $d_center++ ) {
				for ( $day = 1; $day <= 31; $day++ ) {
					$_days[ $list_month[ $d_center ]['y'] . '-' . $list_month[ $d_center ]['m'] ][ 'd' . $day ] = $day;
				}
			}

			for ( $day = 1; $day <= $end_day; $day++ ) {
				$for_date = $end_year . '-' . $end_month . '-' . $day;
				if ( strtotime( $for_date ) <= strtotime( $to ) ) {
					$_days[ $end_year . '-' . $end_month ][ 'd' . $day ] = $day;
				}
			}
		} else {
			for ( $day = $start_day; $day <= 31; $day++ ) {
				$for_date = $start_year . '-' . $start_month . '-' . $day;
				if ( strtotime( $for_date ) >= strtotime( $from ) && strtotime( $for_date ) <= strtotime( $to ) ) {
					$_days[ $start_year . '-' . $start_month ][ 'd' . ( int ) $day ] = ( int ) $day;
				}
			}
			if ( count( $list_month ) > 1 ) {
				for ( $day = 1; $day <= $end_day; $day++ ) {
					$for_date = $end_year . '-' . $end_month . '-' . $day;
					if ( strtotime( $for_date ) <= strtotime( $to ) ) {
						$_days[ $end_year . '-' . $end_month ][ 'd' . $day ] = $day;
					}
				}
			}
		}

		/*
		 * Query by list day
		 * Status 0 : completed
		 * Status 1 : not available
		 * Status 2 : Available
		 * Status 3 : Pending
		 */
		if ( isset( $_days) ) {
			foreach ( $_days as $key_item => $val_item ) {
				$esc_param = implode( ' AND ', self::available_get_array_param( $val_item ) );

				$m = ( int ) date( 'm', strtotime( $key_item ) );
				$y = date( 'Y', strtotime( $key_item ) );

				if ( '' == $room_id ) {
					$param_day = array( $y, $m );
					for ( $i = 0; $i<=count( $val_item)-1;$i++) {
						$param_day[] = 1;
					}
					$sql = $wpdb->prepare("SELECT unit_id FROM {$wpdb->prefix}apb_availability where year = %d and month = %d and $esc_param ", $param_day);

				 }else{
				   if ( $check_all_day == FALSE) {
						/*----------  Check by Status room by start date and End Date : Type ">" ----------*/
					   $param_day = array( $y, $m, $room_id);
					   $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}apb_availability where year = %d and month = %d and unit_id = %d ", $param_day);
				   }
				   if ( $check_all_day == TRUE) {

						if ( $status_filter == TRUE) {
							/*----------  Check by Status room by start date and End Date : Type "=" ----------*/

							/**
							 *
							 * check row date exists
							 * insert row if not exists
							 */

							$date_exists = $wpdb->get_results( $wpdb->prepare("SELECT unit_id FROM {$wpdb->prefix}apb_availability where year = %d and month = %d and unit_id = %d ",array( $y, $m, $room_id) ) );
							if ( empty( $date_exists ) ) {
								AWE_function::update_available( $from, $to, $room_id , 2 );
								// AWE_Controller::update_day_available( $from, $to, $room_id , 2);
							}
							$esc_param = implode(" and ", self::available_get_array_param( $val_item,"=") );

							$param_day = array( $y, $m, $room_id);

							for( $i = 0; $i<=count( $val_item)-1;$i++) {
								$param_day[] = $status;
							}
							$sql = $wpdb->prepare("SELECT unit_id FROM {$wpdb->prefix}apb_availability where year = %d and month = %d and unit_id = %d and $esc_param ", $param_day);
						}

						if ( $status_filter == FALSE) {
							/*----------  Check by Status room by start date and End Date : Type ">" ----------*/

							$param_day = array( $y, $m, $room_id);
							for( $i = 0; $i<=count( $val_item)-1;$i++) {
								$param_day[] = 1;
							}
							$sql = $wpdb->prepare("SELECT unit_id FROM {$wpdb->prefix}apb_availability where year = %d and month = %d and unit_id = %d and $esc_param ", $param_day);
						}

				   }
			   }
			   return $wpdb->get_results( $sql);
			}
		}

	}

	public static function available_get_array_param( $param, $custom = false ) {
		foreach ( $param as $key => $val ) {
			if ( false == $custom ) {
				$array_param[ $key ] = "$key > %d";
			} else {
				$array_param[ $key ] = "$key " . $custom . " %d";
			}
		}
		return  $array_param;
	}

	public static function get_new_post_id() {
		global $wpdb;
		$data_id =  $wpdb->get_results( "SELECT * FROM {$wpdb->posts} order by ID DESC limit 1");
		return $data_id[0]->ID;
	}

	/**
	 * Get_room_option (Room package).
	 * @param int    $object_id Room type id.
	 * @param string $type      Deprecated from 2.0.
	 *
	 * @return array
	 */
	public static function get_room_option( $object_id, $type = 'apb_room_type' ) {
		if ( AWE_function::activated_wpml() ) {
			$object_id = icl_object_id( $object_id, 'apb_room_type', true, ICL_LANGUAGE_CODE );
		}
		global $wpdb;
		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}apb_booking_options WHERE entity_type = '%s' AND object_id = %d",
			$type,
			$object_id
		);
		return $wpdb->get_results( $sql );
	}

	public static function awe_get_link( $action, $paramt = '' ) {
		return admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=' . $action . $paramt );
	}

	/**
	 * Type change price of room.
	 *
	 * @return array
	 * @deprecated 2.0
	 */
	public static function operation() {
		_deprecated_function( __FUNCTION__, '2.0' );
		return array(
			'add'       => 'Add to price',
			'sub'       => 'Subtract from price',
			'replace'   => 'Replace price',
			'increase'  => 'Increase price by % amount',
			'decrease'  => 'Decrease price by % amount',
		);
	}

	/**
	 * Get template from plugin or theme.
	 *
	 * @param string $file  Template file name.
	 * @param array  $param Params to add to template.
	 *
	 * @return string
	 */
	public static function template_exsits( $file, $param = array() ) {
		extract( $param );
		if ( locate_template( 'apb-template/' . $file . '.php' ) ) {
			$template_load = locate_template( 'apb-template/' . $file . '.php' );
		} else {
			$template_load = AWE_BK_PLUGIN_DIR . '/apb-template/' . $file . '.php';
		}

		$path = apply_filters( 'apb_template_exists', $template_load, $file, $param );

		return $path;
	}

	/**
	 * Display select page.
	 * @param  string $name Field name.
	 * @param  int    $id   Page id.
	 * @return void
	 */
	public static function select_page( $name, $id = 0 ) {
		$args = array(
			'post_type' => 'page',
		);
		$posts_array = get_pages( $args );
		$data = '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '">';
		$data .= '<option>-- ' . esc_html__( 'Select a page', 'awebooking' ) . ' --</option>';
		foreach ( $posts_array as $item ) {
			$data .= ' <option ' . selected( $id, $item->ID, false ) . ' value="' . absint( $item->ID ). '">' . esc_html( $item->post_title ) . '</option>';
		}
		$data .= '</select>';

		echo $data;
	}

	/**
	 *
	 * Func get_list_days
	 * fucntion result data
	 *
	 */

	public static function get_list_days( $start_year, $end_year, $start_month, $end_month ) {
		$total_year = $end_year - $start_year;

		if ( $start_year != $end_year ) {
			$days = array();

			for ( $m = $start_month; $m <= 12; $m++ ) {
				$days[] = array(
					'm' => $m,
					'y' => $start_year,
				);
			}
			for ( $y = 1; $y <= $total_year - 1; $y++ ) {
				for ( $m_n = 1; $m_n <= 12; $m_n++ ) {
					$days[] = array(
						'm' => $m_n,
						'y' => $start_year + $y,
					);
				}
			}
			for ( $m2 = 1; $m2 <= $end_month; $m2++ ) {
				$days[] = array(
					'm' => $m2,
					'y' => $end_year,
				);
			}
		} else {
			$days = array();
			for ( $m = $start_month; $m <= $start_month + ( $end_month - $start_month ); $m++ ) {
				$days[] = array(
					'm' => $m,
					'y' => $end_year,
				);
			}
		}
		return $days;
	}

	/**
	 * List currencies.
	 * @return array
	 * @since 1.0
	 */
	public static function list_currencies() {
		return apply_filters( 'apb_currencies', array(
			'AED' => 'United Arab Emirates Dirham',
			'ARS' => 'Argentine Peso',
			'AUD' => 'Australian Dollars',
			'BDT' => 'Bangladeshi Taka',
			'BRL' => 'Brazilian Real',
			'BGN' => 'Bulgarian Lev',
			'CAD' => 'Canadian Dollars',
			'CLP' => 'Chilean Peso',
			'CNY' => 'Chinese Yuan',
			'COP' => 'Colombian Peso',
			'CZK' => 'Czech Koruna',
			'DKK' => 'Danish Krone',
			'DOP' => 'Dominican Peso',
			'EUR' => 'Euros',
			'HKD' => 'Hong Kong Dollar',
			'HRK' => 'Croatia kuna',
			'HUF' => 'Hungarian Forint',
			'ISK' => 'Icelandic krona',
			'IDR' => 'Indonesia Rupiah',
			'INR' => 'Indian Rupee',
			'NPR' => 'Nepali Rupee',
			'ILS' => 'Israeli Shekel',
			'JPY' => 'Japanese Yen',
			'KIP' => 'Lao Kip',
			'KRW' => 'South Korean Won',
			'MYR' => 'Malaysian Ringgits',
			'MXN' => 'Mexican Peso',
			'NGN' => 'Nigerian Naira',
			'NOK' => 'Norwegian Krone',
			'NZD' => 'New Zealand Dollar',
			'PYG' => 'Paraguayan Guaraní',
			'PHP' => 'Philippine Pesos',
			'PLN' => 'Polish Zloty',
			'GBP' => 'Pounds Sterling',
			'GHS' => 'Ghanaian Cedi',
			'RON' => 'Romanian Leu',
			'RUB' => 'Russian Ruble',
			'SAR' => 'Saudi Arabia riyal',
			'SGD' => 'Singapore Dollar',
			'ZAR' => 'South African rand',
			'SEK' => 'Swedish Krona',
			'CHF' => 'Swiss Franc',
			'TWD' => 'Taiwan New Dollars',
			'THB' => 'Thai Baht',
			'TRY' => 'Turkish Lira',
			'UAH' => 'Ukrainian Hryvnia',
			'USD' => 'US Dollars',
			'VND' => 'Vietnamese Dong',
			'EGP' => 'Egyptian Pound',
		) );
	}


	/**
	 * Get currency symbol.
	 *
	 * @param  string $currency Currency code.
	 * @return string
	 */
	public static function get_currency( $currency = '' ) {
		$currency_symbol = '';

		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'AUD' :
			case 'ARS' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;. ';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
				$currency_symbol = '&yen;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'DKK' :
				$currency_symbol = 'DKK';
				break;
			case 'DOP' :
				$currency_symbol = 'RD&#36;';
				break;
			case 'EGP' :
				$currency_symbol = 'EGP';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'GHS' :
				$currency_symbol = 'GHS';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'INR' :
				$currency_symbol = 'Rs. ';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr. ';
				break;
			case 'KIP' :
				$currency_symbol = '&#8365;';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'NPR' :
				$currency_symbol = 'Rs. ';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'PYG' :
				$currency_symbol = '&#8370;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;. ';
				break;
			case 'SAR':
				$currency_symbol = 'ر.س';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'TRY' :
				$currency_symbol = '&#8378;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'UAH' :
				$currency_symbol = '&#8372;';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			default :
				$currency_symbol = '';
				break;
		}

		$currency_symbol = apply_filters( 'apb_get_currency_symbol', $currency_symbol, $currency );

		return $currency_symbol;
	}


	/**
	 * Get cart.
	 *
	 * @return array
	 * @since 2.0
	 */
	public static function get_cart() {
		$cart = isset( $_SESSION['apb_cart'] ) && is_array( $_SESSION['apb_cart'] ) ? $_SESSION['apb_cart'] : array();
		return $cart;
	}


	/**
	 * Update cart.
	 *
	 * @param  array $data New cart data.
	 * @since 2.0
	 */
	public static function update_cart( $data ) {
		$_SESSION['apb_cart'] = $data;
	}


	/**
	 * Delete cart.
	 *
	 * @since 2.0
	 */
	public static function delete_cart() {
		$_SESSION['apb_cart'] = array();

		if ( AWE_function::use_woo_checkout() ) {
			if ( class_exists( 'WooCommerce' ) ) {
				WC()->cart->empty_cart();
			}
		}
	}


	/**
	 * Add room to cart.
	 *
	 * @param int    $room_id      Room ID.
	 * @param string $from         Arrival date.
	 * @param string $to           Departure date.
	 * @param int    $adult        Adult.
	 * @param int    $child        Child.
	 * @param float  $price        Price.
	 * @param array  $sale_info    Sale data.
	 * @param array  $package_data Package data.
	 */
	public static function add_room_to_cart( $room_id, $from, $to, $adult, $child, $price, $sale_info = null, $package_data = null, $cart_index = -1, $post_data = array() ) {
		$cart = AWE_function::get_cart();

		if ( isset( $cart[ $room_id ] ) ) {
			return false;
		}

		$cart_total = ! empty( $cart['total'] ) ? (float) $cart['total'] : 0;

		$room_data = apply_filters( 'apb_cart_item_data', array(
			'room_id'       => $room_id,
			'from'          => $from,
			'to'            => $to,
			'price'         => $price,
			'adult'         => $adult,
			'child'         => $child,
			'sale_info'     => $sale_info,
			'package_data'  => $package_data,
		), $post_data );

		if ( $cart_index >= 0 ) {
			array_splice( $cart, $cart_index, 0, array( $room_id => $room_data ) );
			$new_cart = array();
			foreach ( $cart as $room ) {
				$new_cart[ $room['room_id'] ] = $room;
			}
			$cart = $new_cart;
		} else {
			$cart[ $room_id ] = $room_data;
		}

		$cart_total += (float) $room_data['price'];
		$cart['total'] = $cart_total;

		AWE_function::update_cart( $cart );

		if ( AWE_function::use_woo_checkout() && class_exists( 'WooCommerce' ) ) {
			$cart_item_data = $room_data;
			WC()->cart->add_to_cart( $room_id, 1, null, null, $cart_item_data );
		}

		return true;
	}


	/**
	 * Remove room from cart.
	 *
	 * @param int $room_id Room ID.
	 * @since 2.0
	 */
	public static function remove_room_from_cart( $room_id ) {
		$cart = AWE_function::get_cart();

		if ( ! isset( $cart[ $room_id ] ) ) {
			return;
		}

		unset( $cart[ $room_id ] );

		AWE_function::update_cart( $cart );

		// var_dump($cart);

		if ( AWE_function::use_woo_checkout() && class_exists( 'WooCommerce' ) ) {
			$cart = WC()->cart->get_cart();
			foreach ( $cart as $cart_key => $cart_item ) {
				if ( $room_id == $cart_item['product_id'] ) {
					WC()->cart->remove_cart_item( $cart_key );
				}
			}
		}
	}

	public static function apb_get_option_to_selected( $args = array() ) {
		if ( isset( $args['name'] ) && isset( $args['count_num'] ) ) {
			$html = '<select ';
			if ( isset( $args['name'] ) && '' != $args['name'] ) {
				$html .= 'name="' . esc_attr( $args['name'] ) . '" ';
			}
			if ( isset( $args['data'] ) ) {
				foreach ( $args['data'] as $attr => $value ) {
					$html .= $attr . '="' . esc_attr( $value ) . '" ';
				}
			}
			$start = isset( $args['start_num'] ) ? $args['start_num'] : 1;

			$html .= '>';
			if ( isset( $args['default'] ) ) {
				$html .= '<option selected="selected" value="' . $args['default']['value'] . '">' . $args['default']['label'] . '</option>';
			}
			for ( $i = $start; $i <= $args['count_num']; $i++ ) {
				$html .= '<option ';
				if ( isset( $args['select'] ) && $args['select'] == $i ) {
					$html .= 'selected="selected"';
				}
				$html .= ' value="' . esc_attr( $i ) . '">' . $i . '</option>';
			}
			$html .= '</select>';
			echo $html;
		}
	}

	public static function apb_gen_input( $args = array() ) {
		$html ='<input ';
		if ( ! empty( $args ) ) {
			foreach ( $args as $attr => $value ) {
				$html .= ' ' . $attr . '="' . esc_attr( $value ) . '" ';
			}
		}
		$html .='>';
		echo $html;
	}

	public static function apb_icon_type_price( $type ) {
		_deprecated_function( __FUNCTION__, '2.0' );
		$list = array(
			'add' => 'Add',
			'sub' => 'Sub',
			'replace' => 'Replace',
			'increase' => 'Add %',
			'decrease' => 'Sub %',
		);
		return $list[ $type ];
	}

	/**
	 * Get price detail in a range days of room.
	 *
	 * @param  string  $from    From date.
	 * @param  string  $to      To date.
	 * @param  int     $room_id Room ID.
	 * @param  integer $night   Number of nights.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_pricing_of_days( $from, $to, $room_id, $night = 1 ) {
		if ( AWE_function::activated_wpml() ) {
			$lang = wpml_get_default_language();
			$room_id = icl_object_id( $room_id, 'apb_room_type', true, $lang );
		}

		$start_month = date( 'm', strtotime( $from ) );
		$end_month = date( 'm', strtotime( $to ) );

		$start_year = date( 'Y', strtotime( $from ) );
		$end_year = date( 'Y', strtotime( $to ) );

		$start_date = date( 'd', strtotime( $from ) );
		$end_date = date( 'd', strtotime( $to ) );

		$key_pricing = array();
		if ( $start_month == $end_month ) {

			$check_pricing = AWE_function::check_apb_pricing( $start_year, $start_month, $room_id );
			if ( ! empty( $check_pricing ) ) {

				for ( $day = ( int ) $start_date; $day <= ( int ) $end_date - $night; $day++ ) {
					$get_price = 'd' . $day;
					if ( checkdate( $start_month, $day, $start_year ) ) {
						$key_pricing[ $start_month ][ $day ] = $check_pricing[0]->$get_price;
					}
				}
			} else {
				for ( $day = ( int ) $start_date; $day <= ( int ) $end_date - $night; $day++ ) {
					$get_price = 'd' . $day;
					if ( checkdate( $start_month, $day, $start_year ) ) {
						$key_pricing[ $start_month ][ $day ] = get_post_meta( $room_id, 'base_price', true );
					}
				}
			}
		} else {
			$list_month = AWE_function::get_list_day( $start_year, $end_year, $start_month, $end_month );
			if ( count( $list_month ) > 2 ) {
				//====== List month > 2  ============//
				// Month day start

				$_monthStart = $list_month[0];
				$_monthEnd = $list_month[ count( $list_month ) - 1 ];
				unset( $list_month[0] );
				unset( $list_month[ count( $list_month ) ] );

				$check_pricing_start = AWE_function::check_apb_pricing( $_monthStart['y'], $_monthStart['m'], $room_id );
				if ( ! empty( $check_pricing_start ) ) {
					$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
					for ( $day = ( int ) $start_date; $day <= ( int ) $total_day; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $_monthStart['m'], $day, $start_year ) ) {
							$key_pricing[ $_monthStart['m'] ][ $day ] = $check_pricing_start[0]->$get_price;
						}
					}
				} else {
					$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
					for ( $day = ( int ) $start_date; $day <= ( int ) $total_day; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $_monthStart['m'], $day, $start_year ) ) {
							$key_pricing[ $_monthStart['m'] ][ $day ] = get_post_meta( $room_id, 'base_price', true );
						}
					}
				}
				foreach ( $list_month as $monthCenter ) {
					$check_pricing = AWE_function::check_apb_pricing( $monthCenter['y'], $monthCenter['m'], $room_id );

					if ( ! empty( $check_pricing ) ) {

						$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
						for ( $day = 0; $day <= ( int ) $total_day; $day++ ) {
							$get_price = 'd' . $day;
							if ( checkdate( $monthCenter['m'], $day, $start_year ) ) {
								$key_pricing[ $monthCenter['m'] ][ $day ] = $check_pricing[0]->$get_price;
							}
						}
					} else {

						$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
						for ( $day = 0; $day <= ( int ) $total_day; $day++ ) {
							if ( checkdate( $monthCenter['m'], $day, $start_year ) ) {
								$key_pricing[ $monthCenter['m'] ][ $day ] = get_post_meta( $room_id, 'base_price', true );
							}
						}
					}
				}

				 // Month day end.
				$check_pricing_end = AWE_function::check_apb_pricing( $_monthEnd['y'], $_monthEnd['m'], $room_id );
				if ( ! empty( $check_pricing_end ) ) {
					for ( $day = 1; $day <= ( int ) $end_date - $night; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $_monthEnd['m'], $day, $start_year ) ) {
							$key_pricing[ $_monthEnd['m'] ][ $day ] = $check_pricing_end[0]->$get_price;
						}
					}
				} else {
					for ( $day = 1; $day <= ( int ) $end_date - $night; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $_monthEnd['m'], $day, $start_year ) ) {
							$key_pricing[ $_monthEnd['m'] ][ $day ] = get_post_meta( $room_id, 'base_price', true );
						}
					}
				}
			} else {
				// Month day start.
				$check_pricing_start = AWE_function::check_apb_pricing( $list_month[0]['y'], $list_month[0]['m'], $room_id );
				if ( ! empty( $check_pricing_start ) ) {

					$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
					for ( $day = ( int ) $start_date; $day <= ( int ) $total_day; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $list_month[0]['m'], $day, $start_year ) ) {
							$key_pricing[ $list_month[0]['m'] ][ $day ] = $check_pricing_start[0]->$get_price;
						}
					}
				} else {
					$total_day = date( 't', mktime( 0, 0, 0, $start_year, 1, $start_month ) );
					for ( $day = ( int ) $start_date; $day <= ( int ) $total_day; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $list_month[0]['m'], $day, $start_year ) ) {
							$key_pricing[ $list_month[0]['m'] ][ $day ] = get_post_meta( $room_id, 'base_price', true );
						}
					}
				}
				// Month day start.
				$check_pricing_end = AWE_function::check_apb_pricing( $list_month[ count( $list_month ) - 1 ]['y'], $list_month[ count( $list_month ) - 1 ]['m'], $room_id );
				if ( ! empty( $check_pricing_end ) ) {
					for ( $day = 1; $day <= ( int ) $end_date - $night; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $list_month[ count( $list_month ) - 1 ]['m'], $day, $start_year ) ) {
							$key_pricing[ $list_month[ count( $list_month ) - 1 ]['m'] ][ $day ] = $check_pricing_end[0]->$get_price;
						}
					}
				} else {
					for ( $day = 1; $day <= ( int ) $end_date - $night; $day++ ) {
						$get_price = 'd' . $day;
						if ( checkdate( $list_month[ count( $list_month ) - 1 ]['m'], $day, $start_year ) ) {
							$key_pricing[ $list_month[ count( $list_month ) - 1 ]['m'] ][ $day ] = get_post_meta( $room_id, 'base_price', true );
						}
					}
				}
			}
		}
		return $key_pricing;
	}


	static public function get_list_day( $start_year, $end_year, $start_month, $end_month ) {
		$total_year = $end_year - $start_year;

		if ( $start_year != $end_year ) {
			$days = array();

			for ( $m = $start_month; $m <= 12; $m++ ) {
				$days[] = array(
					'm' => $m,
					'y' => $start_year,
				);
			}
			for ( $y = 1; $y <= $total_year - 1; $y++ ) {
				for ( $m_n = 1; $m_n <= 12; $m_n++ ) {
					$days[] = array(
						'm' => $m_n,
						'y' => $start_year + $y,
					);
				}
			}
			for ( $m2 = 1; $m2 <= $end_month; $m2++) {
				$days[] = array(
					'm' => $m2,
					'y' => $end_year,
				);
			}
		} else {
			$days = array();
			for ( $m = $start_month; $m <= $start_month + ( $end_month - $start_month); $m++) {
				$days[] = array(
					'm' => $m,
					'y' => $end_year,
				);
			}
		}
		return $days;
	}


	public static function get_total_price( $list_pricing_of_days ) {
		$price = 0;
		if ( ! empty( $list_pricing_of_days ) ) {
			foreach ( $list_pricing_of_days as $key => $value ) {
				foreach ( $value as $pr ) {
					$price += $pr;
				}
			}
			return $price;
		}
	}


	public static function apb_setState( $name, $value ) {
		$_SESSION[$name] = $value;
	}


	public static function apb_getStateFlash( $name ) {
		if (isset( $_SESSION[$name] ) ) {
			return $_SESSION[$name];
		}
	}


	public static function Apb_get_permalink( $object = '', $args = array(), $action = '' ) {
		$current = get_option( 'permalink_structure' );
		if ( '' != $current ) {
			$uri = trailingslashit( get_permalink( $object ) );
			if ( 'action' == $action ) {
				$uri .= '?action=apb';
			}
			foreach ( $args as $key => $value ) {
				$uri .= $key . '/' . $value;
			}
			$url = str_replace( 'archives/', '', $uri );
			return str_replace( $uri, get_permalink( $object ), ( $url ) );
		} else {

			$uri = get_permalink( $object );

			foreach ( $args as $key => $value ) {
				if ( 'room_type_info' == $key ) {
					$uri .= '&rt_id=' . $value;
				}
			}
			return rtrim( $uri );
		}
	}

	public static function awe_help( $text = '' ) {
		echo '<img class="help_tip" data-tip="' . esc_attr( $text ) . '" src="' . esc_url( AWE_BK_BASE_URL_PLUGIN . '/assets/backend/images/help.png' ) . '" height="16" width="16" /></p>';
	}

	public static function get_type_of_sale( $key ) {
		$list = array( 'replace' => 'Replace price', 'sub' => 'Subtract from price', 'decrease' => 'Decrease price by %' );
		return $list[ $key ];
	}

	public static function get_symbol_of_sale( $key ) {
		$list = array( 'sub' => '-', 'decrease' => '%' );
		return $list[ $key ];
	}

	public static function apb_price( $price = 0 ) {
		$price = ( float ) $price;
		$currency_pos = AWE_function::get_option( 'woocommerce_currency_pos' ) ? esc_attr( AWE_function::get_option( 'woocommerce_currency_pos' ) ) : 'left';
		switch ( $currency_pos ) {
			case 'left':
				$price_format = '%2$s%1$s';
			break;

			case 'right':
				$price_format = '%1$s%2$s';
			break;

			case 'left_space':
				$price_format = '%2$s&nbsp;%1$s';
			break;

			case 'right_space':
				$price_format = '%1$s&nbsp;%2$s';
			break;
		}

		$args = array(
			'currency'  => AWE_function::get_option( 'woocommerce_currency' ) ? esc_attr( AWE_function::get_option( 'woocommerce_currency' ) ) : '$',
			'thousand_separator'    => AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ? esc_attr( AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ) : '',
			'decimal_separator'     => AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ? esc_attr( AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ) : '',
			'decimals'              => AWE_function::get_option( 'woocommerce_price_num_decimals' ) ? absint( AWE_function::get_option( 'woocommerce_price_num_decimals' ) ) : 0,
			'price_format'          => $price_format,
		);

		extract( $args );
		$price = number_format( $price, $decimals, $decimal_separator, $thousand_separator );

		return sprintf( $price_format, esc_attr( $price ), esc_attr( AWE_function::get_currency( $currency ) ) );
	}

	public static function apb_get_extra_sale( $extra_sale, $total_day, $from = '' ) {
		$BeforeDay = count( AWE_function::range_date( date( 'Y-m-d' ), $from ) );

		$days = array();
		foreach ( $extra_sale as $item_extra_sale ) {
			$sale_type = ! empty( $item_extra_sale['sale_type'] ) ? $item_extra_sale['sale_type'] : 'sub';
			if ( 'Month' == $item_extra_sale['type_duration'] ) {
				$days[ $item_extra_sale['total'] * 30 ] = array(
					'type_duration' => $item_extra_sale['type_duration'],
					'total_day'     => $item_extra_sale['total'] * 30,
					'total'         => $item_extra_sale['total'],
					'amount'        => $item_extra_sale['amount'],
					'sale_type'     => $sale_type,
				);
			}
			if ( 'Week' == $item_extra_sale['type_duration'] ) {
				$days[ $item_extra_sale['total'] * 7 ] = array(
					'type_duration' => $item_extra_sale['type_duration'],
					'total_day'     => $item_extra_sale['total'] * 7,
					'total'         => $item_extra_sale['total'],
					'amount'        => $item_extra_sale['amount'],
					'sale_type'     => $sale_type,
				);
			}
			if ( 'Day' == $item_extra_sale['type_duration'] ) {
				$days[ $item_extra_sale['total'] ] = array(
					'type_duration' => $item_extra_sale['type_duration'],
					'total_day'     => $item_extra_sale['total'],
					'total'         => $item_extra_sale['total'],
					'amount'        => $item_extra_sale['amount'],
					'sale_type'     => $sale_type,
				);
				$last_day = $days[ 'day-' . $item_extra_sale['total'] ];
			}
			if ( 'Before-Day' == $item_extra_sale['type_duration'] ) {
				if ( $BeforeDay >= $item_extra_sale['total'] ) {
					$days[ 'before-' . $item_extra_sale['total'] ] = array(
						'type_duration' => $item_extra_sale['type_duration'],
						'total_day'     => $item_extra_sale['total'],
						'total'         => $item_extra_sale['total'],
						'amount'        => $item_extra_sale['amount'],
						'sale_type'     => $sale_type,
						'type_sale'     => 'before-day',
					);
					$last_before_day = $days[ 'before-' . $item_extra_sale['total'] ];
				}
			}
		}
		$_days = array();
					
		$array_discount = array();
		foreach ( $days as $day ) {
			if ( 'Before-Day' == $day['type_duration'] ) {
				if ( $BeforeDay >= $day['total_day'] ) {
					$_days[] = 'before-' . $day['total_day'];
				}
			} else if( 'Day' == $day['type_duration'] ){
				if( ($day['total_day'] < 7 || ( $day['total_day'] > 7 && $day['total_day'] < 30 ) ||  $day['total_day'] > 30 ) && $day['total_day'] < $total_day ){
					$_days[] = $day['total_day'];
				}
			}else { 
				if ( $total_day - 1 >= $day['total_day'] ) {
					$_days[] = $day['total_day'];
				}

				
			}
		}

		
		if ( ! empty( $_days ) ) {
			sort( $_days,1 );

			//echo $days[ $_days[ count( $_days ) - 1 ]]['total_day'];
			if( $last_before_day['total_day'] != $days[ $_days[ count( $_days ) - 1 ]]['total_day'] ) {

				$array_discount['week'] = ( $days[ $_days[ count( $_days ) - 1 ] ] );
				$array_discount['before'] = $last_before_day;

			} else {
				$array_discount = $days[ $_days[ count( $_days ) - 1 ]];
				//echo count( $array_discount );
			}
			return $array_discount;
		} else {
			return '';
		}

	}


	public static function get_room_type() {
		return get_posts( array( 'post_type' => 'apb_room_type', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'asc', 'suppress_filters' => false ) );
	}

	public static function apb_datepicker_lang( $key = '' ) {
		$i18 = apply_filters( 'apb_datepicker_lang', array(
			// 'datepicker-af'  => 'datepicker-af',
			// 'datepicker-ar-DZ'   => 'datepicker-ar-DZ',
			'datepicker-ar'     => 'Arabic',
			// 'datepicker-az'  => 'datepicker-az',
			// 'datepicker-be'  => 'datepicker-be',
			// 'datepicker-bg'  => 'Bulgarian',
			'datepicker-ca'     => 'Catalan',
			'datepicker-cs'     => 'Czech',
			// 'datepicker-cy-GB'   => 'datepicker-cy-GB',
			// 'datepicker-da'  => 'datepicker-da',
			'datepicker-zh-CN'  => 'Chinese (China)',
			// 'datepicker-zh-HK'   => 'Chinese (Hong Kong) ',
			'datepicker-zh-TW'  => 'Chinese (Taiwan)',
			'datepicker-nl-BE'  => 'Dutch (Belgium)',
			'datepicker-nl'     => 'Dutch (The Netherlands)',
			'datepicker-en-AU'  => 'English (Australia)',
			'datepicker-en-GB'  => 'English (United Kingdom)',
			'datepicker-en-NZ'  => 'English (New Zealand)',
			'datepicker-fr-CA'  => 'French (Canada)',
			'datepicker-fr-CH'  => 'French (Switzerland)',
			'datepicker-fr'     => 'French (France)',
			'datepicker-de'     => 'German',
			'datepicker-ja'     => 'Japanese',
			'datepicker-it'     => 'Italian',
			'datepicker-it-CH'  => 'Italian (Switzerland)',
			'datepicker-pl'     => 'Polish (Poland)',
			'datepicker-pt-BR'  => 'Portuguese (Brazil)',
			'datepicker-pt'     => 'Portuguese (Portugal)',
			'datepicker-ru'     => 'Russian',
			'datepicker-es'     => 'Spanish (Spain)',
			'datepicker-ta'     => 'Tamil (India)',
			'datepicker-tr'     => 'Turkish',
			'datepicker-el'     => 'Greek',
			// 'datepicker-eo'  => 'datepicker-eo',
			// 'datepicker-et'  => 'Estonian',
			// 'datepicker-eu'  => 'datepicker-eu',
			// 'datepicker-fa'  => 'datepicker-fa',
			// 'datepicker-fi'  => 'datepicker-fi',
			// 'datepicker-fo'  => 'datepicker-fo',
			// 'datepicker-gl'  => 'datepicker-gl',
			'datepicker-he'     => 'Hebrew',
			// 'datepicker-hi'  => 'datepicker-hi',
			// 'datepicker-hr'  => 'datepicker-hr',
			'datepicker-hu'     => 'Hungarian',
			// 'datepicker-hy'  => 'datepicker-hy',
			// 'datepicker-id'  => 'Indonesian',
			// 'datepicker-is'  => 'datepicker-is',
			// 'datepicker-ka'  => 'datepicker-ka',
			// 'datepicker-kk'  => 'datepicker-kk',
			// 'datepicker-km'  => 'datepicker-km',
			'datepicker-ko'     => 'Korean',
			// 'datepicker-ky'  => 'datepicker-ky',
			// 'datepicker-lb'  => 'datepicker-lb',
			// 'datepicker-lt'  => 'datepicker-lt',
			// 'datepicker-lv'  => 'datepicker-lv',
			// 'datepicker-mk'  => 'datepicker-mk',
			// 'datepicker-ml'  => 'datepicker-ml',
			// 'datepicker-ms'  => 'datepicker-ms',
			// 'datepicker-nb'  => 'datepicker-nb',
			// 'datepicker-nn'  => 'datepicker-nn',
			// 'datepicker-no'  => 'datepicker-no',
			// 'datepicker-rm'  => 'datepicker-rm',
			// 'datepicker-ro'  => 'datepicker-ro',
			// 'datepicker-sk'  => 'datepicker-sk',
			'datepicker-sk'  	=> 'Slovakia',
			// 'datepicker-sq'  => 'datepicker-sq',
			// 'datepicker-sr-SR'   => 'datepicker-sr-SR',
			// 'datepicker-sr'  => 'datepicker-sr',
			// 'datepicker-sv'  => 'datepicker-sv',
			'datepicker-th'     => 'Thailand',
			// 'datepicker-tj'  => 'datepicker-tj',
			// 'datepicker-uk'  => 'datepicker-uk',
			'datepicker-vi'     => 'Vietnamese',
		) );

		if ( '' != $key ) {
			return $i18[ $key ];
		} else {
			return $i18;
		}
	}

	public static function Apb_Js_FormatDate( $key ) {
		_deprecated_function( __FUNCTION__, '2.0' );
		$list_format = array(
			'dd/mm/yy'  => 'd/m/Y',
			'dd.mm.yy'  => 'd.m.Y',
			'dd-mm-yy'  => 'd-m-Y',
			'yy-mm-dd'  => 'Y-m-d',
			'yy/mm/dd'  => 'Y/m/d',
			'd.m.yy'    => 'd.m.Y',
			'yy.mm.dd. ' => 'Y.m.d',
			'yy. m. d. ' => 'Y.m.d',
			'mm/dd/yy'  => 'm/d/Y',
		);
		return $list_format[ $key ];
	}

	/**
	 * Detect if we should use a light or dark colour on a background colour
	 *
	 * @param mixed $color
	 * @param string $dark (default: '#000000' )
	 * @param string $light (default: '#FFFFFF' )
	 * @return string
	 */
	public static function apb_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}

	/**
	 * Hex darker/lighter/contrast functions for colours
	 *
	 * @param mixed $color
	 * @return string
	 */
	public static function apb_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color{0}.$color{1} );
		$rgb['G'] = hexdec( $color{2}.$color{3} );
		$rgb['B'] = hexdec( $color{4}.$color{5} );

		return $rgb;
	}

	public static function apb_hex_darker( $color, $factor = 30 ) {
		$base  = AWE_function::apb_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

	/**
	 * Hex darker/lighter/contrast functions for colours
	 *
	 * @param mixed $color
	 * @param int $factor (default: 30)
	 * @return string
	 */
	public static function apb_hex_lighter( $color, $factor = 30 ) {
		$base  = AWE_function::apb_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

	/**
	 * Move template action.
	 *
	 * @param string $template_type
	 */
	public static function move_template_action( $template_file , $path ) {
		if ( ! is_dir( get_stylesheet_directory() . '/apb-template/' ) ) {
			mkdir( get_stylesheet_directory() . '/apb-template/' );
		}
		if ( ! is_dir( get_stylesheet_directory() . $path ) ) {
			mkdir( get_stylesheet_directory() . $path );
		}
		if ( ! file_exists( get_stylesheet_directory() . $path . $template_file . '.php' ) ) {
			$f = fopen( get_stylesheet_directory() . $path . $template_file . '.php', 'w+' );
			copy( AWE_BK_PLUGIN_DIR . $path . $template_file . '.php', get_stylesheet_directory() . $path . $template_file . '.php' );
		}
	}

	/**
	 * Delete template action.
	 *
	 * @param string $template_type
	 */
	public static function delete_template_action( $template_file, $path ) {
		if ( is_dir( get_stylesheet_directory() . $path ) ) {
			unlink( get_stylesheet_directory() . $path . $template_file . '.php' );
		}
	}

	public static function apb_print_js( $code ) {

		echo "<!-- Awebooking JavaScript -->\n<script type=\"text/javascript\">\njQuery(function( $) {";

		// Sanitize
		$wc_queued_js = wp_check_invalid_utf8( $code );
		$wc_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $code );
		$wc_queued_js = str_replace( "\r", '', $code );

		echo $wc_queued_js . "});\n</script>\n";
	}


	/**
	 * Check if room in cart.
	 *
	 * @param  int $room_id Room ID.
	 * @return bool         True if room in cart.
	 * @since 1.9
	 */
	public static function check_room_in_cart( $room_id ) {
		$cart = AWE_function::apb_cart( 'apb_cart' );
		if ( ! empty( $cart ) && is_array( $cart ) ) {
			foreach ( $cart as $key_item => $item_cart ) {
				if ( $item_cart['room_id'] == $room_id ) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Get_day_for_month_start : Get all day for one month.
	 * @param $year int
	 * @param $month int
	 * @param $start_date int
	 * @param $end_date int
	 * @param $room_id int
	 * @param $price int
	 * @param $action status get result
	 * @param $control String
	 */
	static public function get_day_for_month_start( $year, $month, $start_date, $end_date, $room_id, $price, $operation, $action, $control = '' ) {
		if ( 'start' == $action ) {

			for ( $day = 1; $day <= 31; $day++ ) {
				$for_date = $year . '-' . $month . '-' . $day;
				if ( strtotime( $for_date ) >= strtotime( $start_date ) && strtotime( $for_date ) <= strtotime( $end_date ) ) {
					$days_price[ 'd' . $day ] = AWE_function::get_price_operation( $operation, $room_id, $month, $year, 'd' . $day, $price);
				} else {
					$days_price[ 'd' . $day ] = get_post_meta( wp_kses( $room_id, '' ), 'base_price', true );
				}
			}
		} elseif ( 'default' == $action ) {
			for ( $day = 1; $day <= 31; $day++ ) {
				$for_date = $year . '-' . $month . '-' . $day;
				$days_price[ 'd' . $day ] = AWE_function::get_price_operation( $operation, $room_id, $month, $year, 'd' . $day, $price );
			}
		} elseif ( 'end' == $action ) {
			for ( $day = 1; $day <= 31; $day++ ) {
				$for_date = $year . '-' . $month . '-' . $day;
				if ( strtotime( $for_date ) <= strtotime( $end_date ) ) {
					$days_price[ 'd' . $day ] = AWE_function::get_price_operation( $operation, $room_id, $month, $year, 'd' . $day, $price );
				} else {
					$days_price[ 'd' . $day ] = get_post_meta( wp_kses( $room_id, '' ), 'base_price', true );
				}
			}
		}
		return $days_price;
	}


	/**
	 * get_price_operation : Get price by operation.
	 * @param $operation string
	 * @param $room_id int
	 * @param $month int
	 * @param $year int
	 * @param $day int
	 * @param $input_price int
	 */
	static public function get_price_operation( $operation, $room_id, $month, $year, $day, $input_price ) {
		$room = AWE_function::check_apb_pricing( $year, $month, $room_id );

		if ( ! empty( $room ) && intval( $room[0]->$day ) ) {
			$price_default = $room[0]->$day;
		} else {
			$price_default = get_post_meta( $room_id, 'base_price', true );
		}

		switch ( $operation ) {
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


	/**
	 * check_rooms_avb : Check room availability manage exists
	 * @param $year int
	 * @param $month int
	 * @param $room_id int
	 */
	public static function check_rooms_avb( $year, $month, $room_id ) {
		global $wpdb;
		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}apb_availability where unit_id = %d and year = %d and month = %d",
			absint( $room_id ),
			absint( $year ),
			absint( $month )
		);
		return $wpdb->get_results( $sql );
	}


	/**
	 * check_apb_pricing : Check room pricing manage exists
	 * @param $year int
	 * @param $month int
	 * @param $room_id int
	 */
	static public function check_apb_pricing( $year, $month, $room_id ) {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apb_pricing where unit_id = $room_id and year = '{$year}' and month = '{$month}'" );
	}


	/**
	 * Get minimum night of room.
	 *
	 * @param  int $room_id Room ID.
	 * @return int          Minimum of night.
	 * @since  1.9
	 */
	public static function get_room_min_night( $room_id ) {
		return absint( get_post_meta( $room_id, 'mid_night', true ) );
	}


	/**
	 * Get total of day price.
	 *
	 * @param  array $price_days Day price array.
	 *
	 * @return float
	 * @since 1.10
	 */
	public static function get_total_day_price( $price_days ) {
		$result = 0;
		foreach ( $price_days as $month => $list_day ) {
			foreach ( $list_day as $day => $price ) {
				$result += $price;
			}
		}
		return ( float ) $result;
	}


	/**
	 * Get number of day price.
	 *
	 * @param  array $price_days Day price array.
	 *
	 * @return int
	 * @since 1.10
	 */
	public static function get_number_day_price( $price_days ) {
		$result = 0;
		foreach ( $price_days as $month => $list_day ) {
			foreach ( $list_day as $day => $price ) {
				$result++;
			}
		}
		return absint( $result );
	}


	public static function calculate_pre_payment($total) {
		$apb_deposit_type = get_option( 'apb_deposit_type' );
		$apb_deposit_number = get_option( 'apb_deposit_number' );

		if ( 'percent' == $apb_deposit_type ) {
			$left = ( $apb_deposit_number / 100 ) * $total;
		} else if ( 'money' == $apb_deposit_type ) {
			$left = $apb_deposit_number;
		}
		return $left;
	}


	/**
	 * Change language code from WordPress to FullCalendar.
	 *
	 * @param  string $code Datepicker language code.
	 *
	 * @return string       FullCalendar language code.
	 * @since 1.11
	 */
	public static function lang_code_datepicker_to_fullcalendar( $code ) {
		if ( 'default' == $code || '' == $code ) {
			$fc_code = false;
		} else {
			$code = strtolower( substr( $code, 11 ) );
			switch ( $code ) {
				case 'ar-dz':
					$fc_code = 'ar-tn';
				break;

				case 'cy-gb':
					$fc_code = 'en-gb';
				break;

				case 'fr-ch':
					$fc_code = 'fr';
				break;

				// case 'it-ch':
				//  $fc_code = 'it';
				// break;

				case 'nl-be':
					$fc_code = 'nl';
				break;

				default:
					$fc_code = $code;
			}
		}

		$fc_code = apply_filters( 'apb_lang_code_datepicker_to_fullcalendar', $fc_code, $code );

		return $fc_code;
	}


	/**
	 * Change language code from WPML to Datepicker.
	 *
	 * @param  string $code WPML language code.
	 *
	 * @return string       Datepicker language code.
	 * @since 1.11
	 */
	public static function lang_code_wpml_to_datepicker( $code ) {
		switch ( $code ) {
			case 'all':
				$datepicker_code = 'default';
			break;

			case 'en':
				$datepicker_code = 'datepicker-en-GB';
			break;

			case 'pt-pt':
				$datepicker_code = 'datepicker-pt';
			break;

			case 'zh-hans':
				$datepicker_code = 'datepicker-zh-CN';
			break;

			case 'zh-hant':
				$datepicker_code = 'datepicker-zh-TW';
			break;

			default:
				$datepicker_code = 'datepicker-' . $code;
		}

		$datepicker_code = apply_filters( 'apb_lang_code_wpml_to_datepicker', $datepicker_code, $code );

		return $datepicker_code;
	}


	/**
	 * Get current date format, according datepicker language option.
	 *
	 * @return string Date format.
	 * @since 1.11
	 */
	public static function get_current_date_format() {
		$format = 'm/d/Y';
		$datepicker_lang = AWE_function::get_datepicker_lang();

		switch ( $datepicker_lang ) {
			case 'datepicker-ar':
			case 'datepicker-ca':
			case 'datepicker-nl-BE':
			case 'datepicker-en-AU':
			case 'datepicker-en-GB':
			case 'datepicker-en-NZ':
			case 'datepicker-fr':
			case 'datepicker-it':
			case 'datepicker-he':
			case 'datepicker-pt-BR':
			case 'datepicker-pt':
			case 'datepicker-es':
			case 'datepicker-el':
			case 'datepicker-ta':
			case 'datepicker-th':
			case 'datepicker-vi':
				$format = 'd/m/Y';
			break;

			case 'datepicker-nl':
				$format = 'd-m-Y';
			break;

			case 'datepicker-fr-CH':
			case 'datepicker-de':
			case 'datepicker-cs':
			case 'datepicker-it-CH':
			case 'datepicker-pl':
			case 'datepicker-ru':
			case 'datepicker-tr':
			case 'datepicker-sk':
				$format = 'd.m.Y';
			break;

			case 'datepicker-zh-TW':
			case 'datepicker-ja':
				$format = 'Y/m/d';
			break;

			case 'datepicker-fr-CA':
			case 'datepicker-zh-CN':
			case 'datepicker-ko':
				$format = 'Y-m-d';
			break;

			case 'datepicker-hu':
				$format = 'Y.m.d.';
				break;

			/*case 'datepicker-ko':
				$format = 'Y. m. d.';
			break;*/

			default:
				$format = 'm/d/Y';
		}

		$format = apply_filters( 'apb_current_date_format', $format );

		return $format;
	}


	/**
	 * Convert date string from other format to m/d/Y.
	 *
	 * @param  string $date_string Date string.
	 * @param  string $date_format Optional. Date format. If empty, use current date format.
	 *
	 * @return string
	 * @since 1.11
	 */
	public static function convert_date_to_mdY( $date_string, $date_format = null ) {
		if ( ! $date_format ) {
			$date_format = AWE_function::get_current_date_format();
		}
		
		if ( 'm/d/Y' == $date_format ) {
			return $date_string;
		}
		
		$d = DateTime::createFromFormat( $date_format, $date_string );
		return $d->format( 'm/d/Y' );
	}

	public static function convert_date_to_Ymd( $date_string, $date_format = null ) {
		if ( ! $date_format ) {
			$date_format = AWE_function::get_current_date_format();
		}
		
		if ( 'Y-m-d' == $date_format ) {
			return $date_string;
		}
		
		$d = DateTime::createFromFormat( $date_format, $date_string );
		return $d->format( 'Y-m-d' );
	}



	public static function is_system_format( $date_string ) {
		$stamp = strtotime( $date_string );

		if ( ! $stamp ) {
			return false;
		}

		return date( 'm/d/Y', $stamp ) === $date_string;
	}


	/**
	 * Replace some value in email.
	 *
	 * @param  string $string   String need replace.
	 * @param  object $order_id Order ID.
	 * @return string
	 * @since 1.11
	 */
	public static function email_str_replace( $string, $order_id ) {
		$new_string = $string;

		$key = array(
			'{site_title}',
			'{order_number}',
			'{order_date}',
		);

		$value = array(
			get_bloginfo( 'name' ),
			$order_id,
			get_the_time( AWE_function::get_current_date_format(), $order_id ),
		);

		$new_string = str_replace( $key, $value, $string );

		return apply_filters( 'email_str_replace', $new_string, $string, $order_id );
	}


	/**
	 * Get check available page url.
	 *
	 * @return string
	 * @since 2.0
	 */
	public static function get_check_available_page() {
		$page_id = AWE_function::get_option( 'check_avb' );
		// if ( AWE_function::activated_wpml() ) {
		// 	$page_id = icl_object_id( $page_id, 'page', true );
		// }
		return esc_url( get_permalink( $page_id ) );
	}


	/**
	 * Get check available page id.
	 *
	 * @return string
	 * @since 2.0
	 */
	public static function get_check_available_page_id() {
		$page_id = AWE_function::get_option( 'check_avb' );
		// if ( AWE_function::activated_wpml() ) {
		// 	$page_id = icl_object_id( $page_id, 'page', true );
		// }
		return $page_id;
	}


	/**
	 * Get list room page id.
	 *
	 * @return string
	 * @since 2.0
	 */
	public static function get_list_room_page_id() {
		$page_id = AWE_function::get_option( 'list_room' );
		// if ( AWE_function::activated_wpml() ) {
		// 	$page_id = icl_object_id( $page_id, 'page', true );
		// }
		return $page_id;
	}


	/**
	 * Get default checkout type page id.
	 *
	 * @return string
	 * @since 2.0
	 */
	public static function get_checkout_page_id() {
		$page_id = AWE_function::get_option( 'apb_checkout' );
		// if ( AWE_function::activated_wpml() ) {
		// 	$page_id = icl_object_id( $page_id, 'page', true );
		// }
		return $page_id;
	}


	/**
	 * Get checkout page url
	 * Include checkout type checking.
	 *
	 * @return string
	 * @since 2.2.1
	 */
	public static function get_checkout_page_url() {
		$link_checkout = '';
		if ( 1 == get_option( 'rooms_checkout_style' ) ) {
			if ( class_exists( 'WC_Cart' ) ) {
				global $woocommerce;
				$link_checkout = $woocommerce->cart->get_checkout_url();
			}
		} elseif ( 2 == get_option( 'rooms_checkout_style' ) ) {
			$link_checkout = get_permalink( AWE_function::get_checkout_page_id() );
		}

		return apply_filters( 'apb_get_checkout_page_url', $link_checkout );
	}


	/**
	 * Check current page is check available page.
	 *
	 * @return bool
	 * @since 2.0
	 */
	public static function is_check_available_page() {
		$page_id = AWE_function::get_option( 'check_avb' );
		// if ( AWE_function::activated_wpml() ) {
		// 	$page_id = icl_object_id( $page_id, 'page', true );
		// }
		return get_the_ID() == $page_id;
	}


	/**
	 * Get datepicker language.
	 *
	 * @return string
	 * @since 2.0
	 */
	public static function get_datepicker_lang() {
		$lang = AWE_function::get_option( 'datepicker_lang' );
		// if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		// 	$lang = AWE_function::lang_code_wpml_to_datepicker( ICL_LANGUAGE_CODE );
		// 	// $format = icl_translate( 'Formats', $format, $format );
		// }

		$lang = apply_filters( 'apb_get_datepicker_lang', $lang );

		return $lang;
	}


	/**
	 * Get max nights.
	 *
	 * @return int
	 * @since 2.0
	 */
	public static function get_max_night() {
		return absint( get_option( 'max_night' ) );
	}


	/**
	 * Get max adult.
	 *
	 * @return int
	 * @since 2.0
	 */
	public static function get_max_adult() {
		return absint( get_option( 'max_adult' ) );
	}


	/**
	 * Get max child.
	 *
	 * @return int
	 * @since 2.0
	 */
	public static function get_max_child() {
		return absint( get_option( 'max_child' ) );
	}


	/**
	 * Remove old version data.
	 * @return void
	 * @since 2.1
	 */
	public static function reset_old_data() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}rooms_availability" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}rooms_booking_unit_options" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}rooms_pricing" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}rooms_unit_type" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_availability" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_booking_options" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_pricing" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}posts WHERE post_type IN ('apb_room_type', 'apb_room', 'shop_order', 'apb_order')" );
	}


	/**
	 * Check if show single calendar.
	 * @return bool
	 */
	public static function show_single_calendar() {
		return absint( get_option( 'apb_show_single_calendar' ) );
	}


	public static function get_similar_name( $room_id ) {
		_deprecated_function( __FUNCTION__, '2.4' );
		$room = get_post( $room_id );

		$room_type_id = $room->post_parent;

		$room_title = $room->post_title;

		$room_type_title = get_the_title( $room_type_id );

		$number_of_rooms = get_post_meta( $room_type_id, 'number_of_rooms', true );

		$result = 1 == $number_of_rooms ? $room_title : $room_type_title . ' (' . $room_title . ')';

		return $result;
	}


	public static function get_extra_guest_data( $base, $adult, $child ) {
		$result = array( 'adult' => 0, 'child' => 0 );

		$total = $adult + $child;

		if ( $total > $base ) {
			$extra_guess = $total - $base;

			if ( $adult <= $base ) {
				$result['child'] = $extra_guess;
			} else {
				$result['adult'] = $adult - $base;
				$result['child'] = $child;
			}
		}

		return apply_filters( 'apb_get_extra_guest_data', $result, $base, $adult, $child );
	}


	public static function get_extra_price_data( $room_type_id, $adult, $child ) {
		$result = array( 'adult' => 0, 'child' => 0 );

		$base_price_for = get_post_meta( $room_type_id, 'base_price_for', true ) ? absint( get_post_meta( $room_type_id, 'base_price_for', true ) ) : 1;

		$extra_guess_data = self::get_extra_guest_data( $base_price_for, $adult, $child );

		$extra_adult = get_post_meta( $room_type_id, 'extra_adult', true );
		$extra_child = get_post_meta( $room_type_id, 'extra_child', true );

		if ( ! empty( $extra_guess_data['adult'] ) && ! empty( $extra_adult ) ) {
			foreach ( $extra_adult as $v ) {
				if ( $v['number'] == $extra_guess_data['adult'] ) {
					$result['adult'] = $v['price'];
					break;
				}
			}
		}

		if ( ! empty( $extra_guess_data['child'] ) && ! empty( $extra_child ) ) {
			foreach ( $extra_child as $v ) {
				if ( $v['number'] == $extra_guess_data['child'] ) {
					$result['child'] = $v['price'];
					break;
				}
			}
		}

		return $result;
	}


	/**
	 * Get room type name to display in input field in room type meta box.
	 *
	 * @param  int $room_type_id Room type ID.
	 * @return string
	 * @since 2.2.1
	 */
	public static function get_room_name_for_meta_value( $room_type_id ) {
		$rooms = self::get_rooms_of_room_type( $room_type_id );
		if ( empty( $rooms ) ) {
			return '';
		}

		$value = array();
		foreach ( $rooms as $room ) {
			$value[] = $room->post_title;
		}

		return implode( ',', $value );
	}


	/**
	 * Support order by title numeric.
	 *
	 * @param  string $orderby Post where string.
	 * @return string
	 * @since 2.2.1
	 */
	public static function orderby_post_title_int( $orderby ) {
		return 'LENGTH(wp_posts.post_title) ASC, wp_posts.post_title ASC';
	}


	/**
	 * Calculate total price.
	 *
	 * @param  int $room_type_id Room type ID.
	 * @param  string $from         Arrival date in m/d/Y format.
	 * @param  string $to           Departure date in m/d/Y format.
	 * @param  int    $adult        Number of adults.
	 * @param  int    $child        Number of children.
	 * @param  array  $package_data Package data.
	 * @return float                Total price.
	 */
	public static function calculate_total_price( $room_type_id, $from, $to, $adult, $child, $package_data = null ) {
		$total = 0;

		$number_nights = count( self::range_date( $from, $to ) ) - 1;

		$price_nights = self::get_pricing_of_days( $from, $to, $room_type_id );
		$total_price_nights = self::get_total_price( $price_nights );


		$extra_sale = get_post_meta( $room_type_id, 'extra_sale', true );
		$base_price_for = get_post_meta( $room_type_id, 'base_price_for', true );
		$extra_guess_data = self::get_extra_guest_data( $base_price_for, $adult, $child );
		$extra_price_data = self::get_extra_price_data( $room_type_id, $adult, $child );

		$total += $total_price_nights;

		if ( ! empty( $extra_price_data['adult'] ) ) {
			$total += $number_nights * $extra_price_data['adult'];
		}

		if ( ! empty( $extra_price_data['child'] ) ) {
			$total += $number_nights * $extra_price_data['child'];
		}

		if ( ! empty( $extra_sale ) ) {
			$number_dates = $number_nights + 1;
			$data_extra_sale = self::apb_get_extra_sale( $extra_sale, $number_dates, $from );

			if ( ! empty( $data_extra_sale ) ) {
				if( count( $data_extra_sale ) > 2 ) { 
					if ( 'sub' == $data_extra_sale['sale_type'] ) {
						$total = $total - $data_extra_sale['amount'];
					}
					if ( 'decrease' == $data_extra_sale['sale_type'] ) {
						$total = $total - $data_extra_sale['amount'] / 100 * $total;
					}
				} else {
					if ( 'sub' == $data_extra_sale['before']['sale_type'] ) {
						$total = $total - $data_extra_sale['before']['amount'];
					}
					if ( 'decrease' == $data_extra_sale['before']['sale_type'] ) {
						$total = $total - $data_extra_sale['before']['amount'] / 100 * $total;
					}

					if ( 'sub' == $data_extra_sale['week']['sale_type'] ) {
						$total = $total - $data_extra_sale['week']['amount'];
					}
					if ( 'decrease' == $data_extra_sale['week']['sale_type'] ) {
						$data_extra_sale['week']['amount'];
						$total = $total - $data_extra_sale['week']['amount'] / 100 * $total;
					}


				}
			}
		}

		return $total;
	}


	/**
	 * Create room for room type.
	 *
	 * @param  int    $room_type_id Room type id.
	 * @param  string $room_name    Room name.
	 * @return int|false
	 * @since 2.2.1
	 */
	public static function create_room( $room_type_id, $room_name ) {
		$room_id = wp_insert_post( array(
			'post_parent'   => $room_type_id,
			'post_title'    => $room_name,
			'post_type'     => 'apb_room',
			'post_status'   => 'publish',
		) );

		return $room_id;
	}


	/**
	 * Get displayed tax text.
	 *
	 * @param float  $price      Price to calculate.
	 * @param float  $tax_amount Tax amount.
	 * @param string $tax_type   Tax type.
	 * @return string
	 * @since 2.2.1
	 */
	public static function get_displayed_tax( $price = null, $tax_amount = null, $tax_type = null ) {
		if ( ! $tax_amount ) {
			$tax_amount = (float) get_option( 'apb_tax_amount' );
		}

		if ( ! $tax_type ) {
			$tax_type = get_option( 'apb_tax_type' );
		}

		$output = '';

		if ( ! empty( $tax_amount ) ) {
			if ( ! $price ) {
				if ( 'fixed' == $tax_type ) {
					$output = AWE_function::apb_price( $tax_amount );
				} else {
					$output = $tax_amount . '%';
				}
			} else {
				$output = self::apb_price( self::calculate_tax( $price, $tax_amount, $tax_type ) - $price );

			}
		}

		return apply_filters( 'apb_get_displayed_tax', $output );
	}


	/**
	 * Calculate tax.
	 *
	 * @param float  $price      Price to calculate.
	 * @param float  $tax_amount Tax amount.
	 * @param string $tax_type   Tax type.
	 * @return float
	 * @since 2.2.1
	 */
	public static function calculate_tax( $price, $tax_amount = null, $tax_type = null ) {
		if ( ! $tax_amount ) {
			$tax_amount = (float) get_option( 'apb_tax_amount' );
		}

		if ( ! $tax_type ) {
			$tax_type = get_option( 'apb_tax_type' );
		}

		$result = $price;

		if ( ! empty( $tax_amount ) ) {
			if ( 'fixed' == $tax_type ) {
				$result += $tax_amount;
			} else {
				$result += $tax_amount / 100 * $price;
			}
		}

		return apply_filters( 'apb_calculate_tax', $result, $price );
	}


	/**
	 * Get option.
	 *
	 * @param  string $option_name Option name.
	 * @return mixed               Option value.
	 * @since 2.3.1
	 */
	public static function get_option( $option_name ) {
		$option_name = apply_filters( 'apb_option_name', $option_name );

		$value = get_option( $option_name );

		return $value;
	}


	/**
	 * Update option.
	 *
	 * @param  string $option_name Option name.
	 * @param  mixed  $value       Option value.
	 * @since 2.3.1
	 */
	public static function update_option( $option_name, $value ) {
		$option_name = apply_filters( 'apb_option_name', $option_name );
		update_option( $option_name, $value );
	}


	/**
	 * Check WPML activated.
	 *
	 * @return bool
	 * @since 2.3.1
	 */
	public static function activated_wpml() {
		return class_exists( 'SitePress' );
	}


	/**
	 * Get master post id. Use for wpml.
	 *
	 * @param  int $id Post id.
	 * @return int
	 * @since 2.4
	 */
	public static function get_master_post_id( $id ) {
		if ( AWE_function::activated_wpml() ) {
			if ( 'apb_room' == get_post_type( $id ) ) {
				if ( get_post_meta( $id, '_icl_lang_duplicate_of', true ) ) {
					return (int) get_post_meta( $id, '_icl_lang_duplicate_of', true );
				}

				return $id;
			}

			return icl_object_id( $id, get_post_type( $id ), true, wpml_get_default_language() );
		}

		return $id;
	}


	/**
	 * Get room type id in current language.
	 *
	 * @param  int $id room type id.
	 * @return int
	 * @since 2.4.1
	 */
	public static function get_room_type_id_current_lang( $id ) {
		if ( AWE_function::activated_wpml() ) {
			$id = icl_object_id( $id, 'apb_room_type', true, ICL_LANGUAGE_CODE );
		}

		return $id;
	}


	/**
	 * Check customer name and email, if email doesn't exists, create new customer.
	 *
	 * @param  string $name  Customer name.
	 * @param  string $email Customer email.
	 * @return int        Customer id.
	 * @since 2.4.1
	 */
	public static function maybe_create_customer( $name, $email ) {
		if ( empty( $name ) || empty( $email ) ) {
			return false;
		}

		if ( email_exists( $email ) ) {
			$customer = get_user_by( 'email', $email );
			$customer_id = $customer->ID;
		} else {
			$random_password = wp_generate_password( 12, false );
			$customer_id = wp_insert_user( array(
				'user_email'	=> $email,
				'user_login'	=> $email,
				'display_name'	=> $name,
				'user_pass'		=> $random_password,
				'role'			=> 'customer',
			) );
		}

		return $customer_id;
	}


	/**
	 * Get customer email from order id.
	 *
	 * @param  int $order_id Order id.
	 * @return string|false
	 */
	public static function get_customer_email( $order_id ) {
		$customer = get_post_meta( $order_id, 'custommer', true );
		$customer = get_userdata( $customer );

		if ( ! empty( $customer->user_email ) ) {
			return $customer->user_email;
		}

		return false;
	}


	/**
	 * Get customer name from order id.
	 *
	 * @param  int $order_id Order id.
	 * @return string|false
	 */
	public static function get_customer_name( $order_id ) {
		$customer = get_post_meta( $order_id, 'custommer', true );
		$customer = get_userdata( $customer );

		if ( ! empty( $customer->user_login ) ) {
			return $customer->user_login;
		}

		return false;
	}


	public static function wpml_get_default_room_type( $room_type_id ) {
		if ( ! function_exists( 'wpml_get_default_language' ) ) {
			return $room_type_id;
		}

		return apply_filters( 'wpml_object_id', $room_type_id, 'apb_room_type', true, wpml_get_default_language() );
	}
}


/**
 * Filter option date_format to translate date with WPML.
 *
 * @param  string $format Date format.
 *
 * @return string
 * @since 1.11
 */
function apb_translate_date_format( $format ) {
	if ( function_exists( 'icl_translate' ) ) {
		$format = AWE_function::lang_code_wpml_to_datepicker( ICL_LANGUAGE_CODE );
		// $format = icl_translate( 'Formats', $format, $format );
	}
	return $format;
}
// add_filter( 'option_date_format', 'apb_translate_date_format' );


/**
 * Send mail to user when order is cancelled.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 1.11
 */
function apb_send_mail_when_cancelled( $order_id ) {
	$config_mail = get_option( 'apb_mail_cancel' );
	if ( empty( $config_mail['notice_status'] ) ) {
		return;
	}

	$customer_email = AWE_function::get_customer_email( $order_id );
	$customer_name = AWE_function::get_customer_name( $order_id );

	$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : '[{site_title}] Cancelled order ({order_number})';
	$subject = AWE_function::email_str_replace( $subject, $order_id );

	ob_start();
	do_action( 'apb_mail_cancelled_order', $order_id );
	$message = ob_get_clean();

	$email = new APB_Email();

	if ( isset( $config_mail['notice_status'] ) && 1 == $config_mail['notice_status'] ) {
		$message_user = $email->apb_style_inline( $email->apb_wrap_message( $config_mail['header'], $message ) );
		$email->apb_sendMail( $customer_email, $subject, $message_user, 0, $customer_name );
	}
	$email->destroy();
}
//add_action( 'apb-cancelled_shop_order', 'apb_send_mail_when_cancelled' );


/**
 * Send mail to user when order is completed.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 1.11
 */
function apb_send_mail_when_completed( $order_id ) {
	$config_mail = get_option( 'apb_mail_complete' );
	if ( empty( $config_mail['notice_status'] ) ) {
		return;
	}

	$customer_email = AWE_function::get_customer_email( $order_id );
	$customer_name = AWE_function::get_customer_name( $order_id );

	$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : 'Your {site_title} booking from {order_date} is completed';
	$subject = AWE_function::email_str_replace( $subject, $order_id );

	ob_start();
	do_action( 'apb_mail_complete_order', $order_id );
	$message = ob_get_clean();

	$email = new APB_Email();

	if ( isset( $config_mail['notice_status'] ) && 1 == $config_mail['notice_status'] ) {
		$message_user = $email->apb_style_inline( $email->apb_wrap_message( $config_mail['header'], $message ) );
		$email->apb_sendMail( $customer_email, $subject, $message_user, 0, $customer_name );
	}
	$email->destroy();
}
//add_action( 'apb-completed_shop_order', 'apb_send_mail_when_completed' );


/**
 * Update available and order status when order is cancelled.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 1.11
 */
function apb_update_available_when_cancelled( $order_id ) {
	$from = get_post_meta( $order_id, 'from', true );
	$to = get_post_meta( $order_id, 'to', true );
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );

	if ( ! is_array( $order_data ) ) {
		return;
	}

	foreach ( $order_data as $v ) {
		Boxes_info_booking::update_status( $v['id'], 'apb-cancelled' );
		AWE_function::update_available( $from, $to, $v['order_room_id'], 2 );
		// AWE_Controller::update_day_available( $from, $to, $v['order_room_id'], 2 );
	}
}
//add_action( 'apb-cancelled_shop_order', 'apb_update_available_when_cancelled' );
add_action( 'trash_shop_order', 'apb_update_available_when_cancelled' );


function apb_delete_order( $order_id ) {
	if ( 'apb_order' != get_post_type( $order_id ) ) {
		return;
	}

	$order_data = get_post_meta( $order_id, 'apb_data_order', true );

	if ( is_array( $order_data ) ) {
		foreach ( $order_data as $v ) {
			AWE_function::update_available( $v['from'], $v['to'], $v['order_room_id'], 2 );

			wp_delete_post( $v['id'], true );
		}
	}
}
add_action( 'delete_post', 'apb_delete_order' );


/**
 * Update available and order status when order is pending.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 2.0
 */
function apb_update_available_when_pending( $order_id ) {
	$from = get_post_meta( $order_id, 'from', true );
	$to = get_post_meta( $order_id, 'to', true );
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );

	if ( ! is_array( $order_data ) ) {
		return;
	}

	foreach ( $order_data as $v ) {
		Boxes_info_booking::update_status( $v['id'], 'apb-pending' );
		AWE_function::update_available( $from, $to, $v['order_room_id'], 3 );
	}
}
//add_action( 'apb-pending_shop_order', 'apb_update_available_when_pending' );


/**
 * Update available and order status when order is completed.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 2.0
 */
function apb_update_available_when_completed( $order_id ) {
	$from = get_post_meta( $order_id, 'from', true );
	$to = get_post_meta( $order_id, 'to', true );
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );

	if ( ! is_array( $order_data ) ) {
		return;
	}

	foreach ( $order_data as $v ) {
		Boxes_info_booking::update_status( $v['id'], 'apb-completed' );
		AWE_function::update_available( $from, $to, $v['order_room_id'], 0 );
	}
}
//add_action( 'apb-completed_shop_order', 'apb_update_available_when_completed' );


/**
 * Add room when import.
 *
 * @param  int    $post_id Room type ID.
 * @param  string $key     Meta key.
 * @param  string $value   Meta value.
 * @return void
 */
function apb_add_room_import( $post_id, $key, $value ) {
	if ( 'apb_room_type' == get_post_type( $post_id ) && 'number_of_rooms' == $key && ! empty( $value ) ) {
		AWE_function::bulk_create_rooms( $post_id, $value );
	}
}
add_action( 'import_post_meta', 'apb_add_room_import', 10, 3 );


/**
 * Remove room when trash room type.
 *
 * @param  int $post_id Room type ID.
 * @return void
 * @since 2.1
 */
function apb_remove_room_when_remove_room_type( $post_id ) {
	if ( 'apb_room_type' != get_post_type( $post_id ) ) {
		return;
	}

	$rooms = AWE_function::get_rooms_of_room_type( $post_id );
	foreach ( $rooms as $room ) {
		wp_trash_post( $room->ID );
	}
}
add_action( 'trash_apb_room_type', 'apb_remove_room_when_remove_room_type' );


/**
 * Create room when untrash room type.
 *
 * @param  WP_Post $post Room type object.
 * @return void
 * @since 2.1
 */
function apb_restore_room_when_restore_room_type( $post ) {
	if ( 'apb_room_type' != $post->post_type ) {
		return;
	}

	$rooms = AWE_function::get_rooms_of_room_type( $post->ID, true );

	foreach ( $rooms as $room ) {
		wp_publish_post( $room->ID );
	}
}
add_action( 'trash_to_publish', 'apb_restore_room_when_restore_room_type' );


/**
 * Filter to body class.
 *
 * @param  array $classes Body class.
 * @return array
 *
 * @since 2.2
 */
function apb_body_class( $classes ) {
	if ( is_page() && get_the_ID() == AWE_function::get_checkout_page_id() ) {
		$classes[] = 'apb-checkout-page';
	}

	return $classes;
}
add_filter( 'body_class', 'apb_body_class' );


/**
 * Fix issue with Woocommerce Multilingual
 *
 * Cart always empty when book.
 *
 * @since 2.2.1
 */
function apb_fix_wcml_empty_cart() {
	if ( ! class_exists( 'woocommerce_wpml' ) ) {
		return;
	}

	global $woocommerce_wpml;
	$wcml_product = $woocommerce_wpml->products;

	remove_action( 'woocommerce_before_calculate_totals', array( $wcml_product, 'woocommerce_calculate_totals' ) );
}
add_action( 'init', 'apb_fix_wcml_empty_cart', 99 );


/**
 * Remove room when remove room type.
 *
 * @param  int $post_id Room type id.
 * @return void
 * @since 2.2.1
 */
function apb_remove_rooms( $post_id ) {
	if ( 'apb_room_type' != get_post_type( $post_id ) ) {
		return;
	}

	$rooms = AWE_function::get_rooms_of_room_type( $post_id );
	foreach ( $rooms as $room ) {
		wp_delete_post( $room->ID, true );
	}
}
add_action( 'delete_post', 'apb_remove_rooms' );


/**
 * Duplicate room when room type is duplicated.
 *
 * @param  int    $master_post_id Master post id.
 * @param  string $lang           Language code.
 * @param  array  $post_array     Post array.
 * @param  int    $id             Pois id.
 * @since 2.3.1
 */
function apb_duplicate_room( $master_post_id, $lang, $post_array, $id ) {
	if ( 'apb_room_type' != get_post_type( $master_post_id ) ) {
		return;
	}

	global $sitepress;

	$rooms = AWE_function::get_rooms_of_room_type( $master_post_id );
	foreach ( $rooms as $room ) {
		$new_room_id = $sitepress->make_duplicate( $room->ID, $lang );

		wp_update_post( array(
			'ID'          => $new_room_id,
			'post_parent' => $id,
		) );
	}
}
add_action( 'icl_make_duplicate', 'apb_duplicate_room', 10, 4 );


/**
 * Filter option name.
 *
 * @param  string $option_name Option name.
 * @return string
 * @since 2.3.1
 */
function apb_filter_option_name( $option_name ) {
	if ( AWE_function::activated_wpml() ) {
		$default_lang = wpml_get_default_language();
		$current_lang = ICL_LANGUAGE_CODE;

		if ( $current_lang != $default_lang ) {
			$option_name = $option_name . '_' . $current_lang;
		}
	}

	return $option_name;
}
add_filter( 'apb_option_name', 'apb_filter_option_name' );


/**
 * Filter product class. Fix bug can't add to cart in woocommerce 2.6.
 *
 * @param  string $class        Product class.
 * @param  string $product_type Product type.
 * @param  string $post_type    Post type.
 * @return string
 * @since 2.5.1
 */
function apb_wc_product_class( $class, $product_type, $post_type ) {
	if ( 'apb_room_type' == $post_type || 'apb_room' == $post_type ) {
		$class = 'WC_Product_Simple';
	}

	return $class;
}
add_filter( 'woocommerce_product_class', 'apb_wc_product_class', 10, 3 );


function apb_unavailable_message_guest( $room_type_id ) {
	echo '<p class="error">';

	$messages = array();

	$messages[] = esc_html__( 'The search information is not right with the following requirements:', 'awebooking' );

	$messages[] = sprintf(
		esc_html__( 'Minimum guests: %s', 'awebooking' ),
		get_post_meta( $room_type_id, 'min_sleeps', true )
	);

	$messages[] = sprintf(
		esc_html__( 'Maximum guests: %s', 'awebooking' ),
		get_post_meta( $room_type_id, 'max_sleeps', true )
	);

	$messages[] = sprintf(
		esc_html__( 'Minimum children: %s', 'awebooking' ),
		get_post_meta( $room_type_id, 'min_children', true )
	);

	$messages[] = sprintf(
		esc_html__( 'Maximum children: %s', 'awebooking' ),
		get_post_meta( $room_type_id, 'max_children', true )
	);

	$messages[] = sprintf(
		esc_html__( 'Minimum nights: %s', 'awebooking' ),
		get_post_meta( $room_type_id, 'min_night', true )
	);


	echo implode( '<br>', $messages ); // WPCS: XSS ok.
	echo '<br>';

	$messages[] = 		esc_html_e( 'Please change your search information and try again.. Thanks', 'awebooking' );

	echo '</p>';
}
add_action( 'apb_unavailable_message_guest', 'apb_unavailable_message_guest' );
add_action( 'apb_unavailable_message_min-night', 'apb_unavailable_message_min_night' );


function apb_unavailable_message_unavailable( $room_type_id ) {
	echo '<p class="error">';

	printf(
		esc_html__( 'There are no rooms available in %s on these dates. Please change your search and try again. Thanks', 'awebooking' ),
		'<b>'.strtoupper( get_the_title( $room_type_id ) ).'</b>'
	);

	echo '</p>';
}
add_action( 'apb_unavailable_message_unavailable', 'apb_unavailable_message_unavailable' );

/**
 * [apb_export_to_excel description]
 * @param  [type] $array [array of order id export]
 * @return [type]        [description]
 */
function apb_export_to_excel( $array ) {
	$args = array( 
        'post_type'  => 'shop_order', 
        'posts_per_page' => '-1',
        'post_status' => array( 'apb-pending', 'apb-completed' ),
       	'post__in'	=> $array,
);
$posts = get_posts( $args );

$html = '';
$html .= '<table>';
	$html .= '<thead>';
		$html .= '<th>' . __( 'Order','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Arrival Date','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Departure Date','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Username','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Email','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Room','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Adult','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Child','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Order Status','awebooking' ) . '</th>';
		$html .= '<th>' . __( 'Total','awebooking' ) . '</th>';
	$html .= '</thead>';
	$html .= '<tbody>';
		$username = '';
		$i=1;
		foreach ($posts as $post) {
			$order_data = AWE_Export::get_order_data( $post->ID );
			$order_date = AWE_Export::get_date( $post->ID );

			$html .= '<tr>';
				$html .= '<td>' . $i++ . '</td>';
				$html .= '<td>' . $order_date['arrival_date'] . '</td>';
				$html .= '<td>' . $order_date['departure_date'] . '</td>';
				$html .= '<td>' . AWE_Export::get_user_name( $post->ID ) . '</td>';
				$html .= '<td>' . AWE_Export::get_email( $post->ID ) . '</td>';
				$html .= '<td>' . AWE_Export::get_room_current( $order_data['order_room_id'] ) . '</td>';
				$html .= '<td>' . $order_data['room_adult'] . '</td>';
				$html .= '<td>' . $order_data['room_child'] . '</td>';
				$html .= '<td>' . AWE_Export::get_order_status( $post->ID ) . '</td>';
				$html .= '<td>' . $order_data['total_price'] . '</td>';
			$html .= '</tr>';
		}
	$html .= '</tbody>';


$html .= '</table>';

return apply_filters( 'apb_export_to_excel', $html );
}

/**
 * [apb_calculate_deposit description]
 * @param  [varchar] $prepayment_type [type of deposit]
 * @param  [varchar] $total           [amount of cash]
 * @return [void]                  	  [amount need to pay]
 */
function apb_calculate_remain_amount($prepayment_type, $total) {
	$amount = $total;
	if( 'full' == $prepayment_type ) {	
		return '0';
	} else {
		
		$ex = explode( '|', $prepayment_type );
		if( '%' == $ex[1]) {
			return $total - ( ($ex[0]/100) * $total );
		} else {
			return $total - $prepayment_type;
		}
	}
}

function apb_calculate_deposit($prepayment_type, $total) {
	$amount = $total;
	if( 'full' == $prepayment_type ) {	
		return $amount;
	} else {
		
		$ex = explode( '|', $prepayment_type );
		if( '%' == $ex[1]) {
			return  ($ex[0]/100) * $total;
		} else {
			return $prepayment_type;
		}
	}
}


function apb_get_deposit_type($value) {
	switch ($value) {
		case 'percent':
			return __( 'Prepayment by percent','awebooking' );
			break;
		case 'money':
			return __( 'Prepayment by money number','awebooking' );
			break;
		
		default:
			
			break;
	}
}

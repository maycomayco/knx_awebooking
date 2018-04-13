<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Controller Autoload
 *
 * @class 		AWE_Controller
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */
if ( ! function_exists( 'wp_handle_upload' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
}
Class AWE_Controller extends AWE_function{

	public function __construct() {
		ob_start();
		add_action( 'add_room', array( $this, 'Controller_add_room' ) );
		add_action( 'delete_room', array( $this, 'Controller_delete_room' ) );

		add_action( 'header_room_single', array( $this, 'header_room_single' ) );

		add_action( 'validate_setting', array( $this, 'validate_setting' ) );
		add_action( 'wp_ajax_Controller_add_booking', array( $this, 'Controller_add_booking' ) );
		add_action( 'delete_booking', array( $this, 'Controller_delete_booking' ) );
		add_action( 'wp_ajax_Controller_delete_booking', array( $this, 'Controller_delete_booking' ) );
		add_action( 'wp_ajax_Controller_delete_new_item_booking', array( $this, 'Controller_delete_new_item_booking' ) );

		add_action( 'wp_ajax_Apb_import_ajax', array( $this, 'Apb_import_ajax' ) );
	}

	/*
	 *  Controller_add_booking
	 *  Creat booking from admin single room - proccess with ajax
	 */
	public function Controller_add_booking() {
		global $wpdb;
		$from = sanitize_text_field( wp_unslash( $_POST['from'] ) );
		$to = sanitize_text_field( wp_unslash( $_POST['to'] ) );
		$total_night = AWE_function::get_number_of_nights( $from, $to );
		// $total_date = count( AWE_function::range_date( $from, $to ) );
		// $room_id = absint( $room_id );
		$room_id = absint( $_POST['room_id'] );

		// ============== Create order booking  ================//
		$booking = array(
			'post_title'    => 'Booking',
			'post_type'     => 'shop_order',
			'post_status'   => wp_kses( $_POST['order_status'], '' ),
			'post_author'   => wp_kses( $_POST['custommer'], '' ),
		);
		$book_id = wp_insert_post( $booking );
		// ============== Create booking of system Awebooking ==============//
		$apb_order = array(
			'post_title'    => '#' . $book_id,
			'post_type'     => 'apb_order',
			'post_status'   => wp_kses( $_POST['order_status'], '' ),
			'post_author'   => wp_kses( $_POST['custommer'], '' ),
		);
		$post_id = wp_insert_post( $apb_order );


		// ========== Meta data of order booking =============

		update_post_meta( $book_id, 'custommer', get_current_user_id() );
		update_post_meta( $book_id, 'from', date( 'm/d/Y', strtotime( $from ) ) );
		update_post_meta( $book_id, 'to', date( 'm/d/Y', strtotime( $to ) ) );
		update_post_meta( $book_id, 'apb_order_id', array( $post_id ) );
		update_post_meta( $book_id, '_order_total', $_POST['room_price'] );
		update_post_meta( $book_id, 'order_type', 2 );

		// ========== Meta data booking of system Awebooking =============
		$adult = $_POST['room_adult'];
		$child = $_POST['room_child'];
		$extra_price_data = AWE_function::get_extra_price_data( $room_id, $adult, $child );

		// Get price people by room with number of people.
		$_price = 0;

		if ( ! empty( $extra_price_data['adult'] ) ) {
			$_price += $total_night * $extra_price_data['adult'];
		}

		if ( ! empty( $extra_price_data['child'] ) ) {
			$_price += $total_night * $extra_price_data['child'];
		}

		if ( isset( $_POST['custommer'] ) ) {
			update_post_meta( $post_id, 'custommer', wp_kses( $_POST['custommer'], '' ) );
		}
		if ( isset( $_POST['room_adult'] ) ) {
			update_post_meta( $post_id, 'room_adult', wp_kses( $_POST['room_adult'], '' ) );
		}
		if ( isset( $_POST['room_child'] ) ) {
			update_post_meta( $post_id, 'room_child', wp_kses( $_POST['room_child'], '' ) );
		}
		if ( isset( $from ) ) {
			update_post_meta( $post_id, 'from', date( 'm/d/Y', strtotime( wp_kses( $from, '' ) ) ) );
		}
		if ( isset( $to ) ) {
			update_post_meta( $post_id, 'to', date( 'm/d/Y', strtotime( wp_kses( $to, '' ) ) ) );
		}
		update_post_meta( $post_id, 'order_room_id', $room_id, '' );
		update_post_meta( $post_id, 'posttype_shop_order_id',  $book_id, '' );
		$package_data = array();

		if ( isset( $_POST['package_id'] ) ) {
			for ( $i = 0; $i <= count( $_POST['package_id'] ) - 1; $i++ ) {
				$package_data[] = array(
					'package_id' => $_POST['package_id'][ $i ],
					'type'       => 'add',
					'total'      => $_POST['package_total'][ $i ],
				);
			}
		}


		// ========== Meta data of order booking ============

		$args_order[] = array(
			'id'              => $post_id,
			'custommer'       => wp_kses( $_POST['custommer'], '' ),
			'room_adult'      => wp_kses( $_POST['room_adult'], '' ),
			'room_child'      => wp_kses( $_POST['room_child'], '' ),
			'from'            => date( 'm/d/Y', strtotime( wp_kses( $from, '' ) ) ),
			'to'              => date( 'm/d/Y', strtotime( wp_kses( $to, '' ) ) ),
			'order_room_id'   => $room_id,
			'package_data'    => serialize( $package_data ),
			'total_price'     => wp_kses( $_POST['room_price'] + $_price, '' ),
		);
		update_post_meta( $book_id, "apb_data_order",  $args_order);
		update_post_meta( $book_id, '_order_total', $_POST['room_price'] + $_price);
		Boxes_info_booking::update_status( $book_id, $_POST['order_status'] );
		wp_update_post( array(
			'ID'	=> $post_id,
			'post_title'	=> '#' . $book_id,
			'post_status'	=> $_POST['order_status'],
		) );

		if ( isset( $_POST['order_status'] ) ) {
			switch ( $_POST['order_status'] ) {
				case 'apb-pending':
					$avt_stt = 3;
					break;
				case 'apb-completed':
					$avt_stt = 4;
					break;
				case 'apb-processing':
					$avt_stt = 3;
					break;
				case 'apb-cancelled':
					$avt_stt = 2;
					break;
			}
		}
		AWE_function::update_available( $from, $to, $room_id, $avt_stt );
		// AWE_Controller::update_day_available( $from, $to, $room_id,2 );
		// AWE_Controller::update_day_available( $from, $to, $room_id, $avt_stt );

		$url_result = sprintf(
			esc_html__( '%s to view booking.', 'awebooking' ),
			'<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $book_id ) . '&action=edit' ) ) . '">' . esc_html__( 'Click here', 'awebooking' ) . '</a>'
		);
		$url_result .= '<p id="countdow"><i>'.__( 'Reset after', 'awebooking' ).' 1s</i></p>';
		echo json_encode( $url_result );
		die;
		return false;
	}
	/**
	* Controller_add_new_item_booking - Aadd new item to bookings
	* @param mixed Request Get
	* @type Hook
	*/
	public function Controller_delete_new_item_booking() {
		if ( empty( $_POST['order_id'] ) || ! is_array( $_POST['order_id'] ) ) {
			die(); return false;
		}

		foreach ( $_POST['order_id'] as $item_id ) {
			$shop_order_id = get_post_meta( $item_id, 'posttype_shop_order_id', true );
			$order_data = get_post_meta( $shop_order_id, 'apb_data_order', true );
			// var_dump($shop_order_id, $order_data);
			if ( empty( $order_data ) ) {
				continue;
			}

			foreach ( $order_data as $key => $item_order_data ) {
				if ( $item_order_data['id'] == $item_id ) {
					$from = $item_order_data['from'];
					$to = $item_order_data['to'];
					AWE_function::update_available( $from, $to, $item_order_data['order_room_id'], 2 );
					// AWE_Controller::update_day_available( $from, $to, $item_order_data['order_room_id'], 2 );
					unset( $order_data[ $key ] );
				}
			}
			update_post_meta( $shop_order_id, 'apb_data_order', $order_data );
		}

		die;
		return false;
	}
	/**
	* Controller_delete_booking -Delete bookings
	* @param mixed Request Get
	* @type Hook
	*/
	public function Controller_delete_booking() {
	   global $wpdb;
	   if ( isset( $_POST['book_id'] ) ) {
		  self::apb_remove_booking( $_POST['book_id'] );

		  echo json_encode( 'yes' );
	   }
	  if ( isset( $_GET['book_id'] ) ) {
		self::apb_remove_booking( $_GET['book_id'] );

		wp_redirect(admin_url()."edit.php?post_type=shop_order", 301 );
	  }

	   die;
	   return false;
	}

	public function apb_remove_booking( $book_id ) {
		global $wpdb;
		$from = get_post_meta( $book_id, 'from', true );
		$to   = get_post_meta( $book_id, 'to', true );
		$order_total_item = get_post_meta( $book_id, 'apb_order_id', true );

		$book = get_post( $book_id );
		if ( 'apb-completed' == $book->post_status ) {
			if ( ! empty( $order_total_item ) ) {
				foreach ( $order_total_item as $item_id ) {
					$room_id = get_post_meta( $item_id, 'order_room_id', true );
					AWE_function::update_available( get_post_meta( $item_id, 'from', true ), get_post_meta( $item_id , 'to', true ), $room_id, 2 );
					// $this->update_day_available( get_post_meta( $item_id, 'from', true ), get_post_meta( $item_id , 'to', true ), $room_id, 2 );

					wp_update_post( array(
						'ID'		=> $item_id,
						'post_status'	=> 'trash',
					) );
				}
			}
			wp_update_post( array(
				'ID'	=> $book_id,
				'post_status'	=> 'trash',
			) );
		} else {
			if ( ! empty( $order_total_item ) ) {
				foreach ( $order_total_item as $item_id ) {
					wp_update_post( array(
						'ID'		=> $item_id,
						'post_status'	=> 'trash',
					) );
				}
			}
			wp_update_post( array(
				'ID'	=> $book_id,
				'post_status'	=> 'trash',
			) );
		}
	}

	/**
	 * Update_day_available status of room.
	 * @param from string
	 * @param to string
	 * @param room_id int
	 * @param status int
	 */
	static public function update_day_available( $from, $to, $room_id, $status, $night = 0 ) {
		_deprecated_function( __FUNCTION__, '2.0' );

		/*
		 * Update day room available
		 */
		$process         = new Process_ajax();
		$start_month     = date( 'm', strtotime( $from ) );
		$end_month       = date( 'm', strtotime( $to ) );
		$start_year      = date( 'Y', strtotime( $from ) );
		$end_year        = date( 'Y', strtotime( $to ) );
		$start_date      = date( 'd', strtotime( $from ) );
		$end_date        = date( 'd', strtotime( $to ) );

		$list_month_avb = $process->get_list_month_availability( $start_year, $end_year, $start_month, $end_month, $from, $to, $status );
		$list_update = $process->insert_availability( $list_month_avb, $room_id );
		if ( ! empty( $list_update ) ) {
			/*
			 * Update  - array > 2
			 */
			if (count( $list_update) > 2 ) {
				/*
				 * Update availability Start date
				 */
				  $process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "start");
				  $process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "center");
				  $process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "end", $night );
			} else {
				################## Update  - array <= 2 ###################

				if (count( $list_update) > 1 ) {
					/*
					 * Update availability Start date
					 */
					$process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "start");
					/*
					 * Update pring end date
					 */
					$process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "end", $night );
				}else{
					$process->list_update_availability( $list_update, $room_id, $from, $to, $status,"", "start", $night );
				}
			}
		}
	}
	/**
	* header_room_single - Layout page room
	*/
	public function header_room_single() {
		include( AWE_BK_PLUGIN_DIR . '/lib/view/header_room_single.php' );
	}
	public function validate_setting() {

		if ( $_SERVER['REQUEST_METHOD'] == "POST") {
			if ( isset( $_POST['export'] ) ) {
				$export = new Apb_BackUp();
				$export->apb_export_data();
			} elseif ( isset( $_POST['file_upload'] ) ) {

				$import = new Apb_BackUp();

				$zip = new ZipArchive();
				$import->Apb_upload_file_import( $_FILES['FileInput'] );
				$scan_file_import = scandir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/' );

				unset( $scan_file_import[0], $scan_file_import[1] );

				foreach ( $scan_file_import as $file_import ) {
					if ( is_file( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' . $file_import ) ) {
						$zip->open( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' . $file_import );
						$zip->extractTo( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' );
						$zip->close();
						unlink( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' . $file_import );
					}
				}
				wp_redirect( admin_url( 'edit.php?post_type=apb_room_type&page=apb-setting.php&up=1' ), 301 );

			} else {

				$post_param = apply_filters( 'apb_post_params', array(
				  'check_avb', 'list_room', 'rooms_booking_start_date', 'max_adult', 'max_child',
				  'max_room', 'rooms_checkout_style', 'rooms_price_calculation', 'woocommerce_currency',
				  'apb_checkout', 'label-unavailable', 'color-unavailable', 'label-available',
				  'color-available', 'label-pending', 'color-pending', 'label-complate', 'color-complate',
				  'limit-page', 'max_night', 'shortcode_form', 'datepicker_lang', 'datepicker_num_calendar',
				  'apb_currency_style', 'apb_email_base_color', 'apb_email_background_color', 'apb_email_body_background_color',
				  'apb_email_text_color', 'apb_email_from_name', 'apb_email_header', 'apb_email_footer',
				  'apb_mail_config', 'apb_mail_new_booking', 'apb_mail_pending', 'apb_mail_complete', 'apb_mail_cancel',
				  'woocommerce_currency_pos', 'woocommerce_price_thousand_sep', 'woocommerce_price_decimal_sep', 'woocommerce_price_num_decimals',
				  'apb-primary', 'apb-secondary', 'apb-background-color','apb-background-secondary','apb-highlight-color','apb-highlight-hover-color','apb-border-color',
				  'apb_custom_css', 'apb_tax_amount', 'apb_tax_type',
				) );

				foreach ( $post_param as $item_param ) {
					if ( isset( $_POST[ $item_param ] ) ) {
						$value = wp_unslash( $_POST[ $item_param ] );

						AWE_function::update_option( $item_param, $value );
					}
				}
				update_post_meta(get_option("apb_checkout"), "_apb_page_lang_default",get_option("apb_checkout") );
				update_post_meta(get_option("list_room"), "_apb_page_lang_default",get_option("list_room") );
				update_post_meta(get_option("check_avb"), "_apb_page_lang_default",get_option("check_avb") );

				/*
				 * Setting form
				 */
				$apb_setting_field_form = array(
					'field_status' =>array(
						'status_departure_sc'       => isset( $_POST['apb_setting']['field_status']['status_departure_sc'] ) ? 1 : 0,
						'status_night_number_sc'    => isset( $_POST['apb_setting']['field_status']['status_night_number_sc'] ) ? 1 : 0,
						'status_room_type_sc'       => isset( $_POST['apb_setting']['field_status']['status_room_type_sc'] ) ? 1 : 0,
						'status_multi_room_sc'      => isset( $_POST['apb_setting']['field_status']['status_multi_room_sc'] ) ? 1 : 0,
						'status_departure_page'     => isset( $_POST['apb_setting']['field_status']['status_departure_page'] ) ? 1 : 0,
						'status_night_number_page'  => isset( $_POST['apb_setting']['field_status']['status_night_number_page'] ) ? 1 : 0,
						'status_room_type_page'     => isset( $_POST['apb_setting']['field_status']['status_room_type_page'] ) ? 1 : 0,
						'status_multi_room_page'    => isset( $_POST['apb_setting']['field_status']['status_multi_room_page'] ) ? 1 : 0,
						'form_style'                => isset( $_POST['apb_setting']['field_status']['form_style'] ) ? $_POST['apb_setting']['field_status']['form_style'] : 1,
					)
				);

				AWE_function::update_option( 'apb_setting', apply_filters( 'apb_save_setting', $apb_setting_field_form, $_POST['apb_setting'] ) );

				AWE_function::update_option( 'rooms_type_selector',isset( $_POST['rooms_type_selector'] ) ? $_POST['rooms_type_selector'] : array() );

				AWE_function::update_option( '_booking_pending', isset( $_POST['_booking_pending'] ) );

				AWE_function::update_option( 'apb_show_single_calendar', isset( $_POST['apb_show_single_calendar'] ) );
				
				AWE_function::update_option( 'apb_ajax_single_calendar', isset( $_POST['apb_ajax_single_calendar'] ) );

				AWE_function::update_option( 'apb_show_remain_room', isset( $_POST['apb_show_remain_room'] ) );

				AWE_function::update_option( 'apb_deposit_type',  $_POST['apb_deposit_type']  ); 

				AWE_function::update_option( 'apb_deposit_number',  $_POST['apb_deposit_number']  ); 


				/*----------  Change template  ----------*/
				if ( isset( $_POST['template_html_code'] ) ) {
				  $this->save_template( $_POST['template_html_code'], '/apb-template/emails/'.$_POST['template_html_file'] );
				}

				$_SESSION['apb_notice'] = __( 'Update Setting Success.', 'awebooking' );
			}
		}

		if ( isset( $_GET['move_template'] ) ) {
		  AWE_function::move_template_action( $_GET['move_template'], '/apb-template/emails/' );
		  wp_redirect(admin_url( 'edit.php?post_type=apb_room_type&page=apb-setting.php' ), 301 );
		}
		if ( isset( $_GET['remove_template'] ) ) {
		  AWE_function::delete_template_action( $_GET['remove_template'], '/apb-template/emails/' );
		  wp_redirect(admin_url( 'edit.php?post_type=apb_room_type&page=apb-setting.php' ), 301 );
		}
	}
	public function Apb_import_ajax() {
		$data_import = simplexml_load_file(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/awebooking.xml' );
		$import = new Apb_BackUp();
		if ( $_POST['step'] ==2 ) {
			$import->apb_import_rooms( $data_import );
			echo 2;
		}
		/*if ( $_POST['step'] ==3) {
			$import->apb_import_bookings( $data_import );
			echo 3;
		}*/
		die;
		return false;
	}
	/**
	 * Save the email templates
	 *
	 * @since 2.4.0
	 * @param string $template_code
	 * @param string $template_path
	 */
	protected function save_template( $template_code, $template_path ) {
		if ( file_exists( get_stylesheet_directory() . $template_path . '.php' ) ) {
			$saved  = false;
			$file   = get_stylesheet_directory() . $template_path . '.php';
			$code   = stripslashes( $template_code );

			if ( is_writeable( $file ) ) {
				$f = fopen( $file, 'w+' );

				if ( false !== $f ) {
					fwrite( $f, $code );
					fclose( $f );
					$saved = true;
				}
			}
		}
	}
}

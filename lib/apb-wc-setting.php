<?php
/**
 * AWE WC Setting
 *
 * @class 		Apb_Wc_Setting
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Apb_Wc_Setting.
 */
class Apb_Wc_Setting extends Process_ajax{

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $typenow;
		add_filter( 'woocommerce_is_purchasable', array( $this, 'fix_purchased' ), 10, 2 );

		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'apb_get_cart_item_from_session' ), 10, 2 );

		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'book_save_extra_checkout_fields' ), 10, 2 );
		add_filter( 'wc_order_statuses', array( $this, 'wc_order_statuses' ), 10, 1 );

		add_filter( 'woocommerce_checkout_after_customer_details', array( $this, 'add_new_fields_checkout' ), 10, 1 );

		add_action( 'woocommerce_thankyou', array( $this, 'apb_display_order_data' ), 20 );
		add_action( 'woocommerce_view_order', array( $this, 'apb_display_order_data' ), 20 );

		add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10, 2 );
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 2 );

		add_action( 'woocommerce_review_order_before_order_total', array( $this, 'add_tax_review_order_before_order_total' ), 1 );
		add_action( 'woocommerce_review_order_before_order_total', array( $this, 'add_pre_payment_review_order' ), 1 );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'add_tax_after_calculate_totals' ) );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'get_order_item_totals' ), 10, 2 );

		add_filter( 'woocommerce_order_get_status', array( $this, 'get_order_status' ) );

		add_filter( 'woocommerce_admin_order_data_after_billing_address',  array( $this, 'apb_admin_order_data_after_billing_address' ), 20, 1 );

		add_filter( 'woocommerce_order_note_class', array( $this, 'filter_woocommerce_order_note_class' ), 10, 2 ); 
	
		// Handle paypal with wc
		require_once 'apb-paypal.php';

	}

	public function apb_admin_order_data_after_billing_address( $order ){

		$deposit = get_post_meta( $order->id, '_apb_prepayment_type', true );
		if(!empty( $deposit )){
			if( 'full' == $deposit ) {
				echo '<p><strong>' . __( 'Prepaid', 'awebooking' ) . ':</strong>  Full </p>';
			}else {
				$ex_deposit = explode( '|', $deposit );
				echo '<p><strong>' . __( 'Prepaid', 'awebooking' ) . ':</strong>  ' . $ex_deposit[0] . ' ' . $ex_deposit[1] .' </p>';
			}
			
		}
		 
	}

	public function add_cart_item( $cart_item ) {
		if ( empty( $cart_item['price'] ) ) {
			return $cart_item;
		}

		$cart_item['data']->set_price( $cart_item['price'] );
		$cart_item['quantity'] = 1;
		return $cart_item;
	}

	public function apb_get_cart_item_from_session( $cart_item, $values ) {
		if ( isset( $values['from'] ) ) {
			$cart_item['from']          = $values['from'];
			$cart_item['to']            = $values['to'];
			$cart_item['adult']         = $values['adult'];
			$cart_item['child']         = $values['child'];
			$cart_item['price']         = $values['price'];
			$cart_item['package_data']  = isset( $values['package_data'] ) ? $values['package_data'] : array();
		}

		$cart_item_data = $this->add_cart_item( $cart_item );
		return $cart_item_data;
	}

	public function book_save_extra_checkout_fields( $order_id, $posted ) {
		$data_order = WC()->cart->get_cart();
		$total_price = 0;

		$email = get_post_meta( $order_id, '_billing_email', true );
		$name = get_post_meta( $order_id, '_billing_first_name', true ) . ' ' . get_post_meta( $order_id, '_billing_last_name', true );

		update_post_meta( $order_id, '_apb_prepayment_type', $_REQUEST['apb_prepayment_type'], '' );

		$customer_id = AWE_function::maybe_create_customer( $name, $email );

		foreach ( $data_order as $item ) {
			$from = $item['from'];
			$to = $item['to'];

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

			$room_id = $item['product_id'];
			$room_id = AWE_function::get_master_post_id( $room_id );

			/*
			 * Add meta box
			 */
			$args_order[] = array(
				'id'              => $id,
				'custommer'       => $customer_id,
				'room_adult'      => $item['adult'],
				'room_child'      => $item['child'],
				'from'            => $from,
				'to'              => $to,
				'order_room_id'   => $room_id,
				'package_data'    => serialize( $item['package_data'] ),
				'total_price'     => $item['price'],
			);
			$total_price  += $item['price'];
			update_post_meta( $id, 'custommer', $customer_id );
			update_post_meta( $id, 'room_adult', $item['adult'] );
			update_post_meta( $id, 'room_child', $item['child'] );
			update_post_meta( $id, 'from', $from );
			update_post_meta( $id, 'to', $to );
			update_post_meta( $id, 'order_room_id', $room_id, '' );
			update_post_meta( $id, 'package_data', serialize( $item['package_data'] ), '' );
			update_post_meta( $id, 'posttype_shop_order_id', $order_id, '' );
			
			$start_month     = date( 'm', strtotime( $from ) );
			$end_month       = date( 'm', strtotime( $to ) );
			$start_year      = date( 'Y', strtotime( $from ) );
			$end_year        = date( 'Y', strtotime( $to ) );
			$start_date      = date( 'd', strtotime( $from ) );
			$end_date        = date( 'd', strtotime( $to ) );
			$list_month_avb  = $this->get_list_month_availability( $start_year, $end_year, $start_month, $end_month, $from, date( 'm/d/Y', strtotime( $to ) - DAY_IN_SECONDS ), 2 );
			$list_update     = $this->insert_availability( $list_month_avb, $room_id );

			################## UPDATE AVAILABILITY ##################
			if ( ! empty( $list_update ) ) {
				/*
				 *  Update  - array > 2
				 */
				if ( count( $list_update ) > 2 ) {
					/*
					 * Update availability Start date
					 */
				} else {
					################## Update  - array <= 2 ###################

					/*
					 * Update availability Start date
					 */
					$this->list_update_availability( $list_update, $room_id, $from, date( 'm/d/Y', strtotime( $to ) - DAY_IN_SECONDS ), 3, '', 'start' );

					/*
					 * Update pring end date
					 */
					if ( count( $list_update ) > 1 ) {
						 $this->list_update_availability( $list_update, $room_id, $from, date( 'm/d/Y', strtotime( $to ) - DAY_IN_SECONDS ), 3, '', 'end', 1 );
					}
				}
			}
		}

		update_post_meta( $order_id, 'custommer', $customer_id );
		update_post_meta( $order_id, 'from', $args_order[0]['from'] );
		update_post_meta( $order_id, 'to', $args_order[0]['to'] );
		update_post_meta( $order_id, 'apb_order_id', $apb_order_id );
		update_post_meta( $order_id, 'apb_data_order', $args_order );
		update_post_meta( $order_id, 'order_type', 1 );
		update_post_meta( $order_id, '_pre_payment_type', get_option( 'apb_deposit_type' ) );
		update_post_meta( $order_id, '_deposit_payment', get_option( 'apb_deposit_number' ) );
	}


	public function apb_display_order_data( $order_id ) {
		$sendMailStatus = get_post_meta( $order_id, '_apb_send_mail_status', true );
		
		if( 1 != $sendMailStatus ) {
			$this->apb_sendmail( $order_id );
			update_post_meta($order_id,'_apb_send_mail_status',1);
		}

		$status = 'apb-pending';
		$apb_deposit_type = get_option( 'apb_deposit_type' );
		if ( isset($_GET['utm_nooverride'] ) ) {
			$status = 'apb-completed';
			update_post_meta( $order_id, '_apb_payment_success', 1 );
			$int_status = 4;
		} else {
			$status = 'apb-pending';
			$int_status = 3;
		} 

		$order = wc_get_order( $order_id );
		$my_order = array(
			'ID'           =>  $order_id,
			'post_title'   => '#'. $order_id,
			'post_status'  => $status,
		);
		wp_update_post( $my_order );
		$total_order_room = get_post_meta( $order_id, 'apb_data_order', true );
		?>

		<h2><?php esc_html_e( 'Booking Details', 'awebooking' ); ?></h2>
		<?php
		foreach ( $total_order_room as $item_book ) :
			$room_info = get_post( $item_book['order_room_id'] );
			AWE_function::update_available( $item_book['from'], $item_book['to'], $item_book['order_room_id'], $int_status );
			?>
			<ul class="order_details">
				<li class="order">
					<?php esc_html_e( 'Room:', 'awebooking' ); ?> <strong><?php echo esc_html( get_the_title( $room_info->post_parent ) ); ?></strong>
				</li>

				<li class="date">
					<?php esc_html_e( 'Booking duration:', 'awebooking' ); ?> <strong><?php echo date_i18n( AWE_function::get_current_date_format(), strtotime( $item_book['from'] ) ) ?> - <?php echo date_i18n( AWE_function::get_current_date_format(), strtotime( $item_book['to'] ) ) ?> </strong>
				</li>
				<li class="total">
					<?php esc_html_e( 'Numbers of Guests', 'awebooking' ); ?>
					<strong>
						<?php printf( esc_html__( 'Adult: %s', 'awebooking' ), absint( $item_book['room_adult'] ) ); ?>
						<?php printf( esc_html__( 'Child: %s', 'awebooking' ), absint( $item_book['room_child'] ) ); ?>
					</strong>
				</li>
				<li class="method">
					<?php esc_html_e( 'Price', 'awebooking' ); ?>
					<strong><span class="amount"><?php echo AWE_function::apb_price( $item_book['total_price'] ) ?></span></strong>
				</li>
			</ul>
		<?php endforeach;
	}

	public function apb_sendmail( $order_id ) {
		$order = wc_get_order( $order_id );
		/*----------  Send mail after checkout  ----------*/
		$config_mail = AWE_function::get_option( 'apb_mail_new_booking', $order_id );
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

				$message_admin = '<h2>' . esc_html__( 'Orders Info', 'awebooking' ) . '</h2>';
				$message_admin .= $order->get_formatted_billing_address();
				$message_admin .= $message;
				$message_admin = $email->apb_style_inline( $email->apb_wrap_message( $heading, $message_admin ) );
				$email->apb_sendMail( $email_admin, $subject, $message_admin, 1 );

			}
		}
		$email->destroy();

		/* Send pending email */
		$config_mail = AWE_function::get_option( 'apb_mail_pending' );
		$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : 'Your {site_title} booking receipt from {order_date}';
		$subject = AWE_function::email_str_replace( $subject, $order_id );
		$heading = ! empty( $config_mail['header'] ) ? $config_mail['header'] : 'Thank you for your booking';
		$heading = AWE_function::email_str_replace( $heading, $order_id );

		ob_start();
		do_action( 'apb_mail_pending_order', $order_id );
		$message       = ob_get_clean();
		$email         = new APB_Email();

		if ( isset( $config_mail['user_notice_status'] ) && 1 == $config_mail['user_notice_status'] ) {
			$message_user      = $email->apb_style_inline( $email->apb_wrap_message( $heading, $message ) );
			$email->apb_sendMail( $order->billing_email, $subject, $message_user, 0, $order->billing_first_name );
		}
		$email->destroy();

		//AWE_function::delete_cart();
	}

	/**
	 * Set order wc alway true.
	 */
	public function fix_purchased( $purchasable, $product ) {
		return $purchasable = true;
	}

	public function wc_order_statuses( $order_statuses ) {
		unset( $order_statuses['wc-on-hold'] );
		$order_statuses_new = array(
			'apb-pending'    => _x( 'Pending Payment', 'Order status', 'awebooking' ),
			'apb-processing' => _x( 'Processing', 'Order status', 'awebooking' ),
			'apb-completed'  => _x( 'Completed', 'Order status', 'awebooking' ),
			'apb-cancelled'  => _x( 'Cancelled', 'Order status', 'awebooking' ),
		);

		return array_merge( $order_statuses_new, $order_statuses );
	}

	public function add_new_fields_checkout() {
		$deposit_number 	= get_option( 'apb_deposit_number' );
		$apb_deposit_type 	= get_option( 'apb_deposit_type' );
		$money_type 		= ( 'money' == $apb_deposit_type ) ? AWE_function::get_option( 'woocommerce_currency' ) : '%';
		?>
		<div class="col2-set">
			<div class="col-1">
				<p class="form-row form-row form-row-wide address-field update_totals_on_change validate-required woocommerce-validated" id="billing_apbpay_field">
					<label  class=""><?php _e( 'Prepayment type', 'awebooking' ); ?></label>
					<select name="apb_prepayment_type"> 
						<option value="full"> <?php _e( 'Pay full', 'awebooking' ); ?> </option>
						<option value="<?php echo esc_attr( $deposit_number . '|' . $money_type ) ?>"> <?php echo __( 'Prepaid ', 'awebooking' ) . $deposit_number . ' ' . $money_type; ?> </option>
					</select>
				</p>
			</div>
		</div>
		<div class="apb-room-selected_content">
			<?php do_action( 'apb_loop_item_cart_info' ); ?>
		</div>
		<?php
	}


	/**
	 * Display room type name as cart item name.
	 *
	 * @param  string $name      Room name.
	 * @param  array  $cart_item Cart item.
	 * @return string
	 *
	 * @since 2.2
	 */
	public function cart_item_name( $name, $cart_item ) {
		if ( empty( $cart_item['room_id'] ) ) {
			return $name;
		}

		$room_type_id = wp_get_post_parent_id( $cart_item['room_id'] );

		return get_the_title( $room_type_id );
	}


	/**
	 * Hide cart item quantity.
	 *
	 * @param  string $output    Cart item quantity output.
	 * @param  array  $cart_item Cart item.
	 * @return string
	 *
	 * @since 2.2
	 */
	public function cart_item_quantity( $output, $cart_item ) {
		if ( empty( $cart_item['room_id'] ) ) {
			return $output;
		}

		return '';
	}


	/**
	 * Add tax row to order review table.
	 *
	 * @since 2.2.1
	 */
	public function add_tax_review_order_before_order_total() {
		if ( get_option( 'apb_tax_amount' ) ) {
			$apb_cart = AWE_function::get_cart();
			?>
			<th><?php esc_html_e( 'Tax', 'awebooking' ); ?></th>
			<td><?php echo wp_kses_post( AWE_function::get_displayed_tax( $apb_cart['total'] ) ); ?></td>
			<?php
		}
	}


	/**
	 * Add pre-payment to review order table
	 *
	 * @since 2.8
	 */
	public function add_pre_payment_review_order() {
		$apb_deposit_type = get_option( 'apb_deposit_type' );
		$total = $_SESSION['apb_cart']['total'];
		if ( ! empty( $apb_deposit_type ) && 'none' != get_option( 'apb_deposit_type' ) ) {
			$a = AWE_function::calculate_pre_payment( $total );
			?>
			<tr>
				<th><?php esc_html_e( 'Pre-payment', 'awebooking' ); ?></th>
				<td><?php echo esc_html( AWE_function::apb_price( $a ) ) ?></td>
			<tr/>
			<tr>
				<th><?php esc_html_e( 'Remain', 'awebooking' ); ?></th>
				<td><?php echo esc_html( AWE_function::apb_price( $total - $a ) )?></td>
			</tr>
			<?php
		}
	}


	/**
	 * Filter to cart total.
	 *
	 * @param object $object WC_Cart object.
	 * @since 2.2.1
	 */
	public function add_tax_after_calculate_totals( $object ) {
		$apb_cart = AWE_function::get_cart();

		if ( empty( $apb_cart['total'] ) ) {
			return;
		}

		$object->total += ( AWE_function::calculate_tax( $apb_cart['total'] ) - $apb_cart['total'] );
	}


	/**
	 * Filter to order detail table rows.
	 *
	 * @param array  $total_rows Rows.
	 * @param object $order Order object.
	 * @return array
	 * @since 2.2.1
	 */
	public function get_order_item_totals( $total_rows, $order ) {
		$old_rows = $total_rows;

		if ( get_option( 'apb_tax_amount' ) ) {
			$order_id = $order->id;
			$index = array_search( 'payment_method', array_keys( $total_rows ) );

			$total_ex_tax = get_post_meta( $order_id, 'total_ex_tax', true );
			$tax_amount = get_post_meta( $order_id, 'tax_amount', true );
			$tax_type = get_post_meta( $order_id, 'tax_type', true );

			$tax_row = array(
				'awebooking-tax' => array(
					'label'	=> __( 'Tax', 'awebooking' ),
					'value'	=> AWE_function::get_displayed_tax( $total_ex_tax, $tax_amount, $tax_type ),
				),
			);

			$total_rows = array_slice( $old_rows, 0, $index, true ) + $tax_row + array_slice( $old_rows, $index, null, true );
		}

		return $total_rows;
	}


	public static function get_order_status( $status ) {
		switch ( $status ) {
			case 'apb-pending':
				$status = _x( 'Pending Payment', 'Order status', 'awebooking' );
				break;

			case 'apb-processing':
				$status = _x( 'Processing', 'Order status', 'awebooking' );
				break;

			case 'apb-completed':
				$status = _x( 'Completed', 'Order status', 'awebooking' );
				break;

			case 'apb-cancelled':
				$status = _x( 'Cancelled', 'Order status', 'awebooking' );
				break;
		}

		return $status;
	}

	public function filter_woocommerce_order_note_class( $array, $note ) { 
		
	    return $array; 
	}


}

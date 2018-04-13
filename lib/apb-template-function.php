<?php
/**
 * The template for displaying content in the plugin template
 *
 * @author      AweTeam
 * @package 	AweBooking/function
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/* ============================== Template Page Check Available =====================
 *  Page Check Available layout
 * ======================================================================= */

function form_check_availability() {

	$apb_setting   = get_option( 'apb_setting' );
	$from          = isset( $_GET['from'] ) ? $_GET['from'] : '';
	$to            = isset( $_GET['to'] ) ? $_GET['to'] : '';
	$room_type_id  = isset( $_GET['room_type_id'] ) ? absint( $_GET['room_type_id'] ) : 0;
	if ( ! empty( $from ) && ! empty( $to ) ) {
		$_from = AWE_function::convert_date_to_mdY( $from );
		$_to = AWE_function::convert_date_to_mdY( $to );
		$total_night = AWE_function::get_number_of_nights( $_from, $_to );
		$total_day = $total_night + 1;
	} else {
		$total_day = 0;
		$total_night = 0;
	}

	include AWE_function::template_exsits( 'layout/apb-form-check-availability' );
}

/*
 * Layout step for header plugin.
 */
function html_step( $active = '' ) {
   include AWE_function::template_exsits( 'layout/apb-step' );
}

function loop_data_check_availability() {
	include AWE_function::template_exsits( 'loop/apb-list-check-availability' );
}

function layout_room_select() {
 	$total_cart = count( AWE_function::get_cart() )-1;
	if( $total_cart < 0 ) {
		$total_cart = 0;
	}
	$room_adult  = isset( $_POST['room_adult'] ) ? $_POST['room_adult'] : 1;
	$room_child  = isset( $_POST['room_child'] ) ? $_POST['room_child'] : 0;
	include AWE_function::template_exsits( 'layout/apb-room-select' );
}

function layout_review_book() {
	include AWE_function::template_exsits( 'layout/apb-review_book' );
}

function layout_list_review_book() {
	$i = 1;
	$total_price = 0;
	$apb_cart = AWE_function::get_cart();

	if ( ! empty( $apb_cart ) ) {
		foreach ( $apb_cart as $key => $item ) {
			if ( empty( $item['room_id'] ) ) {
				continue;
			}

			$room_num   = $i++;
			$room_type_id = wp_get_post_parent_id( $key );
			$room_type  = get_post( $room_type_id );
			$range_date = AWE_function::range_date( $item['from'], $item['to'] );
			$total_night  = count( $range_date ) - 1;
			$room_price = get_post_meta( $room_type_id, 'base_price', true );

			$price = $item['price'];

			$total_price += $price;

			$room = get_post( $item['room_id'] );

			include AWE_function::template_exsits( 'loop/apb-list-review-book' );
		}
	}
}

function page_checkout() {
	$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
	include AWE_function::template_exsits( 'apb-checkout' );
}

function layout_list_package( $argsData = array() ) {
	$argsData['check'] = isset( $argsData['check'] ) ? $argsData['check'] : true;
	$argsData['count_day'] = isset( $argsData['count_day'] ) ? $argsData['count_day'] : 1;
	$get_option = AWE_function::get_room_option( get_the_ID(), 'apb_room_type' );
	if ( ! empty( $get_option ) ) {
		include AWE_function::template_exsits( 'loop/apb-list-package' );
	}
}

/*
 * Query filter room available
 * Loop and check room ajax.
 */
function loop_content_check_available() {
	$from = wp_kses( $_POST['from'] ,array() );
	$to   = wp_kses( $_POST['to'], array() );
	$year = date( 'Y' );
	$num_args = absint( $_POST['num_args'] );
	$room_adult = absint( $_POST['room_adult'] );
	$room_child = absint( $_POST['room_child'] );

	Apb_show_all_room( $from, $to, $year, 'all', $num_args, $room_adult, $room_child );
}


function body_check_available() {
	if(isset( $_GET['action'] ) && $_GET['action'] == 'filter_available' ) {
	  $roomtype = isset( $_GET['roomtype'] ) ? wp_kses( $_GET['roomtype'], '' ) : "all";
	  query_room_filter(wp_kses( $_GET['from'],""),wp_kses( $_GET['to'],""),$roomtype,wp_kses( $_GET['room_adult'],""),wp_kses( $_GET['room_child'],"") );
	}else{
	   include AWE_function::template_exsits("layout/apb-check-calendar");
	}

}


function check_avb_pagination( $room) {
	include AWE_function::template_exsits( 'loop/apb-check_pagination' );
}

function layout_loading() {
	include AWE_function::template_exsits( 'layout/apb-loading' );
}

/*----------  Loop price by show all room  ----------*/
/**
 * Display room price.
 *
 * @param string $room_price   Room price.
 * @param int    $room_type_id Room type id.
 * @since 2.2.1  Remove $show param, add $room_type_id param.
 */
function loop_price( $price, $room_type_id ) {
	include AWE_function::template_exsits( 'loop/apb-price' );
}


/**
 * Display total price.
 *
 * @param  float $price        Price.
 * @param  int   $room_type_id Room type ID.
 * @since 2.2.1
 */
function loop_total_price( $price, $room_type_id ) {
	include AWE_function::template_exsits( 'loop/apb-total-price' );
}

/**
 * Element result data from ajax select room.
 */
function fill_content_js() {
	echo apply_filters( 'apb_fill_list_room', '<div class="room-select-js"></div>' );
}

/**
 * General field check number of people.
 */
function general_field_check_people( $type = 'multi' ) {
	$total_num = isset( $_GET['room_num'] ) ? absint( $_GET['room_num'] ) : 1;
	$room_adult = isset( $_GET['room_adult'] ) ? wp_kses_post( wp_unslash( $_GET['room_adult'] ) ) : array( 1 );
	$room_child = isset( $_GET['room_child'] ) ? wp_kses_post( wp_unslash( $_GET['room_child'] ) ) : array( 0 );

	echo wp_kses_post( apply_filters( 'apb_general_field_check_people_before', '<div class="list-room">' ) );
	for ( $i = 1; $i <= $total_num; $i++ ) {
		include AWE_function::template_exsits( 'loop/apb-general-field-check-people' );
	}
	echo wp_kses_post( apply_filters( 'apb_general_field_check_people_after', '</div>' ) );
}

/**
 * Element result data from ajax select room.
 */
function get_content_select_room() {
	$_room = 1;
	$total_cart = count( AWE_function::get_cart() );
	$_i = 0;
	if ( is_array( AWE_function::get_cart() ) ) {
		foreach ( AWE_function::get_cart() as $key_item => $item_cart ) {
			if ( isset( $item_cart['room_id'] ) ) {
				$t_i = $_i++;
				$room_info = get_post( $item_cart['room_id'] );
				$room_type_id = wp_get_post_parent_id( $item_cart['room_id'] );
				$room_type = get_post( $room_type_id );
				if ( isset( $_POST['room_adult'][ $t_i ]['adult'] ) ) {
					$total_day = count( AWE_function::range_date( $item_cart['from'], $item_cart['to'] ) ) - 1;
					include AWE_function::template_exsits( 'loop/apb-content-room-select' );
				}
			}
		}
	}
}

/**
 * Before content to javascript.
 */
function apb_room_content_before() {
	include AWE_function::template_exsits( 'room/apb-room_content_before' );
}


/**
 * After content to javascript.
 */
function apb_room_content_after() {
	include AWE_function::template_exsits( 'room/apb-room_content_after' );
}

/**
 * Room selected content.
 * @return void
 */
function loop_content_room_select() {
	$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
	$_room = 1;

	$cart = AWE_function::get_cart();
	// var_dump($cart);
	$total_cart = count( $cart );
	$_i = 0;
	if ( is_array( $cart ) ) {
		foreach ( $cart as $key_item => $item_cart ) {
			if ( empty( $item_cart['room_id'] ) ) {
				continue;
			}

			$t_i = $_i++;
			$room_info = get_post( $item_cart['room_id'] );
			$room_type_id = wp_get_post_parent_id( $item_cart['room_id'] );
			$room_type = get_post( $room_type_id );
			if ( isset( $_POST['room_adult'][ $t_i ]['adult'] ) ) {
				$total_day = count( AWE_function::range_date( $item_cart['from'], $item_cart['to'] ) ) - 1;
				if ( isset( $_POST['key'] ) && $_POST['key'] == $key_item) {
					?>
					<!-- CURRENT -->
					<div class="apb-room-seleted_current apb-bg_blue">
						<h6><?php printf( esc_html__( 'You are changing room %s', 'awebooking' ), absint( $_POST['num_args'] ) + 1 ); ?></h6>
						<span>
							<?php printf( esc_html__( '%s Adult', 'awebooking' ), $_POST['room_adult'][$_POST['num_args']]['adult'] ); ?>,
							<?php printf( esc_html__( '%s Child', 'awebooking' ), $_POST['room_child'][$_POST['num_args']]['child'] ); ?>
						</span>
					</div>
					<!-- CURRENT -->
					<?php
				} else {
					include AWE_function::template_exsits( 'loop/apb-content-room-select' );
				}
			}
		}
	}
}

function apb_notice_available() {
	include AWE_function::template_exsits( 'loop/apb-notice_check_available' );
}

/**
 * Template Post Type room.
 * @param array $argsData Args.
 */
function loop_single_package( $argsData = array( 'count_day' => 1 ) ) {
	$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
	include AWE_function::template_exsits( 'room/loop/apb-room-single-package' );
}

function loop_price_single( $room_price = '' ) {
	$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
	include AWE_function::template_exsits( 'room/loop/apb-room-single-price' );
}

function apb_room_type_availability_calendar( $room_type_id ) {
	$month_current = isset( $_GET['apb_mon'] ) ? $_GET['apb_mon'] + 2 : 2;

	$_date_curent = date_create( date( 'Y-m-d' ) );

	// Next/prev calendar by ajax.
	$ajax_single_calendar = get_option( 'apb_ajax_single_calendar' );

	date_add( $_date_curent, date_interval_create_from_date_string( ( isset( $_GET['apb_mon'] ) ? $_GET['apb_mon'] : '0' ) . ' month' ) );

	$new_date = date_format( $_date_curent, 'Y-m-d' );

	$month_sub = isset( $_GET['apb_mon'] ) ? $_GET['apb_mon'] - 2 : '-2';

	if ( date( 'm', strtotime( $new_date ) ) == 12 ) {
		$_year = date( 'Y', strtotime( $new_date ) ) + 1;
	} else {
		$_year = date( 'Y', strtotime( $new_date ) );
	}

	$next = add_query_arg(
		array(
			'apb_mon'	=> $month_current,
			'apb_year'	=> date( 'Y', strtotime( $new_date ) ),
			'room_id'	=> $room_type_id,
		),
		get_permalink()
	);

	$prev = add_query_arg(
		array(
			'apb_mon'	=> $month_sub,
			'apb_year'	=> date( 'Y', strtotime( $new_date ) ),
			'room_id'	=> $room_type_id,
		),
		get_permalink()
	);
	?>

	<script>
		var awe_date_curent_1  = '<?php echo date( 'Y', strtotime( $new_date ) ); ?>-<?php echo date( 'm', strtotime( $new_date ) ); ?>';
		var awe_date_curent_2  = '<?php echo $_year; ?>-<?php echo date( 'm', strtotime( date( 'Y', strtotime( $new_date ) ) . '-' . ( date( 'm', strtotime( $new_date ) ) + 1 ) ) ); ?>';
		var room_id = <?php echo absint( $room_type_id ) ?>
	</script>
	<div id="preloader" class="apb-loading" style=" display: none; ">
	    <span>Loading</span>
	</div>
	<div class="apb-month">
		<?php if($ajax_single_calendar != 1): ?>
		<a href="<?php echo esc_html( $prev ); ?>#date-available" class="apb-fc-nav apb-fc-prev" type="button"><?php esc_html_e( 'Prev', 'awebooking' ); ?></a>
	 	<?php endif; ?>
		 <div id="calendar"> </div>
	</div>

	<div class="apb-month">
		<?php if($ajax_single_calendar != 1): ?>
		<a href="<?php echo esc_html( $next ); ?>#date-available" class="apb-fc-nav apb-fc-next" type="button"><?php esc_html_e( 'Next', 'awebooking' ); ?></a>
		<?php endif; ?>
		<div id="calendar2"> </div>
	</div>
	<?php
}

/**
 * Form check available of post type room
 */
function form_check_available_single() {
	include AWE_function::template_exsits( 'room/apb-form-check-available-single' );
}

function loop_content_list_room() {
	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$room  = new WP_Query( apply_filters( 'apb_list_room_query_args', array(
		'post_type'       => 'apb_room_type',
		'posts_per_page'  => get_option( 'limit-page' ),
		'paged'           => $paged,
	) ) );

	do_action( 'apb_before_list_room' );

	while ( $room->have_posts() ) : $room->the_post();
		$room_price    = get_post_meta( get_the_ID(), 'base_price', true );
		$room_desc     = get_post_meta( get_the_ID(), 'room_desc', true );
		$max_sleeps    = get_post_meta( get_the_ID(), 'max_sleeps', true );
		$min_sleeps    = get_post_meta( get_the_ID(), 'min_sleeps', true );
		$max_children  = get_post_meta( get_the_ID(), 'max_children', true );
		$min_children  = get_post_meta( get_the_ID(), 'min_children', true );
		include AWE_function::template_exsits( 'room/apb-content-list-room' );
	endwhile;

	do_action( 'apb_after_list_room' );

	list_room_pagination( $room );
}

function list_room_pagination( $room ) {
	include AWE_function::template_exsits( 'room/apb-list-room-pagination' );
}

function single_gallery() {
	$room_gallery = get_post_meta( get_the_ID(), 'apb_gallery', true );
	include AWE_function::template_exsits( 'loop/apb-single-gallery' );
}

/* ============================== Template Check out =====================
 *  Checkout layout
 * ======================================================================= */

function loop_item_cart_info() {
	global $post,$wp;
	$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
	$i = 1;
	$apb_cart = AWE_function::get_cart();
	$order_id = '';
	$prepayment_type = '';
	if ( ! empty( $apb_cart ) ) {
		foreach ( $apb_cart as $item ) {
			if ( empty( $item['room_id'] ) ) {
				continue;
			}

			$room_num     = $i++;
			$room_id = absint( $item['room_id'] );
			$room = get_post( $room_id );
			$room_type_id = wp_get_post_parent_id( $item['room_id'] );
			$room_type    = get_post( $room_type_id );
			$range_date   = AWE_function::range_date( $item['from'], $item['to'] );
			$total_day    = count( $range_date ) - 1;
			$room_price   = get_post_meta( $room_type->ID, 'base_price', true );
			$base_price_for = get_post_meta( $room_type->ID, 'base_price_for', true );
			$extra_guess_data = AWE_function::get_extra_guest_data( $base_price_for, $item['adult'], $item['child'] );
			$extra_price_data = AWE_function::get_extra_price_data( $room_type->ID, $item['adult'], $item['child'] );
			if( isset( $wp->query_vars['order-received'] ) && NULL != $wp->query_vars['order-received'] ) {
				$order_id = $wp->query_vars['order-received'];
				$prepayment_type = get_post_meta( $order_id, '_apb_prepayment_type', true );
			}
			
			

			$price = $item['price'];
			include AWE_function::template_exsits( 'checkout/loop/apb-item_cart_info' );
		}

		if ( $apb_cart['total'] ) {
			include AWE_function::template_exsits( 'checkout/loop/apb-item-cart-total' );
		}
	}
}


/*================================================================
=            Display layout room current booking detail          =
=================================================================*/

function apb_gen_room_select() {
	ob_start();
	$total_num = isset( $_POST['number'] ) ? absint( $_POST['number'] ) : 3;
	$type = 'multi';
	for ( $i = 1; $i <= $total_num; $i++ ) {
		include AWE_function::template_exsits( 'loop/apb-general-field-check-people' );
	}
	$html = ob_get_clean();
	echo $html;
	die;
	return false;
}
add_action( 'wp_ajax_apb_gen_room_select', 'apb_gen_room_select' );
add_action( 'wp_ajax_nopriv_apb_gen_room_select', 'apb_gen_room_select' );

/*=====  End of Display layout room current booking  ===========*/



/*================================================
=            Render wraper Awebooking            =
================================================*/

function Apb_renderBefore() {
	ob_start();
}

function Apb_renderAfter() {
	printf( '<div id="%s" class="awebooking">%s</div>', apply_filters( 'awe_element_id', 'awe-plugin-booking' ), ob_get_clean() );
}
/*=====  End of Render wraper Awebooking  ======*/



/*==========================================
=            Template shortcode            =
==========================================*/

function apb_shortcode_check_available( $apb_setting = '' ) {
	if ( empty( $apb_setting ) ) {
		$apb_setting = array(
			'departure'     => 1,
			'night'         => 1,
			'room_type'     => 1,
			'mullti_room'   => 1,
			'style'         => 1,
			'tmp_file'      => '',
		);
	}
	if ( empty( $apb_setting['tmp_file'] ) ) {
		include AWE_function::template_exsits( 'shortcode/apb_check_available/apb-shortcode-check-availability' );
	} else {
		include AWE_function::template_exsits( 'shortcode/apb_check_available/' . $apb_setting['tmp_file'] );
	}
}

function apb_get_day_advance() {
	echo '<script>
		var apb_day_book = "' . esc_js( get_option( 'rooms_booking_start_date' ) ) . '";
	</script>';
}

function apb_shorcode_rooms( $apb_attr = '' ) {
	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$room = new WP_Query( array(
		'post_type'       => 'apb_room_type',
		'posts_per_page'  => get_option( 'limit-page' ),
		'paged'           => $paged,
	) );

	while ( $room->have_posts() ) : $room->the_post();
		$room_price    = get_post_meta( get_the_ID(), 'base_price', true );
		$room_desc     = get_post_meta( get_the_ID(), 'room_desc', true );
		$max_sleeps    = get_post_meta( get_the_ID(), 'max_sleeps', true );
		$min_sleeps    = get_post_meta( get_the_ID(), 'min_sleeps', true );
		$max_children  = get_post_meta( get_the_ID(), 'max_children', true );
		$min_children  = get_post_meta( get_the_ID(), 'min_children', true );
		include AWE_function::template_exsits( 'shortcode/apb_rooms/apb-shortcode-display-rooms' );
	endwhile;
	list_room_pagination( $room );
}

/*=====  End of Template shortcode  ======*/


/*=====================================
=            Template mail            =
=====================================*/

/**
 * Mail for admin when new order.
 *
 * @return void
 * @since 1.11
 */
function apb_mail_new_booking( $order_id ) {
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );
	$i = 1;
	include AWE_function::template_exsits( 'emails/apb-customer-new-booking' );
}

/**
 * Mail when order is created.
 *
 * @return void
 * @since 1.11
 */
function apb_mail_pending_order( $order_id ) {
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );
	$i = 1;
	include AWE_function::template_exsits( 'emails/apb-customer-pending-order' );
}

/**
 * Mail when order is completed.
 *
 * @param  int $order_id Order ID.
 * @return void
 * @since 1.0
 */
function apb_mail_complete_order( $order_id ) {
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );
	$i = 1;
	include AWE_function::template_exsits( 'emails/apb-customer-completed-order' );
}

/**
 * Mail when order is cancelled.
 *
 * @param  int $order_id Order Id.
 * @return void
 * @since 1.0
 */
function apb_mail_cancelled_order( $order_id ) {
	$order_data = get_post_meta( $order_id, 'apb_data_order', true );
	$i = 1;
	include AWE_function::template_exsits( 'emails/apb-customer-cancel-order' );
}


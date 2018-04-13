<?php
/**
 * The hook for displaying content in the plugin template
 *
 * @author      AweTeam
 * @package 	AweBooking/Hook
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



/* ============================== Hook Template Page Check Available ========
 *  Hook Page Check Available layout
 * ======================================================================= */
add_action( 'form_check_availability', 'form_check_availability' );
add_action( 'form_step', 'html_step' );
add_action( 'loop_data_check_availability', 'loop_data_check_availability' );
add_action( 'layout_room_select', 'layout_room_select' );
add_action( 'apb_layout_review_book', 'layout_review_book' );
add_action( 'apb_layout_list_review_book', 'layout_list_review_book' );
add_action( 'apb_page_checkout',"page_checkout");
add_action( 'layout_loading', 'layout_loading' );
add_action( 'apb_layout_list_package', 'layout_list_package' );
add_action( 'apb_loop_book', 'loop_book' );
// add_action( 'apb_loop_book_room_type', 'loop_book_room_type' );
add_action( 'apb_loop_content_check_available', 'loop_content_check_available' );
add_action( 'apb_fill_content_js', 'fill_content_js' );
add_action( 'apb_loop_content_room_select', 'loop_content_room_select' );
add_action( 'apb_notice_available', 'apb_notice_available' );
add_action( 'apb_body_check_available', 'body_check_available' );
add_action( 'apb_get_content_select_room', 'get_content_select_room' );
add_action( 'apb_general_field_check_people', 'general_field_check_people' );
/*
 *  Before and after content to javascript
 */
add_action( 'apb_room_content_before', 'apb_room_content_before' );

add_action( 'apb_room_content_after', 'apb_room_content_after' );



/* ============================== Hook Template Post Type room ============
 * Hook Post type room layout
 * ======================================================================= */
add_action( 'apb_loop_single_package', 'loop_single_package' );
add_action( 'apb_form_check_available_single', 'form_check_available_single' );
add_action( 'apb_loop_content_list_room', 'loop_content_list_room' );
add_action( 'apb_single_message', 'single_message' );
add_action( 'apb_single_gallery', 'single_gallery' );
add_action( 'apb_room_type_availability_calendar', 'apb_room_type_availability_calendar' );

/* ============================== Hook Template Post Type room_type =====================
 * Hook Post type room_type layout
 * ======================================================================= */
add_action( 'apb_form_check_available_room_type', 'form_check_available_room_type' );
add_action( 'apb_loop_package_room_type_single', 'loop_package_room_type_single' );
add_action( 'apb_loop_package_room_type', 'loop_package_room_type' );
add_action( 'apb_room_type_single_gallery', 'room_type_single_gallery' );
/* ============================== Hook Template Check out =====================
 * Hook Checkout layout
 * ======================================================================= */
add_action( 'apb_loop_item_cart_info', 'loop_item_cart_info' );


/*===============================================
=            Template shortcode hook            =
===============================================*/
add_action( 'apb_short_check_available', 'apb_shortcode_check_available' );
add_action( 'apb_get_day_advance', 'apb_get_day_advance' );
add_action( 'apb_shorcode_rooms', 'apb_shorcode_rooms' );
/*=====  End of Template shortcode hook  ======*/


/*================================================
=            Render wraper Awebooking            =
================================================*/
add_action( 'apb_renderAfter', 'Apb_renderAfter' );
add_action( 'apb_renderBefore', 'Apb_renderBefore' );
/*=====  End of Render wraper Awebooking  ======*/

/*==========================================
=            Hook template mail            =
==========================================*/

add_action( 'apb_mail_new_booking', 'apb_mail_new_booking' );
add_action( 'apb_mail_pending_order', 'apb_mail_pending_order' );
add_action( 'apb_mail_complete_order', 'apb_mail_complete_order' );
add_action( 'apb_mail_cancelled_order', 'apb_mail_cancelled_order' );
/*=====  End of Hook template mail  ======*/


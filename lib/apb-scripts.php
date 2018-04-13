<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Load Script Auloload
 *
 * @class 		AWE_function
 * @version		1.0
 * @package		AweBooking/function/
 * @author 		AweTeam
 */

add_action( 'admin_enqueue_scripts', 'awe_load_script', 1000000000 );
add_action( 'admin_enqueue_scripts', 'awe_load_style', 1000000000 );

function awe_load_script() {
	if ( ! is_admin() ) {
		return;
	}

	$get_lang = get_query_var( 'lang' );
	$datepicker_lang = AWE_function::get_datepicker_lang();
	$fc_lang = AWE_function::lang_code_datepicker_to_fullcalendar( $datepicker_lang );

	global $typenow;
	$post_type = array( 'apb_room_type', 'shop_order', 'room_type' );
	$action = array( 'avb', 'pricing', 'room_type', 'manage_avb', 'manage_pricing', 'edit_room_type', 'add_room_type' );
	if ( in_array( $typenow, $post_type ) || ( isset( $_GET['action'] ) && in_array( $_GET['action'], $action ) ) ) {
		wp_enqueue_script( 'moment', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-moment.min.js', false, '2.11.0', true );
		wp_enqueue_script( 'awe-fullcalendar', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-fullcalendar.min.js',false, '2.6.1', true );
		if ( ! empty( $fc_lang ) ) {
			wp_enqueue_script( 'awe-fullcalendar-lang', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/full-calendar-lang/' . $fc_lang . '.js', false, AWE_VERSION, true );
		}
		wp_enqueue_script( 'apb-js-jquery-ui', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/jquery-ui.min.js', array( 'jquery' ), AWE_VERSION, true );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'apb-jquery-tipTip', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/jquery.tipTip.js', array( 'jquery' ), AWE_VERSION, true );
		wp_enqueue_script( 'apb-scripts', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-scripts.js', array( 'jquery', 'jquery-ui-core', 'wp-color-picker' ), AWE_VERSION, true );
		wp_enqueue_script( 'apb-room-gallery', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-gallery.js', '', AWE_VERSION, true );
		wp_enqueue_script( 'apb-form-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js', '', AWE_VERSION, true );

		if ( ! empty( $get_lang ) ) {
			wp_enqueue_script( 'apb-js-ui-i18', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/ui-i18/' . AWE_function::lang_code_wpml_to_datepicker( $get_lang ) . '.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
		} else {
			$ui_i18 = AWE_function::get_datepicker_lang();
			if ( ! empty( $ui_i18 ) && 'default' != $ui_i18 ) {
				wp_enqueue_script( 'apb-js-ui-i18', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/ui-i18/' . $ui_i18 . '.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
			} else {
				wp_enqueue_script( 'apb-js-ui-i18', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/ui-i18/datepicker-en-US.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
			}
		}

		wp_localize_script( 'apb-scripts', 'ApbBackend', array(
			'ajaxUrl'	=> admin_url( 'admin-ajax.php' ),
			'textConfirmRemoveOld'	=> __( 'All data about room type, room and booking will be deleted and can not restore. Are you sure to continue?', 'awebooking' ),
		) );
	}


	/**
	 * Load js room manage pricing.
	 */

	if ( isset( $_GET['action'] ) && $_GET['action'] == "pricing") {
		wp_enqueue_script( 'apb-room_pricing', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-room_pricing.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
	}
	if ( isset( $_GET['action'] ) &&  $_GET['action'] == "manage_pricing") {
		wp_enqueue_script( 'apb-manage-pricing', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-manage-pricing.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
	}
	if ( isset( $_GET['action'] ) &&  $_GET['action'] == "avb") {
		wp_enqueue_script( 'apb-availability', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-availability.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
	}
	 if ( isset( $_GET['action'] ) &&  $_GET['action'] == "manage_avb") {
		wp_enqueue_script( 'apb-manage-availability', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-manage-availability.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
	}
	$action = array( 'add_room', 'add_room_type', 'edit_room_type', 'edit_room' );
	if ( isset( $_GET['action'] ) &&  in_array( $_GET['action'], $action ) || $typenow == 'apb_room_type' ) {
		wp_enqueue_script( 'apb-room_type', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-room_type.js' );
		wp_enqueue_script( 'apb-media_single', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-media_single.js' );
	}
	if ( isset( $_GET['action'] ) &&  $_GET['action'] == "add_bookings" || isset( $_GET['action'] ) &&  $_GET['action'] == "edit_bookings" || get_post_type() == 'shop_order' ) {
		
		wp_enqueue_script( 'apb-apb-bookings', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-bookings.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
		
	}
	$ui_i18 = AWE_function::get_datepicker_lang();
	$ui_i18 = str_replace( 'datepicker-','',$ui_i18 );
	if( 'default' == $ui_i18 ) {
		$ui_i18 = 'en-US';
	}
	wp_localize_script( 'apb-scripts', 'apb_lang', $ui_i18 );
}

function awe_load_style() {
	global $wp_scripts, $typenow;
	if ( is_admin() ) {
		$post_type = array( 'apb_room_type', 'shop_order', 'room_type' );
		$action = array( 'avb', 'pricing', 'room_type', 'manage_avb', 'manage_pricing', 'edit_room_type', 'add_room_type' );
		if ( in_array( $typenow, $post_type ) || ( isset( $_GET['action'] ) && in_array( $_GET['action'], $action ) ) ) {
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			wp_enqueue_style( 'awe-fullcalendar-style', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/css/apb-fullcalendar.min.css', array(), '2.6.1' );

			$_action = array( 'pricing', 'avb', 'manage_pricing', 'manage_avb', 'add_bookings', 'edit_bookings' );
			if ( isset( $_GET['action'] ) && in_array( $_GET['action'], $_action ) || $typenow == 'shop_order' ) {
				wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
			}
			//wp_enqueue_style( 'apb-awesome', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/css/lib/font-awesome.min.css' );
			wp_enqueue_style( 'apb-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		}
		wp_enqueue_style( 'apb-style', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/css/apb-style.css' );
	}
}

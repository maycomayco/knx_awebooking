<?php
/*
Plugin Name: AweBooking
Plugin URI: http://awethemes.com
Description: You can easily create a booking/reservation system into your WordPress website without any hassle
Author: AweTeam
Version: 2.8.3
Author URI: http://awethemes.com
Text Domain: awebooking
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '<h3>Direct access to this file do not allow!</h3>' );
}

if ( version_compare( phpversion(), '5.3.0', '<' ) ) {
	/**
	 * Adds a message for outdate PHP version.
	 */
	function awebooking_upgrade_notice() {
		$message = sprintf( __( 'AweBooking requires at least PHP version 5.3.29. You are running version %s.', 'awebooking' ), phpversion() );
		printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.
	}
	add_action( 'admin_notices', 'awebooking_upgrade_notice' );

	// Leave code.
	return;
}

define( 'AWE_BK_FILE', __FILE__ );
define( 'AWE_BK_FILENAME', basename( __FILE__ ) );
define( 'AWE_BK_PLUGIN_NAME', plugin_basename( dirname( __FILE__ ) ) );
define( 'AWE_BK_PLUGIN_DIR', untrailingslashit( plugin_dir_path( AWE_BK_FILE ) ) );
define( 'AWE_BK_BASE_URL_PLUGIN', untrailingslashit( plugins_url( '', AWE_BK_FILE ) ) );

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$plugin = get_plugin_data( __FILE__ );

define( 'AWE_VERSION', $plugin['Version'] );
define( 'AWE_LANGUAGE', 'awebooking' );

ob_start();
if ( ! session_id() ) {
	session_start();
}
ob_clean();
error_reporting(0);
if ( ! class_exists( 'APB_Booking' ) ) {
	require_once AWE_BK_PLUGIN_DIR . '/lib/booking.php';
}

/**
 * Redirect after active plugin.
 */
register_activation_hook( __FILE__, 'my_plugin_activate' );
add_action( 'admin_init', 'my_plugin_redirect' );

function my_plugin_activate() {
	update_option( 'apb_active', 1 );
	update_option( '_booking_pending', 1 );

	if ( false === AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ) {
		AWE_function::update_option( 'woocommerce_price_thousand_sep', ',' );
	}

	if ( false === AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ) {
		AWE_function::update_option( 'woocommerce_price_decimal_sep', '.' );
	}

	if ( false === AWE_function::get_option( 'woocommerce_price_num_decimals' ) ) {
		AWE_function::update_option( 'woocommerce_price_num_decimals', 2 );
	}

	awebooking_clean_room();

	awebooking_add_role();
}

function my_plugin_redirect() {
	if ( get_option( 'apb_active' ) == 1 ) {
		update_option( 'apb_active', 0 );
		Apb_autoload::apb_setting_default();
		wp_redirect( admin_url( 'index.php?page=apb-about' ) );
		exit();
	}
}

/**
 * Add theme-name in base classes.
 *
 * @param  array $classes Body classes.
 * @return array
 */
function awebooking_add_bodyclass( $classes ) {
	$theme = wp_get_theme();

	if ( ! in_array( $theme->stylesheet, $classes ) ) {
		$classes[] = $theme->stylesheet;
	}

	return $classes;
}
add_filter( 'body_class', 'awebooking_add_bodyclass' );


/**
 * Clean room.
 */
function awebooking_clean_room() {
	$rooms = get_posts( array(
		'post_type'		=> 'apb_room',
		'nopaging'		=> true,
	) );

	foreach ( $rooms as $room ) {
		$room_type_id = $room->post_parent;

		if ( get_post_status( $room_type_id ) != 'publish' ) {
			wp_trash_post( $room->ID );
		}
	}
}


/**
 * Add customer role.
 *
 * @return void
 */
function awebooking_add_role() {
	if ( get_role( 'customer' ) ) {
		return;
	}

	add_role( 'customer', __( 'Customer', 'awebooking' ), array(
		'read' => true,
	) );
}

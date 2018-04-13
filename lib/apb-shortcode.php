<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Shortcode
 *
 * @class 		Apb_shortcode
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

class Apb_shortcode {
	function __construct() {
		add_shortcode( 'apb_checkout', array( $this, 'apb_checkout' ) );
		add_shortcode( 'apb_check_available', array( $this, 'apb_check_available' ) );
		add_shortcode( 'apb_rooms', array( $this, 'apb_show_room_item' ) );
	}

	public function apb_checkout( $attr, $content = '' ) {
		extract( shortcode_atts( array(), $attr ) );

		ob_start();
		do_action( 'apb_page_checkout' );
		$html = ob_get_clean();

		return $html;
	}

	public function apb_check_available( $attr, $content = '' ) {
		$attr = shortcode_atts( array(
			'departure'     => 1,
			'night'         => 1,
			'room_type'     => 1,
			'mullti_room'   => 1,
			'status'        => 'public',
			'style'         => 1,
			'tmp_file'      => '',
		), $attr, 'apb_check_available' );

		extract( $attr );
		$apb_setting = get_option( 'apb_setting' );

		/*----------  use do_shortcode ----------*/
		if ( 'static' == $status ) {
			$args_shortcode = apply_filters( 'apb_check_form_template_args', array(
				'departure'     => $apb_setting['field_status']['status_departure_page'],
				'night'         => $apb_setting['field_status']['status_night_number_page'],
				'mullti_room'   => $apb_setting['field_status']['status_multi_room_page'],
				'room_type'     => $apb_setting['field_status']['status_room_type_page'],
				'style'         => 1,
				'tmp_file'      => $tmp_file,
			), $apb_setting );
		} else {
			/*----------  use content shortcode ----------*/
			$args_shortcode = apply_filters( 'apb_check_form_template_args', array(
				'departure'     => 'on' == $departure ? 1 : 0,
				'night'         => 'on' == $night ? 1 : 0,
				'room_type'     => 'on' == $room_type ? 1 : 0,
				'mullti_room'   => 'on' == $mullti_room ? 1 : 0,
				'style'         => 'vertical' == $style ? 1 : 2,
				'tmp_file'      => $tmp_file,
			), $attr );
		}

		ob_start();
		do_action( 'apb_short_check_available', $args_shortcode );
		$html = ob_get_clean();

		return $html;
	}

	public function apb_show_room_item( $attr, $content = '' ) {
		$attrs = apply_filters( 'apb_shortcode_rooms', array(
			'type'   => 'apb_room_type',
		) );
		extract( shortcode_atts( $attrs, $attr ) );

		ob_start();
		do_action( 'apb_shorcode_rooms', $attr );
		$html = ob_get_clean();

		return $html;
	}
}

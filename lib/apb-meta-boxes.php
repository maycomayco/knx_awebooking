<?php
/**
 * AWE Meta Box Autoload
 *
 * @class 		Apb_meta_boxes
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Apb_meta_boxes {

	public function __construct() {
		$this->boxes_package_room_type();
		$this->create_gallery();
		$this->boxes_info_booking();
		$this->boxes_apb_order_data();
		$this->boxes_apb_set_page_lang();
		add_action( 'save_post','boxes_package_room_type::save' );
		add_action( 'save_post','create_gallery::save' );
		add_action( 'save_post','Boxes_info_booking::save' );
		add_action( 'save_post','Apb_set_page_lang::save' );

		add_action( 'add_meta_boxes', array( $this, 'apb_add_meta_boxes' ), 40 );
	}

	public function boxes_package_room_type() {
		include 'meta_boxe/boxes_package_room_type.php';
	}

	public function create_gallery() {
		include 'meta_boxe/create_gallery.php';
	}

	public function boxes_info_booking() {
		include 'meta_boxe/boxes_info_booking.php';
	}

	public function boxes_apb_order_data() {
		include 'meta_boxe/boxes_apb_order_data.php';
	}

	public function boxes_apb_set_page_lang() {
		include 'meta_boxe/boxes_set_page_lang.php';
	}

	public function apb_add_meta_boxes() {
		// add_meta_box( 'apb-set-lang-meta-box', __( 'Awebooking Lang', 'awebooking' ), 'Apb_set_page_lang::output', 'page', 'side', 'default', 1 );
	}
}

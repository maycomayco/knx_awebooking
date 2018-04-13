<?php
/**
 * APB_Booking Autoloader.
 *
 * @class 		APB_Booking
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class APB_Booking {

	static private $instance = NULL;

	static public function getInstance() {
		self::$instance = new APB_Booking();
		$GLOBALS['apb_db'] = self::table_exists();
		self::lib_function();
		$GLOBALS['apb_autoload'] = self::autoload();
		self::scripts();
		$GLOBALS['apb_post_type'] = self::post_type();
		$GLOBALS['apb_backend'] = self::manage_admin();
		$GLOBALS['apb_controller'] = self::controller();
		$GLOBALS['apb_ajax'] = self::process_ajax();
		self::apb_add_to_cart();
		$GLOBALS['apb_template'] = self::template_func();
		$GLOBALS['apb_wc'] = self::apb_wc_setting();
		$GLOBALS['apb_shortcode'] = self::apb_shortcode();
		$GLOBALS['apb_meta_box'] = self::apb_meta_boxes();
		self::apb_widget();
		self::apb_emails();
		self::apb_import_export();
		self::apb_export_file();
		self::apb_emogrifier();
		self::apb_list_font_awesome();
		self::apb_icon_require();
		return self::$instance;
	}

	static public function table_exists() {
		include 'apb-database-exits.php';
		return new database_exits();
	}

	static public function lib_function() {
		include 'apb-lib-function.php';
	}
	static public function controller() {
		include 'apb-controller.php';
		return new AWE_Controller();
	}
	static public function manage_admin() {
		include 'apb-backend.php';
		return new AWE_backend();
	}
	static public function post_type() {
		include 'apb-post-type.php';
		return new AWE_Post_type();
	}
	static public function scripts() {
		include 'apb-scripts.php';
	}
	static public function process_ajax() {
		include 'apb-process-ajax.php';
		return new Process_ajax();
	}
	static public function template_func() {
		include 'apb-template.php';
		return new AWE_Template();
	}
	static public function apb_wc_setting() {
		include 'apb-wc-setting.php';
		return new Apb_Wc_Setting();
	}
	static public function apb_shortcode() {
		include 'apb-shortcode.php';
		return new Apb_shortcode();
	}
	static public function apb_meta_boxes() {
		include 'apb-meta-boxes.php';
		return new Apb_meta_boxes();
	}
	static public function autoload() {
		include 'apb-autoload.php';
		return new Apb_autoload();
	}
	static public function apb_widget() {
		include 'apb-widget.php';
	}
	static public function apb_import_export() {
		include 'apb-import-export.php';
	}
	static public function apb_export_file() {
		include 'apb-export-file.php';
	}
	static public function apb_add_to_cart() {
		include 'apb-class-add-cart.php';
	}
	static public function apb_emails() {
		include 'apb-class-email.php';
	}
	static public function apb_emogrifier() {
		if ( class_exists( 'Emogrifier' ) ) {
			return;
		}
		include 'apb-class-emogrifier.php';
	}
	static public function apb_list_font_awesome(){
		include 'apb-font-icon.php';
	}

	static public function apb_icon_require(){
		include 'apb-icon-require.php';
	}
}

function begin_plugin() {
	return APB_Booking::getInstance();
}
begin_plugin();


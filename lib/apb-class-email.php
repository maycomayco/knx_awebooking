<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *   Email Class
 *
 * @class       WC_Email
 * @version     1.6
 * @package     awebooking/Classes/Emails
 * @author      awebooking
 */

Class APB_Email{

	public function __construct(){
		add_action( 'apb_email_header', array( $this, 'apb_email_header' ) );
		add_action( 'apb_email_footer', array( $this, 'apb_email_footer' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'mail_content_type' ) );

	}

	/**
	 * Remove email object.
	 *
	 * @return void
	 * @since 1.11
	 */
	public function destroy(){
		remove_action( 'apb_email_header', array( $this, 'apb_email_header' ) );
		remove_action( 'apb_email_footer', array( $this, 'apb_email_footer' ) );
	}


	public function apb_sendMail( $to, $subject, $message, $no_message = 0, $to_name = '' ) {
		require_once ABSPATH . WPINC . '/pluggable.php';

		$result = wp_mail( $to, $subject, $message, $to_name );

		remove_filter( 'wp_mail_content_type', array( $this, 'mail_content_type' ) );

		return $result;
	}

	public function mail_content_type() {
		return 'text/html';
	}

	public function apb_style_inline( $content ) {
		ob_start();

		include AWE_function::template_exsits( 'emails/apb-email-style' );

		$css = apply_filters( 'apb_email_styles', ob_get_clean() );


		// apply CSS styles inline for picky email clients
		$emogrifier = new Emogrifier( $content, $css );
		$content = $emogrifier->emogrify();

		return $content;
	}

	/**
	 * Wraps a message in the awebooking mail template.
	 *
	 * @param mixed $email_heading
	 * @param string $message
	 * @return string
	 */
	public function apb_wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer
		ob_start();

		do_action( 'apb_email_header', $email_heading );

		echo wpautop( wptexturize( $message ) );

		do_action( 'apb_email_footer' );

		// Get contents
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading heading for the email
	 */
	public function apb_email_header( $email_heading ) {
		include AWE_function::template_exsits( 'emails/apb-email-header', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function apb_email_footer() {
		include AWE_function::template_exsits( 'emails/apb-email-footer' );
	}


	/**
	 * Display check in.
	 *
	 * @param  array $item Item data.
	 * @since 2.4.1
	 */
	public static function email_check_in( $item ) {
		include AWE_function::template_exsits( 'emails/apb-email-check-in' );
	}


	/**
	 * Display check out.
	 *
	 * @param  array $item Item data.
	 * @since 2.4.1
	 */
	public static function email_check_out( $item ) {
		include AWE_function::template_exsits( 'emails/apb-email-check-out' );
	}


	/**
	 * Display room name.
	 *
	 * @param array $item Item data.
	 * @param int   $room_num Room number.
	 * @param int   $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_room_name( $item, $room_num, $room_type_id ) {
		include AWE_function::template_exsits( 'emails/apb-email-room-name' );
	}


	/**
	 * Display guest info.
	 *
	 * @param array $item Item data.
	 * @since 2.4.1
	 */
	public static function email_guest_info( $item, $order_id ) {
		include AWE_function::template_exsits( 'emails/apb-email-guest-info' );
	}


	/**
	 * Display room type name.
	 *
	 * @param int $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_room_type_name( $room_type_id ) {
		$room_type_id = AWE_function::get_room_type_id_current_lang( $room_type_id );

		include AWE_function::template_exsits( 'emails/apb-email-room-type-name' );
	}


	/**
	 * Display days pricing in email.
	 *
	 * @param  array $item         Item data.
	 * @param  int   $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_days_pricing( $item, $room_type_id ) {
		include AWE_function::template_exsits( 'emails/apb-email-days-pricing' );
	}


	/**
	 * Display extra price.
	 *
	 * @param array $item         Item data.
	 * @param int   $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_extra_price( $item, $room_type_id ) {
		$range_date   = AWE_function::range_date( $item['from'], $item['to'] );
		$base_price_for = get_post_meta( $room_type_id, 'base_price_for', true );
		$extra_guess_data = AWE_function::get_extra_guest_data( $base_price_for, $item['room_adult'], $item['room_child'] );
		$extra_price_data = AWE_function::get_extra_price_data( $room_type_id, $item['room_adult'], $item['room_child'] );

		include AWE_function::template_exsits( 'emails/apb-email-extra-price' );
	}


	/**
	 * Display package.
	 *
	 * @param array $item         Item data.
	 * @param int   $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_package( $item, $room_type_id ) {
		$range_date   = AWE_function::range_date( $item['from'], $item['to'] );

		include AWE_function::template_exsits( 'emails/apb-email-package' );
	}


	/**
	 * Display extra sale.
	 *
	 * @param array $item         Item data.
	 * @param int   $room_type_id Room type id.
	 * @since 2.4.1
	 */
	public static function email_extra_sale( $item, $room_type_id ) {
		$range_date   = AWE_function::range_date( $item['from'], $item['to'] );

		include AWE_function::template_exsits( 'emails/apb-email-extra-sale' );
	}


	/**
	 * Display subtotal.
	 *
	 * @param float $subtotal Subtotal.
	 * @since 2.4.1
	 */
	public static function email_subtotal( $subtotal, $id ) {
		include AWE_function::template_exsits( 'emails/apb-email-subtotal' );
	}
}

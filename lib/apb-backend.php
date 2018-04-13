<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Backend Autoload
 *
 * @class 		AWE_backend
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

class AWE_backend extends AWE_function{

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_my_custom_menu_page' ) );
		// add_filter( 'custom_menu_order', array( $this,'custom_menu_order' ) );
		add_action( 'admin_init', array( $this, 'apb_preview_emails' ) );
	}

	/**
	 * Register_my_custom_menu_page - Register menu page admin.
	 */
	public function register_my_custom_menu_page() {
		add_submenu_page( 'edit.php?post_type=apb_room_type', __( 'Room management', 'awebooking' ), __( 'Room management', 'awebooking' ), 'edit_posts', 'rooms.php', array( $this, 'render_page_backend' ) );
		add_submenu_page( 'edit.php?post_type=apb_room_type', __( 'Settings', 'awebooking' ), __( 'Settings', 'awebooking' ), 'edit_posts', 'apb-setting.php', array( $this, 'render_page_setting' ) );
	}

	public function custom_menu_order( $menu_ord ) {
		_deprecated_function( __FUNCTION__, '2.0' );
		global $submenu;

		$arr = array();
		if ( class_exists( 'WooCommerce' ) ) {
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][5];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][13];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][12];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][11];
		} else {
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][5];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][13];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][11];
			$arr[] = $submenu['edit.php?post_type=apb_room_type'][12];
		}
		$submenu['edit.php?post_type=apb_room_type'] = $arr;

		return $menu_ord;
	}

	/**
	 * Render_page_backend - Get template by action.
	 */
	public function render_page_backend() {
		if ( isset( $_GET['action'] ) ) {
			switch ( $_GET['action'] ) {
				case 'apb_room_type':
					include 'view/manage_room.php';
					do_action( 'delete_room' );
					break;

				case 'pricing':
					?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery(".wp-submenu li").map(function() {
								jQuery(this).removeClass( 'current' );
							});
							jQuery(".wp-first-item").addClass( 'current' );
						});
					</script>
					<?php
					include 'view/apb-single_pricing.php';
					break;

				case 'manage_pricing':
					include 'view/apb-manage_pricing.php';
					break;

				case 'manage_avb':
					include 'view/apb-manage_availability.php';
					break;

				case 'trashbook':
					do_action( 'delete_booking' );
					break;

				case 'intallpage':
					Apb_autoload::page_intall();
					break;
			}
		} else {
			wp_redirect( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=manage_avb' ), 301 );
		}

	}


	/**
	 * All tab setting  - backend.
	 */
	public function render_page_setting() {
		do_action( 'validate_setting' );

		$form_check_sub_tab = array(
			array(
				'tab_name' => __( 'Default', 'awebooking' ),
				'tab_href' => 'tabs-form-check-available',
				'tab_content' => $this->content_tab_setting( 'apb-form-page-check-available-setting', 'tabs-form-check-available' ),
			),
			array(
				'tab_name' => __( 'Shortcode', 'awebooking' ),
				'tab_href' => 'tabs-form-shortcode',
				'tab_content' => $this->content_tab_setting( 'apb-form-shortcode-setting', 'tabs-form-shortcode' ),
			),
			array(
				'tab_name' => __( 'Calendar', 'awebooking' ),
				'tab_href' => 'tabs-calendar',
				'tab_content' => $this->content_tab_setting( 'apb-calendar-tab', 'tabs-calendar' ),
			),
		);

		$form_email_sub_tab = array(
			array(
				'tab_name' => __( 'Email template', 'awebooking' ),
				'tab_href' => 'tabs-mail-template',
				'tab_content' => $this->content_tab_setting( 'apb-mail-template-setting', 'tabs-mail-template' ),
			),
			array(
				'tab_name' => __( 'New booking', 'awebooking' ),
				'tab_href' => 'tabs-mail-new-booking',
				'tab_content' => $this->content_tab_setting( 'apb-mail-new-booking-setting', 'tabs-mail-new-booking' ),
			),
			array(
				'tab_name' => __( 'Pending booking', 'awebooking' ),
				'tab_href' => 'tabs-mail-pending',
				'tab_content' => $this->content_tab_setting( 'apb-mail-pending-setting', 'tabs-mail-pending' ),
			),
			array(
				'tab_name' => __( 'Complete booking', 'awebooking' ),
				'tab_href' => 'tabs-mail-complete',
				'tab_content' => $this->content_tab_setting( 'apb-mail-complete-setting', 'tabs-mail-complete' ),
			),
			array(
				'tab_name' => __( 'Cancelled booking', 'awebooking' ),
				'tab_href' => 'tabs-mail-cancelled',
				'tab_content' => $this->content_tab_setting( 'apb-mail-cancelled-setting', 'tabs-mail-cancelled' ),
			),
		);

		$tab_array = apply_filters(
			'apb_tab_settings',
			array(
				'tabs-1' => array(
					'tab_name' => __( 'General', 'awebooking' ),
					'tab_href' => 'tabs-1',
					'tab_content' => $this->content_tab_setting( 'apb-general', 'tabs-1' ),
				),
				'tabs-custom-color' => array(
					'tab_name' => __( 'Custom color', 'awebooking' ),
					'tab_href' => 'tabs-custom-color',
					'tab_content' => $this->content_tab_setting( 'apb-custom-color', 'tabs-custom-color' ),
				),
				'apb-form-check' => array(
					'tab_name' => __( 'Booking form', 'awebooking' ),
					'tab_href' => 'apb-form-check',
					'tab_content' => $this->content_tab_setting( 'apb-form-check', 'apb-form-check', $form_check_sub_tab ),
				),
				'tabs-3' => array(
					'tab_name' => __( 'Pages setting', 'awebooking' ),
					'tab_href' => 'tabs-3',
					'tab_content' => $this->content_tab_setting( 'apb-room-page-setting', 'tabs-3' ),
				),
				'tabs-2' => array(
					'tab_name' => __( 'Checkout', 'awebooking' ),
					'tab_href' => 'tabs-2',
					'tab_content' => $this->content_tab_setting( 'apb-page-checkout-setting', 'tabs-2' ),
				),
				'tabs-color' => array(
					'tab_name' => __( 'Rooms status', 'awebooking' ),
					'tab_href' => 'tabs-color',
					'tab_content' => $this->content_tab_setting( 'apb-color-calendar-setting', 'tabs-color' ),
				),
				'tabs-emails' => array(
					'tab_name' => __( 'Email', 'awebooking' ),
					'tab_href' => 'tabs-emails',
					'tab_content' => $this->content_tab_setting( 'apb-emails-setting', 'tabs-emails', $form_email_sub_tab ),
				),
				'tabs-custom-code' => array(
					'tab_name' => __( 'Custom code', 'awebooking' ),
					'tab_href' => 'tabs-custom-code',
					'tab_content' => $this->content_tab_setting( 'apb-custom-code', 'tabs-custom-code' ),
				),
				'tabs-import-export' => array(
					'tab_name' => __( 'Import/Export', 'awebooking' ),
					'tab_href' => 'tabs-import-export',
					'tab_content' => $this->content_tab_setting( 'apb-import-export', 'tabs-import-export' ),
				),
			)
		);
		include 'view/apb-page_setting.php';
	}

	/**
	 * Render content setting.
	 *
	 * @return string
	 */
	public function content_tab_setting( $file, $tab_href, $sub_tabs = '' ) {
		$current_user = wp_get_current_user();
		$html = '<fieldset id="' . esc_attr( $tab_href ) . '" class="form-wrapper vertical-tabs-pane">';
		ob_start();
		include 'view/settings/' . $file . '.php';
		$html .= ob_get_clean();
		$html .= '</fieldset>';
		return $html;
	}

	public function apb_render_setting_html( $args ) {
		foreach ( $args as $item ) {
			?>
			<div class="<?php echo esc_attr( $item['class'] ) ?>">
				<p><?php echo esc_html( $item['name'] ) ?></p>
				<div class="form-input">
					<?php
					if ( 'text' == $item['type']['type'] || 'password' == $item['type']['type'] ) {
						AWE_function::apb_gen_input( array(
							'type'          => $item['type']['type'],
							'name'          => $item['type']['name'],
							'placeholder'   => $item['type']['placeholder'],
							'class'         => $item['type']['class'],
							'id'            => $item['type']['id'],
							'value'         => $item['type']['value'],
						) );
					} elseif ( 'checkbox' == $item['type']['type'] ) {
						$args_checkbox = array(
							'type'          => $item['type']['type'],
							'name'          => $item['type']['name'],
							'placeholder'   => $item['type']['placeholder'],
							'class'         => $item['type']['class'],
							'id'            => $item['type']['id'],
							'value'         => $item['type']['value'],
						);
						( isset( $item['type']['_value'] ) && 1 == $item['type']['_value'] ) ? $args_checkbox['checked'] = 'checked' : '';
						AWE_function::apb_gen_input( $args_checkbox );
						echo $item['desc'];

					} elseif ( 'textarea' == $item['type']['type'] ) {
						echo "<textarea name=\"{$item['type']['name']}\" cols=\"{$item['type']['cols']}\">{$item['type']['value']}</textarea>";
					}

					?>
				</div>

				<span class="description">
					<?php
					if ( 'checkbox' != $item['type']['type'] ) {
						echo ( $item['desc'] );
					}
					?>
				</span>
			</div>
			<?php
		}
	}

	/**
	 * Preview email template.
	 *
	 * @return void
	 */
	public function apb_preview_emails() {

		if ( isset( $_GET['preview_awebooking_mail'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'preview-mail' ) ) {
				die( 'Security check' );
			}

			// Get the preview email subject.
			$email_heading = __( 'Checkout order', 'awebooking' );

			// Get the preview email content.
			ob_start();
			include( 'view/html-email-template-preview.php' );
			$message       = ob_get_clean();

			// Create a new email.
			$email         = new APB_Email();

			$message       = $email->apb_style_inline( $email->apb_wrap_message( $email_heading, $message ) );

			// Print the preview email.
			echo $message;
			// $email->apb_sendMail( 'leeit.1992@gmail.com', 'Awebooking checkout order', $message );

			exit;
		}
	}
}


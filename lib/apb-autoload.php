<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Autoload
 *
 * @class 		Apb_autoload
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

Class Apb_autoload extends AWE_function{

	public static  $list_page  = null;

	function __construct() {
		self::$list_page = array( 'awe-booking-check', 'awe-room-list', 'awe-checkout' );
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_notices', array($this,'my_admin_notice' ));
		add_action( 'wp_ajax_apb_skip_install', array($this, 'apb_skip_install' ));

		add_action( 'after_setup_theme', function() {
			add_theme_support( 'post-thumbnails' );
		} );

		$this->load_lang();
	}

	public function load_lang() {
		if(file_exists(WP_CONTENT_DIR . '/languages/loco/plugins/awebooking-'.get_locale().'.mo') ) {
			load_textdomain( 'awebooking', WP_CONTENT_DIR . '/languages/loco/plugins/awebooking-'.get_locale().'.mo');
		}
		if(file_exists(WP_CONTENT_DIR .'/languages/plugins/'.get_locale().".mo")) {
			load_textdomain( 'awebooking', WP_CONTENT_DIR .'/languages/plugins/'.get_locale().".mo");
		}
		load_plugin_textdomain( 'awebooking',false, apply_filters( 'apb_lang_path',WP_CONTENT_DIR .'/languages' ));
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {

		if ( empty( $_GET['page'] ) ) {
			return;
		}

		$welcome_page_name  = __( 'About AweBooking', 'awebooking' );
		$welcome_page_title = __( 'Welcome to AweBooking', 'awebooking' );

		switch ( $_GET['page'] ) {
			case 'apb-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'apb-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
		}
	}

	/**
	 * Admin_css function.
	 */
	public function admin_css() {
		wp_enqueue_style( 'apb-style', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/css/apb-style.css' );
	}
	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>

		<div class="wrap about-wrap">
			<?php
			echo AWE_function::apb_getStateFlash( 'apb_intall_page' );
			unset( $_SESSION['apb_intall_page'] );
			?>
			<h1><?php esc_html_e( 'Welcome to AweBooking', 'awebooking' ); ?></h1>
			<div class="about-text apb-about-text">
				<?php
				if ( ! empty( $_GET['apb-installed'] ) ) {
					$message = __( 'Thanks, all done!', 'awebooking' );
				} elseif ( ! empty( $_GET['apb-updated'] ) ) {
					$message = __( 'Thank you for updating to the latest version!', 'awebooking' );
				} else {
					$message = __( 'Thank you for using our ', 'awebooking' );
				}

				printf( esc_html__( '%s AweBooking to be your room management system.', 'awebooking' ), $message, AWE_VERSION );
				?>
			</div>
			<div class="apb-badge">
				<img src="<?php echo AWE_BK_BASE_URL_PLUGIN ?>/assets/backend/images/awe.png">
				<span class="apb-version">Version <?php echo AWE_VERSION; ?></span>
			</div>

			<p class="apb-actions">
				<a class="apb-btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=apb-setting.php' ) ) ?>"><?php esc_html_e( 'Settings', 'awebooking' ) ?></a>
				<a class="apb-btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=intallpage' ) ) ?>"><?php esc_html_e( 'Intall Pages', 'awebooking' ) ?> </a>
			</p>

			<div class="changelog">

				<div class="changelog about-integrations">
					<div class="apb-feature feature-section">
						<div class="apb-col23">
							<h4><?php _e( 'AweBooking', 'awebooking' ) ?></h4>
							<p><?php _e( 'AweBooking is an ideal solution for hotel booking. It is simple, easy-to-use but features all necessary functions needed in a booking plugin:', 'awebooking' ) ?></p>

							<p><?php _e( '+ Room type management: categorize rooms into equivalent room types.', 'awebooking' ) ?></p>
							<p><?php _e( '+ Room management: Set single room (in a room type) price and room status for specific date.' ); ?></p>
							<p><?php _e( '+ Booking System: All booking orders of customer will be stored in the Booking System, where you have permission to review as well as change those orders.' ); ?></p>

							<h4><?php _e( '2 Booking types', 'awebooking' ) ?></h4>
							<p><?php _e( 'AweBooking provides user 2 booking type: Online booking (using Woocommerce) and Offline booking (using Contact Form 7).', 'awebooking' ) ?></p>

						</div>
						<div class="apb-col13">
							<h4><?php _e( 'Powerful settings', 'awebooking' ) ?></h4>
							<p><?php _e( 'You can also change how soon a booking can start, currency, what room type are available for search, text label... All in <a href="'.admin_url().'edit.php?post_type=apb_room_type&page=apb-setting.php">AweBooking Settings Page</a>', 'awebooking' ) ?></p>
							<h4><?php _e( 'Need help?', 'awebooking' ) ?></h4>
							<p><?php _e( 'If you need help with setting up the plugin, please <a href="http://awethemes.com/forum/plugin-support/awebooking" target="blank">post your question here</a>', 'awebooking' ) ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Add styles just for this page, and remove dashboard page links.
	 */
	public function admin_head() {
		?>
		<style type="text/css">
			.about-wrap .feature-section {border: none; padding: 3px 20px}
			.apb-col23 {width: 60%; margin-right: 5%; float: left;}
			.apb-col13 {width: 34%; float: left;}
			.about-wrap .apb-badge {
					position: absolute;
					right: 0;
					top: 0;
			}
			.about-wrap .apb-badge .apb-version {
				position: absolute;
				bottom: 30px;
				right: 0;
				left: 0;
				text-align: center;
				font-size: 13px;
				color: #fabccb;
			}
			.apb-btn{
					background-color: #e93c4f;
					color: #fff;
					display: inline-block;
					font-weight: bold;
					padding: 10px 25px;
					text-transform: uppercase;
					text-decoration: none;
			}
			.apb-btn:hover{
				color: #fff;
				background-color: #aa0000 !important;
			}
			.about-integrations {
					background: #fff none repeat scroll 0 0;
					margin: 20px 0;
					padding: 1px 20px 10px;
			}
			.updated.settings-error.notice.is-dismissible {
					padding: 10px;

			}
			.about-wrap div.error, .about-wrap div.updated{
					display: block !important;
			}
		</style>
		<?php
	}

	/**
	 * Setting default after active plugin.
	 */
	static public function apb_setting_default() {

		$apb_setting = array(
			'field_status'     => array(
				'status_departure_sc'      => 1,
				'status_night_number_sc'   => 1,
				'status_room_type_sc'      => 1,
				'status_multi_room_sc'     => 1,
				'status_departure_page'    => 1,
				'status_night_number_page' => 1,
				'status_room_type_page'    => 0,
				'status_multi_room_page'   => 0,
				'form_style'               => 1,
			),
		);
		update_option( 'permalink_structure', '%year%/%monthnum%/%day%/%postname%/' );

		$param = array(
			array( 'name' => 'rooms_booking_start_date', 'value' => 0 ),
			array( 'name' => 'max_adult', 'value' => 6 ),
			array( 'name' => 'max_child', 'value' => 6 ),
			array( 'name' => 'max_room', 'value' => 6 ),
			array( 'name' => 'max_night', 'value' => 6 ),
			array( 'name' => 'apb_number_calendar', 'value' => 2 ),
			array( 'name' => 'rooms_checkout_style', 'value' => 2 ),
			array( 'name' => 'rooms_price_calculation', 'value' => 1 ),
			array( 'name' => 'woocommerce_currency', 'value' => 'USD' ),
			array( 'name' => 'label-unavailable', 'value' => 'N/A' ),
			array( 'name' => 'color-unavailable', 'value' => '#d10d00' ),
			array( 'name' => 'label-available', 'value' => 'AV' ),
			array( 'name' => 'color-available', 'value' => '#8ba175' ),
			array( 'name' => 'label-pending', 'value' => 'P/D' ),
			array( 'name' => 'color-pending', 'value' => '#c5c5c5' ),
			array( 'name' => 'label-complate', 'value' => 'Complete' ),
			array( 'name' => 'color-complate', 'value' => '#bada55' ),
			array( 'name' => 'limit-page', 'value' => 6 ),
			array( 'name' => 'apb_setting', 'value' => $apb_setting ),
			array( 'name' => 'datepicker_num_calendar', 'value' => 1 ),
			array( 'name' => 'apb_currency_style', 'value' => 1 ),
			array( 'name' => 'apb_email_footer', 'value' => get_bloginfo().' - Powered by Awebooking' ),
			array( 'name' => 'apb_email_base_color', 'value' => '#557da1' ),
			array( 'name' => 'apb_email_background_color', 'value' => '#f5f5f5' ),
			array( 'name' => 'apb_email_body_background_color', 'value' => '#fdfdfd' ),
			array( 'name' => 'apb_email_text_color', 'value' => '#505050' ),
			array( 'name' => 'apb_deposit_type', 'value' => 'none' ),

		);
		foreach ( $param as $item_param ) {
			update_option( $item_param['name'], $item_param['value'] );
		}
	}

	/**
	 * Intall page after active plugin.
	 */
	static public function page_intall() {
		foreach ( self::$list_page as $page ) {
			$is_page = get_posts( array( 'name' => $page, 'post_type' => 'page' ) );
			if ( ! empty( $is_page ) ) {
				switch ( $page ) {
					case 'awe-booking-check':
						update_option( 'check_avb', $is_page[0]->ID );
						break;
					case 'awe-room-list':
						update_option( 'list_room',$is_page[0]->ID );
						break;
					case 'awe-checkout':
						update_option( 'apb_checkout', $is_page[0]->ID );
						break;
				}
			} else {
				switch ( $page ) {
					case 'awe-booking-check':
						$tittle = __( 'AWE Check Available', 'awebooking' );
						$content = '';
							break;
					case 'awe-room-list':
						$tittle = __( 'AWE List Room', 'awebooking' );
						$content = '';
						break;
					case 'awe-checkout':
						$tittle = __( 'AWE CheckOut', 'awebooking' );
						$content = '';
						break;
				}

				$awe_page = array(
					'post_title'    => $tittle,
					'post_content'  => $content,
					'post_name'     => $page,
					'post_status'   => 'publish',
					'post_author'   => get_current_user_id(),
					'post_type'     => 'page',
				);

				$id = wp_insert_post( $awe_page );
				switch ( $page ) {
					case 'awe-booking-check':
						update_option( 'check_avb', $id );
						break;
					case 'awe-room-list':
						update_option( 'list_room', $id );
						break;
					case 'awe-checkout':
						update_option( 'apb_checkout', $id );
						break;
				}
			}
		}
		AWE_function::apb_setState( 'apb_intall_page', '<div class="updated settings-error notice is-dismissible">' . esc_html__( 'Install pages successfully!', 'awebooking' ) . '</div>' );
		wp_redirect( admin_url( 'index.php?page=apb-about' ) );
		exit();
	}

	public function my_admin_notice() {
		$empty_page = array();
		foreach ( self::$list_page as $page ) {
			$is_page = get_posts( array( 'name' => $page, 'post_type' => 'page' ) );
			if ( empty( $is_page ) ) {
				$empty_page[] = $page;
			}
		}
		if ( ! empty( $empty_page ) && get_option( 'apb_skip_install' ) != 1 ) {
			include( 'view/html-notice-install.php' );
		}
	}

	public function apb_skip_install() {
		update_option( 'apb_skip_install', 1 );
		die;
		return false;
	}

	static public function remove_package_draf() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}apb_booking_options WHERE status = %s", 'draf' ) );
	}
}

<?php
/**
 * AWE Template Autoload.
 *
 * @class 		AWE_Template
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AWE_Template.
 */
class AWE_Template extends AWE_function {

	public $lang_page_check_avb = null;
	public $lang_page_list_room = null;
	public $lang_page_checkout = null;

	public function __construct() {

		$this->lang_page_check_avb = ( array ) get_option( '_apb_page_check_avb' );
		$this->lang_page_list_room = ( array ) get_option( '_apb_page_list_room' );
		$this->lang_page_checkout = ( array ) get_option( '_apb_page_checkout' );
		add_filter( 'template_include', array( $this, 'template_include' ) );

		// load script.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 1005 );

		add_filter( 'rewrite_rules_array',array( $this, 'Apb_prefix_movie_rewrite_rule' ) );
		add_action( 'wp_loaded',array( $this, 'my_flush_rules' ) );
		add_filter( 'query_vars',array( $this, 'wp_insertMyRewriteQueryVars' ) );

		$this->template_hook();
		$this->template_function();
		
	}

	/**
	 * Load scripts.
	 * @return void
	 */
	public function load_scripts() {
		$get_lang = get_query_var( 'lang' );
		$datepicker_lang = AWE_function::get_datepicker_lang();
		$fc_lang = AWE_function::lang_code_datepicker_to_fullcalendar( $datepicker_lang );

		$link_checkout = AWE_function::get_checkout_page_url();

		$mon_current = isset( $_GET['mon'] ) ? $_GET['mon'] : date( 'm' );
		$year_current = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
		$apb_ajax_single_calendar = get_option( 'apb_ajax_single_calendar' );
		$params = array(
			'ajax_url'            => admin_url( 'admin-ajax.php' ),
			'order_status'        => 0,
			'checkout_page'       => isset( $link_checkout ) ? $link_checkout : '',
			'type_checkout'       => get_option( 'rooms_checkout_style' ),
			'awe_date_curent_one' => $year_current . '-' . $mon_current,
			'awe_date_curent_two' => $year_current . '-0' . ( $mon_current + 1 ),
			'apb_number_calendar' => get_option( 'datepicker_num_calendar' ),
			'nonce'               => wp_create_nonce( 'awebooking' ),
			'textRoom'            => esc_html__( 'Room', 'awebooking' ),
			'textChangingRoom'    => esc_html__( 'You are changing room', 'awebooking' ),
			'textErrorMaxNight'   => sprintf( esc_html__( 'Maximum night can be booked is %s', 'awebooking' ), get_option( 'max_night' ) ),
			'textAdult'           => esc_html__( 'Adult', 'awebooking' ),
			'textChild'           => esc_html__( 'Child', 'awebooking' ),
			'textPackage'         => esc_html__( 'Package', 'awebooking' ),
			'textErrorCheckAfterDay'    => sprintf( esc_html__( 'You only can check room available after day current is %s day', 'awebooking' ), get_option( 'rooms_booking_start_date' ) ),
			'maxAdult'            => AWE_function::get_max_adult(),
			'maxChild'            => AWE_function::get_max_child(),
			'singleCalendarAjax'  => ! empty( $apb_ajax_single_calendar ) ? $apb_ajax_single_calendar : false,
		);

		if ( AWE_function::activated_wpml() ) {
			$params['lang'] = ICL_LANGUAGE_CODE;
		}

		if ( ! is_admin() ) {
			/**
			 * Load script default.
			 */
			$assets_path = trailingslashit( str_replace( array( 'http:', 'https:' ), '', AWE_BK_BASE_URL_PLUGIN ) );

			// If we in debug, just use AWE_BK_BASE_URL_PLUGIN only.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$assets_path = AWE_BK_BASE_URL_PLUGIN . '/';
			}

			wp_enqueue_style( 'owl-carousel', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/css/owl.carousel.css', array(), '1.3.3' );
			wp_enqueue_style( 'apb-layout', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/css/apb-layout.css', array(), AWE_VERSION );
			wp_enqueue_style( 'apb-style', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/css/apb-theme.css', array(), AWE_VERSION );
			if( null != ApbFontIcon::getList() ){
				foreach (ApbFontIcon::getList() as $keyIc => $valIc) {
					wp_enqueue_style( 'apb-icon-' . $valIc->name, $valIc->style, array(), AWE_VERSION );
				}
			}

			$custom_css = '';

			// Custom color for primary color.
			if ( get_option( 'apb-primary' ) ) {
				$color = get_option( 'apb-primary' );
				$custom_css .= '
					.awebooking, .apb-datepicker, .awebooking-widget,
					.apb-calendar .ui-datepicker td a, .ui-datepicker.apb-datepicker td a,
					.apb-room_name a, .apb-room_name a:visited,
					.apb-select, .apb-input.apb-calendar,
					.apb-calendar .ui-datepicker td a,
					.apb-calendar .ui-datepicker td span,
					.apb-calendar .ui-datepicker th span,
					.ui-datepicker.apb-datepicker td a,
					.ui-datepicker.apb-datepicker td span,
					.ui-datepicker.apb-datepicker th span,
					.apb-calendar .ui-datepicker .ui-datepicker-title,
					.apb-calendar .ui-datepicker .ui-datepicker-title span,
					.ui-datepicker.apb-datepicker .ui-datepicker-title,
					.ui-datepicker.apb-datepicker .ui-datepicker-title span,
					.apb-product_tab-header > li > a {
						color: ' . $color . ';
					}';
			}

			// custom color for secondary color.
			if ( get_option( 'apb-secondary' ) ) {
				$color = get_option( 'apb-secondary' );
				$custom_css .= '
					.apb-step ul > li > span {
						background-color: ' . $color . ';
					}

					.apb-step ul > li,
					.apb-field-group .fa,
					.apb-field-group .apbf,
					.apb-field-group .icon,
					.apb-room_package a,
					.apb-room-select-footer .link-other-room,
					.apb-room-selected_item .apb-room-seleted_change,
					.apb-product_thumb .owl-prev:after, .awebooking .apbfc-prev > span:after,
					.awebooking .ui-datepicker-prev > span:after,
					.ui-datepicker.apb-datepicker .apbfc-prev > span:after,
					.ui-datepicker.apb-datepicker .ui-datepicker-prev > span:after,
					.apb-fc-prev:after, .apb-product_thumb .owl-next:after,
					.awebooking .apbfc-next > span:after,
					.awebooking .ui-datepicker-next > span:after,
					.ui-datepicker.apb-datepicker .apbfc-next > span:after,
					.ui-datepicker.apb-datepicker .ui-datepicker-next > span:after, .apb-fc-next:after {
						color: ' . $color . ';
					}
					.apb-room_package-more,
					.apb-calendar .ui-datepicker .ui-datepicker-prev,
					.apb-calendar .ui-datepicker .ui-datepicker-next,
					.ui-datepicker.apb-datepicker .ui-datepicker-prev,
					.ui-datepicker.apb-datepicker .ui-datepicker-next {
						border-color: ' .$color. ';
					}
					';
			}

			// Custom background color.
			if ( get_option( 'apb-background-color' ) ) {
				$color = get_option( 'apb-background-color' );
				$custom_css .= '
					.room-detail_book,
					.awebooking-wrapper,
					.apb-step,
					.apb-content-area .apb-content .apb-heading,
					.apb-calendar_wrap,
					.apb-modal-body .apb-list-price h6,
					.apb-room-selected,
					.apb-room-select-footer  {
						background-color: ' .$color. ';
					}
					';
			}

			// Custom background secondary color.
			if ( get_option( 'apb-background-secondary' ) ) {
				$color = get_option( 'apb-background-secondary' );
				$custom_css .= '
					.apb-select, .apb-input.apb-calendar,
					.apb-calendar .ui-datepicker-inline td a,
					.apb-calendar .ui-datepicker-inline td span,
					.apb-select:focus, .apb-select:active,
					.apb-input.apb-calendar:focus,
					.apb-input.apb-calendar:active,
					.apb-room-selected_item.apb-bg_blue,
					.apb-room-seleted_current.apb-bg_blue {
						background-color: ' .$color. ';
					}
					';
			}

			// Custom highlight color.
			if ( get_option( 'apb-highlight-color' ) ) {
				$color = get_option( 'apb-highlight-color' );
				$custom_css .= '
					.apb-step ul > li.active,
					.apb-step ul > li.step-complete,
					.apb-room_package-more:hover,
					.apb-room_package-more:focus,
					.ui-datepicker.apb-datepicker td a:hover,
					.ui-datepicker.apb-datepicker td a:focus,
					.ui-datepicker.apb-datepicker td.apb-highlight a,
					.apb-modal-body .list-price-item span,
					.apb-room-select-item .apb-room-select-package ul li span,
					.apb-room-select-item .apb-room-select-price .price,
					.awebooking .apbfc-next > span:hover:after,
					.awebooking .ui-datepicker-next > span:hover:after,
					.ui-datepicker.apb-datepicker .apbfc-next > span:hover:after,
					.ui-datepicker.apb-datepicker .ui-datepicker-next > span:hover:after,
					.awebooking .apbfc-prev > span:hover:after,
					.awebooking .ui-datepicker-prev > span:hover:after,
					.ui-datepicker.apb-datepicker .apbfc-prev > span:hover:after,
					.ui-datepicker.apb-datepicker .ui-datepicker-prev > span:hover:after,
					.apb-room-selected_item.apb-bg_blue,
					.apb-room-seleted_current.apb-bg_blue,
					.apb-room_name a:hover,
					.apb-room_name a:focus {
						color: ' .$color. ';
					}

					.apb-step ul > li.active > span,
					.apb-step ul > li.step-complete > span,
					.awebooking .apb-btn,
					.awebooking .apb-btn:visited,
					.apb-calendar .ui-datepicker-inline td a:hover,
					.apb-calendar .ui-datepicker-inline td a:focus,
					.apb-calendar .ui-datepicker-inline td.apb-highlight a,
					.apb-product_tab-header > li.active > a:after,
					.awebooking .apb-btn:hover,
					.awebooking .apb-btn:focus {
						background-color: ' .$color. ';
					}

					.apb-room_package-more:hover, .apb-room_package-more:focus,
					.apb-select:focus, .apb-select:active,
					.apb-input.apb-calendar:focus, .apb-input.apb-calendar:active,
					.ui-datepicker.apb-datepicker td a:hover,
					.ui-datepicker.apb-datepicker td a:focus,
					.ui-datepicker.apb-datepicker td.apb-highlight a,
					.apb-calendar .ui-datepicker .ui-datepicker-prev:hover,
					.apb-calendar .ui-datepicker .ui-datepicker-prev:focus,
					.apb-calendar .ui-datepicker .ui-datepicker-next:hover,
					.apb-calendar .ui-datepicker .ui-datepicker-next:focus,
					.ui-datepicker.apb-datepicker .ui-datepicker-prev:hover,
					.ui-datepicker.apb-datepicker .ui-datepicker-prev:focus,
					.ui-datepicker.apb-datepicker .ui-datepicker-next:hover,
					.ui-datepicker.apb-datepicker .ui-datepicker-next:focus {
						border-color: ' .$color. ';
					}
					';
			}

			// Custom highlight hover color.
			if ( get_option( 'apb-highlight-hover-color' ) ) {
				$color = get_option( 'apb-highlight-hover-color' );
				$custom_css .= '
					.awebooking .apb-btn:hover,
					.awebooking .apb-btn:focus, {
						background-color: ' .$color. ';
					}
					';
			}

			// Custom border color.
			if ( get_option( 'apb-border-color' ) ) {
				$color = get_option( 'apb-border-color' );
				$custom_css .= '
					.apb-heading,
					.room-detail_total {
						border-bottom-color: ' .$color. ';
					}
					.apb-select, .apb-input.apb-calendar {
						border-color: ' .$color. ';
					}
					';
			}

			// Custom CSS.
			if ( get_option( 'apb_custom_css' ) ) {
				$css = get_option( 'apb_custom_css' );
				$custom_css .= wp_unslash( $css );
			}

			wp_add_inline_style( 'apb-style', $custom_css );

			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'phpjs.number-format', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/phpjs.number-format.js', array(), AWE_VERSION, true );
			wp_enqueue_script( 'awebooking-helper', AWE_BK_BASE_URL_PLUGIN . '/assets/plugin/helper.js', array(), AWE_VERSION, true );
			
			if( is_single() && 'apb_room_type' == get_post_type() ) {
				wp_enqueue_script( 'owl-carousel', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/js/owl.carousel.min.js', array( 'jquery' ), '1.3.3', true );
			}
			
			wp_enqueue_script( 'apb-bootstrap', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/js/apb-bootstrap.js', array( 'jquery' ), '1.0.0', true );

			if ( ! empty( $get_lang ) ) {
				wp_enqueue_script( 'apb-js-ui-i18', $assets_path . '/assets/plugin/ui-i18/' . AWE_function::lang_code_wpml_to_datepicker( $get_lang ) . '.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
				wp_localize_script( 'apb-js-ui-i18', 'apb_lang',$get_lang );
			} else {
				$ui_i18 = AWE_function::get_datepicker_lang();
				if ( ! empty( $ui_i18 ) && 'default' != $ui_i18 ) {
					wp_enqueue_script( 'apb-js-ui-i18', $assets_path . '/assets/plugin/ui-i18/' . $ui_i18 . '.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
					wp_localize_script( 'apb-js-ui-i18', 'apb_lang',str_replace( 'datepicker-','',$ui_i18 ) );
				} else {
					wp_enqueue_script( 'apb-js-ui-i18', $assets_path . '/assets/plugin/ui-i18/datepicker-en-US.js', array( 'jquery', 'jquery-ui-datepicker' ), AWE_VERSION, true );
					wp_localize_script( 'apb-js-ui-i18', 'apb_lang','en-US' );
				}
			}

			if ( AWE_function::get_checkout_page_id() == get_the_ID() || in_array( get_the_ID(), $this->lang_page_checkout ) ) {
				wp_enqueue_script( 'apb-js-checkout', $assets_path . '/assets/frontend/js/checkout.js', array( 'jquery' ), AWE_VERSION, true );
				wp_localize_script( 'apb-js-checkout', 'apbAjax', $params );
			}
			wp_enqueue_script( 'moment', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-moment.min.js', false, '2.11.0', true );
			if ( get_the_ID() == AWE_function::get_check_available_page_id() || get_post_type() == 'apb_room_type' || in_array( get_the_ID(), $this->lang_page_check_avb ) ) {
				/**
				 * Fullcalendar.
				 */
				wp_enqueue_script( 'moment', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-moment.min.js', false, '2.11.0', true );
				wp_enqueue_script( 'awe-fullcalendar', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-fullcalendar.min.js', false, '2.6.1', true );
				wp_enqueue_style( 'awe-fullcalendar-style', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/css/apb-fullcalendar.min.css', array(), '2.6.1' );

				if ( 'apb_room_type' == get_post_type() ) {
					wp_enqueue_script( 'awe-fullcalendar', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/apb-fullcalendar.min.js', false, '2.6.1', true );
					if ( ! empty( $fc_lang ) ) {
						wp_enqueue_script( 'awe-fullcalendar-lang', AWE_BK_BASE_URL_PLUGIN . '/assets/backend/js/full-calendar-lang/' . $fc_lang . '.js', false, AWE_VERSION, true );
					}
					wp_enqueue_script( 'awe-single-calendar', AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/js/single-calendar.js', false, AWE_VERSION, true );
					wp_localize_script( 'awe-single-calendar', 'apbAjax', $params );
				}
				if ( get_the_ID() == AWE_function::get_check_available_page_id() ) {
				   // wp_enqueue_script( 'apb-page-calendar',AWE_BK_BASE_URL_PLUGIN. '/assets/frontend/js/apb-calendar.js',false, AWE_VERSION, true );
				   // wp_localize_script( 'apb-page-calendar', 'apb_script_param', $params );
				}
			}

			wp_enqueue_script( 'apb-js-check_available', $assets_path . 'assets/frontend/js/check_available.js', array( 'jquery', 'underscore' ), AWE_VERSION, true );
			if ( is_page() && get_the_ID() == AWE_function::get_check_available_page_id() || get_the_ID() == AWE_function::get_list_room_page_id() || in_array( get_the_ID(), $this->lang_page_list_room ) || get_post_type() == 'apb_room_type' || in_array( get_the_ID(), $this->lang_page_check_avb ) ) {
				if ( 2 == get_option( 'rooms_checkout_style' ) ) {
					wp_enqueue_script( 'apb-js-checkout', $assets_path . '/assets/frontend/js/checkout.js', array( 'jquery' ), AWE_VERSION, true );
					wp_localize_script( 'apb-js-checkout', 'apbAjax', $params );
				}

				// wp_enqueue_script( 'apb-js-cart-checkout', $assets_path . '/assets/frontend/js/apb-cart-checkout.js', array( 'jquery' ), AWE_VERSION, true );
				wp_localize_script( 'apb-js-check_available', 'apbAjax', $params );
				wp_enqueue_script( 'apb-js-add-cart', $assets_path . 'assets/frontend/js/apb-add-to-cart.js', array( 'jquery' ), AWE_VERSION, true );
			}
			wp_enqueue_script( 'apb-js-frontend-scripts', $assets_path . 'assets/frontend/js/scripts.js', array( 'jquery' ), AWE_VERSION, true );
			wp_localize_script( 'apb-js-frontend-scripts', 'apbAjax', $params );
		}
	}

	public function template_include( $template ) {
		
		if ( is_page() ) {
			if ( get_the_ID() == AWE_function::get_check_available_page_id() || in_array( get_the_ID(), $this->lang_page_check_avb ) ) {
				$template_load = $this->template_exsits( 'apb-check-availability' );
				if ( file_exists( $template_load ) ) {
					return $template_load;
				}
			}

			if ( get_the_ID() == AWE_function::get_list_room_page_id() || in_array( get_the_ID(), $this->lang_page_list_room ) ) {
				$template_load = $this->template_exsits( 'apb-list-room' );

				if ( file_exists( $template_load ) ) {
					return $template_load;
				}
			}

			if ( get_the_ID() == AWE_function::get_checkout_page_id() || in_array( get_the_ID(), $this->lang_page_checkout ) ) {
				$template_load = $this->template_exsits( 'apb-checkout' );
				if ( file_exists( $template_load ) ) {
					return $template_load;
				}
			}
		}

		if ( is_single() && get_post_type() == 'apb_room_type' ) {
			$template_load = $this->template_exsits( 'single-room' );
			if ( file_exists( $template_load ) ) {
				return $template_load;
			}
		}

		if ( is_archive() && get_post_type() == 'apb_room_type' ) {
			$template_load = $this->template_exsits( 'apb-list-room' );
			if ( file_exists( $template_load ) ) {
				return $template_load;
			}
		}

		return $template;
	}

	public function template_hook() {
		include 'apb-template-hook.php';
	}
	public function template_function() {
		include 'apb-template-function.php';
	}

	/**
	 * Add Rewrite.
	 */
	public function Apb_prefix_movie_rewrite_rule( $rules ) {
		$newrules = array();
		$newrules['room_type/([^/]*)/room_type_info/([^/]*)'] = 'index.php?room_type=$matches[1]&rt_id=$matches[2]';
		return $newrules + $rules;
	}

	/**
	 * Flush_rules() if our rules are not yet included.
	 * @return void
	 */
	public function my_flush_rules() {
		$rules = get_option( 'rewrite_rules' );

		if ( ! isset( $rules['room_type/([^/]*)/room_type_info/([^/]*)'] ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}

	public function wp_insertMyRewriteQueryVars( $vars ) {
		array_push( $vars, 'rt_id' );
		return $vars;
	}
}

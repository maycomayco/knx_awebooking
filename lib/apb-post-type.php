<?php
/**
 * AWE Post Type
 *
 * @class       AWE_Post_type
 * @version     1.0
 * @package     AweBooking/Classes/
 * @author      AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AWE_Post_type.
 */
class AWE_Post_type extends AWE_function {

	public function __construct() {

		remove_filter( 'woocommerce_register_post_type_shop_order', array( 'WC_Post_types', 'register_post_types' ), 10 );
		remove_action( 'admin_footer', array( 'WC_Admin_Post_Types', 'bulk_admin_footer' ), 10 );

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		// add_action( 'edit_form_after_title', array( $this,"edit_form_after_title") );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxe_room' ), 10 );
		add_action( 'add_meta_boxes' ,  array( $this, 'add_meta_boxe_shop_order' ), 600);

		/**
		 * Custom columns post type room.
		 */
		add_filter( 'manage_edit-apb_room_type_columns', array( $this, 'room_columns' ),15 );
		add_action( 'manage_apb_room_type_posts_custom_column', array( $this, 'render_room_columns_value' ), 2, 2 );

		/*
		 * Custom columns post type room type
		 */
		add_filter( 'manage_edit-room_type_columns', array( $this, 'room_type_columns' ),15 );
		add_action( 'manage_room_type_posts_custom_column', array( $this, 'render_room_type_columns_value' ), 2 );

		// Filters.
		add_filter( 'request', array( $this, 'request_query' ) );

		/*
		 * Custom columns post type shop_order
		 */
		add_filter( "views_edit-shop_order",  array( $this, 'status_filter_custom_shop_order' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'shop_order_column' ),15 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shop_order_column' ), 2);

		/*
		 * Custom bulk action
		 */
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'shop_order_bulk_actions' ) );
		add_action( 'load-edit.php', array( $this, 'bulk_action' ),10 );
		add_action( 'admin_footer', array( $this, 'apb_bulk_admin_footer' ), 9 );
		if ( isset( $_SESSION['apb_fail'] ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_shop_order' ) );
			unset( $_SESSION['apb_fail'] );
		}
		add_action( 'admin_head', array( $this, 'menu_highlight' ), 11 );

		add_action( 'admin_init', array( $this, 'remove_shop_order_title' ) );

		//add_action( 'posts_where', array( $this, 'hide_future_posts', 10, 2 ) );
		//

	 	// Custom wpml duplicate field
	 	add_action('icl_make_duplicate',  array( $this, 'apb_room_type_make_duplicate' ), 10, 4 );

	 	add_action( 'admin_init', array($this,'apb_unhook_wp_head_footer'),100 );
	}

	public function apb_unhook_wp_head_footer(){
		global $wp_filter;

		foreach ($wp_filter['add_meta_boxes'][10] as $key => $value) {
			if( $value['function'][1] == 'remove_meta_boxes' ) {
				remove_action( 'add_meta_boxes', $wp_filter['add_meta_boxes'][10][$key]['function'], 10 );
			}
		}

	}

	public function hide_future_posts($where, $q) {
	   var_dump($where);

	   return $where;
	}



	public function admin_notice_shop_order() {
		?>
		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery(".notice-success").remove();
		  });
		</script>
		<?php
		echo '<div class="notice notice-error" id="message"><p>Order updated fail.</p></div>';
	}

	public function register_post_type() {
		register_post_type( 'apb_room_type', apply_filters( 'awe_post_type_room_type', array(
			'labels' => array(
				'name'                => _x( 'Room Type', 'Post Type General Name', 'awebooking' ),
				'singular_name'       => _x( 'AweBooking', 'Post Type Singular Name', 'awebooking' ),
				'menu_name'           => __( 'AweBooking', 'awebooking' ),
				'all_items'           => __( 'All Room Types', 'awebooking' ),
				'view_item'           => __( 'View Room Type', 'awebooking' ),
				'add_new_item'        => __( 'Add New Room Type', 'awebooking' ),
				'add_new'             => __( 'Add New Room Type', 'awebooking' ),
				'edit_item'           => __( 'Edit Room Type', 'awebooking' ),
				'update_item'         => __( 'Update Room Type', 'awebooking' ),
				'search_items'        => __( 'Search Room Types', 'awebooking' ),
				'not_found'           => __( 'No Room Types found', 'awebooking' ),
				'parent'              => __( 'Parent Hotel', 'awebooking' ),
			),
			'description'           => __( 'This is where store orders are stored.', 'awebooking' ),
			'public'                => true,
			'show_ui'               => true,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => true,
			'hierarchical'          => false,
			'show_in_nav_menus'     => true,
			'rewrite'               => array( 'slug' => 'apb-room-type' ),
			'query_var'             => true,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'           => 'apb_room_type',
			'menu_icon'             => AWE_BK_BASE_URL_PLUGIN . '/assets/backend/images/calendar.ico', // 'dashicons-calendar-alt',
		) ) );

		register_post_type( 'shop_order', apply_filters( 'awe_post_type_shoporder', array(
			'labels' => array(
				'name'                => _x( 'Bookings', 'Post Type General Name', 'awebooking' ),
				'singular_name'       => _x( 'Bookings', 'Post Type Singular Name', 'awebooking' ),
				'menu_name'           => __( 'Bookings', 'awebooking' ),
				'all_items'           => __( 'Bookings', 'awebooking' ),
				'view_item'           => __( 'View Bookings', 'awebooking' ),
				'add_new_item'        => __( 'Add New Bookings', 'awebooking' ),
				'add_new'             => __( 'New Bookings', 'awebooking' ),
				'edit_item'           => __( 'Edit Bookings', 'awebooking' ),
				'update_item'         => __( 'Update Bookings', 'awebooking' ),
				'search_items'        => __( 'Search Bookings', 'awebooking' ),
				'not_found'           => __( 'No Rooms found', 'awebooking' ),
				'parent'              => __( 'Parent Hotel', 'awebooking' ),
			),
			'description'         => __( 'This is where store orders are stored.', 'awebooking' ),
			'public'                => true,
			'show_ui'               => true,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'publicly_queryable'    => true,
			'has_archive'           => true,
			'exclude_from_search'   => true,
			'hierarchical'          => false,
			'rewrite'               => false,
			'query_var'             => true,
			'show_in_menu'        => 'edit.php?post_type=apb_room_type',
			'supports'              => true,
		) ) );

		register_post_type( 'apb_room', apply_filters( 'awe_post_type_room', array(
			'labels'                => __( 'Room', 'awebooking' ),
			'public'                => false,
			'show_ui'               => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'publicly_queryable'    => false,
			'exclude_from_search'   => true,
			'hierarchical'          => false,
			'show_in_nav_menus'     => false,
			'rewrite'               => false,
			'query_var'             => false,
			'supports'              => array(),
			'has_archive'           => false,
		) ) );

		register_post_type( 'apb_order', apply_filters( 'awe_post_type_apb_order', array(
			'labels' => array(
				'name'                => _x( 'Bookings', 'Post Type General Name', 'awebooking' ),
				'singular_name'       => _x( 'Bookings', 'Post Type Singular Name', 'awebooking' ),
				'menu_name'           => __( 'Bookings', 'awebooking' ),
				'all_items'           => __( 'Bookings', 'awebooking' ),
				'view_item'           => __( 'View Bookings', 'awebooking' ),
				'add_new_item'        => __( 'Add New Bookings', 'awebooking' ),
				'add_new'             => __( 'New Bookings', 'awebooking' ),
				'edit_item'           => __( 'Edit Bookings', 'awebooking' ),
				'update_item'         => __( 'Update Bookings', 'awebooking' ),
				'search_items'        => __( 'Search Bookings', 'awebooking' ),
				'not_found'           => __( 'No Rooms found', 'awebooking' ),
				'parent'              => __( 'Parent Hotel', 'awebooking' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => '',
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => false,
			'has_archive'         => false,
		) ) );

	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public static function register_post_status() {
		register_post_status( 'apb-pending', array(
			'label'                     => _x( 'Pending payment', 'Order status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending payment <span class="count">(%s)</span>', 'Pending payment <span class="count">(%s)</span>', 'awebooking' ),
		) );
		register_post_status( 'apb-processing', array(
			'label'                     => _x( 'Processing', 'Order status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'awebooking' ),
		) );

		register_post_status( 'apb-completed', array(
			'label'                     => _x( 'Completed', 'Order status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'awebooking' ),
		) );

		register_post_status( 'apb-cancelled', array(
			'label'                     => _x( 'Cancelled', 'Order status', 'awebooking' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'awebooking' ),
		) );
	}


	public function edit_form_after_title( $post ) {
		global $typenow;
		if ( 'shop_order' == $typenow ) {
			echo '<style>div#titlewrap{display: none;}</style>';
		}
	}


	public function add_meta_boxe_room() {
		add_meta_box( 'apb-room-meta-box', __( 'Room Data', 'awebooking' ), 'boxes_package_room_type::output', 'apb_room_type', 'normal', 'high' );
		add_meta_box( 'apb-gallery', __( 'Create Gallery', 'awebooking' ), 'create_gallery::output', 'apb_room_type', 'side', 'default', '' );
	}

	/**
	 * Customize column post type room.
	 */

	public function room_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['title'] );
		$new_columns = array(
			'img'            => __( '<i class="fa fa-file-image-o"></i>', 'awebooking' ),
			'title'          => __( 'Title', 'awebooking' ),
			// 'manage_avb'     => __( 'Manage Availability', 'awebooking' ),
			// 'manage_pricing' => __( 'Manage Pricing', 'awebooking' ),
			'number_of_rooms'   => __( 'Number of rooms', 'awebooking' ),
			'base_price'    => __( 'Base price', 'awebooking' ),
			'date'          => __( 'Date', 'awebooking' ),
		);

		return array_merge( $columns, $new_columns );
	}

	public function render_room_columns_value( $column, $post_id ) {
		$extra_sale = get_post_meta( $post_id, 'extra_sale', true );

		switch ( $column ) {
			case 'img':
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', false );
				if ( ! empty( $image ) ) {
					$image = $image[0];
				} else {
					$image = AWE_BK_BASE_URL_PLUGIN . '/assets/backend/images/placeholder.png';
				}

				if ( ! empty( $extra_sale ) ) {
					$sale_icon = ' <img class="apb-sale-icon" src="' . esc_url( AWE_BK_BASE_URL_PLUGIN . '/assets/backend/images/sale-icon.png' ) . '">';
				} else {
					$sale_icon = '';
				}
				echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $post_id ) . '&action=edit' ) ) . '">' . wp_kses_post( $sale_icon ) .
					'<img width="150" height="150" class="wp-post-image" src="' . esc_url( str_replace( '-150x150', '', $image ) ) . '"></a>';

				break;

			case 'number_of_rooms':
				echo get_post_meta( $post_id, 'number_of_rooms', true ) ? absint( get_post_meta( $post_id, 'number_of_rooms', true ) ) : 1;
				break;

			case 'base_price':
				echo wp_kses( AWE_function::apb_price( ( float ) get_post_meta( $post_id, 'base_price', true ) ), array() );
				break;
		}
	}
	 #################### Customize column post type room type ####################

	public function room_type_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['title'] );
		$new_columns = array(
			'img'            => '<i class="fa fa-file-image-o"></i>',
			'title'          => __( 'Title', 'awebooking' ),
			'date'           => __( 'Date', 'awebooking' ),
		);

		return array_merge( $columns, $new_columns );
	}

	public function render_room_type_columns_value( $column ) {
		global $post;
		switch ( $column ) {
			case 'img':
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail', false );
			if ( ! empty( $image ) ) {
				$image = $image[0];
			} else {
				$image = AWE_BK_BASE_URL_PLUGIN . '/assets/backend/images/placeholder.png';
			}
			echo '<a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">
				<img class="wp-post-image" src="' . esc_url( $image ) . '"></a>';
				break;
		}
	}


	public function request_query( $vars ) {
		global $typenow, $wp_query, $wp_post_statuses;
		if ( 'shop_order' === $typenow ) {
			if ( ! isset( $vars['post_status'] ) ) {
				if ( function_exists( 'wc_get_order_statuses' ) ) {
					$post_statuses =  wc_get_order_statuses();
				} else {
					$post_statuses = AWE_function::apb_get_order_statuses();
				}
				$post_statuses['publish'] = __( 'Publish', 'awebooking' );
				$post_statuses['trash']   = __( 'Trash', 'awebooking' );
				foreach ( $post_statuses as $status => $value ) {
					if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
						unset( $post_statuses[ $status ] );
					}
				}

				$vars['post_status'] = array_keys( $post_statuses );
			}
		}
		return $vars;
	}
	#################### End custom column post type room ####################


	#################### Custom column post type shop_order ####################
	public function status_filter_custom_shop_order( $columns ) {
		$counts = ( array ) wp_count_posts( 'shop_order' );
		if ( class_exists( 'WooCommerce' ) ) {
			unset( $columns['wc-on-hold'] );
			unset( $columns['wc-cancelled'] );
			// unset( $columns['wc-pending'] );
		}

		$args_deposit = array(
			'post_type' => 'shop_order',
			'post_status' => 'apb-completed',
		   	'meta_query' => array(
		       	array(
		            'key' => '_apb_prepayment_type',
		       	)
		   )
		);
		$deposit_query = get_posts($args_deposit);

		$columns['apb-pending'] = '<a href="edit.php?post_status=apb-pending&post_type=shop_order">' . __( 'Pending payment', 'awebooking' ) . ' <span class="count">( ' . $counts['apb-pending'] . ' )</span></a>';
		$columns['apb-completed'] = '<a href="edit.php?post_status=apb-completed&post_type=shop_order">' . __( 'Completed', 'awebooking' ) . ' <span class="count">( ' . $counts['apb-completed'] . ' )</span></a>';
		$columns['apb-completed-deposit'] = '<a href="edit.php?post_status=apb-completed_deposit&post_type=shop_order">' . __( 'Pre Payment', 'awebooking' ) . ' <span class="count">( ' . count( $deposit_query ) . ' )</span></a>';
		$columns['apb-cancelled'] = '<a href="edit.php?post_status=apb-cancelled&post_type=shop_order">' . __( 'Cancelled', 'awebooking' ) . ' <span class="count">( ' . $counts['apb-cancelled'] . ' )</span></a>';
		$columns['trash'] = '<a href="edit.php?post_status=trash&post_type=shop_order">' . __( 'Trash', 'awebooking' ) . ' <span class="count">( ' . $counts['trash'].' )</span></a>';

		return $columns;
	}

	public function shop_order_column( $columns ) {

		unset( $columns['title'], $columns['comments'], $columns['date'] );
		unset( $columns['order_items'] );
		unset( $columns['shipping_address'] );
		unset( $columns['customer_message'] );
		unset( $columns['order_notes'] );
		unset( $columns['order_date'] );
		unset( $columns['order_total'] );
		unset( $columns['order_title'] );
		unset( $columns['order_actions'] );
		unset( $columns['order_status'] );

		$columns['apb_order_title']     = __( 'Order', 'awebooking' );
		$columns['apb_order_arrival']   = __( 'Arrival Date', 'awebooking' );
		$columns['apb_order_departure'] = __( 'Departure Date', 'awebooking' );

		$columns['apb_status']            = __( 'Status', 'awebooking' );
		$columns['apb_order_total']     = __( 'Total', 'awebooking' );
		$columns['apb_order_date']      = __( 'Date', 'awebooking' );
		//$columns['deposit']          	= __( 'Payment', 'awebooking' );
		$columns['apb_action']          = __( 'Action', 'awebooking' );


		return $columns;
	}

	public function render_shop_order_column( $column ) {
		global $post;

		$room_info = get_post_meta( $post->ID, 'data_room', true );

		switch ( $column ) {
			case 'apb_order_title':

				if ( get_post_meta($post->ID, '_shipping_last_name', true) ) {
					$username = get_post_meta($post->ID, '_shipping_first_name', true) . ' ' . get_post_meta($post->ID, '_shipping_last_name', true);
				} else {
					$user = get_post_meta( $post->ID, 'apb_customer_name', true );
					if ( ! empty( $user ) ) {
						$username = $user;
					} else {
						$cm_id = get_post_meta( $post->ID, 'custommer', true );
						$get_user = get_user_by( 'id',$cm_id );
						$get_user = (array) $get_user;

						if(!empty($get_user['data'])){
							$data_user = (array) $get_user['data'];
							$username = $data_user['display_name'];
							} else {
								$username = 'Guest';
							}
						}
					}

				printf(
					esc_html__( '%s by %s', 'awebooking' ),
					'<a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">#' . absint( $post->ID ) . '</a>',
					esc_html( $username )
				);
				break;


			case 'apb_order_arrival':
				echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( get_post_meta( $post->ID, 'from', true ) ) ) );
				break;

			case 'apb_order_departure':
				echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( get_post_meta( $post->ID, 'to', true ) ) ) );
				break;

			case 'apb_status':
				if ( function_exists( 'wc_get_order_statuses' ) ) {
					$order_status = wc_get_order_statuses();
				} else {
					$order_status = AWE_function::apb_get_order_statuses();
				}
				$order_status['publish'] = 'Publish';
				$order_status['trash']   = 'trash';
				$order_status['draft']   = 'draft';
				$order_status['apb-cancelled'] = _x( 'Cancelled', 'Order status', 'awebooking' );
				$apb_deposit_type = get_post_meta( $post->ID, '_pre_payment_type' );

				if ( empty( $apb_deposit_type ) || 'none' == $apb_deposit_type[0] ) {
					echo esc_html( $order_status[ get_post_status( $post->ID ) ] );
				} else {
					if( 'Completed' == $order_status[ get_post_status( $post->ID ) ] ){
						echo $order_status[ get_post_status( $post->ID ) ];
					} else {
						echo esc_html( 'Pre Payment','awebooking' );
					}
				}
				break;

			case 'apb_action' :
				if( !empty($_GET['post_status']) ){
					if( 'trash' == $_GET['post_status'] ){
						 $_wpnonce = wp_create_nonce( 'untrash-post_' . absint( $post->ID ) );
						echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $post->ID ) ) ) . '&action=untrash&_wpnonce=' . $_wpnonce  . '">' . esc_html__( 'Restore', 'awebooking' ) . '</a>';
					}
				}else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=trashbook&book_id=' . absint( $post->ID ) ) ) . '">' . esc_html__( 'Trash', 'awebooking' ) . '</a>';
				}
				break;
			case 'apb_order_date' :

				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$t_time = $h_time = __( 'Unpublished', 'awebooking' );
				} else {
					$h_time = get_the_time( AWE_function::get_current_date_format(), $post );
					$t_time = get_the_time( AWE_function::get_current_date_format() . ' ' . get_option( 'time_format' ), $post );
				}

				echo '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $post ) ) . '</abbr>';

				break;
			case 'apb_order_total' :
				$currency = AWE_function::get_option( 'woocommerce_currency' ) ? AWE_function::get_option( 'woocommerce_currency' ) : '$';
				echo isset( $room_info['price'] ) ? AWE_function::apb_price( $room_info['price'] ) : AWE_function::apb_price( get_post_meta( $post->ID, '_order_total', true ) );
				break;
		}
	}

	public function add_meta_boxe_shop_order() {


		add_meta_box( 'apb-info-booking-meta-box', __( 'Info booking', 'awebooking' ), 'Boxes_info_booking::output', 'shop_order', 'normal', 'high' );

		if ( 2 == get_option( 'rooms_checkout_style' ) ) {
			add_meta_box( 'apb-order-data', sprintf( __( 'Booking %s', 'awebooking' ), '#' . get_the_ID() ), 'boxes_apb_order_data::output', 'shop_order', 'normal', 'high' );
			remove_meta_box( 'woocommerce-order-data' , 'shop_order', 'normal' );
		}

		remove_meta_box( 'woocommerce-order-items', 'shop_order', 'normal' );

		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		remove_meta_box( 'woocommerce-product-data', 'product', 'high' );
		remove_meta_box( 'woocommerce-product-images', 'product', 'side' );
		if( function_exists('wc_get_order_types') ) {
			foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
				remove_meta_box( 'woocommerce-order-data',  $type, 'normal', 'high' );
				remove_meta_box( 'woocommerce-order-items',  $type, 'normal', 'high' );
				remove_meta_box( 'woocommerce-order-notes', $type, 'side', 'default' );
				remove_meta_box( 'woocommerce-order-downloads', $type, 'normal', 'default' );
				remove_meta_box( 'woocommerce-order-actions', $type, 'side', 'high' );
			}
		}

		remove_meta_box( 'woocommerce-coupon-data','normal', 'high' );

	}

	/**
	 * [shop_order_bulk_actions description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
	public function shop_order_bulk_actions( $actions ) {
		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}
	/**
	 * [bulk_action description]
	 * @return [type] [description]
	 */
	public function bulk_action() {
		global $typenow,$wpdb;
		if ( 'shop_order' == $typenow ) {

			// Custom filter deposit payments.
			if( isset( $_GET['post_status'] ) &&  'apb-completed_deposit' == $_GET['post_status'] ){
				add_filter( 'posts_where' , function( $where ){
					global $wpdb;
					$where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_apb_prepayment_type' )";
					return $where;
				} );
			}

			// Custom action bulk action.
			if ( isset( $_GET['post'] ) && isset( $_GET['action'] ) || isset( $_GET['post'] ) && isset( $_GET['action2'] ) ) {
				$post_ids = array_map( 'absint', ( array ) $_GET['post'] );

				if ( 'trash' == $_GET['action'] || 'trash' == $_GET['action2'] ) {
					foreach ( $post_ids as $post_id ) {

						/*----------  Send mail after remove order  ----------*/
						$get_info_user = ( get_post_meta( $post_id, 'info_custom_order', true ) );
						$this->apb_mail_remove_order( $post_id );
						if ( get_option( 'rooms_checkout_style' ) == 1 ) {
							$order = wc_get_order( $post_id );
							$this->apb_mail_remove_order( $post_id );
						}

						$order_total_item = get_post_meta( $post_id, 'apb_order_id', true );
						$book = get_post( $post_id );
						if ( 'apb-completed' == $book->post_status ) {
							if ( ! empty( $order_total_item ) ) {
								foreach ( $order_total_item as $item_id ) {
									$room_id = get_post_meta( $item_id, 'order_room_id', true );
									$from = get_post_meta( $item_id, 'from', true );
									$to = get_post_meta( $item_id, 'to', true );

									AWE_function::update_available( $from, $to, $room_id, 2 );
									// AWE_Controller::update_day_available( $from, $to, $room_id, 2 );
									$item = get_post( $item_id );
									wp_update_post( array(
										'ID'    => $item_id,
										'post_status'   => 'trash',
									) );
								}
							}
							wp_update_post( array(
								'ID'    => $post_id,
								'post_status'   => 'trash',
							) );
						} else {
							if ( ! empty( $order_total_item ) ) {
								foreach ( $order_total_item as $item_id ) {
									$item = get_post( $item_id );
									wp_update_post( array(
										'ID'    => $item_id,
										'post_status'   => 'trash',
									) );
								}
							}
						}
					}
				} elseif ( 'untrash' == $_GET['action'] || 'untrash' == $_GET['action2'] ) {

					foreach ( $post_ids as $post_id ) {
						$order_total_item = get_post_meta( $post_id, 'apb_order_id', true );
						foreach ( $order_total_item as $item_id ) {

							$from    = get_post_meta( $item_id, 'from', true );
							$to      = get_post_meta( $item_id, 'to', true );
							$room_id = get_post_meta( $item_id, 'order_room_id', true );

							$check_available = AWE_function::check_room_available( $from, $to, $room_id, true );
							if ( ! empty( $check_available ) ) {
								AWE_function::update_available( $from, $to, $room_id, 3 );
								// AWE_Controller::update_day_available( $from, $to, $room_id, 3 );
								$item = get_post( $item_id );
								wp_update_post( array(
									'ID'    => $item_id,
									'post_status'   => 'apb-pending',
								) );
							}
						}
						wp_update_post( array(
							'ID'    => $post_id,
							'post_status'   => 'apb-pending',
						) );
						wp_redirect( admin_url( 'edit.php?post_type=shop_order' ), 301 );
						die;
					}
				} elseif ( 'apb_export' == $_GET['action'] || 'apb_export' == $_GET['action2'] ) {
					$array_id  = $_GET['post'];
					$output = apb_export_to_excel( $array_id );

					header("Content-type: application/vnd.ms-excel; name='excel'");
					header("Content-Disposition: attachment; filename=" . $_SERVER['SERVER_NAME'] . '_' . date('Ymd') . ".xls");
					header("Pragma: no-cache");
					header("Expires: 0");
					print($output);

					die();
				} elseif ( 'apb_delete' == $_GET['action'] || 'apb_delete' == $_GET['action2'] ) {

					foreach ( $post_ids as $post_id ) {
						$order_total_item = get_post_meta( $post_id, 'apb_order_id', true );
						foreach ( $order_total_item as $item_order ) {
							$room_id = get_post_meta( $item_order, 'order_room_id', true );
							$from = get_post_meta( $item_order, 'from', true );
							$to = get_post_meta( $item_order, 'to', true );

							AWE_function::update_available( $from, $to, $room_id, 2 );
							// AWE_Controller::update_day_available( $from, $to, $room_id, 2 );

							wp_delete_post( $item_order );
						}

						/*----------  Send mail after remove order  ----------*/
						$get_info_user = get_post_meta( $post_id, 'info_custom_order', true );
						if ( isset( $get_info_user['apb-email'] ) ) {
							$this->apb_mail_remove_order( $post_id );
						}
						if ( get_option( 'rooms_checkout_style' ) == 1 ) {
							$order = wc_get_order( $post_id );
							$this->apb_mail_remove_order( $post_id );
						}
						wp_delete_post( wp_kses( $post_id, array() ) );
					}
				}
			}
		}

		if ( 'apb_room_type' == $typenow ) {
			if ( isset( $_GET['post'] ) && isset( $_GET['action'] ) || isset( $_GET['post'] ) && isset( $_GET['action2'] ) ) {
				$post_ids = array_map( 'absint', ( array ) $_GET['post'] );
				if ( 'trash' == $_GET['action'] || 'trash' == $_GET['action2'] ) {
					foreach ( $post_ids as $post_id ) {
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}apb_booking_options WHERE object_id = %d", $post_id ) );
					}
				}
			}
		}
	}

	/**
	 * Add extra bulk action options to mark orders as complete or processing
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031.
	 */
	public function apb_bulk_admin_footer() {
		global $typenow;
		if ( 'shop_order' == $typenow ) {
			?>
			<script type="text/javascript">
				jQuery(function() {

					jQuery( '<option>' ).val( 'apb_delete' ).text( '<?php esc_html_e( 'Awe Delete', 'awebooking' )?>' ).appendTo("select[name='action']");
					jQuery( '<option>' ).val( 'apb_delete' ).text( '<?php esc_html_e( 'Awe Delete', 'awebooking' )?>' ).appendTo("select[name='action2']");

					/*----------  Export to Excel  ----------*/
					jQuery( '<option>' ).val( 'apb_export' ).text( '<?php esc_html_e( 'Awe Export', 'awebooking' )?>' ).appendTo("select[name='action']");
					jQuery( '<option>' ).val( 'apb_export' ).text( '<?php esc_html_e( 'Awe Export', 'awebooking' )?>' ).appendTo("select[name='action2']");
				});
			</script>
			<?php
		}
	}

	/**
	 * wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-room
	 * wp-has-submenu wp-not-current-submenu menu-top menu-icon-room
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;
		switch ( $post_type ) {
			case 'shop_order':
			?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery("#menu-posts-room").removeAttr( 'class' );
					jQuery("#menu-posts-room").attr( 'class', 'wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-room' );
				});
				</script>
			<?php

			case 'shop_coupon' :
				$parent_file = '';
			break;
		}
	}


	/**
	 * Remove order.
	 * @param  string $mail     Email.
	 * @param  int    $order_id Order ID.
	 * @return void
	 */
	public function apb_mail_remove_order( $order_id ) {
		$mail = AWE_function::get_customer_email( $order_id );
		$to_name = AWE_function::get_customer_name( $order_id );
		$no_message = 0;
		$config_mail = get_option( 'apb_mail_cancel' );
		$subject = ! empty( $config_mail['subject'] ) ? $config_mail['subject'] : '[{site_title}] Cancelled order ({order_number})';
		$subject = AWE_function::email_str_replace( $subject, $order_id );
		$heading = ! empty( $config_mail['header'] ) ? $config_mail['header'] : 'Cancelled booking';
		$heading = AWE_function::email_str_replace( $heading, $order_id );

		ob_start();
		do_action( 'apb_mail_cancelled_order', $order_id );
		$message = ob_get_clean();

		$email = new APB_Email();

		if ( isset( $config_mail['notice_status'] ) && 1 == $config_mail['notice_status'] ) {
			$message_user = $email->apb_style_inline( $email->apb_wrap_message( $heading, $message ) );
			$email->apb_sendMail( $mail, $subject, $message_user, $no_message, $to_name );
		}

		$email->destroy();
	}


	/**
	 * Remove order title.
	 *
	 * @since 2.4
	 */
	public function remove_shop_order_title() {
		remove_post_type_support( 'shop_order', 'title' );
	}

	/**
	 * apb_room_type_make_duplicate
	 *
	 * @return void
	 */
	public function apb_room_type_make_duplicate( $master_post_id, $lang, $post_array, $id ){
		global $wpdb;

		$info_post_make = get_post( $id );
		if( 'apb_room_type' == $info_post_make->post_type ) {
			$get_option = AWE_function::get_room_option( $master_post_id, 'apb_room_type' );

			foreach ($get_option as $key => $value) {
				$wpdb->insert(
					$wpdb->prefix . 'apb_booking_options',
					array(
						'entity_type'       => 'apb_room_type',
						'object_id'         => absint( $info_post_make->ID ),
						'total'             => $value->total,
						'option_name'       => $value->option_name,
						'option_operation'  => $value->option_operation,
						'option_value'      => $value->option_value,
						'option_icon'       => $value->option_icon,
						'option_desc'       => $value->option_desc,
						'status'            => 'publish',
						'revision_id'       => $value->revision_id,
					),
					array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
				);
			}
		}
	}

}

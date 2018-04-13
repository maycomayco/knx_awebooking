<?php
/**
 * AWE Widget
 *
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check available form widget.
 */
class Form_check_available extends WP_Widget {

	function __construct() {
		// Instantiate the parent object.
		parent::__construct( false, 'Awe Booking: Form Check Available' );
	}

	function widget( $args, $instance ) {
		$defaults = array(
			'title' => __( 'Your Reservation', 'awebooking' ),
			'status_departure'      => 1,
			'status_night_number'   => 1,
			'status_multi_room'     => 1,
			'form_style'            => 1,
		);

		$instance = wp_parse_args( ( array ) $instance, $defaults );

		do_action( 'apb_get_day_advance' );
		?>
		<div class="awebooking">
			<div class="awebooking-widget">
				<?php include AWE_function::template_exsits( 'layout/apb-widget-check-availability' ); ?>
			</div>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags from title and name to remove HTML.
		$instance['title']                = wp_kses( $new_instance['title'], array() );
		$instance['status_departure']     = wp_kses( $new_instance['status_departure'], array() );
		$instance['status_night_number']  = wp_kses( $new_instance['status_night_number'], array() );
		$instance['status_room_type']     = wp_kses( $new_instance['status_room_type'], array() );
		$instance['status_multi_room']    = wp_kses( $new_instance['status_multi_room'], array() );
		$instance['form_style']           = wp_kses( $new_instance['form_style'], array() );

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( 'Your Reservation', 'awebooking' ),
			'status_departure'      => 1,
			'status_night_number'   => 1,
			'status_multi_room'     => 1,
			'form_style'            => 1,
		);

		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'awebooking' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'status_departure' ) ); ?>" class="apb_departure" name="<?php echo esc_attr( $this->get_field_name( 'status_departure' ) ); ?>" <?php checked( $instance['status_departure'], 1, true ); ?> type="checkbox" value="1">
			<label for="<?php echo esc_attr( $this->get_field_id( 'status_departure' ) ); ?>"> <?php esc_html_e( 'Enable Departure Date', 'awebooking' ) ?></label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'status_night_number' ) ); ?>" class="apb_nightnum" name="<?php echo esc_attr( $this->get_field_name( 'status_night_number' ) ); ?>" <?php checked( $instance['status_night_number'], 1, true ); ?> type="checkbox" value="1">
			<label for="<?php echo esc_attr( $this->get_field_id( 'status_night_number' ) ); ?>"><?php esc_html_e( 'Enable Night Number', 'awebooking' ) ?></label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'status_multi_room' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'status_multi_room' ) ); ?>" <?php checked( $instance['status_multi_room'], 1, true ); ?> type="checkbox" value="1">
			 <label for="<?php echo esc_attr( $this->get_field_id( 'status_multi_room' ) ); ?>"> <?php esc_html_e( 'Enable Multiple Room Selection', 'awebooking' ) ?></label>
		</p>
		<div>
			<label><?php esc_html_e( 'Form Style:', 'awebooking' ); ?> </label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'form_style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'form_style' ) ); ?>">
				<option <?php selected( $instance['form_style'], 1 ); ?> value="1"><?php esc_html_e( 'Vertical', 'awebooking' ) ?></option>
				<option <?php selected( $instance['form_style'], 2 ); ?> value="2"><?php esc_html_e( 'Horizontal', 'awebooking' ) ?></option>
			</select>
		</div>

		<?php
	}
}

function wc_custom_register_widgets() {
	register_widget( 'Form_check_available' );
}
add_action( 'widgets_init', 'wc_custom_register_widgets' );

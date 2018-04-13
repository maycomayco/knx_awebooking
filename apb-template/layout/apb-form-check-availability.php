<?php
/**
 * The template for displaying form check available.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_check = 0;
if ( is_page() && get_the_ID() == AWE_function::get_check_available_page_id() ) {
	$is_check = 1;
}

$auto_check = 0;
if ( $is_check && isset( $_GET['check_from'] ) ) {
	$auto_check = 1;
}
?>
<form action="<?php echo esc_url( AWE_function::get_check_available_page() ); ?>" method="GET" class="apb-check-avb-form <?php echo ! $is_check ? 'non-ajax' : ''; ?> <?php echo $auto_check ? 'auto-check' : ''; ?>">

	<div class="awebooking-wrapper">
		<h2 class="apb-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ) ?></h2>

		<div class="apb-content">

			<div class="apb-field">
				<label><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>

				<div class="apb-field-group">
					<i class="apbf apbf-calendar"></i>

					<?php
					/**
					 * General input from date.
					 */
					AWE_function::apb_gen_input(array(
						'type'          => 'text',
						'name'          => 'from',
						'placeholder'   => __( 'Arrival Date ', 'awebooking' ),
						'class'         => 'date-start-js apb-calendar apb-input',
						'value'         => $from,
						'data-date'     => isset( $_from ) ? $_from : '',
					) );
					?>
				</div>
			</div>

			<?php if ( 1 == $apb_setting['field_status']['status_night_number_page'] ) : ?>
				<div class="apb-field">
					<label><?php esc_html_e( 'Nights', 'awebooking' ); ?></label>
					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>

						<?php
						/**
						 * General selected total numner of night.
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name'      => '',
							'count_num' => get_option( 'max_night' ),
							'data'      => array( 'class' => 'apb-select night-select-js' ),
							'select'    => $total_night,
							'value'		=> $total_night,
						) );
						?>
					</div>
				</div>
			<?php endif; ?>

			<input type="hidden" value="<?php echo absint( get_option( 'max_night' ) ); ?>" class="max-night-js">

			<?php if ( 1 == $apb_setting['field_status']['status_departure_page'] ) : ?>
				<div class="apb-field">
					<label><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-calendar"></i>

						<?php
						/**
						 * General input to date.
						 */
						AWE_function::apb_gen_input( array(
							'type'          => 'text',
							'name'          => 'to',
							'placeholder'   => __( 'Departure Date', 'awebooking' ),
							'class'         => 'date-end-js apb-calendar apb-input',
							'value'         => $to,
							'data-date'     => isset( $_to ) ? $_to : '',
						) );
						?>
					</div>
				</div>
			<?php else : ?>
				<?php echo '<input type="hidden" name="to" class="date-end-js" value="' . esc_attr( $to ) . '" data-date="' . esc_attr( $_to ) . '">'; ?>
			<?php endif; ?>

			<?php
			/**
			 * Hook apb_after_check_available_date_field
			 */
			do_action( 'apb_after_check_available_date_field', $apb_setting['field_status'] );
			?>

			<?php
			if ( is_singular( 'apb_room_type' ) ) {
				?>
				<input type="hidden" name="room_type_id" value="<?php the_ID(); ?>">
				<?php
			} else {
				if ( ! isset( $apb_setting['field_status']['status_room_type_page'] ) || 1 == $apb_setting['field_status']['status_room_type_page'] ) :
					$room_type_value = isset( $_GET['room_type_id'] ) ? absint( $_GET['room_type_id'] ) : ( is_singular( 'apb_room_type' ) ? get_the_ID() : 0 );
					?>
					<div class="apb-field">
						<label><?php esc_html_e( 'Room Type', 'awebooking' ); ?></label>

						<div class="apb-field-group">
							<i class="apbf apbf-select"></i>

							<select name="room_type_id" class="apb-select">
								<option value="0" <?php selected( $room_type_value, 0 ); ?>>- <?php esc_html_e( 'All', 'awebooking' ); ?></option>
								<?php
								$room_types = AWE_function::get_room_type();
								foreach ( $room_types as $room_type ) {
									printf(
										'<option value="%1$s" %2$s>%3$s</option>',
										absint( $room_type->ID ),
										selected( $room_type->ID, $room_type_value, false ),
										esc_html( $room_type->post_title )
									);
								}
								?>
							</select>
						</div>
					</div>
				<?php
				elseif ( ! empty( $_GET['room_type_id'] ) ) :
					?>
					<input type="hidden" name="room_type_id" value="<?php echo absint( $_GET['room_type_id'] ); ?>">
					<?php
				endif;
			}
			?>

			<?php
			/**
			 * Hook apb_after_check_available_room_type_field
			 */
			do_action( 'apb_after_check_available_room_type_field', $apb_setting['field_status'] );
			?>

			<?php if ( 1 == $apb_setting['field_status']['status_multi_room_page'] ) : ?>
				<div class="apb-field">
					<label><?php esc_html_e( 'Rooms', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>

						<?php
						/**
						 * General selected number of room.
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name'      => 'room_num',
							'count_num' => absint( get_option( 'max_room' ) ),
							'data'      => array(
								'class' => 'apb-select total-room-js',
								'data-max-adult'	=> absint( get_option( 'max_adult' ) ),
								'data-max-child'	=> absint( get_option( 'max_child' ) ),
								'data-check-page'	=> $is_check ? true : false,
							),
							'select'    => isset( $_GET['room_num'] ) ? absint( $_GET['room_num'] ) : 1,
						) );
						?>
					</div>
				</div>
			<?php endif; ?>

			<?php
			/**
			 * General selected number of people.
			 *
			 * @hooked general_field_check_people() - 10
			 */
			do_action( 'apb_general_field_check_people', ( 1 == $apb_setting['field_status']['status_multi_room_page'] ) ? 'multi' : 'only' );

			do_action( 'apb_get_day_advance' ); ?>

			<?php
			/**
			 * Hook apb_after_check_available_guest_field
			 */
			do_action( 'apb_after_check_available_guest_field', $apb_setting['field_status'] );
			?>

			<?php if ( ! $is_check ) : ?>
				<input type="hidden" name="check_from" value="other">
			<?php endif; ?>

			<?php
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				echo '<input type="hidden" name="lang" value="' . ICL_LANGUAGE_CODE . '">';
			}
			?>

			<input type="hidden" name="page_id" value="<?php echo absint( AWE_function::get_check_available_page_id() ); ?>">

			<?php $button_class = $is_check ? 'check-avb-js' : ''; ?>
			<button type="submit" class="apb-btn <?php echo esc_attr( $button_class ); ?>" data-num-args="0"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
		</div>
	</div>
</form>

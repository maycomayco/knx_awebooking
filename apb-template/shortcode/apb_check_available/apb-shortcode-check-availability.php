<?php
/**
 * The template for displaying form shortcode check available
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

do_action( 'apb_get_day_advance' );
?>
<div class="awebooking">
	<div class="awebooking-wrapper <?php echo ( 2 == $apb_setting['style'] ) ? 'inline' : ''; ?>">

		<!-- HEADING -->
		<h2 class="apb-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ); ?></h2>
		<!-- END / HEADING -->

		<!-- SIDEBAR CONTENT -->
		<div class="apb-content">
			<form method="get" action="<?php echo esc_url( AWE_function::get_check_available_page() ); ?>" class="apb-single-check-avb-form">
				<div class="apb-field">
					<label><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-calendar"></i>
						<?php
						/**
						 * General input from date
						 */
						AWE_function::apb_gen_input( array(
							'type'          => 'text',
							'name'          => 'from',
							'placeholder'   => esc_attr__( 'Arrival Date', 'awebooking' ),
							'class'         => 'date-start-js apb-calendar apb-input',
						) );
						?>
					</div>
				</div>

				<?php if ( 1 == $apb_setting['night'] ) { ?>
					<div class="apb-field <?php echo ( 1 == $apb_setting['style'] ) ? 'small' : ''; ?>">
						<label><?php esc_html_e( 'Nights', 'awebooking' ); ?></label>
						<div class="apb-field-group">
							<i class="apbf apbf-select"></i>

							<?php
							/**
							 * General selected total numner of night
							 */
							AWE_function::apb_get_option_to_selected( array(
								'name' => '',
								'count_num' => get_option( 'max_night' ),
								'data' => array(
									'class' => 'apb-select night-select-js',
								),
							) );
							?>
						</div>
					</div>
				<?php } ?>
				<input type="hidden" value="<?php echo absint( get_option( 'max_night' ) ); ?>" class="max-night-js">

				<?php
				if ( 1 == $apb_setting['departure'] ) {
					?>
					<div class="apb-field">
						<label><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>

						<div class="apb-field-group">
							<i class="apbf apbf-calendar"></i>
							<?php
							/**
							 * General input to date
							 */
							AWE_function::apb_gen_input( array(
								'type'          => 'text',
								'name'          => 'to',
								'placeholder'   => esc_attr__( 'Departure Date', 'awebooking' ),
								'class'         => 'date-end-js apb-calendar apb-input',
							) );
							?>
						</div>
					</div>
					<?php
				} else {
					echo '<input type="hidden" name="to" class="date-end-js">';
				}
				?>

				<?php
				/**
				 * Hook apb_after_check_available_date_field
				 */
				do_action( 'apb_after_check_available_date_field', $apb_setting );
				?>

				<?php
				if ( 1 == $apb_setting['room_type'] ) :
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
				<?php endif; ?>

				<?php
				/**
				 * Hook apb_after_check_available_room_type_field
				 */
				do_action( 'apb_after_check_available_room_type_field', $apb_setting );
				?>

				<?php if ( 1 == $apb_setting['mullti_room'] ) : ?>
					<div class="apb-field">
						<label><?php esc_html_e( 'ROOMS', 'awebooking' ); ?></label>

						<div class="apb-field-group">
							<i class="apbf apbf-select"></i>

							<?php
							/**
							 * General selected number of room
							 */
							AWE_function::apb_get_option_to_selected( array(
								'name' => 'room_num',
								'count_num' => get_option( 'max_room' ),
								'data' => array(
									'class' => 'apb-select total-room-js',
									'data-max-adult'	=> absint( get_option( 'max_adult' ) ),
									'data-max-child'	=> absint( get_option( 'max_child' ) ),
								),
							) );
							?>
						</div>
					</div>
				<?php
				endif;

				/*----------  Disnable/Enable div html by style.  ----------*/
				ob_start();
				?>
				<div class="apb-field <?php echo ( 1 == $apb_setting['style'] ) ? 'small' : ''; ?>">
					<label><?php esc_html_e( 'Adult', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>

						<?php
						/**
						 * General selected number of maximum adult
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name' => 'room_adult[]',
							'count_num' => get_option( 'max_adult' ),
							'data' => array(
								'class' => 'apb-select apb-adult-select',
							),
						) );
						?>
					</div>
				</div>

				<div class="apb-field <?php echo ( 1 == $apb_setting['style'] ) ? 'small' : ''; ?>">
					<label><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>
						<?php
						/**
						 * General selected number of maximum child
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name' => 'room_child[]',
							'start_num' => 0,
							'count_num' => get_option( 'max_child' ),
							'data' => array(
								'class' => 'apb-select apb-child-select',
							),
						) );
						?>
					</div>
				</div>
				<?php
				$select_num_people = ob_get_clean();
				/*----------  Style default  ----------*/
				//if ( 1 == $apb_setting['style'] ) :
				?>
					<div class="list-room <?php echo  ( 1 == $apb_setting['style'] ) ? 'apb-list-room-default' : 'apb-list-room-horizontal'  ?>">
						<div class="apb-sidebar_group">
							<span class="label-group"><?php esc_html_e( 'Room', 'awebooking' ); ?> 1</span>
							<div class="apb-field_group">
								<?php echo $select_num_people; ?>
							</div>
						</div>
					</div>
				<?php //endif; ?>

				<?php
				/*----------  Style horizontal  ----------*/
				// if ( 2 == $apb_setting['style'] ) {
				// 	echo $select_num_people;
				// }
				?>

				<?php
				/**
				 * Hook apb_after_check_available_guest_field
				 */
				do_action( 'apb_after_check_available_guest_field', $apb_setting );
				?>

				<input type="hidden" name="check_from" value="shortcode">
				<input type="hidden" name="page_id" value="<?php echo absint( AWE_function::get_check_available_page_id() ); ?>">

				<?php
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					echo '<input type="hidden" name="lang" value="' . ICL_LANGUAGE_CODE . '">';
				}
				?>

				<button type="submit" class="apb-btn"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
			</form>
		</div>
		<!-- END / SIDEBAR CONTENT -->

	</div>
</div>

<?php
/**
 * The template for displaying form check available for widget.
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
?>
<div class="awebooking-wrapper <?php echo ( 2 == $instance['form_style'] ) ? 'inline' : ''; ?>">
	<!-- HEADING -->
	<h2 class="apb-heading"><?php echo ! empty( $instance['title'] ) ? esc_html( $instance['title'] ) : esc_html__( 'Your Reservation', 'awebooking' ); ?></h2>
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
					 * General input from date.
					 */
					AWE_function::apb_gen_input(array(
						'type'          => 'text',
						'name'          => 'from',
						'placeholder'   => __( 'Arrival Date', 'awebooking' ),
						'class'         => 'date-start-js apb-calendar apb-input',
					) );
					?>
				</div>
			</div>

			<?php if ( 1 == $instance['status_night_number'] ) : ?>
				<div class="apb-field <?php echo ( 2 == $instance['form_style'] ) ? 'small' : ''; ?>">
					<label><?php esc_html_e( 'Nights', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>

						<?php
						/**
						 * General selected total numner of night.
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name'		=> '',
							'count_num' => get_option( 'max_night' ),
							'data' 		=> array(
								'class' => 'apb-select night-select-js',
							),
						) );
						?>
					</div>
				</div>
			<?php endif; ?>
			<input type="hidden" value="<?php echo absint( get_option( 'max_night' ) ); ?>" class="max-night-js">

			<?php if ( 1 == $instance['status_departure'] ) : ?>
				<div class="apb-field">
					<label><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-calendar"></i>

						<?php
						/**
						 * General input to date.
						 */
						AWE_function::apb_gen_input(array(
							'type'          => 'text',
							'name'          => 'to',
							'placeholder'   => __( 'Departure Date', 'awebooking' ),
							'class'         => 'date-end-js apb-calendar apb-input',
						) );
						?>
					</div>
				</div>
			<?php
			else :
				echo '<input type="hidden" name="to" class="date-end-js">';
			endif;
			?>

			<?php
			/**
			 * Hook apb_after_check_available_date_field
			 */
			do_action( 'apb_after_check_available_date_field', $instance );
			?>

			<?php if ( 1 == $instance['status_multi_room'] ) : ?>
				<div class="apb-field">
					<label><?php esc_html_e( 'Rooms', 'awebooking' ); ?></label>

					<div class="apb-field-group">
						<i class="apbf apbf-select"></i>

						<?php
						/**
						 * General selected number of room.
						 */
						AWE_function::apb_get_option_to_selected( array(
							'name'		=> 'room_num',
							'count_num' => get_option( 'max_room' ),
							'data' 		=> array(
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
			<div class="apb-field <?php echo ( 2 == $instance['form_style'] ) ? 'small' : ''; ?>">
				<label><?php esc_html_e( 'Adult', 'awebooking' ); ?></label>

				<div class="apb-field-group">
					<i class="apbf apbf-select"></i>

					<?php
					/**
					 * General selected number of maximum adult.
					 */
					AWE_function::apb_get_option_to_selected( array(
						'name'		=> 'room_adult[]',
						'count_num' => get_option( 'max_adult' ),
						'data' 		=> array(
							'class' => 'apb-select apb-adult-select',
						),
					) );
					?>
				</div>
			</div>

			<div class="apb-field <?php echo ( 2 == $instance['form_style'] ) ? 'small' : ''; ?>">
				<label><?php esc_html_e( 'Children', 'awebooking' ); ?></label>

				<div class="apb-field-group">
					<i class="apbf apbf-select"></i>

					<?php
					/**
					 * General selected number of maximum child.
					 */
					AWE_function::apb_get_option_to_selected( array(
						'name'		=> 'room_child[]',
						'start_num' => 0,
						'count_num' => get_option( 'max_child' ),
						'data' 		=> array(
							'class' => 'apb-select apb-child-select',
						),
					) );
					?>
				</div>
			</div>
			<?php
			$select_num_people = ob_get_clean();

			/*----------  Style default  ----------*/
			if ( 1 == $instance['form_style'] ) {
				?>
				<div class="list-room">
					<div class="apb-sidebar_group">
						<span class="label-group"><?php printf( esc_html__( 'Room %s', 'awebooking' ), 1 ); ?></span>
						<div class="apb-field_group">
							<?php echo $select_num_people; ?>
						</div>
				  </div>
				</div>
				<?php
			} elseif ( 2 == $instance['form_style'] ) {
				echo $select_num_people;
			}
			?>

			<?php
			/**
			 * Hook apb_after_check_available_guest_field
			 */
			do_action( 'apb_after_check_available_guest_field', $instance );
			?>

			<input type="hidden" name="check_from" value="widget">
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

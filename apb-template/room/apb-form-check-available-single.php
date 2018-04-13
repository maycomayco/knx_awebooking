<?php
/**
 * The template for displaying form check available of room single.
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

<div class="room-detail_form">
	<form method="get" action="<?php echo esc_url( AWE_function::get_check_available_page() ); ?>" class="apb-single-check-avb-form">
		<?php
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			echo '<input type="hidden" name="lang" value="' . ICL_LANGUAGE_CODE . '">';
		}
		?>

		<input type="hidden" name="page_id" value="<?php echo absint( AWE_function::get_check_available_page_id() ); ?>">

		<div class="apb-field">
			<label><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>

			<div class="apb-field-group">
				<i class="apbf apbf-calendar"></i>
				<?php
				/**
				 * General input start date form check availbale.
				 */
				AWE_function::apb_gen_input( array(
					'type'          => 'text',
					'placeholder'   => __( 'Arrival Date ', 'awebooking' ),
					'class'         => 'apb-input apb-calendar date-start-js',
					'name'			=> 'from',
				) ); ?>
			</div>
		</div>

		<div class="apb-field">
			<label><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>

			<div class="apb-field-group">
				<i class="apbf apbf-calendar"></i>
				
				<?php
				/**
				 * General input end date form check availbale.
				 */
				AWE_function::apb_gen_input( array(
					'type'          => 'text',
					'placeholder'   => __( 'Departure Date', 'awebooking' ),
					'class'         => 'apb-input apb-calendar date-end-js',
					'name'			=> 'to',
				) ); ?>
			</div>
		</div>

		<!-- <input type="hidden" value="<?php echo absint( get_option( 'max_night' ) ); ?>" class="max-night-js"> -->

		<div class="apb-field">
			<label><?php esc_html_e( 'Adult ', 'awebooking' ); ?></label>

			<div class="apb-field-group">
				<i class="apbf apbf-select"></i>
				<?php
				/**
				 * General selected number of maximum adult.
				 */
				AWE_function::apb_get_option_to_selected( array(
					'name'		=> 'room_adult',
					'count_num' => get_option( 'max_adult' ),
					'data'      => array( 'class' => 'apb-select apb-adult-select' ),
				) ); ?>
			</div>
		</div>

		<div class="apb-field">
			<label><?php esc_html_e( 'Children', 'awebooking' ); ?></label>

			<div class="apb-field-group">
				<i class="apbf apbf-select"></i>
				<?php
				/**
				 * General selected number of maximum child.
				 */
				AWE_function::apb_get_option_to_selected( array(
					'name'		=> 'room_child',
					'start_num' => 0,
					'count_num' => get_option( 'max_child' ),
					'data'      => array( 'class' => 'apb-select apb-child-select' ),
				) ); ?>
			</div>
		</div>

		<input type="hidden" name="room_type_id" value="<?php the_ID(); ?>">
		<input type="hidden" name="check_from" value="single">

		<button class="apb-btn apb-single-checkavb-js"><?php esc_html_e( 'Book', 'awebooking' ); ?></button>
	</form>
</div>

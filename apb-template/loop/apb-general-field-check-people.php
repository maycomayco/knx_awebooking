<?php
/**
 * The template for displaying loop general list room.
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

<div class="<?php echo 'multi' == $type ? 'apb-sidebar_group apb-people-box-'.$i : '' ?>">
	<?php
	if ( 'multi' == $type ) {
		echo '<span class="label-group">' . sprintf( esc_html__( 'Room %s', 'awebooking' ), absint( $i ) ) . '</span>';
	}
	?>
	<div class="apb-field_group">
		<div class="apb-field">
			<label><?php echo AWE_function::get_option( 'label_adult' ) ? esc_html( AWE_function::get_option( 'label_adult' ) ) : esc_html__( 'Adult', 'awebooking' ); ?></label>
			<div class="apb-field-group">
				<i class="apbf apbf-select"></i>

				<?php
				/**
				 * General selected number of maximum adult.
				 */
				AWE_function::apb_get_option_to_selected( array(
					'name'      => 'room_adult[]',
					'count_num' => AWE_function::get_option( 'max_adult' ),
					'data'      => array( 'class' => 'apb-select apb-adult-select' ),
					'select'    => isset( $room_adult[ $i - 1 ] ) ? $room_adult[ $i - 1 ] : 1,
				) );
				?>
			</div>
		</div>
		<div class="apb-field">
			<label><?php echo AWE_function::get_option( 'label_child' ) ? esc_html( AWE_function::get_option( 'label_child' ) ) : esc_html__( 'Children', 'awebooking' ); ?></label>
			<div class="apb-field-group">
				<i class="apbf apbf-select"></i>

				<?php
				/**
				 * General selected number of maximum child.
				 */
				AWE_function::apb_get_option_to_selected( array(
					'name'      => 'room_child[]',
					'start_num' => 0,
					'count_num' => AWE_function::get_option( 'max_child' ),
					'data'      => array( 'class' => 'apb-select apb-child-select' ),
					'select'    => isset( $room_child[ $i - 1 ] ) ? $room_child[ $i - 1 ] : 0,
				) );
				?>
			</div>
		</div>
	</div>
</div>

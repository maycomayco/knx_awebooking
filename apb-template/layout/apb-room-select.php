<?php
/**
 * The template for displaying all room user select.
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
<div class="apb-room-selected">
	<!-- HEADING -->
	<h2 class="apb-heading"><?php esc_html_e( 'Select Rooms', 'awebooking' ) ?></h2>
	<!-- END / HEADING -->

	<div class="apb-room-selected_content">
		<?php
		/**
		 * Hook: apb_loop_content_room_select.
		 *
		 * @hooked loop_content_room_select
		 */
		do_action( 'apb_loop_content_room_select' );
		?>

		<?php
		if ( isset( $total_cart ) && $total_cart < count( $room_adult ) ) {
			?>

			<div class="apb-room-seleted_current apb-bg_blue" data-type="1" data-adult="<?php echo absint( $room_adult[ $total_cart ]['adult'] ) ?>" data-child="<?php echo absint( $room_child[ $total_cart ]['child'] ) ?>">
				<h6><?php printf( esc_html__( 'You are booking room %s', 'awebooking' ), absint( $total_cart + 1 ) ); ?></h6>
				<span>
					<?php
					printf( esc_html__( '%s Adult', 'awebooking' ), absint( $room_adult[ $total_cart ]['adult'] ) );
					echo ', ';
					printf( esc_html__( '%s Child', 'awebooking' ), absint( $room_child[ $total_cart ]['child'] ) );
					?>
				</span>
			</div>

			<?php
			if ( $total_cart < count( $room_adult ) - 1 ) {
				for ( $i = $total_cart + 1; $i <= count( $room_adult ) - 1; $i++ ) {
					?>
					<div class="apb-room-selected_item apb_disable" data-adult="<?php echo absint( $room_adult[ $i ]['adult'] ) ?>" data-child="<?php echo absint( $room_child[ $i ]['child'] ) ?>">
						<h6><?php printf( esc_html__( 'Room %s', 'awebooking' ), absint( $i + 1 ) ); ?></h6>
						<span class="apb-option">
							<?php
							printf( esc_html__( '%s Adult', 'awebooking' ), absint( $room_adult[ $i ]['adult'] ) );
							echo ', ';
							printf( esc_html__( '%s Child', 'awebooking' ), absint( $room_child[ $i ]['child'] ) );
							?>
						</span>
					</div>
					<?php
				}
			}
		}
		?>
	</div>
</div>

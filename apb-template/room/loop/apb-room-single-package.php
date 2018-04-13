<?php
/**
 * The template for displaying loop package of room.
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

$get_option = AWE_function::get_room_option( get_the_ID(), 'apb_room_type' );
foreach ( $get_option as $item_option ) :
	?>
	<!-- ITEM package -->
	<div class="apb-package_item">
		<div class="apb-package_text">
			<h4>
				<i class="<?php echo isset( $item_option->option_icon ) ? $item_option->option_icon : '' ?>"></i> 
				<label for="package-<?php echo absint( $item_option->id ); ?>"><?php echo esc_html( $item_option->option_name ); ?></a>
			</h4>

			<p><?php echo wp_kses_post( $item_option->option_desc ); ?></p>

			<div class="apb-package_book-price">
				<p class="apb-package_price">
					<span class="amount"><?php echo AWE_function::apb_price( $item_option->option_value ) ?></span> / <?php ( 1 == $item_option->revision_id ) ? esc_html_e( 'day', 'awebooking' ) : esc_html_e( 'package', 'awebooking' ); ?>
				</p>
			</div>
		</div>
	</div>
	<!-- END / ITEM package -->
<?php endforeach; ?>

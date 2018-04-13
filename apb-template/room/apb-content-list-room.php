<?php
/**
 * The template for displaying list room.
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
<div class="apb_content_room_js apb-room_item apb-room_item-<?php the_ID(); ?>">
	<!-- ITEM -->
	<div class="apb-room_heading">
		<h2 class="apb-room_name">
			<a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		</h2>
	</div>

	<div class="apb-room_img">
		<a href="<?php the_permalink() ?>"><?php the_post_thumbnail( 'post-thumbnail' ); ?></a>
	</div>

	<div class="apb-room_text">
		<div class="apb-room_desc">
			<?php echo wp_kses_post( $room_desc ); ?>
		</div>
		<a href="<?php the_permalink() ?>" class="apb-room_view-more">
			<?php esc_html_e( 'View More Information', 'awebooking' ); ?>
		</a>
		<div class="clear"></div>
		<?php loop_price( $room_price, get_the_ID() ); ?>
	</div>
	<?php do_action( 'apb_layout_list_package', array( 'count_day' => 1, 'check' => false ) ); ?>
	<!-- END / ITEM -->
</div>

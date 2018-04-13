<?php
/**
 * The template for displaying loop gallery of room.
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

<?php if ( is_array( $room_gallery ) ) : ?>

	<div class="apb-product_image">
		<?php foreach ( $room_gallery as $item_gallery ) : ?>
			<div class="apb-product_image_item">
				<?php echo wp_get_attachment_image( $item_gallery, 'full' ); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="apb-product_thumb">
		<?php foreach ( $room_gallery as $item_gallery ) : ?>
			<div class="apb-product_thumb_item">
				<a href="#"><?php echo wp_get_attachment_image( $item_gallery, 'thumbnail' ); ?></a>
			</div>
		<?php endforeach; ?>
	</div>

<?php endif; ?>

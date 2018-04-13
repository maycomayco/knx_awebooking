<?php

/**
 * Description of create_gallery
 *
 * @author Letrungha
 */
class create_gallery {

	public static function output( $post ) {
		$room_gallery = get_post_meta( $post->ID, 'apb_gallery', true );
		?>
		<div id="room_images_container">
			<ul class="room_images">
				<?php
				if ( isset( $room_gallery ) && is_array( $room_gallery ) ) {
					foreach ( $room_gallery as $item_gallery ) {
						$image = wp_get_attachment_image_src( $item_gallery, 'thumbnail', false );
						?>
						<li data-attachment_id="<?php echo absint( $item_gallery ) ?>" class="image image-<?php echo absint( $item_gallery ) ?>">
							<img src="<?php echo esc_url( $image[0] ); ?>">
							<input type="hidden" value="<?php echo absint( $item_gallery ) ?>" name="apb_gallery[]">
							<ul class="actions">
								<li><a data-id="<?php echo absint( $item_gallery ) ?>" class="delete"><?php esc_html_e( 'delete', 'awebooking' ); ?></a></li>
							</ul>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
		<p class="add_product_images hide-if-no-js">
			<a data-choose="<?php esc_attr_e( 'Add Images to Product Gallery', 'awebooking' ); ?>" class="apb_add_gallery" href="#"><?php esc_html_e( 'Add gallery images', 'awebooking' ); ?></a>
		</p>

		<?php wp_nonce_field( 'awe_gallery_meta', 'awe_gallery_nonce_field' );
	}

	public static function save( $post_id ) {
		if ( isset( $_POST['apb_gallery'] ) && ! empty( $_POST['apb_gallery'] ) ) {
			update_post_meta( $post_id, 'apb_gallery', wp_kses( wp_unslash( $_POST['apb_gallery'] ), array() ) );
		} else {
			update_post_meta( $post_id, 'apb_gallery', array() );
		}
	}
}

<?php
/**
 * Description of boxes_apb_order_data
 *
 * @package Awebooking
 * @author Letrungha
 */

/**
 * Class boxes_apb_order_data
 */
class boxes_apb_order_data {

	/**
	 * Display meta box.
	 *
	 * @param  object $post Post object.
	 * @return void
	 */
	public static function output( $post ) {
		$custom_info = get_post_meta( $post->ID, 'info_custom_order', true );
		if ( ! empty( $custom_info ) ) {
			foreach ( $custom_info as $key => $val ) {
				if ( empty( $val ) ) {
					continue;
				}

				if ( 'apb-name' == strtolower( $key ) ) {
					$label = __( 'Customer name', 'awebooking' );
				} elseif ( 'apb-email' == strtolower( $key ) ) {
					$label = __( 'Customer email', 'awebooking' );
				} else {
					$label = str_replace( '-', ' ', ucwords( $key ) );
				}

				echo '<strong>' . esc_html( $label ) . '</strong>: ' . esc_html( $val ) . '<br/>';
			}
		}
	}
}

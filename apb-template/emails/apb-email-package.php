<?php
/**
 * Email package
 *
 * @package Awebooking
 */

?>

<?php if ( ! empty( $item['package_data'] ) ) : ?>
	<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;"><?php esc_html_e( 'Package', 'awebooking' ); ?></h6>
	<ul style="list-style: outside none none; margin-bottom: 0; margin-top: 5px; padding-bottom: 2px; padding-left: 0;">
		<?php
		foreach ( $item['package_data'] as $info_package ) :
			$packages = AWE_function::get_room_option( $room_type_id, 'apb_room_type' );
			foreach ( $packages as $item_package ) {
				if ( $item_package->id == $info_package['package_id'] ) {
					?>
					<li style="color: #333333; font-size: 12px; overflow: hidden;">
						<span class="apb-room-seleted_date"><?php echo esc_html( $item_package->option_name ); ?></span>
						<span class="apb-amount" style="float: right; font-weight: bold; text-transform: uppercase;">
							<?php
							if ( $item_package->revision_id ) {
								echo wp_kses_post( AWE_function::apb_price( $item_package->option_value ) . ' x ' . ( count( $range_date ) - 1 ) );
							} else {
								echo wp_kses_post( AWE_function::apb_price( $item_package->option_value ) . ' x ' . $info_package['total'] );
							}
							?>
						</span>
					</li>
					<?php
				}
			}
		endforeach;
		?>
	</ul>
<?php endif; ?>

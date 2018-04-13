<?php
/**
 * Email extra sale
 *
 * @package Awebooking
 */

?>

<?php
error_reporting(0);
$extra_sale = get_post_meta( $room_type_id, 'extra_sale', true );
if ( ! empty( $extra_sale ) ) {
	$data_extra_sale = AWE_function::apb_get_extra_sale( $extra_sale, count( $range_date ), $item['from'] );

	?>
	<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;"><?php esc_html_e( 'Sale', 'awebooking' ); ?></h6>

	<ul style="list-style: outside none none; margin-bottom: 0; margin-top: 5px; padding-bottom: 2px; padding-left: 0;">
		<li style="color: #333333; font-size: 12px; overflow: hidden;">
			<?php
			if ( ! empty( $data_extra_sale ) ) {
					$item['sale_info'] = $data_extra_sale;
				?>
				<span class="apb-room-seleted_date">
					<?php esc_html_e( 'Sale', 'awebooking' ); ?>
				</span>
				<span style="float: right; font-weight: bold; text-transform: uppercase;" class="apb-amount">
					<?php

					if( count( $item['sale_info'] ) > 2 ) {
						if ( 'sub' == $item['sale_info']['sale_type'] ) {
							?>
							(<?php echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['amount'] ) ); ?>)
							<?php
						} else {
							?>
							(-<?php echo wp_kses_post( $item['sale_info']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) ); ?>)
							<?php
						}
					} else {

						if ( 'sub' == $item['sale_info']['before']['sale_type'] && null != $item['sale_info'] ) {
							?>
							( <?php echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['before']['amount'] ) ); ?> ) + 
								
							<?php
						} else if( 'decrease' == $item['sale_info']['before']['sale_type'] && null != $item['sale_info'] ) {
							?>
							(-<?php echo wp_kses_post( $item['sale_info']['before']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) ); ?>) + 
							<?php
						}

						if ( 'sub' == $item['sale_info']['week']['sale_type'] ) {
							echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['week']['amount'] ) ); 
						} else {
							?>
							( -<?php echo wp_kses_post( $item['sale_info']['week']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) ); ?> )
							<?php
						}
					}
					?>
				</span>
				<?php
			}
			?>
		</li>
	</ul>
<?php } ?>

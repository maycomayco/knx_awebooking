<?php
/**
 * The template for displaying loop item for cart
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
<!-- ITEM -->
<div class="apb-room-selected_item">
    <h6><?php printf( esc_html__( 'Room %d', 'awebooking' ), absint( $room_num ) ); ?></h6>
	<div class="apb-room-seleted_name has-package">
		<h2><?php echo esc_html( $room_type->post_title ); ?></h2>
	</div>
	<span class="apb-option">
		<?php printf( esc_html__( '%d Adult', 'awebooking' ), absint( $item['adult'] ) ); ?>, <?php printf( esc_html__( '%d Child', 'awebooking' ), absint( $item['child'] ) ); ?>
	</span>

	<div class="apb-room-seleted_package">
		<h6><?php esc_html_e( 'Price/Night', 'awebooking' ); ?></h6>
		<ul>
			<?php
			$year_from = date( 'Y', strtotime( $item['from'] ) );
			$year_to = date( 'Y', strtotime( $item['to'] ) );
			$info_price_day = AWE_function::get_pricing_of_days( $item['from'], $item['to'], $room_type->ID );
			foreach ( $info_price_day as $month => $list_day ) {
				foreach ( $list_day as $day => $price_day ) {
					if ( $year_from < $year_to ) {
						if ( intval( $month ) > 6 ) {
							$year = $year_from;
						} else {
							$year = $year_to;
						}
					} else {
						$year = $year_from;
					}
					?>
					<li>
						<span class="apb-room-seleted_date"><?php echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( $month . '/' . $day . '/' . $year ) ) ); ?></span>
						<span class="apb-amount"><?php echo wp_kses_post( AWE_function::apb_price( $price_day ) ); ?></span>
					</li>
					<?php
				}
			}
			?>
		</ul>

		

		<?php
		$extra = '';

		if ( ! empty( $extra_price_data['adult'] ) ) {
			ob_start();
			?>
			<li>
				<span class="apb-room-seleted_date">
					<?php printf( esc_html__( '%s Adult', 'awebooking' ), absint( $extra_guess_data['adult'] ) ); ?>
					+
					<?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ) ?>
				</span>
				<span class="apb-amount"><?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ?> × <?php echo absint( count( $range_date ) - 1 ); ?></span>
			</li>
			<?php
			$extra .= ob_get_clean();
		}

		if ( ! empty( $extra_price_data['child'] ) ) {
			ob_start();
			?>
			<li>
				<span class="apb-room-seleted_date">
					<?php printf( esc_html__( '%s Child', 'awebooking' ), absint( $extra_guess_data['child'] ) ); ?>
					+
					<?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ) ?>
				</span>
				<span class="apb-amount"><?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ?> × <?php echo absint( count( $range_date ) - 1 ); ?></span>
			</li>
			<?php
			$extra .= ob_get_clean();
		}

		if ( ! empty( $extra ) ) {
			echo '<h6>' . esc_html__( 'Extra price', 'awebooking' ) . '</h6>';
			echo '<ul>';
			echo wp_kses_post( $extra );
			echo '</ul>';
		}
		?>

		<?php if ( ! empty( $item['sale_info'] ) ) { ?>
			<h6><?php esc_html_e( 'Discount price', 'awebooking' ); ?></h6>
			<ul>
				<li>
					<span class="apb-room-seleted_date"><?php esc_html_e( 'Sale', 'awebooking' ); ?></span>
					<span class="apb-amount">
					<?php
					if( count( $item['sale_info'] ) > 2 ) {
						if ( 'sub' == $item['sale_info']['sale_type'] ) {
							?>
							<?php echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['amount'] ) ); ?>
							<?php
						} else {
							?>
							<?php echo wp_kses_post( $item['sale_info']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) ); ?>
							<?php
						}
					} else {
						if ( 'sub' == $item['sale_info']['before']['sale_type'] && null != $item['sale_info'] ) {
							?>
							<?php echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['before']['amount'] ) ); ?> + 
								
							<?php
						} else if( 'decrease' == $item['sale_info']['before']['sale_type'] && null != $item['sale_info'] ) {
							?>
							<?php echo wp_kses_post( $item['sale_info']['before']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) ); ?> + 
							<?php
						}

						if ( 'sub' == $item['sale_info']['week']['sale_type'] ) {
							 echo wp_kses_post( AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) . AWE_function::apb_price( $item['sale_info']['week']['amount'] ) ); ?>
							<?php
						} else {
							?>
							<?php echo wp_kses_post( $item['sale_info']['week']['amount'] . AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) ); ?>
							<?php
						}
					}
					?>
					</span>
					</span>
				</li>
			</ul>
		<?php } ?>


		<?php if ( ! empty( $item['package_data'] ) ) : ?>
			<h6><?php esc_html_e( 'Package', 'awebooking' ) ?></h6>
			<ul>
				<?php
				foreach ( $item['package_data'] as $info_package ) :
					$getPackage = AWE_function::get_room_option( $room_type->ID, 'apb_room_type' );
					foreach ( $getPackage as $item_package ) {
						if ( $item_package->id == $info_package['package_id'] ) {

							?>
							<li>
								<span class="apb-room-seleted_date"><?php echo esc_html( $item_package->option_name ) ?></span>
								<span class="apb-amount">
									<?php
										if( 1 == $item_package->revision_id ){
											echo ( AWE_function::apb_price( $item_package->option_value * $info_package['total'] *  absint( count( $range_date ) - 1 ) ) );
										} else {
											echo ( AWE_function::apb_price( $item_package->option_value * $info_package['total'] ) );
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
	</div>

	<?php
	/**
	 * Hook apb_after_cart_info
	 */
	do_action( 'apb_after_cart_info', $item, $room_type, $room );
	?>

	<input type="hidden" name="apb-price[]" value="<?php echo ( float ) $price; ?>">
	<input type="hidden" name="apb-room_id[]" value="<?php echo absint( $item['room_id'] ) ?>">
	<input type="hidden" name="apb-from[]" value="<?php echo esc_attr( $item['from'] ) ?>">
	<input type="hidden" name="apb-to[]" value="<?php echo esc_attr( $item['to'] ) ?>">
	<input type="hidden" name="apb-adult[]" value="<?php echo absint( $item['adult'] ) ?>">
	<input type="hidden" name="apb-child[]" value="<?php echo absint( $item['child'] ) ?>">
	<input type="hidden" name="apb-package[]" value="<?php echo esc_attr( serialize( $item['package_data'] ) ) ?>">
	<div class="apb-room-seleted_total-room">
		<?php esc_html_e( 'subtotal', 'awebooking' ); ?>
		<span class="apb-amount"><?php echo wp_kses_post( AWE_function::apb_price( $price ) ); ?></span>
	</div>
	
	<?php 
		$apb_deposit_type = get_option( 'apb_deposit_type' );
		$total = $_SESSION['apb_cart']['total'];
		if ( ! empty( $apb_deposit_type ) && 'none' != get_option( 'apb_deposit_type' ) ) {
			$a = AWE_function::calculate_pre_payment( $total );

	?>
	

	<div class="apb-room-seleted_total-room">
		<?php esc_html_e( 'Pre-payment', 'awebooking' ); ?>
		<span class="apb-amount"><?php echo esc_html( AWE_function::apb_price( $a ) ) ?></span>
	</div>

	<div class="apb-room-seleted_total-room">
		<?php esc_html_e( 'Remaining Amount', 'awebooking' ) ?>
		<span class="apb-amount"><?php echo esc_html( AWE_function::apb_price( $total - $a ) )?></span>

	</div>
	<?php
	}
	?>

	

</div>
<!-- END / ITEM -->

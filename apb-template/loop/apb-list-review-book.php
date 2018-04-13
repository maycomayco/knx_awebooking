<?php
/**
 * The template for displaying loop complated select room
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
<div class="apb-room-select-item">
	<div class="img">
		<?php echo get_the_post_thumbnail( $room_type_id, 'thumbnail', false ); ?>
	</div>
	<div class="apb-desc">
		<span class="room-select-th"><?php printf( esc_html__( 'Room %s', 'awebooking' ), absint( $room_num ) ); ?></span>

		<h4><?php echo esc_html( $room_type->post_title ); ?></h4>

		<?php
		/**
		 * Hook apb_after_review_room_name
		 */
		do_action( 'apb_after_review_room_name', $item, $room_type, $room );
		?>

		<p>
			<?php
			printf( esc_html__( '%s Adult', 'awebooking' ), absint( $item['adult'] ) );
			echo ', ';
			printf( esc_html__( '%s Child', 'awebooking' ), absint( $item['child'] ) );
			?>
			<br/>
		</p>

		<p>
			<?php echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( $item['from'] ) ) ); ?>
			<i class="fa fa-long-arrow-right">-</i>
			<?php echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( $item['to'] ) ) ); ?>
		</p>
	</div>

	<?php
	$package_price = 0;
	$info_price_day = AWE_function::get_pricing_of_days( $item['from'], $item['to'], $room_type_id, 1 );
	$total_day_price = AWE_function::get_total_day_price( $info_price_day );

	$base_price_for = get_post_meta( $room_type_id, 'base_price_for', true );
	$extra_guess_data = AWE_function::get_extra_guest_data( $base_price_for, $item['adult'], $item['child'] );
	$extra_price_data = AWE_function::get_extra_price_data( $room_type_id, $item['adult'], $item['child'] );
	?>
	<div class="apb-room-select-package">
		<span class="room-select-th"><?php esc_html_e( 'Price', 'awebooking' ); ?></span>
		<ul>
			<li>
				<?php printf( esc_html__( '%s night', 'awebooking' ), absint( $total_night ) ); ?>
				<span><?php echo wp_kses_post( AWE_function::apb_price( ( $total_day_price / $total_night ) *  absint( $total_night ) ) ); ?> ( <?php echo wp_kses_post( AWE_function::apb_price( ( $total_day_price / $total_night ) ) ) . ' × ' . absint( $total_night ) ?> )</span>
			</li>

			<?php
			if ( ! empty( $extra_price_data['adult'] ) ) {
				?>
				<li>
					<?php printf( esc_html__( '%s Adult', 'awebooking' ), absint( $extra_guess_data['adult'] ) ); ?> + <?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ); ?></span>
					<span><?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ?> × <?php echo absint( count( $range_date ) - 1 ); ?></span>
				</li>
				<?php
			}

			if ( ! empty( $extra_price_data['child'] ) ) {
				?>
				<li>
					<?php printf( esc_html__( '%s Child', 'awebooking' ), absint( $extra_guess_data['child'] ) ); ?> + <?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ); ?></span>
					<span><?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ?> × <?php echo absint( count( $range_date ) - 1 ); ?></span>
				</li>
				<?php
			}
			?>

			<?php if ( ! empty( $item['sale_info'] ) ) { ?>
				<li>
					<?php
					if( count($item['sale_info']) > 2 ) { 
						if ( 'sub' == $item['sale_info']['sale_type'] ) {
							echo esc_html__( 'Sale:', 'awebooking' ) . ' <span class="apb-amount"> ' . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) ) . wp_kses_post( AWE_function::apb_price( $item['sale_info']['amount'] ) ) . '</span>';
						} else {
							echo esc_html__( 'Sale', 'awebooking' ) . ' <span class="apb-amount">' . ( float ) $item['sale_info']['amount'] . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['sale_type'] ) ) . '</span>';
						}
					} else {

						if ( 'sub' == $item['sale_info']['before']['sale_type'] ) {
							echo esc_html__( 'Sale:', 'awebooking' );
							if( $item['sale_info']['before'] != null ) {
							echo ' <span class="apb-amount"> ' . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) ) . wp_kses_post( AWE_function::apb_price( $item['sale_info']['before']['amount'] ) ) . ' </span> + ';
							}
						} else {
							echo esc_html__( 'Sale:', 'awebooking' );
							if( $item['sale_info']['before'] != null ) {
							echo ' <span class="apb-amount">' . ( float ) $item['sale_info']['before']['amount'] . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['before']['sale_type'] ) ) . ' </span> + ';
							}
						}
						
						if ( 'sub' == $item['sale_info']['week']['sale_type'] ) {
							echo '<span class="apb-amount">' . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) ) . wp_kses_post( AWE_function::apb_price( $item['sale_info']['week']['amount'] ) ) . '</span>';
						} else {
							echo ' <span class="apb-amount">' . ( float ) $item['sale_info']['week']['amount'] . esc_html( AWE_function::get_symbol_of_sale( $item['sale_info']['week']['sale_type'] ) ) . ' </span>';
						}
						echo '</span>';
					}
					?>
				</li>
			<?php } ?>

			<?php
			if ( ! empty( $item['package_data'] ) ) {
				foreach ( $item['package_data'] as $info_package ) {
					$get_package = AWE_function::get_room_option( $room_type_id, 'apb_room_type' );
					foreach ( $get_package as $item_package ) {
						if ( $item_package->id == $info_package['package_id'] ) {
							$package_price += $info_package['total'] * $item_package->option_value;
							if( '1' == $item_package->revision_id ) {
										$day_text = ' / ' . __( 'day','awebooking' );
										$count = $info_package['total'];
										$total = wp_kses_post( AWE_function::apb_price( $item_package->option_value * $count ) );
										$desc_text =  '';
									} else {
										$day_text = ' / ' . __( 'package','awebooking' );
										$count = $info_package['total'];
										$total = wp_kses_post( AWE_function::apb_price( $item_package->option_value * $count ) );
										$desc_text = '';
							}
							?>
							<li>
								<?php echo esc_html( $item_package->option_name ) . $day_text . ': '; ?>
								<span><?php echo $total; ?> (<?php echo wp_kses_post( AWE_function::apb_price( $item_package->option_value ) ); ?> × <?php echo $count ?>) </span>
							</li>
							<?php
						}
					}
				}
			}
			?>
		</ul>
	</div>

	<div class="apb-room-select-price">
		<span class="room-select-th"><?php esc_html_e( 'Total', 'awebooking' ); ?></span>
		<span class="price"><?php echo wp_kses_post( AWE_function::apb_price( $item['price'] ) ); ?></span>
	</div>

</div>
<!-- ITEM -->

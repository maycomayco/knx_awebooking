<?php
/**
 * Email extra price
 *
 * @package Awebooking
 */

?>

<?php if ( ! empty( $extra_price_data['adult'] ) || ! empty( $extra_price_data['child'] ) ) : ?>
	<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;"><?php esc_html_e( 'Extra price', 'awebooking' ); ?></h6>

	<ul style="list-style: outside none none; margin-bottom: 0; margin-top: 5px; padding-bottom: 2px; padding-left: 0;">
		<?php if ( ! empty( $extra_price_data['adult'] ) ) : ?>
			<li style="color: #333333; font-size: 12px; overflow: hidden;">
				<span class="apb-room-seleted_date">
					<?php printf( esc_html__( '%s Adult', 'awebooking' ), absint( $extra_guess_data['adult'] ) ); ?>
					+
					<?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ); ?>
				</span>

				<span style="float: right; font-weight: bold; text-transform: uppercase;" class="apb-amount" class="apb-amount">
					<?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ?>
					x
					<?php echo absint( count( $range_date ) - 1 ); ?>
				</span>
			</li>
		<?php endif; ?>

		<?php if ( ! empty( $extra_price_data['child'] ) ) : ?>
			<li style="color: #333333; font-size: 12px; overflow: hidden;">
				<span class="apb-room-seleted_date">
					<?php printf( esc_html__( '%s Child', 'awebooking' ), absint( $extra_guess_data['child'] ) ); ?>
					+
					<?php printf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ); ?>
				</span>

				<span style="float: right; font-weight: bold; text-transform: uppercase;" class="apb-amount" class="apb-amount">
					<?php echo wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ?>
					x
					<?php echo absint( count( $range_date ) - 1 ); ?>
				</span>
			</li>
		<?php endif; ?>
	</ul>
<?php endif ?>

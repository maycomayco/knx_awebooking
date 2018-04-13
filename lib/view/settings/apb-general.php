<?php
/**
 *  Tempalte setting default room setting
 *
 * @version     1.0
 * @package     AweBooking/admin/
 * @author      AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="form-elements">
	<label for="rooms_booking_start_date"><?php esc_html_e( 'How soon a booking can start', 'awebooking' ); ?></label><br>
	<select name="rooms_booking_start_date" id="rooms_booking_start_date">
		<option value="0"><?php esc_html_e( 'Same day bookings', 'awebooking' ); ?></option>
		<?php for ( $i = 1; $i <= 10; $i++ ) : ?>
			<option <?php selected( $i, get_option( 'rooms_booking_start_date' ) ); ?> value="<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Day in advance', 'awebooking' ); ?> <?php echo absint( $i ); ?></option>
		<?php endfor; ?>
	</select>
	<span class="description">
		<?php esc_html_e( 'Current date + some date.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="max_room"><?php esc_html_e( 'Max room can choose', 'awebooking' ); ?></label><br>
	<input type="number" value="<?php echo absint( get_option( 'max_room' ) ) ?>" name="max_room" id="max_room">
	<span class="description">
		<?php esc_html_e( 'Maximum number of rooms which user can choose when check availability.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="max_adult"><?php esc_html_e( 'Max adult', 'awebooking' ); ?></label><br>
	<input type="number" value="<?php echo absint( get_option( 'max_adult' ) ) ?>" name="max_adult" id="max_adult">
	<span class="description">
		<?php esc_html_e( 'Number of adults.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="max_child"><?php esc_html_e( 'Max Child', 'awebooking' ); ?></label><br>
	<input type="number" value="<?php echo absint( get_option( 'max_child' ) ) ?>" name="max_child" id="max_child">
	<span class="description">
		<?php esc_html_e( 'Number of children.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="max_night"><?php esc_html_e( 'Max Night', 'awebooking' ); ?></label><br>
	<input type="number" value="<?php echo absint( get_option( 'max_night' ) ) ?>" name="max_night" id="max_night">
	<span class="description">
		<?php esc_html_e( 'Number of nights.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="apb_tax_amount"><?php esc_html_e( 'Tax amount', 'awebooking' ); ?></label><br>
	<input name="apb_tax_amount" id="apb_tax_amount" type="text" value="<?php echo floatval( get_option( 'apb_tax_amount' ) ); ?>">
</div>

<div class="form-elements">
	<?php
	$tax_type = get_option( 'apb_tax_type' ) ? get_option( 'apb_tax_type' ) : 'percentage';
	?>
	<label for="apb_tax_type"><?php esc_html_e( 'Tax type', 'awebooking' ); ?></label><br>
	<select name="apb_tax_type" id="apb_tax_type">
		<option value="percentage" <?php selected( $tax_type, 'percentage' ); ?>><?php esc_html_e( 'Percentage', 'awebooking' ); ?></option>
		<option value="fixed" <?php selected( $tax_type, 'fixed' ); ?>><?php esc_html_e( 'Fixed', 'awebooking' ); ?></option>
	</select>
</div>

<div class="form-elements">
	<label for="woocommerce_currency"><?php esc_html_e( 'Currency', 'awebooking' ); ?></label><br>
	<select name="woocommerce_currency" id="woocommerce_currency">
		<?php foreach ( AWE_function::list_currencies() as $key => $val ) { ?>
			<option <?php selected( AWE_function::get_option( 'woocommerce_currency' ), $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val . ' (' . AWE_function::get_currency( $key ) . ')' ); ?></option>
		<?php } ?>
	</select>
</div>

<div class="form-elements">
	<?php
	$currency_pos = AWE_function::get_option( 'woocommerce_currency_pos' ) ? esc_attr( AWE_function::get_option( 'woocommerce_currency_pos' ) ) : 'left';
	$currency = AWE_function::get_currency( AWE_function::get_option( 'woocommerce_currency' ) );
	?>
	<label for="woocommerce_currency_pos"><?php esc_html_e( 'Currency Position', 'awebooking' ); ?></label><br>
	<select name="woocommerce_currency_pos" id="woocommerce_currency_pos">
		<option value="left" <?php selected( $currency_pos, 'left' ); ?>><?php printf( esc_html__( 'Left (%s)', 'awebooking' ), esc_attr( $currency ) . '99.99' ); ?></option>
		<option value="right" <?php selected( $currency_pos, 'right' ); ?>><?php printf( esc_html__( 'Right (%s)', 'awebooking' ), '99.99' . esc_attr( $currency ) ); ?></option>
		<option value="left_space" <?php selected( $currency_pos, 'left_space' ); ?>><?php printf( esc_html__( 'Left with space (%s)', 'awebooking' ), esc_attr( $currency ) . ' 99.99' ); ?></option>
		<option value="right_space" <?php selected( $currency_pos, 'right_space' ); ?>><?php printf( esc_html__( 'Right with space (%s)', 'awebooking' ), '99.99 ' . esc_attr( $currency ) ); ?></option>
	</select>
</div>

<div class="form-elements">
	<label for="woocommerce_price_thousand_sep"><?php esc_html_e( 'Thousand Separator', 'awebooking' ); ?></label><br>
	<input name="woocommerce_price_thousand_sep" type="text" value="<?php echo esc_attr( AWE_function::get_option( 'woocommerce_price_thousand_sep' ) ); ?>" style="width:50px;min-width:auto" id="woocommerce_price_thousand_sep">
</div>

<div class="form-elements">
	<label for="woocommerce_price_decimal_sep"><?php esc_html_e( 'Decimal Separator', 'awebooking' ); ?></label><br>
	<input name="woocommerce_price_decimal_sep" type="text" value="<?php echo esc_attr( AWE_function::get_option( 'woocommerce_price_decimal_sep' ) ); ?>" style="width:50px;min-width:auto" id="woocommerce_price_decimal_sep">
</div>

<div class="form-elements">
	<label for="woocommerce_price_num_decimals"><?php esc_html_e( 'Number of Decimals', 'awebooking' ); ?></label><br>
	<input name="woocommerce_price_num_decimals" type="number" value="<?php echo esc_attr( AWE_function::get_option( 'woocommerce_price_num_decimals' ) ); ?>" id="woocommerce_price_num_decimals">
</div>

<div class="form-elements">
	<div class="form-radios">
		<input id="_booking_pending" class="apb_departure" name="_booking_pending" <?php checked( get_option( '_booking_pending' ), 1, true ); ?> type="checkbox" value="1">
		<label for="_booking_pending"><?php esc_html_e( 'Prevent book on pending date', 'awebooking' ); ?></label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<input id="apb_show_single_calendar" class="apb_departure" name="apb_show_single_calendar" <?php checked( get_option( 'apb_show_single_calendar' ), 1, true ); ?> type="checkbox" value="1">
		<label for="apb_show_single_calendar"><?php esc_html_e( 'Show calendar in single room type', 'awebooking' ); ?></label>
	</div>
</div>
<div class="form-elements">
	<div class="form-radios">
		<input id="apb_ajax_single_calendar" class="apb_departure" name="apb_ajax_single_calendar" <?php checked( get_option( 'apb_ajax_single_calendar' ), 1, true ); ?> type="checkbox" value="1">
		<label for="apb_ajax_single_calendar"><?php esc_html_e( 'Prev/Next calendar in single by ajax', 'awebooking' ); ?></label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<input id="apb_show_remain_room" class="apb_departure" name="apb_show_remain_room" <?php checked( get_option( 'apb_show_remain_room' ), 1, true ); ?> type="checkbox" value="1">
		<label for="apb_show_remain_room"><?php esc_html_e( 'Show number of remain rooms on check availability results', 'awebooking' ); ?></label>
	</div>
</div>

<?php
do_action( 'apb_after_general_setting' );
?>

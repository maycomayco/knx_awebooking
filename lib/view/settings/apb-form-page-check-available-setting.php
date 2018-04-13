<?php
/**
 *  Tempalte setting form check available.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_status = get_option( 'apb_setting' );
?>

<p><?php esc_html_e( 'Enable or disable field of form check available.', 'awebooking' ); ?></p>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="apb_departure" name="apb_setting[field_status][status_departure_page]" <?php checked( $field_status['field_status']['status_departure_page'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Departure Date', 'awebooking' ); ?>
		</label>
	</div>
</div>

<?php
$night_number = isset( $field_status['field_status']['status_night_number_page'] ) ? absint( $field_status['field_status']['status_night_number_page'] ) : 1;
?>
<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="apb_nightnum" name="apb_setting[field_status][status_night_number_page]" <?php checked( $night_number, 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Night Number', 'awebooking' ); ?>
		</label>
	</div>
</div>

<?php
$room_type = isset( $field_status['field_status']['status_room_type_page'] ) ? absint( $field_status['field_status']['status_room_type_page'] ) : 0;
?>
<div class="form-elements">
	<div class="form-radios">
		<label>
			<input name="apb_setting[field_status][status_room_type_page]" <?php checked( $room_type, 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Room Type', 'awebooking' ); ?>
		</label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input name="apb_setting[field_status][status_multi_room_page]" <?php checked( $field_status['field_status']['status_multi_room_page'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Select Multiple Room', 'awebooking' ); ?>
		</label>
	</div>
</div>

<?php
do_action( 'apb_after_form_check_available_setting', $field_status );
?>

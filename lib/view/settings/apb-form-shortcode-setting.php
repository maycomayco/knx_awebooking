<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Tempalte setting shortcode check available
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

$field_status = get_option( 'apb_setting' );

?>
<p><span class="description"><?php esc_html_e( 'Enable/disable field in check available form shortcode.', 'awebooking' ) ?></span></p>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="apb_departure get_attr_shortcode_js" data-attr="departure" name="apb_setting[field_status][status_departure_sc]" <?php checked( $field_status['field_status']['status_departure_sc'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Departure Date', 'awebooking' ); ?>
		</label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="apb_nightnum get_attr_shortcode_js" data-attr="night" name="apb_setting[field_status][status_night_number_sc]" <?php checked( $field_status['field_status']['status_night_number_sc'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Night Number', 'awebooking' ) ?>
		</label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="get_attr_shortcode_js" data-attr="room_type" name="apb_setting[field_status][status_room_type_sc]" <?php checked( $field_status['field_status']['status_room_type_sc'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Room Type', 'awebooking' ); ?>
		</label>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<label>
			<input class="get_attr_shortcode_js" data-attr="mullti_room" name="apb_setting[field_status][status_multi_room_sc]" <?php checked( $field_status['field_status']['status_multi_room_sc'], 1, true ); ?> type="checkbox" value="1">
			<?php esc_html_e( 'Enable Select multiple Room ', 'awebooking' ) ?>
		</label>
	</div>
</div>

<?php
do_action( 'apb_after_shortcode_checkbox_setting', $field_status );
?>

<div class="form-elements">
	<div class="form-radios">
		<select class="apb_form_check_style_js" name="apb_setting[field_status][form_style]">
			<option <?php checked( $field_status['field_status']['form_style'], 1 ); ?> value="1"><?php esc_html_e( 'Vertical', 'awebooking' ) ?></option>
			<option <?php checked( $field_status['field_status']['form_style'], 2 ); ?> value="2"><?php esc_html_e( 'Horizontal', 'awebooking' ) ?></option>
		</select>
		<?php esc_html_e( 'Style form check available.', 'awebooking' ) ?>
	</div>
</div>

<div class="form-elements">
	<div class="form-radios">
		<span class="description">
			<?php esc_html_e( 'Copy this shortcode and paste it into your post, page, or text widget content:', 'awebooking' ) ?>
		</span>
		<span class="shortcode wp-ui-highlight apb-gen-shortcode-js">
			[apb_check_available]
		</span>
	</div>
</div>

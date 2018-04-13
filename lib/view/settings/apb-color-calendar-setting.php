<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Tempalte setting calendar
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

?>
<div class="form-elements">
	<p><?php esc_html_e( 'Unavailable', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="color-unavailable" value="<?php echo esc_attr( get_option( 'color-unavailable' ) ); ?>" class="my-color-field" />
	</div>

	<div class="form-radios">
		<input type="text" name="label-unavailable" value="<?php echo esc_attr( get_option( 'label-unavailable' ) ) ?>" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Available', 'awebooking' ) ?> </p>
	<div class="form-radios">
		<input type="text" name="color-available" value="<?php echo esc_attr( get_option( 'color-available' ) ) ?>" class="my-color-field" />
	</div>
	<div class="form-radios">
		<input type="text" name="label-available" value="<?php echo esc_attr( get_option( 'label-available' ) ) ?>" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Pending', 'awebooking' ) ?> </p>
	<div class="form-radios">
		<input type="text" name="color-pending" value="<?php echo esc_attr( get_option( 'color-pending' ) ) ?>" class="my-color-field" />
	</div>
	<div class="form-radios">
		<input type="text" name="label-pending" value="<?php echo esc_attr( get_option( 'label-pending' ) ) ?>" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Complete', 'awebooking' ) ?> </p>
	<div class="form-radios">
		<input type="text" name="color-complate" value="<?php echo esc_attr( get_option( 'color-complate' ) ) ?>" class="my-color-field" />
	</div>
	<div class="form-radios">
		<input type="text" name="label-complate" value="<?php echo esc_attr( get_option( 'label-complate' ) ) ?>" />
	</div>
</div>

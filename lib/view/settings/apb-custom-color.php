<?php
/**
 * Custom color setting.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="form-elements">
	<p><?php esc_html_e( 'Primary color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-primary" value="<?php echo esc_attr( get_option( 'apb-primary' ) ); ?>" class="my-color-field" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Secondary color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-secondary" value="<?php echo esc_attr( get_option( 'apb-secondary' ) ); ?>" class="my-color-field" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Background color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-background-color" value="<?php echo esc_attr( get_option( 'apb-background-color' ) ); ?>" class="my-color-field" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Background secondary color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-background-secondary" value="<?php echo esc_attr( get_option( 'apb-background-secondary' ) ); ?>" class="my-color-field" />
	</div>
</div>

<div class="form-elements">
	<p><?php esc_html_e( 'Highlight color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-highlight-color" value="<?php echo esc_attr( get_option( 'apb-highlight-color' ) ); ?>" class="my-color-field" />
	</div>
</div>


<div class="form-elements">
	<p><?php esc_html_e( 'Highlight hover color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-highlight-hover-color" value="<?php echo esc_attr( get_option( 'apb-highlight-hover-color' ) ); ?>" class="my-color-field" />
	</div>
</div>


<div class="form-elements">
	<p><?php esc_html_e( 'Border color', 'awebooking' ); ?> </p>
	<div class="form-radios">
		<input type="text" name="apb-border-color" value="<?php echo esc_attr( get_option( 'apb-border-color' ) ); ?>" class="my-color-field" />
	</div>
</div>


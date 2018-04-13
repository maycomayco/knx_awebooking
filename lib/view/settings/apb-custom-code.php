<?php
/**
 * Setting custom code.
 *
 * @version   2.1
 * @package   AweBooking/admin/
 * @author    AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="form-elements">
	<label for="apb_custom_css"><?php esc_html_e( 'Custom CSS', 'awebooking' ) ?></label><br>
	<div class="form-radios">
		<div class="form-elements">
			<textarea rows="4" cols="50" name="apb_custom_css" id="apb_custom_css"><?php echo esc_textarea( wp_unslash( get_option( 'apb_custom_css' ) ) ); ?></textarea>
			<br/>
			<span class="description">
				<?php esc_html_e( 'Not include <style> tag.', 'awebooking' ); ?>
			</span>
		</div>
	</div>
</div>

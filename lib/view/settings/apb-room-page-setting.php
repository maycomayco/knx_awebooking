<?php
/**
 *  Tempalte setting page setting
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
	<label for="check_avb"><?php esc_html_e( 'Check Availability', 'awebooking' ) ?></label><br>
	<?php AWE_function::select_page( 'check_avb', AWE_function::get_check_available_page_id() ); ?>
	<span class="description">
		<?php esc_html_e( 'Selected page to display check availability form.', 'awebooking' ) ?>
	</span>
</div>

<div class="form-elements">
	<label for="list_room"><?php esc_html_e( 'List Room', 'awebooking' ) ?></label><br>
	<?php AWE_function::select_page( 'list_room', AWE_function::get_list_room_page_id() ); ?>
	<span class="description">
		<?php esc_html_e( 'Selected page to display all rooms.', 'awebooking' ) ?>
	</span>
</div>

<div class="form-elements">
	<label for="apb_checkout"><?php esc_html_e( 'Checkout', 'awebooking' ); ?></label><br>
	<?php AWE_function::select_page( 'apb_checkout', AWE_function::get_checkout_page_id() ); ?>
	<span class="description">
		<?php esc_html_e( 'Selected page to display checkout form.', 'awebooking' ) ?>
	</span>
</div>

<div class="form-elements">
	<a class="apb-btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=intallpage' ) ); ?>"><?php esc_html_e( 'Install Pages', 'awebooking' ) ?> </a>

	<span class="description">
		<?php esc_html_e( 'Install default pages.', 'awebooking' ) ?>
	</span>
</div>


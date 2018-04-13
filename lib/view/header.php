<?php
/**
 * AWE Header Teamplate Manage Room Type.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="clearfix" id="branding">
    <div class="awe-brand-header">
        <h1 class="page-title"><?php esc_html_e( 'Room management', 'awebooking' ); ?></h1>
        <ul class="tabs primary">
            <li><a <?php echo ( isset( $_GET['action'] ) && 'manage_avb' == $_GET['action'] ) ? 'class="active"' : ''; ?> href="<?php echo esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=manage_avb' ) ) ?>"><?php esc_html_e( 'Room availability management', 'awebooking' ); ?></a></li>
            <li><a <?php echo ( isset( $_GET['action'] ) && 'manage_pricing' == $_GET['action'] ) ? 'class="active"' : ''; ?> href="<?php echo esc_url( admin_url( 'edit.php?post_type=apb_room_type&page=rooms.php&action=manage_pricing' ) ) ?>"><?php esc_html_e( 'Room pricing management', 'awebooking' ); ?></a></li>
        </ul>
    </div>
</div>

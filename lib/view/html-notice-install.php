<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Notice before intall plugin
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

?>

<div id="apb_message" class="updated">
    <p><?php _e( 'You need to add some pages to display rooms and check availability result for AweBooking.', 'awebooking'); ?></p>
    <p class="submit">     
        <a href="<?php echo admin_url() ?>edit.php?post_type=apb_room_type&page=rooms.php&action=intallpage" class="button-primary"><?php _e( 'Add Pages automatically', 'awebooking' ); ?></a> 
        <a class="button-secondary button apb_skip" href="#"><?php _e( 'Skip','awebooking' ); ?></a>
    </p>
</div>

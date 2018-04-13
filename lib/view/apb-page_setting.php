<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Page setting for plugin
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */
?>
<div id="awe-booking-wraper" class="awe-booking-wraper">
	<div class="awe-plugin">
		<div class="clearfix" id="branding">
			<div class="awe-brand-header">
				<h1 class="page-title"><?php esc_html_e( 'Settings', 'awebooking' ); ?></h1>
			</div>
		</div>
		<?php
		if ( isset( $_SESSION['apb_notice'] ) ) {
			?>
				<div class="updated notice notice-success is-dismissible below-h2" id="message">
					<p><?php echo esc_html( $_SESSION['apb_notice'] ); ?></p>
					<button class="notice-dismiss" type="button">
					<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'awebooking' ); ?></span></button>
				</div>
			<?php
			unset( $_SESSION['apb_notice'] );
		}
		?>
		<div id="page">
			<div class="tabs-secondary clearfix"></div>
			<form action="<?php echo admin_url( 'edit.php?post_type=apb_room_type&page=apb-setting.php' ) ?>" method="POST" enctype="multipart/form-data">
				<div class="clearfix" id="content">
					<div id="tabs" class="vertical-tabs">
						<ul class="vertical-tabs-list">
							<?php foreach ( $tab_array as $item_tab ) : ?>
								<li class="vertical-tab-button">
									<a href="#<?php echo esc_attr( $item_tab['tab_href'] ) ?>">
										<strong><?php echo esc_html( $item_tab['tab_name'] ) ?></strong>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
						<div class="vertical-tabs-panes vertical-tabs-processed">
							<?php
							$tabs_sub = array();
							foreach ( $tab_array as $item_tab ) {
								echo $item_tab['tab_content'];
							}
							?>

						</div>
					</div>
				</div>
				<button type="submit" class="awe-avb-js button" name="saved"><?php esc_html_e( 'Save Settings', 'awebooking' ); ?></button>
				<?php // if ( AWE_function::old_table_exists() ) : ?>
					<!--<button type="button" class="awe-avb-js awe-avb-remove-old-data button"><?php esc_html_e( 'Remove old data', 'awebooking' ); ?></button>-->
				<?php // endif; ?>
			</form>
		</div>
	</div>
</div>

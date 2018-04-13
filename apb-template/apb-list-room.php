<?php
/**
 * The template for displaying list room
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
do_action( 'apb_renderBefore' );
?>
<!-- PAGE WRAP -->
<div class="apb-container">
	<div class="apb-layout">
		<!-- SIDEBAR -->
		<div class="apb-widget-area">
			<?php
			// Hook apb_fill_content_js.
			do_action( 'apb_fill_content_js' );

			// Hook form check available.
			do_action( 'form_check_availability' );
			?>
		</div>
		<!-- END / SIDEBAR -->

		<!-- CONTENT -->
		<div class="apb-content-area">
			<?php
			/**
			 * Layout_loading hook.
			 *
			 * @hooked layout_loading() - 10
			 */
			do_action( 'layout_loading' );
			?>
			<div class="apb-content <?php echo esc_attr( apply_filters( 'apb_content_room_js', 'apb-content-js' ) ); ?>">
				<div class="apb-content">
					<div class="apb-room">
						<?php
						/**
						 * Hook apb_loop_content_list_room
						 *
						 * @hooked loop_content_list_room() - 10
						 */
						do_action( 'apb_loop_content_list_room' );
						wp_reset_query()
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END / PAGE WRAP -->
<?php
do_action( 'apb_renderAfter' );
get_footer();

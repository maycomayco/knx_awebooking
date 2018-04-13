<?php
/**
 * The template for displaying cbheck out
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
<div class="apb-container apb-checkout">
	<div class="apb-layout apb-sidebar-left">

		<?php
		/**
		 * Display step.
		 *
		 * @hooked html_step()
		 */
		do_action( 'form_step', 3 );
		
		?>

		<!-- SIDEBAR -->
		<div class="apb-widget-area">
			<div class="apb-room-selected">
				<!-- HEADING -->
				<h2 class="apb-heading"><?php esc_html_e( 'Your booking', 'awebooking' ) ?></h2>
				<!-- END / HEADING -->

				<div class="apb-room-selected_content">
					<?php do_action( 'apb_loop_item_cart_info' ); ?>
				</div>
			</div>
		</div>
		<!-- END / SIDEBAR -->

		<!-- CONTENT -->
		<div class="apb-content-area">
			<?php

			/**
			 * Layout_loading hook.
			 */
			do_action( 'layout_loading' );

			/**
			 * Before content add data of javascript.
			 */
			do_action( 'apb_room_content_before' );
			
			$apb_cart = AWE_function::get_cart();
			
			if ( ! empty( $apb_cart ) ) {
				while ( have_posts() ) : the_post();
					?>
					<section class="section-checkout">
						<div class="checkout">
							<div class="row">

								<div class="col-md-6">
									<div class="checkout_head">
										<h3><?php the_title(); ?></h3>
									</div>
									<?php the_content(); ?>
								</div>
							</div>
						</div>
					</section>
				<?php
				endwhile;
				wp_reset_postdata();
				?>
			<?php } else { ?>
				<a href="<?php echo esc_url( AWE_function::get_check_available_page() ) ?>"><?php esc_html_e( 'No Item. Return to Check Availability form!', 'awebooking' ); ?></a>
			<?php } ?>

			<?php
			/**
			 * After content add data of javascript
			 */
			do_action( 'apb_room_content_after' );
			?>
		</div>
		<!-- END / CONTENT -->

	</div>
</div>
<!-- END / PAGE WRAP -->
<?php
do_action( 'apb_renderAfter' );
get_footer();
?>

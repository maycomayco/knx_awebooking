<?php
/**
 * The template for displaying single room
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

do_action( 'apb_renderBefore' );
?>
<div class="apb-container">
	<?php
	/**
	 * Layout_loading hook.
	 */
	do_action( 'layout_loading' );
	?>
	<div class="apb-product_detail room-detail">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<div class="apb-layout">
					<div class="apb-content-area">
						<?php
						/**
						 * Hook: apb_single_gallery.
						 */
						do_action( 'apb_single_gallery' );
						?>
					</div>

					<div class="apb-widget-area">
						<div class="room-detail_book">
							<div class="room-detail_total">
								<h6><?php echo esc_html( ucfirst( get_the_title() ) ); ?></h6>
								<?php loop_price_single( get_post_meta( get_the_ID(), 'base_price', true ) ); ?>
							</div>

							<?php
							form_check_availability();

							/**
							 * Hook: apb_form_check_available_single.
							 */
							// do_action( 'apb_form_check_available_single' );
							?>
						</div>
					</div>
				</div>

				<?php // do_action( 'apb_single_message' ); ?>

				<div class="apb-product_tab">
					<?php
					/**
					 * Handler active tabs and content-tabs.
					 */
					$date_available_need_active = ! empty( $_GET['apb_mon'] );

					?>
					<ul class="apb-product_tab-header">
						<li class="<?php echo $date_available_need_active ? '' : 'active'; ?>">
							<a href="#information" data-toggle="tab"><?php esc_html_e( 'Information', 'awebooking' ) ?></a>
						</li>

						<?php
						$package = AWE_function::get_room_option( get_the_ID(), 'apb_room_type' );
						if ( ! empty( $package ) ) :
							?>
							<li>
								<a href="#package" data-toggle="tab"><?php esc_html_e( 'Optional extras', 'awebooking' ) ?></a>
							</li>
						<?php endif; ?>

						<?php if ( AWE_function::show_single_calendar() ) : ?>
							<li class="<?php echo $date_available_need_active ? 'active' : ''; ?>">
								<a href="#date-available" data-toggle="tab"><?php esc_html_e( 'Date Available', 'awebooking' ) ?></a>
							</li>
						<?php endif; ?>
					</ul>

					<div class="apb-product_tab-panel tab-content">
						<div class="tab-pane fade <?php echo $date_available_need_active ? '' : 'active in'; ?>" id="information">
							<?php the_content() ?>
						</div>

						<div class="tab-pane fade" id="package">
							<?php
							/**
							 * Hook : apb_loop_single_package
							 * Get list package for room.
							 * @hooked loop_single_package
							 */
							do_action( 'apb_loop_single_package' );
							?>
						</div>

						<?php if ( AWE_function::show_single_calendar() ) : ?>
							<div class="tab-pane fade <?php echo $date_available_need_active ? 'active in' : ''; ?>" id="date-available">
								<?php
								/**
								 * @hooked apb_room_type_availability_calendar() - 10
								 */
								do_action( 'apb_room_type_availability_calendar', AWE_function::wpml_get_default_room_type( get_the_ID() ) );
								?>
							</div>
						<?php endif; ?>
					</div>

				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</div>
<!-- END / PAGE WRAP -->
<?php
do_action( 'apb_renderAfter' );
get_footer();
?>

<?php
/**
 *  Manage all room availability
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="awe-booking-wraper" class="awe-booking-wraper">
	<div class="awe-plugin">
		<?php include 'header.php'; ?>

		<div id="page">
			<div class="tabs-secondary clearfix"></div>

			<div class="clearfix" id="content">
				<div class="element-invisible"><a id="main-content"></a></div>

				<div class="region region-content">
					<?php
					/**
					 * Get Date current.
					 */
					$mon_current = isset( $_GET['mon'] ) ? absint( $_GET['mon'] ) : date( 'm' );
					$year_current = isset( $_GET['year'] ) ? absint( $_GET['year'] ) : date( 'Y' );
					$month_curent = date( 'F', mktime( 0, 0, 0, $mon_current, 1, date( 'Y' ) ) );
					$room_type_id = ! empty( $_GET['apb_room_type'] ) ? absint( $_GET['apb_room_type'] ) : 0;
					?>
					<form action="" method="GET">
						<div class="awe-form-item">
							<label><?php esc_html_e( 'Month', 'awebooking' ); ?></label>
							<select name="mon">
								<?php
								for ( $m = 1; $m <= 12; $m++ ) {
									$month = date( 'F', mktime( 0, 0, 0, $m, 1, date( 'Y' ) ) );
									?>
									<option <?php selected( $month_curent, $month ); ?> value="<?php echo absint( $m ); ?>"><?php echo esc_html( $month ); ?></option>
									<?php
								}
								?>
							 </select>
						</div>

						<div class="awe-form-item">
							<label><?php esc_html_e( 'Year', 'awebooking' ) ?></label>
							<select name="year">
								<?php
								for ( $y = 2015; $y <= date( 'Y' ) + 3; $y++ ) {
									echo '<option ' . selected( $year_current, $y, false ) . ' value="' . absint( $y ) . '">' . absint( $y ) . '</option>';
								}
								?>
							</select>
						</div>

						<div class="awe-form-item">
							<label><?php esc_html_e( 'Room type', 'awebooking' ) ?></label>
							<select name="apb_room_type">
								<option value="0" <?php selected( $room_type_id, 0 ) ?>><?php esc_html_e( 'All', 'awebooking' ); ?></option>
								<?php
								$apb_query = get_posts( apply_filters( 'apb_room_management_room_type_query_args', array(
									'post_type'			=> 'apb_room_type',
									'posts_per_page'	=> -1,
									'orderby'			=> 'title',
									'order'				=> 'asc',
									'suppress_filters'	=> 0,
								) ) );

								$room_types = array();

								foreach ( $apb_query as $v ) {
									$room_types[] = $v->ID;

									printf(
										'<option value="%1$s" %2$s>%3$s</option>',
										absint( $v->ID ),
										selected( $v->ID, $room_type_id, false ),
										esc_html( $v->post_title )
									);
								}
								?>
							</select>
						</div>

						<?php
						/**
						 * Hook apb_after_room_management_filter_form.
						 *
						 * @since 2.2.1
						 */
						do_action( 'apb_after_room_management_filter_form' );
						?>

						<div class="awe-form-item">
							<input type="hidden" name="post_type" value="apb_room_type">
							<input type="hidden" name="page" value="rooms.php">
							<input type="hidden" name="action" value="manage_avb">
							<button type="submit" class="button"><?php esc_html_e( 'Filter', 'awebooking' ); ?></button>
						</div>
					</form>

					<fieldset class="form-wrapper">
						<legend>
							<span class="fieldset-legend">
								<a href="#" class="form-update-pricing-js"><?php esc_html_e( 'Update Availability', 'awebooking' ) ?></a>
							</span>
						</legend>

						<div class="update-pricing" style="display:none">
							<div class="fieldset-description">
								<?php esc_html_e( 'Change the availability status of the rooms selected below for the specified date range.', 'awebooking' ) ?>
							</div>
							<div class="awe-form-item">
								<input id="awe_datepicker_start" name="rooms_start_date" placeholder="<?php esc_attr_e( 'From Date', 'awebooking' ); ?>" name="" type="text" class="form-text date-start-js">
							</div>
							<div class="awe-form-item">
								<input id="awe_datepicker_end" name="rooms_end_date" placeholder="<?php esc_attr_e( 'To Date', 'awebooking' ); ?>" type="text" class="form-text date-end-js">
							</div>
							<div class="awe-form-item">
								<label><?php esc_html_e( 'Sun', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Sunday' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Mon', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Monday', 'awebooking' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Tue', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Tuesday' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Wed', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Wednesday' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Thu', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Thursday' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Fri', 'awebooking' ) ?></label> <input name="day_options[]" value="<?php echo esc_attr( 'Friday' ) ?>" type="checkbox" class="form-text get_day_js">
								<label><?php esc_html_e( 'Sat', 'awebooking' ) ?></label> <input name="day_options[7]" value="<?php echo esc_attr( 'Saturday' ) ?>" type="checkbox" class="form-text get_day_js">
							</div>
							<div class="awe-form-item">
								<select class="form-select" name="unit_state">
									<option value="1"><?php esc_html_e( 'Unavailable', 'awebooking' ) ?></option>
									<option value="2"><?php esc_html_e( 'Available', 'awebooking' ) ?></option>
								</select>
							</div>
							<div>
								<button type="button" class="awe-avb-js button" data-value="yes"><?php esc_html_e( 'Update Availability', 'awebooking' ) ?></button>
							</div>
							<div class="btn-update-status-js" style="display:none;">
								<p class="description">(*) <?php esc_html_e( 'Displaying orders can be affected by changing  room status in the date range that you selected. You should change room status only after deleting orders in the selected date range.', 'awebooking' ); ?></p><br/>
								<button data-value="yes" class="button apb-update-status-js"><?php esc_html_e( 'Yes', 'awebooking' ); ?></button>
								<button data-value="no" class="button apb-update-status-js"><?php esc_html_e( 'No', 'awebooking' ); ?></button>
								<span class="spinner" style="display: none;"></span>
							</div>
						</div>
					</fieldset>

					<table class="rooms-month-manager">
						<thead>
							<tr>
								<th class="unit-bulk-select">
									<div class="form-item form-type-select form-item-select-all">
										<div class="form-elements">
											<select name="select_all">
												<option selected="selected" value="">- <?php esc_html_e( 'Select', 'awebooking' ) ?> -</option>
												<option value="all"><?php esc_html_e( 'All', 'awebooking' ) ?></option>
												<option value="none"><?php esc_html_e( 'None', 'awebooking' ) ?></option>
											</select>
										</div>
									</div>
								</th>
								<th class="month-name">
									<div class="fc-header-title">
										<h2><?php echo esc_html( date( 'F', mktime( 0, 0, 0, $mon_current, 1, $year_current ) ) . ' ' . $year_current ) ?></h2>
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$args = array(
								'post_type'  		=> 'apb_room',
								'posts_per_page'    => -1,
								'post_status'		=> 'publish',
								'orderby'			=> 'title',
								'order'				=> 'asc',
							);
							if ( ! empty( $_GET['apb_room_type'] ) ) {
								$args['post_parent'] = absint( $_GET['apb_room_type'] );
							}

							// add_filter( 'posts_orderby', array( 'AWE_function', 'orderby_post_title_int' ) );
 							$room = new WP_Query( $args );
							// remove_filter( 'posts_orderby', array( 'AWE_function', 'orderby_post_title_int' ) );

							while ( $room->have_posts() ) : $room->the_post();
								global $post;

								if ( ! in_array( $post->post_parent, $room_types ) ) {
									continue;
								}
								?>
								<tr>
									<th class="unit-name">
										<div class="form-item form-type-checkbox form-item-rooms-1">
											<div class="form-elements">
												<input type="checkbox" id="room-<?php echo absint( AWE_function::get_master_post_id( get_the_ID() ) ) ?>" name="room_id[]" value="<?php echo absint( AWE_function::get_master_post_id( get_the_ID() ) ) ?>" class="form-checkbox get_room_id_js">
												<label for="room-<?php echo absint( AWE_function::get_master_post_id( get_the_ID() ) ) ?>" class="label-checkbox option"><?php the_title(); ?></label>
										   </div>
										</div>
									</th>
									<td class="unit-days">
										<div class="manage-avb" data-id="<?php echo absint( AWE_function::get_master_post_id( get_the_ID() ) ) ?>" id="manage-avb-<?php the_ID() ?>"> </div>
									</td>
								</tr>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</tbody>
					</table>
					<div class="box-event-info-js">
						<table class="sticky-header" >
							<thead>
								<tr>
									<th><?php esc_html_e( 'Booking Details', 'awebooking' ) ?></th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td>
										<span class="spinner is-active apb-get-event" style="display: none;"></span>
										<div class="content-event-js"> </div>
									</td>
								</tr>

							</tbody>
							 <tfoot>
								<tr>
									<th  colspan="2">
										<button type="button" class="close-box-room-type-js button"><?php esc_html_e( 'Close', 'awebooking' ) ?></button>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>

					<script>
						<?php
						$awe_date_current = absint( $year_current ) . '-' . zeroise( $mon_current, 2 );
						$awe_start_date = date( AWE_function::get_current_date_format(), strtotime( date( 'm', strtotime( $year_current . '-' . $mon_current ) ) . '/01/' . absint( $year_current ) ) );
						$awe_end_date = date( AWE_function::get_current_date_format(), strtotime( date( 'm', strtotime( $year_current . '-' . ( $mon_current + 1 ) ) ) . '/01/' . absint( $year_current ) ) );
						?>
						var awe_date_current  = '<?php echo esc_js( $awe_date_current ); ?>';
						var awe_start_date    = '<?php echo esc_js( $awe_start_date ) ?>';
						var awe_end_date      = '<?php echo esc_js( $awe_end_date ) ?>';
					</script>
				</div>
			</div>
		</div>
	</div>
</div>




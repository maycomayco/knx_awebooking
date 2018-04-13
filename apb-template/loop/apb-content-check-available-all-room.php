<?php
/**
 * The template for displaying loop body content after check available.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- ITEM -->
<div class="apb-room_item apb-room_item-<?php the_ID(); ?>">
	<div class="apb-room_heading">
		<h2 class="apb-room_name">
			<?php echo '<a href="' . esc_url( get_permalink() ) . '" target="_blank">' . esc_html( get_the_title() ) . '</a>'; ?>
		</h2>

		<?php loop_price( $avg_price, get_the_ID() ); ?>
	</div>

	<div class="apb-room_img">
		<a href="<?php the_permalink() ?>" target="_blank"><?php the_post_thumbnail( 'post-thumbnail' ); ?></a>
		<?php if ( ! empty( $extra_sale ) ) : ?>
			<span class="apb-sale-icon"><img src="<?php echo esc_url( AWE_BK_BASE_URL_PLUGIN . '/assets/frontend/img/sale.png' ); ?>"></span>
		<?php endif; ?>
	</div>

	<div class="apb-room_text">
		<div class="apb-room_desc">
			<?php echo wp_kses_post( $room_desc ); ?>
		</div>

		<div class="apb-total-price-wrapper">
			<?php loop_total_price( $total_price, get_the_ID() ); ?>
			<a class="readmore-price" data-toggle="abp-modal" data-target="#apb-modal-<?php the_ID() ?>" href="#"><?php esc_html_e( 'View Price Detail', 'awebooking' ); ?></a>
		</div>

		<?php if ( get_option( 'apb_show_remain_room' ) ) : ?>
			<p class="apb-remain-room">
				<?php
				printf( esc_html( _n( '%s room remaining', '%s rooms remaining', $remain, 'awebooking' ) ), absint( $remain ) );
				?>
			</p>
		<?php endif; ?>

		<div class="apb-action-wrapper">
			<a href="#" data-id="<?php echo absint( get_the_ID() ); ?>" data-cart-index="<?php echo intval( $cart_index ); ?>" class="apb-btn apb-book-now-js" title="<?php the_title(); ?>"><?php esc_html_e( 'BOOK ROOM', 'awebooking' ); ?></a>
		</div>
	</div>

	<?php
	/**
	 * Hook List apb_layout_list_package
	 * List package for room
	 *
	 * @hooked layout_list_package
	 */
	do_action( 'apb_layout_list_package', array( 'count_day' => count( AWE_function::range_date( $from, $to ) ) -1 ) );
	?>

</div>

<div class="apb-modal" id="apb-modal-<?php the_ID() ?>">
	<div class="apb-modal-dialog">
		<div class="apb-modal-content">
			<div class="apb-modal-header">
				<h4 class="apb-modal-title"><?php the_title(); ?></h4>
				<a href="#" data-toggle="abp-close-modal" class="abp-close-modal">&times;</a>
			</div>

			<div class="apb-modal-body">
				<!-- List detail price of day -->
				<div class="apb-list-price">
					<h6><?php esc_html_e( 'Price Detail Of Day', 'awebooking' ); ?></h6>

					<ul class="apb-list-price-list">
						<?php
						$year_from = date( 'Y', strtotime( AWE_function::convert_date_to_mdY( $_POST['from'] ) ) );
						$year_to = date( 'Y', strtotime( AWE_function::convert_date_to_mdY( $_POST['to'] ) ) );

						if ( ! empty( $info_price_day ) ) :
							foreach ( $info_price_day as $month => $list_day ) :
								foreach ( $list_day as $day => $price_day ) :
									if ( $year_from < $year_to ) {
										if ( intval( $month ) > 6 ) {
											$year = $year_from;
										} else {
											$year = $year_to;
										}
									} else {
										$year = $year_from;
									}
									// Check day exists.
									if ( ! checkdate( $month, $day, $year ) ) {
										continue;
									}

									echo '<li>';
									echo '<span class="list-price-item">' . date_i18n( AWE_function::get_current_date_format(), strtotime( $month . '/' . $day . '/' . $year ) ) . ' - <span>' . wp_kses_post( AWE_function::apb_price( $price_day ) ) . '</span></span>';
									echo '</li>';
								endforeach;
							endforeach;
						endif ?>
					</ul>
				</div>

				<!-- List detail Extra price -->
				<?php
				if ( ! empty( $extra_price_data['adult'] ) || ! empty( $extra_price_data['child'] ) ) {
					$output = '';

					if ( ! empty( $extra_price_data['adult'] ) ) {
						$output .= '<li>';
						$output .= '<span class="list-price-item">';
						$output .= sprintf( esc_html__( '%s Adult', 'awebooking' ), absint( $extra_guess_data['adult'] ) );
						$output .= ' + ';
						$output .= '<span>' . sprintf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['adult'] ) ) ) . '</span>';
						$output .= '</span>';
						$output .= '</li>';
					}

					if ( ! empty( $extra_price_data['child'] ) ) {
						$output .= '<li>';
						$output .= '<span class="list-price-item">';
						$output .= sprintf( esc_html__( '%s Child', 'awebooking' ), absint( $extra_guess_data['child'] ) );
						$output .= ' + ';
						$output .= '<span>' . sprintf( esc_html__( '%s/night', 'awebooking' ), wp_kses_post( AWE_function::apb_price( $extra_price_data['child'] ) ) ) . '</span>';
						$output .= '</span>';
						$output .= '</li>';
					}

					if ( ! empty( $output ) ) { ?>
						<div class="apb-list-price">
							<h6><?php esc_html_e( 'Extra price', 'awebooking' ); ?></h6>
							<ul class="apb-list-price-list">
								<?php echo wp_kses_post( $output ); ?>
							</ul>
						</div>
						<?php
					}
				}

				if ( ! empty( $extra_sale ) ) {
					$total_date = count( AWE_function::range_date( $from, $to ) );
					$item_sale = AWE_function::apb_get_extra_sale( $extra_sale, $total_date, $from );

					if ( ! empty( $item_sale ) ) {
						if( count($item_sale) > 2 ) {
						?>
						<!-- List detail Discount price -->
						<div class="apb-list-price clearfix">
							<h6><?php esc_html_e( 'Discount Price', 'awebooking' ); ?></h6>
							<ul class="apb-list-price-list">
								<?php
								if ( 'Before-Day' == $item_sale['type_duration'] ) {
									echo '<li><span class="list-price-item">';
									printf( esc_html__( 'Booking %s upwards: ', 'awebooking' ), str_replace( '-', '&nbsp;', $item_sale['type_duration'] ) . $item_sale['total_day'] );
								} else {
									echo '<li><span class="list-price-item">';
									printf( esc_html__( 'From %s upwards: ', 'awebooking' ), $item_sale['total'] . ' ' . $item_sale['type_duration'] );
								}
								switch ( $item_sale['sale_type'] ) {
									case 'sub':
										echo '-<span>' . wp_kses_post( AWE_function::apb_price( $item_sale['amount'] ) ) . ' </span>';
										break;
									case 'decrease':
										echo '-<span>' . ( float ) $item_sale['amount'] . '%</span>';
										break;
								}
								echo '</span></li>';
								?>
							</ul>
						</div>
						<?php }	 else {

							?>
							<div class="apb-list-price clearfix">
							<h6><?php esc_html_e( 'Discount Price', 'awebooking' ); ?></h6>
							<ul class="apb-list-price-list">
								<?php
								
								if( $item_sale['before'] != null ) {
									echo '<li>';
									printf( esc_html__( 'From before %s day: ', 'awebooking' ), $item_sale['before']['total'] . ' ' . $item_sale['type_duration']);
								}
								// printf( esc_html__( 'From before %s days upwards and %s %s: ', 'awebooking' ), $item_sale['before']['total'] . ' ' . $item_sale['type_duration'], $item_sale['week']['total'], $item_sale['week']['type_duration'] );
								if( 'sub' == $item_sale['before']['sale_type'] ) { 
									echo '<span class="list-price-item"><span>' . wp_kses_post( AWE_function::apb_price( $item_sale['before']['amount'] ) ) . '</span>';
								} else if( 'decrease' == $item_sale['before']['sale_type'] ) {
									echo '<span class="list-price-item"><span>' . ( float ) ( $item_sale['before']['amount'] ) . '%</span>';
								} 
								if( $item_sale['week'] != null ) {
									 echo '<li><span class="list-price-item">';
									 printf( esc_html__( 'Booking upwads %s: ', 'awebooking' ),$item_sale['week']['total'] .' ' . $item_sale['week']['type_duration']);
								} else {
									 echo '<li><span class="list-price-item">';
								}

								if( 'sub' == $item_sale['week']['sale_type'] ) { 
									echo  '<span>' . wp_kses_post ( AWE_function::apb_price( $item_sale['week']['amount'] ) ) . '</span></span></li>';
								} else if( 'decrease' == $item_sale['week']['sale_type'] ) {
									echo '<span>' . ( float ) ( $item_sale['week']['amount'] ) . '%</span></span></li>';
								} 
		
								//echo $price_text;
								//echo '</li>';
								?>
							</ul>
						</div>
							<?php
							}

						 ?>
					<?php } ?>
				<?php } ?>

				<div class="apb-list-price apb-list-price-package" style="display: none;">
					<h6><?php esc_html_e( 'Package', 'awebooking' ); ?></h6>
					<ul class="apb-list-price-list"></ul>
				</div>
			</div>

		</div>
	</div>
</div>
<!-- END / ITEM -->

<script type="text/template" id="apb-popup-package-price-tpl">
	<li id="list-price-item-<%= package_id %>">
		<span class="list-price-item">
			<%= package_name %> <%= day_text %>:
			<span><%= package_num %> x <%= price_text %></span>
		</span>
	</li>
</script>

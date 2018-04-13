<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying loop package of room.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

?>
<div class="apb-room_package">
	<a data-toggle="collapse" href="#apb-room_packge-<?php the_ID() ?>" class="apb-room_package-more"><?php esc_html_e( 'Optional extras', 'awebooking' ); ?></a>
	<div class="collapse apb-room_package-content" id="apb-room_packge-<?php the_ID(); ?>">
		<?php foreach ( $get_option as $item_option ) : ?>
			<!-- ITEM PACKGE -->
			<div class="apb-package_item">
				<div class="apb-package_img">
					<?php
					
					if ( true == $argsData['check'] ) {
						printf('<input class="package-check-js id-option-js-%d" id="package-%d" data-option_id="%d" data-id="%d" data-pricing="%s" data-daily="%d" data-days="%d" value="%s" type="checkbox">',
							absint( $item_option->id ),
							absint( $item_option->id ),
							absint( $item_option->id ),
							absint( get_the_ID() ),
							( float ) $item_option->option_value,
							absint( $item_option->revision_id ),
							isset( $argsData['count_day'] ) ? absint( $argsData['count_day'] ) : 1,
							esc_attr( $item_option->option_operation )
						);
					}
					?>
					</div>
					<div class="apb-package_text">
						<h4>
							<i class="<?php echo isset( $item_option->option_icon ) ? $item_option->option_icon : '' ?>"></i> <label for="package-<?php echo esc_attr( $item_option->id ) ?>"><?php echo esc_html( $item_option->option_name ); ?></label>
						</h4>
						<p><?php echo esc_html( $item_option->option_desc ); ?></p>
						<div class="package-input" style="display: none;">
							<div class="apb-select-package">
								<?php
								if ( true == $argsData['check'] ) {
									printf('<input class="number-of-packages package-option-id-%d" data-id="%d" data-option_id="%d"  data-pricing="%s" data-operation="%s" type="%s" size="5" value="1" placeholder="%s" name="num_package">',
										absint( $item_option->id ),
										absint( get_the_ID() ),
										absint( $item_option->id ),
										$item_option->option_value,
										$item_option->option_operation,
										// ( 1 == $item_option->revision_id ) ? 'hidden' : 'text',
										'text',
										// ( 1 == $item_option->revision_id && isset( $argsData['count_day'] ) ) ? $argsData['count_day'] : 1,
										esc_attr__( 'Number', 'AweBooking' )
									);
								}
								?>
							</div>

							<div class="apb-package_book-price">
								<p class="apb-package_price">
									<span class="amount">
										<?php echo wp_kses_post( AWE_function::apb_price( $item_option->option_value ) ); ?>
									</span> 
									<span class="day">/ <?php ( 1 == $item_option->revision_id ) ? esc_html_e( 'day', 'awebooking' ) : esc_html_e( 'package', 'awebooking' ); ?>
									</span>
								</p>
							</div>
						</div>
					</div>
			</div>
			<!-- END / ITEM PACKGE -->
		<?php endforeach; ?>
	</div>
</div>

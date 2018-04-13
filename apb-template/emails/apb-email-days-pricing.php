<?php
/**
 * Days pricing in email
 *
 * @package Awebooking
 */

?>
<h6 style="color: #333333; display: inline-block; font-size: 14px; font-weight: bold; line-height: 1.428em; margin: 0 10px 0 0; text-transform: uppercase;">
	<?php esc_html_e( 'Price/Night', 'awebooking' ); ?>
</h6>
<ul style="list-style: outside none none; margin-bottom: 0; margin-top: 5px; padding-bottom: 2px; padding-left: 0;">
	<?php
	$from = date_i18n( AWE_function::get_current_date_format(), strtotime( $item['from'] ) );
	$to = date_i18n( AWE_function::get_current_date_format(), strtotime( $item['to'] ) );
	$info_price_day = AWE_function::get_pricing_of_days( AWE_function::convert_date_to_mdY( $from ), AWE_function::convert_date_to_mdY( $to ), $room_type_id );

	$year_from = date( 'Y', strtotime( AWE_function::convert_date_to_Ymd( $from ) ) );
	$year_to = date( 'Y',  strtotime( AWE_function::convert_date_to_Ymd( $from ) )  );

	foreach ( $info_price_day as $month => $list_day ) {
		foreach ( $list_day as $day => $price_day ) {
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

			?>
			<li style="color: #333333; font-size: 12px; overflow: hidden;">
				<span class="apb-room-seleted_date"><?php echo esc_html( date_i18n( AWE_function::get_current_date_format(), strtotime( $month . '/' . $day . '/' . $year ) ) ); ?></span>
				<span class="apb-amount" style="float: right; font-weight: bold; text-transform: uppercase;"><?php echo wp_kses_post( AWE_function::apb_price( $price_day ) ); ?></span>
			</li>
			<?php
		}
	}
	?>
</ul>

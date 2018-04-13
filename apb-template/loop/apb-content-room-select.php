<?php
/**
 * The template for displaying loop content for user select room.
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

printf(
	'<div data-id="%d" data-child="%d" data-adult="%d" class="%s apb-room-selected_item  box-cart-item-%s">',
	$item_cart["room_id"],
	$_POST['room_child'][ $t_i ]['child'],
	$_POST['room_adult'][ $t_i ]['adult'],
	'check-list-cart-js',
	$key_item
);

$package_price = 0;
if ( ! empty( $item_cart['package_data'] ) ) :
	foreach ( $item_cart['package_data'] as $info_package ) :
		$getPackage = AWE_function::get_room_option( $item_cart['room_id'], 'apb_room_type' );
		foreach ( $getPackage as $item_package ) {
			if ( $item_package->id == $info_package['package_id'] ) {
				$package_price += $info_package['total'] * $item_package->option_value;
			}
		}
	endforeach;
endif;

$info_price_day = AWE_function::get_pricing_of_days( $item_cart['from'], $item_cart['to'], $item_cart['room_id'], 1 );
$total_day_price = AWE_function::get_total_day_price( $info_price_day );
?>

	<h6><?php printf( esc_html__( 'Room %s', 'awebooking' ), absint( $t_i + 1 ) ); ?></h6>
	<div class="apb-room-seleted_name">
		<h2><?php echo esc_html( $room_type->post_title ); ?></h2>
		<span class="apb-amount">
			<?php echo AWE_function::apb_price( $item_cart['price'] ); ?>
		</span>
		<?php
		printf(
			'<input type="hidden" class="price-day-js" data-id="%d" value="%d">',
			$item_cart["room_id"],
			$item_cart["price"]
		);
		?>
	</div>

	<span>
		<?php
		printf( esc_html__( '%s Adult', 'awebooking' ), absint( $item_cart['adult'] ) );
		echo ', ';
		printf( esc_html__( '%s Child', 'awebooking' ), absint( $item_cart['child'] ) );
		?>
	</span>

	<?php
	/*----------  Display show only all room  ----------*/
	printf(
		'<a class="apb-room-seleted_change %s" data-num="%d" data-key="%s" data-id="%d" href="#">%s</a>',
		'change-item-cart-js',
		$t_i,
		$key_item,
		$item_cart['room_id'],
		esc_html__( 'Change Room', 'awebooking' )
	);
	?>
</div>

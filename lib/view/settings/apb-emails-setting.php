<?php
/**
 *  Tempalte setting calendar
 *
 * @version   1.0
 * @package   AweBooking/admin/
 * @author    AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="awe-tabs-sub" class="awe-sub-tab">
	<div class="awe-tabs-sub-wrap">
		<ul>
			<?php foreach ( $sub_tabs as $item_tab_sub ) : ?>
				<li>
					<a href="#<?php echo esc_attr( $item_tab_sub['tab_href'] ) ?>">
						<strong><?php echo esc_html( $item_tab_sub['tab_name'] ); ?></strong>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	foreach ( $sub_tabs as $item_tab_sub ) {
		echo $item_tab_sub['tab_content'];
	}
	?>
</div>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying loop pagination for check available.
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

?>
<nav class="apb-pagination-nav">
	<ul class="apb-pagination">
		<?php
		if ( $room->max_num_pages > 1 ) :
			$page = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
			for ( $i = 1; $i <= $room->max_num_pages; $i++ ) {
				?>
				<li <?php if ( $page == $i ) echo 'class="active"'; ?>>
					<?php
					printf(
						'<a data-page="%d" class="paged-room-js" href="#">%d</a>',
						absint( $i ),
						absint( $i )
					);
					?>
				</li>
				<?php
			}
		endif;
		?>
	</ul>
</nav>

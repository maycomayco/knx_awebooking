<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The template for displaying pagination of list room
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */

$big = 999999999; // need an unlikely integer

echo paginate_links( array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format' => '?paged=%#%',
    'current' => max( 1, get_query_var( 'paged' ) ),
    'total' => $room->max_num_pages,
) );

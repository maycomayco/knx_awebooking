<?php
/**
 * The template for displaying loop button book.
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
	'<a href="#" data-id="%d" class="apb-btn %s" title="%s">%s</a>',
	absint( get_the_ID() ),
	'apb-book-now-js',
	esc_attr( get_the_title() ),
	esc_html__( 'BOOK ROOM', 'awebooking' )
);

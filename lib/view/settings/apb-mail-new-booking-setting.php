<?php
/**
 *  Tempalte setting new booking mail.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config_mail = AWE_function::get_option( 'apb_mail_new_booking' );
$list_setting_email_checkout_admin = array(
	array(
		'class' => 'form-elements',
		'name' => __( 'Enable/Disable', 'awebooking' ),
		'type' => array(
			'type'	=> 'checkbox',
			'name' => 'apb_mail_new_booking[admin_notice_status]',
			'class' => '',
			'id' => '',
			'placeholder' => '',
			'value' => 1,
			'_value' =>  ! empty( $config_mail['admin_notice_status'] ) ? 1 : 0,
		),
		'desc' => 'Enable this email notification',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Recipient(s)', 'awebooking' ),
		'type'  => array(
			'type'	      => 'textarea',
			'name'        => 'apb_mail_new_booking[admin_mail_multi]',
			'cols'        => '60',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => isset( $config_mail['admin_mail_multi'] ) ? $config_mail['admin_mail_multi'] : get_option( 'admin_email' ),
		),
		'desc'  => 'Enter recipients (comma separated) for this email. Defaults to <code>'. $current_user->user_email.'</code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __('Subject', 'awebooking'),
		'type' => array('type'	=> 'text','name' => 'apb_mail_new_booking[subject]','class' => '','id' => '','placeholder' =>  'Awebooking Checkout order',
		'value' =>  isset($config_mail['subject']) ? $config_mail['subject'] : '[{site_title}] New customer booking ({order_number}) - {order_date}'),
		'desc' => 'This controls the email subject line. Leave blank to use the default subject: <code> [{site_title}] New customer booking ({order_number}) - {order_date} </code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __('Email Heading', 'awebooking'),
		'type' => array('type'	=> 'text','name' => 'apb_mail_new_booking[header]','class' => '','id' => '','placeholder' =>  'Awebooking Checkout order',
		'value' =>  isset($config_mail['header']) ? $config_mail['header'] : 'New customer booking' ),
		'desc' => 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>New customer booking</code>.',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Email Text', 'awebooking' ),
		'type'  => array(
			'type'	      => 'textarea',
			'name'        => 'apb_mail_new_booking[text]',
			'cols'        => '60',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => isset( $config_mail['text'] ) ? $config_mail['text'] : '',
		),
		'desc'	=> 'Leave blank or fill in some notices that you want to send to customer',
	),
);
?>
<h2><?php _e( 'New booking', 'awebooking' ); ?></h2>
<p><?php _e( 'New bookings emails are sent to the recipient list when bookings have been marked checkout','awebooking' ); ?>.</p>
<hr>

<h2><?php _e( 'Send mail to admin','awebooking' ); ?></h2>
<?php $this->apb_render_setting_html($list_setting_email_checkout_admin) ?>
<hr>

<div class="form-elements">
	<p><?php _e('HTML template', 'awebooking' ); ?></p>
	<?php
	if ( file_exists( get_stylesheet_directory() . '/apb-template/emails/apb-customer-new-booking.php' ) ) {
	?>
	<span class="description">
		<?php _e('To override and edit this email template copy', 'awebooking' ); ?>
		<?php _e('This template has been overridden by your theme and can be found in:','awebooking') ?> <code>yourtheme/apb-template/emails/apb-customer-new-booking.php</code>.
	</span>
	<a class="button" href="<?php echo esc_url( wp_nonce_url(add_query_arg( 'remove_template', 'apb-customer-new-booking' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php _e('Delete template file','awebooking' ); ?></a>
	<a class="button toggle_editor" href="#"><?php _e('View template','awebooking' ); ?></a>
	<div class="form-input editor">
		<textarea  cols="25" rows="20" data-name="template_html_code"><?php echo file_get_contents(AWE_function::template_exsits("emails/apb-customer-new-booking")) ?></textarea>
		<input type="hidden" name="template_html_file" value="apb-customer-new-booking">
	</div>
	<?php
	}else{
	?>
	<span class="description">
		<?php _e('To override and edit this email template copy', 'awebooking' ); ?>
		<code>awebooking/apb-template/emails/apb-customer-new-booking.php</code><?php _e('to your theme folder', 'awebooking' ); ?>: <code>yourtheme/apb-template/emails/apb-customer-new-booking.php</code>.
	</span>
	<a class="button" href="<?php echo esc_url( wp_nonce_url(add_query_arg( 'move_template', 'apb-customer-new-booking' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php _e('Copy file to theme','awebooking' ); ?></a>
	<a class="button toggle_editor" href="#"><?php _e('View template','awebooking' ); ?></a>
	<div class="form-input editor">
		<textarea readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( AWE_function::template_exsits( 'emails/apb-customer-new-booking' ) ) ?></textarea>
	</div>
	<?php
	}
	?>
</div>
<?php
AWE_function::apb_print_js( "
	var view = '" . esc_js( __( 'View template', 'awebooking' ) ) . "';
	var hide = '" . esc_js( __( 'Hide template', 'awebooking' ) ) . "';
	jQuery( 'a.toggle_editor' ).text( view ).toggle( function() {
		jQuery( this ).text( hide ).closest(' .form-elements' ).find( '.editor' ).slideToggle();
		return false;
	}, function() {
		jQuery( this ).text( view ).closest( '.form-elements' ).find( '.editor' ).slideToggle();
		return false;
	} );
	jQuery( '.editor textarea' ).change( function() {
		var name = jQuery( this ).attr( 'data-name' );

		if ( name ) {
			jQuery( this ).attr( 'name', name );
		}
	});
" )
?>

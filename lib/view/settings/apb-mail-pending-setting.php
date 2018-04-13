<?php
/**
 *  Tempalte setting mail option
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config_mail_pending = AWE_function::get_option( 'apb_mail_pending' );
$list_setting_email_checkout_user = array(
	array(
		'class'  => 'form-elements',
		'name'   => __( 'Enable/Disable', 'awebooking' ),
		'type'   => array(
			'type'	=> 'checkbox',
			'name' => 'apb_mail_pending[user_notice_status]',
			'class' => '',
			'id' => '',
			'placeholder' => '',
			'value' => 1,
			'_value' => ! empty( $config_mail_pending['user_notice_status'] ) ? 1 : 0,
		),
		'desc'   => 'Enable this email notification',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Subject', 'awebooking' ),
		'type' => array(
			'type'	=> 'text',
			'name' => 'apb_mail_pending[subject]',
			'class' => '',
			'id' => '',
			'placeholder' => 'Awebooking Checkout order',
			'value' => isset( $config_mail_pending['subject'] ) ? $config_mail_pending['subject'] : 'Your {site_title} booking receipt from {order_date}',
		),
		'desc' => 'This controls the email subject line. Leave blank to use the default subject: <code>Your {site_title} booking receipt from {order_date}</code>.',
	),
	array(
		'class' => 'form-elements',
		'name' => __('Email Heading', 'awebooking'),
		'type' => array('type'	=> 'text','name' => 'apb_mail_pending[header]','class' => '','id' => '','placeholder' =>  'Awebooking Checkout order',
		'value' =>  isset($config_mail_pending['header']) ? $config_mail_pending['header'] : 'Thank you for your booking' ),
		'desc' => 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>Thank you for your booking</code>.',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Email Text', 'awebooking' ),
		'type'  => array(
			'type'	      => 'textarea',
			'name'        => 'apb_mail_pending[text]',
			'cols'        => '60',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => isset( $config_mail_pending['text'] ) ? $config_mail_pending['text'] : '',
		),
		'desc'	=> 'Leave blank or fill in some notices that you want to send to customer',
	),
);
?>
<h2><?php _e( 'Pending booking', 'awebooking' ); ?></h2>
<p><?php _e( 'New bookings emails are sent to customer soon after the booking process is complete','awebooking' ); ?>.</p>
<hr>

<h2><?php _e( 'Send mail to user','awebooking' ); ?></h2>
<?php $this->apb_render_setting_html($list_setting_email_checkout_user) ?>

<div class="form-elements">
	<p><?php _e('HTML template', 'awebooking' ); ?></p>
	<?php
	if(file_exists(get_stylesheet_directory().'/apb-template/emails/apb-customer-pending-order.php')){
	?>
	<span class="description">
		<?php _e('To override and edit this email template copy', 'awebooking' ); ?>
		<?php _e('This template has been overridden by your theme and can be found in:','awebooking') ?> <code>yourtheme/apb-template/emails/apb-customer-pending-order.php</code>.
	</span>
	<a class="button" href="<?php echo esc_url( wp_nonce_url(add_query_arg( 'remove_template', 'apb-customer-pending-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php _e('Delete template file','awebooking' ); ?></a>
	<a class="button toggle_editor" href="#"><?php _e('View template','awebooking' ); ?></a>
	<div class="form-input editor">
		<textarea  cols="25" rows="20" data-name="template_html_code"><?php echo file_get_contents(AWE_function::template_exsits("emails/apb-customer-pending-order")) ?></textarea>
		<input type="hidden" name="template_html_file" value="apb-customer-pending-order">
	</div>
	<?php
	}else{
	?>
	<span class="description">
		<?php _e('To override and edit this email template copy', 'awebooking' ); ?>
		<code>awebooking/apb-template/emails/apb-customer-pending-order.php</code><?php _e('to your theme folder', 'awebooking' ); ?>: <code>yourtheme/apb-template/emails/apb-customer-pending-order.php</code>.
	</span>
	<a class="button" href="<?php echo esc_url( wp_nonce_url(add_query_arg( 'move_template', 'apb-customer-pending-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php _e('Copy file to theme','awebooking' ); ?></a>
	<a class="button toggle_editor" href="#"><?php _e('View template','awebooking' ); ?></a>
	<div class="form-input editor">
		<textarea readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents(AWE_function::template_exsits("emails/apb-customer-pending-order")) ?></textarea>
	</div>
	<?php
	}
	?>
</div>

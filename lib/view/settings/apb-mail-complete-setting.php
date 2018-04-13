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

$apb_mail_complete = AWE_function::get_option( 'apb_mail_complete' );
$list_setting_email_option = array(
	array(
		'class' => 'form-elements',
		'name' => __( 'Enable/Disable', 'awebooking' ),
		'type' => array(
			'type'	=> 'checkbox',
			'name' => 'apb_mail_complete[notice_status]',
			'class' => '',
			'id' => '',
			'placeholder' => '',
			'value' => '1',
			'_value' => ! empty( $apb_mail_complete['notice_status'] ) ? 1 : 0,
		),
		'desc' => 'Enable this email notification',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Subject', 'awebooking' ),
		'type' => array(
			'type'	=> 'text',
			'name' => 'apb_mail_complete[subject]',
			'class' => '',
			'id' => '',
			'placeholder' => 'Complete order',
			'value' => isset( $apb_mail_complete['subject'] ) ? $apb_mail_complete['subject'] : 'Your {site_title} booking from {order_date} is complete',
		),
		'desc' => 'This controls the email subject line. Leave blank to use the default subject: <code>Your {site_title} booking from {order_date} is complete</code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Email Heading', 'awebooking' ),
		'type' => array(
			'type'	=> 'text',
			'name' => 'apb_mail_complete[header]',
			'class' => '',
			'id' => '',
			'placeholder' => 'Complete order',
			'value' => isset( $apb_mail_complete['header'] ) ? $apb_mail_complete['header'] : 'Your booking is completed',
		),
		'desc' => 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>Your booking is completed</code>.',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Email Text', 'awebooking' ),
		'type'  => array(
			'type'	      => 'textarea',
			'name'        => 'apb_mail_complete[text]',
			'cols'        => '60',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => isset( $apb_mail_complete['text'] ) ? $apb_mail_complete['text'] : '',
		),
		'desc'	=> __( 'Leave blank or fill in some notices that you want to send to customer', 'awebooking' ),
	),
);
?>
<h2><?php esc_html_e( 'Completed booking', 'awebooking' ); ?></h2>
<p><?php esc_html_e( 'Booking complete emails are sent to customers when their bookings are marked completed and usually indicate that their orders have been shipped.', 'awebooking' ); ?></p>
<hr>

<?php
$this->apb_render_setting_html( $list_setting_email_option );
?>

<div class="form-elements">
	<p><?php esc_html_e( 'HTML template', 'awebooking' ); ?></p>
	<?php
	if ( file_exists( get_stylesheet_directory() . '/apb-template/emails/apb-customer-completed-order.php' ) ) {
		?>
		<span class="description">
			<?php esc_html_e( 'To override and edit this email template copy', 'awebooking' ); ?>
			<?php esc_html_e( 'This template has been overridden by your theme and can be found in:', 'awebooking' ) ?> <code>yourtheme/apb-template/emails/apb-customer-completed-order.php</code>.
		</span>
		<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'remove_template', 'apb-customer-completed-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php esc_html_e( 'Delete template file', 'awebooking' ); ?></a>
		<a class="button toggle_editor" href="#"><?php esc_html_e( 'View template', 'awebooking' ); ?></a>
		<div class="form-input editor">
			<textarea cols="25" rows="20" data-name="template_html_code"><?php echo esc_textarea( file_get_contents( AWE_function::template_exsits( 'emails/apb-customer-completed-order' ) ) ); ?></textarea>
			<input type="hidden" name="template_html_file" value="apb-customer-completed-order">
		</div>
		<?php
	} else {
		?>
		<span class="description">
			<?php
			printf( esc_html__( 'To override and edit this email template copy %s to your theme folder: %s.', 'awebooking' ),
				'<code>awebooking/apb-template/emails/apb-customer-completed-order.php</code>',
				'<code>yourtheme/apb-template/emails/apb-customer-completed-order.php</code>'
			);
			?>
		</span>
		<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'move_template', 'apb-customer-completed-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php esc_html_e( 'Copy file to theme', 'awebooking' ); ?></a>
		<a class="button toggle_editor" href="#"><?php esc_html_e( 'View template', 'awebooking' ); ?></a>
		<div class="form-input editor">
			<textarea readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo esc_textarea( file_get_contents( AWE_function::template_exsits( 'emails/apb-customer-completed-order' ) ) ); ?></textarea>
		</div>
	<?php } ?>
</div>

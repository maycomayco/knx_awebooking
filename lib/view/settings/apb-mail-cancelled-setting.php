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

$apb_mail_cancel = AWE_function::get_option( 'apb_mail_cancel' );
$list_setting_email_cancel_order = array(
	array(
		'class' => 'form-elements',
		'name'  => __( 'Enable/Disable', 'awebooking' ),
		'type'  => array(
			'type'        => 'checkbox',
			'name'        => 'apb_mail_cancel[notice_status]',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => '1',
			'_value'      => ! empty( $apb_mail_cancel['notice_status'] ) ? 1 : 0,
		),
		'desc'  => __( 'Enable this email notification', 'awebooking' ),
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Subject', 'awebooking' ),
		'type'  => array(
			'type'        => 'text',
			'name'        => 'apb_mail_cancel[subject]',
			'class'       => '',
			'id'          => '',
			'placeholder' => __( 'Cancelled order', 'awebooking' ),
			'value'       => isset( $apb_mail_cancel['subject'] ) ? $apb_mail_cancel['subject'] : '[{site_title}] Cancelled order ({order_number})',
		),
		'desc'  => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: %s', 'awebooking' ), '<code>[{site_title}] Cancelled order ({order_number})</code>' ),
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Email Heading', 'awebooking' ),
		'type'  => array(
			'type'	      => 'text',
			'name'        => 'apb_mail_cancel[header]',
			'class'       => '',
			'id'          => '',
			'placeholder' => __( 'Cancelled order', 'awebooking' ),
			'value'       => isset( $apb_mail_cancel['header'] ) ? $apb_mail_cancel['header'] : 'Cancelled booking',
		),
		'desc' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: %s', 'awebooking' ), '<code>Cancelled booking</code>' ),
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Email Text', 'awebooking' ),
		'type'  => array(
			'type'	      => 'textarea',
			'name'        => 'apb_mail_cancel[text]',
			'cols'        => '60',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => isset( $apb_mail_cancel['text'] ) ? $apb_mail_cancel['text'] : '',
		),
		'desc'	=> 'Leave blank or fill in some notices that you want to send to customer',
	),
);
?>
<h2><?php esc_html_e( 'Cancelled booking','awebooking' ); ?></h2>
<p><?php esc_html_e( 'Cancelled booking emails are sent to the recipient list when bookings have been marked cancelled (Delete)', 'awebooking' ); ?></p>
<?php
$this->apb_render_setting_html( $list_setting_email_cancel_order );
?>
<div class="form-elements">
	<p><?php esc_html_e( 'HTML template', 'awebooking' ); ?></p>
	<?php
	if ( file_exists( get_stylesheet_directory() . '/apb-template/emails/apb-customer-cancel-order.php' ) ) {
		?>
		<span class="description">
			<?php esc_html_e( 'To override and edit this email template copy', 'awebooking' ); ?>
			<?php esc_html_e( 'This template has been overridden by your theme and can be found in:', 'awebooking' ) ?> <code>yourtheme/apb-template/emails/apb-customer-cancel-order.php</code>.
		</span>
		<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'remove_template', 'apb-customer-cancel-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php esc_html_e( 'Delete template file', 'awebooking' ); ?></a>
		<a class="button toggle_editor" href="#"><?php esc_html_e( 'View template', 'awebooking' ); ?></a>
		<div class="form-input editor">
			<textarea cols="25" rows="20" data-name="template_html_code"><?php echo file_get_contents( AWE_function::template_exsits( 'emails/apb-customer-cancel-order' ) ); ?></textarea>
			<input type="hidden" name="template_html_file" value="apb-customer-cancel-order">
		</div>
	<?php } else { ?>
		<span class="description">
			<?php
			printf(
				esc_html__( 'To override and edit this email template copy %s to your theme folder: %s', 'awebooking' ),
				'<code>awebooking/apb-template/emails/apb-customer-cancel-order.php</code>',
				'<code>yourtheme/apb-template/emails/apb-customer-cancel-order.php</code>'
			);
			?>
		</span>
		<a class="button" href="<?php echo esc_url( wp_nonce_url(add_query_arg( 'move_template', 'apb-customer-cancel-order' ) , 'apb_email_template_nonce', '_apb_email_nonce' ) ); ?>"><?php _e('Copy file to theme','awebooking' ); ?></a>
		<a class="button toggle_editor" href="#"><?php esc_html_e( 'View template', 'awebooking' ); ?></a>
		<div class="form-input editor">
			<textarea readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( AWE_function::template_exsits( 'emails/apb-customer-cancel-order' ) ); ?></textarea>
		</div>
	<?php } ?>
</div>

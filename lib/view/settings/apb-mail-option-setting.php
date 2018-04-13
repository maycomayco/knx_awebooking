<?php
/**
 * Tempalte setting mail option.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$apb_mail_config = AWE_function::get_option( 'apb_mail_config' );
$list_setting_email_config = array(
	array(
		'class' => 'form-elements',
		'name'  => __( 'SMTP Host', 'awebooking' ),
		'type'  => array(
			'type'        => 'text',
			'name'        => 'apb_mail_config[host]',
			'class'       => '',
			'id'          => '',
			'placeholder' => 'smtp.gmail.com',
			'value'       => ! empty( $apb_mail_config['host'] ) ? $apb_mail_config['host'] : '',
		),
		'desc'  => '',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'SMTP Port', 'awebooking' ),
		'type'  => array(
			'type'	      => 'text',
			'name'        => 'apb_mail_config[port]',
			'class'       => '',
			'id'          => '',
			'placeholder' => '587',
			'value'       => ! empty( $apb_mail_config['port'] ) ? $apb_mail_config['port'] : '',
		),
		'desc'  => 'Port default: Port 465 (SSL required), Port 587 (TLS required)',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'SMTP Secure', 'awebooking' ),
		'type'  => array(
			'type'	      => 'text',
			'name'        => 'apb_mail_config[smtp_secure]',
			'class'       => '',
			'id'          => '',
			'placeholder' => 'tls',
			'value'       => ! empty( $apb_mail_config['smtp_secure'] ) ? $apb_mail_config['smtp_secure'] : '',
		),
		'desc'  => 'SMTP Secure default: SSL (Port 465 required), TLS (Port 587 required)',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Mail', 'awebooking' ),
		'type'  => array(
			'type'	      => 'text',
			'name'        => 'apb_mail_config[mail]',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => ! empty( $apb_mail_config['mail'] ) ? $apb_mail_config['mail'] : '',
		),
		'desc'  => '',
	),
	array(
		'class' => 'form-elements',
		'name'  => __( 'Password', 'awebooking' ),
		'type'  => array(
			'type'	      => 'password',
			'name'        => 'apb_mail_config[pass]',
			'class'       => '',
			'id'          => '',
			'placeholder' => '',
			'value'       => ! empty( $apb_mail_config['pass'] ) ? $apb_mail_config['pass'] : '',
		),
		'desc'  => '',
	),
);

?>
<h2><?php esc_html_e( 'Config to send email', 'awebooking' ); ?></h2>
<?php $this->apb_render_setting_html( $list_setting_email_config ); ?>

<?php
/**
 *  Tempalte setting mail template.
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$apb_mail_config = AWE_function::get_option( 'apb_mail_config' );
$list_setting_email_option = array(
	array(
		'class' => 'form-elements',
		'name' => __( 'From Email Address', 'awebooking' ),
		'type' => array(
			'type'	=> 'text',
			'name' => 'apb_email_from_name',
			'class' => '',
			'id'	=> '',
			'placeholder' => 'Awebooking notice',
			'value' => get_option( 'apb_email_from_name' ),
		),
		'desc' => '',
	),
	// array(
	// 	'class' => 'form-elements',
	// 	'name' => __( 'Email Header', 'awebooking' ),
	// 	'type' => array('type'	=> 'text','name' => 'apb_email_header','class' => '','id'	=> '','placeholder' =>  '','value' =>  get_option('apb_email_header')),
	// 	'desc' => '',
	// ),
	array(
		'class' => 'form-elements',
		'name' => __( 'Email Footer', 'awebooking' ),
		'type' => array(
			'type' => 'textarea',
			'name' => 'apb_email_footer',
			'cols' => '40',
			'id'	=> '',
			'placeholder' => '',
			'value' => AWE_function::get_option( 'apb_email_footer' ),
		),
		'desc' => '',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Base Colour', 'awebooking' ),
		'type' => array(
			'type'	=> 'text',
			'name' => 'apb_email_base_color',
			'class' => 'my-color-field',
			'id' => 'apb_email_base_color',
			'placeholder' => '',
			'value' => get_option( 'apb_email_base_color' ),
		),
		'desc' => __( 'The base colour for Awebooking email templates. Default', 'awebooking' ) . '&nbsp;<code>' . get_option( 'apb_email_base_color' ) . '</code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Background Colour', 'awebooking' ),
		'type' => array('type'	=> 'text','name' => 'apb_email_background_color','class' => 'my-color-field','id' => 'apb_email_background_color','placeholder' =>  '','value' =>  get_option('apb_email_background_color')),
		'desc' => __( 'The background colour for Awebookgin email templates. Default','awebooking').'&nbsp;<code>'.get_option('apb_email_background_color').'</code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Email Body Background Colour', 'awebooking' ),
		'type' => array('type'	=> 'text','name' => 'apb_email_body_background_color','class' => 'my-color-field','id' => 'apb_email_body_background_color','placeholder' =>  '','value' =>  get_option('apb_email_body_background_color')),
		'desc' => __( 'The main body background colour. Default','awebooking').'&nbsp;<code>'.get_option('apb_email_body_background_color').'</code>',
	),
	array(
		'class' => 'form-elements',
		'name' => __( 'Email Body Text Colour', 'awebooking' ),
		'type' => array('type'	=> 'text','name' => 'apb_email_text_color','class' => 'my-color-field','id' => 'apb_email_text_color','placeholder' =>  '','value' =>  get_option('apb_email_text_color')),
		'desc' => __( 'The main body text colour. Default','awebooking').'&nbsp;<code>'.get_option('apb_email_text_color').'</code>',
	),
);

?>
<h2>Setting mail template</h2>
<?php $this->apb_render_setting_html($list_setting_email_option); ?>
<div class="form-elements">
    <p><?php esc_html_e( 'Email Template', 'awebooking' ); ?> </p>
    <span class="description">
        This section lets you customise the Awebooking emails. <a target="_blank" href="<?php echo wp_nonce_url( admin_url( '?preview_awebooking_mail=true' ), 'preview-mail' ) ?>">Click here to preview your email template</a>. For more advanced control copy <code>awebooking/apb-template/emails/</code> to <code>yourtheme/apb-template/emails/</code>.
    </span>
</div>

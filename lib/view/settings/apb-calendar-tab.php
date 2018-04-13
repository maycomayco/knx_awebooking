<?php
// $datepicker_lang = AWE_function::get_datepicker_lang();

// if ( function_exists('icl_object_id') ) {
// 	if( 'all' == ICL_LANGUAGE_CODE ){
// 		$datepicker_lang = AWE_function::get_option( 'datepicker_lang' );		
// 	} else {
//     	$datepickerepicker_lang = AWE_function::get_option( 'datepicker_lang_'.ICL_LANGUAGE_CODE);
//     	$datepicker_lang = AWE_function::lang_code_wpml_to_datepicker(ICL_LANGUAGE_CODE);
// 	}
// } else {
//}
$datepicker_lang = AWE_function::get_option( 'datepicker_lang' );

?>
<div class="form-elements">
	<label for="datepicker_lang"><?php esc_html_e( 'DatePicker Language', 'awebooking' ); ?></label><br>
	<select name="datepicker_lang" id="datepicker_lang">
		<option value="default"><?php esc_html_e( 'English (United States)', 'awebooking' ); ?></option>
		<?php
		foreach ( AWE_function::apb_datepicker_lang() as $key => $value ) {
			?>
			<option <?php selected( $datepicker_lang, $key ); ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( ucfirst( $value ) ) ?></option>
			<?php
		}
		?>
	</select>

	<span class="description">
		<?php esc_html_e( 'Select language default.', 'awebooking' ); ?>
	</span>
</div>

<div class="form-elements">
	<label for="datepicker_num_calendar"><?php esc_html_e( 'Number calendar', 'awebooking' ); ?></label><br>
	<select name="datepicker_num_calendar" id="datepicker_num_calendar">
		<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
			<option <?php selected( $i, get_option( 'datepicker_num_calendar' ) ); ?> value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ); ?></option>
		<?php endfor; ?>
	</select>

	<span class="description">
		<?php esc_html_e( 'Number of calendar datepicker', 'awebooking' ); ?>
	</span>
</div>

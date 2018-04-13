<?php 

/**
*  Get list font icon
*/

class ApbFontIcon
{
	static public function getList(){
		$icons = array();
		if( function_exists( 'AWEI' ) ) {
			return AWEI()->icon->get_icons();
		}
	}
}
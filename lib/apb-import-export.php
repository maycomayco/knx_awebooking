<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE BackUp
 *
 * @class 		Apb_BackUp
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */

if ( ! function_exists( 'wp_handle_upload' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

Class Apb_BackUp extends AWE_function{
	/*===============================================
	=            AweBooking Export Data             =
	===============================================*/

	public function apb_write_dir(){
		if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData')){
			mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData', 0777, true);
		}
		if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/_thumbnail_room')){
			mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/_thumbnail_room', 0777, true);
		}
		if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_gallery')){
			mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_gallery', 0777, true);
		}
		if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/_thumbnail_room_intro')){
			mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/_thumbnail_room_intro', 0777, true);
		}
		if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_intro_gallery')){
			mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_intro_gallery', 0777, true);
		}
	}
	public function apb_export_rooms($rooms_data,$doc,$rooms){
		global $wpdb;
		/*---------- Export rooms data  ----------*/
		foreach ($rooms_data as $value_item) {
			$item_room = $doc->createElement( 'Item' );

			$post_title = $doc->createElement( 'post_title' );
			$post_title->nodeValue = $value_item->post_title;
			$item_room->appendChild( $post_title );

			$post_type = $doc->createElement( 'post_type' );
			$post_type->nodeValue = $value_item->post_type;
			$item_room->appendChild( $post_type );

			$post_author = $doc->createElement( 'post_author' );
			$post_author->nodeValue = $value_item->post_author;
			$item_room->appendChild( $post_author );

			$post_content = $doc->createElement( 'post_content' );
			$post_content->nodeValue = $value_item->post_content;
			$item_room->appendChild( $post_content );

			$post_name = $doc->createElement( 'post_name' );
			$post_name->nodeValue = $value_item->post_name;
			$item_room->appendChild( $post_name );

			$post_status = $doc->createElement( 'post_status' );
			$post_status->nodeValue = $value_item->post_status;
			$item_room->appendChild( $post_status );

			 /*---------- Export Package data ----------*/
			$room_package= $doc->createElement( 'package' );
			$item_room->appendChild( $room_package );

			$list_package = AWE_function::get_room_option($value_item->ID,'apb_room_type');
			foreach ($list_package as $item_package) {
				$item_package_element = $doc->createElement( 'Item' );

				$package_name = $doc->createElement( 'package_name' );
				$package_name->nodeValue = $item_package->option_name;
				$item_package_element->appendChild( $package_name );

				$package_price = $doc->createElement( 'package_price' );
				$package_price->nodeValue = $item_package->option_value;
				$item_package_element->appendChild( $package_price );

				$package_desc = $doc->createElement( 'package_desc' );
				$package_desc->nodeValue = $item_package->option_desc;
				$item_package_element->appendChild( $package_desc );

				$package_daily = $doc->createElement( 'package_daily' );
				$package_daily->nodeValue = $item_package->revision_id;
				$item_package_element->appendChild( $package_daily );

				$room_package->appendChild( $item_package_element );
			}

			/*---------- Export  Room calendar pricing data ----------*/
			$get_pricing = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apb_pricing WHERE unit_id = {$value_item->ID}" );
			$pricing_manage= $doc->createElement( 'pricing_manage' );
			$pricing_manage->nodeValue = serialize($get_pricing);
			$item_room->appendChild( $pricing_manage );

			/*---------- Export Room calendar available data ----------*/
			$get_availability = $wpdb->get_results("SELECT * From {$wpdb->prefix}apb_availability where unit_id = {$value_item->ID}");
			$availability_manage= $doc->createElement( 'availability' );
			$availability_manage->nodeValue = serialize($get_availability);
			$item_room->appendChild( $availability_manage );


			/*---------- Export Post meta  ----------*/

			$post_meta = $doc->createElement( 'post_meta' );
			$post_meta->nodeValue = $value_item->post_meta;
			$item_room->appendChild( $post_meta );

			$get_postmeta = $wpdb->get_results("SELECT meta_key,meta_value From {$wpdb->postmeta} where post_id = {$value_item->ID}");

			foreach ($get_postmeta as $item_postmeta) {
				if($item_postmeta->meta_key != "_thumbnail_id" && $item_postmeta->meta_key != "apb_gallery" && $item_postmeta->meta_key != "room_type_id"){
					$value_data = $doc->createElement( $item_postmeta->meta_key );
					$value_data->nodeValue = $item_postmeta->meta_value;
					$post_meta->appendChild( $value_data );
				}else{
					/*----------  Export thumbnail  ----------*/

					if ( '_thumbnail_id' == $item_postmeta->meta_key ) {
						$room_thumbnail_path = get_attached_file( $item_postmeta->meta_value );
			
						if ( ! @copy( $room_thumbnail_path, AWE_BK_PLUGIN_DIR . '/lib/xml-data/AwebookingData/_thumbnail_room/' . $value_item->post_name . '.jpg' ) ) {
							echo 'Can not copy file ' . $room_thumbnail_path;
							continue;
						}

						$value_data = $doc->createElement( $item_postmeta->meta_key );
						$value_data->nodeValue = '_thumbnail_room/' . $value_item->post_name . '.jpg';
						$post_meta->appendChild( $value_data );
					} elseif ( 'apb_gallery' == $item_postmeta->meta_key ) {
						/*----------  Export Gallery  ----------*/
						$apb_image_gallery = array();
						$get_gallery = !empty($item_postmeta->meta_value) ? unserialize($item_postmeta->meta_value) : array();
						$i_gallery = 0;
						foreach ($get_gallery as $image_id) {
							$i = $i_gallery++;
							if(!is_dir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_gallery/'.$value_item->post_name)){
								mkdir(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/room_gallery/'.$value_item->post_name);
							}

							$room_thumbnail_path = get_attached_file( $image_id );
				
							if ( ! @copy( $room_thumbnail_path, AWE_BK_PLUGIN_DIR . '/lib/xml-data/AwebookingData/room_gallery/' . $value_item->post_name . '/' . $value_item->post_name . '-' . $i . '.jpg' ) ) {
								echo 'Can not copy file ' . $room_thumbnail_path;
								continue;
							}

							$apb_image_gallery[] = 'room_gallery/' . $value_item->post_name . '/' . $value_item->post_name . '-' . $i . '.jpg';
						}

						$value_data = $doc->createElement( $item_postmeta->meta_key );
						$value_data->nodeValue = serialize( $apb_image_gallery );
						$post_meta->appendChild( $value_data );
					}
				}
			}
			$rooms->appendChild( $item_room );
		}
	}


	public function apb_export_bookings( $doc, $get_booking, $bookings ) {
		global $wpdb;
		/*----------  Export xml Booking ----------*/

		foreach ($get_booking as $value_booking) {
		  $item_booking = $doc->createElement( 'Item' );

		  $post_title = $doc->createElement( 'post_title' );
		  $post_title->nodeValue = "#".$value_booking->ID;
		  $item_booking->appendChild( $post_title );


		  $post_type = $doc->createElement( 'post_type' );
		  $post_type->nodeValue = $value_booking->post_type;
		  $item_booking->appendChild( $post_type );

		  $post_author = $doc->createElement( 'post_author' );
		  $post_author->nodeValue = $value_booking->post_author;
		  $item_booking->appendChild( $post_author );

		  $post_name = $doc->createElement( 'post_name' );
		  $post_name->nodeValue = $value_booking->post_name;
		  $item_booking->appendChild( $post_name );

		  $post_status = $doc->createElement( 'post_status' );
		  $post_status->nodeValue = $value_booking->post_status;
		  $item_booking->appendChild( $post_status );

		  /*----------  Created xml post meta  ----------*/
		  $post_meta = $doc->createElement( 'post_meta' );
		  $post_meta->nodeValue = $value_booking->post_meta;
		  $item_booking->appendChild( $post_meta );

		  $get_postmeta = $wpdb->get_results("SELECT meta_key,meta_value From {$wpdb->postmeta} where post_id = {$value_booking->ID}");

		  foreach ($get_postmeta as $item_postmeta) {
			if($item_postmeta->meta_key == "apb_data_order"){
				 $_apb_data_order = ($item_postmeta->meta_value != "") ? unserialize($item_postmeta->meta_value) : array();

				 $_get_all_room_booking = null;
				 foreach ($_apb_data_order as $value_date_order) {
					$ger_room_booking_info = get_post( $value_date_order['order_room_id']);
					 $value_date_order['order_room_slug'] =  $ger_room_booking_info->post_name;
					 $_get_all_room_booking[] =  $value_date_order;
				  }
				  update_post_meta($value_booking->ID,"apb_data_order",$_get_all_room_booking);
			}
			$value_data = $doc->createElement( $item_postmeta->meta_key );
			$value_data->nodeValue = $item_postmeta->meta_value;
			$post_meta->appendChild( $value_data );

		  }

		  /*----------  Created xml all room booking ----------*/
		  $room_for_booking = $doc->createElement( 'room_for_booking' );
		  $item_booking->appendChild( $room_for_booking );

		  $args_room_booking = array();
		  $room_ids  = get_post_meta($value_booking->ID,'apb_data_order',true);

		  foreach ($room_ids as $item_room_id) {
			$item_room_for_booking = $doc->createElement( 'Item' );
			  $_room_info = get_post($item_room_id['id']);

			  $post_title = $doc->createElement( 'post_title' );
			  $post_title->nodeValue = '#' . $_room_info->ID;
			  $item_room_for_booking->appendChild( $post_title );

			  $post_name = $doc->createElement( 'post_name' );
			  $post_name->nodeValue = 'apb-order-' . $_room_info->post_name;
			  $item_room_for_booking->appendChild( $post_name );

			  $post_type = $doc->createElement( 'post_type' );
			  $post_type->nodeValue = $_room_info->post_type;
			  $item_room_for_booking->appendChild( $post_type );

			  $post_author = $doc->createElement( 'post_author' );
			  $post_author->nodeValue = $_room_info->post_author;
			  $item_room_for_booking->appendChild( $post_author );


			  $post_status = $doc->createElement( 'post_status' );
			  $post_status->nodeValue = $_room_info->post_status;
			  $item_room_for_booking->appendChild( $post_status );

			  $get_postmeta = $wpdb->get_results("SELECT meta_key,meta_value From {$wpdb->postmeta} where post_id = {$_room_info->ID}");

			  /*----------  Created xml post meta  ----------*/
			  $post_meta = $doc->createElement( 'post_meta' );
			  $item_room_for_booking->appendChild( $post_meta );

			  foreach ($get_postmeta as $item_postmeta) {
				$value_data = $doc->createElement( $item_postmeta->meta_key );
				$value_data->nodeValue = $item_postmeta->meta_value;
				$post_meta->appendChild( $value_data );
				if( $item_postmeta->meta_key == "order_room_id"){
					$postmeta_get_room_info = get_post($item_postmeta->meta_value);
					$get_slug_order_room_id = $doc->createElement( "slug_order_room_id" );
					$get_slug_order_room_id->nodeValue = $postmeta_get_room_info->post_name;
					$post_meta->appendChild( $get_slug_order_room_id );
				}
			  }
			  $_room_id  = get_post_meta($_room_info->ID,"order_room_id",true);
			  $get_room_info = get_post($_room_id);

			  // /*----------  Get room slug  ----------*/
			  // $get_room_slug = $doc->createElement( "get_room_slug" );
			  // $get_room_slug->nodeValue = $get_room_info->post_name;
			  // $item_room_for_booking->appendChild( $get_room_slug );

			   $room_for_booking->appendChild( $item_room_for_booking );

		  }



		  $bookings->appendChild( $item_booking );
		}
	}
	public function apb_export_data(){
		global $wpdb;

		/*----------  Write dir  ----------*/
		$this->apb_write_dir();

		$get_room = get_posts( array(
			'post_type'      => 'apb_room_type',
			'posts_per_page' => -1,
		) );
		$get_booking = get_posts( array(
			'post_type'      => 'shop_order',
			'posts_per_page' => -1,
			'post_status'    => array( 'apb-pending', 'apb-completed', 'apb-cancelled' ),
		) );


		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->preserveWhiteSpace = FALSE;
		$doc->formatOutput = TRUE;

		/*----------  Created xml root  ----------*/
		$AwebookingData = $doc->createElement( 'AwebookingData' );
		$doc->appendChild( $AwebookingData );

		/*----------  Created xml Rooms  ----------*/
		$rooms = $doc->createElement( 'Rooms' );
		$AwebookingData->appendChild( $rooms );

		/*----------  Created xml Rooms  ----------*/
		$this->apb_export_rooms($get_room,$doc,$rooms);

		/*----------  Created xml Room Type  ----------*/
		/*$room_type = $doc->createElement( 'Room_type' );
		$AwebookingData->appendChild( $room_type );*/

		/*----------  Export xml Room Type  ----------*/
		// $this->apb_export_room_type($doc,$room_type);

		/*----------  Export xml Booking ----------*/
		/*$bookings = $doc->createElement( 'Bookings' );
		$AwebookingData->appendChild( $bookings );
		$this->apb_export_bookings($doc,$get_booking,$bookings);*/

		$doc->save(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData/awebooking.xml');

		 /*----------   Start the Export! ----------*/
		ini_set('max_execution_time', 600);
		ini_set('memory_limit','1024M');

		/*----------   Remove file export old ----------*/
		if(is_file(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData.zip')){
		 unlink(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData.zip');
		}
		/*----------   Zip Data ----------*/
		$this->zipData(AWE_BK_PLUGIN_DIR.'/lib/xml-data/', AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData.zip');
		$this->apb_remove_file(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData');

		/*----------   Send file zip! ----------*/
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename=AwebookingData.zip");
		header('Content-Length: ' . filesize(AWE_BK_PLUGIN_DIR.'/lib/xml-data/AwebookingData.zip'));
		header("Location: ".AWE_BK_BASE_URL_PLUGIN.'/lib/xml-data/AwebookingData.zip');
	}
	public function zipData($source, $destination) {
		if (extension_loaded('zip')) {
			if (file_exists($source)) {
				$zip = new ZipArchive();
				if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
					$source = realpath($source);
					if (is_dir($source)) {
						$iterator = new RecursiveDirectoryIterator($source);
						// skip dot files while iterating
						$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

						/*----------  Get all item of folder  ----------*/
						$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

						foreach ($files as $file) {
							$file = realpath($file);
							if (is_dir($file)) {

								$zip->addEmptyDir("AwebookingData");
							} else if (is_file($file)) {
							   $ex =  explode("xml-data", $file);

								$zip->addFromString($ex[1], file_get_contents($file));
							}
						}
					} else if (is_file($source)) {

						$zip->addFromString(basename($source), file_get_contents($source));
					}
				}
				return $zip->close();
			}
		}
		return false;
	}

	/*=====  End of AweBooking Export Data   ======*/


	public function apb_remove_file($path){
	   $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
	   foreach ($files as $file)
		{
			if (in_array($file->getBasename(), array('.', '..')) !== true)
			{
				if ($file->isDir() === true)
				{
					rmdir($file->getPathName());
				}

				else if (($file->isFile() === true) || ($file->isLink() === true))
				{
					unlink($file->getPathname());
				}
			}
		}
		rmdir($path);
	}

	  /*=========================================
	=            AweBooking Import            =
	=========================================*/

	public function Apb_upload_file_import($_file){

		/*----------  Remove file old  ----------*/
		$apb_dir = scandir( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' );
		unset( $apb_dir[0], $apb_dir[1] );
		foreach ( $apb_dir as $value ) {
			if ( is_dir( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' . $value ) ) {
				$this->apb_remove_file( AWE_BK_PLUGIN_DIR . '/lib/xml-data/AwebookingData' );
			} else {
				unlink( AWE_BK_PLUGIN_DIR . '/lib/xml-data/' . $value );
			}
		}

		$rootpath = AWE_BK_PLUGIN_DIR . '/lib/xml-data/';

		$valid_exts = array('zip');
		$result=array();

		if( ! empty($_file) ) {
			//upload multi file
			$count = count($_file['name']);

			// get uploaded file extension
			$ext = strtolower(pathinfo($_file['name'], PATHINFO_EXTENSION));

			// looking for format and size validity
			if (in_array($ext, $valid_exts)) {
				$path = $rootpath . uniqid(). '.' .$ext;
				// move uploaded file from temp to uploads directory
				if (move_uploaded_file($_file['tmp_name'], $path)) {
								$result['error']='no';
								$result['data']=$path;
				   return $path;
				}
			} else {
				$result['error']='Invalid file!';
			}

		} else {
			$result['error']= 'File not uploaded!';
		}

	}

	/*----------   Import rooms   ----------*/
	public function apb_import_rooms( $data ) {
		global $wpdb;
		_wp_relative_upload_path( '' );
		$wp_upload_dir = wp_upload_dir();
		$wp_upload_dir['url'];

		/*----------  Format table pricing and available  ----------*/

		$wpdb->get_results( "DELETE FROM {$wpdb->prefix}apb_availability" );
		$wpdb->get_results( "DELETE FROM {$wpdb->prefix}apb_pricing" );

		foreach ( $data->Rooms->Item as $item_room ) {

		$check_room_exists = get_posts( array(
			'name'        => $item_room->post_name,
			'post_type'   => 'apb_room_type',
			'post_status' => 'publish',
			'numberposts' => 1,
		) );
		if ( ! empty( $check_room_exists ) ) {
			foreach ( $check_room_exists as $item ) {
				wp_delete_post( $item->ID );
			}
		}
		$room_id = wp_insert_post( array(
			'post_title'    => ( string ) $item_room->post_title,
			'post_content'  => ( string ) $item_room->post_content,
			'post_status'   => ( string ) $item_room->post_status,
			'post_name'     => ( string ) $item_room->post_name,
			'post_type'     => ( string ) $item_room->post_type,
			'post_author'   => ( string ) $item_room->post_author,
		) );
		/*----------  Import post meta  ----------*/
		foreach ($item_room->post_meta[0] as $meta_key => $meta_value) {
			  if($meta_key != "apb_gallery" || $meta_key != "_thumbnail_id"){
				update_post_meta($room_id,(string)$meta_key,(string)$meta_value);
			  }
			  /*----------  import image thumbnail ----------*/
			if ( '_thumbnail_id' == $meta_key ) {

				  if(file_exists(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".(string)$meta_value)){
					  $_thumbnail = (string)$meta_value;
				  }else{
					  $_thumbnail = "awebooking.png";
				  }

				  $file_thumb_info = new SplFileInfo(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".$_thumbnail);
				  $file_thumb_size = getimagesize(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".$_thumbnail);
				  $thumb_name = $item_room->post_name."-id_".$room_id.".".$file_thumb_info->getExtension();
				  file_put_contents($wp_upload_dir['path']."/".$thumb_name, file_get_contents(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".$_thumbnail));
				  $thumb_id = $this->apb_upload_post_image($thumb_name,$file_thumb_info->getExtension(),$file_thumb_size);
				  update_post_meta($room_id,"_thumbnail_id",$thumb_id);
			} elseif ( 'apb_gallery' == $meta_key ) {
				/*----------  import image gallery ----------*/

				  $gallery_image_id = array();
				  $room_gallery = ($meta_value != "") ? unserialize($meta_value) : array();
				  $_i_gallery = 0;
				  foreach ($room_gallery as  $item_image) {
					$i = $_i_gallery++;

					if(file_exists(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".$item_image)){
					  $_item_image = (string)$item_image;
					}else{
					  $_item_image = "awebooking.png";
					}

					$file_gallery_info = new SplFileInfo(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".(string)$_item_image);
					$file_gallery_size = getimagesize(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".(string)$_item_image);
					$image_name = $item_room->post_name."-".$i."-id_".$room_id.".".$file_gallery_info->getExtension();
					file_put_contents($wp_upload_dir['path']."/".$image_name, file_get_contents(AWE_BK_PLUGIN_DIR."/lib/xml-data/AwebookingData/".$_item_image));
					$gallery_image_id[] = $this->apb_upload_post_image($image_name,$file_gallery_info->getExtension(),$file_gallery_size);
				  }

				  update_post_meta($room_id,"apb_gallery",$gallery_image_id);
			  }
			   if($meta_key == "extra_adult"){
				$extra_adult = ($meta_value != "") ? unserialize($meta_value) : array();
				update_post_meta($room_id,"extra_adult",$extra_adult );
			   }
			   if($meta_key == "extra_child"){
				$extra_child = ($meta_value != "") ? unserialize($meta_value) : array();
				update_post_meta($room_id,"extra_child",$extra_child);
			   }
			   if($meta_key == "extra_sale"){
				$extra_sale = ($meta_value != "") ? unserialize($meta_value) : array();
				update_post_meta($room_id,"extra_sale",$extra_sale);
			   }
			}


			/*----------  Import pricing manage  ----------*/

			foreach ($item_room->pricing_manage as $item_pricing) {
				$day_info = unserialize($item_pricing);
				if(!empty($day_info[0])){

					$pricing_manage =  array(
					   'unit_id'    => $room_id,
					   'year'  => $day_info[0]->year,
					   'month'   => $day_info[0]->month,
					);
					for ($i=1; $i < 32; $i++) {
					   $parram_day = "d".$i;
					   $pricing_manage["d".$i] =  $day_info[0]->$parram_day;
					}
					for ($i=1; $i < 33; $i++) {
					   $parram_format[] = '%s';
					}

					$wpdb->insert(
						$wpdb->prefix . 'apb_pricing',
						$pricing_manage,
						$parram_format
					 );
				}

			}

			/*----------  Import available manage  ----------*/

			foreach ( $item_room->availability as $item_availability ) {
				$day_info = unserialize( $item_availability );
				if ( ! empty( $day_info[0] ) ) {
					$pricing_manage =  array(
						'unit_id'    => $room_id,
						'year'  => $day_info[0]->year,
						'month'   => $day_info[0]->month,
					);
					for ( $i = 1; $i < 32; $i++ ) {
						$parram_day = 'd' . $i;
						$pricing_manage[ 'd' . $i ] = $day_info[0]->$parram_day;
					}
					for ( $i = 1; $i < 33; $i++ ) {
						$parram_format[] = '%s';
					}

					$wpdb->insert(
						$wpdb->prefix . 'apb_availability',
						$pricing_manage,
						$parram_format
					);
				}

			}

			/*----------  import package data ----------*/
			foreach ( $item_room->package->Item as $item_package ) {
				$wpdb->insert(
					$wpdb->prefix . 'apb_booking_options',
					array(
						'entity_type'       => 'apb_room_type',
						// 'object_name'       => $item->label,
						'object_id'         => $room_id,
						'option_name'       => $item_package->package_name,
						'option_operation'  => 'add',
						'option_value'      => $item_package->package_price,
						'option_desc'       => $item_package->package_desc,
						'revision_id'		=> $item_package->package_daily,
					),
					array( '%s', '%d', '%s', '%s', '%s', '%s', '%d' )
				);
			}
			AWE_function::bulk_create_rooms( $room_id, get_post_meta( $room_id, 'number_of_rooms', true ) );

		}

	}

	public function apb_import_bookings( $data ) {
		$items = $data->Bookings->Item;
		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->apb_import_booking( $item );
			}
		} else {
			$this->apb_import_booking( $items );
		}
	}

	public function apb_import_booking( $item ) {
		$post_data = array(
			'post_type'		=> $item->post_type,
			'post_author'	=> $item->post_author,
			'post_name'		=> $item->post_name,
			'post_status'	=> $item->post_status,
		);
		if ( isset( $item->post_title ) ) {
			$post_data['post_title'] = $item->post_title;
		}

		$booking_id = wp_insert_post( $post_data );

		foreach ( get_object_vars( $item->post_meta ) as $k => $v ) {
			update_post_meta( $booking_id, $k, $v );
		}

		if ( isset( $item->room_for_booking ) ) {
			$this->apb_import_booking( $item->room_for_booking->Item );
		}
	}

	public function apb_import_bookings_old( $data ) {
		var_dump($data->Bookings);
		 global $wpdb;

		 foreach ($data->Bookings->Item as $item_booking) {
			$wpdb->insert(
				$wpdb->posts,
				array(
				   'post_title'    => (string)$item_booking->post_title,
				   'post_status'   => (string)$item_booking->post_status,
				   'post_name'     => (string)$item_booking->post_name,
				   'post_type'     => (string)$item_booking->post_type,
				   'post_author'   => (string)$item_booking->post_author,
				  ),
				array('%s','%s','%s','%s','%s')
			 );
			$booking_id = $wpdb->insert_id;
			 /*----------  Import post meta  ----------*/
			  $_meta_order_room_id = null;
			  foreach ($item_booking->post_meta as $meta_key => $meta_value) {
				update_post_meta($booking_id,(string)$meta_key,(string)$meta_value);
				  if($meta_key == "apb_data_order"){

					$_meta_order_room_id = ($meta_value != "") ? unserialize( $meta_value) : array();

					$_meta_order_room_update = array();
					 foreach ($_meta_order_room_id as $item_value) {
						  $args_meta_apb_order = array(
							  'name'        => $item_value['order_room_slug'],
							  'post_type'   => 'apb_room_type',
							  'post_status' => 'publish',
							  'numberposts' => 1
							);
						  $_meta_room_info = get_posts($args_meta_apb_order);
						  $item_value['order_room_id'] = $_meta_room_info[0]->ID;
						  $_meta_order_room_update[] = $item_value;
					 }
				  }
				   if($meta_key == "info_custom_order"){
					$info_custom_order[] = ($meta_value != "") ? unserialize( $meta_value) : array();
						update_post_meta( $booking_id,"info_custom_order",$info_custom_order);
					}
			  }

			  foreach ($item_booking->room_for_booking->Item as $item_apb_order) {

				 $wpdb->insert(
					$wpdb->posts,
					array(
					   'post_status'   => (string)$item_apb_order->post_status,
					   'post_name'     => (string)$item_apb_order->post_name,
					   'post_type'     => (string)$item_apb_order->post_type,
					   'post_author'   => (string)$item_apb_order->post_author,
					  ),
					array('%s','%s','%s','%s','%s')
				 );
				$apb_order_id = $wpdb->insert_id;
				$get_apb_order_id[] = $apb_order_id;
				  foreach ($item_apb_order->post_meta[0] as $meta_key => $meta_value) {
					update_post_meta($apb_order_id,(string)$meta_key,(string)$meta_value);
					if($meta_key == "order_room_id"){
						$args_apb_order = array(
						  'name'        => $item_apb_order->get_room_slug,
						  'post_type'   => 'apb_order',
						  'post_status' => 'publish',
						  'numberposts' => 1
						);
						 get_posts($args_apb_order);
					}

					/**
					 * Add room to pos type apb_order
					 */

					if($meta_key == "order_room_id"){
						$_meta_apb_orde_room_update = false;
						foreach ($_meta_order_room_update as $item_value) {
							  $item_value['id'] = $apb_order_id;
							  $_meta_apb_orde_room_update[] = $item_value;
						 }

						update_post_meta($booking_id,'apb_data_order', $_meta_apb_orde_room_update);
						// update_post_meta($apb_order_id,'order_room_id',$_room_info[0]->ID);
					}

					/**
					 * Get id of post room by slug room
					 */
					if($meta_key == "slug_order_room_id"){
						 $_room_info = get_posts(array(
									  'name'        => $meta_value,
									  'post_type'   => 'apb_room_type',
									  'post_status' => 'publish',
									  'numberposts' => 1
									));
						 update_post_meta($apb_order_id,'order_room_id',$_room_info[0]->ID);

					}
					if($meta_key == "posttype_shop_order_id"){
						update_post_meta($apb_order_id,"posttype_shop_order_id",$booking_id);
					 }
				  }

			  }

		 }

	}


	/*----------  Upload image and insert post image  ----------*/
	public function apb_upload_post_image($filename,$file_type,$file_size){
		global $wpdb;
		$wp_upload_dir = wp_upload_dir();

		$attr_id = wp_insert_attachment( array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => 'image/'.$file_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_date'      => current_time( 'mysql' ),
			'post_date_gmt'  => current_time( 'mysql', 1 ),
		) );
		update_post_meta( $attr_id, '_wp_attached_file', ltrim( $wp_upload_dir['subdir'] . '/' . $filename, '/' ) );

		$info_image = array(
			  "width"  => $file_size[0],
			  "height" => $file_size[1],
			  "file"  => $wp_upload_dir['subdir']."/".$filename,
			  "sizes" => array(
					"thumbnail" => array(
						  "file" =>   $filename,
						  "width" => 150,
						  "height"=> 150,
						  "mime-type" => $file_size['mime']
					 ),
					"medium"=> array(
						  "file"=> $filename,
						  "width"=> 300,
						  "height"=>203,
						  "mime-type"=> $file_size['mime']
					),
					"post-thumbnail" => array(
						  "file"=> $filename,
						  "width"=> 805,
						  "height"=> 510,
						  "mime-type"=> $file_size['mime']
						)
			  ),
			  "image_meta"=> array(
						"aperture"=>0,
						"credit"=> "",
						"camera"=> "",
						"caption"=> "",
						"created_timestamp"=> 0,
						"copyright"=> "",
						"focal_length"=>0,
						"iso"=> 0,
						"shutter_speed"=>0,
						"title"=>  "",
						"orientation"=> 0,
					  )
			);
		update_post_meta($attr_id,'_wp_attachment_metadata',$info_image);
		return $attr_id;

	}

}

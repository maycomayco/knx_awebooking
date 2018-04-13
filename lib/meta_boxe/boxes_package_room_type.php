<?php

/**
 * Description of boxes_package_room_type
 *
 * @author Letrungha
 */
class boxes_package_room_type extends AWE_function {

	public static function output( $post ) {
		$term 					= wp_get_object_terms( $post->ID, 'product_type' );
		$number_of_rooms        = get_post_meta( $post->ID, 'number_of_rooms', true );
		$room_name 				= AWE_function::get_room_name_for_meta_value( $post->ID );
		$base_price             = get_post_meta( $post->ID, 'base_price', true );
		$base_price_for         = get_post_meta( $post->ID, 'base_price_for', true ) ? get_post_meta( $post->ID, 'base_price_for', true ) : 1;
		$room_min_sleeps        = get_post_meta( $post->ID, 'min_sleeps', true );
		$room_max_sleeps        = get_post_meta( $post->ID, 'max_sleeps', true );
		$room_min_children      = get_post_meta( $post->ID, 'min_children', true );
		$room_max_children      = get_post_meta( $post->ID, 'max_children', true );
		$room_min_night         = get_post_meta( $post->ID, 'min_night', true );

		$room_desc      = get_post_meta( $post->ID, 'room_desc', true );
		$extra_adult    = get_post_meta( $post->ID, 'extra_adult', true );
		$extra_child    = get_post_meta( $post->ID, 'extra_child', true );
		$extra_sale     = get_post_meta( $post->ID, 'extra_sale', true );
		$iconList		= ApbFontIcon::getList();
		?>

		<div class="awe-plugin" id="awe_write_panel_tabs">
			<div class="panel-wrap product_data awe-panel-with-table">
				<ul class="product_data_tabs awe-tabs">
					<li>
						<a href="#general_product_data"><?php esc_html_e( 'General', 'awebooking' ); ?></a>
					</li>
					<li class="box_customize">
						<a href="#sale_price_total_day"><?php esc_html_e( 'Discount', 'awebooking' ); ?></a>
					</li>
					<li class="box_customize">
						<a href="#room_package"><?php esc_html_e( 'Package', 'awebooking' ); ?></a>
					</li>
					<li class="box_customize">
						<a href="#extra_price"><?php esc_html_e( 'Extra Price', 'awebooking' ); ?></a>
					</li>
					<li>
						<a href="#desc_room"><?php esc_html_e( 'Short Description', 'awebooking' ); ?></a>
					</li>

					<?php
					/**
					 * Allow add new tab title for room type meta box.
					 */
					do_action( 'awebooking_room_type_meta_box_tab_title', $post );
					?>
				</ul>

				<div id="general_product_data" class="panel awe_options_panel">
					<div class="options_group ">
						<p class="awe-form-inline box_customize">
							<label><?php esc_html_e( 'Number of rooms', 'awebooking' ); ?> *</label>
							<input name="number_of_rooms" type="text" class="form-text" size="5" value="<?php echo esc_attr( $number_of_rooms ); ?>">
							<?php AWE_function::awe_help( __( 'Number of rooms. If changing it, all room of this room type will be removed', 'awebooking' ) ); ?>
						</p>

						<p class="awe-form-inline box_customize">
							<label style="vertical-align: top"><?php esc_html_e( 'Room name', 'awebooking' ); ?> *</label>
							<textarea name="room_name" cols="40" rows="5"><?php echo esc_attr( $room_name ); ?></textarea>
							<?php AWE_function::awe_help( __( 'Fill room name, separate by comma', 'awebooking' ) ); ?>
						</p>

						<p class="awe-form-inline box_customize">
							<label><?php esc_html_e( 'Base price', 'awebooking' ); ?> *</label>
							<input name="base_price" type="text" class="form-text" size="5" value="<?php echo esc_attr( $base_price ); ?>">
							<?php
							printf(
								esc_html__( 'for %s guest(s)', 'awebooking' ),
								'<input type="number" name="base_price_for" value="' . absint( $base_price_for ) . '">'
							);
							?>
							<?php // AWE_function::awe_help( 'Default price of room.' ) ?>
						</p>

						<p class="awe-form-inline box_customize">
							<label><?php esc_html_e( 'Sleeping capacity', 'awebooking' ) ?> *</label>
							<input name="min_sleeps" type="text" class="form-text apb-int" placeholder="Minimum" size="5" value="<?php echo esc_attr( $room_min_sleeps ); ?>">
							<input name="max_sleeps" type="text" class="form-text apb-int" placeholder="Maximum" size="5" value="<?php echo esc_attr( $room_max_sleeps ); ?>">
							<?php AWE_function::awe_help( __( 'Minimum - Maximum guests (included adult & children) allowed.', 'awebooking' ) ) ?>
						</p>

						<p class="awe-form-inline box_customize">
							<label><?php esc_html_e( 'Child capacity', 'awebooking' ); ?></label>
							<input type="text" class="form-text apb-int" name="min_children" placeholder="Minimum" size="5" value="<?php echo esc_attr( $room_min_children ); ?>">
							<input type="text" class="form-text apb-int" name="max_children" placeholder="Maximum" size="5" value="<?php echo esc_attr( $room_max_children ); ?>">
							<?php AWE_function::awe_help( __( 'Minimum - Maximum children allowed.', 'awebooking' ) ) ?>
						</p>

						<p class="awe-form-inline box_customize">
							<label for="edit-label"><?php esc_html_e( 'Minimum Night', 'awebooking' ) ?></label>
							<input type="text" class="form-text apb-int" placeholder="Minimum night" size="5" name="min_night" value="<?php echo esc_attr( $room_min_night ); ?>">
							<?php AWE_function::awe_help( __( 'Minimum night number.', 'awebooking' ) ) ?>
						</p>
					</div>
				</div>

				<div id="extra_price" class="panel awe_options_panel">
					<div class="options_group ">
						<div class="form-field">
							<table class="field-multiple-table awe-add-option-table">
								<thead>
									<tr>
										<th></th>
										<th class="field-label"><label><?php esc_html_e( 'For Adults', 'awebooking' ) ?> </label></th>
										<th></th>
									</tr>
									<tr>
										<th class="field-label"><label><?php esc_html_e( 'Amount', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Extra Price', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Action', 'awebooking' ) ?> </label></th>
									</tr>
								</thead>

								<tbody class="form-extra-price-adult-js">
									<?php
									if ( is_array( $extra_adult ) ) :
										foreach ( $extra_adult as $item_extra_adult ) :
										?>
										<tr  class="item-extra-price">
											<td><input type="text" class="apb-int"value="<?php echo esc_attr( $item_extra_adult['number'] ); ?>" name="extra_adult[number][]"></td>
											<td><input type="text" value="<?php echo esc_attr( $item_extra_adult['price'] ); ?>" name="extra_adult[price][]"></td>
											<td><button type="button" class="button remove-extra-price-js"><?php esc_html_e( 'Remove', 'awebooking' ) ?></button></td>
										</tr>
										<?php
										endforeach;
									endif;
									?>
								</tbody>

								<tfoot>
									<tr>
										<th colspan="2">
											<button type="button" class="button add_extrad_price_adult_js"><?php esc_html_e( 'Add', 'awebooking' ) ?></button>
											<span class="spinner add_extra_price" style="display: none;"></span>
										</th>
									</tr>
								</tfoot>
							</table>

							<table class="field-multiple-table awe-add-option-table">
								<thead>
									<tr>
										<th></th>
										<th class="field-label"><label><?php esc_html_e( 'For Children', 'awebooking' ) ?> </label></th>
										<th></th>
									</tr>
									<tr>
										<th class="field-label"><label><?php esc_html_e( 'Amount', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Extra Price', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Action', 'awebooking' ) ?> </label></th>
									</tr>
								</thead>

								<tbody class="form-extra-price-child-js">
									<?php
									if ( is_array( $extra_child ) ) :
										foreach ( $extra_child as $item_extra_child ) :
										?>
										<tr class="item-extra-price">
											<td><input type="text" class="apb-int" value="<?php echo esc_attr( $item_extra_child['number'] ); ?>" name="extra_child[number][]"></td>
											<td><input type="text" value="<?php echo esc_attr( $item_extra_child['price'] ); ?>" name="extra_child[price][]"></td>
											<td><button type="button" class="button remove-extra-price-js"><?php esc_html_e( 'Remove', 'awebooking' ) ?></button></td>
										</tr>
										<?php
										endforeach;
									endif;
									?>
								</tbody>

								<tfoot>
									<tr>
										<th colspan="2">
											<button type="button" class="button add_extrad_price_child_js"><?php esc_html_e( 'Add', 'awebooking' ) ?></button>
											<span class="spinner add_extra_price" style="display: none;"></span>
										</th>
									</tr>
								</tfoot>
						  </table>
						</div>
					</div>
				</div>

				<div id="sale_price_total_day" class="panel awe_options_panel">
					<div class="options_group ">
						<div class="form-field">
							<table class="field-multiple-table awe-add-option-table">
								<thead>
									<tr>
										<th colspan="5" class="field-label"><label>&nbsp;</label></th>
									</tr>
									<tr>
										<th class="field-label"><label><?php esc_html_e( 'Type', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Unit', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Sale type', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Amount', 'awebooking' ) ?> </label></th>
										<th class="field-label"><label><?php esc_html_e( 'Action', 'awebooking' ) ?> </label></th>
									</tr>
								</thead>
								<tbody class="form-extra-sale-js">
									<tr id="item-extra-sale-sample" style="display: none;">
										<td class="apb-select-sale-type">
											<select class="sale-date-field" data-key="date" readonly>
												<option value="Before-Day"><?php esc_html_e( 'Before day', 'awebooking' ); ?></option>
												<option value="Day"><?php esc_html_e( 'Day', 'awebooking' ); ?></option>
												<option value="Week"><?php esc_html_e( 'Week', 'awebooking' ); ?></option>
												<option value="Month"><?php esc_html_e( 'Month', 'awebooking' ); ?></option>
											</select>
										</td>

										<td><input type="text" class="sale-date-field" data-key="total"></td>

										<td class="apb-select-sale-type">
											<select class="apb-input-type-sale-js" class="sale-date-field" data-key="type" readonly>
												<option value="sub"><?php esc_html_e( 'Subtract from price', 'awebooking' ); ?></option>
												<option value="decrease"><?php esc_html_e( 'Decrease price by %', 'awebooking' ); ?></option>
											</select>
										</td>
										<td><input type="text" class="sale-date-field" data-key="price"></td>
										<td><button class="button remove-extra-sale-js" type="button"><?php esc_html_e( 'Remove', 'awebooking' ) ?></button></td>
									</tr>
									<?php
									if ( is_array( $extra_sale ) ) :
										$i = 0;
										foreach ( $extra_sale as $item_extra_sale ) :
											$_i = $i++;
											?>
											<tr class="item-extra-sale">
												<td class="apb-select-sale-type">
													<select class="input-sale-hidden-<?php echo absint( $_i ); ?>" data-int="<?php echo absint( $_i ) ?>" name="sale_date[date][]" readonly>
														<option value="Before-Day" <?php selected( 'Before-Day', $item_extra_sale['type_duration'] ) ?>><?php esc_html_e( 'Before day', 'awebooking' ); ?></option>
														<option value="Day" <?php selected( 'Day', $item_extra_sale['type_duration'] ) ?>><?php esc_html_e( 'Day', 'awebooking' ); ?></option>
														<option value="Week" <?php selected( 'Week', $item_extra_sale['type_duration'] ) ?>><?php esc_html_e( 'Week', 'awebooking' ); ?></option>
														<option value="Month" <?php selected( 'Month', $item_extra_sale['type_duration'] ) ?>><?php esc_html_e( 'Month', 'awebooking' ); ?></option>
													</select>
												</td>

												<td><input type="text" name="sale_date[total][]" value="<?php echo esc_attr( $item_extra_sale['total'] ) ?>"></td>

												<td class="apb-select-sale-type">
													<?php $list_type_sale = array( 'replace' => 'Replace price', 'sub' => 'Subtract from price', 'decrease' => 'Decrease price by %' ); ?>

													<select class="apb-input-type-sale-js input-sale-<?php echo absint( $_i ); ?>" data-int="<?php echo absint( $_i ) ?>" name="sale_date[type][]" readonly>
														<option value="sub" <?php selected( 'sub', $item_extra_sale['sale_type'] ) ?>><?php esc_html_e( 'Subtract from price', 'awebooking' ); ?></option>
														<option value="decrease" <?php selected( 'decrease', $item_extra_sale['sale_type'] ) ?>><?php esc_html_e( 'Decrease price by %', 'awebooking' ); ?></option>
													</select>
												</td>
												<td><input type="text" name="sale_date[price][]" value="<?php echo esc_attr( $item_extra_sale['amount'] ) ?>"></td>
												<td><button class="button remove-extra-sale-js" type="button"><?php esc_html_e( 'Remove', 'awebooking' ) ?></button></td>
											</tr>
											<?php
										endforeach;
									endif;
									?>
								</tbody>

								<tfoot>
									<tr>
										<th colspan="5">
											<button type="button" class="button add_sale_js"><?php esc_html_e( 'Add', 'awebooking' ) ?></button>
										</th>
									</tr>
								</tfoot>
						  </table>
						</div>
					</div>
				</div>

				<div id="room_package" class="panel awe_options_panel">
					
					<div class="options_group ">
						<div class="form-field">
							 <table class="field-multiple-table awe-add-option-table">
								<thead>
									<tr>
										<th class="field-label" colspan="2"><label><?php esc_html_e( 'Options', 'awebooking' ) ?> </label></th>
									</tr>
								</thead>
								<tbody class="form-option-js">
									<?php
									$data_option = AWE_function::get_room_option( $post->ID, 'apb_room_type' );

									if ( ! empty( $data_option) ) {
										foreach ( $data_option as $item_option ) {
											?>
											<tr class="draggable odd item-option op-<?php echo $item_option->id ?>">
												<td>
													<div class="form-item form-type-textfield apb-form-type-icon">

														<div class="form-elements">
															<label>&nbsp; </label>
															<input type="hidden" class="apb-input-icon-<?php echo $item_option->id ?>" value="<?php echo $item_option->option_icon ?>" name="option_icon[]">
															<button type="button" data-id="<?php echo $item_option->id ?>" class="apb-js-chose-icon button apb-icon-<?php echo $item_option->id ?>"><?php echo (!empty( $item_option->option_icon ) && !empty($iconList)) ? '<i class="' . $item_option->option_icon . '"></i>' : esc_html__( 'Icon', 'awebooking' ) ?></button>
														</div>

													</div>
													<div class="form-item form-type-textfield">
														<div class="form-elements">
															<label><?php esc_html_e( 'Name', 'awebooking' ) ?> </label>
															<input type="text" size="15" value="<?php echo $item_option->option_name ?>" name="option_name[]">
														</div>
													</div>
													<div class="form-item form-type-textfield text-desc">
														<div class="form-elements">
															<label><?php esc_html_e( 'Description', 'awebooking' ) ?> </label>
															<textarea name="option_desc[]"><?php echo esc_html( $item_option->option_desc ); ?></textarea>
														</div>
													</div>
													<input type="hidden" name="option_operation[]" value="add">

													<div class="form-item form-type-textfield">
														<div class="form-elements">
															<label><?php esc_html_e( 'Price', 'awebooking' ) ?> </label>
															<input name="option_value[]" type="text" size="10" value="<?php echo esc_attr( $item_option->option_value) ?>">
															<div class="awe-set-type-package">
																<input class="apb-option-type-js" data-id="<?php echo esc_attr( $item_option->id) ?>" type="checkbox" <?php checked( $item_option->revision_id,1 ); ?> value="1">
																<input type="hidden" id="apb-option-type-<?php echo esc_attr( $item_option->id) ?>" name="type_package[]" value="<?php echo esc_attr( $item_option->revision_id) ?>">
																<span><?php esc_html_e( 'Daily package', 'awebooking' ) ?> </span>
															</div>
														</div>
													</div>
													
													<div class="form-item form-type-textfield">
														<div class="form-elements">
															<label>&nbsp; </label>
															<input type="hidden" name="option_id[]" value="<?php echo $item_option->id ?>">
															<button type="button" data-id="<?php echo $item_option->id ?>" class="remove-option-js button"><?php esc_html_e( 'Remove', 'awebooking' ) ?></button>
														</div>
													</div>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2">
											<button type="button" class="awe-add-option-js button"><?php esc_html_e( 'Add another item', 'awebooking' ) ?></button>
											<span class="spinner" style="display: none;"></span>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					
				</div>
				<div class="apb-package-icon media-modal wp-core-ui">
					<button type="button" class="button-link media-modal-close apb-js-modal-close"><span class="media-modal-icon"><span class="screen-reader-text"><?php _e( 'Close media panel', 'awebooking' ); ?></span></span></button>	
					<div class="media-modal-content">
						<?php if( function_exists( 'AWEI' ) ) : ?>
						<div class="media-frame mode-select">
							<div class="media-frame-menu">
								<div class="media-menu">
									<?php 
										foreach ($iconList as $keyIcon => $valueIcon) {
											echo '<a href="#apb-tab-icon-' . esc_attr( $valueIcon->name ) . '" class="media-menu-item">' . $valueIcon->label . '</a>';
										}
									?>
									
								</div>
							</div>
						</div>

						<div class="media-frame-title">
							<h1><?php _e( 'List Icons', 'awebooking' ); ?></h1>		
						</div>

						<div class="media-frame-content">
							<?php echo (empty($iconList)) ? '&nbsp;<h3>' . __( 'You have not upload icon.', 'awebooking.' ) . '<a target="_blank" href="' . admin_url( 'admin.php?page=ac-icon-manager' ) . '"> » Upload icon.</a> </h3>' : '';  ?>
							<?php foreach ($iconList as $keyIcon => $valueIcon): ?>
							<link rel="stylesheet" type="text/css" href="<?php echo esc_attr( $valueIcon->style ) ?>">
							<div id="apb-tab-icon-<?php echo esc_attr( $valueIcon->name ) ?>" class="apb-tab-icon-item">
								<ul class="apb-package-list-icon apb-js-package-icon">
									<li data-icon="none"><?php _e( 'None', 'awebooking' ); ?></li>
									<?php 
										foreach ($valueIcon->icons as $kIcon => $valIcon) {
											echo '<li data-icon="' . $valIcon['classes'] . '"><i class="' . $valIcon['classes'] . '"></i></li>';
										}
									?>
								</ul>
							</div>
							<?php endforeach; ?>
						</div>
						<?php 
							else:
							  echo '&nbsp;<h3>' . __( 'You have to enable aweicon plugin before can use this feature.', 'awebooking.' ) . '</h3>';
							endif;?>
					</div>
				</div>
				
				<div id="desc_room" class="panel awe_options_panel">
					<div class="options_group ">
						<div class="awe-form-inline">
						<label>&nbsp;</label>
							<?php
							wp_editor(
								$room_desc, 'room_desc',
								array(
									'media_buttons' => false,
									'teeny' => true,
									'textarea_rows' => '10',
								)
							);
							?>
						</div>
					</div>
				</div>

				<?php
				/**
				 * Allow add new tab content for room type meta box.
				 */
				do_action( 'awebooking_room_type_meta_box_tab_content', $post );
				?>
			</div>

		</div>
		<?php
	}


	public static function save( $post_id ) {
		global $typenow, $wpdb;
		remove_action( 'save_post', 'boxes_package_room_type::save' );

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && 'apb_room_type' == $typenow ) {
			if ( isset( $_POST['room_desc'] ) ) {
				update_post_meta( $post_id, 'room_desc', wp_kses_post( $_POST['room_desc'] ) );
			}

			$number_of_rooms = ! empty( $_POST['number_of_rooms'] ) ? absint( $_POST['number_of_rooms'] ) : 1;
			$room_names = ! empty( $_POST['room_name'] ) ? explode( ',', wp_kses( $_POST['room_name'], array() ) ) : array();
			$rooms = array_values( AWE_function::get_rooms_of_room_type( $post_id ) );

			if ( get_post_meta( $post_id, 'number_of_rooms', true ) != $number_of_rooms ) {
				update_post_meta( $post_id, 'number_of_rooms', $number_of_rooms );

				foreach ( $rooms as $room ) {
					wp_delete_post( $room->ID );
				}

				for ( $i = 0; $i < $number_of_rooms; $i++ ) {
					if ( ! empty( $room_names[ $i ] ) ) {
						$room_name = $room_names[ $i ];
					} elseif ( 1 != $number_of_rooms ) {
						$room_name = get_the_title( $post_id ) . ' ' . ( $i + 1 );
					} else {
						$room_name = get_the_title( $post_id );
					}

					AWE_function::create_room( $post_id, $room_name );
				}
			} else {
				for ( $i = 0; $i < $number_of_rooms; $i++ ) {
					if ( ! empty( $room_names[ $i ] ) ) {
						$room_name = trim( $room_names[ $i ] );
					} elseif ( 1 != $number_of_rooms ) {
						$room_name = get_the_title( $post_id ) . ' ' . ( $i + 1 );
					} else {
						$room_name = get_the_title( $post_id );
					}

					if ( ! empty( $rooms[ $i ] ) ) {
						wp_update_post( array(
							'ID'		=> $rooms[ $i ]->ID,
							'post_title'	=> $room_name,
						) );
					} else {
						AWE_function::create_room( $post_id, $room_name );
					}
				}
			}

			if ( isset( $_POST['base_price'] ) ) {
				update_post_meta( $post_id, 'base_price', ( float ) $_POST['base_price'] );
			}
			if ( isset( $_POST['base_price_for'] ) ) {
				update_post_meta( $post_id, 'base_price_for', absint( $_POST['base_price_for'] ) );
			}
			if ( isset( $_POST['min_sleeps'] ) ) {
				update_post_meta( $post_id, 'min_sleeps', absint( $_POST['min_sleeps'] ) );
			}
			if ( isset( $_POST['max_sleeps'] ) ) {
				update_post_meta( $post_id, 'max_sleeps', absint( $_POST['max_sleeps'] ) );
			}
			if ( isset( $_POST['min_children'] ) ) {
				update_post_meta( $post_id, 'min_children', wp_kses( $_POST['min_children'], array() ) );
			}
			if ( isset( $_POST['max_children'] ) ) {
				update_post_meta( $post_id, 'max_children', wp_kses( $_POST['max_children'], array() ) );
			}
			if ( isset( $_POST['min_night'] ) ) {
				update_post_meta( $post_id, 'min_night', absint( $_POST['min_night'] ) );
			}

			if ( isset( $_POST['extra_adult'] ) ) {
				for ( $i = 0; $i <= count( $_POST['extra_adult']['number'] ) - 1; $i++ ) {
					$extra_adult[] = array( 'number' => $_POST['extra_adult']['number'][ $i ], 'price' => $_POST['extra_adult']['price'][ $i ] );
				}
			} else {
				$extra_adult = '';
			}
			update_post_meta( $post_id, 'extra_adult', $extra_adult );

			if ( isset( $_POST['extra_child'] ) ) {
				for ( $i = 0; $i <= count( $_POST['extra_child']['number'] ) - 1; $i++ ) {
				   $extra_child[] = array( 'number' => $_POST['extra_child']['number'][ $i ],'price' => $_POST['extra_child']['price'][ $i ] );
				}
			} else {
				$extra_child = '';
			}
			update_post_meta( $post_id, 'extra_child', $extra_child );


			if ( isset( $_POST['sale_date'] ) ) {
				for ( $i = 0; $i <= count( $_POST['sale_date']['date'] ) - 1; $i++ ) {
					if ( ! empty( $_POST['sale_date']['date'][ $i ] ) ) {
						$extra_sale[] = array(
							'type_duration' =>$_POST['sale_date']['date'][ $i ],
							'total'         =>$_POST['sale_date']['total'][ $i ],
							'sale_type'     =>$_POST['sale_date']['type'][ $i ],
							'amount'        =>$_POST['sale_date']['price'][ $i ],
						);
					}
				}
			} else {
				$extra_sale = '';
			}

			update_post_meta( $post_id, 'extra_sale', $extra_sale );

			if ( isset( $_POST['option_name'] ) ) {
				$total_package      = $_POST['option_name'];
				$package_name       = $_POST['option_name'];
				$package_quantity   = isset( $_POST['quantity'] ) ? $_POST['quantity'] : array();
				$package_operation  = $_POST['option_operation'];
				$package_value      = $_POST['option_value'];
				$package_desc       = $_POST['option_desc'];
				$package_type       = $_POST['type_package'];
				$package_icon       = $_POST['option_icon'];
			} else {
				$data_option = AWE_function::get_room_option( $post_id, 'apb_room_type' );
				$total_package = $data_option;

				foreach ( $data_option as $item_package) {
					$package_name[]         = $item_package->option_name;
					$package_quantity[]     = $item_package->total;
					$package_operation[]    = $item_package->option_operation;
					$package_value[]        = $item_package->option_value;
					$package_desc[]         = $item_package->option_desc;
					$package_type[]         = $item_package->revision_id;
					$package_icon[]         = $item_package->option_icon;
				}
			}


			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}apb_booking_options WHERE object_id = %d", $post_id ) );

			for ( $i = 0; $i <= count( $total_package ) - 1; $i++ ) {
				$wpdb->insert(
					$wpdb->prefix . 'apb_booking_options',
					array(
						'entity_type'       => 'apb_room_type',
						'object_id'         => absint( $post_id ),
						'total'             => wp_kses( $package_quantity[ $i ], '' ),
						'option_name'       => wp_kses( $package_name[ $i ], '' ),
						'option_operation'  => wp_kses( $package_operation[ $i ], '' ),
						'option_value'      => wp_kses( $package_value[ $i ], '' ),
						'option_icon'       => wp_kses( $package_icon[ $i ], '' ),
						'option_desc'       => stripslashes( $package_desc[ $i ] ),
						'status'            => 'publish',
						'revision_id'       => isset( $package_type[ $i ] ) ? $package_type[ $i ]: 0,
					),
					array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
				);
			}
		}

		add_action( 'save_post', 'boxes_package_room_type::save' );
	}
}

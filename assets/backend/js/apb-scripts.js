jQuery(document).ready(function($) {

	$('.awe-avb-remove-old-data').on('click', function() {
		var a = confirm(ApbBackend.textConfirmRemoveOld);
		if (a) {
			$.post(ApbBackend.ajaxUrl, {action: 'apb_remove_old_data'}, function(res) {
				$('#content').after('<div class="updated notice notice-success below-h2" style="margin-bottom: 15px;"><p>' + res + '</p></div>');
				// $('#content').after('<p class="apb-alert success">' + res + '</p>');
			});
		}
	});

	$(".close-box-room-type-js").click(function(){
		 $(".box-select-room-type-js").hide();
		 $(".box-event-info-js").hide();
	});
	$(".select-room-type-js").click(function(){
		 $(".box-select-room-type-js").show();
	});
	$('.my-color-field').wpColorPicker();
	$("#order_status").remove();
	$("#s2id_order_status").remove();

	DatePicker();
	function DatePicker() {
		var date_format = (jQuery.datepicker.regional[apb_lang].dateFormat) ? jQuery.datepicker.regional[apb_lang].dateFormat : 'mm/dd/yy';
		if(typeof awe_start_date !== 'undefined'){
			var default_date_start = awe_start_date;
			var default_date_end = awe_start_date;
		}else{
			var default_date_start = "+0w";
			var default_date_end = "+0w";
		}
		$( ".date-start-js" ).datepicker({
			prevText: '<i class="fa fa-chevron-left"></i>',
			nextText: '<i class="fa fa-chevron-right"></i>',
			buttonImageOnly: false,
			dateFormat: date_format,
			defaultDate: default_date_start,
			numberOfMonths: 2,
			minDate : 0,

			onClose: function( selectedDate ) {
				$( ".date-end-js" ).datepicker( "option", "minDate", selectedDate );
				},
			beforeShow: function(input, inst) {
				 $('#ui-datepicker-div').addClass('apb-datepicker');
			 }
		});
		$( ".date-end-js" ).datepicker({
			prevText: '<i class="fa fa-chevron-left"></i>',
			nextText: '<i class="fa fa-chevron-right"></i>',
			buttonImageOnly: false,
			dateFormat: date_format,
			defaultDate: default_date_end,
			numberOfMonths: 2,
			minDate : 0,
			onClose: function( selectedDate ) {
				//$( ".date-start-js" ).datepicker( "option", "maxDate", selectedDate );
				},
			beforeShow: function(input, inst) {
				 $('#ui-datepicker-div').addClass('apb-datepicker');
			 }
		});
	}
	CheckAll();
	function CheckAll(){
		$("select[name=select_all]").change(function(){
			 if($(this).val() == "all"){

				 $(".get_room_id_js").each(function(){
					 this.checked = true;
				 });
			 }
			 if($(this).val() == "none"){
				 $(".get_room_id_js").each(function(){
						this.checked = false;
				 });
			 }
		});
		$("#apb-check").click(function(){
			if(this.checked){
			$(".apb-check").each(function(){
					this.checked = true;
			});
			}else{
			$(".apb-check").each(function(){
					this.checked = false;
			});
			}
		});
	}
	validate_apb_form();

	function validate_apb_form(){
		// Price input validation
		$('body').on( 'blur', '.apb-int', function() {
		$('.apb_error_tip').fadeOut('100', function(){ $(this).remove(); } );
		return this;
		});

		 var regula = new RegExp('^[0-9]+$');
		 $("body").on('keyup change','.apb-int',function(){
			 var apb_int = $(this).val();
			 if(!regula.test(apb_int)){
			if ( $(this).parent().find('.apb_error_tip').size() === 0 ) {
				var offset = $(this).position();
				$(this).after( '<div class="apb_error_tip">Please enter in integer (.) format without thousand separators and symbols.</div>' );
				$('.apb_error_tip')
				.css('left', offset.left + $(this).width() - ( $(this).width() / 2 ) - ( $('.apb_error_tip').width() / 2 ) )
				.css('top', offset.top + $(this).height() )
				.fadeIn('100');
				$(this).val('');
			}
			 }
			return this;
		 });
		$("body").on('change','.apb-int',function(){
			 var apb_int = $(this).val();
			 if(!regula.test(apb_int)){
				 $(this).val('');
			 }
		 });
	}
	skip_install();
	function skip_install(){
		$(".apb_skip").click(function(){
			$("#apb_message").slideUp();
			data = { action: "apb_skip_install" }
			$.post(ajaxurl, data, function(reuslt){});
		});
	}

	function init_tiptip() {
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.help_tip' ).tipTip({
		'attribute': 'data-tip',
		'fadeIn': 50,
		'fadeOut': 50,
		'delay': 200
		});
	}
	init_tiptip();

	function awe_tabs_metabox(){
		// TABS
		$('ul.awe-tabs').show();
		$('div.panel-wrap').each(function(){
		$(this).find('div.panel:not(:first)').hide();
		});

		$('ul.awe-tabs a').click(function(){
			var panel_wrap =  $(this).closest('div.panel-wrap');
			$('ul.awe-tabs li', panel_wrap).removeClass('active');
			$(this).parent().addClass('active');
			$('div.panel', panel_wrap).hide();
			$( $(this).attr('href') ).show();
			return false;
		});
		$('ul.awe-tabs li:visible').eq(0).find('a').click();
		}
	awe_tabs_metabox();
	/*
		Add extra price meta box of post type room
	*/
	function add_extra_price(){

		$(".add_extrad_price_adult_js").click(function(){
			var html ='<tr class="item-extra-price">'
					+'<td><input class="apb-int" type="text" name="extra_adult[number][]"></td>'
					+'<td><input type="text" name="extra_adult[price][]"></td>'
					+'<td><button type="button" class="button remove-extra-price-js">Remove</button></td>'
					+'</tr>';
			$(".form-extra-price-adult-js").append(html);
		});
		$(".add_extrad_price_child_js").click(function(){
			var html ='<tr class="item-extra-price">'
					+'<td><input class="apb-int" type="text" name="extra_child[number][]"></td>'
					+'<td><input type="text" name="extra_child[price][]"></td>'
					+'<td><button type="button" class="button remove-extra-price-js">Remove</button></td>'
					+'</tr>';
			$(".form-extra-price-child-js").append(html);
		});

		$(".awe-plugin").on("click",".remove-extra-price-js",function(){
		$(this).closest(".item-extra-price").remove();
		});
	}
	add_extra_price();

	var ScriptAllPlugin = {
		init : function(){
		this.RoomMetaBox();
		this.SaveActiveMenuSetting();
		this.AddSale();
		this.UploadFileImport();
		this.GenShortcode();
		this.CheckDaiyPackage();
		this.depositCheck();
		this.listIcon();
		/*----------  remove element order status  ----------*/
		$(".order_actions.submitbox #actions").remove();
		$(".form-field.form-field-wide label").each(function(){
			if($(this).attr('for') == 'order_status'){
			$(this).remove();
			}
		});

		},
		RoomMetaBox : function(){
			$('.type_box').appendTo( '#apb-room-meta-box h3.hndle span' );
			$('#apb-room-meta-box h3.hndle').unbind('click.postboxes');
			$('#pb-room-meta-box').on('click', 'h3.hndle', function(event){

			// If the user clicks on some form input inside the h3 the box should not be toggled
			if ( $(event.target).filter('input, option, label, select').length )
				return;
			$('#pb-room-meta-box').toggleClass('closed');
			});

		},
		SaveActiveMenuSetting : function(){
		var index = 'key';
		//  Define friendly data store name
		var dataStore = window.sessionStorage;
		//  Start magic!
		try {
			// getter: Fetch previous value
			var oldIndex = dataStore.getItem(index);
		} catch(e) {
			// getter: Always default to first tab in error state
			var oldIndex = 0;
		}
		$( "#tabs" ).tabs(
			{
				 // The zero-based index of the panel that is active (open)
				active : oldIndex,
				// Triggered after a tab has been activated
				activate : function( event, ui ){
					//  Get future value
					var newIndex = ui.newTab.parent().children().index(ui.newTab);
					//  Set future value
					dataStore.setItem( index, newIndex )
				}
			}
		);
		var sub_index = 'sub_key';
		//  Define friendly data store name
		var sub_dataStore = window.sessionStorage;
		//  Start magic!
		try {
			// getter: Fetch previous value
			var sub_oldIndex = sub_dataStore.getItem(sub_index);
		} catch(e) {
			// getter: Always default to first tab in error state
			var sub_oldIndex = 0;
		}
		$( ".awe-sub-tab" ).tabs(
			 {
				 // The zero-based index of the panel that is active (open)
				active : sub_oldIndex,
				// Triggered after a tab has been activated
				activate : function( event, ui ){
					//  Get future value
					var newIndex = ui.newTab.parent().children().index(ui.newTab);
					//  Set future value
					sub_dataStore.setItem( sub_index, newIndex )
				}
			}
			);
		},
		AddSale : function(){
		$(".options_group").on('click','.apb-input-type-sale-js',function(event){
			$(".apb-sale-type").hide();
			var id = $(this).attr('data-int');
			$(".type-sale-"+id).fadeIn(100);
			event.stopPropagation();
		});
		$(".options_group").on('click','.apb-input-type-duration-js',function(event){
			 $(".apb-sale-type").hide();
			var id = $(this).attr('data-int');
			$(".type-duration-"+id).fadeIn(100);
			event.stopPropagation();
		});
		$(".options_group").on("click",".value-duration-js",function(){
			var type = $(this).attr('data-type');
			var id = $(this).attr('data-int');
			$(".input-duration-"+id).val(type);
		});
		$(".options_group").on("click",".value-type-js",function(){
			var type_name = $(this).attr('data-value');
			var type = $(this).attr('data-type');
			var id = $(this).attr('data-int');
			$(".input-sale-"+id).val(type_name);
			$(".input-sale-hidden-"+id).val(type);
		});
		$('body').click(function(){
			$(".apb-sale-type").fadeOut(100);
		});
		//Add new form
		var i = $(".item-extra-sale").length;
		$(".add_sale_js").click(function(e) {
			var _i = i++;
			var $html = $('#item-extra-sale-sample').clone();
			$html.removeAttr('id').addClass('item-extra-sale');
			var $fields = $html.find('.sale-date-field');
			$fields.each(function() {
				var $this = $(this);
				var key = $this.attr('data-key');
				$this.attr('name', 'sale_date[' + key + '][]');
			});
			$(".form-extra-sale-js").append($html);
			$html.fadeIn(300);
		});
		$(".awe-plugin").on("click",".remove-extra-sale-js",function(){
			$(this).closest(".item-extra-sale").remove();
		});
		},
		GenShortcode: function(){
			function reset_text_shortcode() {
				var attr = $(".apb_form_check_style_js").val();
				if (attr == 1) {
					attr = "vertical";
				} else if (attr == 2) {
					attr = "horizontal";
				}
				var shortcode_current = $('.apb-gen-shortcode-js').text();
				var new_shortcode = shortcode_current.replace(']',' style="'+attr+'"]');
				$('.apb-gen-shortcode-js').text(new_shortcode);
			}
			$(window).load(function(){
				var attr = "";
				$('.get_attr_shortcode_js').each(function() {
					if (this.checked) {
						attr += $(this).attr('data-attr') + '="on" ';
					}
				});
				$('.apb-gen-shortcode-js').text('[apb_check_available '+attr+']');
				reset_text_shortcode();
			});

			$('.get_attr_shortcode_js').change(function() {
				var attr = '';
				$('.get_attr_shortcode_js').each(function() {
					if (this.checked) {
						attr += $(this).attr('data-attr') + '="on" ';
					}
				});
				$('.apb-gen-shortcode-js').text('[apb_check_available ' + attr + ']');
				reset_text_shortcode();
			});

			$(".apb_form_check_style_js").change(function(){
				var shortcode_current = $('.apb-gen-shortcode-js').text();
				var attr = $(this).val();
				if (attr == 1) {
					attr = 'vertical';
				} else if (attr == 2) {
					attr = 'horizontal';
				}

				var new_shortcode = shortcode_current.replace(']',' style="' + attr + '"]');

				if (shortcode_current.search('style="horizontal"') != -1) {
					var shortcode_current = $('.apb-gen-shortcode-js').text();
					var new_shortcode = shortcode_current.replace('style="horizontal"]',' style="'+attr+'"]');
				}
				if(shortcode_current.search('style="vertical"') != -1){
					var shortcode_current = $('.apb-gen-shortcode-js').text();
					var new_shortcode = shortcode_current.replace('style="vertical"]',' style="'+attr+'"]');
				}
				$('.apb-gen-shortcode-js').text(new_shortcode);

			});
		},
		UploadFileImport : function(){
		function render_notice(notice,id){
			return '<div id="message-'+id+'" class="updated notice notice-success is-dismissible below-h2">\
					<p>'+notice+'</p>\
					<button type="button" class="notice-dismiss">\
					<span class="screen-reader-text">Dismiss this notice.</span></button>\
					<button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>\
					</div>';
		}

		$(".awe-begin-import-js").click(function(){
			$(".sk-folding-cube").show();
			var data_step_2 = {
				action: "Apb_import_ajax",
				step: 2
			};
			$.post(ajaxurl, data_step_2, function(reuslt_step_2){
				if(reuslt_step_2 == 2){
					$(".awe-import").append(render_notice("Import Success.",2));
					$(".sk-folding-cube").hide();
					/*var data_step_3 = {
						action: "Apb_import_ajax",
						step: 3
					};
					$.post(ajaxurl, data_step_3, function(reuslt_step_3) {
						if(reuslt_step_3 == 3){
						$(".awe-import").append(render_notice("Import Bookings Success.",3));
						$(".awe-import").append(render_notice("Import Data Success.",3));
						$(".sk-folding-cube").hide();
						}
					});*/
				}
			});

			return false;
		});
		 $(".awe-import").on("click",".notice-dismiss",function(){
			$(this).parent().remove();
		 });
		},
		CheckDaiyPackage: function(){
		$("#roomtype_package,#room_package").on('change','.apb-option-type-js',function(){
			var id = $(this).attr('data-id');
			if(this.checked == true){
				$("#apb-option-type-"+id).val(1);
			}else{
				$("#apb-option-type-"+id).val(0);
			}
		});
		},
		depositCheck: function(){
			// $("#rooms_checkout_style_1").change(function(){
			// 	if(this.checked) {
			// 		$(".apb-js-wc-deposit").fadeIn(200);
			// 	}
			// });
			// $("#rooms_checkout_style_2").change(function(){
			// 	if(this.checked) {
			// 		$(".apb-js-wc-deposit").fadeOut(100);
			// 	}
			// });

			$('select[name=apb_deposit_type]').change(function(){
				var type = $(this).val();
				var _type = $(".apb-js-type-deposit").attr('data-type');

				if('none' == type) {
					$('input[name=apb_deposit_number]').closest('.form-elements').fadeOut(100);
				}else{
					$('input[name=apb_deposit_number]').closest('.form-elements').fadeIn(100);
				}

				if('percent' == type) {
					$(".apb-js-type-deposit").html('%');
				}
				if('money' == type) {
					$(".apb-js-type-deposit").html(_type);
				}
			})
		},

		listIcon : function(){
			var self = this;
			$("#apb-room-meta-box").on('click','.apb-js-chose-icon',function(){
				self.tabIcon();
				$(".apb-package-icon .media-frame-title h1").text( 'List Icons' );
				$(".apb-tab-icon-item:first").show();
				$(".apb-package-icon").fadeIn(100);
				$(".apb-js-package-icon").attr('append-id',$(this).attr('data-id'));
			});
		

			$('.apb-package-icon .media-modal-close').click(function(){
				$(".apb-package-icon").fadeOut(100);
			})
			$(document).keyup(function(e) { 
				if (e.which === 27){
					$('.apb-package-icon .media-modal-close').trigger('click');
				}
			})

			$(".apb-js-package-icon li").click(function(){
				var icon = $(this).attr('data-icon'),
				 	id 	 = $(this).closest('.apb-js-package-icon').attr('append-id'),
				 	iconTpl = ( icon == 'none' ) ? 'Icon' : '<i class="'+icon+'"></i>',
				 	iconVal = ( icon == 'none' ) ? '' : icon;
				$(".apb-icon-"+id).html(iconTpl);
				$(".apb-input-icon-"+id).val(iconVal);
				$(".apb-package-icon").fadeOut(100);
			})
		},

		/**
		 * tabIcon
		 * Tab icon for awebooking
		 * @return {[type]} [description]
		 */
		tabIcon : function(){
			function hideItem(){
				$(".apb-tab-icon-item").each(function(index,el){
					$(el).hide();
				});
			}
			hideItem();
			$('.apb-package-icon .media-menu-item').click(function(){
				var idAttr = $(this).attr('href'),
					name   = $(this).text();
				hideItem();
				$(".apb-package-icon .media-frame-title h1").text(name);
				$(idAttr).show();
			})
		}
	}


	ScriptAllPlugin.init();





});

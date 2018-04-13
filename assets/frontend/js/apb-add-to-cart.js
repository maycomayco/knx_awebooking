(function($) {
  "use strict";

  $(document).ready(function() {

    var ApbAddToCart = {
      data_init : {
        _num: 0
      },
      init: function() {
        $(window).load(function() {
          ApbAddToCart.data_init._num = 0;
        });
        this.AddCartAllRoom();
        this.ChangeCart();
      },

      /*====================================================================
      =             Add product to session cart | Type All Room            =
      ====================================================================*/

      AddCartAllRoom : function() {

        $('#awe-plugin-booking').on('click', '.apb-book-now-js', function(e) {
          e.preventDefault();

          $('#preloader').show();

          var $btn         = $(this);
          var $wrapper     = $btn.closest('.apb-check-availability');
          var num_room     = parseInt( $wrapper.find('.total-room-js').val() );
          var $checkBtn    = $wrapper.find('.check-avb-js');
          var num_args     = parseInt( $checkBtn.attr('data-num-args') );
          var from         = $wrapper.find('.date-start-js').attr('data-date');
          var to           = $wrapper.find('.date-end-js').attr('data-date');
          var room_type_id = parseInt( $btn.attr('data-id') );
          var price        = $('.room-price-base-' + room_type_id).val();
          var total_price  = $('.total-price-room-' + room_type_id).val();
          var cart_index = parseInt( $btn.attr('data-cart-index') );
          var room_id;

          var package_data = new Array();
          $('.package-check-js').map(function(_, el) {
            if( $(el).attr('data-id') == room_type_id && this.checked ) {
              var package_id = $(el).attr('data-option_id');
              package_data.push({
                package_id: package_id,
                type:       $(el).val(),
                total:      $('.package-option-id-' + package_id).val(),
              });
            }
          });

          var room_adult = new Array();
          $wrapper.find('select.apb-adult-select').map(function(_, el) {
            room_adult.push({adult: $(el).val()});
          });

          var room_child = new Array();
          $wrapper.find('select.apb-child-select').map(function(_, el) {
            room_child.push({child: $(el).val()});
          });

          var ajaxData = {
            action:       'apb_add_room_to_cart',
            room_type_id: room_type_id,
            price       : total_price,
            from        : from,
            to          : to,
            adult       : ( cart_index == -1 ) ? room_adult[ num_args ].adult : room_adult[ cart_index ].adult,
            child       : ( cart_index == -1 ) ? room_child[ num_args ].child : room_child[ cart_index ].child,
            package_data: package_data,
            cart_index  : cart_index,
          };
          $.ajax({
            url:     apbAjax.ajax_url,
            data:    ajaxData,
            type:    'post',
            success: function(result) {
              if ( ! result.success ) {
                ApbAddToCart.ApbNotice(result.data, 'alert', num_args);
                $('#preloader').hide();
              } else {
                $(document).trigger('apb_book_room_success', [ajaxData, num_args, num_room]);

                ApbAddToCart.ApbNotice(result.data.message, 'success', num_args);
                room_id = result.data.room_id;

                if ( cart_index == -1 ) {
                  var data = {
                    action:     'apb_update_layout_room_select',
                    room_adult: room_adult,
                    room_child: room_child,
                  };
                  $.ajax({
                    url:     apbAjax.ajax_url,
                    data:    data,
                    type:    'post',
                    // async:   false,
                    success: function(result) {
                      $('.room-select-js').html(result);

                      if ( num_args < num_room - 1 ) {
                        $checkBtn.attr('data-num-args', parseInt(num_args) + 1);
                        $wrapper.find('.check-avb-js').trigger('click');
                        // $('#preloader').hide();
                      } else {
                        var all_item = $('.apb-room-selected_item');
                        $checkBtn.attr('data-num-args', 0);
                        var call = ApbAddToCart.CallLayoutSelectRoom(all_item, room_adult);
                      }
                    },
                  });
                } else {
                  // Change room.
                  var $current_item = $('.apb-room-selected_content .apb-room-seleted_current');
                  var data = {
                    action: 'apb_change_selected_item_layout',
                    room_id: room_id,
                    adult: room_adult,
                    child: room_child,
                    index: $current_item.index(),
                    disable: -1,
                  };
                  $.ajax({
                    url: apbAjax.ajax_url,
                    data: data,
                    type: 'post',
                    success: function(result) {
                      $current_item.replaceWith(result);

                      var $next_item = $('.apb-room-selected_content .apb_disable:first');
                      if ( $next_item.length ) {
                        var next_adult = $next_item.attr('data-adult');
                        var data = {
                          action: 'apb_change_selected_item_layout',
                          room_id: room_id,
                          adult: next_adult,
                          child: $next_item.attr('data-child'),
                          index: $next_item.index(),
                          disable: 0,
                        };
                        $.ajax({
                          url: apbAjax.ajax_url,
                          data: data,
                          type: 'post',
                          success: function(result) {
                            $next_item.replaceWith(result);

                            if ( num_args < num_room - 1 ) {
                              $checkBtn.attr('data-num-args', parseInt(num_args) + 1);
                              $wrapper.find('.check-avb-js').trigger('click');
                              // $('#preloader').hide();
                            } else {
                              var all_item = $('.apb-room-selected_item');
                              $checkBtn.attr('data-num-args', 0);
                              var call = ApbAddToCart.CallLayoutSelectRoom(all_item, room_adult);
                            }
                          },
                        });
                      } else {
                        if ( num_args < num_room - 1 ) {
                          $checkBtn.attr('data-num-args', parseInt(num_args) + 1);
                          $wrapper.find('.check-avb-js').trigger('click');
                          // $('#preloader').hide();
                        } else {
                          var all_item = $('.apb-room-selected_item');
                          $checkBtn.attr('data-num-args', 0);
                          var call = ApbAddToCart.CallLayoutSelectRoom(all_item, room_adult);
                        }
                      }
                    },
                  });
                }
              }
            },
          });
        });
      },

      ApbNotice : function(message,classStyle,num) {
        /**
         * Status color : success|alert alert-warning
         */
        var notice_setWidth = $(".apb-content-js").width();
        var notice = '<p class="apb-alert '+classStyle+' apb-not-'+(parseInt(num)+1)+'">'
                 + message
                 +'</p>'
        $(".apb-notice-js").append(notice);
        $(".apb-notice-js").fadeIn(100);

        var i = 0;
        var inter = setInterval(function() {
          var _i = i++;
          if(_i <= $(".apb-alert").length) {
          $('.apb-notice-'+_i).fadeOut(100);
          $('.apb-not-'+_i).fadeOut(100);
          }else{
          clearInterval(inter);
          }
        },3000);
      },
      /*=====  End of Add product to sesstion cart | Type Only room type  ======*/


      AddToCheckout : function(room_id,from,to,price,adult,child,room_type,package_data,people_sort,search_by ) {
        if(!search_by) {
        search_by = 'apb_room_type';
        }
         var data = {
          action: 'woocommerce_add_to_cart',
          product_id:  room_id,
          quantity: 1,
          from: from,
          to: to,
          num:people_sort,
          price: price,
          adult: adult,
          child: child,
          package_data:package_data,
          search_by: search_by
        };

        $.post(apbAjax.ajax_url, data, function(form) {
        });
      },
      /*==========================================
        Layout all room check available
       ==========================================*/
      CallLayoutSelectRoom : function(all_item,room) {
        var data = {
          action: 'full_select_room',
        };
        if ( all_item.length == room.length ) {
          $('#preloader').show();
          $.post(apbAjax.ajax_url, data, function(result) {
            var data = result;
            // Change next step.
            $(".apb-step-one").removeClass('active');
            $(".apb-step-one").addClass('step-complete');
            $(".apb-step-two").addClass('active');
            $(".apb-content-js").html(data);
            $("#preloader").hide();
          });
          return true;
        }
      },

      /*==========================================
          Change cart of session cart
       ==========================================*/
      ChangeCart : function() {
        $(document).on('click', '.change-item-cart-js', function(e) {
          e.preventDefault();
          $('#preloader').show();

          var $btn = $(this);
          var $item = $(this).closest('.apb-room-selected_item');
          var $wrapper = $btn.closest('.apb-widget-area');
          var room_id = $item.attr('data-id');
          var index = $item.index();
          var adult = parseInt( $item.attr('data-adult') );
          var child = parseInt( $item.attr('data-child') );

          // Change item layout.
          var data = {
            action: 'apb_change_selected_item_layout',
            room_id: room_id,
            adult: adult,
            child: child,
            index: index,
            disable: 0,
          };
          $.ajax({
            url: apbAjax.ajax_url,
            data: data,
            type: 'post',
            success: function(result) {
              $item.replaceWith(result);
            },
          });

          var $current_item = $item.parent().find('.apb-room-seleted_current');
          if ( $current_item.length ) {
            var current_index = $current_item.index();
            var current_adult = parseInt( $current_item.attr('data-adult') );
            var current_child = parseInt( $current_item.attr('data-child') );

            var data = {
              action: 'apb_change_selected_item_layout',
              room_id: room_id,
              adult: current_adult,
              child: current_child,
              index: current_index,
              disable: 1,
            };
            $.ajax({
              url: apbAjax.ajax_url,
              data: data,
              type: 'post',
              success: function(result) {
                $current_item.replaceWith(result);
              },
            });
          }

          // Remove item from cart.
          $.ajax({
            url: apbAjax.ajax_url,
            data: {
              action: 'apb_remove_room_from_cart',
              room_id: room_id,
            },
            type: 'post',
            success: function(result) {

              // Change book index.
              var $checkBtn    = $wrapper.find('.check-avb-js');
              if ( current_index !== undefined ) {
                $checkBtn.attr('data-num-args', parseInt(current_index) - 1);
              } else {
                var num_room = $wrapper.find('.total-room-js').val();
                $checkBtn.attr('data-num-args', num_room - 1);
              }

              // Recheck available.
              // $checkBtn.click();
              $('.apb-step-one').addClass('active');
              $('.apb-step-two').removeClass('active');
              $('.apb-step-one').removeClass('step-complete');
              $('.apb-step-two').removeClass('step-complete');

              var from = $wrapper.find('.date-start-js').val();
              var to = $wrapper.find('.date-end-js').val();
              var num_args = index;
              var room_type_id = parseInt( $wrapper.find('input[name="room_type_id"]').val() );

              var data = {
                action: 'apb_check_available',
                from: from,
                to: to,
                adult: adult,
                child: child,
                num_args: num_args,
                cart_index: index,
              };

              // console.log('change', from, $btn.length, $wrapper.length, $wrapper.find('.date-start-js').length);

              if ( room_type_id ) {
                data.room_type_id = room_type_id;
              }
              // console.log(data);
              $.post( apbAjax.ajax_url, data, function(result) {
                var data_result = result;
                $('.apb-content-js').html(data_result);
                $('#preloader').hide();
              } );

            },
          })

        });
      },

      /*===================================================================================
        Call layout all room selected and room was checking the status validate max people
       ====================================================================================*/
      UserRoomSelect : function(from,to,room_adult,room_child,key_cart,data_num,control) {
        var room_select = {
          action: "user_room_select",
          room_adult:room_adult,
          room_child:room_child,
          control: control,
          from:from,
          to:to,
          key:key_cart,
          num_args: data_num,
        };

        $.ajax({
          url: apbAjax.ajax_url,
          data: room_select,
          type: 'post',
          async: false,
          success: function(result) {
            var data = JSON.parse(result);
            $(".room-select-js").html(data);
            $(".box-cart-item-"+key_cart).hide();

            /*----------  Set room change  ----------*/
            var _i = ($(".apb-room-selected_content div").length  == 2) ? 1 : 0;
            $(".apb-room-selected_content div").each(function(index, el) {
              if ( $(el).attr('data-id') ) {
                $(".apb-room_item-" + $(el).attr('data-id') ).remove();
              }
              var i = _i++;
              if ( $(el).attr('data-type') ) {
                $(el).removeClass('apb-room-seleted_current');
                $(el).removeClass('apb-bg_blue');
                $(el).addClass('apb-room-selected_item apb_disable');
                $(el).find('h6').html('Room '+i);
              }
            });
            $("#preloader").hide();
          },
        });

        // $.post(apbAjax.ajax_url, room_select, function(result) {

        // });
      },

      count_days: function(date1, date2) {
        var each_day = 1000 * 60 * 60 * 24;//milliseconds in a day
        var ms_date1 = date1.getTime();//milliseconds for date1
        var ms_date2 = date2.getTime();//milliseconds for date2
        var ms_date_diff = Math.abs(ms_date1 - ms_date2);//different of the two dates in milliseconds
        var days = Math.round(ms_date_diff / each_day);//divided the different with millisecond in a day
        return days;
      },
    }

    ApbAddToCart.init();

  });

})(jQuery);

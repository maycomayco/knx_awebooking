(function($) {
  "use strict";

  $(document).ready(function() {
    var ApbBookings = {

      init: function(){
        this.ApbCheckAvailability();
        this.ApbGetOptionRoom();
        this.ApbGetPriceOption();
        this.FromAction();
        this.AddNewItemBooking();
        this.selectCustomer();
      },
      ApbCheckAvailability : function(){
        $(".check-avb-js").click(function(e) {
          e.preventDefault();
          var from = $(".date-start-js").val();
          var to = $(".date-end-js").val();
          var adult = $('select[name="room_adult"]').val();
          var child = $('select[name="room_child"]').val();

          if ( from == '') {
            $(".date-start-js").focus();
            return this;
          }

          if ( to == '' ) {
            $(".date-end-js").focus();
            return this;
          }

          $(".spinner").show();

          var data = {
            action: "admin_check_availability",
            from: from,
            to: to,
            adult: adult,
            child: child,
            // room_adult:room_adult,
            // room_child:room_child,
          };
          $.post(ajaxurl, data, function(result) {
            var data_result = result;
            $(".apb-list-room-js").html(data_result);
            $(".spinner").hide();
            $("input[name=save]").attr('disabled','disabled');
          });
        });
      },
      ApbGetOptionRoom : function(){
        $('.awe-plugin').on('click','.rooms',function(){
          $('.spinner').hide();
          var id = $(this).val();
          var name = $(this).attr('data-name');
          var to = $('input[name=to]').val();
          var from = $('input[name=from]').val();

          var room_adult = new Array();
          $('select[name=room_adult]').map(function(_,el){
            room_adult.push({adult: $(el).val()});
          });

          var room_child = new Array();
          $('select[name=room_child]').map(function(_,el){
            room_child.push({child: $(el).val()});
          });

          $('.apb-load-'+id).show();
          var data = {
            action: 'admin_get_option_room',
            room_id: id,
            name:name,
            from: from,
            to: to,
            room_adult: room_adult,
            room_child: room_child,
          };
          $.post(ajaxurl, data, function(result) {
            var data_result = result;
            $('.list-option-room-js').html(data_result);
            $('.apb-load-' + id).hide();
            $('input[name="save"]').show();
            $('input[name="save"]').removeAttr('disabled');
          });
        });
      },

      ApbGetPriceOption: function() {
        $(document).on('change', '.options_operation_js', function() {
          var operation = $(this).attr('data-operation');
          var price_package = parseFloat( $(this).attr('data-value') );
          var price_room_current = parseFloat( $('.total_price_js').val() );
          var daily_package = parseInt( $(this).attr('data-daily') );
          var package_id = $(this).val();
          var number_nights = $(this).attr('data-nights');
          var $package_num = $(this).closest('p').find('.package-num-' + package_id);
          var number_packages = parseInt( $package_num.val() );

          var total_price;
          if ( $(this).is(':checked') ) {
            $package_num.attr('readonly', 'readonly');
            if ( daily_package ) {
              total_price = price_room_current + price_package * number_packages * number_nights;
            } else {
              total_price = price_room_current + price_package * number_packages;
            }
          } else {
            $package_num.attr('readonly', false);
            if ( daily_package ) {
              total_price = price_room_current - price_package * number_packages * number_nights;
            } else {
              total_price = price_room_current - price_package * number_packages;
            }
          }
          $(".total_price_js").val(total_price);
        });
      },

      FromAction : function(){
        $(window).load(function(){
          $("select[name=apb-booking-bulk-actions]").removeAttr('disabled');
        });

        $(".apb-reload-action").click(function() {
          $(".apb-get_action").show();
          var action = $("select[name=apb-booking-bulk-actions]").val();

          if (action == 'newitem') {

            $("input[name=save]").attr('disabled','disabled');
            $("select[name=apb-booking-bulk-actions]").attr('disabled','disabled');
            var from = $("input[name=from]").val();
            var to = $("input[name=to]").val();
            var adult = $('select[name="room_adult"]').val();
            var child = $('select[name="room_child"]').val();

            var data = {
              action: "admin_check_availability",
              from: from,
              to: to,
              adult: adult,
              child: child,
              order_id: $(this).attr('data-id')
            };
            $.post(ajaxurl, data, function(result) {
              $(".apb-get_action").hide();
              var data_result = result;
              $(".apb-list-room-js").html(data_result);
              $(".spinner").hide();
              $(".apb-add-new-item").fadeIn(100);

              $(".apb-check").each(function(){
                $(".room-"+$(this).attr('data-id')).hide();
              });
            });

            $(".apb-action-cancel").fadeIn(100);
          } else if (action == 'remove') {
            var order_id = new Array();
            $(".apb-check:checked").map(function(_, el) {
              order_id.push( $(el).val() );
            });
            if ( ! order_id.length ) {
              return;
            }

            var data = {
              action: 'Controller_delete_new_item_booking',
              order_id: order_id,
            };
            $.post(ajaxurl, data, function(result) {
              $(".apb-get_action").hide();
              $(".apb-check:checked").map(function(_, el) {
                $( '.item-order-' + $(el).val() ).fadeOut(100);
              });
            });
          }

        });

        $(".apb-action-cancel").click(function() {
          $("select[name=apb-booking-bulk-actions]").removeAttr('disabled');
          $("input[name=save]").removeAttr('disabled');
          $(".apb-list-room-js").html('');
          $(".apb-add-new-item").fadeOut(100);
          $(this).fadeOut(100);
        });
      },

      AddNewItemBooking : function(){
        $(".add-new-item-booing").click(function(){
          $(".add-load-js").show();
          var from = $("input[name=from]").val();
          var to = $("input[name=to]").val();
          var order_id = $("input[name=order_current]").val();
          var room_select = $(".rooms").val();
          var room_adult = new Array();
          $("select[name=room_adult]").map(function(_,el){
            room_adult.push({adult: $(el).val()});
          });
          var room_child = new Array();
          $("select[name=room_child]").map(function(_,el){
            room_child.push({child: $(el).val()});
          });
          var data = {
            action: "Controller_add_new_item_booking",
            from: from,
            to: to,
            room_adult:room_adult,
            room_child:room_child,
            order_id: order_id,
            room_id: room_select
          };
          $.post(ajaxurl, data, function(result) {
            $(".add-load-js").hide();
          });

        });
      },

      selectCustomer: function() {
        $('#apb-select-customer').on('change', function() {
          if (!parseInt($(this).val())) {
            $('#apb-customer-name-email').show();
          } else {
            $('#apb-customer-name-email').hide();
          }
        });
      }

    }
    ApbBookings.init();
  });
})(jQuery);

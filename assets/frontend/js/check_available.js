(function($) {
  "use strict";

  var ApbCheckAvailable = {

    init: function(){
      this.CheckAvailable();
      this.AutoCheck();
      this.UserCheckPackage();
      this.SingleRoomCheck();
       // this.GeneralRoomSelect();
      this.Pagination();
    },
    CheckAvailable : function(){
      $('#awe-plugin-booking').on('click', '.check-avb-js', function(e) {
        e.preventDefault();

        $('#preloader').show();

        // Change next step.
        $('.apb-step-one').addClass('active');
        $('.apb-step-two').removeClass('active');
        $('.apb-step-one').removeClass('step-complete');
        $('.apb-step-two').removeClass('step-complete');

        // Begin process check available.
        apbAjax.order_status = 0;
        var $btn = $(this);
        var $form = $btn.closest('form');
        var num_room = $form.find('.total-room-js').val();
        var num_args = $btn.attr('data-num-args');
        var from = $form.find('.date-start-js').val();
        var to = $form.find('.date-end-js').val();
        var room_type_id = parseInt( $form.find('*[name="room_type_id"]').val() );

        var room_adult = new Array();
        $form.find('select.apb-adult-select').map(function(_,el){
          room_adult.push({adult: $(el).val()});
        });

        var room_child = new Array();
        $form.find('select.apb-child-select').map(function(_,el){
          room_child.push({child: $(el).val()});
        });

        if (from.length == 0) {
          e.preventDefault();
          $form.find( '.date-start-js' ).focus();
          $('#preloader').hide();
        } else if (to.length == 0) {
          e.preventDefault();
          $form.find( '.date-end-js' ).focus();
          $('#preloader').hide();
        } else {
          var d1 = new Date( from );
          var d2 = new Date(to);
          var date_current = new Date();
          date_current.setDate( date_current.getDate() + parseInt( apb_day_book ) );

          var fixMonth = new Array();
              fixMonth[0] = "1";
              fixMonth[1] = "2";
              fixMonth[2] = "3";
              fixMonth[3] = "4";
              fixMonth[4] = "5";
              fixMonth[5] = "6";
              fixMonth[6] = "7";
              fixMonth[7] = "8";
              fixMonth[8] = "9";
              fixMonth[9] = "10";
              fixMonth[10] = "11";
              fixMonth[11] = "12";
          var get_day_current = fixMonth[date_current.getMonth()] + '/' + date_current.getDate() + '/' + date_current.getFullYear();

          /**
           * Start book after num day setting admin.
           */
          if ( Date.parse(from) >= Date.parse(get_day_current) || apb_day_book == 0 ) {

            // $('#preloader').show();
            if ( num_args == 0 ) {
              // ApbCheckAvailable.removeCart();
              ApbCheckAvailable.UserRoomSelect(from, to, room_adult, room_child, 'remove');
            }

            var data = {
              action: 'apb_check_available',
              from: from,
              to: to,
              adult: room_adult[ num_args ].adult,
              child: room_child[ num_args ].child,
              num_args: num_args,
            };

            if ( room_type_id ) {
              data.room_type_id = room_type_id;
            }

            if (apbAjax.lang) {
              data.lang = apbAjax.lang;
            }

            // console.log(room_adult, data);
            $.post( apbAjax.ajax_url, data, function(result) {
              var data_result = result;
              $('.apb-content-js').html(data_result);
              $('#preloader').hide();
              $('#awe-plugin-booking').trigger('apb-checked-availability');
            } );
          } else {
            $('.apb-content-js').html(apbAjax.textErrorCheckAfterDay);
            $('#preloader').hide();
          }
        }

      });


      $('.apb-check-avb-form.non-ajax').on('submit', function(e) {
        var $from = $(this).find('.date-start-js');
        var from = $from.val();

        var $to = $(this).find('.date-end-js');
        var to = $to.val();

        var $adult = $(this).find('select.apb-adult-select');
        var adult = $adult.val();

        var $child = $(this).find('select.apb-child-select');
        var child = $child.val();

        var room_type_id = $(this).find('input[name="room_type_id"]').val();

        if ( ! from.length ) {
          $from.focus();
          e.preventDefault();
          return false;
        }

        if ( ! to.length ) {
          $to.focus();
          e.preventDefault();
          return false;
        }

        if ( ! parseInt( adult ) ) {
          $adult.focus();
          e.preventDefault();
          return false;
        }
      });


      $(document).on('click', '.change-all-room-btn', function(e) {
        e.preventDefault();
        $('#preloader').show();
        $(this).closest('.apb-layout').find('.apb-widget-area .check-avb-js').click();
      });
    },
    AutoCheck : function(){
      $.fn.hasAttr = function(name) {
         return this.attr(name) !== undefined;
      };
      $(window).load(function(){
        if ( $('.apb-check-avb-form.auto-check').length ) {
          $('.check-avb-js').click();
        }
      });

    },

    removeCart: function() {
      var data = {
        action: 'apb_remove_cart',
      };
      $.ajax({
        url: apbAjax.ajax_url,
        data: data,
        type: 'post',
        async: false,
        success: function(result) {

        },
      });
    },

    UserRoomSelect : function(from,to,room_adult,room_child,control) {
      $('#preloader').show();
      var room_select = {
        action: "user_room_select",
        room_adult: room_adult,
        room_child: room_child,
        from: from,
        to: to,
        control: control,
      };

      $.ajax({
        url: apbAjax.ajax_url,
        data: room_select,
        type: 'post',
        async: false,
        success: function(result) {
          var data = result;
          $(".room-select-js").html(data);
        },
      });
      // $.post(apbAjax.ajax_url, room_select, function(result) {

      // });
    },

    UserCheckPackage: function() {

      function plush_price( operation, price_room_current, price_package, num_package ) {
        var total_price = '';
        switch(operation) {
          case 'add':
            total_price = price_room_current + (price_package * num_package );
            break;

          case 'sub':
            total_price = price_room_current - (price_package * num_package );
            break;

          case 'replace':
            total_price = price_room_current * num_package;
            break;

          case 'increase':
            total_price = price_room_current - (price_package * num_package) / 100 * price_room_current;
            break;

          case 'decrease':
            total_price =  price_room_current + (price_package * num_package) / 100 * price_room_current;
            break;
          }

        return total_price;
      }

      function sub_price( operation, price_room_current,price_package,num_package ) {
        var total_price = "";
        switch(operation) {
          case 'add':
            total_price = price_room_current - (price_package * num_package);
          break;
          case 'sub':
            total_price = price_room_current + (price_package * num_package);
          break;
          case 'replace':
            total_price = price_package * num_package;
            break;
          case 'increase':
            total_price = price_room_current + (price_package*num_package) / 100 * price_room_current;
            break;
          case 'decrease':
            total_price =  price_room_current - (price_package*num_package) / 100 * price_room_current;
            break;
        }
        return total_price;
      }


      $('#awe-plugin-booking').on('click', '.package-check-js', function() {
        var $check = $(this);
        var roomId = $check.attr('data-id');
        var daily = parseInt( $check.attr('data-daily') );
        var $room = $check.closest('.apb-room_item-' + roomId);
        var package_id = $check.attr('id');

        var $priceNightText = $room.find('.apb-price-' + roomId);
        var $priceNight = $room.find('.room-price-base-' + roomId);
        var $priceTotalText = $room.find('.apb-total-all-price-' + roomId);
        var $priceTotal = $room.find('.total-price-room-' + roomId);

        var numberOfDays = parseInt( $check.attr('data-days') );
        var priceNight = parseFloat( $priceNight.val() );
        var priceTotal = parseFloat( $priceTotal.val() );
        var pricePackage = parseFloat( $check.attr('data-pricing') );

        // Argument for price format.
        var decimals = $priceTotal.attr('data-decimals');
        var decimalSep = $priceTotal.attr('data-decimal-sep');
        var thousandSep = $priceTotal.attr('data-thousand-sep');
        var currency = $priceTotal.attr('data-currency');
        var currencyPos = $priceTotal.attr('data-currency-pos');

        // Add to popup.
        var $popup_content = $('#apb-modal-' + roomId).find('.apb-modal-body');
        var $package_div = $popup_content.find('.apb-list-price-package');
        var $package_list = $package_div.find('ul');
        var package_text = $check.closest('.apb-package_item').find('.amount').text();
        var day_text = $check.closest('.apb-package_item').find('.day').text();
        console.log('123');
        var package_name = $('label[for="' + package_id + '"]').text();
        var template = _.template($('#apb-popup-package-price-tpl').html());

        if ( this.checked ) {
          $check.closest('.apb-package_item').find('.package-input').show();
          var numberOfPackages = parseInt( $check.closest('.apb-package_item').find('.number-of-packages').val() );
          if ( isNaN( numberOfPackages ) ) {
            numberOfPackages = 0;
          }

          if ( daily ) {
            // Daily package.
            // $priceNight.val( priceNight + pricePackage );

            $priceTotal.val( priceTotal + pricePackage * numberOfDays * numberOfPackages );
            $priceTotalText.text( awebookingPriceFormat( priceTotal + pricePackage * numberOfDays * numberOfPackages, decimals, decimalSep, thousandSep, currency, currencyPos ) );
          } else {
            // Normal package.

            $priceTotal.val( priceTotal + pricePackage * numberOfPackages );
            $priceTotalText.text( awebookingPriceFormat( priceTotal + pricePackage * numberOfPackages, decimals, decimalSep, thousandSep, currency, currencyPos ) );
          }

          // Add to popup.
          if ( ! $package_div.is(':visible') ) {
            $package_div.show();
          }

          if ( $('#list-price-item-' + package_id).length ) {
            $('#list-price-item-' + package_id).remove();
          }

          if (numberOfPackages != 0) {
            $package_list.append(template({
              package_id: package_id,
              package_name: package_name,
              package_num: numberOfPackages,
              price_text: package_text,
              day_text: day_text,
            }));
          }
        } else {
          $check.closest('.apb-package_item').find('.package-input').hide();
          var numberOfPackages = parseInt( $check.closest('.apb-package_item').find('.number-of-packages').val() );
          if ( isNaN( numberOfPackages ) ) {
            numberOfPackages = 0;
          }

          if ( daily ) {
            // Daily package.
            // $priceNight.val( priceNight - pricePackage );
            // $priceNightText.text( awebookingPriceFormat( priceNight - pricePackage, decimals, decimalSep, thousandSep, currency, currencyPos ) );

            $priceTotal.val( priceTotal - pricePackage * numberOfDays * numberOfPackages );
            $priceTotalText.text( awebookingPriceFormat( priceTotal - pricePackage * numberOfDays * numberOfPackages, decimals, decimalSep, thousandSep, currency, currencyPos ) );
          } else {

            // Normal package.
            $priceTotal.val( priceTotal - pricePackage * numberOfPackages );
            $priceTotalText.text( awebookingPriceFormat( priceTotal - pricePackage * numberOfPackages, decimals, decimalSep, thousandSep, currency, currencyPos ) );
          }

          // Remove from popup.
          if ( $('#list-price-item-' + package_id).length ) {
            $('#list-price-item-' + package_id).remove();
          }

          if ( ! $package_list.find('li').length ) {
            $package_div.hide();
          }
        }
      });


      $('#awe-plugin-booking').on('keyup', '.number-of-packages', function() {
        var $this = $(this),
            $item = $this.closest('.apb-package_item'),
            $check = $item.find('.package-check-js');

        if (!$check.prop('checked')) {
          return;
        }

        var step = $this.attr('step') ? parseInt($this.attr('step')) : 1,
            number = $this.val() ? parseInt($this.val()) : 0,
            roomId = $check.attr('data-id'),
            $room = $check.closest('.apb-room_item-' + roomId),
            $priceTotal = $room.find('.total-price-room-' + roomId),
            $priceTotalText = $room.find('.apb-total-all-price-' + roomId),
            numberOfDays = parseInt( $check.attr('data-days') ),
            priceTotal = parseFloat( $priceTotalText.attr('data-value') ),
            pricePackage = parseFloat( $check.attr('data-pricing') );

        var decimals = $priceTotal.attr('data-decimals');
        var decimalSep = $priceTotal.attr('data-decimal-sep');
        var thousandSep = $priceTotal.attr('data-thousand-sep');
        var currency = $priceTotal.attr('data-currency');
        var currencyPos = $priceTotal.attr('data-currency-pos');

        var newTotalPrice = parseFloat($priceTotalText.attr('data-value'));

        $room.find('.package-check-js').each(function() {
          if (!$(this).prop('checked')) {
            return;
          }

          var $item = $(this).closest('.apb-package_item');
          var number = $item.find('.number-of-packages').val() ? parseInt($item.find('.number-of-packages').val()) : 0;
          var pricePackage = parseFloat( $(this).attr('data-pricing') );

          if (parseInt($(this).attr('data-daily'))) {
            newTotalPrice += number * pricePackage * numberOfDays;
          } else {
            newTotalPrice += number * pricePackage;
          }
        });

        $priceTotal.val( newTotalPrice );
        $priceTotalText.text( awebookingPriceFormat( newTotalPrice, decimals, decimalSep, thousandSep, currency, currencyPos ) );

        // Change on popup.
        var $popup_content = $('#apb-modal-' + roomId).find('.apb-modal-body');
        var $package_div = $popup_content.find('.apb-list-price-package');
        var $package_list = $package_div.find('ul');
        var package_id = $check.attr('id');
        var package_text = $check.closest('.apb-package_item').find('.amount').text();
        var day_text = $check.closest('.apb-package_item').find('.day').text();
        var package_name = $('label[for="' + package_id + '"]').text();
        var template = _.template($('#apb-popup-package-price-tpl').html());
        if ( $('#list-price-item-' + package_id).length ) {
          $('#list-price-item-' + package_id).remove();
        }

        if ( number != 0 ) {
          $package_list.append(template({
            package_id: package_id,
            package_name: package_name,
            package_num: number,
            price_text: package_text,
            day_text: day_text,
          }));
        }

        if ( ! $package_list.find('li').length ) {
          $package_div.hide();
        }
      });

    },
    SingleRoomCheck : function(){
      $('.apb-single-check-avb-form').submit(function(e) {
        var $from = $(this).find('.date-start-js');
        var from = $from.val();

        var $to = $(this).find('.date-end-js');
        var to = $to.val();

        var $adult = $(this).find('select.apb-adult-select');
        var adult = $adult.val();

        var $child = $(this).find('select.apb-child-select');
        var child = $child.val();

        var room_type_id = $(this).find('input[name="room_type_id"]').val();

        if ( ! from.length ) {
          $from.focus();
          e.preventDefault();
          return false;
        }

        if ( ! to.length ) {
          $to.focus();
          e.preventDefault();
          return false;
        }

        if ( ! parseInt( adult ) ) {
          $adult.focus();
          e.preventDefault();
          return false;
        }
      });
    },
    count_days: function(date1, date2) {
        var each_day = 1000 * 60 * 60 * 24;//milliseconds in a day
        var ms_date1 = date1.getTime();//milliseconds for date1
        var ms_date2 = date2.getTime();//milliseconds for date2
        var ms_date_diff = Math.abs(ms_date1 - ms_date2);//different of the two dates in milliseconds
        var days = Math.round(ms_date_diff / each_day);//divided the different with millisecond in a day
        return days;
    },
    GeneralRoomSelect: function(){
      $(".total-room-js").change(function() {
        var data = {
          action: 'apb_gen_room_select',
        };
        $.post(apbAjax.ajax_url, data, function(result) {
          var data_result = result;
          $(".list-room").append(data_result);
        });
      })
    },
    Pagination : function(){
      $('#awe-plugin-booking').on('click', '.paged-room-js', function() {
        var from = $('.date-start-js').val();
        var to = $('.date-end-js').val();

        var room_adult = new Array();
        $('select[name=room_adult]').map(function(_, el) {
          room_adult.push({
            adult: $(el).val()
          });
        });

        var room_child = new Array();
        $('select[name=room_child]').map(function(_, el) {
          room_child.push({
            child: $(el).val()
          });
        });

        var paged = $(this).attr('data-page');
        $("#preloader").show();
          var data = {
            action: "user_check_available",
            from: from,
            to: to,
            room_adult:room_adult,
            room_child:room_child,
            control: "pagination",
            paged:paged,
            num_args: $(".check-list-cart-js").length,
          };
          $.post(apbAjax.ajax_url, data, function(result) {
           var data_result = JSON.parse(result);
            $(".apb-content-js").html(data_result);
            $("#preloader").hide();
          });
        return false;
      });
    }
  }
   ApbCheckAvailable.init();
})(jQuery);

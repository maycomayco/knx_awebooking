(function($) {
  "use strict";

  /*==============================
    Main
  ==============================*/

  function main() {
    $('.total-room-js').each(function() {
      var $form = $(this).closest('form');
      var total_select = parseInt($(this).val());
      var list_room = $form.find('.apb-sidebar_group');

      if (total_select == 1) {
        list_room.find('.label-group').hide();
      } else {
        list_room.find('.label-group').show();
      }
    });
  }

  /* Datepicker */
  DatePicker();
  function DatePicker() {
    var apb_get_day_advance = (typeof apb_day_book != 'undefined') ? parseInt(apb_day_book) : 0;
    var date_format = (jQuery.datepicker.regional[apb_lang].dateFormat) ? jQuery.datepicker.regional[apb_lang].dateFormat : 'mm/dd/yy';
    var apb_number_of_calendar = (typeof apbAjax.apb_number_calendar != 'undefined') ? parseInt(apbAjax.apb_number_calendar) : 2;
    $('.date-start-js').datepicker({
      dateFormat: date_format,
      buttonImageOnly: false,
      //defaultDate: "+1w",
      numberOfMonths: apb_number_of_calendar,
      minDate : apb_get_day_advance,

      onClose: function( selectedDate ) {
        var $this = $(this);
        var $form = $this.closest('form');
        if ( $('.apb-format-date').length == 0 ) {
          $form.append('<input type="hidden" name="apb_formatdate" class="apb-format-date" value="' + $.datepicker._defaults.dateFormat + '">');
        }
        if (selectedDate) {

          var timeConvert = new Date( $this.datepicker('getDate').getTime() );
          var number_nights = $form.find('.night-select-js').length ? parseInt( $form.find('.night-select-js').val() ) : 1;

          var $date_end = $form.find('.date-end-js');
          var date_end = new Date(timeConvert);
          date_end.setDate(date_end.getDate() + number_nights);

          $date_end.val($.datepicker.formatDate($.datepicker._defaults.dateFormat,new Date(date_end)));
          $date_end.attr("data-date",$.datepicker.formatDate('mm/dd/yy',new Date(date_end)));

          /*----------  Add Date Customize  ----------*/
          $this.attr("data-date",$.datepicker.formatDate('mm/dd/yy',new Date(timeConvert)));
          /*----------  Add Date Customize  ----------*/

          /*----------  Relationship to calendar  ----------*/
          $this.trigger('change');
          $('#apb_calendar').datepicker('refresh');
          $('#apb_calendar').datepicker('setDate', $.datepicker.formatDate( 'mm/dd/yy', new Date(timeConvert) ) );
          $('td.ui-datepicker-current-day a.ui-state-default').removeClass('ui-state-active');

          $form.find('.date-end-js').datepicker( "option", "minDate", date_end );
        }

      },
      beforeShow: function(input, inst) {
        $('#ui-datepicker-div').addClass('apb-datepicker');
      }
    });

    $('.date-end-js').datepicker({
      dateFormat: date_format,
      buttonImageOnly: false,
      //defaultDate: "+1w",
      numberOfMonths: apb_number_of_calendar,
      minDate : apb_get_day_advance,
      onClose: function( selectedDate ) {
        var $this = $(this);
        var $form = $this.closest('form');
        if ( $('.apb-format-date').length == 0 ) {
          $form.append('<input type="hidden" name="apb_formatdate" class="apb-format-date" value="' + $.datepicker._defaults.dateFormat + '">');
        }
        if (selectedDate) {

          var timestamp = $this.datepicker('getDate').getTime() / 1000;
          var timeConvert = new Date( timestamp * 1000 );
          $this.attr('data-date', $.datepicker.formatDate( 'mm/dd/yy', new Date(timeConvert) ) );

          var $date_start = $form.find('.date-start-js');
          if ( ! $date_start.val().length ) {
            return;
          }
          var date_start = new Date( $date_start.datepicker('getDate').getTime() );

          if ( $form.find('.night-select-js').length ) {
            var number_nights = ( timestamp * 1000 - $date_start.datepicker('getDate').getTime() ) / 1000 / 60 / 60 / 24;

            if ( number_nights > $form.find('.night-select-js option').length ) {
              $form.find('.night-select-js').html('');
              for( var i = 1; i <= number_nights; i++ ) {
                $form.find('.night-select-js').append(
                  $('<option/>', {
                    value: i,
                    text : i,
                  })
                );
              }
            }

            $form.find('.night-select-js').val(number_nights);
          }

          /*----------  Relationship to calendar  ----------*/
          $(this).trigger('change');
          $('#apb_calendar').datepicker('refresh');
          $('#apb_calendar').datepicker('setDate', $.datepicker.formatDate( 'mm/dd/yy', new Date(timeConvert) ) );
          $('td.ui-datepicker-current-day a.ui-state-default').removeClass('ui-state-active');
        }
      },
      beforeShow: function(input, inst) {
         $('#ui-datepicker-div').addClass('apb-datepicker');
       }
    });

    $(".night-select-js").change(function(e) {
      var $this = $(this);
      var $form = $this.closest('form');
      if ( $form.find('.date-start-js').val() == '' ) {
        e.preventDefault();
        $form.find('.date-start-js').focus();
      } else {
        var start_date = $form.find('.date-start-js').datepicker('getDate').getTime() / 1000;;
        var timeConvert = new Date(start_date * 1000);
        var dateNight = new Date(timeConvert);
        dateNight.setDate( dateNight.getDate() + parseInt( $(this).val() ) );

        $form.find('.date-end-js').attr('data-date', $.datepicker.formatDate( date_format, dateNight ));
        $form.find('.date-end-js').val( $.datepicker.formatDate( date_format, dateNight ) );

        $('#apb_calendar').datepicker('refresh');
        $('#apb_calendar').datepicker('setDate', $.datepicker.formatDate('mm/dd/yy',new Date(timeConvert)) );
        $('td.ui-datepicker-current-day a.ui-state-default').removeClass('ui-state-active');
      }

    });

    $('#apb_calendar').datepicker({
      dateFormat: "mm/dd/yy",
      buttonImageOnly: false,
      //defaultDate: "+1w",
      numberOfMonths: [1, 2],
      minDate : apb_get_day_advance,
      beforeShowDay: function(date) {
        var $date_start = $(".date-start-js");
        var $date_end = $(".date-end-js");
        var date = new Date(date);
        var date1 =  new Date($date_start.attr('data-date'));
        var date2 = new Date($date_end.attr('data-date'));
        return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "apb-highlight" : ""];
      },
      onSelect: function(dateText, inst) {

        var $date_start = $(".date-start-js");
        var $date_end = $(".date-end-js");
        var date1 =  $(".date-start-js").attr('data-date');
        var date2 = $(".date-end-js").attr('data-date');

        if ( ! date1 || date2 ) {
          $(".date-start-js").val($.datepicker.formatDate(date_format,new Date(dateText)));
          $(".date-start-js").attr('data-date',dateText)
           // $(".date-start-js" ).datepicker( "setDate", dateText );
          $date_end.val("");
          $date_end.attr('data-date','');
          $date_end.datepicker( "setDate", '' );
        } else {

          var d1 = new Date(date1);
          var d2 = new Date(dateText);
          if (d1 < d2) {
            $date_end.val($.datepicker.formatDate(date_format,new Date(dateText))).trigger('change');
            $date_end.attr('data-date',dateText).trigger('change');
            // $(".date-end-js" ).datepicker( "setDate", $.datepicker.formatDate(jQuery.datepicker._defaults.dateFormat,new Date(dateText)) );
            var day_plush = new Array();

            $('.night-select-js option').map(function(_, el) {
              if ( this.value < count_days(d1, d2) ) {
                day_plush.push( count_days(d1, d2) );
              }
            });

            if ( count_days(d1, d2) > $('.night-select-js option').length ) {
              $('.night-select-js').html("");
              for(var i=1; i<=day_plush[0]; i++){
                $('.night-select-js').append($('<option/>', {
                  value: i,
                  text : i
                }));
              }
            }
            $('.night-select-js').val( count_days(d1, d2) );
          }
        }
      },
    });
  }

  function count_days(date1, date2) {
    var each_day = 1000 * 60 * 60 * 24;//milliseconds in a day
    var ms_date1 = date1.getTime();//milliseconds for date1
    var ms_date2 = date2.getTime();//milliseconds for date2
    var ms_date_diff = Math.abs(ms_date1 - ms_date2);//different of the two dates in milliseconds
    var days = Math.round(ms_date_diff / each_day);//divided the different with millisecond in a day
    return days;
  }

  /*abp Product Thumbs*/
  apbProductThumbs();
  function apbProductThumbs() {
    if ( ! $.fn.owlCarousel ) return;

    var $sync1 = $('.apb-product_image');
    var $sync2 = $('.apb-product_thumb');

    $sync1.owlCarousel({
      singleItem: true,
      slideSpeed: 500,

      navigation: false,
      pagination: false,

      autoHeight: true,
      theme: 'apb-owl-carousel',
      autoPlay: 5000,

      responsiveRefreshRate: 200,
      afterAction: function(el) {
        $sync2.find('.owl-item').removeClass('synced').eq(this.currentItem).addClass('synced');
      },
    });

    $sync2.owlCarousel({
      items: 9,
      itemsDesktop: [1199, 9],
      itemsDesktopSmall: [991, 6],
      itemsTablet: [767, 5],
      itemsMobile: [480, 3],

      pagination: false,
      navigation: true,
      theme: 'apb-owl-carousel',

      responsiveRefreshRate: 100,
      afterInit: function(el){
        el.find('.owl-item').eq(0).addClass('synced');
      }
    });

    $sync2.on('click', '.owl-item', function(e) {
      e.preventDefault();
      $sync1.trigger('owl.goTo', $(this).data('owlItem'));
    });
  }

  gen_room();
  function gen_room() {
    $('.total-room-js').change(function() {

      var $form = $(this).closest('form');
      var total_select = parseInt( $(this).val() );
      var list_room = $form.find('.apb-sidebar_group');

      if (total_select == 1) {
        list_room.find('.label-group').hide();
      } else {
        list_room.find('.label-group').show();
      }

      var html = '';
      var _for = 0;
      if ( list_room.length < total_select ) {
        _for = total_select - list_room.length;
      } else {
        var i_remove = total_select;
        do {
          $form.find('.apb-people-box-' + ( ( i_remove++ ) + 1 ) ).remove();
        } while (i_remove <= list_room.length);
      }

      var maxAdult = apbAjax.maxAdult;
      var maxChild = apbAjax.maxChild;

      var $selectAdult = '<select name="room_adult[]" class="apb-select apb-adult-select">';
      for ( var i = 1; i <= maxAdult; i++ ) {
        $selectAdult += '<option>' + i + '</option>';
      }
      $selectAdult += '</select>';

      var $selectChild = '<select name="room_child[]" class="apb-select apb-child-select">';
      for ( var i = 0; i <= maxChild; i++ ) {
        $selectChild += '<option>' + i + '</option>';
      }
      $selectChild += '</select>';

      for (var i = 1; i <= _for;  i++) {
        html += '<div class="apb-sidebar_group apb-people-box-' + ( i + list_room.length ) + '">'
                + '<span class="label-group">' + apbAjax.textRoom + ' ' + ( i + list_room.length ) + '</span>'
                + '<div class="apb-field_group">'
                  + '<div class="apb-field">'
                    + '<label>' + apbAjax.textAdult + '</label>'
                    + '<div class="apb-field-group"><i class="apbf apbf-select"></i>' + $selectAdult + '</div>'
                  + '</div>'

                  + '<div class="apb-field">'
                    + '<label>' + apbAjax.textChild + '</label>'
                    + '<div class="apb-field-group"><i class="apbf apbf-select"></i>' + $selectChild + '</div>'
                  + '</div>'
                + '</div>'
              + '</div>';
      }
      $form.find('.list-room').append(html);
    });
  }

  // SubmitCheckAvailable();
  function SubmitCheckAvailable(){

    $(".apb-checkavailable-submit").submit(function(event){
      var from = $(".date-start-js").val();
      var to = $(".date-end-js").val();
      if (from.length == 0) {
        event.preventDefault();
        $( ".date-start-js" ).focus();
      } else if(to.length == 0) {
        event.preventDefault();
        $( ".date-end-js" ).focus();
      }

    });
  }

  // Modal
  $(document).on('click', '[data-toggle="abp-modal"]', function(e) {
    e.preventDefault();
    var target = $(this).data('target');

    if (target && $(target).length) {
      $(target).fadeIn(150);
      $('body').addClass('apb-modal-open');

      setTimeout(function() {
        $(target).addClass('in');
      }, 50);
    }
  });

  $(document).on('click', '.apb-modal', function(e) {
    e.preventDefault();
    var self = this;

    if ($(e.target).hasClass('apb-modal-content') || $(e.target).closest('.apb-modal-content').length) {
      return;
    }

    $(self).removeClass('in');
    $('body').removeClass('apb-modal-open');

    setTimeout(function() {
      $(self).fadeOut(150);
    }, 100);
  });

  $(document).on('click', '[data-toggle="abp-close-modal"]', function() {
    $('.apb-modal').trigger('click');
    return false;
  });

  $(document).ready(function() {
    main();
  });

})(jQuery);

(function($) {
  "use strict";

  var FC = $.fullCalendar; // a reference to FullCalendar's root namespace
	var BasicView = FC.BasicView;      // the class that all views must inherit from
	var SingleRowMonthView;

	SingleRowMonthView = BasicView.extend({
		renderDates: function() {
			// this.dayNumbersVisible = this.dayGrid.rowCnt > 1; // TODO: make grid responsible
			this.dayNumbersVisible = true;
			this.weekNumbersVisible = this.opt('weekNumbers');
			this.dayGrid.numbersVisible = true;

			this.el.addClass('fc-basic-view').html(this.renderSkeletonHtml());
			this.renderHead();

			this.scrollerEl = this.el.find('.fc-day-grid-container');

			this.dayGrid.setElement(this.el.find('.fc-day-grid'));
			this.dayGrid.renderDates(this.hasRigidRows());
		},
	});

	FC.views.singleRowMonth = {
		class: SingleRowMonthView,
		type: 'basic',
		duration: {
			days: 31,
		},
	};

  $(document).ready(function() {
    var mange_pricing =  {
      init: function() {
        this.GenListCalendar();
        this.OpenForm();
        this.UpdatePricing();
      },

      data_init: {
        awe_date_current: awe_date_current,
      },

      OpenForm: function(){
        var x = false;
        $('.form-update-pricing-js').click(function(e) {
          e.preventDefault();

          x = !x;
          if ( x == true ) {
            $('.update-pricing').slideDown();
          } else {
            $('.update-pricing').slideUp();
          }
        });
      },

      GenListCalendar: function() {
        var list = $('.rooms-month-manager').find('.manage-pricing');
        list.each(function(i) {
          var calender_id = list[i].getAttribute('id');
          var post_id     = list[i].getAttribute('data-id');

          $('#' + calender_id).fullCalendar({
            header:{
              left: 'title',
              center: '',
              right: '',
            },
            ignoreTimezone: false,
            handleWindowResize: true,
            editable: false,
            contentHeight: 120,
            defaultView:'singleRowMonth',
            defaultDate: mange_pricing.data_init.awe_date_current,
            events: function(start, end, timezone, callback) {
              var data = {
                action: "get_pricing",
                date: mange_pricing.data_init.awe_date_current,
                room_id: post_id,
              };
              $.post(ajaxurl, data, function(reuslt) {
                var data_result = JSON.parse(reuslt);
                callback(data_result);
              });
            },
            eventRender: function(event, el) {
              el.find('.fc-time').remove();
            },
            eventAfterRender: function( event, element, view){
              $(".fc-day-header").css('font-size','13px');
            }
          });
        });
      },

      SecondsToDay: function() {
        var today = new Date();
        var mm = today.getMonth() + 1;
        if ( mm < 10 ) {
          mm = '0' + mm;
        }
        return today.getFullYear() + '-' + mm;
      },

      UpdatePricing: function() {
        $('.awe-add-pricing-js').click(function() {
          var rooms_start_date = $('input[name=rooms_start_date]').val();
          var rooms_end_date =  $('input[name=rooms_end_date]').val();
          var day_option =  $('.get_day_js:checked').map(function(_, el) {
            return $(el).val();
          }).get();
          var _room_id = new Array();
          var room_id = $('.get_room_id_js:checked').map(function(_, el) {
            _room_id.push( $(el).val() );
          });

          if ( ! _room_id.length ) {
            return false;
          }
          var operation = $('select[name=operation]').val();
          var amount = $('input[name=amount]').val();

          if ( rooms_start_date == '' ) {
            $('#awe_datepicker_start').focus();
          } else {
            $('.spinner').show();
            var data = {
              action: 'add_pricing_for_room',
              rooms_start_date: rooms_start_date,
              rooms_end_date: rooms_end_date,
              day_option: day_option,
              operation: operation,
              amount: amount,
              room_id: _room_id,
            };

            $.post(ajaxurl, data, function(result) {
              window.location = location.href = '';
            });
          }
        });
      }
    }
    mange_pricing.init();

  });
})(jQuery);

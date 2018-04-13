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

    var mange_pricing = {
      init: function(){
        this.GenListCalendar();
        this.OpenForm();
        this.UpdateAvailability();
        this.DeleteOrder();
      },

      data_init: {
        awe_date_current: awe_date_current,
      },

      OpenForm: function(){
        var x = false;
        $(".form-update-pricing-js").click(function(e) {
          e.preventDefault();

          x = !x;
          if (x == true) {
            $(".update-pricing").slideDown();
          } else {
            $(".update-pricing").slideUp();
          }
        });
      },

      GenListCalendar: function(){
        var list = $(".rooms-month-manager").find('.manage-avb');
        list.each(function(i){
          var calender_id   = list[i].getAttribute("id");
          var post_id     = list[i].getAttribute("data-id");
          $("#" + calender_id).fullCalendar({
            header:{
              left: 'title',
              center: '',
              right: '',
            },
            ignoreTimezone: false,
            handleWindowResize: true,
            editable:false,
            contentHeight: 120,
            defaultView:'singleRowMonth',
            defaultDate: mange_pricing.data_init.awe_date_current,
            events: function(start, end, timezone, callback) {
              var data = {
                action: "get_availability_2_2",
                date: mange_pricing.data_init.awe_date_current,
                room_id: post_id,
                room_status: 'apb-all',
              };
              $.post(ajaxurl, data, function(result) {
                // var data_result = JSON.parse(result);
                // console.log(result); 
                callback(result);
              });
            },

            eventClick: function(calEvent, jsEvent, view) {
              // Getting the Unix timestamp - JS will only give us milliseconds
              if (calEvent.end === null) {
                // We are probably dealing with a single day event
                calEvent.end = calEvent.start;
              }

              var sd = calEvent.start.unix();
              var ed = calEvent.end.unix();
              $(".box-event-info-js").show();
              mange_pricing.GetInfoEvent(sd,ed,post_id);

              // Open the modal for edit
            },
            eventRender: function(event, el) {
              el.find('.fc-time').remove();
            },
            eventAfterRender: function( event, element, view){
              $(".fc-day-header").css('font-size','13px');
              // if(element.css('margin-top') == "1px"){
              //   element.css('margin-bottom','-22px');
              // }
              // Event width.
              var width = element.parent().width()
              // Event colspan number.
              var colspan = element.parent().get(0).colSpan;
              // Single cell width.
              var cell_width = width/colspan;
              var half_cell_width = cell_width/2;

              // Move events between table margins.
              element.css('margin-left', half_cell_width);
              element.css('margin-right', half_cell_width * -1);

              // Calculate width event to add end date triangle.
              var width_event = element.children('.fc-content').width();

              // Add a margin left to the top triangle.
              element.children().closest('.event-end').css('margin-left', width_event-23);

              // If the event end in a next row.
              if (element.hasClass('fc-not-end')) {
                element.css('margin-right', 0);
              }
              // If the event start in a previous row.
              if (element.hasClass('fc-not-start')) {
                // Fixes to work well with jquery 1.7.
                if (colspan == 1) {
                  width_event = 0;
                }
                element.css('margin-left', 0);
                element.children().closest('.event-end').css('margin-left', width_event);
              }
            }
          });
        });
      },

      SecondsToDay: function(){
        var today = new Date();
        var mm = today.getMonth()+1;
        if (mm < 10) {
          mm = '0' + mm;
        }
        return today.getFullYear() + '-' + mm;
      },

      GetInfoEvent: function(from,to,post_id){
        $(".content-event-js").html('');
        $(".apb-get-event").show();
          var data = {
            action: "get_info_event",
            from: from,
            to: to,
            room_id: post_id,
            book_id: ''
          };
          $.post(ajaxurl, data, function(result) {
             //console.log(result);
            var data_result = result;
            $(".content-event-js").html(data_result);
            $(".apb-get-event").hide();

            $('.room_translate_name').html($('label[for="room-'+post_id+'"]').html());
          });
        },

        DeleteOrder: function(){
          $(".awe-plugin").on('click','.room-delete-order',function(){
            var book_id = $(this).attr('data-id');
            var data = {
              action: "Controller_delete_booking",
              book_id: book_id,
            };
            $.post(ajaxurl, data, function(result) {
              var data_result = JSON.parse(result);
              if (data_result == 'yes') {
                $('.item-order-'+book_id).slideUp();
              }
            });
          });
        },

        UpdateAvailability: function() {
          $(".awe-avb-js").click(function() {
            var rooms_start_date = $("input[name=rooms_start_date]").val();
            var rooms_end_date = $("input[name=rooms_end_date]").val();
            if (rooms_start_date == "") {
              $(".date-start-js").focus();
              return this;
            }
            if (rooms_end_date == "") {
              $(".date-end-js").focus();
              return this;
            }

            var action = $(this).attr('data-value');
            if (action == 'yes') {
              var rooms_start_date  = $("input[name=rooms_start_date]").val();
              var rooms_end_date    =  $("input[name=rooms_end_date]").val();
              var unit_state        = $("select[name=unit_state]").val();
              var _room_id          = new Array();

              var day_option =  $(".get_day_js:checked").map(function(_,el){
                return $(el).val();
              }).get();

              var room_id =  $(".get_room_id_js:checked").map(function(_,el){
                _room_id.push($(el).val());
              });

              if (rooms_start_date == "") {
                $("#awe_datepicker_start").focus();
              } else {
                $(".spinner").show();
                var data = {
                  action: "add_availability_for_room",
                  rooms_start_date: rooms_start_date,
                  rooms_end_date: rooms_end_date,
                  unit_state: unit_state,
                  room_id: _room_id,
                  day_option: day_option,
                };
                $.post(ajaxurl, data, function(result) {
                  window.location = location.href = "";
                });
              }
            } else {
              $(".btn-update-status-js").slideUp();
            }

          });
       }
    }
    mange_pricing.init();

  });
})(jQuery);

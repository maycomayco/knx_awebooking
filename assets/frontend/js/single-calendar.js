(function($) {
  "use strict";

  if ( ! $('#date-available').length ) {
    return;
  }
  var single_calendar =  {
    data_init : {
      date_default_1 : awe_date_curent_1,
      date_default_2 : awe_date_curent_2,
      room_id : room_id
    },
    init: function() {
      this.fullCalendar();
    },
    fullCalendar : function() {
      $('#calendar').fullCalendar({
        header: {
          left: ( apbAjax.singleCalendarAjax ) ? 'prev' : '',
          right: "",
          center: 'title',
        },
        ignoreTimezone: false,
        editable: false,
        selectable: true,
        handleWindowResize: true,
        defaultDate: single_calendar.data_init.date_default_1,
        events: function(start, end, timezone, callback) {

          if( apbAjax.singleCalendarAjax ) {
            var prevTime = this.getDate(),
                currentTime = this.getDate();
            $('#calendar2').fullCalendar('gotoDate', prevTime.add(1, 'month').format('YYYY-MM-DD') );
            $(".apb-month").css('opacity','0.3');
          }

          var data = {
            // action: "apb_frontend_get_availability",
            action: 'apb_get_room_type_availability_color',
            date: ( apbAjax.singleCalendarAjax )  ? currentTime.format('YYYY-M') : single_calendar.data_init.date_default_1,
            room_id: single_calendar.data_init.room_id,
          };
          $.post(apbAjax.ajax_url, data, function(result) {
            var data_result = result;
            // console.log(data_result);
            callback(data_result);
            $(".apb-month").css('opacity','');
          });
          return false;
          
        },
        windowResize: function(view) {
          $('#calendar').fullCalendar('refetchEvents');
        },
        eventRender: function(event, el) {
          el.find('.fc-time').remove();
          if (el.hasClass('fc-start') && this.id == 1) {
            el.append('<div class="event-start"/>');
            el.find('.event-start').css('border-top-color', this.color);
          }

          // Add a class if the event end and it is not "AV" or "N/A".
          if (el.hasClass('fc-end') && this.id == 1) {
            el.append('<div class="event-end"/>');
            el.find('.event-end').css('border-top-color', this.color);
          }
        },
        eventAfterRender : function( event, element, view){

          if(element.css('margin-top') == "1px"){
            element.css('margin-bottom','-22px');
          }
          // Event width.
          var width = element.parent().width()
          // Event colspan number.
          var colspan = element.parent().get(0).colSpan;
          // Single cell width.
          var cell_width = width/colspan;
          var half_cell_width = cell_width/2;

          // Move events between table margins.
          element.css('margin-left', half_cell_width);
          element.css('margin-right', half_cell_width);

          // Calculate width event to add end date triangle.
          var width_event = element.children('.fc-content').width();

          // Add a margin left to the top triangle.
          element.children().closest('.event-end').css('margin-left', width_event-23);

          // If the event end in a next row.
          if ( element.hasClass('fc-not-end') ) {
            element.css('margin-right', 0);
          }
          // If the event start in a previous row.
          if ( element.hasClass('fc-not-start') ) {
            // Fixes to work well with jquery 1.7.
            if (colspan == 1) {
              width_event = 0;
            }
            element.css('margin-left', 0);
            element.children().closest('.event-end').css('margin-left', width_event);
          }
        }
     });
     $('#calendar2').fullCalendar({
        header: {
          left: '',
          right: ( apbAjax.singleCalendarAjax ) ? 'next' : '',
          center: 'title',
        },
        ignoreTimezone: false,
        editable: false,
        selectable: true,
        handleWindowResize: true,
        defaultDate: single_calendar.data_init.date_default_2,
        events: function(start, end, timezone, callback) {
          if( apbAjax.singleCalendarAjax ) {
              var nextTime = this.getDate(),
                  currentTime = this.getDate();
              $('#calendar').fullCalendar('gotoDate', nextTime.subtract(1, 'month').format('YYYY-MM-DD') );
              $(".apb-month").css('opacity','0.3');
          }
         

          var data = {
            // action: "apb_frontend_get_availability",
            action: 'apb_get_room_type_availability_color',
            date: ( apbAjax.singleCalendarAjax ) ? currentTime.format('YYYY-M') : single_calendar.data_init.date_default_2,
            room_id: single_calendar.data_init.room_id,
          };

          $.post(apbAjax.ajax_url, data, function(result) {
            // var data_result = JSON.parse(result);
            var data_result = result;
            // console.log(data_result);
            callback(data_result);
            $(".apb-month").css('opacity','');
          });
        },
        windowResize: function(view) {
          $('#calendar2').fullCalendar('refetchEvents');
        },
        eventRender: function(event, el) {
          el.find('.fc-time').remove();
          if (el.hasClass('fc-start') && this.id == 1) {
            el.append('<div class="event-start"/>');
            el.find('.event-start').css('border-top-color', this.color);
          }

          // Add a class if the event end and it is not "AV" or "N/A".
          if (el.hasClass('fc-end') && this.id == 1) {
            el.append('<div class="event-end"/>');
            el.find('.event-end').css('border-top-color', this.color);
          }
        },
        eventAfterRender : function( event, element, view){
          if(element.css('margin-top') == "1px"){
           element.css('margin-bottom','-22px');
          }
          // Event width.
          var width = element.parent().width()
          // Event colspan number.
          var colspan = element.parent().get(0).colSpan;
          // Single cell width.
          var cell_width = width/colspan;
          var half_cell_width = cell_width/2;

          // Move events between table margins.
          element.css('margin-left', half_cell_width);
          element.css('margin-right', half_cell_width);

          // Calculate width event to add end date triangle.
          var width_event = element.children('.fc-content').width();

          // Add a margin left to the top triangle.
          element.children().closest('.event-end').css('margin-left', width_event-23);

          // If the event end in a next row.
          if (element.hasClass('fc-not-end')) {
            element.css('margin-right', 0);
          }
          // If the event start in a previous row.
          if(element.hasClass('fc-not-start')) {
            // Fixes to work well with jquery 1.7.
            if (colspan == 1) {
              width_event = 0;
            }
            element.css('margin-left', 0);
            element.children().closest('.event-end').css('margin-left', width_event);
          }
        }
      });
    }
  }
  single_calendar.init();
})(jQuery);

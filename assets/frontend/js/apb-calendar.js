
(function($) {
  "use strict";
  function count_days(date1, date2) {
        var each_day = 1000 * 60 * 60 * 24;//milliseconds in a day
        var ms_date1 = date1.getTime();//milliseconds for date1
        var ms_date2 = date2.getTime();//milliseconds for date2
        var ms_date_diff = Math.abs(ms_date1 - ms_date2);//different of the two dates in milliseconds
        var days = Math.round(ms_date_diff / each_day);//divided the different with millisecond in a day

        return (days != "") ? days : 1;
    }
  apb_calendar();

  function apb_calendar(){
     $('#apb_calendar').fullCalendar({
        header: {
          left: '',
          right: "",
          center: 'title',
        },
        ignoreTimezone: false,
        selectable: true,
         editable: false,
        handleWindowResize: true,
        defaultDate: apb_script_param.awe_date_curent_one,
        select: function(start, end,allDay) {

           var ed = end.subtract(1, 'days');
           start.add('month', 1);
           end.add('month', 1);
           var day_start = new Date(start);
           var start_date = day_start.getMonth()+"/"+day_start.getDate()+"/"+day_start.getFullYear();

          var d1 = new Date(start);
          var d2 = new Date(end);

          $(".night-select-js").val(count_days(d1,d2));
          $(".date-start-js").val(start_date);

           var day_end = new Date(end);
           var end_date = day_end.getMonth()+"/"+day_end.getDate()+"/"+day_end.getFullYear();
           $(".date-end-js").val(end_date);
        },

        eventRender: function(event, el) {
          el.find('.fc-time').remove();
          },
     });
     $('#apb_calendar2').fullCalendar({
        header: {
          left: '',
          right: "",
          center: 'title',
        },
        editable: false,
        ignoreTimezone: false,
        selectable: true,
        handleWindowResize: true,
        defaultDate: apb_script_param.awe_date_curent_two,
         select: function(start, end,allDay) {
           var ed = end.subtract(1, 'days');
           start.add('month', 1);
           end.add('month', 1);
           var day_start = new Date(start);
           var start_date = day_start.getMonth()+"/"+day_start.getDate()+"/"+day_start.getFullYear();

           var d1 = new Date(start);
           var d2 = new Date(end);

           $(".night-select-js").val(count_days(d1,d2));
           $(".date-start-js").val(start_date);

           var day_end = new Date(end);
           var end_date = day_end.getMonth()+"/"+day_end.getDate()+"/"+day_end.getFullYear();
           $(".date-end-js").val(end_date);
        },
        eventRender: function(event, el) {
          el.find('.fc-time').remove();
          },
     });
  }
  function SecondsToDay(seconds){
    var second = new Date(seconds);
    var date = second.toLocaleDateString('de-DE', {
      month: '2-digit',
      day: '2-digit',
      year: 'numeric',
    }).replace(/\./g, '/');
    return  date;

   }
   /*
    * End render check available
    */
})(jQuery);

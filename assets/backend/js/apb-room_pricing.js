jQuery(document).ready(function($) {

   var pricing_calendar =  {
            data_init : {
                 date_default_1 : awe_date_curent_1,
                 date_default_2 : awe_date_curent_2,
                 room_id : room_id
            },
            init: function () {  
               this.fullCalendar();
               this.addPricing();
            },
            fullCalendar : function(){
                $('#calendar').fullCalendar({
                        header: {
                            left: '',
                            right: "",
                            center: 'title',
                        },
                        ignoreTimezone: false,
                      //  editable: false,
                      //  selectable: true,
                        handleWindowResize: true,
                        defaultDate: this.data_init.date_default_1,
                        select: function(start, end) {
                            var title = prompt('Event Title:');
                            var eventData;
                            if (title) {
                                eventData = {
                                        title: title,
                                        start: start,
                                        end: end
                                };
                                $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
                            }
                            $('#calendar').fullCalendar('unselect');
                        },
                        events: function(start, end, timezone, callback) {
                            data = {
                                action: "get_pricing",
                                date: pricing_calendar.data_init.date_default_1,
                                room_id: pricing_calendar.data_init.room_id,
                            }
                            $.post(ajaxurl, data, function(reuslt) { 
                                var data_result = JSON.parse(reuslt);  
                                 callback(data_result);
                            }); 
                        },
                         eventRender: function(event, el) {
                            el.find('.fc-time').remove();
                          },
                });
                $('#calendar2').fullCalendar({
                        header: {
                            left: '',
                            right: '',
                            center: 'title',
                        },
                        defaultDate: this.data_init.date_default_2,
                        ignoreTimezone: false,
                        select: function(start, end) {

                            var title = prompt('Event Title:');
                            var eventData;
                            if (title) {
                                eventData = {
                                        title: title,
                                        start: start,
                                        end: end
                                };
                                $('#calendar2').fullCalendar('renderEvent', eventData, true); // stick? = true
                            }
                            $('#calendar2').fullCalendar('unselect');
                        },

                        events: function(start, end, timezone, callback) {
                            
                            data = {
                                action: "get_pricing",
                                date: pricing_calendar.data_init.date_default_2,
                                room_id: pricing_calendar.data_init.room_id,
                            }
                            $.post(ajaxurl, data, function(reuslt) { 
                                var data_result = JSON.parse(reuslt);  
                                callback(data_result);
                            });
                        }, 
                         eventRender: function(event, el) {
                            el.find('.fc-time').remove();
                          },
                });
             },
            
             addPricing : function(){     
                 $(".awe-add-pricing-js").click(function(){
                      
                      var rooms_start_date = $("input[name=rooms_start_date]").val();
                      var rooms_end_date =  $("input[name=rooms_end_date]").val();
                      var day_option =  $(".get_day_js:checked").map(function(_,el){
                          return $(el).val();
                      }).get();
                      var operation = $("select[name=operation]").val();
                      var amount = ($("input[name=amount]").val() != "") ? $("input[name=amount]").val() : "0";
                      var _room_id = new Array();  _room_id.push( pricing_calendar.data_init.room_id);
                      
                      if(rooms_start_date == ""){
                          $("#awe_datepicker_start").focus();
                      }else{
                          $(".spinner").show();
                            data = {
                                action: "add_pricing_for_room",
                                rooms_start_date: rooms_start_date,
                                rooms_end_date: rooms_end_date,
                                day_option: day_option,
                                operation: operation,
                                amount: amount,
                                room_id:_room_id,
                            }
                            $.post(ajaxurl, data, function(reuslt) { 
                              window.location = location.href = "";
                            });
                      }
                      
                      
                     
                 });
             }
   }
   pricing_calendar.init();                  
		
});
(function($) {
  "use strict";

  $(document).ready(function() {

    var availability_calendar =  {
      data_init : {
        date_default_1 : awe_date_curent_1,
        date_default_2 : awe_date_curent_2,
        room_status    : room_status,
        room_id        : room_id,
      },

      init: function() {
        this.fullCalendar();
        this.UpdateAvailability();
        this.AddBookingSingle();
        this.ApbGetPriceOption();
      },

      AweCalendar_eventRender: function(event,el){
        el.find('.fc-time').remove();
        if (event.key_status == 0) {
          el.addClass('awe-complete');
        } else if (event.key_status == 3) {
          el.addClass('awe-pending');
        } else if (event.key_status == 1) {
          el.addClass('awe-notavailable');
        } else if(event.type_show == 'apb-pending'){
          el.removeClass('awe-pending');
        }
      },

      AweCalendar_eventAfterRender : function( event, element, view){
        if (event.type_show != 'apb-pending') {
          if (element.css('margin-top') == "1px") {
            element.css('margin-bottom','-22px');
          }
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
        width_event = element.children('.fc-content').width();

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
      },

      // Get Calander status of room
      fullCalendar : function(){
        var self = this;
        $('#calendar').fullCalendar({
          height: 500,
          header: {
              left: '',
              right: "",
              center: 'title',
          },
          ignoreTimezone: false,
          editable: false,
          selectable: true,
          handleWindowResize: true,
          defaultDate: this.data_init.date_default_1,
           //eventLimit: 2,
          select: function(start, end,allDay) {
            var ed          = end.subtract(1, 'days');
            var start_date  = availability_calendar.SecondsToDay(start);
            var end_date    = availability_calendar.SecondsToDay(end);

            $(".btn-update-status-js").hide();
            $(".change_status_notice_js").html();
            $(".box-select-room-type-js").show();
            $(".event-details-js").html("Duration: "+start_date+" to "+end_date);
            $("input[name=popup_rooms_start_date]").attr("value",start_date);
            $("input[name=popup_rooms_end_date]").attr("value",end_date);

            $("select[name=popup_unit_state]").change(function(){
            $(".btn-popup-update-status-js").slideDown();
            });

            $(".apb-popup-update-status-js").click(function(){

            var type = $(this).attr('data-value');
            if (type == 'yes') {
              var rooms_start_date  = $("input[name=popup_rooms_start_date]").val();
              var rooms_end_date    = $("input[name=popup_rooms_end_date]").val();
              var unit_state        = $("select[name=popup_unit_state]").val();
              var _room_id          = new Array();  _room_id.push( availability_calendar.data_init.room_id);
              $(".change_status_notice_js").html('Loading...');
                  var data = {
                      action: "add_availability_for_room",
                      rooms_start_date: rooms_start_date,
                      rooms_end_date: rooms_end_date,
                      unit_state: unit_state,
                      room_id: _room_id,
                  };
                  $.post(ajaxurl, data, function(reuslt) {
                      window.location = location.href = "";
                  });
              } else {
                  $(".btn-popup-update-status-js").slideUp();
              }

            });
            $(".add-booking-single-form-js").hide();
            $(".notice-check-avb-single-js").hide();

            },
            windowResize: function(view) {
              $('#calendar').fullCalendar('refetchEvents');
            },
            events: function(start, end, timezone, callback) {
              var data = {
                action: "get_availability",
                date: availability_calendar.data_init.date_default_1,
                room_id: availability_calendar.data_init.room_id,
                room_status:availability_calendar.data_init.room_status,
                calendar: 1
              };
              $.post(ajaxurl, data, function(reuslt) {
                var data_result = JSON.parse(reuslt);
                callback(data_result);
              });

            },
            eventClick: function(calEvent, jsEvent, view) {
              // Getting the Unix timestamp - JS will only give us milliseconds
              if (calEvent.end === null) {
                //We are probably dealing with a single day event
                calEvent.end = calEvent.start;
              }

              var sd = calEvent.start.unix();
              var ed = calEvent.end.unix();
              $(".box-event-info-js").show();
              availability_calendar.GetInfoEvent(sd, ed, calEvent.post_id, 1);

              // Open the modal for edit
            },
            eventRender: function(event, el) {
              // Remove Time from events.
              self.AweCalendar_eventRender(event, el);
              if (event.type_show != 'apb-pending') {
                // Add a class if the event start it is not "AV" or "N/A".
                if (el.hasClass('fc-start') && this.id != 1 && this.id != 0) {
                  el.append('<div class="event-start"/>');
                  el.find('.event-start').css('border-top-color', this.color);
                }

                // Add a class if the event end and it is not "AV" or "N/A".
                if (el.hasClass('fc-end') && this.id != 1 && this.id != 0) {
                  el.append('<div class="event-end"/>');
                  el.find('.event-end').css('border-top-color', this.color);
                }
              }
            },
            eventAfterRender: function( event, element, view ) {
              self.AweCalendar_eventAfterRender( event, element, view);
            }
          });

          $('#calendar2').fullCalendar({
            height: 500,
            header: {
              left: '',
              right: '',
              center: 'title',
            },
            defaultDate: this.data_init.date_default_2,
            ignoreTimezone: false,
            editable: false,
            selectable: true,
            firstDay: 0,
            //eventLimit: 2,
            select: function(start, end,allDay) {
              var ed = end.subtract(1, 'days');
              var start_date = availability_calendar.SecondsToDay(start);
              var end_date = availability_calendar.SecondsToDay(end);

              $(".btn-popup-update-status-js").hide();
              $(".change_status_notice_js").html();
              $(".box-select-room-type-js").show();
              $(".event-details-js").html("Duration: "+start_date+" to "+end_date);
              $("input[name=popup_rooms_start_date]").attr("value",start_date);
              $("input[name=popup_rooms_end_date]").attr("value",end_date);

              $("select[name=popup_unit_state]").change(function(){
                $(".btn-popup-update-status-js").slideDown();
              });
              $(".apb-update-status-js").click(function(){
                var type = $(this).attr('data-value');
                if(type == 'yes'){
                  var rooms_start_date    = $("input[name=popup_rooms_start_date]").val();
                  var rooms_end_date      = $("input[name=popup_rooms_end_date]").val();
                  var unit_state          = $("select[name=popup_unit_state]").val();
                  var _room_id            = new Array();  _room_id.push( availability_calendar.data_init.room_id);
                  $(".change_status_notice_js").html('Loading...');
                  var data = {
                    action: "add_availability_for_room",
                    rooms_start_date: rooms_start_date,
                    rooms_end_date: rooms_end_date,
                    unit_state: unit_state,
                    room_id: _room_id,
                  };
                   $.post(ajaxurl, data, function(reuslt) {
                     window.location = location.href = "";

                  });
                }else{
                   $(".btn-popup-update-status-js").slideUp();
                }

              });
              $(".add-booking-single-form-js").hide();
              $(".notice-check-avb-single-js").hide();

            },

            events: function(start, end, timezone, callback) {
              var data = {
                action: 'get_availability',
                date: availability_calendar.data_init.date_default_2,
                room_id: availability_calendar.data_init.room_id,
                room_status:availability_calendar.data_init.room_status,
                calendar: 2
              };
              $.post(ajaxurl, data, function(reuslt) {
                var data_result = JSON.parse(reuslt);
                 callback(data_result);
              });
            },

            eventClick: function(calEvent, jsEvent, view) {

              // Getting the Unix timestamp - JS will only give us milliseconds
              if (calEvent.end === null) {
                //We are probably dealing with a single day event
                calEvent.end = calEvent.start;
              }

              var sd = calEvent.start.unix();
              var ed = calEvent.end.unix();
              $(".box-event-info-js").show();
              availability_calendar.GetInfoEvent(sd,ed,calEvent.post_id,2);

              // Open the modal for edit
            },

            windowResize: function(view) {
              $('#calendar2').fullCalendar('refetchEvents');
            },

            eventRender: function(event, el) {
              self.AweCalendar_eventRender(event, el);
              if(event.type_show != 'apb-pending'){
                if (el.hasClass('fc-start') && this.id != 1 && this.id != 0) {
                  el.append('<div class="event-start"/>');
                  el.find('.event-start').css('border-top-color', this.color);
                }

                // Add a class if the event end and it is not "AV" or "N/A".
                if (el.hasClass('fc-end') && this.id != 1 && this.id != 0) {
                  el.append('<div class="event-end"/>');
                  el.find('.event-end').css('border-top-color', this.color);
                }
              }
            },
            eventAfterRender: function( event, element, view ) {
              self.AweCalendar_eventAfterRender( event, element, view)
            }
          });
        },

        SecondsToDay : function(seconds) {
          var second = new Date(seconds);
          var date = second.toLocaleDateString('de-DE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
          }).replace(/\./g, '-');

          return  date;

        },

        GetInfoEvent : function(from,to,post_id,num) {
          $(".content-event-js").html('');
          $(".spinner").show();
          var data = {
            action: "get_info_event",
            from: from,
            to: to,
            room_id: availability_calendar.data_init.room_id,
            book_id:post_id,
            calendar: num,
          };
          $.post(ajaxurl, data, function(reuslt) {
           
            var data_result = JSON.parse(reuslt);
            $(".content-event-js").html(data_result);
            $(".spinner").hide();
          });
        },

        UpdateAvailability : function(){
          $(".awe-avb-js").click(function(){
            var $form = $(this).closest('form');
            var rooms_start_date = $form.find("input[name=rooms_start_date]").val();
            var rooms_end_date =  $form.find("input[name=rooms_end_date]").val();
            if (rooms_start_date == "") {
              $form.find(".date-start-js").focus();
              return this;
            }
            if (rooms_end_date == "") {
              $form.find(".date-end-js").focus();
              return this;
            }
            $(".btn-update-status-js").slideDown();
          });
          $(".apb-update-status-js").click(function() {
            var action = $(this).attr('data-value');
            if (action == 'yes') {
              var rooms_start_date = $("input[name=rooms_start_date]").val();
              var rooms_end_date   =  $("input[name=rooms_end_date]").val();
              var unit_state       = $("select[name=unit_state]").val();

              var day_option =  $(".get_day_js:checked").map(function(_,el){
                  return $(el).val();
              }).get();

              var _room_id = new Array();  _room_id.push( availability_calendar.data_init.room_id);
              if ( rooms_start_date == "" ) {
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
                  $.post(ajaxurl, data, function(reuslt) {
                      window.location = location.href = "";
                  });
              }
            } else {
                $(".btn-update-status-js").slideUp();
            }

          });
        },

        AddBookingSingle : function() {
          $(".apb-add-booking").click(function(){
            $(".apb-text-loading").show();
            var rooms_start_date = $("input[name=popup_rooms_start_date]").val();
            var rooms_end_date   =  $("input[name=popup_rooms_end_date]").val();
            var _room_id         = availability_calendar.data_init.room_id;
            var data = {
              action: "Apb_add_booking_single_available_manage",
              from: rooms_start_date,
              to: rooms_end_date,
              room_id: _room_id
            };
            $.post(ajaxurl, data, function(reuslt) {
              var data_result = JSON.parse(reuslt);
              $(".apb-text-loading").hide();
              if(data_result !="no"){
                $(".apb-single-avb-package-js").html(data_result)
                $(".add-booking-single-form-js").show();
                $(".notice-check-avb-single-js").hide();
              } else {
                $(".notice-check-avb-single-js").html('Day Unavailable.');
               $(".notice-check-avb-single-js").show();
               $(".add-booking-single-form-js").hide();
              }
            });
          });
          $("#add-booking-js").click(function(){

            var rooms_start_date  = $("input[name=popup_rooms_start_date]").val();
            var rooms_end_date    = $("input[name=popup_rooms_end_date]").val();
            var custommer         = $("select[name=custommer]").val();
            var room_adult        = $("select[name=room_adult]").val();
            var room_child        = $("select[name=room_child]").val();
            var order_status      = $("select[name=apb_order_status]").val();
            var package_id      = $('input[name^=package_id]').map(function(idx, elem) {
              if(this.checked){
                return $(elem).val();
              }
            }).get();
            var package_total     = $('input[name^=package_total]').map(function(idx, elem) {
              return $(elem).val();
            }).get();
            var room_price = $("input[name=room_price]").val();
            var _room_id         = availability_calendar.data_init.room_id;
            var data = {
              action: "Controller_add_booking",
              custommer:      custommer,
              room_adult:     room_adult,
              room_child:     room_child,
              order_status:   order_status,
              package_id :    package_id,
              package_total:  package_total,
              room_price:     room_price,
              from:           rooms_start_date,
              to:             rooms_end_date,
              room_id:        _room_id,
            };

            $.post(ajaxurl, data, function(reuslt) {
              var countdow = 2;
              var data_result = JSON.parse(reuslt);
              $(".notice-check-avb-single-js").html(data_result);

              setInterval(function() {
                var count = countdow++;
                $("#countdow").html('<i>Reset after ' + ( count++ ) + 's</i></p>');
                if (count == 6) {
                  window.location = location.href = "";
                }
              }, 1000);

              $(".notice-check-avb-single-js").show();
              $(".add-booking-single-form-js").slideUp();
            });

          });
          $(".awe-plugin").on('click','.room-delete-order',function(){
            var book_id = $(this).attr('data-id');
            var data = {
              action: "Controller_delete_booking",
              book_id: book_id,
            };
            $.post(ajaxurl, data, function(reuslt) {
              var data_result = JSON.parse(reuslt);
              if(data_result == 'yes'){
                $('.item-order-'+book_id).slideUp();
              }
            });
          });

        },

      ApbGetPriceOption: function() {
        $(document).on('change', '.package_total_js', function() {
          var package_price = parseInt($(this).attr('data-value'));
          var package_num = parseInt($(this).val());
          var package_id = $(this).attr('data-id');
          var total_price_package = package_price*package_num;
          var price_room = $(".total_price_js").val();

          $(".package-"+package_id).attr('data-value',total_price_package);

        });

        $(document).on('change', '.options_operation_js', function() {
          var operation = $(this).attr('data-operation');
          var price_package = parseFloat( $(this).attr('data-value') );
          var price_room_current = parseFloat( $('.total_price_js').val() );
          var daily_package = parseInt( $(this).attr('data-daily') );
          var package_id = $(this).val();
          var number_nights = $(this).attr('data-nights');

          var total_price;
          if (this.checked) {
            var $package_num = $(this).closest('p').find('.package-num-' + package_id);
            $package_num.attr('readonly', 'readonly');
            var number_packages = parseInt( $package_num.val() );

            if ( daily_package ) {
              total_price = price_room_current + price_package * number_nights * number_packages;
            } else {
              total_price = price_room_current + price_package * number_packages;
            }
          } else {
            var $package_num = $(this).closest('p').find('.package-num-' + package_id);
            $package_num.attr('readonly', 'readonly');
            var number_packages = parseInt( $package_num.val() );

            if ( daily_package ) {
              total_price = price_room_current - price_package * number_nights * number_packages;
            } else {
              total_price = price_room_current - price_package * number_packages;
            }
          }
          $(".total_price_js").val(total_price);
        });
      },
      GetStatusRoomByButton : function(){

        $(".btn-status-js").click(function(){
        var status = $(this).attr('data-value');
           availability_calendar.fullCalendar();
        });
      }
    }
    availability_calendar.init();

  });
})(jQuery);

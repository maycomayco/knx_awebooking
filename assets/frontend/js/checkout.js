(function($) {

    if ($('.apb-container').hasClass('apb-checkout')) {
        $(".apb-step-one").addClass('step-complete');
        $(".apb-step-two").addClass('step-complete');
    }

     $("#awe-plugin-booking").on('click','.get-checkout-js',function(){
         $("#preloader").show();

         data = {
             action:  "info_cart_ckeckout"
         }
         $.post(apbAjax.ajax_url, data, function(result) {
            var data_result = JSON.parse(result);
            $(".room-select-js").html(data_result)
         });
         form = {
             action: "apb_ckeckout_form"
         }
         $.post(apbAjax.ajax_url, form, function(result) {
            var data_result = JSON.parse(result);
            $(".apb-content-js").html(data_result);
             $("#preloader").hide();
         });
         return false;
    });

    $.fn.serializeObject = function()
       {
         var o = {};
         var a = this.serializeArray();
         $.each(a,function(){
             if(o[this.name] !== undefined){
                 if(!o[this.name].push){
                     o[this.name] = [o[this.name]];
                 }
                 o[this.name].push(this.value || '');
             }else{
                 o[this.name] = this.value || '';
             }
         });
         return o;
       };
    function apb_value_info_order(element){

          var new_array = $('input[name^='+element+']').map(function(idx, elem) {
            return $(elem).val();
          }).get();

        return new_array;
    }
    $("input[type=submit]").click(function(){

        var request_form = $('.wpcf7-form-control').serializeObject();
        data = {
            action:         "add_to_order",
            from:           apb_value_info_order('apb-from'),
            to:             apb_value_info_order('apb-to'),
            room_type:      "all",
            info_custom:    request_form,
            room_adult:     apb_value_info_order('apb-adult'),
            room_child:     apb_value_info_order('apb-child'),
            price:          apb_value_info_order('apb-price'),
            packages:       apb_value_info_order('apb-package'),
            room_id:        apb_value_info_order('apb-room_id'),
        }
        var error = 0;
        $(this).closest('form').find(".wpcf7-validates-as-required").each(function(){
            if($(this).val() != ""){
                error++;
            }
        });

        if($(this).closest('form').find(".wpcf7-validates-as-required").length == error){
            $.post(apbAjax.ajax_url, data, function(result) {
               
              $(".wpcf7-form p").hide();

              $(document).trigger('apb-completed-cf7-checkout');
            });
        }

    });

})(jQuery);

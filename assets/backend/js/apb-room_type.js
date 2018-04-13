jQuery(document).ready(function($) {
    $(".add-new-h2").attr('href',"#");
    $(".add-new-h2").click(function(){
         $(".box-select-room-type-js").show(); 
    });
    $(".awe-add-option-js").click(function(){
        var itemnums = $(".item-option").length;
        data = {
            action: "genform_option",
            itemnums: itemnums
        }
        $(".spinner").css("visibility","inherit");
        $(".spinner").show();
        $.post(ajaxurl, data, function(reuslt) { 
            var result = JSON.parse(reuslt);
            $(".spinner").hide();
            $(".form-option-js").append(result);
        });
    });
    $("body").on("click",".remove-option-js",function(){
        var id = $(this).attr("data-id");
        if(id){
            data = {
                action: "delete_type_option",
                id:id
            }
            $(".spinner").show();
            $.post(ajaxurl, data, function(reuslt) { 
                var result = JSON.parse(reuslt);
                $(".spinner").hide();
                $(".op-"+id).remove();
            });
        }else{
            $(this).closest(".item-option").remove();
        }  
    });
});
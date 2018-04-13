jQuery(document).ready(function($) {

	"use strict";
	var room_gallery;
	var room_image_gallery = $('.room_images');
	var galler_contaner = $('.room_images');
	$('body').on('click',".apb_add_gallery", function()
	{
		
        var self = $(this);
        var attachment_id = room_image_gallery.val();
        //If the uploader object has already been created, reopen the dialog
        if (room_gallery) {
            room_gallery.open();
            return false;
        }
       
       
        //Extend the wp.media object
        room_gallery = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image', 
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });
        //When a file is selected, grab the URL and set it as the text field's value
        room_gallery.on('select', function() {
            
            var selection = room_gallery.state().get('selection');
  

            selection.map( function( attachment ) {

				attachment = attachment.toJSON();
				if ( attachment.id ) {
                                    
				
                    attachment_id = attachment_id ? attachment_id + "," + attachment.id : attachment.id;
					galler_contaner.append('\
						<li class="image image-'+ attachment.id +'" data-attachment_id="' + attachment.id + '">\
							<img src="' + attachment.url + '" />\n\
                                                                <input type="hidden" name="apb_gallery[]" value="' + attachment.id + '" >\n\
							<ul class="actions">\
								<li><a class="delete" data-id="'+ attachment.id +'" title="delete">delete</a></li>\
							</ul>\
						</li>');
				}

			});
            room_image_gallery.val( attachment_id );
        });

        room_gallery.open();
        return false;
	});

        
	$("body").on('click', '.delete', function(){
		var id_image = $(this).attr("data-id");
                $(".image-"+id_image).remove();
    
	});


	galler_contaner.sortable({
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		},
		update: function(event, ui) {
			var array_id=[];

			$('.awe_gallery_images li.image').css('cursor','default').each(function() {

				var attachment_id = $(this).data( 'attachment_id' );
                if(attachment_id!=undefined)
                    array_id.push(attachment_id);
			});
            if(array_id.length>0)
			    room_image_gallery.val( JSON.stringify(array_id));
		}
	});

});
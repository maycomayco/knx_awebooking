jQuery(document).ready(function($) {
 "use strict";
 // console.log("sadsad");
 var room_gallery;
 var room_image_gallery = $('#room_image_feature');
 var galler_contaner = $('.thumb');

 $('body').on('click',".awe_add_image", function()
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
            var ids = [], urls=[];
            
            selection.map( function( attachment ) {

    attachment = attachment.toJSON();

    if ( attachment.id ) {
        attachment_id = attachment_id ? attachment_id + "," + attachment.id : attachment.id;
        galler_contaner.html('<div class="image"><input type="hidden" name="thumb_id" class="thumb_id" value="' + attachment.id + '"><img class="thumb_img" src="' + attachment.url + '" /><a href="#" class="awe_add_image delete" title="delete">delete</a></div>');
    }

   });
         room_image_gallery.val(attachment_id);
        });

        room_gallery.open();
        return false;
 });
 
 galler_contaner.on('click', 'a.delete', function(e){
  e.preventDefault();
  $(this).parents('.image').remove();
  var ids = '';
  $(".thumb").html('<a class="awe_add_image">Set featured image</a>');
  galler_contaner.find('.image').each(function()
  {
   var id = $(this).data('attachment_id');
   ids = ids + id +',';
  });
  room_image_gallery.val(ids);
  return false;
 });


// galler_contaner.sortable({
//  items: 'li.image',
//  cursor: 'move',
//  scrollSensitivity:40,
//  forcePlaceholderSize: true,
//  forceHelperSize: false,
//  helper: 'clone',
//  opacity: 0.65,
//  start:function(event,ui){
//   ui.item.css('background-color','#f6f6f6');
//  },
//  stop:function(event,ui){
//   ui.item.removeAttr('style');
//  },
//  update: function(event, ui) {
//   var attachment_ids = '';
//
//   $('.awe_gallery_images li.image').css('cursor','default').each(function() {
//    var attachment_id = jQuery(this).attr( 'data-attachment_id' );
//    attachment_ids = attachment_ids + attachment_id + ',';
//   });
//
//   room_image_gallery.val( attachment_ids );
//  }
// });

});
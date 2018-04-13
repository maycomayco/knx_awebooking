(function($) {
    var ApbCartCheckout = {

        init: function(){
            this.AddToCheckout();
        },
        AddToCheckout : function(){
            $('#awe-plugin-booking').on('click', '.add-to-checkout-js', function() {
                $('#preloader').show();
                window.location = apbAjax.checkout_page;
                $('#preloader').hide();
                return false;
            });
        }
    }
   ApbCartCheckout.init();
})(jQuery);

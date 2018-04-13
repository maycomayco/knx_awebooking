<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * The template for displaying check available
 *
 * Override this template by copying it to your theme
 *
 * @author  AweTeam
 * @package AweBooking/Templates
 * @version 1.0
 */
get_header() ;
do_action('apb_renderBefore');
?>
<div class="apb-container apb-check-availability">

    <div class="apb-layout apb-sidebar-left">
        <!-- CONTENT -->
        <div class="apb-content-area">
            <?php
               do_action("form_step");
               /**
               * layout_loading hook
               */
              do_action('layout_loading');
               /*
               * Before content add data of javascript
               */
              do_action('apb_room_content_before');

              /*
               * body content get request data or data ajax
               */

              do_action('apb_body_check_available');

               /*
               * After content add data of javascript
               */
              do_action('apb_room_content_after');
            ?>
        </div>
        <!-- END / CONTENT -->

        <!-- SIDEBAR -->
        <div class="apb-widget-area">
           <?php
           /*
            * @hooked fill_content_js() - 10
            */
           do_action('apb_fill_content_js');

           /*
            * Hook Form check available
            */
           do_action("form_check_availability");

           ?>
        </div>
        <!-- END / SIDEBAR -->
    </div>
</div>
<!-- END / PAGE WRAP -->
<?php
do_action('apb_renderAfter');
get_footer()
?>

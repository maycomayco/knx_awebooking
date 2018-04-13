<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Tempalte setting restore setting
 *
 * @version		1.0
 * @package		AweBooking/admin/
 * @author 		AweTeam
 */

?>
 <div class="form-elements">
     <div class="form-radios">
         <button type="submit" name="export" class="apb-import-data button awe-avb-js" ><?php _e('Export data','awebooking' ); ?></button>
          <span class="spinner is-active" style="display: none;"></span>
    </div>
   <span class="description">
        <?php _e('Export Data') ?>
   </span>
</div>

  <div class="form-elements">
       <div class="form-radios">
          <input name="FileInput" type="file" />
          <button type="submit" class="awe-avb-js" name="file_upload" />Upload </button>    
      </div>
     <span class="description">
          <?php _e('Upload File Import') ?>
     </span>
     <?php if(isset($_GET['up']) && $_GET['up'] == 1): ?>
      <br/>
      <div id="message" class="updated notice notice-success is-dismissible below-h2">
        <p><?php _e('Upload File Success','awebooking' ); ?></p>
        
        <button type="button" class="notice-dismiss">
        <span class="screen-reader-text"><?php _e('Dismiss this notice.','awebooking' ); ?></span></button>
        <button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.','awebooking' ); ?></span></button>
      </div>
      <br/>
      <div class="awe-import">
         <button class="awe-begin-import-js awe-avb-js button" type="button"><?php _e('Begin Import Data','awebooking' ); ?></button>
          <div class="sk-folding-cube" style="display:none;">
            <div class="sk-cube1 sk-cube"></div>
            <div class="sk-cube2 sk-cube"></div>
            <div class="sk-cube4 sk-cube"></div>
            <div class="sk-cube3 sk-cube"></div>
          </div>
      </div>

    <?php endif; ?>

  </div>

  
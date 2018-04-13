<?php

/**
 * Description of page set lang
 *
 * @author leeit
 */
class Apb_set_page_lang{
    public static function output( $post ) {
        $status = get_post_meta($post->ID, "_apb_page_lang",true);
        $page_lang_default = get_post_meta($post->ID,'_apb_page_lang_default',true);

        if($page_lang_default == get_option("check_avb")){
            echo '<label>'.__('Set Page Check Available: ','awebooking').'</label><input type="checkbox" '.checked($status,1,false).' name="_apb_page_lang" value="1">';
        }
        if($page_lang_default == get_option("list_room")){
            echo '<label>'.__('Set Page List Rooms: ','awebooking').'</label><input type="checkbox" '.checked($status,1,false).' name="_apb_page_lang" value="1">';
        }
        if($page_lang_default == get_option("apb_checkout")){
            echo '<label>'.__('Set Page Checkout: ','awebooking').'</label><input type="checkbox" '.checked($status,1,false).' name="_apb_page_lang" value="1">';
        }
    }
    static public function save($post_id) {
        $page_lang_default = get_post_meta($post_id,'_apb_page_lang_default',true);
        if(isset($_POST['_apb_page_lang']) && !empty($_POST['_apb_page_lang'])){
            update_post_meta($post_id, "_apb_page_lang",$_POST['_apb_page_lang']);
            if($page_lang_default == get_option("check_avb")){
                $current_id = get_option('_apb_page_check_avb');
                $current_id[] = $post_id;
                update_option('_apb_page_check_avb',$current_id);
            }
            if($page_lang_default == get_option("list_room")){
                $current_id = get_option('_apb_page_list_room');
                $current_id[] = $post_id;
                update_option('_apb_page_list_room',$current_id);
            }
            if($page_lang_default == get_option("apb_checkout")){
                $current_id[] = $post_id;
                update_option('_apb_page_checkout',$current_id);
            }
        }else{
            update_post_meta($post_id, "_apb_page_lang",0);

            if($page_lang_default == get_option("check_avb")){
                $current_id = get_option('_apb_page_check_avb');
                if(isset($current_id[$post_id])){
                    unset($current_id[$post_id]);
                }
                update_option('_apb_page_check_avb',$current_id);
            }
            if($page_lang_default == get_option("list_room")){
                $current_id = get_option('_apb_page_list_room');
                if(isset($current_id[$post_id])){
                    unset($current_id[$post_id]);
                }
                update_option('_apb_page_list_room',$current_id);
            }
            if($page_lang_default == get_option("apb_checkout")){
                if(isset($current_id[$post_id])){
                    unset($current_id[$post_id]);
                }
                update_option('_apb_page_checkout',$current_id);
            }
        }
    }
}

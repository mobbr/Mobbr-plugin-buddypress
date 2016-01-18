<?php
require_once("mobbr.config.php");
require_once("mobbr.utils.php");

function ajax_save_post_participation_metadata() {
    $success = false;
    $error_msg = 'Missing parameters';
    if(isset($_POST['url']) && isset($_POST['participants']) && is_array($_POST['participants'])) {
        $url = $_POST['url'];
        $post_id = url_to_postid($url);
        $participants = $_POST['participants'];

        if($post_id) {
            $options = get_mobbr_plugin_options();

            delete_post_meta($post_id, '_mobbr_participants');
            foreach($participants as $participant) {
                if (!isset($participant['id']) || !isset($participant['share'])) {
                    continue;
                }
                $id = $participant['id'];
                $share = (int)$participant['share'];
                $role = isset($participant['role'])?sanitize_text_field($participant['role']):MOBBR_ROLE_TASK_CONTRIBUTOR;

                if($id && (filter_var(str_replace("mailto:", "", $id), FILTER_VALIDATE_EMAIL) || preg_match(MOBBR_REGEX_URL, $id)) && $share > 0 && $share < 100 && ($role != MOBBR_ROLE_WEBSITE_OWNER || "mailto:".$options['email'] != $id)) {
                    $data = array('id' => $id, 'share' => $share, 'role' => $role);
                    if(add_post_meta($post_id, '_mobbr_participants', json_encode($data))) {
                        $success = true;
                    }
                }

                if(!$success && $role == 'owner' && count($participants) == 1) {
                    // Allows payer to choose the owner as the only recipient
                    $success = true;
                }
            }
        } else {
            $error_msg = "Couldn't find post";
        }
    }

    if($success) {
        echo json_encode(array('status'=>true, 'msg'=>'Metadata saved successfully'));
    } else {
        header("HTTP/1.0 400 Bad Request");
        echo json_encode(array('status'=>false, 'msg'=>$error_msg));
    }
    wp_die();
}
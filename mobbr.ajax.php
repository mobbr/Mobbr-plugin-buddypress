<?php
require_once("mobbr.config.php");

function ajax_save_post_participation_metadata() {
    $success = false;
    $error_msg = 'Missing parameters';
    if(isset($_POST['url']) && isset($_POST['participants']) && is_array($_POST['participants'])) {
        $url = $_POST['url'];
        $post_id = url_to_postid($url);
        $participants = $_POST['participants'];

        if($post_id) {
            delete_post_meta($post_id, '_mobbr_participants');
            foreach($participants as $participant) {
                if (!isset($participant['id']) || !isset($participant['share'])) {
                    continue;
                }
                $id = $participant['id'];
                $share = (int)$participant['share'];
                $role = isset($participant['role'])?sanitize_text_field($participant['role']):'contributor';

                if($id && (filter_var(str_replace("mailto:", "", $id), FILTER_VALIDATE_EMAIL) || preg_match(MOBBR_REGEX_URL, $id)) && $share > 0 && $share < 100 && $role != 'owner') {
                    $data = array('id' => $id, 'share' => $share, 'role' => $role);
                    if(add_post_meta($post_id, '_mobbr_participants', json_encode($data))) {
                        $success = true;
                    }
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
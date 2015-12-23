<?php
require_once("mobbr.config.php");

function get_page_url() {
    return (is_single())?get_permalink():(isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function get_post_url() {
    return (is_single() || is_home())?get_permalink():get_page_url();
}

function get_mobbr_participation() {
    $page_url = get_page_url();

    $title = get_the_title();
    $options = get_option('mobbr_plugin_options');

    $options['email'] = $options['email']?$options['email']:get_option('wp_email');
    $options['share'] = $options['share']?$options['share']:10;

    $owner = array(
        "id" => "mailto:$options[email]",
        "role" => "owner",
        "share" => "$options[share]%"
    );

    global $wp_query;
    $content = in_the_loop()?get_the_content():$wp_query->post->post_content;

    $task_url = "";
    if(preg_match("/https?:\/\/[^\s\"]+/i", $content, $matches)) {
        $task_url = $matches[0];
    }

    $amount = 0;
    if(preg_match("/Fee:\s*\Q$\E?\s*([\d,\.]+)/i", $title.' '.$content, $matches)) {
        if(count($matches) > 1) {
            $number = $matches[1];
            if(preg_match("/\d+\.\d+/i", $number)) {
                $amount = str_replace(",","",$number);
            } else {
                $amount = str_replace(",",".",$number);
            }
        }
    }
    $amount = floatval($amount);

    $script_type = 'payment';
    $script_lang = 'EN';
    $script_title = $title;
    $script_desc = $content;
    $script_keywords = array('tunga.io', 'tunga');
    $script_participants = array($owner);

    $use_local_script = true;

    if($task_url) {
        $req = wp_remote_get(MOBBR_URI_ENDPOINT . "?url=" . urlencode($task_url), array('headers'=> array('Accept' => 'application/json')));
        if(!is_wp_error($req) && $req && $req['response']['code'] == 200) {
            $response = json_decode($req['body'], true);
            $task_script = $response['result']['script'];
            $script_type = $task_script['type'];
            $script_lang = $task_script['language'];
            $script_title = $task_script['title'];
            $script_desc = $task_script['description'];
            $script_keywords = array_merge($script_keywords, $task_script['keywords']);
            $task_participants = $task_script['participants'];
            if(is_array($task_participants)) {
                if($options['share'] >= 0 and $options['share'] <= 100) {
                    $cut = round((1 - ($options['share']/100.0)), 4);
                    foreach($task_participants as $key=>$participant) {
                        if(preg_match("/\\%$/", $participant['share'])) {
                            $participant['share'] = round(((int)str_replace("%", "", $participant['share']))*$cut, 4) . "%";
                            $task_participants[$key] = $participant;
                        }
                    }
                }
                $script_participants = array_merge($script_participants, $task_participants);
                $use_local_script = false;
            }
        }
    }

    if($use_local_script) {
        $local_script_participants = array();
        $participants_meta = get_post_meta($wp_query->post->ID, '_mobbr_participants');
        if(is_array($participants_meta)) {
            foreach(get_post_meta($wp_query->post->ID, '_mobbr_participants') as $participant) {
                array_push($local_script_participants, json_decode($participant, true));
            }
            $script_participants = array_merge($script_participants, $local_script_participants);
        }
    }

    $participation = array(
        "type" => $script_type,
        "language" => $script_lang,
        "title" => $script_title,
        "description" => $script_desc,
        "keywords" => $script_keywords,
        "participants" => $script_participants,
        "extras" => array(
            "editable" => $use_local_script,
            "task_url" => $task_url,
            "amount" => $amount)
    );

    return $participation;
}

function save_post_participation_metadata($post_id){
    if (!isset($_POST['mobbr_participant_id']) || !$_POST['mobbr_participant_id'] || !is_array($_POST['mobbr_participant_id'])) {
        return;
    }

    if (!isset($_POST['mobbr_participant_share']) || !$_POST['mobbr_participant_share'] || !is_array($_POST['mobbr_participant_share'])) {
        return;
    }

    $ids = $_POST['mobbr_participant_id'];
    $shares = $_POST['mobbr_participant_share'];

    $num = min(count($ids),count($shares));

    delete_post_meta($post_id, '_mobbr_participants');
    foreach(range(0,$num-1) as $key) {
        $id = sanitize_text_field($ids[$key]);
        $share = (int)$shares[$key];

        if($id && filter_var($id, FILTER_VALIDATE_EMAIL) && $share > 0 && $share < 100) {
            $data = array('id' => 'mailto:'.$id, 'share' => $share, 'role' => 'contributor');
            add_post_meta($post_id, '_mobbr_participants', json_encode($data));
        }
    }
}
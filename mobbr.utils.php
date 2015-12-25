<?php
require_once("mobbr.config.php");

function get_page_url() {
    return (is_single())?get_permalink():(isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function get_post_url() {
    return (is_single() || is_home())?get_permalink():get_page_url();
}

function get_mobbr_plugin_options() {
    global $MOBBR_DEFAULT_OPTIONS;
    $options = get_option('mobbr_plugin_options', $MOBBR_DEFAULT_OPTIONS);
    if(count($options) != count($MOBBR_DEFAULT_OPTIONS)) {
        $options = array_merge($MOBBR_DEFAULT_OPTIONS, $options);
    }
    return $options;
}

function get_mobbr_participation() {
    $title = get_the_title();
    $options = get_mobbr_plugin_options();
    $owner = array(
        "id" => "mailto:$options[email]",
        "role" => "owner",
        "share" => "$options[share]%"
    );

    global $wp_query;
    $content = in_the_loop()?get_the_content():$wp_query->post->post_content;

    $task_url = "";
    if(preg_match(MOBBR_REGEX_URL, $title.' '.$content, $matches)) {
        $task_url = $matches[0];
    }

    $amount = 0;
    $currency = 'USD';
    if(preg_match(MOBBR_REGEX_TASK_AMOUNT, $title.' '.$content, $matches)) {
        $number = null;
        if(isset($matches['amount']) && $matches['amount']) {
            $number = $matches['amount'];
        } else if(isset($matches['amount2']) && $matches['amount2']) {
            $number = $matches['amount2'];
        }
        if($number) {
            $amount = str_replace(",",(preg_match("/\d+\.\d+/i", $number)?"":"."),$number);
        }

        global $MOBBR_EURO_SYMBOLS;
        if((isset($matches['currency']) && $matches['currency'] && in_array($matches['currency'], $MOBBR_EURO_SYMBOLS)) || (isset($matches['currency2']) && $matches['currency2'] && in_array($matches['currency2'], $MOBBR_EURO_SYMBOLS))) {
            $currency = 'EUR';
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
        $req = wp_remote_get(MOBBR_URI_INFO_ENDPOINT . "?url=" . urlencode($task_url), array('headers'=> array('Accept' => 'application/json')));
        if(!is_wp_error($req) && $req && $req['response']['code'] == 200) {
            $response = json_decode($req['body'], true);
            $task_script = $response['result']['script'];
            if(isset($task_script['type']))
                $script_type = $task_script['type'];
            if(isset($task_script['language']))
                $script_lang = $task_script['language'];
            if(isset($task_script['title']))
                $script_title = $task_script['title'];
            if(isset($task_script['description']))
                $script_desc = $task_script['description'];
            if(isset($task_script['keywords']) && is_array($task_script['keywords']))
                $script_keywords = array_merge($script_keywords, $task_script['keywords']);
            if(isset($task_script['participants']) && is_array($task_script['participants'])) {
                $task_participants = $task_script['participants'];
                if($options['share'] >= 0 and $options['share'] < 100) {
                    $absolute_shares = array();
                    $relative_shares = array();
                    $absolute_participants = array();
                    $relative_participants = array();
                    foreach($task_participants as $key=>$participant) {
                        if(preg_match("/%$/", $participant['share'])) {
                            $share = intval(str_replace("%", "", $participant['share']));
                            if($share > 0) {
                                $absolute_shares[] = $share;
                                $new_participant = $participant;
                                $new_participant['share'] = $share;
                                $absolute_participants[] = $new_participant;
                            }
                        } else {
                            $share = intval($participant['share']);
                            if($share > 0) {
                                $relative_shares[] = $share;
                                $new_participant = $participant;
                                $new_participant['share'] = $share;
                                $relative_participants[] = $new_participant;
                            }
                        }
                    }
                    $additional_participants = array();
                    $total_absolutes = array_sum($absolute_shares);
                    $total_relatives = array_sum($relative_shares);
                    if($total_absolutes >= 100 || $total_relatives == 0) {
                        $additional_participants = $absolute_participants;
                    } else if($total_absolutes == 0) {
                        $additional_participants = $relative_participants;
                    } else {
                        $additional_participants = $absolute_participants;
                        foreach($relative_participants as $participant) {
                            $share = round((($participant['share']*(100-$total_absolutes))/($total_relatives)),0);
                            if($share > 0) {
                                $new_participant = $participant;
                                $new_participant['share'] = $share;
                                $additional_participants[] = $new_participant;
                            }
                        }
                    }
                    if(count($additional_participants)) {
                        $script_participants = array_merge($script_participants, $additional_participants);
                    }
                }
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
            "amount" => $amount,
            "currency" => $currency
        )
    );
    return $participation;
}
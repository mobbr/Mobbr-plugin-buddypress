<?php
require_once("mobbr.config.php");

function mobbr_plugin_add_meta_boxes() {
    $screens = array('post', 'page');
    foreach ($screens as $screen) {
        add_meta_box(
            'mobbr_plugin_task_details',
            __( 'Task Details', 'mobbr_plugin' ),
            'mobbr_plugin_meta_box_task_details',
            $screen
        );

        add_meta_box(
            'mobbr_plugin_payment_recipients',
            __( 'Add Payment Recipients', 'mobbr_plugin' ),
            'mobbr_plugin_meta_box_participants',
            $screen
        );
    }
}

function mobbr_plugin_meta_box_task_details($post) {
    wp_nonce_field('mobbr_plugin_save_meta_box_task_details', 'mobbr_plugin_meta_box_task_details_nonce');
    $task_fee = get_post_meta( $post->ID, 'taskfee', true);
    $task_currency = get_post_meta( $post->ID, 'taskcurrency', true);
    $task_url = get_post_meta( $post->ID, 'taskurl', true);

    echo '<label for="mobbr_task_fee">';
    _e( 'Fee', 'mobbr_plugin' );
    echo '</label>';
    echo '<p>';

    echo '<select name="mobbr_task_currency">';
    echo '<option value="">- Currency -</option>';
    global $MOBBR_SUPPORTED_CURRENCIES;
    foreach($MOBBR_SUPPORTED_CURRENCIES as $currency) {
        echo '<option value="'.$currency.'" '.(($currency == $task_currency)?'selected':'').'>'.$currency.'</option>';
    }
    echo '</select>';

    echo '<input type="text" id="mobbr_task_fee" name="mobbr_task_fee" value="'.$task_fee.'" placeholder="Fee" />';

    echo '</p>';

    echo '<label for="mobbr_task_url">';
    _e( 'Task url', 'mobbr_plugin' );
    echo '</label> ';
    echo '<p><input type="text" id="mobbr_task_url" name="mobbr_task_url" value="'.$task_url.'" placeholder="Task url" style="width:100%;"/></p>';
}

function mobbr_plugin_meta_box_participants($post) {
    wp_nonce_field('mobbr_plugin_save_meta_box_participants', 'mobbr_plugin_meta_box_participants_nonce');
    $participants = get_post_meta( $post->ID, '_mobbr_participants');
    if(count($participants)) {
        echo "<h4>Update existing entries</h4>";
    }
    foreach($participants as $participant) {
        make_participant_fields($participant);
    }
    echo "<h4>Add more entries</h4>";
    foreach(range(1,5) as $key) {
        make_participant_fields(null);
    }
}

function make_participant_fields($participant) {
    $data = array('id'=>'', 'share'=>'');
    if($participant) {
        $data = json_decode($participant, true);
    }
    echo '<label for="mobbr_participant_id">';
    _e( 'ID', 'mobbr_plugin' );
    echo '</label> ';
    echo '<input type="text" id="mobbr_participant_id" name="mobbr_participant_id[]" value="'.str_replace('mailto:','',$data['id']).'" style="margin-right: 20px;" placeholder="email or profile url"/>';
    echo '<label for="mobbr_participant_share">';
    _e( 'Share', 'mobbr_plugin' );
    echo '</label> ';
    echo '<input type="text" id="mobbr_participant_share" name="mobbr_participant_share[]" value="'.$data['share'].'" placeholder="relative share" /><br/>';
}

function mobbr_plugin_save_meta_boxes($post_id) {
    mobbr_plugin_save_meta_box_task_details($post_id);
    mobbr_plugin_save_meta_box_participants($post_id);
}

function mobbr_plugin_save_meta_box_task_details($post_id) {
    if (!isset($_POST['mobbr_plugin_meta_box_task_details_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['mobbr_plugin_meta_box_task_details_nonce'], 'mobbr_plugin_save_meta_box_task_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type'] ) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id )) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id )) {
            return;
        }
    }

    if (isset($_POST['mobbr_task_fee']) && $_POST['mobbr_task_fee']) {
        $task_fee = (int) $_POST['mobbr_task_fee'];
        if($task_fee > 0) {
            delete_post_meta($post_id, 'taskfee');
            add_post_meta($post_id, 'taskfee', $task_fee);
        }
    }

    if (isset($_POST['mobbr_task_currency']) && $_POST['mobbr_task_currency']) {
        $task_currency = $_POST['mobbr_task_currency'];
        global $MOBBR_SUPPORTED_CURRENCIES;
        if($task_currency && in_array($task_currency, $MOBBR_SUPPORTED_CURRENCIES)) {
            delete_post_meta($post_id, 'taskcurrency');
            add_post_meta($post_id, 'taskcurrency', $task_currency);
        }
    }

    if (isset($_POST['mobbr_task_url']) && $_POST['mobbr_task_url']) {
        $task_url = $_POST['mobbr_task_url'];
        if($task_url && preg_match(MOBBR_REGEX_URL, $task_url)) {
            delete_post_meta($post_id, 'taskurl');
            add_post_meta($post_id, 'taskurl', sanitize_text_field($task_url));
        }
    }
}

function mobbr_plugin_save_meta_box_participants($post_id) {
    if (!isset($_POST['mobbr_plugin_meta_box_participants_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['mobbr_plugin_meta_box_participants_nonce'], 'mobbr_plugin_save_meta_box_participants')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type'] ) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id )) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id )) {
            return;
        }
    }

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

        $is_email = filter_var($id, FILTER_VALIDATE_EMAIL);

        if($id && ($is_email || preg_match(MOBBR_REGEX_URL, $id)) && $share > 0 && $share < 100) {
            if($is_email)
                $id = 'mailto:'.$id;
            $data = array('id' => $id, 'share' => $share, 'role' => MOBBR_ROLE_TASK_CONTRIBUTOR);
            add_post_meta($post_id, '_mobbr_participants', json_encode($data));
        }
    }
}
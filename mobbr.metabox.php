<?php
function mobbr_plugin_add_meta_box() {
    $screens = array('post', 'page');
    foreach ($screens as $screen) {
        add_meta_box(
            'mobbr_plugin_sectionid',
            __( 'Add Payment recipients', 'mobbr_plugin_textdomain' ),
            'mobbr_plugin_meta_box_callback',
            $screen
        );
    }
}

function mobbr_plugin_meta_box_callback($post) {
    wp_nonce_field('mobbr_plugin_save_meta_box_data', 'mobbr_plugin_meta_box_nonce');
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
    _e( 'ID', 'mobbr_plugin_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="mobbr_participant_id" name="mobbr_participant_id[]" value="'.str_replace('mailto:','',$data['id']).'" style="margin-right: 20px;"/>';
    echo '<label for="mobbr_participant_id">';
    _e( 'Share', 'mobbr_plugin_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="mobbr_participant_share" name="mobbr_participant_share[]" value="'.$data['share'].'" /><br/>';
}

function mobbr_plugin_save_meta_box_data($post_id) {
    if (!isset($_POST['mobbr_plugin_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['mobbr_plugin_meta_box_nonce'], 'mobbr_plugin_save_meta_box_data')) {
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

        if($id && filter_var($id, FILTER_VALIDATE_EMAIL) && $share > 0 && $share < 100) {
            $data = array('id' => 'mailto:'.$id, 'share' => $share, 'role' => 'contributor');
            add_post_meta($post_id, '_mobbr_participants', json_encode($data));
        }
    }
}
<?php
require_once("mobbr.utils.php");

function mobbr_script() {
    echo <<<SCRIPT
        <script type="text/javascript" src="https://mobbr.com/mobbr-button.js"></script>
SCRIPT;

}

// Add mobbr participation metadata
function mobbr_participation_meta() {
    $participation = get_mobbr_participation();
    $extras = $participation['extras'];
    unset($participation['extras']);
    $participation_json = json_encode($participation);
    $ajax_url = admin_url('admin-ajax.php');
    $extras['ajax_url'] = $ajax_url;
    $extras_json = json_encode($extras);
    echo <<<META
        <meta name="participation" content='$participation_json'/>
        <meta name="description" content='$participation[description]'/>
        <meta name="lightbox" content='$extras_json'/>
META;
}
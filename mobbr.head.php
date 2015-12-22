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
    $participation_json = json_encode($participation);
    echo <<<META
        <meta name="participation" content='$participation_json'/>
        <meta name="description" content='$participation[description]'/>
META;
}
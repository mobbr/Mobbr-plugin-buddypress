<?php
require_once("mobbr.utils.php");

function mobbr_script() {
    echo <<<SCRIPT
        <script type="text/javascript" src="https://mobbr.com/mobbr-button.js"></script>
SCRIPT;

}

// Add mobbr participation metadata
function mobbr_participation_meta() {
    $participation = json_encode(get_mobbr_participation());
    echo <<<META
        <meta name="participation" content='$participation'/>
META;
}
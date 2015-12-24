<?php
define('LIGHTBOX_URL', plugin_dir_url( __FILE__ ) . "lightbox/#");
define('MOBBR_URI_ENDPOINT', "https://api.mobbr.com/api_v1/uris/info");
define('BUTTON_STYLE_OFFICIAL', 'official');
define('BUTTON_STYLE_CUSTOM', 'custom');
define('BUTTON_PLACEMENT_POSTS', 'posts');
define('BUTTON_PLACEMENT_PAGES', 'pages');
define('BUTTON_POSITION_TOP', 'top');
define('BUTTON_POSITION_BOTTOM', 'bottom');
define('BUTTON_POSITION_WIDGET', 'widget');
define('URL_REGEX', "/\b(?:https?:\/\/)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i");
define('TASK_AMOUNT_REGEX', "/Fee:\s*\Q$\E?\s*([\d,\.]+)\s*(€|&#8364;|&euro;)?/iu");
<?php
define('MOBBR_LIGHTBOX_URL', plugin_dir_url( __FILE__ ) . "lightbox/#");
define('MOBBR_URI_INFO_ENDPOINT', "https://api.mobbr.com/api_v1/uris/info");
define('MOBBR_BUTTON_STYLE_OFFICIAL', 'official');
define('MOBBR_BUTTON_STYLE_CUSTOM', 'custom');
define('MOBBR_BUTTON_PLACEMENT_POSTS', 'posts');
define('MOBBR_BUTTON_PLACEMENT_PAGES', 'pages');
define('MOBBR_BUTTON_POSITION_TOP', 'top');
define('MOBBR_BUTTON_POSITION_BOTTOM', 'bottom');
define('MOBBR_BUTTON_POSITION_WIDGET', 'widget');
define('MOBBR_BUTTON_REQUIRE_AUTH_YES', 'yes');
define('MOBBR_BUTTON_REQUIRE_AUTH_NO', 'no');

define('MOBBR_REGEX_URL', "/\b(?:https?:\/\/)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i");

$MOBBR_EURO_SYMBOLS = array('EUR', '€', '&#8364;', '&euro;');
define('MOBBR_PATTERN_EURO', implode('|', $MOBBR_EURO_SYMBOLS));
define('MOBBR_PATTERN_CURRENCY', "\Q$\E|USD|".MOBBR_PATTERN_EURO);
define('MOBBR_PATTERN_AMOUNT', "[\d,\.]+");
define('MOBBR_REGEX_TASK_AMOUNT', "/(?:(?P<currency>".MOBBR_PATTERN_CURRENCY.")\s*(?P<amount>".MOBBR_PATTERN_AMOUNT."))|(?:(?P<amount2>".MOBBR_PATTERN_AMOUNT.")\s*(?P<currency2>".MOBBR_PATTERN_CURRENCY."))/iu");

$MOBBR_SUPPORTED_CURRENCIES = array('USD', 'EUR');
$MOBBR_DEFAULT_OPTIONS = array(
    'placement_options' => array(MOBBR_BUTTON_PLACEMENT_POSTS),
    'button_style' => MOBBR_BUTTON_STYLE_OFFICIAL,
    'button_position' => MOBBR_BUTTON_POSITION_TOP,
    'button_text' => 'Make Payment',
    'email' => get_option('admin_email', 'webmaster@'.$_SERVER['HTTP_HOST']),
    'share' => 10,
    'require_auth' => true
);
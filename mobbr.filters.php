<?php
require_once("mobbr.config.php");
function add_mobbr_button_to_content($content) {
    $options = get_option('mobbr_plugin_options');
    $can_show_posts = (isset($options['placement_options']) && in_array(BUTTON_PLACEMENT_POSTS, $options['placement_options']));
    $can_show_pages = (isset($options['placement_options']) && in_array(BUTTON_PLACEMENT_PAGES, $options['placement_options']));
    $can_show_button = ((is_single() || is_home()) && $can_show_posts) || (is_page() && $can_show_pages);
    $widget = new MobbrWidget();
    if($can_show_button && isset($options['button_position']) && $options['button_position'] == BUTTON_POSITION_TOP)
        $widget->widget(null, null);
    echo $content;
    if($can_show_button && isset($options['button_position']) && $options['button_position'] == BUTTON_POSITION_BOTTOM)
        $widget->widget(null, null);
}
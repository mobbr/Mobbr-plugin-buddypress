<?php
require_once("mobbr.config.php");
require_once("mobbr.utils.php");

class MobbrWidget extends WP_Widget {

    public function __construct() {
        parent::__construct("mobbr_widget", "Mobbr Widget", array("description" => "Add mobbr button to your WordPress/BuddyPress website."));
    }

    public function form($instance) {
        $currency = "USD";

        if(!empty($instance)) {
            $currency = $instance["currency"];
        }

        $currencyId = $this->get_field_id("currency");
        $currencyName = $this->get_field_name("currency");

        echo "<label for='$currencyId'>Currency</label><br/>";
        echo "<input id='$currencyId' type='text' name='$currencyName' value='$currency'><br/>";
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["currency"] = htmlentities($newInstance["currency"]);
        return $values;
    }

    public function widget($args, $instance) {
        $options = get_option('mobbr_plugin_options');
        if(is_home() && isset($options['button_position']) && $options['button_position'] == BUTTON_POSITION_WIDGET)
            return;
        $currency = isset($instance["currency"])?$instance["currency"]:"USD";
        $lighbox_url = LIGHTBOX_URL;
        $url = get_post_url();

        echo "<script type='text/javascript'>mobbr.setLightboxUrl('$lighbox_url');</script>";

        if($options['button_style'] == BUTTON_STYLE_CUSTOM) {
            echo "<button onClick=\"mobbr.makePayment('$url')\">Make Payment</button>";
        } else {
            echo "<script type='text/javascript'>mobbr.button('$url', '$currency');</script>";
        }
    }
}
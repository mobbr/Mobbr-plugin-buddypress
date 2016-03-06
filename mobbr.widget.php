<?php
require_once("mobbr.config.php");
require_once("mobbr.utils.php");

class MobbrWidget extends WP_Widget {

    public function __construct() {
        parent::__construct("mobbr_widget", "Mobbr Widget", array("description" => "Add Mobbr payments button to your WordPress website."));
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
        $options = get_mobbr_plugin_options();

        if($options['require_auth'] && !is_user_logged_in())
            return;

        if(is_home() && isset($options['button_position']) && $options['button_position'] == MOBBR_BUTTON_POSITION_WIDGET)
            return;

        global $MOBBR_SUPPORTED_CURRENCIES;
        $currency = (isset($instance["currency"]) && $instance["currency"] && in_array($instance["currency"], $MOBBR_SUPPORTED_CURRENCIES))?$instance["currency"]:"USD";
        $url = get_post_url();

        echo "<script type='text/javascript'>mobbr.setLightboxUrl('".MOBBR_LIGHTBOX_URL."');</script>";

        if($options['button_style'] == MOBBR_BUTTON_STYLE_CUSTOM) {
            echo "<button class='mobbr-payment-button' onClick=\"mobbr.makePayment('$url')\">$options[button_text]</button>";
        } else {
            echo "<script type='text/javascript'>mobbr.button('$url', '$currency');</script>";
        }
    }
}
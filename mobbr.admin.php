<?php
require_once("mobbr.config.php");
require_once("mobbr.utils.php");

function plugin_admin_add_page() {
    add_options_page('Mobbr Payments', 'Mobbr Payments', 'manage_options', 'mobbr-payments', 'mobbr_plugin_options_page');
}

function mobbr_plugin_options_page() {
    ?>
    <div>
        <h2>Mobbr Payments</h2>
        <form action="options.php" method="post">
            <?php settings_fields('mobbr_plugin_options'); ?>
            <?php do_settings_sections('mobbr_plugin'); ?>

            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class="button button-primary" />
        </form>
    </div>

<?php
}

function plugin_admin_init()  {
    register_setting( 'mobbr_plugin_options', 'mobbr_plugin_options', 'mobbr_plugin_options_validate' );

    add_settings_section('mobbr_plugin_appearance', 'Appearance Settings', 'mobbr_plugin_section_appearance_text', 'mobbr_plugin');
    add_settings_field('mobbr_plugin_require_auth', 'Require Authentication', 'mobbr_plugin_setting_require_auth', 'mobbr_plugin', 'mobbr_plugin_appearance');
    add_settings_field('mobbr_plugin_placement_options', 'Button Placement', 'mobbr_plugin_setting_placement_options', 'mobbr_plugin', 'mobbr_plugin_appearance');
    add_settings_field('mobbr_plugin_button_style', 'Button Style', 'mobbr_plugin_setting_button_style', 'mobbr_plugin', 'mobbr_plugin_appearance');
    add_settings_field('mobbr_plugin_button_position', 'Button Position', 'mobbr_plugin_setting_button_position', 'mobbr_plugin', 'mobbr_plugin_appearance');
    add_settings_field('mobbr_plugin_button_text', 'Button Text', 'mobbr_plugin_setting_button_text', 'mobbr_plugin', 'mobbr_plugin_appearance');

    add_settings_section('mobbr_plugin_payment_details', 'Payment Details', 'mobbr_plugin_section_payment_details_text', 'mobbr_plugin');
    add_settings_field('mobbr_plugin_payment_email', 'Email', 'mobbr_plugin_setting_payment_email', 'mobbr_plugin', 'mobbr_plugin_payment_details');
    add_settings_field('mobbr_plugin_payment_share', 'Share', 'mobbr_plugin_setting_payment_share', 'mobbr_plugin', 'mobbr_plugin_payment_details');
}

function mobbr_plugin_section_appearance_text() {
    echo '<p>Customize the appearance and placement of the Mobbr payment button.</p>';
}

function mobbr_plugin_section_payment_details_text() {
    echo '<p>Set email address and share of website owner.</p>';
}

function mobbr_plugin_setting_placement_options() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_placement_options' name='mobbr_plugin_options[placement_options][]' type='checkbox' value='".MOBBR_BUTTON_PLACEMENT_POSTS."' ".((isset($options['placement_options']) && in_array(MOBBR_BUTTON_PLACEMENT_POSTS, $options['placement_options']))?"checked":"")."/> Display on Posts<br/>";
    echo "<input id='mobbr_plugin_placement_options' name='mobbr_plugin_options[placement_options][]' type='checkbox' value='".MOBBR_BUTTON_PLACEMENT_PAGES."' ".((isset($options['placement_options']) && in_array(MOBBR_BUTTON_PLACEMENT_PAGES, $options['placement_options']))?"checked":"")."/> Display on Pages";
}

function mobbr_plugin_setting_button_style() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_button_style' name='mobbr_plugin_options[button_style]' type='radio' value='".MOBBR_BUTTON_STYLE_OFFICIAL."' ".((isset($options['button_style']) && $options['button_style'] == MOBBR_BUTTON_STYLE_OFFICIAL)?"checked":"")." /> Mobbr Payment Button<br/>";
    echo "<input id='mobbr_plugin_button_style' name='mobbr_plugin_options[button_style]' type='radio' value='".MOBBR_BUTTON_STYLE_CUSTOM."' ".((isset($options['button_style']) && $options['button_style'] == MOBBR_BUTTON_STYLE_CUSTOM)?"checked":"")."/> Custom Button";
}

function mobbr_plugin_setting_button_position() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_button_position' name='mobbr_plugin_options[button_position]' type='radio' value='".MOBBR_BUTTON_POSITION_TOP."' ".((isset($options['button_position']) && $options['button_position'] == MOBBR_BUTTON_POSITION_TOP)?"checked":"")." /> Top<br/>";
    echo "<input id='mobbr_plugin_button_position' name='mobbr_plugin_options[button_position]' type='radio' value='".MOBBR_BUTTON_POSITION_BOTTOM."' ".((isset($options['button_position']) && $options['button_position'] == MOBBR_BUTTON_POSITION_BOTTOM)?"checked":"")."/> Bottom<br/>";
    echo "<input id='mobbr_plugin_button_position' name='mobbr_plugin_options[button_position]' type='radio' value='".MOBBR_BUTTON_POSITION_WIDGET."' ".((isset($options['button_position']) && $options['button_position'] == MOBBR_BUTTON_POSITION_WIDGET)?"checked":"")."/> Widget Area<br/>";
}

function mobbr_plugin_setting_button_text() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_button_text' name='mobbr_plugin_options[button_text]' type='text' value='".(isset($options['button_text'])?$options['button_text']:"")."' />";
}

function mobbr_plugin_setting_require_auth() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_require_auth' name='mobbr_plugin_options[require_auth]' type='checkbox' value='yes' ".((isset($options['require_auth']) && $options['require_auth'])?"checked":"")."/><br/>";
}

function mobbr_plugin_setting_payment_email() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_payment_email' name='mobbr_plugin_options[email]' type='text' value='".(isset($options['email'])?$options['email']:"")."' />";
}

function mobbr_plugin_setting_payment_share() {
    $options = get_mobbr_plugin_options();
    echo "<input id='mobbr_plugin_payment_share' name='mobbr_plugin_options[share]' type='text' value='".(isset($options['share'])?$options['share']:"")."' /> %";
}

function mobbr_plugin_options_validate($input) {
    $options = get_mobbr_plugin_options();
    $options['require_auth'] = isset($input['require_auth']);
    if(isset($input['placement_options']) && is_array($options['placement_options'])) {
        $options['placement_options'] = $input['placement_options'];
    } else {
        $options['placement_options'] = array();
    }
    if(isset($input['button_style']) && $input['button_style']) {
        $options['button_style'] = sanitize_text_field($input['button_style']);
    }
    if(isset($input['button_position']) && $input['button_position']) {
        $options['button_position'] = sanitize_text_field($input['button_position']);
    }
    if(isset($input['button_text']) && $input['button_text']) {
        $options['button_text'] = sanitize_text_field($input['button_text']);
    }
    if(isset($input['email']) && is_email($input['email'])) {
        $options['email'] = sanitize_email($input['email']);
    }
    if(isset($input['share'])) {
        $share = (int) $input['share'];
        if($share >= 0 && $share <= 100) {
            $options['share'] = $share;
        }
    }
    return $options;
}
?>
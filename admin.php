<?php
// If this file is called directly, abort.
defined('ABSPATH') or die;

// The Default Value for each of the options
$settings_defaults = self::SETTINGS_DEFAULT;

// Save Settings
if ($_POST['submit'] ?? false) {

    if (!isset($_POST['rbi_nonce']))
        wp_die("Nonce failed, try again.");

    if (wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rbi_nonce'])), 'rbi_nonce') == false)
        wp_die("Nonce failed, try again.");


    $settings['img_url'] = isset($_POST['img_url']) ? sanitize_url(trim($_POST['img_url'])) : '';


    // Validate Url
    $res = self::test_url($settings['img_url']);
    if(empty($res)){
        add_settings_error('ERT_SETTINGS', 'VALID_ERT_SETTINGS', 'Invalid Url.', 'error');
    }
    
    update_option(self::SETTINGS_NAME, $settings);

    add_settings_error('RBI_SETTINGS', 'VALID_RBI_SETTINGS', 'Updated successfully.', 'updated');
}

// Reset Settings
elseif ($_POST['reset_all'] ?? false) {
    update_option(self::SETTINGS_NAME, $settings_defaults);
    add_settings_error('ERT_SETTINGS', 'VALID_ERT_SETTINGS', 'Updated successfully.', 'warning');
}

$settings = get_option(self::SETTINGS_NAME, $settings_defaults);


// Code Shorteners
$s = $settings;

// Display settings errors
settings_errors('RBI_SETTINGS');

?>
<div class="wrap">
    <h1><?php esc_html_e('Replace Broken Image Settings', 'replace-broken-images') ?></h1>
    <div class="row" style="display: flex;justify-content: space-between;">
        <div class="col" style="width: 80vw;">
            <form method="post">
                <?php wp_nonce_field('rbi_nonce', 'rbi_nonce'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Replacement Image URL', 'replace-broken-images'); ?></th>
                        <td><input type="text" name="img_url" value="<?php echo esc_attr($s['img_url']); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <div class="col" style="width: 20vw;">
        </div>
    </div>
</div>


<style>
    .form-table tr>th {
        width: 30%;
    }

    .form-table tr>td {
        width: 70%;
    }

    .form-table input[type="text"] {
        width: 100%;
        text-align: left !important;
    }

    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 30px;
        height: 17px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 13px;
        width: 13px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(13px);
        -ms-transform: translateX(13px);
        transform: translateX(13px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<?php
// If this file is called directly, abort.
defined('ABSPATH') or die;

// The Default Value for each of the options
$settings_defaults = ReplaceBrokenImages::SETTINGS_DEFAULT;

// Save Settings
if ($_POST['submit'] ?? false) {

    if (!isset($_POST['rbi_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rbi_nonce'])), 'rbi_nonce')) {
        wp_die("Nonce failed, try again.");
    }

    $img_url = isset($_POST['img_url']) ? sanitize_text_field(trim($_POST['img_url'])) : '';

    // Validate URL: Ensure it's a proper URL and ends with an image extension
    if (filter_var($img_url, FILTER_VALIDATE_URL) && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i', $img_url)) {
        $settings = ['img_url' => $img_url];
        update_option(ReplaceBrokenImages::SETTINGS_NAME, $settings);
        add_settings_error('RBI_SETTINGS', 'VALID_RBI_SETTINGS', 'Updated successfully.', 'updated');
    } else {
        add_settings_error('RBI_SETTINGS', 'INVALID_RBI_SETTINGS', 'Invalid URL or not an image.', 'error');
    }
}

// Reset Settings
elseif ($_POST['reset_all'] ?? false) {
    update_option(ReplaceBrokenImages::SETTINGS_NAME, $settings_defaults);
    add_settings_error('RBI_SETTINGS', 'VALID_RBI_SETTINGS', 'Settings reset successfully.', 'warning');
}

$settings = get_option(ReplaceBrokenImages::SETTINGS_NAME, $settings_defaults);

// Display settings errors
settings_errors('RBI_SETTINGS');
?>

<div class="wrap">
    <h1><?php esc_html_e('Replace Broken Images Settings', 'replace-broken-images'); ?></h1>
    <form method="post">
        <?php wp_nonce_field('rbi_nonce', 'rbi_nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Replacement Image URL', 'replace-broken-images'); ?></th>
                <td>
                    <input type="text" name="img_url" id="img_url" value="<?php echo esc_attr($settings['img_url']); ?>" style="width: 70%;" />
                    <button type="button" id="select-image" class="button"><?php esc_html_e('Select Image', 'replace-broken-images'); ?></button>
                    <p class="description"><?php esc_html_e('Select or upload an image to replace broken images.', 'replace-broken-images'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Image Preview', 'replace-broken-images'); ?></th>
                <td>
                    <img id="image-preview" src="<?php echo esc_url($settings['img_url']); ?>" alt="<?php esc_html_e('Preview Image', 'replace-broken-images'); ?>" style="max-width: 100px; max-height: 100px;" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
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
    }

    #image-preview {
        display: block;
        max-width: 100px;
        max-height: 100px;
        border: 1px solid #ddd;
        margin-top: 10px;
    }
</style>
<?php
function src_register_settings() {
    add_option('src_show_name_field', '1');
    add_option('src_show_email_field', '1');
    add_option('src_rating_input_type', 'stars');

    // Replace this url with google reviews link
    add_option('src_google_reviews_url', 'https://www.github.com');

    // Redirect after review submit
    $home_url = get_home_url();
    add_option('src_redirect_after_submit_url', $home_url);
    
    // Add site owners email address
    add_option('src_site_owner_email', get_option('admin_email'));

    register_setting('src_options_group', 'src_show_name_field');
    register_setting('src_options_group', 'src_show_email_field');
    register_setting('src_options_group', 'src_rating_input_type');
    register_setting('src_options_group', 'src_google_reviews_url', 'esc_url_raw');
    register_setting('src_options_group', 'src_redirect_after_submit_url', 'esc_url_raw');
    register_setting('src_options_group', 'src_site_owner_email', 'sanitize_email');
}

add_action('admin_init', 'src_register_settings');

function src_register_options_page() {
    add_options_page('Reviews Collector', 'Reviews Collector', 'manage_options', 'reviews-collector', 'src_options_page');
}

add_action('admin_menu', 'src_register_options_page');

function src_options_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h2>Reviews Collector Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('src_options_group'); ?>
            <h3>Form Fields</h3>
            <p>
                <label for="src_show_name_field">Show Name Field:</label>
                <input type="checkbox" id="src_show_name_field" name="src_show_name_field" value="1" <?php checked(1, get_option('src_show_name_field'), true); ?> />
            </p>
            <p>
                <label for="src_show_email_field">Show Email Field:</label>
                <input type="checkbox" id="src_show_email_field" name="src_show_email_field" value="1" <?php checked(1, get_option('src_show_email_field'), true); ?> />
            </p>
            <br />
            <h3>Rating Input Type</h3>
            <p>
                <label for="src_rating_input_type">Rating Input Type:</label>
                <select id="src_rating_input_type" name="src_rating_input_type">
                    <option value="dropdown" <?php selected(get_option('src_rating_input_type'), 'dropdown'); ?>>Dropdown</option>
                    <option value="stars" <?php selected(get_option('src_rating_input_type'), 'stars'); ?>>Stars</option>
                </select>
            </p>
            <br />
            <h3>Google Reviews URL</h3>
            <p>
                <label for="src_google_reviews_url">Google Reviews URL:</label>
                <input type="text" id="src_google_reviews_url" name="src_google_reviews_url" value="<?php echo esc_attr(get_option('src_google_reviews_url')); ?>" />
            </p>
            <br />
            <h3>Email Settings</h3>
            <p>
                <label for="src_site_owner_email">Reviews will be send to this email address:</label>
                <input type="email" id="src_site_owner_email" name="src_site_owner_email" value="<?php echo esc_attr(get_option('src_site_owner_email')); ?>" />
            </p>
            <br />
            <p>
                <label for="src_redirect_after_submit_url">Redirect client after form submit (default: home url):</label>
                <input type="text" id="src_redirect_after_submit_url" name="src_redirect_after_submit_url" value="<?php echo esc_attr(get_option('src_redirect_after_submit_url')); ?>" />
            </p>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}
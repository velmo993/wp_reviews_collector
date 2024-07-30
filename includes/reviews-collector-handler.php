<?php
// Handle review submission and email notification
add_action('wp_ajax_submit_review', 'src_handle_review_submission');
add_action('wp_ajax_nopriv_submit_review', 'src_handle_review_submission');

function src_handle_review_submission() {
    check_ajax_referer('src_review_nonce', 'nonce');

    if (isset($_POST['action']) && $_POST['action'] == 'submit_review') {

        if (isset($_COOKIE['src_review_submitted'])) {
            wp_send_json_error('You have already submitted a review.');
            return;
        }

        global $wpdb;
        $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'reviews';

        $existing_review = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE ip_address = %s OR (user_id != 0 AND user_id = %d)",
            $ip_address, $user_id
        ));

        if ($existing_review > 0) {
            wp_send_json_error('You have already submitted a review.');
            return;
        }

        if (isset($_POST['review_rating']) && is_numeric($_POST['review_rating'])) {
            $review_rating = intval($_POST['review_rating']);
            if ($review_rating < 1 || $review_rating > 5) {
                wp_send_json_error('Invalid rating value.');
                return;
            }
        } else {
            wp_send_json_error('Invalid rating value.');
            return;
        }

        $review_data = array(
            'review_rating' => $review_rating,
            'review_text'   => isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '',
            'reviewer_name' => isset($_POST['reviewer_name']) ? sanitize_text_field($_POST['reviewer_name']) : '',
            'reviewer_email'=> isset($_POST['reviewer_email']) ? sanitize_email($_POST['reviewer_email']) : '',
            'ip_address'    => $ip_address,
            'user_id'       => $user_id,
        );

        if ($review_data['review_rating'] < 5) {
            src_send_review_notification($review_data);
        }

        src_save_review_to_database($review_data);

        // Set a cookie to prevent further submissions
        setcookie('src_review_submitted', '1', time() + 365*24*60*60, COOKIEPATH, COOKIE_DOMAIN, is_ssl());

        wp_send_json_success('Review submitted successfully.');
    } else {
        wp_send_json_error('Invalid request.');
    }
}

function src_send_review_notification($review_data) {
    $site_owner_email = get_option('src_site_owner_email', get_option('admin_email'));

    if (!$site_owner_email) {
        error_log('Site owner email not set.');
        return;
    }

    $subject = 'New Review Submission - Less than 5 Stars';
    // HTML Email Content
    $message = '<html><body>';
    $message .= '<h2>New Review Submission</h2>';
    $message .= '<p><strong>Rating:</strong> ' . intval($review_data['review_rating']) . ' Stars</p>';
    $message .= '<p><strong>Review Text:</strong></p>';
    $message .= '<p>' . nl2br(esc_html($review_data['review_text'])) . '</p>';
    $message .= '<p><strong>Reviewer Name:</strong> ' . esc_html($review_data['reviewer_name']) . '</p>';
    $message .= '<p><strong>Reviewer Email:</strong> ' . sanitize_email($review_data['reviewer_email']) . '</p>';
    $message .= '</body></html>';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Your Business Website <no-reply@yourbusiness.com>' // Change to your desired name and email address
    );

    $email_sent = wp_mail($site_owner_email, $subject, $message, $headers);

    if (!$email_sent) {
        error_log('Email not sent.');
    } else {
        error_log('Email sent successfully.');
    }
}

function src_save_review_to_database($review_data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $wpdb->insert(
        $table_name,
        array(
            'review_rating'   => $review_data['review_rating'],
            'review_text'     => $review_data['review_text'],
            'reviewer_name'   => $review_data['reviewer_name'],
            'reviewer_email'  => $review_data['reviewer_email'],
            'ip_address'      => $review_data['ip_address'],
            'user_id'         => $review_data['user_id'],
            'submission_date' => current_time('mysql')
        ),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        )
    );
}
?>
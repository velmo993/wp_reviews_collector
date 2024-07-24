<?php
// Shortcode function to display the review form
function src_review_form_shortcode() {
    $show_name = get_option('src_show_name_field') == '1';
    $show_email = get_option('src_show_email_field') == '1';
    $rating_input_type = get_option('src_rating_input_type');

    ob_start();
    ?>
    <form id="ratingForm" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
        <label for="review_rating">How would you rate us?:</label>
        <div class="star-rating">
            <input type="radio" name="review_rating" value="5" id="rating1">
            <label for="rating1">&#9733;</label>
            <input type="radio" name="review_rating" value="4" id="rating2">
            <label for="rating2">&#9733;</label>
            <input type="radio" name="review_rating" value="3" id="rating3">
            <label for="rating3">&#9733;</label>
            <input type="radio" name="review_rating" value="2" id="rating4">
            <label for="rating4">&#9733;</label>
            <input type="radio" name="review_rating" value="1" id="rating5">
            <label for="rating5">&#9733;</label>
        </div>
    </form>

    <form id="reviewForm" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" style="display:none;">
        <?php if ($show_name) : ?>
            <label for="reviewer_name">Name (optional):</label>
            <input type="text" name="reviewer_name" id="reviewer_name">
            <br>
        <?php endif; ?>
        <?php if ($show_email) : ?>
            <label for="reviewer_email">Email (optional):</label>
            <input type="email" name="reviewer_email" id="reviewer_email">
            <br>
        <?php endif; ?>
        <label for="review_text">Your Feedback (Please let us know what we can do to improve):</label>
        <textarea name="review_text" id="review_text" required></textarea>
        <br>
        <input type="hidden" name="action" value="submit_review">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('src_review_nonce')); ?>">
        <button type="submit">Submit Review</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('src_review_form', 'src_review_form_shortcode');

?>
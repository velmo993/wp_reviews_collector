jQuery(document).ready(function($) {
    // Handle rating selection
    $('.star-rating input').on('change', function() {
        var rating = $('input[name="review_rating"]:checked').val();
        if (rating == 5) {
            window.location.href = srcSettings.googleReviewsUrl;
            return false;
        } else {
            $('#ratingForm').hide();
            $('#reviewForm').show();
        }
    });

    // Handle review form submission
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();

        var rating = $('input[name="review_rating"]:checked').val();
        var formData = $(this).serialize();
        formData += '&action=submit_review';
        formData += '&review_rating=' + rating;

        $.ajax({
            type: 'POST',
            url: srcSettings.ajaxurl,
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Thank you for your feedback! It helps a lot for us!');
                    $('#reviewForm')[0].reset();
                    $('#reviewForm').hide();
                    $('#ratingForm').show();
                    window.location.href = srcSettings.redirectAfterFormSubmit;
                } else {
                    alert(response.data);
                }
            },
            error: function(xhr, status, error) {
                alert('An unexpected error occurred. Please try again.');
            }
        });

        return false; // Prevent default form submission
    });
});
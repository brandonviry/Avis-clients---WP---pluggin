/**
 * Front-end JavaScript for review form
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Rating stars interaction
        const $ratingInput = $('#sac-rating-input');
        const $stars = $ratingInput.find('.sac-star');
        const $hiddenRating = $('#sac-review-rating');
        const $ratingText = $ratingInput.find('.sac-rating-text');

        const ratingLabels = {
            1: sacData.strings.rating_1 || 'Très mauvais',
            2: sacData.strings.rating_2 || 'Mauvais',
            3: sacData.strings.rating_3 || 'Moyen',
            4: sacData.strings.rating_4 || 'Bon',
            5: sacData.strings.rating_5 || 'Excellent'
        };

        // Hover effect
        $stars.on('mouseenter', function() {
            const rating = $(this).data('rating');
            updateStarsDisplay(rating);
            $ratingText.text(ratingLabels[rating]);
        });

        // Click to select
        $stars.on('click', function() {
            const rating = $(this).data('rating');
            $hiddenRating.val(rating);
            updateStarsDisplay(rating);
            $ratingText.text(ratingLabels[rating]);
            $stars.removeClass('selected');
            $(this).prevAll().addBack().addClass('selected');
        });

        // Reset on mouse leave if no selection
        $ratingInput.on('mouseleave', function() {
            const currentRating = parseInt($hiddenRating.val());
            if (currentRating > 0) {
                updateStarsDisplay(currentRating);
                $ratingText.text(ratingLabels[currentRating]);
            } else {
                $stars.text('☆').removeClass('hover');
                $ratingText.text('');
            }
        });

        function updateStarsDisplay(rating) {
            $stars.each(function(index) {
                if (index < rating) {
                    $(this).text('★').addClass('hover');
                } else {
                    $(this).text('☆').removeClass('hover');
                }
            });
        }

        // Form submission
        const $form = $('#sac-review-submission-form');
        const $messages = $('.sac-form-messages');
        const $submitButton = $form.find('.sac-submit-button');
        const $buttonText = $submitButton.find('.sac-button-text');
        const $buttonLoader = $submitButton.find('.sac-button-loader');

        $form.on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            $messages.empty().removeClass('success error');

            // Validate rating
            const rating = parseInt($hiddenRating.val());
            if (rating < 1 || rating > 5) {
                showMessage('error', sacData.strings.select_rating || 'Veuillez sélectionner une note');
                return false;
            }

            // Disable submit button
            $submitButton.prop('disabled', true);
            $buttonText.hide();
            $buttonLoader.show();

            // Prepare form data
            const formData = {
                action: 'sac_submit_review',
                nonce: sacData.nonce,
                review_author: $('#sac-review-author').val(),
                review_email: $('#sac-review-email').val(),
                review_title: $('#sac-review-title').val(),
                review_rating: rating,
                review_content: $('#sac-review-content').val()
            };

            // Submit via AJAX
            $.ajax({
                url: sacData.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage('success', response.data.message);
                        $form[0].reset();
                        $hiddenRating.val('0');
                        $stars.text('☆').removeClass('selected hover');
                        $ratingText.text('');

                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: $messages.offset().top - 100
                        }, 500);
                    } else {
                        showMessage('error', response.data.message || sacData.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    showMessage('error', sacData.strings.error);
                },
                complete: function() {
                    // Re-enable submit button
                    $submitButton.prop('disabled', false);
                    $buttonText.show();
                    $buttonLoader.hide();
                }
            });

            return false;
        });

        function showMessage(type, message) {
            $messages
                .removeClass('success error')
                .addClass(type)
                .html('<p>' + escapeHtml(message) + '</p>')
                .show();
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Character counter for textarea
        const $textarea = $('#sac-review-content');
        const maxLength = $textarea.attr('maxlength');

        if (maxLength) {
            const $counter = $('<small class="sac-char-counter"></small>');
            $textarea.after($counter);

            function updateCounter() {
                const remaining = maxLength - $textarea.val().length;
                $counter.text(remaining + ' caractères restants');

                if (remaining < 100) {
                    $counter.addClass('warning');
                } else {
                    $counter.removeClass('warning');
                }
            }

            $textarea.on('input', updateCounter);
            updateCounter();
        }

        // Client-side validation
        const $inputs = $form.find('input[required], textarea[required]');

        $inputs.on('blur', function() {
            const $input = $(this);
            const $group = $input.closest('.sac-form-group');
            $group.find('.sac-validation-error').remove();

            if (!$input.val().trim()) {
                $input.addClass('error');
                $group.append('<small class="sac-validation-error">' + sacData.strings.required + '</small>');
            } else {
                $input.removeClass('error');
            }
        });

        $inputs.on('input', function() {
            const $input = $(this);
            if ($input.val().trim()) {
                $input.removeClass('error');
                $input.closest('.sac-form-group').find('.sac-validation-error').remove();
            }
        });
    });

})(jQuery);

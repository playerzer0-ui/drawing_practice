$(function () {
    const $formContainer = $('.form-container');

    $('.toggle-btn').on('click', function (e) {
        e.preventDefault();

        $formContainer.toggleClass('flipped');

        if ($formContainer.hasClass('flipped')) {
            // Clear login form
            $('#login-email, #login-password').val('');
        } else {
            // Clear register form
            $('#reg-username, #reg-email, #reg-password').val('');
        }
    });

    // ESC to flip back
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $formContainer.hasClass('flipped')) {
            $formContainer.removeClass('flipped');
        }
    });
});

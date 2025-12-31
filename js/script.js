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

// ---- Loader helpers (single source of truth) ----
function showLoader() {
    const loader = document.getElementById("loader") || document.querySelector(".loader-UI");
    if (loader) loader.style.display = "flex";
}

function hideLoader() {
    const loader = document.getElementById("loader") || document.querySelector(".loader-UI");
    if (loader) loader.style.display = "none";
}

// ---- jQuery navigation handling ----
$(document).ready(function () {

    $("a").on("click", function (e) {
        const href = $(this).attr("href");

        // Skip anchors without navigation
        if (!href || href.startsWith("#")) return;

        e.preventDefault();
        showLoader();

        // Let browser repaint before navigation
        setTimeout(() => {
            window.location.href = href;
        }, 50);
    });

    $("form").on("submit", function (e) {
        e.preventDefault(); // prevent immediate submission
        const form = this;
        showLoader();

        // Give browser time to render loader, then submit
        setTimeout(() => {
            form.submit();
        }, 50);
    });

    $("input[type='file'][name='image']").on("change", function () {
        showLoader();
        // submit the form programmatically
        this.form.submit();
    });

});




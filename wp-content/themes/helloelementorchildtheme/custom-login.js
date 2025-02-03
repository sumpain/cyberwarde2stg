jQuery(document).ready(function($) {
    // Check if the error message exists
    if (loginErrorData.errorMessage) {
        // Append the error message to a div or specific element on the page
        $('.registration-login-form').prepend('<div class="login-error" style="color: red;">' + loginErrorData.errorMessage + '</div>');
    }
});
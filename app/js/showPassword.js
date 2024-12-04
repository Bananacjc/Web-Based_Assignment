$(document).ready(function() {
    $('#togglePassword').on('click', function () {
        var $passwordInput = $('#password');
        if ($passwordInput.attr('type') === 'password') {
            $passwordInput.attr('type', 'text');
            $(this).removeClass('ti-eye-off').addClass('ti-eye');
        } else {
            $passwordInput.attr('type', 'password');
            $(this).removeClass('ti-eye').addClass('ti-eye-off');
        }
    });

    $('#toggleConfirmPassword').on('click', function () {
        var $confirmPasswordInput = $('#confirm-password');
        if ($confirmPasswordInput.attr('type') === 'password') {
            $confirmPasswordInput.attr('type', 'text');
            $(this).removeClass('ti-eye-off').addClass('ti-eye');
        } else {
            $confirmPasswordInput.attr('type', 'password');
            $(this).removeClass('ti-eye').addClass('ti-eye-off');
        }
    });
});

$(document).ready(function () {
    function togglePasswordVisibility($toggleButton, $passwordInput) {
        const isPassword = $passwordInput.attr('type') === 'password';
        $passwordInput.attr('type', isPassword ? 'text' : 'password');
        $toggleButton
            .toggleClass('ti-eye', isPassword)
            .toggleClass('ti-eye-off', !isPassword);
    }

    $('#togglePassword').on('click', function () {
        togglePasswordVisibility($(this), $('#password'));
    });

    $('#toggleConfirmPassword').on('click', function () {
        togglePasswordVisibility($(this), $('#confirm-password'));
    });
});

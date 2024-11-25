document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.classList.replace('ti-eye-off', 'ti-eye');
    } else {
        passwordInput.type = 'password';
        this.classList.replace('ti-eye', 'ti-eye-off');
    }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
    const confirmPasswordInput = document.getElementById('confirm-password');
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        this.classList.replace('ti-eye-off', 'ti-eye');
    } else {
        confirmPasswordInput.type = 'password';
        this.classList.replace('ti-eye', 'ti-eye-off');
    }
});

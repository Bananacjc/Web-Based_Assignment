document.addEventListener('DOMContentLoaded', function() {
    var inputs = document.querySelectorAll('.input-box');

    inputs.forEach(input => {
        input.addEventListener('keyup', updateClass);
        input.addEventListener('change', updateClass);
        input.addEventListener('focus', updateClass);

        function updateClass() {
            if (input.value.trim() !== '') {
                input.classList.add('has-content');
            } else {
                input.classList.remove('has-content');
            }
        }

        // Initialize on page load
        updateClass();
    });
});

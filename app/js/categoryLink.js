$(document).ready(function () {
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault(); // Prevent default anchor click behavior
        const target = $(this.getAttribute('href')); // Get the target element
        if (target.length) { // Check if the target exists
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 500); // Smooth scroll to the target
        }
    });
});
$(document).ready(function () {
    let lastScrollTop = 0;

    $(window).on("scroll", function () {
        let currentScroll = $(this).scrollTop();
        let header = $("#header");

        if (currentScroll > lastScrollTop && currentScroll > 80) {
            // Scrolling down
            header.addClass("hidden");
        } else if (currentScroll < lastScrollTop) {
            // Scrolling up
            header.removeClass("hidden");
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Prevent negative scroll
    });
});

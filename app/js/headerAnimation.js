$(() => {
    
    /**
     * Allow header to stay 'hidden' when body is scrolled beyond the header height
     * Header will stay 'hidden' when scrolled down, and appear when scrolled up
     */
    let header = $('#header');
    let body = $(document.body);
    let lastScrollTop = 0;
    $(body).on("scroll", () => {
        let currentScroll = body.scrollTop();

        if (currentScroll > lastScrollTop && currentScroll > header.height()) {
            header.addClass('hidden');
            console.log('added')
        } else if (currentScroll < lastScrollTop) {
            header.removeClass('hidden');
        }

        lastScrollTop = Math.abs(currentScroll);
    });

})

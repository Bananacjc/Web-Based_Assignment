document.addEventListener("DOMContentLoaded", () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add 'active' class every time element comes into view
                entry.target.classList.add('active');
            } else {
                // Remove 'active' class when element is not in view
                entry.target.classList.remove('active');
            }
        });
    }, {
        rootMargin: '0px',
        threshold: 0.01 // Adjusted to trigger even if only 1% of the element is in view
    });

    document.querySelectorAll('.container2 .cont').forEach(cont => {
        observer.observe(cont);
    });
});



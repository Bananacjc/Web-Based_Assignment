document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.sidebar li');
    const contentDivs = document.querySelectorAll('.content');

    // Function to activate a specific tab
    function activateTab(tabId) {
        // Hide all content divs and remove active class
        contentDivs.forEach(div => (div.style.display = 'none'));
        buttons.forEach(btn => btn.classList.remove('active'));

        // Show the selected content div and add active class
        const targetContent = document.getElementById(tabId.replace('-btn', '-content'));
        const targetButton = document.getElementById(tabId);

        if (targetContent) {
            targetContent.style.display = 'block';
        }
        if (targetButton) {
            targetButton.classList.add('active');
        }
    }

    // Initialize based on URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('activeTab') || 'personal-info-btn';
    activateTab(activeTab);

    // Add event listeners to sidebar buttons
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.id;

            // Update the URL
            const newUrl = `${window.location.pathname}?activeTab=${tabId}`;
            window.history.replaceState(null, '', newUrl);

            // Activate the clicked tab
            activateTab(tabId);
        });
    });
});

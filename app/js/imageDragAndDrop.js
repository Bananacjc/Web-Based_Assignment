function previewFile() {
    const preview = document.getElementById('image-preview');
    const fileInput = document.getElementById('profile-pic');
    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        reader.readAsDataURL(file);
    } else {
        preview.src = "data:image/jpeg;base64,${customer.image}";
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('profile-pic');
    const dragOverlay = document.getElementById('drag-overlay');

    // Prevent default behavior for drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('dragover');
        }, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files && files.length > 0) {
            fileInput.files = files; // Set the files to the input
            previewFile(); // Call your existing preview function
        }
    }

    // Optional: Allow clicking on drop zone to open file dialog
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });
});


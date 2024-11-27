function previewFile() {
    const preview = document.getElementById('image-preview'); // The image container
    const file = document.getElementById('profile-pic').files[0]; // The selected file
    const reader = new FileReader();
    
    reader.onloadend = function() {
        preview.src = reader.result; // Set the preview image src to the loaded file
        preview.style.display = 'block'; // Make sure the preview is visible
    }

    if (file) {
        reader.readAsDataURL(file); // Read the file as a data URL (base64)
    } else {
        preview.src = ""; // No file selected, clear the preview
        preview.style.display = 'none'; // Hide the preview if no file is selected
    }
}

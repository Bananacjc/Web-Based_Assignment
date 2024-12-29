    function showAddForm() {
        document.getElementById("addCategoryModal").style.display = "block";
    }

    function hideAddForm() {
        document.getElementById("addCategoryModal").style.display = "none";
    }

    function showUpdateCategoryForm() {
    var modal = document.getElementById('updateModal');
    var form = document.getElementById('updateForm');
    modal.style.display = "block";

    updateImage();
}

function updateImage() {
    var select = document.getElementById('categorySelect');
    var selectedOption = select.options[select.selectedIndex];
    var selectedImage = selectedOption.getAttribute('data-image');

    document.getElementById('current_image').src = selectedImage;
}
    function hideUpdateForm() {
        document.getElementById("updateModal").style.display = "none";
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this category?');
    }

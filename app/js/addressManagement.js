$(document).ready(function () {
    const $addressInput = $("#address-input");
    const $addressIndexInput = $("#address-index");
    const $actionInput = $("#action");
    const $saveButton = $("#save-address-btn");
    const $addressForm = $("#address-form");

    // Handle edit button clicks using event delegation
    $(document).on("click", ".edit-address-btn", function (e) {
        e.preventDefault();
        const $row = $(this).closest("tr");
        const index = $(this).data("index");
        const address = $row.find(".address-text").text();

        // Populate the input fields for editing
        $addressInput.val(address);
        $addressIndexInput.val(index);
        $actionInput.val("edit-address");
        $saveButton.text("Update Address");

        // Focus the input box
        $addressInput.focus();
    });

    // Handle delete button clicks using event delegation
    $(document).on("click", ".delete-address-btn", function (e) {
        e.preventDefault();
        const index = $(this).data("index");

        // Confirm deletion
        if (confirm("Are you sure you want to delete this address?")) {
            // Update the form for deletion
            $actionInput.val("delete-address");
            $addressIndexInput.val(index);

            // Submit the form
            $addressForm.submit();
        }
    });

    // Reset form when input is cleared
    $addressInput.on("input", function () {
        if ($addressInput.val().trim() === "") {
            // Reset to "Add Address" state
            $actionInput.val("save-address");
            $addressIndexInput.val("");
            $saveButton.text("Add Address");
        }
    });

    // Reset form to default after submission
    $saveButton.on("click", function () {
        setTimeout(() => {
            $saveButton.text("Add Address");
            $actionInput.val("save-address");
            $addressIndexInput.val("");
            $addressInput.val("");
        }, 500); // Reset the form after submission
    });
});

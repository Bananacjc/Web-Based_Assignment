$(document).ready(function () {
    const $line1Input = $("#line-1");
    const $villageInput = $("#village");
    const $postalCodeInput = $("#postal-code");
    const $cityInput = $("#city");
    const $stateInput = $("#state");
    const $addressIndexInput = $("#address-index");
    const $actionInput = $("#action");
    const $saveButton = $("#save-address-btn");
    const $addressForm = $("#address-form");

    // Handle edit button clicks using event delegation
    $(document).on("click", ".edit-address-btn", function (e) {
        e.preventDefault();

        // Retrieve address data from data attributes
        const $button = $(this);
        const index = $button.data("index");
        const line1 = $button.data("line1");
        const village = $button.data("village");
        const postalCode = $button.data("postalcode");
        const city = $button.data("city");
        const state = $button.data("state");

        // Populate the input fields for editing
        $line1Input.val(line1);
        $villageInput.val(village);
        $postalCodeInput.val(postalCode);
        $cityInput.val(city);
        $stateInput.val(state);
        $addressIndexInput.val(index);
        $actionInput.val("edit-address");
        $saveButton.text("Update Address");

        // Focus the first input field
        $line1Input.focus();
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

    // Reset form when all inputs are cleared
    $("input").on("input", function () {
        if (
            !$line1Input.val().trim() &&
            !$villageInput.val().trim() &&
            !$postalCodeInput.val().trim() &&
            !$cityInput.val().trim() &&
            !$stateInput.val().trim()
        ) {
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
            $line1Input.val("");
            $villageInput.val("");
            $postalCodeInput.val("");
            $cityInput.val("");
            $stateInput.val("");
        }, 500); // Reset the form after submission
    });
});

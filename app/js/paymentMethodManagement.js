$(document).ready(function () {
    const $bankAccountInput = $("#bank-account-input");
    const $bankCVVInput = $("#bank-cvv-input");
    const $bankExpiryInput = $("#expiry-date-input"); // Correct ID for expiry input
    const $bankIndexInput = $("#bank-index");
    const $actionInput = $("#bank-action");
    const $saveButton = $("#save-bank-btn");
    const $bankForm = $("#bank-form");

    // Handle edit button clicks using event delegation
    $(document).on("click", ".edit-bank-btn", function (e) {
        e.preventDefault();
        const $row = $(this).closest("tr");
        const index = $(this).data("index");
        const account = $row.find(".bank-account").text().trim();
        const cvv = $row.find(".bank-cvv").text().trim();
        const expiry = $row.find(".bank-expiry").text().trim(); // Extract text

        // Parse expiry date to match 'YYYY-MM'
        const formattedExpiry = expiry.length === 7 ? expiry : ""; // Ensure format is YYYY-MM

        // Populate the input fields for editing
        $bankAccountInput.val(account);
        $bankCVVInput.val(cvv);
        $bankExpiryInput.val(formattedExpiry); // Apply to month input
        $bankIndexInput.val(index);
        $actionInput.val("edit-bank");
        $saveButton.text("Update Bank");

    });

    // Handle delete button clicks using event delegation
    $(document).on("click", ".delete-bank-btn", function (e) {
        e.preventDefault();
        const index = $(this).data("index");

        // Confirm deletion
        if (confirm("Are you sure you want to delete this bank?")) {
            // Update the form for deletion
            $actionInput.val("delete-bank");
            $bankIndexInput.val(index);

            // Submit the form
            $bankForm.submit();
        }
    });

    // Reset form when input is cleared
    $bankAccountInput.add($bankCVVInput).add($bankExpiryInput).on("input", function () {
        if (
            $bankAccountInput.val().trim() === "" &&
            $bankCVVInput.val().trim() === "" &&
            $bankExpiryInput.val().trim() === ""
        ) {
            // Reset to "Add Bank" state
            $actionInput.val("save-bank");
            $bankIndexInput.val("");
            $saveButton.text("Add Bank");
        }
    });

    // Reset form to default after submission
    $saveButton.on("click", function () {
        setTimeout(() => {
            $saveButton.text("Add Bank");
            $actionInput.val("save-bank");
            $bankIndexInput.val("");
            $bankAccountInput.val("");
            $bankCVVInput.val("");
            $bankExpiryInput.val("");
        }, 500); // Reset the form after submission
    });
});

$(document).ready(function () {
    const $contactInput = $("#contact-num");
    const $contactLabel = $("#contact-label");

    $contactInput.on("focus", function () {
        $contactLabel.text("Contact Number");
    });

    $contactInput.on("blur", function () {
        if ($contactInput.val().trim() === "") {
            $contactLabel.text("Contact Number (e.g., 0123456789)");
        }
    });
});

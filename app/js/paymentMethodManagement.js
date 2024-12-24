$(document).ready(function () {
    const $bankTable = $("#bank-table tbody");
    const $ewalletTable = $("#ewallet-table tbody");

    function resetForm($form, saveAction) {
        $form.find("input").val("");
        $form.find("select").val("");
        $form.find("[name='action']").val(saveAction);
        $form.find("[name='index']").val("");
        $form.find(".btn").text("Add");
    }

    // Handle bank edit
    $bankTable.on("click", ".edit-bank-btn", function () {
        const $row = $(this).closest("tr");
        const index = $(this).data("index");

        $("#bank-container [name='name']").val($row.find(".bank-name").text());
        $("#bank-container [name='acc-num']").val($row.find(".bank-account").text());
        $("#bank-container [name='cvv']").val($row.find(".bank-cvv").text());
        $("#bank-container [name='expiry-date']").val($row.find(".bank-expiry").text());
        $("#bank-container [name='card-type']").val($row.find(".bank-card-type").text());
        $("#bank-container [name='action']").val("edit-bank");
        $("#bank-container [name='index']").val(index);
        $("#bank-container .btn").text("Update");
    });

    // Handle bank delete
    $bankTable.on("click", ".delete-bank-btn", function () {
        const index = $(this).data("index");
        if (confirm("Are you sure you want to delete this bank?")) {
            $("#bank-container [name='action']").val("delete-bank");
            $("#bank-container [name='index']").val(index);
            $("#bank-container").submit();
        }
    });

    // Handle e-wallet edit
    $ewalletTable.on("click", ".edit-wallet-btn", function () {
        const $row = $(this).closest("tr");
        const index = $(this).data("index");

        $("#e-wallet-container [name='name']").val($row.find(".wallet-name").text());
        $("#e-wallet-container [name='phone']").val($row.find(".wallet-phone").text());
        $("#e-wallet-container [name='action']").val("edit-ewallet");
        $("#e-wallet-container [name='index']").val(index);
        $("#e-wallet-container .btn").text("Update");
    });

    // Handle e-wallet delete
    $ewalletTable.on("click", ".delete-wallet-btn", function () {
        const index = $(this).data("index");
        if (confirm("Are you sure you want to delete this e-wallet?")) {
            $("#e-wallet-container [name='action']").val("delete-ewallet");
            $("#e-wallet-container [name='index']").val(index);
            $("#e-wallet-container").submit();
        }
    });

    // Reset bank form
    $("#bank-container .btn").on("click", function () {
        resetForm($("#bank-container"), "save-bank");
    });

    // Reset e-wallet form
    $("#e-wallet-container .btn").on("click", function () {
        resetForm($("#e-wallet-container"), "save-ewallet");
    });
});

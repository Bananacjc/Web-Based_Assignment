$(document).ready(function () {
    // Banks Management
    const $bankForm = $("#bank-container");
    const $bankActionInput = $("<input type='hidden' name='action' value='save-bank'>");
    const $bankIndexInput = $("<input type='hidden' name='index'>");
    $bankForm.append($bankActionInput).append($bankIndexInput);

    $(document).on("click", ".edit-bank-btn", function (e) {
        e.preventDefault();
        const $row = $(this).closest("tr");
        const index = $(this).data("index");
        const name = $row.find(".bank-name").text();
        const accNum = $row.find(".bank-account").text();
        const cvv = $row.find(".bank-cvv").text();
        const expiry = $row.find(".bank-expiry").text();
        const cardType = $row.find(".bank-card-type").text();

        $bankForm.find("[name='name']").val(name);
        $bankForm.find("[name='acc-num']").val(accNum);
        $bankForm.find("[name='cvv']").val(cvv);
        $bankForm.find("[name='expiry-date']").val(expiry);
        $bankForm.find("[name='card-type']").val(cardType);
        $bankIndexInput.val(index);
        $bankActionInput.val("edit-bank");
    });

    $(document).on("click", ".delete-bank-btn", function (e) {
        e.preventDefault();
        const index = $(this).data("index");

        if (confirm("Are you sure you want to delete this bank?")) {
            $bankActionInput.val("delete-bank");
            $bankIndexInput.val(index);
            $bankForm.submit();
        }
    });

    // Reset Bank Form
    $bankForm.on("reset", function () {
        $bankActionInput.val("save-bank");
        $bankIndexInput.val("");
    });

    // E-Wallets Management
    const $walletForm = $("#e-wallet-container");
    const $walletActionInput = $("<input type='hidden' name='action' value='save-ewallet'>");
    const $walletIndexInput = $("<input type='hidden' name='index'>");
    $walletForm.append($walletActionInput).append($walletIndexInput);

    $(document).on("click", ".edit-wallet-btn", function (e) {
        e.preventDefault();
        const $row = $(this).closest("tr");
        const index = $(this).data("index");
        const name = $row.find(".wallet-name").text();
        const phone = $row.find(".wallet-phone").text();

        $walletForm.find("[name='name']").val(name);
        $walletForm.find("[name='phone']").val(phone);
        $walletIndexInput.val(index);
        $walletActionInput.val("edit-ewallet");
    });

    $(document).on("click", ".delete-wallet-btn", function (e) {
        e.preventDefault();
        const index = $(this).data("index");

        if (confirm("Are you sure you want to delete this e-wallet?")) {
            $walletActionInput.val("delete-ewallet");
            $walletIndexInput.val(index);
            $walletForm.submit();
        }
    });

    // Reset E-Wallet Form
    $walletForm.on("reset", function () {
        $walletActionInput.val("save-ewallet");
        $walletIndexInput.val("");
    });
});



$(".input-box").on("input", function () {
    if ($(this).val().trim() !== "") {
        $(this).addClass("has-content");
    } else {
        $(this).removeClass("has-content");
    }
});


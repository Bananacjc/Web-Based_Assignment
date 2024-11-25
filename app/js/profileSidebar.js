document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.sidebar li');
    var contentDivs = document.querySelectorAll('.content');

    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = btn.id.replace('-btn', '-content');
            contentDivs.forEach(function(div) {
                div.style.display = 'none';
            });
            document.getElementById(targetId).style.display = 'block';
        });
    });
});
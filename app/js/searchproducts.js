document.addEventListener('DOMContentLoaded', function () {
    const searchBar = document.querySelector('.searchbar');
    const productCards = document.querySelectorAll('.product-card');
    const productNames = document.querySelectorAll('.product-name');

    searchBar.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        
        productCards.forEach((card, index) => {
            const name = productNames[index].textContent.toLowerCase();
            if (name.includes(searchText)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

<!DOCTYPE html>
<html>
<?php
$_title = 'Shop';
$_css = '../css/shop.css';
require '../_base.php';
include '../_head.php';
?>

<h1 class="h1 header-banner">Shop</h1>
<div id="container">
    <div id="sidebar-container">
        <div id="sidebar">
            <div id="searchbar-container">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Search" id="searchbar">
            </div>
            <h3>Categories</h3>
            <ul id="sidebar-list" class="list-style-type-none p-0">
                <li><a href="#fruits">Fruits</a></li>
                <li><a href="#vegetables">Vegetables</a></li>
                <li><a href="#juices">Juices</a></li>
                <li><a href="#meat">Meat</a></li>
                <li><a href="#cold-drinks">Cold Drinks</a></li>
                <li><a href="#breads">Breads</a></li>
            </ul>
            <h3>Filter by Price</h3>
            <input type="range" min="0" max="200" value="100" class="slider" id="price-slider">
            <span id="price-value">RM 0 - RM 200</span>
        </div>
    </div>
    <div id="main-content">
        <!-- Repeat this block for each product -->

        <h2 id="category-title" id="<%= category.toLowerCase() %>">Fruits</h2>

        <div id="product-card">
            <a href="ProductDetail?productId=<%= product.getProductId() %>" class="text-decoration-none">
                <img src="../upload/product_image/apple.webp" alt="<%= product.getProductName() %>">
                <div id="product-info">
                    <h4 class="product-name">Apple</h4>
                    <p class="rating">Rating: ★★★☆☆</p>
                    <p class="price-tag">Price: RM </p>
                </div>
            </a>
            <form action="OrderServlet" method="post">
                <input name="url" value="cart" type="hidden">
                <input type="hidden" name="productId" value="<%= product.getProductId() %>">
                <input type="hidden" name="action" value="add">
                <button id="cart-button" type="submit">Add to Cart</button>
            </form>
        </div>

    </div>
</div>

<div id="modal" style="display:block;">
    <div id="modal-content">
        <div id="product-detail-container" class="d-flex">
            <img src="data:image/jpeg;base64,<%= selectedProduct.getImage() %>" alt="<%= selectedProduct.getProductName() %>" width="250" height="250" />
            <div id="product-detail-subcontainer">
                <span id="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
                <h4 id="selected-product-name"></h4>
                <div id="rating-and-sold" class="d-flex">
                    <p id="average-rating-stars">

                        <i class="ti ti-star-filled"></i>

                        <i class="ti ti-star-half-filled"></i>

                        <i class="ti ti-star"></i>

                    </p>
                    <p id="rating-amount"></p>
                    <p id="amount-sold">0</p>
                </div>
                <p id="price">RM 0.00</p>
                <p id="description">Description</p>
            </div>
        </div>
        <div id="comment-container">
            <h4 id="comments-title">Comments</h4>


            <div id="comments">
                <img id="profile-pic" src="data:image/jpeg;base64,<%= comment.getImage() %>" alt="<%= comment.getCustomerName() %>" width="50px" height="50px" />
                <div id="comments-detail-container">
                    <p id="user-name">Customer Name</p>
                    <p id="rating-stars">

                        <i class="ti ti-star-filled"></i>

                        <i class="ti ti-star-half-filled"></i>

                        <i class="ti ti-star"></i>

                    </p>
                    <p id="date-time"></p>
                    <p id="comment"></p>
                </div>
            </div>

        </div>

        <h4>No comment</h4>

    </div>
</div>


<script src="../js/slider.js"></script>
<script src="../js/searchproducts.js"></script>
<script src="../js/categoryFilter.js"></script>
<script type="text/javascript">
    function closeModal() {
        var modal = document.getElementById('modal');
        modal.style.display = 'none';
    }
</script>

<?php include '../_foot.php'; ?>

</body>

</html>
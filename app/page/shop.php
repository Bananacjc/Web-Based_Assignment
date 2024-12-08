<!DOCTYPE html>
<html>
<?php
$_title = 'Shop';
$_css = '../css/shop.css';
require '../_base.php';
include '../_head.php';

// Fetch categories and products
$categories = [];
$products = [];
try {
    // Fetch categories
    $stmt = $_db->query("SELECT category_name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch products
    $stmt = $_db->query("
        SELECT p.product_id, p.product_name, p.category_name, p.price, p.description, 
               p.product_image, p.status, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.rating) as review_count
        FROM products p
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE p.status = 'AVAILABLE'
        GROUP BY p.product_id
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    temp('error', "Error fetching products or categories: " . $e->getMessage());
    redirect(); // Redirect to prevent further execution
}
?>

<h1 class="h1 header-banner">Shop</h1>
<div id="container">
    <div id="sidebar-container">
        <div id="sidebar">
            <div id="searchbar-container">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Search" id="searchbar">
            </div>
            <h3 class="sidebar-title">Categories</h3>
            <ul id="sidebar-list" class="list-style-type-none p-0">
                <?php foreach ($categories as $category): ?>
                    <li><a href="#<?= strtolower($category) ?>"><?= $category ?></a></li>
                <?php endforeach; ?>
            </ul>
            <h3 class="sidebar-title">Filter by Price</h3>
            <input type="range" min="0" max="200" value="100" class="slider" id="price-slider">
            <span id="price-value">RM 0 - RM 200</span>
        </div>
    </div>
    <div id="main-content">
        <!-- Repeat this block for each product -->
        <?php foreach ($categories as $category): ?>
            <h2 id="<?= strtolower($category) ?>" class="category-title"><?= $category ?></h2>
            <?php foreach ($products as $product): ?>
                <?php if ($product['category_name'] === $category): ?>
                    <div class="product-card">
                        <a href="product_detail.php?product_id=<?= $product['product_id'] ?>" class="text-decoration-none">
                            <img src="../uploads/product_images/<?= $product['product_image'] ?>" alt="<?= $product['product_name'] ?>">
                            <div class="product-info">
                                <h4 class="product-name"><?= $product['product_name'] ?></h4>
                                <p class="rating">
                                    Rating: <?= str_repeat('★', floor($product['avg_rating'])) ?>
                                    <?= str_repeat('☆', 5 - floor($product['avg_rating'])) ?>
                                    (<?= $product['review_count'] ?> reviews)
                                </p>
                                <p class="price-tag">Price: RM <?= number_format($product['price'], 2) ?></p>
                            </div>
                        </a>
                        <form action="OrderServlet" method="post">
                            <input name="url" value="cart" type="hidden">
                            <input type="hidden" name="productId" value="<?= htmlspecialchars($product['product_id']) ?>">
                            <input type="hidden" name="action" value="add">
                            <button class="cart-button">Add to Cart</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
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
                    <p id="rating-amount">100</p>
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
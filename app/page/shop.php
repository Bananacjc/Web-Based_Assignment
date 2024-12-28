<!DOCTYPE html>
<html>
<?php
$_title = 'Shop';
$_css = '../css/shop.css';
require '../_base.php';
include '../_head.php';

if (is_post()) {
    $id         = req('pID');
    $imagePath  = req('pImage');
    $cart       = get_cart();

    update_cart($id, $cart[$id] ?? 1);

    temp('cart-popup-image', $imagePath);

    redirect();
}

// Fetch categories and products
$categories = [];
$products = [];
try {
    // Fetch categories
    $stmt = $_db->query("SELECT category_name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch products
    $stmt = $_db->query("SELECT p.product_id, p.product_name, p.category_name, p.price, p.description, 
               p.product_image, p.status, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.rating) as review_count
        FROM products p
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE p.status IN ('AVAILABLE', 'OUT_OF_STOCK')
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
        <?php foreach ($categories as $category): ?>
            <div class="category-section">
                <h2 id="<?= strtolower($category) ?>" class="category-title"><?= $category ?></h2>
                <?php foreach ($products as $product): ?>
                    <?php
                    $pID = $product['product_id'];
                    $pCategory = $product['category_name'];
                    $pImage = $product['product_image'];
                    $pName = $product['product_name'];
                    $pRating = $product['avg_rating'];
                    $pReviewCount = $product['review_count'];
                    $pPrice = $product['price'];
                    $pStatus = $product['status'];

                    ?>
                    <?php if ($pCategory === $category): ?>
                        <div class="product-card">
                            <a href="javascript:void(0);" class="product-detail-link text-decoration-none" onclick="showProductDetails('<?= $pID ?>')">
                                <img src="../uploads/product_images/<?= $pImage ?>" alt="<?= $pName ?>">
                                <div class="product-info">
                                    <h4 class="product-name"><?= $pName ?></h4>
                                    <p class="rating">
                                        <?php
                                        $fullStars = floor($pRating);
                                        $halfStar = ($pRating - $fullStars >= 0.5) ? 1 : 0;
                                        $emptyStars = 5 - $fullStars - $halfStar;
                                        for ($i = 0; $i < $fullStars; $i++) echo '<i class="ti ti-star-filled"></i>';
                                        if ($halfStar) echo '<i class="ti ti-star-half-filled"></i>';
                                        for ($i = 0; $i < $emptyStars; $i++) echo '<i class="ti ti-star"></i>';
                                        ?>
                                        (<?= $pReviewCount ?> reviews)
                                    </p>
                                    <p class="price-tag">Price: RM <?= number_format($pPrice, 2) ?></p>
                                </div>
                            </a>
                            <?php if ($pStatus === 'AVAILABLE'): ?>
                                <form action="" method="post">
                                    <?= html_hidden('pID'); ?>
                                    <?= html_hidden('pImage'); ?>
                                    <input name="url" value="cart" type="hidden">
                                    <input type="hidden" name="productId" value="<?= $pID ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button class="cart-button">Add to Cart</button>
                                </form>
                            <?php elseif ($pStatus === 'OUT_OF_STOCK'): ?>
                                <div class="out-of-stock">
                                    <button class="out-of-stock-button" disabled>OUT OF STOCK</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Product Details Modal -->
<div id="productModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
        <div class="product-detail-container">
            <img id="modal-product-image" src="" alt="Product Image" />
            <div class="product-detail-subcontainer">
                <h4 id="modal-product-name"></h4>
                <div class="rating-and-sold">
                    <p id="modal-average-rating-stars"></p>
                    <p id="modal-rating-amount"></p>
                    <p id="modal-amount-sold"></p>
                </div>
                <p id="modal-price"></p>
                <p id="modal-description"></p>
            </div>
        </div>
        <div id="modal-comments" class="comment-container">
            <h4 class="comments-title">Comments</h4>
            <div id="modal-comment-list"></div>
        </div>
    </div>
</div>

<script src="../js/slider.js"></script>
<script src="../js/searchproducts.js"></script>
<script src="../js/categoryFilter.js"></script>
<script type="text/javascript">
    $('#cart-button').on('click', e => {
        e.target.form.submit();
    })

    function showProductDetails(productId) {
        fetch(`get_product_details.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Populate modal with product details
                document.getElementById('modal-product-image').src = `../uploads/product_images/${data.product.product_image}`;
                document.getElementById('modal-product-name').textContent = data.product.product_name;
                document.getElementById('modal-average-rating-stars').innerHTML = renderStars(data.product.avg_rating);
                document.getElementById('modal-rating-amount').textContent = `${data.product.review_count} Ratings`;
                document.getElementById('modal-amount-sold').textContent = `${data.product.amount_sold} Sold`;
                document.getElementById('modal-price').textContent = `Price: RM ${parseFloat(data.product.price).toFixed(2)}`;
                document.getElementById('modal-description').textContent = data.product.description;

                // Populate comments
                const commentsContainer = document.getElementById('modal-comment-list');
                commentsContainer.innerHTML = ''; // Clear previous comments
                if (data.comments && data.comments.length > 0) {
                    data.comments.forEach(comment => {
                        const commentHTML = `
                        <div class="comments">
                            <img class="profile-pic" src="../uploads/customer_images/${comment.customer_image}" alt="${comment.customer_name}" width="50px" height="50px" />
                            <div class="comments-detail-container">
                                <p class="user-name">${comment.customer_name}</p>
                                <p class="rating-stars">${renderStars(comment.rating)}</p>
                                <p class="date-time">${comment.comment_date_time}</p>
                                <p class="comment">${comment.comment}</p>
                            </div>
                        </div>`;
                        commentsContainer.innerHTML += commentHTML;
                    });
                } else {
                    commentsContainer.innerHTML = '<p>No comments available.</p>';
                }

                // Show the modal
                document.getElementById('productModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching product details:', error);
            });
    }

    function renderStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating - fullStars >= 0.5 ? 1 : 0;
    const emptyStars = 5 - fullStars - halfStar;

    let starsHTML = '';
    for (let i = 0; i < fullStars; i++) starsHTML += '<i class="ti ti-star-filled"></i>';
    if (halfStar) starsHTML += '<i class="ti ti-star-half-filled"></i>';
    for (let i = 0; i < emptyStars; i++) starsHTML += '<i class="ti ti-star"></i>';

    return starsHTML;
}


    function closeModal() {
        document.getElementById('productModal').style.display = 'none';
    }
</script>

<?php include '../_foot.php'; ?>
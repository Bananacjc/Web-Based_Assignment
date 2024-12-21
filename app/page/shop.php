<!DOCTYPE html>
<html>
<?php
$_title = 'Shop';
$_css = '../css/shop.css';
require '../_base.php';
include '../_head.php';

if (is_post()){
    $id     = req('pID');
    $cart = get_cart();

    update_cart($id, $cart[$id] ?? 1);

    // Popup add to cart

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
                        $pID= $product['product_id'];
                        $pCategory = $product['category_name'];
                        $pImage = $product['product_image'];
                        $pName = $product['product_name'];
                        $pRating = $product['avg_rating'];
                        $pReviewCount = $product['review_count'];
                        $pPrice = $product['price'];
                        $pStatus = $product['status'];

                    ?>
                    <?php if ($pCategory=== $category): ?>
                        <div class="product-card">
                            <a href="product_detail.php?product_id=<?= $pID ?>" class="text-decoration-none">
                                <img src="../uploads/product_images/<?= $pImage ?>" alt="<?= $pName ?>">
                                <div class="product-info">
                                    <h4 class="product-name"><?= $pName?></h4>
                                    <p class="rating">
                                        <?php
                                        $fullStars = floor($pRating); // Full stars
                                        $halfStar = ($pRating - $fullStars >= 0.5) ? 1 : 0; // Half star
                                        $emptyStars = 5 - $fullStars - $halfStar; // Remaining stars

                                        // Output full stars
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<i class="ti ti-star-filled"></i>';
                                        }

                                        // Output half star if applicable
                                        if ($halfStar) {
                                            echo '<i class="ti ti-star-half-filled"></i>';
                                        }

                                        // Output empty stars
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="ti ti-star"></i>';
                                        }
                                        ?>
                                        (<?=  $pReviewCount ?> reviews)
                                    </p>
                                    <p class="price-tag">Price: RM <?= number_format($pPrice, 2) ?></p>
                                </div>
                            </a>
                            <?php if ($pStatus === 'AVAILABLE'): ?>
                                <form action="" method="post">
                                    <?= html_hidden('pID');?>
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


<script src="../js/slider.js"></script>
<script src="../js/searchproducts.js"></script>
<script src="../js/categoryFilter.js"></script>
<script type="text/javascript">
    $('#cart-button').on('click', e => {
        e.target.form.submit();
    })
</script>

<?php include '../_foot.php'; ?>

</body>

</html>
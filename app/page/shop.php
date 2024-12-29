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
    // Fetch categories with products
    $stmt = $_db->query("
        SELECT DISTINCT c.category_name 
        FROM categories c
        INNER JOIN products p ON c.category_name = p.category_name
        WHERE p.status IN ('AVAILABLE', 'OUT_OF_STOCK')
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch products
    $stmt = $_db->query("
        SELECT p.product_id, p.product_name, p.category_name, p.price, p.description, 
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
            <h3 class="sidebar-title">Filter by Rating</h3>
            <div class="rating-stars" id="rating-filter">
                <i class="star ti ti-star" onclick="setFilterRating(1)"></i>
                <i class="star ti ti-star" onclick="setFilterRating(2)"></i>
                <i class="star ti ti-star" onclick="setFilterRating(3)"></i>
                <i class="star ti ti-star" onclick="setFilterRating(4)"></i>
                <i class="star ti ti-star" onclick="setFilterRating(5)"></i>
            </div>
            <input type="hidden" id="ratingFilterInput" value="0">
            <div id="filter-controls">
                <button id="reset-filters" class="cart-button">Reset Filters</button>
            </div>
        </div>
    </div>
    <div id="main-content">
        <?php foreach ($categories as $category): ?>
            <div class="category-section" data-category="<?= strtolower($category) ?>">
                <h2 id="<?= strtolower($category) ?>" class="category-title"><?= $category ?></h2>
                <div class="product-container">
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
                                        <p class="rating"><?= renderStars($product['avg_rating']) ?> (<?= $product['review_count'] ?> reviews)</p>
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
                <div id="pagination-container"></div>
                <div id="loader" style="display: none;">Loading...</div>
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
<script src="../js/filter.js"></script>
<script type="text/javascript">
    $('#cart-button').on('click', e => {
        e.target.form.submit();
    })

    //AJAX Get Product Details and Show Popup
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
                        // Check and sanitize comment text
                        const sanitizedComment = comment.comment && comment.comment !== "null" ? comment.comment.trim() : null;

                        // Check if review_image exists and is valid
                        const reviewImage = comment.review_image && comment.review_image !== "null" ? comment.review_image.trim() : null;

                        let commentHTML = `
        <div class="comments">
            <img class="profile-pic" src="../uploads/customer_images/${comment.profile_image}" alt="${comment.username}" width="50px" height="50px" />
            <div class="comments-detail-container">
                <p class="user-name">${comment.username}</p>
                <p class="comment-rating-stars">${renderStars(comment.rating)}</p>
                <p class="date-time">${comment.comment_date_time}</p>
        `;

                        // Append comment only if it exists
                        if (sanitizedComment) {
                            commentHTML += `
                <p class="comment">${sanitizedComment}</p>
            `;
                        }

                        // Append review image only if it exists
                        if (reviewImage) {
                            commentHTML += `
                <img class="review-image" src="../uploads/review_images/${reviewImage}" alt="Review Image" />
            `;
                        }

                        // Close the comment HTML structure
                        commentHTML += `
            </div>
        </div>`;

                        // Add the comment HTML to the container
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


    //AJAX For Paginations
    function fetchProducts(category, page) {
        $("#loader").show(); // Show loader
        $.ajax({
            url: "fetch_products.php",
            type: "GET",
            data: {
                category_name: category,
                page: page
            },
            dataType: "json",
            success: function(response) {
                const products = response.products;
                const totalPages = response.total_pages;

                // Render products and pagination
                renderProducts(category, products);
                renderPagination(category, page, totalPages);

                $("#loader").hide(); // Hide loader
            },
            error: function(xhr, status, error) {
                console.error("Error fetching products:", error);
                $("#loader").hide(); // Hide loader
            },
        });
    }


    function renderProducts(category, products) {
        const container = $(`#main-content .category-section[data-category="${category.toLowerCase()}"] .product-container`);
        container.html(""); // Clear existing products

        products.forEach((product) => {
            const fullStars = Math.floor(product.avg_rating);
            const halfStar = product.avg_rating - fullStars >= 0.5 ? 1 : 0;
            const emptyStars = 5 - fullStars - halfStar;

            let starsHTML = "";
            for (let i = 0; i < fullStars; i++) starsHTML += '<i class="ti ti-star-filled"></i>';
            if (halfStar) starsHTML += '<i class="ti ti-star-half-filled"></i>';
            for (let i = 0; i < emptyStars; i++) starsHTML += '<i class="ti ti-star"></i>';

            const productCard = `
            <div class="product-card">
                <a href="javascript:void(0);" class="product-detail-link text-decoration-none" onclick="showProductDetails('${product.product_id}')">
                    <img src="../uploads/product_images/${product.product_image}" alt="${product.product_name}">
                    <div class="product-info">
                        <h4 class="product-name">${product.product_name}</h4>
                        <p class="rating">
                            ${starsHTML} (${product.review_count} reviews)
                        </p>
                        <p class="price-tag">Price: RM ${parseFloat(product.price).toFixed(2)}</p>
                    </div>
                </a>
                ${
                    product.status === "AVAILABLE"
                        ? `
                        <form action="" method="post">
                            <input type="hidden" name="pID" value="${product.product_id}">
                            <input type="hidden" name="pImage" value="${product.product_image}">
                            <button class="cart-button">Add to Cart</button>
                        </form>`
                        : `
                        <div class="out-of-stock">
                            <button class="out-of-stock-button" disabled>OUT OF STOCK</button>
                        </div>`
                }
            </div>
        `;
            container.append(productCard);
        });
    }



    function renderPagination(category, currentPage, totalPages) {
        const paginationContainer = $(`#main-content .category-section[data-category="${category.toLowerCase()}"] #pagination-container`);
        paginationContainer.html(""); // Clear existing pagination

        if (totalPages > 1) {
            let paginationHTML = "";

            if (currentPage > 1) {
                paginationHTML += `<button class="pagination-btn" onclick="fetchProducts('${category}', ${currentPage - 1})">Prev</button>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `<button class="pagination-btn" onclick="fetchProducts('${category}', ${i})" class="${i === currentPage ? "active" : ""}">${i}</button>`;
            }

            if (currentPage < totalPages) {
                paginationHTML += `<button class="pagination-btn" onclick="fetchProducts('${category}', ${currentPage + 1})">Next</button>`;
            }

            paginationContainer.html(paginationHTML);
        }
    }


    $(document).ready(function() {
        $("#sidebar-list li a").on("click", function() {
            const category = $(this).text();
            fetchProducts(category, 1); // Load the first page of the selected category
        });

        // Fetch initial products for the first category
        const firstCategory = $("#sidebar-list li:first-child a").text();
        if (firstCategory) {
            fetchProducts(firstCategory, 1);
        }
    });
</script>

<?php include '../_foot.php'; ?>
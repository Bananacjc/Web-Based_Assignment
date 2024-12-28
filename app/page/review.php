<?php
$_title = 'Review';
$_css = '../css/review.css';
require '../_base.php';
include '../_head.php';

if (is_post()) {
    $productID = post('orderItemId');
    $rating = post('rating');
    $comment = post('comment');
    $customerID = $_user->customer_id;

    // Validate inputs
    if (!$productID || !$rating || !$comment) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Check if the review already exists
    $stmt = $_db->prepare("SELECT * FROM reviews WHERE customer_id = ? AND product_id = ?");
    $stmt->execute([$customerID, $productID]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
        exit;
    }

    // Insert the review
    $stmt = $_db->prepare("INSERT INTO reviews (review_id, customer_id, product_id, rating, comment, comment_date_time) VALUES (?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        generate_unique_id('REV', 'reviews', 'review_id', $_db), 
        $customerID, 
        $productID, 
        $rating, 
        $comment, 
        date('Y-m-d H:i:s')
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review.']);
    }
}

$orderID = $_GET['order_id'] ?? null; // Get the order ID from the query string
if (!$orderID) {
    temp('popup-msg', ['msg' => 'No order selected for review.', 'isSuccess' => false]);
    redirect('order_history.php');
}

// Fetch order details
$stmt = $_db->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$orderID, $_user->customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    temp('popup-msg', ['msg' => 'Order not found.', 'isSuccess' => false]);
    redirect('order_history.php');
}

// Decode order items
$orderItems = json_decode($order['order_items'], true);

// Fetch product details and review status
$products = [];
foreach ($orderItems as $productID => $quantity) {
    $stmt = $_db->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$productID]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product is already reviewed
    $reviewStmt = $_db->prepare("SELECT * FROM reviews WHERE customer_id = ? AND product_id = ?");
    $reviewStmt->execute([$_user->customer_id, $productID]);
    $review = $reviewStmt->fetch(PDO::FETCH_ASSOC);

    $products[] = [
        'product' => $product,
        'quantity' => $quantity,
        'reviewed' => $review ? true : false,
    ];
}
?>

<h1 class="h1 header-banner">Review</h1>
<table class="review-table rounded-table">
    <thead>
        <tr>
            <th class="product-header">PRODUCT</th>
            <th>PRICE (RM)</th>
            <th>QUANTITY</th>
            <th>TOTAL (RM)</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $item): 
            $product = $item['product'];
            $quantity = $item['quantity'];
            $subtotal = $product['price'] * $quantity;
        ?>
            <tr>
                <td>
                    <div class="product-info">
                        <img src="../uploads/product_images/<?= $product['product_image'] ?>" alt="Product Image" width="100">
                        <span class="product-name"><?= $product['product_name'] ?></span>
                    </div>
                </td>
                <td class="price"><?= number_format($product['price'], 2) ?></td>
                <td class="quantity"><?= $quantity ?></td>
                <td class="total-price"><?= number_format($subtotal, 2) ?></td>
                <td class="action">
                    <?php if (!$item['reviewed']): ?>
                        <a class="reviewbtn" onclick="showModal('<?= $product['product_id'] ?>')"><span>Review&nbsp;&nbsp;</span><i class="ti ti-circle-filled"></i></a>
                    <?php else: ?>
                        <a class="reviewbtn"><span>Reviewed&nbsp;&nbsp;</span><i class="ti ti-check"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="orderModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
        <form action="ReviewForm" method="post">
            <input id="orderItemIdInput" type="hidden" name="orderItemId" value="">
            <div class="form-container">
                <div class="rating-stars">
                    <i class="star ti ti-star" onclick="setRating(1)"></i>
                    <i class="star ti ti-star" onclick="setRating(2)"></i>
                    <i class="star ti ti-star" onclick="setRating(3)"></i>
                    <i class="star ti ti-star" onclick="setRating(4)"></i>
                    <i class="star ti ti-star" onclick="setRating(5)"></i>
                </div>
                <input id="ratingInput" type="hidden" name="rating" value="">
                <textarea name="comment" id="comment" rows="4" cols="50" placeholder="Leave a comment"></textarea>
                <button class="submitbtn" type="submit">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show the modal
    function showModal(orderItemId) {
        document.getElementById('orderItemIdInput').value = orderItemId;
        document.getElementById('orderModal').style.display = 'block';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    // Set rating stars
    function setRating(rating) {
        document.querySelectorAll('.rating-stars .star').forEach(function(star, index) {
            star.classList.toggle('ti-star-filled', index < rating);
        });
        document.getElementById('ratingInput').value = rating;
    }

    // Handle form submission via AJAX
    $(document).ready(function () {
        $('#reviewForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: 'review_submit.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        popup(data.message, true); // Use existing popup function
                        closeModal();
                        location.reload(); // Reload to reflect updated review status
                    } else {
                        popup(data.message, false); // Use existing popup function
                    }
                },
                error: function () {
                    popup('An unexpected error occurred.', false); // Use existing popup function
                }
            });
        });
    });
</script>

<?php include '../_foot.php'; ?>

</body>


</html>
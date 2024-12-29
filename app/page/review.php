<?php
$_title = 'Review';
$_css = '../css/review.css';
require '../_base.php';
include '../_head.php';

require_login();

$orderID = $_GET['order_id'] ?? null;
if (!$orderID) {
    temp('popup-msg', ['msg' => 'No order selected for review.', 'isSuccess' => false]);
    redirect('profile.php');
}

// Fetch order details
$stmt = $_db->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$orderID, $_user->customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    temp('popup-msg', ['msg' => 'Order not found.', 'isSuccess' => false]);
    redirect('profile.php');
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
        'review' => $review,
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
                        <a class="editbtn" onclick="showModal('<?= $product['product_id'] ?>', true)">
                            <span>Edit&nbsp;&nbsp;</span><i class="ti ti-edit"></i>
                        </a>
                        <a class="deletebtn" onclick="confirmDelete('<?= $product['product_id'] ?>')">
                            <span>Delete&nbsp;&nbsp;</span><i class="ti ti-trash"></i>
                        </a>

                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="orderModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal();"><i class="ti ti-x"></i></span>
        <form id="reviewForm" action="review_handler.php?action=update&order_id=<?= urlencode($orderID) ?>" method="post" enctype="multipart/form-data">
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

                <!-- Drag and Drop Image Zone -->
                <div class="input-file-container" id="drop-zone">
                    <div class="image-preview-container">
                        <img id="image-preview" src="" alt="Drag your image here or click to upload" />
                    </div>
                    <input type="file" name="review_image" id="image-input" class="input-file" accept="image/*" onchange="previewFile()" />
                    <div class="drag-overlay" id="drag-overlay">
                    </div>
                </div>

                <!-- Checkbox to remove image -->
                <div class="remove-image-container d-flex align-items-center">
                    <input type="checkbox" name="remove_image" id="remove-image" value="1">
                    <label for="remove-image">Remove existing image</label>
                </div>

                <button class="submitbtn" type="submit">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewFile() {
        const fileInput = document.getElementById('image-input');
        const preview = document.getElementById('image-preview');
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                preview.src = reader.result;
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = ""; // Reset preview if no file is selected
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('image-input');

        // Prevent default behavior for drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when a file is dragged over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files && files.length > 0) {
                fileInput.files = files; // Assign files to the input
                previewFile(); // Preview the file
            }
        }

        // Allow clicking the drop zone to open file dialog
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });
    });


    function showModal(orderItemId, isEdit = false) {
        document.getElementById('orderItemIdInput').value = orderItemId;

        const form = document.getElementById('reviewForm');
        if (isEdit) {
            // Set form action to update
            form.action = `review_handler.php?action=update&order_id=<?= urlencode($orderID) ?>`;
            const reviewData = <?= json_encode($products) ?>.find(
                p => p.product.product_id === orderItemId
            );
            if (reviewData && reviewData.reviewed) {
                document.getElementById('comment').value = reviewData.review.comment;
                document.getElementById('ratingInput').value = reviewData.review.rating;
                document.querySelectorAll('.rating-stars .star').forEach((star, index) => {
                    star.classList.toggle('ti-star-filled', index < reviewData.review.rating);
                });
                if (reviewData.review.review_image) {
                    document.getElementById('image-preview').src =
                        '../uploads/review_images/' + reviewData.review.review_image;
                }
            }
        } else {
            // Set form action to create
            form.action = `review_handler.php?action=create&order_id=<?= urlencode($orderID) ?>`;
            resetModal(); // Clear the form for new review
        }

        document.getElementById('orderModal').style.display = 'block';
    }

    function resetModal() {
        document.getElementById('comment').value = '';
        document.getElementById('ratingInput').value = '';
        document.getElementById('image-preview').src = '';
        document.querySelectorAll('.rating-stars .star').forEach(star => {
            star.classList.remove('ti-star-filled');
        });
    }

    function confirmDelete(productId) {
        if (confirm('Are you sure you want to delete this review?')) {
            window.location.href = `review_handler.php?action=delete&order_id=<?= urlencode($orderID) ?>&product_id=${productId}`;
        }
    }


    // Close the modal
    function closeModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    function setRating(rating) {
        document.querySelectorAll('.rating-stars .star').forEach(function(star, index) {
            star.style.transform = 'scale(1)'; // Reset scale for all stars
            if (index < rating) {
                star.classList.add('ti-star-filled');
                star.style.transform = 'scale(1.1)'; // Scale up the filled stars
                setTimeout(function() {
                    star.style.transform = 'scale(1)'; // Scale back after 0.3s
                }, 300); // Animation duration 300 ms
            } else {
                star.classList.remove('ti-star-filled');
            }
        });
        document.getElementById('ratingInput').value = rating;
    }
</script>

<?php include '../_foot.php'; ?>
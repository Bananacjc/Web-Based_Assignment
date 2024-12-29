<?php
require '../_base.php';

$action = $_GET['action'] ?? null;

if (!$action) {
    temp('popup-msg', ['msg' => 'Invalid action.', 'isSuccess' => false]);
    redirect('profile.php');
}

if ($action === 'create') {
    $orderID = $_GET['order_id'] ?? null;
    $productID = trim(post('orderItemId'));
    $rating = trim(post('rating'));
    $comment = trim(post('comment')) ?: null; // Make comment optional
    $customerID = $_user->customer_id;
    $reviewImage = get_file('review_image');
    $reviewImagePath = null;

    // Validate inputs
    if (!$productID || !$rating) { // Only rating is required
        temp('popup-msg', ['msg' => 'Rating is required.', 'isSuccess' => false]);
        redirect("review.php?order_id=" . urlencode($orderID));
    }

    // Handle image upload (optional)
    if ($reviewImage) {
        $reviewImagePath = save_photo($reviewImage, '../uploads/review_images');
        if (!$reviewImagePath) {
            temp('popup-msg', ['msg' => 'Failed to upload review image.', 'isSuccess' => false]);
            redirect("review.php?order_id=" . urlencode($orderID));
        }
    }

    // Check if the review already exists
    $stmt = $_db->prepare("SELECT * FROM reviews WHERE customer_id = ? AND product_id = ?");
    $stmt->execute([$customerID, $productID]);
    if ($stmt->fetch()) {
        temp('popup-msg', ['msg' => 'You have already reviewed this product.', 'isSuccess' => false]);
        redirect("review.php?order_id=" . urlencode($orderID));
    }

    // Insert the review
    $stmt = $_db->prepare("INSERT INTO reviews (review_id, customer_id, product_id, rating, comment, review_image, comment_date_time)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        generate_unique_id('REV', 'reviews', 'review_id', $_db),
        $customerID,
        $productID,
        $rating,
        $comment,
        $reviewImagePath,
        date('Y-m-d H:i:s'),
    ]);

    temp('popup-msg', ['msg' => $success ? 'Review submitted successfully.' : 'Failed to submit review.', 'isSuccess' => $success]);
    redirect("review.php?order_id=" . urlencode($orderID));
}

if ($action === 'update') {
    $orderID = $_GET['order_id'] ?? null;
    $productID = trim(post('orderItemId'));
    $rating = trim(post('rating'));
    $comment = trim(post('comment')) ?: null; // Make comment optional
    $removeImage = post('remove_image') === '1'; // Check if the user wants to remove the image
    $reviewImage = get_file('review_image');
    $reviewImagePath = null;

    // Validate inputs
    if (!$productID || !$rating) { // Only rating is required
        temp('popup-msg', ['msg' => 'Rating is required.', 'isSuccess' => false]);
        redirect("review.php?order_id=" . urlencode($orderID));
    }

    // Fetch the existing review
    $stmt = $_db->prepare("SELECT review_image FROM reviews WHERE customer_id = ? AND product_id = ?");
    $stmt->execute([$_user->customer_id, $productID]);
    $existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle image upload (optional)
    if ($reviewImage) {
        $reviewImagePath = save_photo($reviewImage, '../uploads/review_images');
        if (!$reviewImagePath) {
            temp('popup-msg', ['msg' => 'Failed to upload review image.', 'isSuccess' => false]);
            redirect("review.php?order_id=" . urlencode($orderID));
        }

        // Delete old image if a new one is uploaded
        if ($existingReview['review_image']) {
            $oldImagePath = "../uploads/review_images/" . $existingReview['review_image'];
            if (file_exists($oldImagePath)) unlink($oldImagePath);
        }
    } elseif ($removeImage) {
        // If the user wants to remove the image
        if ($existingReview['review_image']) {
            $oldImagePath = "../uploads/review_images/" . $existingReview['review_image'];
            if (file_exists($oldImagePath)) unlink($oldImagePath);
        }
        $reviewImagePath = null; // Set image to null
    }

    // Update review
    $stmt = $_db->prepare("UPDATE reviews SET rating = ?, comment = ?, review_image = ?, comment_date_time = ? 
                           WHERE customer_id = ? AND product_id = ?");
    $success = $stmt->execute([
        $rating,
        $comment,
        $reviewImagePath,
        date('Y-m-d H:i:s'),
        $_user->customer_id,
        $productID,
    ]);

    temp('popup-msg', ['msg' => $success ? 'Review updated successfully.' : 'Failed to update review.', 'isSuccess' => $success]);
    redirect("review.php?order_id=" . urlencode($orderID));
}

if ($action === 'delete') {
    $productID = $_GET['product_id'];
    $orderID = $_GET['order_id'];

    // Fetch the existing review
    $stmt = $_db->prepare("SELECT review_image FROM reviews WHERE customer_id = ? AND product_id = ?");
    $stmt->execute([$_user->customer_id, $productID]);
    $existingReview = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete the review
    $stmt = $_db->prepare("DELETE FROM reviews WHERE customer_id = ? AND product_id = ?");
    $success = $stmt->execute([$_user->customer_id, $productID]);

    // Delete the associated image if it exists
    if ($success && $existingReview['review_image']) {
        $oldImagePath = "../uploads/review_images/" . $existingReview['review_image'];
        if (file_exists($oldImagePath)) unlink($oldImagePath);
    }

    temp('popup-msg', ['msg' => $success ? 'Review deleted successfully.' : 'Failed to delete review.', 'isSuccess' => $success]);
    redirect("review.php?order_id=" . urlencode($orderID));
}

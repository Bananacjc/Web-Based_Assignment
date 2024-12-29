<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    $review_id = req('review_id');
    $customer_id = req('customer_id');
    $product_id = req('product_id');
    $rating = req('rating');
    $comment = req('comment');
    $review_image = get_file('review_image'); 

    $_err = [];

    if (empty($review_id)) {
        $_err['review_id'] = "Review ID is required.";
    } else {
        $stmt = $_db->prepare("SELECT COUNT(*) FROM reviews WHERE review_id = ?");
        $stmt->execute([$review_id]);
        if ($stmt->fetchColumn() == 0) {
            $_err['review_id'] = "Review ID does not exist.";
        }
    }

    if (empty($rating)) {
        $_err['rating'] = "Rating is required.";
    } elseif (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $_err['rating'] = "Rating must be a number between 1 and 5.";
    }

    if (empty($comment)) {
        $_err['comment'] = "Comment is required.";
    }

    if ($review_image) {
        if (!str_starts_with($review_image->type, 'image/')) {
            $_err['review_image'] = "Review image must be a valid image file.";
        } elseif ($review_image->size > 2 * 1024 * 1024) { // Limit to 2MB
            $_err['review_image'] = "Review image exceeds the size limit (2MB).";
        }
    }

    if (!$_err) {
        // Handle review image upload
        if ($review_image) {
            // Save the new image
            $review_image_path = save_photo($review_image, '../../uploads/review_images');
        } else {
            // Retain existing image if none is uploaded
            $stmt = $_db->prepare("SELECT review_image FROM reviews WHERE review_id = ?");
            $stmt->execute([$review_id]);
            $review_image_path = $stmt->fetchColumn();
        }

        $current_time = date('Y-m-d H:i:s');

        $stmt = $_db->prepare("
            UPDATE reviews SET
                rating = ?, 
                comment = ?, 
                review_image = ?, 
                comment_date_time = ? 
            WHERE review_id = ?
        ");
        $stmt->execute([
            $rating,
            $comment,
            $review_image_path,
            $current_time,
            $review_id
        ]);

        // Log action if an employee is logged in
        if ($_user && isset($_user->employee_id)) {
            $employee_id = $_user->employee_id;
            log_action($employee_id, 'Updated Review', "Updated Review: $review_id", $_db);
        }

        temp('info', 'Review updated successfully!');
        redirect('review.php');
    } else {
        temp('error', $_err);
        redirect('review.php');
    }
}
?>

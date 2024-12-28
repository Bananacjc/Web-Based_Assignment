<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {
            $stm = $_db->prepare('SELECT review_image FROM reviews WHERE review_id = ?');
            $stm->execute([$v]);
            $review = $stm->fetch(PDO::FETCH_OBJ);

            if ($review && $review->review_image && file_exists("../../uploads/review_images/{$review->preview_image}")) {
                unlink("../../uploads/review_images/{$review->review_image}");
            }

            $stm = $_db->prepare('DELETE FROM reviews WHERE review_id = ?');
            $stm->execute([$v]);
        }

        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            $deletedReviewIds = implode(', ', $id);

            log_action($employeeId, 'Delete Review', "Delete Review: {$deletedReviewIds }", $_db);
        }


        temp('info', count($id) . " review(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting review(s): ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method. Only POST requests are allowed.');
}
redirect('review.php');



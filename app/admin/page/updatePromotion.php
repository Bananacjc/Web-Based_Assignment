<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    $promo_id = req('promo_id');
    $promo_name = req('promo_name');
    $promo_code = req('promo_code');
    $description = req('description');
    $requirement = req('requirement');
    $promo_amount = req('promo_amount');
    $limit_usage = req('limit_usage');
    $start_date = req('start_date');
    $end_date = req('end_date');
    $status = req('status');
    $promo_image = get_file('promo_image');
    
    $_err = [];

    if (empty($promo_name)) {
        $_err['promo_name'] = "Promo Name is required for Promo ID: $promo_id.";
    } elseif (strlen($promo_name) > 50) {
        $_err['promo_name'] = "Maximum 50 characters for Promo Name in Promo ID: $promo_id.";
    }

    if (empty($promo_code)) {
        $_err['promo_code'] = "Promo Code is required for Promo ID: $promo_id.";
    }

    if (empty($description)) {
        $_err['description'] = "Description is required for Promo ID: $promo_id.";
    }

    if (!is_numeric($requirement) || $requirement < 0) {
        $_err['requirement'] = "Requirement must be a non-negative number for Promo ID: $promo_id.";
    }

    if (!is_numeric($promo_amount) || $promo_amount < 0) {
        $_err['promo_amount'] = "Promo Amount must be a non-negative number for Promo ID: $promo_id.";
    }

    if (!is_numeric($limit_usage) || $limit_usage < 1) {
        $_err['limit_usage'] = "Usage Limit must be greater than or equal to 1 for Promo ID: $promo_id.";
    }

    if (empty($start_date) || empty($end_date)) {
        $_err['start_date'] = "Start Date and End Date are required for Promo ID: $promo_id.";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $_err['end_date'] = "End Date must be later than Start Date for Promo ID: $promo_id.";
    }

    if ($promo_image) {
        if (!str_starts_with($promo_image->type, 'image/')) {
            $_err['promo_image'] = 'Promo Image must be an image.';
        } elseif ($promo_image->size > 1 * 1024 * 1024) {
            $_err['promo_image'] = 'Promo Image size exceeds the limit (1MB).';
        }
    }

    if (!$_err) {
        $promo_image_path = null;
        if ($promo_image) {
            $promo_image_path = save_photo($promo_image, '../../uploads/promo_images');
        }

        if (!$promo_image_path) {
            $stmt = $_db->prepare("SELECT promo_image FROM promotions WHERE promo_id = ?");
            $stmt->execute([$promo_id]);
            $existing_promo_image = $stmt->fetchColumn();
            $promo_image_path = $existing_promo_image;
        }

        $sql = "UPDATE promotions SET 
                    promo_name = ?, 
                    promo_code = ?, 
                    description = ?, 
                    requirement = ?, 
                    promo_amount = ?, 
                    limit_usage = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    promo_image = ?, 
                    status = ? 
                WHERE promo_id = ?";

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            $promo_name, 
            $promo_code, 
            $description, 
            $requirement, 
            $promo_amount, 
            $limit_usage, 
            $start_date, 
            $end_date, 
            $promo_image_path, 
            $status, 
            $promo_id
        ]);

        // Log the action if user is authenticated
        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            log_action($employeeId, 'Updated Promotion', "Updated Promotion: $promo_id", $_db);
        }

        temp('info', 'Promotion updated successfully!');
        redirect('promotionVoucher.php');
    } else {
        temp('error', $_err);
        redirect('promotionVoucher.php');
    }
}
?>

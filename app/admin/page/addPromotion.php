<?php
require '../_base.php'; // Include base functions and database connection

if (is_post()) {
    global $_err;

    $promoName = post('promo_name');
    $promoCode = post('promo_code');
    $description = post('description');
    $requirement = post('requirement');
    $promoAmount = post('promo_amount');
    $limitUsage = post('limit_usage');
    $startDate = post('start_date');
    $endDate = post('end_date');
    $status = post('status');
    $promoImage = get_file('promo_image'); 

    if (empty($promoName)) {
        $_err['promo_name'] = 'Promo name is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $promoName)) {
        $_err['promo_name'] = 'Promo name can only contain letters, numbers, space.';
    }

    if (empty($promoCode)) {
        $_err['promo_code'] = 'Promo code is required.';
    } elseif (!preg_match('/^[A-Z0-9]+$/', $promoCode)) {
        $_err['promo_code'] = 'Promo code must be uppercase letters and numbers only.';
    }

    if (empty($description)) {
        $_err['description'] = 'Description is required.';
    } elseif (strlen($description) > 255) {
        $_err['description'] = 'Description cannot be more than 255 characters.';
    }

    if (!is_numeric($requirement) || $requirement < 0) {
        $_err['requirement'] = 'Requirement must be a valid non-negative number.';
    }

    if (!is_numeric($promoAmount) || $promoAmount <= 0) {
        $_err['promo_amount'] = 'Promo amount must be a valid positive number.';
    }

    if (!is_numeric($limitUsage) || $limitUsage <= 0) {
        $_err['limit_usage'] = 'Usage limit must be a valid positive number.';
    }

    if (empty($startDate) || empty($endDate)) {
        $_err['dates'] = 'Start date and end date are required.';
    } elseif ($startDate > $endDate) {
        $_err['dates'] = 'Start date cannot be later than end date.';
    }

    if (empty($status)) {
        $_err['status'] = 'Status is required.';
    }

    if ($promoImage) {
        if (!str_starts_with($promoImage->type, 'image/')) {
            $_err['promo_image'] = 'Invalid image file. Please upload an image.';
        }
    }

    if (empty($_err)) {
        $promoImagePath = null;
        if ($promoImage) {
            $promoImagePath = save_photo($promoImage, '../../uploads/promo_images');
        }

        $promoId = generate_unique_id('PROMO', 'promotions', 'promo_id', $_db);

        try {
            $stmt = $_db->prepare("
                INSERT INTO promotions (promo_id, promo_name, promo_code, description, requirement, promo_amount, limit_usage, start_date, end_date, status, promo_image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $promoId,
                $promoName,
                $promoCode,
                $description,
                $requirement,
                $promoAmount,
                $limitUsage,
                $startDate,
                $endDate,
                $status,
                $promoImagePath
            ]);

            if ($_user && isset($_user->employee_id)) {
                $employeeId = $_user->employee_id;
                log_action($employeeId, 'Add Promotion', 'Added new promotion: ' . $promoName, $_db);
            }
            temp('info', "Promotion added successfully!");
            redirect('promotionVoucher.php');
        } catch (PDOException $e) {
            $_err['error'] = 'Error adding promotion: ' . $e->getMessage();
        }
    } else {
        temp('error', $_err);
        redirect('promotionVoucher.php');
    }
}
?>

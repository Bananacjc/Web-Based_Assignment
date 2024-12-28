<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    // Get form values
    $customer_id = req('customer_id');
    $username = req('username');
    $email = req('email');
    $contact_num = req('contact_num');
    $banks = req('banks');
    $addresses = req('addresses');
    $cart = req('cart');
    $promotion_records = req('promotion_records');
    $profile_image = get_file('profile_image'); // Profile image is optional

    $_err = [];

    // Validate username
    if (empty($username)) {
        $_err['username'] = "Username is required for Customer ID: $customer_id.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $username)) {
        $_err['username'] = "Username can only contain letters, numbers, spaces, and hyphens for Customer ID: $customer_id.";
    }

    // Validate email
    if (empty($email)) {
        $_err['email'] = "Email is required for Customer ID: $customer_id.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid email format for Customer ID: $customer_id.";
    } else {
        $stmt = $_db->prepare("SELECT customer_id FROM customers WHERE email = ? AND customer_id != ?");
        $stmt->execute([$email, $customer_id]);
        if ($stmt->rowCount() > 0) {
            $_err['email'] = "Email is already in use by another customer.";
        }
    }

    // Validate contact number
    if (empty($contact_num)) {
        $_err['contact_num'] = "Contact number is required for Customer ID: $customer_id.";
    } elseif (!ctype_digit($contact_num)) {
        $_err['contact_num'] = "Contact number must be numeric for Customer ID: $customer_id.";
    }

    // Validate profile image if uploaded
    if ($profile_image) {
        if (!str_starts_with($profile_image->type, 'image/')) {
            $_err['profile_image'] = "Profile image must be a valid image file for Customer ID: $customer_id.";
        } elseif ($profile_image->size > 2 * 1024 * 1024) { // Limit to 2MB
            $_err['profile_image'] = "Profile image exceeds the size limit (2MB) for Customer ID: $customer_id.";
        }
    }

    if (!$_err) {
        // Handle profile image
        if ($profile_image) {
            // If a new image is uploaded, save it
            $profile_image_path = save_photo($profile_image, '../../uploads/profile_images');
        } else {
            // If no image is uploaded, keep the existing profile image
            $stmt = $_db->prepare("SELECT profile_image FROM customers WHERE customer_id = ?");
            $stmt->execute([$customer_id]);
            $profile_image_path = $stmt->fetchColumn(); // Use the existing image path
        }

        // Encode fields to JSON format
        $banks = json_encode($banks);
        $addresses = json_encode($addresses);
        $cart = json_encode($cart);
        $promotion_records = json_encode($promotion_records);

        // Update customer in the database
        $stmt = $_db->prepare("
            UPDATE customers SET
                username = ?, 
                email = ?, 
                contact_num = ?, 
                banks = ?, 
                addresses = ?, 
                cart = ?, 
                promotion_records = ?, 
                profile_image = ? 
            WHERE customer_id = ?
        ");

        $stmt->execute([
            $username,
            $email,
            $contact_num,
            $banks, 
            $addresses, 
            $cart, 
            $promotion_records, 
            $profile_image_path,
            $customer_id
        ]);

        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            log_action($employeeId, 'Updated Customer', "Updated Customer: $customer_id", $_db);
        }
        temp('info', 'Customer updated successfully!');
        redirect('customer.php');
    } else {
        temp('error', $_err);
        redirect('customer.php');
    }
}
?>

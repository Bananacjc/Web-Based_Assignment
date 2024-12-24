<?php
require '../_base.php'; // Include base functions and database connection

// Handle POST request
if (is_post()) {
    $_err = []; // Store validation errors

    // Sanitize and validate inputs
    $username = req('username');
    $email = req('email');
    $contactNum = req('contact_num');
    $banks = req('banks');
    $ewallets = req('ewallets');
    $addresses = req('addresses');
    $cart = req('cart');
    $promotionRecords = req('promotion_records');

    // Validate username
    if ($username == '') {
        $_err['username'] = 'Username is required.';
    }

    // Validate email
    if ($email == '') {
        $_err['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email address.';
    } else {
        $stmt = $_db->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_err['email'] = 'Email is already in use.';
        }
    }

    // Validate contact number
    if ($contactNum == '') {
        $_err['contact_num'] = 'Contact number is required.';
    } elseif (!ctype_digit($contactNum)) {
        $_err['contact_num'] = 'Contact number must be numeric.';
    }

    // Validate optional fields
    if ($cart && json_decode($cart, true) === null) {
        $_err['cart'] = 'Invalid JSON format for cart.';
    }
    if ($promotionRecords && json_decode($promotionRecords, true) === null) {
        $_err['promotion_records'] = 'Invalid JSON format for promotion records.';
    }

    // Handle profile image upload
    $profileImagePath = null;
    $profileImage = get_file('profile_image');
    if ($profileImage) {
        if ($profileImage['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileMimeType = mime_content_type($profileImage['tmp_name']);

            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                $_err['profile_image'] = 'Invalid image type. Only JPG, PNG, and GIF are allowed.';
            } elseif ($profileImage['size'] > 2 * 1024 * 1024) {
                $_err['profile_image'] = 'Profile image exceeds 2MB limit.';
            } else {
                $profileImagePath = save_photo($profileImage, '../uploads/profile_images');
            }
        } else {
            $_err['profile_image'] = 'Error uploading profile image.';
        }
    }

    // If no errors, insert customer data
    if (!$_err) {
        try {
            $customerId = generate_unique_id('CUS', 'customers', 'customer_id', $_db);

            $stmt = $_db->prepare("
                INSERT INTO customers (customer_id, username, email, contact_num, banks, ewallets, addresses, cart, promotion_records, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $customerId,
                $username,
                $email,
                $contactNum,
                $banks ?: null,
                $ewallets ?: null,
                $addresses ?: null,
                $cart ?: null,
                $promotionRecords ?: null,
                $profileImagePath
            ]);

            temp('success', "Customer added successfully!");
            redirect('customer.php');
        } catch (PDOException $e) {
            temp('error', "Error adding customer: " . $e->getMessage());
            redirect();
        }
    }
}

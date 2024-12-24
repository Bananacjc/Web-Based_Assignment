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

    // Validate and decode JSON fields
    if ($cart) {
        $decodedCart = json_decode($cart, true);  // Decode as an associative array
        if (json_last_error() !== JSON_ERROR_NONE) {
            $_err['cart'] = 'Invalid JSON format for cart.';
        } else {
            $cart = $decodedCart;  // Only assign if valid
        }
    }

    if ($promotionRecords) {
        $decodedPromotionRecords = json_decode($promotionRecords, true);  // Decode as an associative array
        if (json_last_error() !== JSON_ERROR_NONE) {
            $_err['promotion_records'] = 'Invalid JSON format for promotion records.';
        } else {
            $promotionRecords = $decodedPromotionRecords;  // Only assign if valid
        }
    }

    // Handle profile image upload
    $profileImagePath = null;
    $profileImage = get_file('profile_image');
    if (!$profileImage) {
        $_err['profile_image'] = 'Product image is required.';
    } elseif (!str_starts_with($profileImage->type, 'image/')) {
        $_err['profile_image'] = 'Invalid image file. Please upload an image.';
    }
    if(empty($_err)){
        $profileImagePath= save_photo($profileImage, '../uploads/profile_images');

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
                $banks ?: null, // handle case when banks is empty or null
                $ewallets ?: null, // handle case when ewallets is empty or null
                $addresses ?: null, // handle case when addresses is empty or null
                $cart ? json_encode($cart) : null, // Save as JSON if it is not empty
                $promotionRecords ? json_encode($promotionRecords) : null, // Save as JSON if it is not empty
                $profileImagePath
            ]);

            temp('success', "Customer added successfully!");
            redirect('customer.php');
        } 
    }
        
    


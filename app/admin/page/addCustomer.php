<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    // Get form values
    $username = req('username');
    $email = req('email');
    $contact_num = req('contact_num');
    $banks = req('banks');
    $ewallets = req('ewallets');
    $addresses = req('addresses');
    $cart = req('cart');
    $promotion_records = req('promotion_records');
    $profile_image = get_file('profile_image'); // Profile image is optional

    $_err = [];

    if (empty($username)) {
        $_err['username'] = "Username is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $username)) {
        $_err['username'] = "Username can only contain letters, numbers, spaces, and hyphens.";
    }

    if (empty($email)) {
        $_err['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid email format.";
    } else {
        $stmt = $_db->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_err['email'] = "Email is already in use.";
        }
    }

    if (empty($contact_num)) {
        $_err['contact_num'] = "Contact number is required.";
    } elseif (!ctype_digit($contact_num)) {
        $_err['contact_num'] = "Contact number must be numeric.";
    }

    if ($profile_image) {
        if (!str_starts_with($profile_image->type, 'image/')) {
            $_err['profile_image'] = "Profile image must be a valid image file.";
        } elseif ($profile_image->size > 2 * 1024 * 1024) { // Limit to 2MB
            $_err['profile_image'] = "Profile image exceeds the size limit (2MB).";
        }
    }

        if ($profile_image) {
            $profile_image_path = save_photo($profile_image, '../uploads/profile_images');
        } else {
            $profile_image_path = null;
        }
        
    if (!$_err) {

        $banks = json_encode($banks);
        $ewallets = json_encode($ewallets);
        $addresses = json_encode($addresses);
        $cart = json_encode($cart);
        $promotion_records = json_encode($promotion_records);

        // Generate a unique customer ID
        $customer_id = generate_unique_id('CUS', 'customers', 'customer_id', $_db);

        // Insert new customer into the database
        try {
            $stmt = $_db->prepare("
                INSERT INTO customers (customer_id, username, email, contact_num, banks, ewallets, addresses, cart, promotion_records, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $customer_id,
                $username,
                $email,
                $contact_num,
                $banks, // Store as JSON-encoded string
                $ewallets, // Store as JSON-encoded string
                $addresses, // Store as JSON-encoded string
                $cart, // Store as JSON-encoded string
                $promotion_records, // Store as JSON-encoded string
                $profile_image_path
            ]);

            // Success message and redirect
            temp('info', "Customer added successfully!");
            redirect('customer.php');
        } catch (PDOException $e) {
            $_err['error'] = 'Error adding customer: ' . $e->getMessage();
        }
    } else {
        // If validation failed, send errors back to the form
        temp('error', $_err);
        redirect('customer.php');
    }
}
?>

<?php
require '../_base.php';

if (is_post()) {
    global $_err;   
    
    $product_id = req('product_id');
    $product_name = req('product_name');
    $existing_category = req('category_name'); 
    $new_category_name = req('new_category_name');
    $new_category_image = get_file('new_category_image');
    $price = req('price');
    $description = req('description');
    $current_stock = req('current_stock');
    $amount_sold = req('amount_sold');
    $product_image = get_file('product_image');
    $status = req('status');

    if (empty($product_name)) {
        $_err['product_name'] = 'Product Name is required.';
    } elseif (strlen($product_name) > 100) {
        $_err['product_name'] = 'Maximum 100 characters for Product Name.';
    }

    if (empty($price)) {
        $_err['price'] = 'Price is required.';
    } elseif (!is_money($price)) {
        $_err['price'] = 'Price must be in a valid format (e.g., 10.00, 99.99).';
    } elseif ($price < 0.01 || $price > 99.99) {
        $_err['price'] = 'Price must be between 0.01 and 99.99.';
    }

    if (empty($description)) {
        $_err['description'] = 'Description is required.';
    }

    if (empty($current_stock)) {
        $_err['current_stock'] = 'Current Stock is required.';
    } elseif ($current_stock < 0) {
        $_err['current_stock'] = 'Current Stock must be a positive number.';
    }

    if (empty($amount_sold)) {
        $_err['amount_sold'] = 'Amount Sold is required.';
    } elseif ($amount_sold < 0) {
        $_err['amount_sold'] = 'Amount Sold must be a positive number.';
    }

    if ($new_category_name) {
        if (empty($new_category_name)) {
            $_err['new_category_name'] = 'Category Name is required.';
        }

        if ($new_category_image) {
            if (!str_starts_with($new_category_image->type, 'image/')) {
                $_err['new_category_image'] = 'New Category Image must be an image.';
            } elseif ($new_category_image->size > 3 * 1024 * 1024) { // 3MB limit
                $_err['new_category_image'] = 'New Category Image size exceeds the limit (3MB).';
            }
        }
    }

    if ($product_image) {
        if (!str_starts_with($product_image->type, 'image/')) {
            $_err['product_image'] = 'Product Image must be an image.';
        } elseif ($product_image->size > 1 * 1024 * 1024) { // 1MB limit
            $_err['product_image'] = 'Product Image size exceeds the limit (1MB).';
        }
    }

    $category_name = $new_category_name ?: $existing_category; 

    if (!$_err) {
        // Handle new category image if provided
        if ($new_category_image) {
            $new_category_image_path = save_photo($new_category_image, '../uploads/category_images');
        }

        // Handle product image if provided
        if ($product_image) {
            // Delete the old product image (if required)
            if (file_exists("../uploads/product_images/{$product_image}")) {
                unlink("../uploads/product_images/{$product_image}"); 
            }
            $product_image_path = save_photo($product_image, '../uploads/product_images');
        }

        // Insert new category if it does not exist
        if ($new_category_name) {
            $stmt = $_db->prepare("SELECT category_name FROM categories WHERE category_name = ?");
            $stmt->execute([$new_category_name]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                $stmt = $_db->prepare('INSERT INTO categories (category_name, category_image) VALUES (?, ?)');
                $stmt->execute([$new_category_name, $new_category_image_path ?? null]);
            }
        }

        // Prepare SQL to update product details
        $sql = "UPDATE products SET 
                    product_name = ?, 
                    category_name = ?, 
                    price = ?, 
                    description = ?, 
                    current_stock = ?, 
                    amount_sold = ?, 
                    product_image = ?, 
                    status = ? 
                WHERE product_id = ?";

        // Execute SQL query
        $stmt = $_db->prepare($sql);
        $stmt->execute([
            $product_name, 
            $category_name, 
            $price, 
            $description, 
            $current_stock, 
            $amount_sold, 
            $product_image_path ?? $product_image, 
            $status, 
            $product_id
        ]);

        temp('info', 'Product updated successfully!');
        redirect('product.php');
    } 
}
?>

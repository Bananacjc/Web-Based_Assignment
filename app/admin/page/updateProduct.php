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

    $_err = [];

    if (empty($product_name)) {
        $_err['product_name'] = "Product Name is required for Product ID: $product_id.";
    } elseif (strlen($product_name) > 20) {
        $_err['product_name'] = "Maximum 20 characters for Product Name in Product ID: $product_id.";
    }

    if (empty($price)) {
        $_err['price'] = "Price is required for Product ID: $product_id.";
    } elseif (!is_money($price)) {
        $_err['price'] = "Price must be in a valid format (e.g., 10.00, 1000) for Product ID: $product_id.";
    } elseif ($price < 0.01 || $price > 1000) {
        $_err['price'] = "Price must be between 0.01 and 1000 for Product ID: $product_id.";
    }

    if (empty($description)) {
        $_err['description'] = "Description is required for Product ID: $product_id.";
    }

    if (!isset($current_stock) || $current_stock < 0) {
        $_err['current_stock'] = "Current Stock must be a non-negative number for Product ID: $product_id.";
    }

    if (!isset($amount_sold) || $amount_sold < 0) {
        $_err['amount_sold'] = "Amount Sold must be a non-negative number for Product ID: $product_id.";
    }
    

    if (!empty($existing_category) && !empty($new_category_name)) {
        $_err['category_name'] = "Please choose either an existing category or provide a new one, not both for Product ID: $product_id.";
    }

    
    if (!empty($existing_category) && !empty($new_category_image)) {
        $_err['category_name'] = 'You cannot upload a new category image when selecting an existing category.';
    }

    if ($new_category_name && $new_category_image) {
        if (!str_starts_with($new_category_image->type, 'image/')) {
            $_err['new_category_image'] = 'New Category Image must be an image.';
        } elseif ($new_category_image->size > 3 * 1024 * 1024) {
            $_err['new_category_image'] = 'New Category Image size exceeds the limit (3MB).';
        }
    }

    if ($product_image) {
        if (!str_starts_with($product_image->type, 'image/')) {
            $_err['product_image'] = 'Product Image must be an image.';
        } elseif ($product_image->size > 1 * 1024 * 1024) {
            $_err['product_image'] = 'Product Image size exceeds the limit (1MB).';
        }
    }

    $category_name = $new_category_name ?: $existing_category;

    if ($current_stock == 0) {
        $status = 'Out of Stock';
    }

    if (!$_err) {
        $new_category_image_path = null;
        if ($new_category_image) {
            $new_category_image_path = save_photo($new_category_image, '../uploads/category_images');
        }

        $product_image_path = $product_image ? save_photo($product_image, '../uploads/product_images') : null;

        if (!$product_image_path) {
            $stmt = $_db->prepare("SELECT product_image FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $existing_product_image = $stmt->fetchColumn();
            $product_image_path = $existing_product_image;
        }

        if ($new_category_name) {
            $stmt = $_db->prepare("SELECT category_name FROM categories WHERE category_name = ?");
            $stmt->execute([$new_category_name]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                $new_category_image_path = $new_category_image_path ?? 'default-placeholder.jpg'; 

                $stmt = $_db->prepare('INSERT INTO categories (category_name, category_image) VALUES (?, ?)');
                $stmt->execute([$new_category_name, $new_category_image_path]);
            }
        }

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

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            $product_name, 
            $category_name, 
            $price, 
            $description, 
            $current_stock, 
            $amount_sold, 
            $product_image_path, 
            $status, 
            $product_id
        ]);

        temp('info', 'Product updated successfully!');
        redirect('product.php');
    } else {
        temp('error', $_err);
        redirect('product.php');
    }
}
?>

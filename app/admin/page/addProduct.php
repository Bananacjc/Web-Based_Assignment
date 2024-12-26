<?php
require '../_base.php'; // Include base functions and database connection

// Handle POST request for adding or updating products
if (is_post()) {
    global $_err;

    $productName = post('product_name');
    $price = post('price');
    $description = post('description');
    $currentStock = post('current_stock');
    $status = post('status');
    $categoryName = post('category_name');
    $newCategoryName = post('new_category_name');
    $categoryImage = get_file('new_category_image'); // For new category image
    $productId = post('product_id'); // Assuming product ID is sent for updates

    // Product name validation
    if (empty($productName)) {
        $_err['product_name'] = 'Product name is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $productName)) {
        $_err['product_name'] = 'Product name can only contain letters, numbers, space.';
    }

    // Price validation
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $_err['price'] = 'Price must be a valid positive number.';
    }

    // Description validation
    if (empty($description)) {
        $_err['description'] = 'Description is required.';
    } elseif (strlen($description) > 255) {
        $_err['description'] = 'Description cannot be more than 255 characters.';
    }

    // Stock validation
    if (!is_numeric($currentStock) || $currentStock < 0) {
        $_err['current_stock'] = 'Stock must be a valid non-negative number.';
    }

    // Category validation: Only one of 'category_name' or 'new_category_name' with 'new_category_image' should be set
    if (!empty($categoryName) && !empty($newCategoryName)) {
        $_err['category_name'] = 'You cannot submit both an existing category and a new category at the same time.';
    }

    if (!empty($categoryName) && !empty($categoryImage)) {
        $_err['category_name'] = 'You cannot upload a new category image when selecting an existing category.';
    }

    if (empty($categoryName) && empty($newCategoryName)) {
        $_err['category_name'] = 'You must select an existing category or provide a new category.';
    }

    // Handle existing category
    if (!empty($categoryName)) {
        $stmt = $_db->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
        $stmt->execute([$categoryName]);
        if ($stmt->fetchColumn() == 0) {
            $_err['category_name'] = 'Selected category does not exist.';
        }
    }

    // Handle new category
    if (!empty($newCategoryName)) {
        if (empty($categoryImage)) {
            $_err['new_category_image'] = 'New category image is required.';
        } elseif (!str_starts_with($categoryImage->type, 'image/')) {
            $_err['new_category_image'] = 'Invalid category image file. Please upload an image.';
        }

        // Insert the new category into the database if no errors
        if (empty($_err)) {
            $categoryImagePath = save_photo($categoryImage, '../uploads/category_images');
            try {
                $stmt = $_db->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
                $stmt->execute([$newCategoryName, $categoryImagePath]);
                $categoryName = $newCategoryName; // Use newly created category name
            } catch (PDOException $e) {
                $_err['new_category_name'] = 'Error adding new category: ' . $e->getMessage();
            }
        }
    }

    // Handle product image
    $productImage = get_file('product_image');
    if (!$productImage) {
        $_err['product_image'] = 'Product image is required.';
    } elseif (!str_starts_with($productImage->type, 'image/')) {
        $_err['product_image'] = 'Invalid image file. Please upload an image.';
    }

    // If no errors, insert or update the product
    if (empty($_err)) {
        $productImagePath = save_photo($productImage, '../uploads/product_images');
        
        // Update product if productId is provided
        if (!empty($productId)) {
            try {
                $stmt = $_db->prepare("
                    UPDATE products SET 
                    product_name = ?, 
                    category_name = ?, 
                    price = ?, 
                    description = ?, 
                    current_stock = ?, 
                    product_image = ?, 
                    status = ? 
                    WHERE product_id = ?
                ");
                $stmt->execute([$productName, $categoryName, $price, $description, $currentStock, $productImagePath, $status, $productId]);
                temp('info', "Product updated successfully!");
                redirect('product.php');
            } catch (PDOException $e) {
                $_err['error'] = 'Error updating product: ' . $e->getMessage();
            }
        } else {
            // New product insertion code
            $productId = generate_unique_id('PRO', 'products', 'product_id', $_db);
            try {
                $stmt = $_db->prepare("
                    INSERT INTO products (product_id, product_name, category_name, price, description, current_stock, amount_sold, product_image, status)
                    VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
                ");
                $stmt->execute([$productId, $productName, $categoryName, $price, $description, $currentStock, $productImagePath, $status]);
                temp('info', "Product added successfully!");
                redirect('product.php');
            } catch (PDOException $e) {
                $_err['error'] = 'Error adding product: ' . $e->getMessage();
            }
        }
    } else {
        temp('error', $_err);
        redirect('product.php');
    }
}
?>

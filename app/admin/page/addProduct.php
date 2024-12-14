<?php
require '../_base.php'; // Include base functions and database connection

// Fetch existing categories
// Handle POST request for adding products
if (is_post()) {
    global $_err;

    // Validate inputs
    $productName = post('product_name');
    $price = post('price');
    $description = post('description');
    $currentStock = post('current_stock');
    $status = post('status');
    $categoryName = post('category_name');

    if (empty($productName)) $_err['product_name'] = 'Product name is required.';
    if (!is_money($price)) $_err['price'] = 'Invalid price format.';
    if (empty($description)) $_err['description'] = 'Description is required.';
    if (!is_numeric($currentStock) || $currentStock < 0) $_err['current_stock'] = 'Invalid stock value.';
    if (empty($categoryName) && !post('new_category_name')) $_err['category_name'] = 'Category is required.';

    // Handle product image
    $productImage = get_file('product_image');
    if (!$productImage) {
        $_err['product_image'] = 'Product image is required.';
    } elseif (!str_starts_with($productImage->type, 'image/')) {
        $_err['product_image'] = 'Invalid image file.';
    }

    

    // Handle new category
    if (post('new_category_name')) {
        $newCategoryName = post('new_category_name');
        $categoryImage = get_file('new_category_image');

        if (!$categoryImage || !str_starts_with($categoryImage->type, 'image/')) {
            $_err['new_category_image'] = 'Invalid or missing category image.';
        } else {
            $categoryImagePath = save_photo($categoryImage, '../uploads/category_images');
        }

        if (empty($_err)) {
            try {
                $stmt = $_db->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
                $stmt->execute([$newCategoryName, $categoryImagePath]);
                $categoryName = $newCategoryName; // Use the newly created category
            } catch (PDOException $e) {
                temp('error', "Error adding category: " . $e->getMessage());
                redirect();
            }
        }
    }

    // Save product if no errors
    if (empty($_err)) {
        $productImagePath = save_photo($productImage, '../uploads/product_images');
        $productId = generate_unique_id('PRO', 'products', 'product_id', $_db);

        try {
            $stmt = $_db->prepare("
                INSERT INTO products (product_id, product_name, category_name, price, description, current_stock, amount_sold, product_image, status)
                VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
            ");
            $stmt->execute([$productId, $productName, $categoryName, $price, $description, $currentStock, $productImagePath, $status]);
            temp('success', "Product added successfully!");
            redirect('product.php');
        } catch (PDOException $e) {
            temp('error', "Error adding product: " . $e->getMessage());
            redirect();
        }
    }
}
?>


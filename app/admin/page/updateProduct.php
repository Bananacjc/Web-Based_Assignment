<?php
require '../_base.php'; // Include base functions and database connection

// Fetch product and category details for editing
if (is_get()) {
    $id = req('product_id');

    $stm = $_db->prepare('
        SELECT p.*, c.category_image 
        FROM products p
        JOIN categories c ON p.category_name = c.category_name
        WHERE product_id = ?
    ');
    $stm->execute([$id]);
    $p = $stm->fetch();

    if (!$p) {
        redirect('product.php');
    }

    extract((array)$p);
    $_SESSION['category_image'] = $p->category_image;
    $_SESSION['product_image'] = $p->product_image;
}

// Handle POST request for updating product and category
if (is_post()) {
    $productId        = req('product_id');
    $productName      = req('product_name');
    $price            = req('price');
    $description      = req('description');
    $currentStock     = req('current_stock');
    $status           = req('status');
    $categoryName     = req('category_name');
    $newCategoryName  = req('new_category_name');
    $productImage     = get_file('product_image');
    $newCategoryImage = get_file('new_category_image');
    $productImagePath = $_SESSION['product_image'];
    $categoryImagePath = $_SESSION['category_image'];

    // Determine the final category name to use
    $finalCategoryName = $newCategoryName ?: $categoryName;

    // Validate: product name
    if ($productName == '') {
        $_err['product_name'] = 'Product name is required.';
    } else if (strlen($productName) > 100) {
        $_err['product_name'] = 'Maximum 100 characters.';
    }

    // Validate: price
    if ($price == '') {
        $_err['price'] = 'Price is required.';
    } else if (!is_money($price)) {
        $_err['price'] = 'Invalid price format.';
    }

    // Validate: description
    if ($description == '') {
        $_err['description'] = 'Description is required.';
    }

    // Validate: current stock
    if (!is_numeric($currentStock) || $currentStock < 0) {
        $_err['current_stock'] = 'Invalid stock value.';
    }

    // Validate: product image (only if a new image is uploaded)
    if ($productImage) {
        if (!str_starts_with($productImage->type, 'image/')) {
            $_err['product_image'] = 'Invalid image file.';
        } else if ($productImage->size > 1 * 1024 * 1024) {
            $_err['product_image'] = 'Maximum file size is 1MB.';
        }
    }

    // Validate: new category image (if uploaded)
    if ($newCategoryImage) {
        if (!str_starts_with($newCategoryImage->type, 'image/')) {
            $_err['new_category_image'] = 'Invalid image file.';
        } else if ($newCategoryImage->size > 1 * 1024 * 1024) {
            $_err['new_category_image'] = 'Maximum file size is 1MB.';
        }
    }

    // Update product and category if no errors
    if (!$_err) {
        if ($productImage) {
            unlink("../uploads/product_images/$productImagePath");
            $productImagePath = save_photo($productImage, '../uploads/product_images');
        }

        if ($newCategoryName) {
            // Insert new category if specified
            $stm = $_db->prepare('
                INSERT INTO categories (category_name, category_image)
                VALUES (?, ?)
            ');
            $newCategoryImagePath = $newCategoryImage
                ? save_photo($newCategoryImage, '../uploads/category_images')
                : $categoryImagePath; // Use existing category image if no new image is uploaded
            $stm->execute([$newCategoryName, $newCategoryImagePath]);
        } elseif ($newCategoryImage) {
            // Update category image if new image uploaded
            unlink("../uploads/category_images/$categoryImagePath");
            $categoryImagePath = save_photo($newCategoryImage, '../uploads/category_images');

            $stm = $_db->prepare('
                UPDATE categories
                SET category_image = ?
                WHERE category_name = ?
            ');
            $stm->execute([$categoryImagePath, $categoryName]);
        }

        try {
            // Update product details in products table
            $stm = $_db->prepare('
                UPDATE products
                SET product_name = ?, price = ?, description = ?, current_stock = ?, status = ?, category_name = ?, product_image = ?
                WHERE product_id = ?
            ');
            $stm->execute([$productName, $price, $description, $currentStock, $status, $finalCategoryName, $productImagePath, $productId]);

            temp('success', 'Product and category updated successfully!');
            redirect('product.php');
        } catch (PDOException $e) {
            temp('error', 'Error updating product: ' . $e->getMessage());
            redirect();
        }
    }
}

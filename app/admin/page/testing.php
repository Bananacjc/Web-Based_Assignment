<?php
require '../_base.php'; // Include base functions and database connection

// Fetch existing categories
$categories = [];
try {
    $stmt = $_db->query("SELECT category_name, category_image FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    temp('error', "Error fetching categories: " . $e->getMessage());
    redirect(); // Redirect to prevent further execution
}

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
    if (empty($_err)) {
        if (post('new_category_name')) {
            $newCategoryName = post('new_category_name');
            $categoryImage = get_file('new_category_image');

            if ($categoryImage && str_starts_with($categoryImage->type, 'image/')) {
                $categoryImagePath = save_photo($categoryImage, '../uploads/category_images');
            } else {
                $_err['new_category_image'] = 'Invalid or missing category image.';
            }

            if (!$_err) {
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
        if (!$_err) {
            $productImagePath = save_photo($productImage, '../uploads/product_images');
            $productId = generate_unique_id('PRO', 'products', 'product_id', $_db);

            try {
                $stmt = $_db->prepare("
                    INSERT INTO products (product_id, product_name, category_name, price, description, current_stock, amount_sold, product_image, status)
                    VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
                ");
                $stmt->execute([$productId, $productName, $categoryName, $price, $description, $currentStock, $productImagePath, $status]);
                temp('success', "Product added successfully!");
                redirect();
            } catch (PDOException $e) {
                temp('error', "Error adding product: " . $e->getMessage());
                redirect();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <?php html_text('product_name', 'required'); ?>
        <?php err('product_name'); ?><br><br>

        <label for="categories">Existing Categories:</label>
        <?php html_select('category_name', array_column($categories, 'category_name', 'category_name'), '- Select Category -'); ?>
        <?php err('category_name'); ?><br><br>

        <label for="new_category_name">New Category Name:</label>
        <?php html_text('new_category_name'); ?><br><br>

        <label for="new_category_image">New Category Image:</label>
        <?php html_file('new_category_image', 'image/*'); ?>
        <?php err('new_category_image'); ?><br><br>

        <label for="price">Price:</label>
        <?php html_number('price', '0', '', '0.01', 'required'); ?>
        <?php err('price'); ?><br><br>

        <label for="description">Description:</label>
        <?php html_textarea('description', 'required'); ?>
        <?php err('description'); ?><br><br>

        <label for="current_stock">Current Stock:</label>
        <?php html_number('current_stock', '0', '', '1', 'required'); ?>
        <?php err('current_stock'); ?><br><br>

        <label for="product_image">Product Image:</label>
        <?php html_file('product_image', 'image/*', 'required'); ?>
        <?php err('product_image'); ?><br><br>

        <label for="status">Status:</label>
        <?php html_select('status', [
            'AVAILABLE' => 'Available',
            'UNAVAILABLE' => 'Unavailable',
            'OUT_OF_STOCK' => 'Out of Stock'
        ], '- Select Status -', 'required'); ?>
        <?php err('status'); ?><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>

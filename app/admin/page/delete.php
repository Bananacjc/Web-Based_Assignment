<?php
include '../_base.php';  // Include necessary base configurations

// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('product_id');  // Retrieve the product ID from the POST request

    // Ensure the ID exists in the database before proceeding
    try {
        // Fetch product details including the photo filenames (product_image and category_image)
        $stm = $_db->prepare('SELECT p.product_image 
            FROM products p
            WHERE p.product_id = ?');
        $stm->execute([$id]);
        $product = $stm->fetch(PDO::FETCH_OBJ);

        if ($product) {
            // Delete product image if it exists
            if ($product->product_image && file_exists("../uploads/product_images/{$product->product_image}")) {
                unlink("../uploads/product_images/{$product->product_image}");
            }

            // Delete the product record from the database
            $stm = $_db->prepare('DELETE FROM products WHERE product_id = ?');
            $stm->execute([$id]);

            temp('info', 'Product deleted successfully');
        } else {
            temp('error', 'Product not found');
        }
    } catch (Exception $e) {
        temp('error', 'Error deleting product: ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method');
}

// Redirect back to the product listing page
redirect('product.php');

// ----------------------------------------------------------------------------

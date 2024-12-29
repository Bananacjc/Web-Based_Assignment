<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {
            $stm = $_db->prepare('SELECT p.product_image FROM products p WHERE p.product_id = ?');
            $stm->execute([$v]);
            $product = $stm->fetch(PDO::FETCH_OBJ);

            if ($product && $product->product_image && file_exists("../../uploads/product_images/{$product->product_image}")) {
                unlink("../../uploads/product_images/{$product->product_image}");
            }

            // Delete the product record from the database
            $stm = $_db->prepare('DELETE FROM products WHERE product_id = ?');
            $stm->execute([$v]);
        }


        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            $deletedProductIds = implode(', ', $id);
            log_action($employeeId, 'Delete Product', "Delete Product: {$deletedProductIds}", $_db);
        }

        temp('info', count($id) . " product(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting product(s): ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method. Only POST requests are allowed.');
}
redirect('product.php');



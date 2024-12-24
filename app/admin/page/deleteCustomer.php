<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {
            $stm = $_db->prepare('SELECT profile_image FROM customers WHERE customer_id = ?');
            $stm->execute([$v]);
            $customer = $stm->fetch(PDO::FETCH_OBJ);

            if ($customer && $customer->profile_image && file_exists("../uploads/product_images/{$customer->profile_image}")) {
                unlink("../uploads/product_images/{$customer->profile_image}");
            }

            // Delete the product record from the database
            $stm = $_db->prepare('DELETE FROM customers WHERE customer_id = ?');
            $stm->execute([$v]);
        }

        temp('info', count($id) . " customer(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting product(s): ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method. Only POST requests are allowed.');
}
redirect('customer.php');



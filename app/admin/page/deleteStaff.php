<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {
            $stm = $_db->prepare('SELECT profile_image FROM employees WHERE employee_id = ?');
            $stm->execute([$v]);
            $employee = $stm->fetch(PDO::FETCH_OBJ);

            if ($employee && $employee->profile_image && file_exists("../../uploads/product_images/{$employee->profile_image}")) {
                unlink("../../uploads/product_images/{$employee->profile_image}");
            }

            // Delete the product record from the database
            $stm = $_db->prepare('DELETE FROM employees WHERE employee_id = ?');
            $stm->execute([$v]);
        }

        temp('info', count($id) . " employee(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting product(s): ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method. Only POST requests are allowed.');
}
redirect('staff.php');



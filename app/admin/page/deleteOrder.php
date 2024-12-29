<?php
include '../_base.php';  

if (is_post()) {
    $id = req('id', []); 
    if (!is_array($id)) $id = [$id]; 
    try {
        foreach ($id as $v) {

            $stm = $_db->prepare('DELETE FROM orders WHERE order_id = ?');
            $stm->execute([$v]);
        }


        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            $deletedOrderIds = implode(', ', $id);
            log_action($employeeId, 'Delete Order Id', "Delete Order: {$deletedOrderIds}", $_db);
        }

        temp('info', count($id) . " order(s) deleted successfully.");  
    } catch (Exception $e) {
        temp('error', 'Error deleting product(s): ' . $e->getMessage());
    }
} else {
    temp('error', 'Invalid request method. Only POST requests are allowed.');
}
redirect('orderStatus.php');


